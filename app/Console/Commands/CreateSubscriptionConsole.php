<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Package;
use App\Models\Tasks;
use Illuminate\Console\Command;

class CreateSubscriptionConsole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-subscription-console';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orders=Order::whereNotNull('renew_on')->where('status',\OrderStatus::Complete)->where('payment',\OrderPayment::Complete)->get();
        foreach ($orders as $order){
            if (count($order->tasks) == $order->tasks()->where('status',\TaskStatus::Complete)->count()){
                $order->renew_on=null;
                $order->save();

                $newOrder=new Order();
                $newOrder->car_id=$order->car_id;
                $newOrder->subscription_id=$order->subscription_id;
                $newOrder->price=$order->price;
                $newOrder->save();




                $lastSunday=date('Y-m-d');
                foreach (getNext4Sundays() as $sunday){
                    $task=new Tasks();
                    $task->date=$sunday;
                    $task->status=0;
                    $task->order_id=$newOrder->id;
                    $task->save();
                    $lastSunday=$sunday;
                }
                $newOrder->renew_on=$lastSunday;
                $newOrder->save();

                logActivity('Auto subscription created  having 4washes for '.$order->car->user->name);

            }
        }
    }
}
