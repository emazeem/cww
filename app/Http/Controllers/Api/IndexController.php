<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\CommonTrait;
use App\Models\Car;
use App\Models\Order;
use App\Models\Package;
use App\Models\Tasks;
use App\Models\User;
use Illuminate\Console\View\Components\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
        }
        return $this->sendSuccess("Task marked as done successful",true);
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
        if ($request->hasfile('receipt')) {
            $file = $request->file('receipt');
            $extenstion = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extenstion;
            $file->move('storage/order-receipt', $filename);
            $order->receipt = $filename;
        }
        $order->save();
        return $this->sendSuccess("Order payment markrd as done",true);
    }

    public function fetchTasks(Request $request){
        $tasks=Tasks::orderBy('created_at','ASC')->get();
        return $this->sendSuccess("Tasks fetched successful",$tasks);
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

        foreach (getNext4Sundays() as $sunday){
            $task=new Tasks();
            $task->date=$sunday;
            $task->status=0;
            $task->order_id=$order->id;
            $task->save();
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

        return $this->sendSuccess("User details updated successfully", $car);
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


}
