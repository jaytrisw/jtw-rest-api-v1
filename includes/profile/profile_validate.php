<?php

function validate_post_callback(WP_REST_Request $request)
{
    return Common::validate_api_key($request, function ($request) {
        $bearer_token = $request->get_header('Authorization');
        $post_request = Common::post_request('https://www.joshuatwood.com/wp-json/jwt-auth/v1/token/validate', json_encode(array()), $bearer_token);
        $response = json_decode($post_request, true);
        $token = str_replace('Bearer ', '', $bearer_token);

        switch ($response['code']) {
            case 'jwt_auth_valid_token':
                return Response::success(array('token' => $token, 'status' => 'valid_access_token'));
            case 'jwt_auth_no_auth_header':
                return Response::success(array('token' => null, 'status' => 'no_authoriztion_header'));
            case 'jwt_auth_invalid_token':
                if ($response['message'] == 'Expired token') {
                    return Response::success(array('token' => $token, 'status' => 'expired_access_token'));
                }
                return Response::success(array('token' => $token, 'status' => 'invalid_access_token'));
            default:
                return Response::success(array('token' => $token, 'status' => 'unknown_error'));
        }
    });
}