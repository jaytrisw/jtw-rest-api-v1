<?php

class Response
{

    static function success(array $data): WP_REST_Response
    {
        return new WP_REST_Response($data);
    }

    static function failure(mixed $message, int $status = 499, ErrorCodes $code = ErrorCodes::UNKNOWN): WP_REST_Response
    {
        return new WP_REST_Response(Response::error($message, $code), $status);
    }

    private static function error(mixed $message, ErrorCodes $code): array
    {
        return array(
            'title' => 'Error',
            'message' => $message,
            'code' => $code->value
        );
    }

}

enum ErrorCodes: int {

    case UNKNOWN = -999;
    case API_KEY = 1;
    case AUTHENTICATION = 2;

}