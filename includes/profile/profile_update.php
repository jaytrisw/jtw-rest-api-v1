<?php

function update_profile_callback(WP_REST_Request $request)
{
    return Common::validate_authenticated_request($request, function (
        $request
    ) {
        $id = Common::get_param($request, "id");
        $current_user = get_user_by("ID", $id);

        $display_name = Common::get_param($request, "display_name");
        $first_name = Common::get_param($request, "first_name");
        $last_name = Common::get_param($request, "last_name");
        $description = Common::get_param($request, "description");
        $url = Common::get_param($request, "url");
        $email = Common::get_param($request, "email");

        $userdata = [
            "ID" => $id,
        ];

        if (!empty($display_name)) {
            $userdata["display_name"] = $display_name;
        }
        if (!empty($first_name)) {
            $userdata["first_name"] = $first_name;
        }
        if (!empty($last_name)) {
            $userdata["last_name"] = $last_name;
        }
        if (!empty($description)) {
            $userdata["description"] = $description;
        }
        if (!empty($url)) {
            $userdata["user_url"] = $url;
        }
        if (!empty($email)) {
            $userdata["user_email"] = $email;
        }

        if ($current_user->ID != $id) {
            return Response::failure(
                "Identifier mismatch, cannot update user",
                StatusCode::FORBIDDEN,
                ErrorCode::INVALID_USER_IDENTIFIER
            );
        }

        $updated_user_id = wp_update_user($userdata);
        if (is_wp_error($updated_user_id)) {
            Response::failure(
                $updated_user_id->get_error_message(),
                StatusCode::FORBIDDEN,
                ErrorCode::UPDATE_FAILED
            );
        }

        return profile_callback($request);
    });
}
