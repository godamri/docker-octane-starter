<?php

namespace App\Utils;

class FriendlyResponse
{

    const STATUS_CODE_SERVER_ERROR = 400;
    const STATUS_CODE_UNPROCESSABLE_ENTITY = 422;
    const STATUS_CODE_NOT_FOUND = 404;

    public static function send($data=[], $message = null, $code = 0)
    {
        $dataArr = [
            'error' => $code,
            'data' => $data
        ];
        if($message) {
            $dataArr['message'] = $message;
        }
        return response()->json($dataArr);
    }

}
