<?php

function delete_profile_callback(WP_REST_Request $request): WP_REST_Response
{
    return Common::validate_authenticated_request($request, function (
        $request
    ) {
        $id = Common::get_param($request, "id");
        $confirm_delete = Common::get_param(
            $request,
            "confirm_delete",
            "false"
        );
        $encoded_password = Common::get_param($request, "password");
        $password = base64_decode($encoded_password);
        $current_user = wp_get_current_user();

        if (empty($password)) {
            return Response::failure(
                "Must include `password` with request",
                StatusCode::BAD_REQUEST,
                ErrorCode::MISSING_PARAMETER
            );
        }
        if (!wp_check_password($password, $current_user->user_pass, $id)) {
            return Response::failure(
                "Password did not match",
                StatusCode::UNAUTHORIZED,
                ErrorCode::WRONG_PASSWORD
            );
        }
        if ($confirm_delete == "false") {
            return Response::failure(
                "Must include `confirm_delete` with request",
                StatusCode::BAD_REQUEST,
                ErrorCode::MISSING_PARAMETER
            );
        }
        if ($current_user->ID == 1) {
            return Response::failure(
                "Cannot delete specified user",
                StatusCode::UNAUTHORIZED,
                ErrorCode::INVALID_USER_IDENTIFIER
            );
        }
        if ($current_user->ID != $id) {
            return Response::failure(
                "Idenitifier mismatch, cannot delete user",
                StatusCode::UNAUTHORIZED,
                ErrorCode::INVALID_USER_IDENTIFIER
            );
        }
        if (current_user_can("administrator")) {
            return Response::failure(
                "Cannot delete user with elevated privileges",
                StatusCode::UNAUTHORIZED,
                ErrorCode::CANNOT_BE_DELETED
            );
        }
        if (!current_user_can("app_user")) {
            return Response::failure(
                "Cannot delete user which was not created through API",
                StatusCode::BAD_REQUEST,
                ErrorCode::CANNOT_BE_DELETED
            );
        }
        require_once "./wp-admin/includes/user.php";
        $is_deleted = wp_delete_user($id);
        if (!$is_deleted) {
            return Response::failure(
                "User could not be deleted",
                StatusCode::METHOD_NOT_ALLOWED,
                ErrorCode::DELETE_FAILED
            );
        }
        return Response::success([
            "token" => null,
            "status" => "no_authoriztion_header",
        ]);
    });
}
