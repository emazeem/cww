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
    $transaction->save();
    return true;
}
