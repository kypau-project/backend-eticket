<?php

namespace App\Helpers;

class ResponseHelper {
    // public function jsonResponseMethod($status, $data = null, $message = null) {
    public static function jsonResponseMethod($status, $data = null, $message = null, $code = 200) {
        $res = ['status' => $status];
        if(!is_null($data)) {
            $res['data'] = $data;   
        }
        if(!is_null($message)) {
            $res['message'] = $message;
        }
        return response()->json($res, $code);
    }
}