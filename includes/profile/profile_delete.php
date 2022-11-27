<?php

function delete_profile_callback(WP_REST_Request $request): WP_REST_Response {
    return Common::validate_authenticated_request($request, function ($request) {
        $id = Common::get_param($request, 'id');
        $confirm_delete = Common::get_param($request, 'confirm_delete', 'false');
        $current_user = wp_get_current_user();
        if ($confirm_delete == 'false') {
            return Response::failure('Must include `confirm_delete` with request');
        }
        if ($current_user->ID == 1) {
            return Response::failure('Cannot delete specified user');
        }
        if ($current_user->ID != $id) {
            return Response::failure('Idenitifier mismatch, cannot delete user');
        }
        if (current_user_can('administrator')) {
            return Response::failure('Cannot delete user with elevated privileges');
        }
        if (!current_user_can('app_user')) {
            return Response::failure('Cannot delete user which was not created through API');
        }
        require_once('./wp-admin/includes/user.php' );
        $is_deleted = wp_delete_user($id);
        if (!$is_deleted) {
            return Response::failure('User could not be deleted');
        }
        return Response::success(array('token' => null, 'status' => 'no_authoriztion_header'));
    });
}