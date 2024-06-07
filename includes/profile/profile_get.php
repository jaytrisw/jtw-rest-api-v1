<?php

function profile_callback(WP_REST_Request $request): WP_REST_Response
{
    return Common::validate_authenticated_request($request, function (
        $request
    ) {
        $current_user = wp_get_current_user();

        $user = [
            "identifier" => intval($current_user->ID),
            "display_name" => get_the_author_meta(
                "display_name",
                $current_user->ID
            ),
            "first_name" =>
                get_the_author_meta("first_name", $current_user->ID) ?: null,
            "last_name" =>
                get_the_author_meta("last_name", $current_user->ID) ?: null,
            "email" => get_the_author_meta("user_email", $current_user->ID),
            "description" =>
                get_the_author_meta("description", $current_user->ID) ?: null,
            "registered" =>
                get_the_author_meta("user_registered", $current_user->ID) ?:
                null,
            "url" =>
                Common::format_url(
                    get_the_author_meta("user_url", $current_user->ID)
                ) ?:
                null,
            "avatar_url" => Common::format_url(
                get_avatar_url($current_user->ID)
            ),
        ];

        return Response::success($user);
    });
}
