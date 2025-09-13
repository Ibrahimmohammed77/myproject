<?php

namespace App\Traits;
use Illuminate\Http\Response;

trait ApiResponse
{

    public function success($message, $data)
    {
        if ($message == null) {
            $message = "Success";
        }
        return response()->json([
            "code" => "Success",
            "message" => $message,
            "data" => $data
        ], Response::HTTP_OK);
    }

    public function error($message, $code, $http, $error)
    {
        if ($message == null) {
            $message = "حدث خطأ اثناء التنفيذ";
        }

        return response()->json([
            "code" => $code,
            "message" => $message,
            "error" => $error
        ], $http);
    }
}
