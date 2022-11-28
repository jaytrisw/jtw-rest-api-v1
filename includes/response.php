<?php

class Response
{

    static function success(array $data): WP_REST_Response
    {
        return new WP_REST_Response($data);
    }

    static function failure(mixed $message, int $status = 499, int $code = -999): WP_REST_Response
    {
        return new WP_REST_Response(Response::error($message, $code), $status);
    }

    private static function error(mixed $message, int $code): array
    {
        return array(
            'title' => 'Error',
            'message' => $message,
            'code' => $code
        );
    }

}