<?php

class Response
{

    static function success(array $data): WP_REST_Response
    {
        return new WP_REST_Response($data);
    }

    static function failure(string $message): WP_REST_Response
    {
        return new WP_REST_Response(Response::error($message));
    }

    private static function error(string $message): array
    {
        return array(
            'title' => 'Error',
            'message' => $message
        );
    }

}