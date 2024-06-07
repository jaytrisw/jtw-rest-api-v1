<?php

function create_profile_callback(WP_REST_Request $request): WP_REST_Response
{
    return Common::validate_api_key($request, function ($request) {
        $encoded_username = Common::get_param($request, 'username');
        $encoded_password = Common::get_param($request, 'password');
        $username = base64_decode($encoded_username);
        $password = base64_decode($encoded_password);
        $display_name = Common::get_param($request, 'display_name');
        $first_name = Common::get_param($request, 'first_name');
        $last_name = Common::get_param($request, 'last_name');
        $description = Common::get_param($request, 'description');
        $url = Common::get_param($request, 'url');
        $email = Common::get_param($request, 'email');

        $userdata = array(
            'user_login' => $username,
            'user_pass' => $password,
            'display_name' => $display_name,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'description' => $description,
            'user_url' => $url,
            'user_email' => $email,
            'role' => 'app_user',
        );

        if (empty($username) || empty($password) || empty($display_name) || empty($first_name) || empty($last_name) || empty($email)) {
            $message = array(
                'information' => 'Failed to parse required parameters from input',
                'parameters' => $userdata
            );

            return Response::failure($message, StatusCode::BAD_REQUEST, ErrorCode::MISSING_PARAMETER);
        }

        if (get_user_by('email', $email) || get_user_by('login', $username)) {
            return Response::failure('A user with already exists with specified parameters', StatusCode::CONFLICT, ErrorCode::USER_EXISTS);
        }

        $new_user_id = wp_insert_user($userdata);

        if (is_wp_error($new_user_id)) {
            Response::failure($new_user_id->get_error_message(), StatusCode::FORBIDDEN, ErrorCode::CREATE_FAILED);
        }

        return autenticate_post_callback($request);

    });
}