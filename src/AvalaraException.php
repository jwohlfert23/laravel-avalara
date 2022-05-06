<?php

namespace Jwohlfert23\LaravelAvalara;

use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;

class AvalaraException extends Exception
{
    public array $response;

    public static function fromResponse(Response $response)
    {
        $message = $response->json('error.message', 'An unknown error occurred');
        $e = new static('Avalara: '.$message);
        $e->response = $response->json();

        return $e;
    }

    public function context()
    {
        return [
            'response' => $this->response,
        ];
    }

    public function render(Request $request)
    {
        return response()->json([
            'message' => $this->message,
        ], 400);
    }
}
