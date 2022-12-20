<?php

class Response
{

    static function success(mixed $data): WP_REST_Response
    {
        return new WP_REST_Response($data);
    }

    static function failure(
        mixed $message, 
        StatusCode $status = StatusCode::UNKNOWN, 
        ErrorCode $code = ErrorCode::UNKNOWN): WP_REST_Response {
            return new WP_REST_Response(Response::error($message, $code), $status->value);
    }

    private static function error(mixed $message, ErrorCode $code): array
    {
        return array(
            'title' => 'Error',
            'message' => json_encode($message),
            'code' => $code->value
        );
    }

}