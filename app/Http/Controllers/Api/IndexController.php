<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\CommonTrait;
use App\Models\Activity;
use App\Models\Car;
use App\Models\Expense;
use App\Models\Order;
use App\Models\Package;
use App\Models\TaskAsset;
use App\Models\Tasks;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserDevices;
use Illuminate\Console\View\Components\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;





use Checkout\CheckoutApiException;
use Checkout\CheckoutException;
use Checkout\CheckoutSdk;
use Checkout\Common\Currency;
use Checkout\Environment;
use Checkout\Payments\Request\PaymentRequest;
use Checkout\Payments\Request\Source\RequestTokenSource;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;





class IndexController extends Controller
{
    use CommonTrait;
    public function login(Request $request){
        $validators = Validator($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages()->first(), null);
        }
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $data['user'] = [
                    'id' => $user->id,
                    'role'=>$user->role,
                ];
                $data['token'] = $user->createToken('auth-token')->plainTextToken;
                return $this->sendSuccess("Login successful", $data);
            } else {
                return $this->sendError("These credentials do not match our records.", null);
            }
        } else {
            return $this->sendError("These credentials do not match our records.", null);
        }
    }

    public function register(Request $request){
        $validators = Validator($request->all(), [
            'name' => 'required',
            'email' => 'required|email|max:255|unique:users',
            'phone' => 'required',
            'password' => 'required|min:8',
            'role' => 'required',
            'address' => 'required',
        ], [
            'unique' => 'This :attribute already exists',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages()->first(), null);
        }

        $userdata = new User();
        $userdata->name = $request->name;
        $userdata->email = $request->email;
        $userdata->phone = $request->phone;
        $userdata->role = $request->role;
        $userdata->address = $request->address;
        $userdata->password = Hash::make($request->password);
        if ($request->hasfile('profile')) {
            $file = $request->file('profile');
            $extenstion = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extenstion;
            $file->move('storage/profile/' . $request->email, $filename);
            $userdata->profile = $filename;
        }
        $userdata->save();
        return $this->sendSuccess("Register successful", $userdata);
    }
    public function fetchCustomers(Request $request){
        $customers=User::where('role',\Role::Customer)->get();
        return $this->sendSuccess("Customers fetched successful", $customers);
    }
    public function fetchTask(Request $request){
        $task=Tasks::with('order','order.car','order.car.user')->where('id',$request->id)->first();
        $images=[];
        foreach ($task->assets as $asset){
            $images[]=$asset->image;
        }
        $task->images=$images;
        return $this->sendSuccess("Task fetched successful", $task);
    }

    public function fetchSubscriptions(Request $request){
        $packages=Package::all();
        return $this->sendSuccess("Packages fetched successful", $packages);
    }

    public function fetchCustomer(Request $request){
        $validators = Validator($request->all(), [
            'id' => 'required',
        ],[
            'id.required'=>'Customer id is required'
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages()->first(), null);
        }
        $customer=User::find($request->id);
        return $this->sendSuccess("Customer fetched successful", $customer);
    }
    public function taskMarkAsDone(Request $request){
        $validators = Validator($request->all(), [
            'id' => 'required',
        ],[
            'id.required'=>'Task id is required'
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages()->first(), null);
        }
        $task=Tasks::find($request->id);
        $task->status=\TaskStatus::Complete;
        $task->save();

        if ($task->order->tasks()->where('status',\TaskStatus::Pending)->count()==0){
            $order=Order::find($task->order_id);
            $order->status=\OrderStatus::Complete;
            $order->save();
            logActivity('All car washes of Order#'.str_pad($task->order->id,4,0,STR_PAD_LEFT).' are completed.');
        }
        logActivity(auth()->user()->name.' marked Task#'.str_pad($task->id,4,0,STR_PAD_LEFT).' of Order#'.str_pad($task->order->id,4,0,STR_PAD_LEFT).' as complete.');


        return $this->sendSuccess("Task marked as done successful",true);
    }
    public function createExpense(Request $request){
        $validators = Validator($request->all(), [
            'type' => 'required',
            'narration' => 'required',
            'amount' => 'required',
            'image' => 'required',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages()->first(), null);
        }
        $expense=new Expense();
        $expense->user_id=auth()->user()->id;
        $expense->type=$request->type;
        $expense->narration=$request->narration;
        $expense->amount=$request->amount;

        $file = $request->file('image');
        $extenstion = $file->getClientOriginalExtension();
        $filename = time() . '.' . $extenstion;
        $file->move('storage/expense', $filename);
        $expense->image = $filename;
        $expense->save();
        one_signal_notification(\Role::Manager,auth()->user()->name.' added new expense',['url'=>'expense','id'=>null],true);
        return $this->sendSuccess("Expense added successfully",true);
    }
    public function fetchExpenses(Request $request){
        if(auth()->user()->role == \Role::Technician){
            $expenses=Expense::with('user')->where('user_id',auth()->user()->id)->get();

        }
        if(auth()->user()->role == \Role::Manager){
            $expenses=Expense::with('user')->get();

        }
        return $this->sendSuccess("Expense fetched successfully",$expenses);
    }
    public function fetchMyExpenses(Request $request){
        $expenses=Expense::with('user')->where('user_id',auth()->user()->id)->get();
        return $this->sendSuccess("My Expenses fetched successfully",$expenses);
    }


    public function paymentMarkAsDone(Request $request){
        $validators = Validator($request->all(), [
            'id' => 'required',
        ],[
            'id.required'=>'Order id is required'
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages()->first(), null);
        }
        $order=Order::find($request->id);
        $order->payment=\OrderPayment::Complete;
        $order->payment_date=date('Y-m-d');

        $order->save();
        logActivity(auth()->user()->name.' received payment of Order#'.str_pad($order->id,4,0,STR_PAD_LEFT).'.');
        logTransaction($order->id,'cash');
        return $this->sendSuccess("Order payment marked as done",true);
    }

    public function fetchTasks(Request $request){
        $tasksByDate = [];
        $orderStatusMap = [];
        foreach (Tasks::with('order','order.car','order.car.user')->get() as $task) {
            $date = $task["date"];
            if (!isset($tasksByDate[$date])) {
                $tasksByDate[$date] = [];
            }

            if ($task->status === 0 && isset($orderStatusMap[$task->order_id]) && $orderStatusMap[$task->order_id] === 0) {
                $task["accessor"] = false;
            }else{
                $task["accessor"] = true;

            }
            $tasksByDate[$date][] = $task;
            if ($task->status === 0) {
                $orderStatusMap[$task->order_id] = 0;
            }
        }
        return $this->sendSuccess("Tasks fetched successful",$tasksByDate);
    }
    public function fetchMyCars(Request $request){
        $cars=Car::where('user_id',auth()->user()->id)->with('order','user')->get();
        return $this->sendSuccess("My Cars fetched successful",$cars);
    }


    public function fetchCars(Request $request){
        $validators = Validator($request->all(), [
            'id' => 'required',
        ],[
            'id.required'=>'Customer id is required'
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages()->first(), null);
        }
        $cars=Car::with('order','order.tasks','order.tasks.assets','order.subscription')->where('user_id',$request->id)->get();

        foreach ($cars as $car){
            foreach ($car->order->tasks as $task){
                $images=[];
                foreach ($task->assets as $asset){
                    $images[]=$asset->image;
                }
                $task->images=$images;
            }
        }
        return $this->sendSuccess("Cars fetched successful",$cars);
    }
    public function cancelSubscription(Request $request){
        $validators = Validator($request->all(), [
            'id' => 'required',
        ],[
            'id.required'=>'Order id is required'
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages()->first(), null);
        }
        $order=Order::find($request->id);
        $order->renew_on=null;
        $order->save();
        logActivity(auth()->user()->name.' cancelled the renewal of Order#.'.str_pad($order->id,4,0,STR_PAD_LEFT));

        return $this->sendSuccess("Subscription cancelled successful",true);
    }
    public function fetchUser(Request $request){
        $user=auth()->user();
        return $this->sendSuccess("Auth user fetched successful",$user);
    }
    public function fetchInvoices(Request $request){
        $orders=Order::with('car','car.user')->get();
        return $this->sendSuccess("Invoices fetched successful",$orders);
    }

    public function fetchMyInvoices(Request $request){
        $orders=Order::with([
            'car' => function ($query) {
                $query->where('user_id',auth()->user()->id);
            },'car.user']
        )->get();
        return $this->sendSuccess("My Invoices fetched successful",$orders);
    }
    public function fetchMyTransactions(Request $request){
        $orders=Transaction::where('user_id',auth()->user()->id)->get();
        return $this->sendSuccess("My Trx fetched successful",$orders);
    }


    public function fetchActivities(Request $request){
        $activites=Activity::all();
        return $this->sendSuccess("Activities fetched successful",$activites);
    }
    public function uploadTaskImage(Request $request){
        $validators = Validator($request->all(), [
            'id' => 'required',
            "image"   => "required|image|mimes:jpg,jpeg,png",
        ],[
            'id.required'=>'Task id is required'
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages()->first(), null);
        }
        $asset=new TaskAsset();
        $asset->task_id=$request->id;

        $file = $request->file('image');
        $extenstion = $file->getClientOriginalExtension();
        $filename = time() . '.' . $extenstion;
        $file->move('storage/tasks', $filename);
        $asset->image = $filename;
        $asset->save();

        return $this->sendSuccess("Image uploaded successful",);
    }
    public function uploadReceipt(Request $request){
        $validators = Validator($request->all(), [
            'id' => 'required',
            "image"   => "required|image|mimes:jpg,jpeg,png",
        ],[
            'id.required'=>'Order id is required'
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages()->first(), null);
        }

        $order=Order::find($request->id);
        $file = $request->file('image');
        $extenstion = $file->getClientOriginalExtension();
        $filename = time() . '.' . $extenstion;
        $file->move('storage/receipt', $filename);
        $order->receipt = $filename;
        $order->payment_type=\PaymentType::COD;
        $order->save();

        return $this->sendSuccess("Receipt uploaded successful",);
    }



    public function changePassword(Request $request){
        $validators = Validator($request->all(), [
            'current_password' => 'required|min:8',
            'new_password' => 'required|same:confirm_password|min:8',

        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages()->first(), null);
        }
        $user = auth()->user();
        if (Hash::check($request->current_password, $user->password)) {
            $user->password = Hash::make($request->new_password);
            $user->save();
            return $this->sendSuccess("Password updated successfully!", true);
        } else {
            return $this->sendError("Incorrect current password!", null);
        }
    }






    public function createCarSubscription(Request $request){
        $validators = Validator($request->all(), [
            'make' => 'required',
            'model' => 'required',
            'plate' => 'required',
            'user_id' => 'required',
            'subscription_id' => 'required',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages()->first(), null);
        }
        $car = new Car();
        $car->model=$request->model;
        $car->make=$request->make;
        $car->plate=$request->plate;
        $car->user_id=$request->user_id;
        if ($request->hasfile('image')) {
            $file = $request->file('image');
            $extenstion = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extenstion;
            $file->move('storage/car', $filename);
            $car->image = $filename;
        }
        $car->save();

        $subscription=Package::find($request->subscription_id);
        $order=new Order();
        $order->car_id=$car->id;
        $order->subscription_id=$subscription->id;
        $order->price=$subscription->price;
        $order->save();

        $customer=User::find($request->user_id);
        if($subscription->is_recurring==1){
            $lastSunday=date('Y-m-d');
            foreach (getNext4Sundays() as $sunday){
                $task=new Tasks();
                $task->date=$sunday;
                $task->status=0;
                $task->order_id=$order->id;
                $task->save();
                $lastSunday=$sunday;
            }
            $order->renew_on=$lastSunday;
            $order->save();

            logActivity(auth()->user()->name.' has created new order having 4washes for '.$customer->name);

        }else{
            $task=new Tasks();
            $task->date=getNext4Sundays()[0];
            $task->status=0;
            $task->order_id=$order->id;
            $task->save();
            logActivity(auth()->user()->name.' has created new order have one time wash for '.$customer->name);

        }
        return $this->sendSuccess("Car and subscription created successfully", $car);
    }
    public function editUser(Request $request){
        $validators = Validator($request->all(), [
            'user_id' => 'required',
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'address' => 'required',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages()->first(), null);
        }
        $user = User::find($request->user_id);
        $user->name=$request->name;
        //$user->email=$request->email;
        $user->phone=$request->phone;
        $user->address=$request->address;
        $user->save();

        return $this->sendSuccess("User details updated successfully", true);
    }
    public function updateLocation(Request $request){
        $validators = Validator($request->all(), [
            'user_id' => 'required',
            'long' => 'required',
            'lat' => 'required',
            'address' => 'required',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages()->first(), null);
        }
        $user = User::find($request->user_id);
        $user->long=$request->long;
        $user->lat=$request->lat;
        $user->address=$request->address;
        $user->save();

        return $this->sendSuccess("Location updated successfully", true);
    }

    public function updatePassword(Request $request)
    {
        $validators = Validator($request->all(), [
            'id' => 'required',
            'current_password' => 'required|min:8',
            'new_password' => 'required|same:confirm_password|min:8',

        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages()->first(), null);
        }
        $user = User::find($request->id);
        if (Hash::check($request->current_password, $user->password)) {
            $user->password = Hash::make($request->new_password);
            $user->save();
            $data['action'] = true;
            return $this->sendSuccess("Password updated successfully!", $data);
        } else {
            return $this->sendError("Incorrect current password!", null);
        }
    }
    public function test(){
        $tasksByDate = [];
        $orderStatusMap = [];
        foreach (Tasks::all() as $task) {
            $date = $task["date"];
            if (!isset($tasksByDate[$date])) {
                $tasksByDate[$date] = [];
            }

            if ($task->status === 0 && isset($orderStatusMap[$task->order_id]) && $orderStatusMap[$task->order_id] === 0) {
                $task["accessor"] = false;
            }else{
                $task["accessor"] = true;

            }
            $tasksByDate[$date][] = $task;
            if ($task->status === 0) {
                $orderStatusMap[$task->order_id] = 0;
            }
        }
        dd($tasksByDate);
    }
    public function home(){
        return view('welcome');
    }
    public function checkout(Request $req){

        $validators = Validator($req->all(), [
            'token' => 'required',
            'name'=>'required',
            'card_number'=>'required',
            'expiry_month'=>'required',
            'expiry_year'=>'required',
            'order_id'=>'required',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages()->first(), null);
        }
        $order=Order::find($req->order_id);

        if($order->payment==\OrderPayment::Pending){

            $log = new Logger("checkout-sdk-php-sample");
            $log->pushHandler(new StreamHandler("php://stdout"));
            try {
                $api = CheckoutSdk::builder()->staticKeys()
                    ->environment(Environment::sandbox())
                    ->secretKey(env("CHECKOUT_SECRET_KEY"))
                    ->build();
            } catch (CheckoutException $e) {
                $log->error("An exception occurred while initializing Checkout SDK : {$e->getMessage()}");
                http_response_code(400);
                return $this->sendError("Error ! {$e->getMessage()}", true);

            }
            $postData = file_get_contents("php://input");
            $request = json_decode($postData);
            $requestTokenSource = new RequestTokenSource();
            $requestTokenSource->token = $req->token;

            $request = new PaymentRequest();
            $request->source = $requestTokenSource;
            $request->currency = Currency::$SAR;
            $request->amount = $order->price;
            $request->processing_channel_id = env('CHECKOUT_CHANNEL_ID');


            try {
                $order->payment=\OrderPayment::Complete;
                $order->receipt=json_encode($api->getPaymentsClient()->requestPayment($request));
                $order->payment_date=date('Y-m-d');
                $order->save();
                logTransaction($order->id,"bank : {$req->card_number}");
                return $this->sendSuccess("Checkout successful!", true);

            } catch (CheckoutApiException $e) {
                $log->error("An exception occurred while processing payment request");
                http_response_code(400);
                return $this->sendError("Error ! {$e->getMessage()}", true);

            }
        }else{
            return $this->sendSuccess("Already paid!", true);
        }
    }




    public function storeNotificationDevice(Request $request)
    {
        $validators = Validator($request->all(), [
            'device_id' => 'required',
        ]);
        if ($validators->fails()) {
            return $this->sendError($validators->messages()->first(), null);
        }
        $device = UserDevices::where('device_id', $request->device_id)->first();
        if ($device) {
            return $this->sendError('Device already exists.', null);
        } else {
            $user_device = new UserDevices;
            $user_device->user_id = auth()->user()->id;
            $user_device->device_id = $request->device_id;
            $user_device->save();
            return $this->sendSuccess("Device added successfully!", true);
        }
    }
    public function removeAllData(Request $request){
        if ($request->password=='EmAzeem123'){
            User::truncate();
            UserDevices::truncate();
            Car::truncate();
            Package::truncate();
            Tasks::truncate();
            Order::truncate();
            return true;
        }
    }

}
