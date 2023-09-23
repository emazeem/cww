<?php

namespace App\Console\Commands;

use App\Models\Tasks;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendCarWashAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-car-wash-alerts';

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
        $carWashEvents = Tasks::where('date', date('Y-m-d'))->get();
        foreach ($carWashEvents as $task) {
            $user=$task->order->car->user;
            one_signal_notification($user->id,"{$user->name} you have a car wash today",['url'=>'task','id'=>$task->id]);
        }
    }
}
