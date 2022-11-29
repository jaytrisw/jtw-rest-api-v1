<?php

class Response
{

    static function success(array $data): WP_REST_Response
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

enum ErrorCode: int {

    case UNKNOWN = -999;
    case API_KEY = 1;
    case AUTHENTICATION = 2;
    case MISSING_PARAMETER = 3;
    case USER_EXISTS = 4;
    case FAILED = 5;
    case INVALID_USER_UPDATE = 6;
    case INVALID_USER_IDENTIFIER = 7;
    case WRONG_PASSWORD = 8;

    case DELETE_FAILED = 9;
    case CANNOT_BE_DELETED = 10;
}

enum StatusCode: int {

    case UNKNOWN = 499;
    case BAD_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case PAYMENT_REQUIRED = 402;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case METHOD_NOT_ALLOWED = 405;
    case CONFLICT = 409;

}