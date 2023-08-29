<?php
namespace App\Http\Traits;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

trait CommonTrait {
    function sendSuccess($message, $data = '') {
        return response()->json(['message' => $message, 'data' => $data,],200);
    }
    function sendError($error_message, $code = '', $data = NULL) {
        //return Response::json(array('status' => 400, 'errorMessage' => $error_message), 400);
        return response()->json([

            'message' => $error_message,
            'data' => $data,
        ],400);
    }

    /**
     * Add Image in local storage
     *
     * @param [type] $file
     * @param [type] $path
    //     * @return void
     */
    function addFile($file, $path) {
        if ($file) {
            if ($file->getClientOriginalExtension() != 'exe') {
                $type = $file->getClientMimeType();
                if ($type == 'image/jpg' || $type == 'image/jpeg' || $type == 'image/png' || $type == 'image/bmp'
                    || $type == 'video/mp4' || $type == 'video/MOV' || $type == 'video/MKV' || $type == 'video/AVI') {
                    $destination_path = $path;
                    $extension = $file->getClientOriginalExtension();
                    $fileName = Str::random(15) . '.' . $extension;
                    //$img=Image::make($file);
                    if (($file->getSize() / 1000) > 2000) {

                        //Image::make($file)->save('public/'.$destination_path. $fileName, 30);
                        $file->move($destination_path, $fileName);
                        $file_path = $destination_path . $fileName;
                    } else {
                        $file->move($destination_path, $fileName);
                        $file_path = $destination_path . $fileName;}
                    return $file_path;
                } else {
                    return FALSE;
                }
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * Send Notification
     *
     * @param [type] $message
     * @param [type] $data
     * @param [type] $emails
    //     * @return void
     */
    function send_OneSignal_Notification($message, $data, $emails) {
        $content = array(
            "en" => $message,
        );

        $fields = array(
            'app_id' => env("ONE_SIGNAL_APP_ID"),
            'include_external_user_ids' => $emails,
            'channel_for_external_user_ids' => 'push',
            'data' => $data,
            'contents' => $content,
        );

        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . env("ONE_SIGNAL_REST_API_KEY")));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);
        info($response);
        return $response;
    }
}
