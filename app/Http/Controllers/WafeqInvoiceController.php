<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class WafeqInvoiceController extends Controller
{
    public function download(){
        $client = new Client();
        $response = $client->request('GET', 'https://api.wafeq.com/v1/invoices/inv_2hjcy3DgX4nW4PDeEwUJma/download/', [
            'headers' => [
                'Authorization' => 'Api-Key ZwVoT8Xa.h6KDRQ937aNNjCAqZBjPd05wjVUCifum',
                'accept' => 'application/pdf; version=v1',
            ],
        ]);

        echo $response->getBody();
    }
    public function create(Request $request){

        $client = new Client();


//        $response = $client->request('GET', 'https://api.wafeq.com/v1/accounts/', [
//            'headers' => [
//                'Authorization' => 'Api-Key ZwVoT8Xa.h6KDRQ937aNNjCAqZBjPd05wjVUCifum',
//                'accept' => 'application/json; version=v1',
//            ],
//        ]);
//
//        $customers= json_decode($response->getBody());
//        dd($customers);

//        $response = $client->request('GET', 'https://api.wafeq.com/v1/contacts/', [
//            'headers' => [
//                'Authorization' => 'Api-Key ZwVoT8Xa.h6KDRQ937aNNjCAqZBjPd05wjVUCifum',
//                'accept' => 'application/json; version=v1',
//            ],
//        ]);
//
//        $res= json_decode($response->getBody(),true);
//
//
//
//
//
//
//        $response = $client->request('GET', 'https://api.wafeq.com/v1/contacts/882290', [
//            'headers' => [
//                'Authorization' => 'Api-Key ZwVoT8Xa.h6KDRQ937aNNjCAqZBjPd05wjVUCifum',
//                'accept' => 'application/json; version=v1',
//            ],
//        ]);
//        dd($response);








        $response = $client->request('POST', 'https://api.wafeq.com/v1/contacts/', [
            'body' => '{"name":"Emazeem"}',
            'headers' => [
                'Authorization' => 'Api-Key ZwVoT8Xa.h6KDRQ937aNNjCAqZBjPd05wjVUCifum',
                'accept' => 'application/json; version=v1',
                'content-type' => 'application/json',
            ],
        ]);

        $chartOfAccounts= json_decode($response->getBody(),true);
        foreach ($chartOfAccounts as $chartOfAccount){
            if ($chartOfAccount['account_code']==403){
                dd($chartOfAccount);
            }
        }

        dd(1);




        $response = $client->request('POST', 'https://api.wafeq.com/v1/invoices/', [
            'body' => '{"currency":"SAR","language":"en","status":"DRAFT","line_items":[
            {
            "account":"acc_fnBqyLMbShv5VUDeDF83zP",
            "quantity":1,
            "unit_amount":100,
            "description":"This provides four car washes in a month"}],
            "invoice_number":"00001",
            "contact":"co_PxR77dGzHeq6Tx5Aqp5XGe",
            "invoice_date":"2023-12-12"
            }',
            'headers' => [
                'Authorization' => "Api-Key "+env('WAFEQ_API_KEY'),
                'accept' => 'application/json; version=v1',
                'content-type' => 'application/json',
            ],
        ]);




        $result= $response->getBody();
        dd($result);
    }
    //
}
