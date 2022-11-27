<?php

function autenticate_post_callback(WP_REST_Request $request): WP_REST_Response
{
    return Common::validate_api_key($request, function ($request) {

        $encoded_username = Common::get_param($request, 'username');
        $encoded_password = Common::get_param($request, 'password');

        $username = base64_decode($encoded_username);
        $password = base64_decode($encoded_password);

        $data_array = array(
            'username' => $username,
            'password' => $password
        );
        $post_request = Common::post_request('https://www.joshuatwood.com/wp-json/jwt-auth/v1/token/', json_encode($data_array));
        $response = json_decode($post_request, true);

        if ($response['token']) {
            return Response::success(
                array(
                    'token' => $response['token'],
                    'status' => 'valid_access_token'
                )
            );
        }

        return Response::failure('Authentication failed.');

    });

}