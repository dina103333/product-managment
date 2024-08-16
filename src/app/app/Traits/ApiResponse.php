<?php
namespace App\Traits;

use Illuminate\Http\Response;

trait ApiResponse
{
    protected function tokenResponse($token, $code = Response::HTTP_OK)
    {
        return response()->json([
            'success' => true,
            'token' => $token
        ], $code);
    }

    protected function successResponse($data = null, $message = null, $code = Response::HTTP_OK)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    protected function errorResponse($message, $code)
    {
        return response()->json([
            'error' => $message,
        ], $code);
    }

    protected function notFoundResponse($message = 'Resource not found')
    {
        return $this->errorResponse($message, Response::HTTP_NOT_FOUND);
    }

    protected function validationErrorResponse($errors)
    {
        return response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $errors
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}