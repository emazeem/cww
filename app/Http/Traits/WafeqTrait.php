<?php
namespace App\Http\Traits;

use App\Models\Order;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

trait WafeqTrait {

    public $client;
    public $url;
    public function __construct(){
        $this->client=new Client();
        $this->url='https://api.wafeq.com/v1';
    }
    function createCustomer($customer) {
        Artisan::call('config:clear');
        $response = $this->client->request('POST', $this->url.'/contacts/', [
            'body' => '{"name":"'.$customer->name.'"}',
            'headers' => [
                'Authorization' => 'Api-Key '.env('WAFEQ_API_KEY'),
                'accept' => 'application/json; version=v1',
                'content-type' => 'application/json',
            ],
        ]);

        $contact= json_decode($response->getBody(),true);
        $us=User::find($customer->id);
        $us->customer_id=$contact['id'];
        $us->save();
        return response()->json(['message' => 'Wafeq customer created successfully!', 'id' => $contact['id']],200);
    }
    function bookInvoice($order) {
        Artisan::call('config:clear');
        $response = $this->client->request('GET', $this->url.'/accounts/', [
            'headers' => [
                'Authorization' => "Api-Key ".env('WAFEQ_API_KEY'),
                'accept' => 'application/json; version=v1',
            ],
        ]);
        $chartOfAccounts= json_decode($response->getBody(),true);
        $salesRecord = array_filter($chartOfAccounts['results'], function ($object) {
            return $object['account_code'] === "403";
        });
        $salesRecord = reset($salesRecord);

        $invoiceNo='INV#'.str_pad($order->id,5,0,STR_PAD_LEFT);
        $description="Car wash invoice ".$invoiceNo." generated for ".$order->car->make.'('.$order->car->model.')';

        $response = $this->client->request('POST', $this->url.'/invoices/', [
            'body' => '{"currency":"SAR","language":"en","status":"SENT","line_items":[
            {
                "account":"'.$salesRecord['id'].'",
                "quantity":1,
                "unit_amount":'.$order->price.',
                "description":"'.$description.'"}],
                "invoice_number":"'.$invoiceNo.'",
                "contact":"'.$order->car->user->customer_id.'",
                "invoice_date":"'.date('Y-m-d').'"
                }',
            'headers' => [
                'Authorization' => "Api-Key ".env('WAFEQ_API_KEY'),
                'accept' => 'application/json; version=v1',
                'content-type' => 'application/json',
            ],
        ]);
        $response=json_decode($response->getBody(),true);
        $or=Order::find($order->id);
        $or->order_id=$response['id'];
        $or->save();
        return response()->json(['message' => 'Wafeq invoice generated successfully!', 'response' => $response],200);
    }

}
