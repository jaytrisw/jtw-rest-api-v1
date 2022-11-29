<?php

function update_profile_callback(WP_REST_Request $request)
{
    return Common::validate_authenticated_request($request, function ($request) {
        $id = Common::get_param($request, 'id');
        $current_user = get_user_by('ID', $id);

        $display_name = Common::get_param($request, 'display_name');
        $first_name = Common::get_param($request, 'first_name');
        $last_name = Common::get_param($request, 'last_name');
        $description = Common::get_param($request, 'description');
        $url = Common::get_param($request, 'url');
        $email = Common::get_param($request, 'email');

        $userdata = array(
            'ID' => $id,
            'display_name' => $display_name,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'description' => $description,
            'user_url' => $url,
            'user_email' => $email
        );

        if (empty($display_name) || empty($first_name) || empty($last_name) || empty($email)) {
            $message = array(
                'information' => 'Failed to parse required parameters from input',
                'parameters' => $userdata
            );

            return Response::failure($message, StatusCode::BAD_REQUEST, ErrorCode::MISSING_PARAMETER);
        }

        if ($current_user->ID != $id) {
            return Response::failure('Idenitifier mismatch, cannot update user', StatusCode::FORBIDDEN, ErrorCode::INVALID_USER_UPDATE);
        }

        $updated_user_id = wp_update_user($userdata);
        if (is_wp_error($updated_user_id)) {
            Response::failure($updated_user_id->get_error_message(), StatusCode::FORBIDDEN, ErrorCode::FAILED);
        }
        return profile_callback($request);
    });
}