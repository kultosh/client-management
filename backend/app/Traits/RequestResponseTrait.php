<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Throwable;

trait RequestResponseTrait
{
    public function successJsonResponse($message, $data=null, $code=200)
    {
        return response()->json([
            'code' => $code,
            'status' => 'success',
            'message' => $message,
            'content' => $data
        ], $code);
    }

    public function exceptionJsonResponse(Throwable $exception, $logChannelType="single", $code = 500)
    {
        Log::channel($logChannelType)->error("API Exception", [
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);
        
        return response()->json([
            'code' => $code,
            'status' => 'error',
            'message' => 'Something went wrong. Please try again later.',
            'content' => env('APP_DEBUG') ? $exception->getMessage().' at Line ===>'.$exception->getLine() : null
        ]);
    }
}
