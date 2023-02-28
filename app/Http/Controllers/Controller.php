<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected function errorResponse($message, $errors=null, $code=422) {
        if($message == null && is_string($errors))
            $message = $errors;
        return response()->json([
            'errors' => $errors,
            'data' => null,
            'message' => $message,
            'status' => 'error'
            ], $code);
    }

    protected function successResponse($message, $data=null, $code=200) {
        return response()->json([
            'errors' => null,
            'data' => $data,
            'message' => $message,
            'status' => 'success'
        ], $code);
    }
}
