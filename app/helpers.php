<?php

class Role
{
    const Manager = 'manager';
    const Technician = 'technician';
    const Customer = 'customer';
}
class TaskStatus
{
    const Pending = 0;
    const Complete = 1;
}
class PaymentType
{
    const COD = 'cash-on-delivery';
    const ONLINE = 'online';
}

class OrderStatus
{
    const Pending = 0;
    const Complete = 1;
}
class OrderPayment
{
    const Pending = 0;
    const Complete = 1;
}


function getNext4Sundays() {
    $currentDate = new DateTime();
    $upcomingSundays = array();
    for ($i = 0; $i < 4; $i++) {
        while ($currentDate->format('N') != 7) {
            $currentDate->add(new DateInterval('P1D'));
        }
        $upcomingSundays[] = $currentDate->format('Y-m-d');
        $currentDate->add(new DateInterval('P1W'));
    }
    return $upcomingSundays;

}
function logActivity($message){
    $activity=new \App\Models\Activity();
    $activity->message=$message;
    $activity->created_by=auth()->user()->id;
    $activity->role=auth()->user()->role;
    $activity->save();
}
function logTransaction($order,$type){

    //$type can be cash or via bank
    $order=\App\Models\Order::find($order);
    $transaction=new \App\Models\Transaction();
    $transaction->user_id=$order->car->user_id;
    $transaction->order_id=$order->id;
    $transaction->narration="Payment of car [ model = {$order->car->model} make = {$order->car->model} plate = {$order->car->model} via {$type}]";
    $transaction->amount=$order->price;
    $transaction->save();
    return true;
}
use App\Models\User;
use App\Models\UserDevices;

function one_signal_notification($to,$title,$route,$sendToRole=false){

    if ($sendToRole){
        $users=User::where('role',$to)->get();
        $uids=[];
        foreach ($users as $user){
            $uids[]=$user->id;
        }
    }

    $userDevices =
        ($sendToRole)
            ? UserDevices::whereIn('user_id', $uids)->get()
            : UserDevices::where('user_id', $to)->get();
    $devices = [];
    foreach ($userDevices as $device) {
        $devices[] = $device->device_id;
    }
    $content = array(
        "en" => strip_tags($title),
    );
    $fields = array(
        'app_id' => env("ONE_SIGNAL_APP_ID"),
        'include_player_ids' => $devices,
        //'included_segments' => array('All'),
        'channel_for_external_user_ids' => 'push',
        'data' => ['url' => $route['url'],'id' => $route['data'] ],
        'contents' => $content,
    );

    $fields = json_encode($fields);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
        'Authorization: Basic ' . env("ONE_SIGNAL_REST_API_KEY")));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    curl_close($ch);
    info($response);
}
