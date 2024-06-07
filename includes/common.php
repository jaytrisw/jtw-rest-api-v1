<?php

class Common
{
    static function generate_query(
        string $id = "",
        string $slug = "",
        string $posts_per_page = "",
        string $post_type = "",
        string $page = "",
        string $search = "",
        array $tax_query = []
    ): WP_Query {
        wp_reset_query();
        $arguments = [
            "p" => $id,
            "name" => $slug,
            "posts_per_page" => $posts_per_page,
            "post_type" => $post_type,
            "paged" => $page,
            "s" => $search,
            "tax_query" => $tax_query,
        ];

        return new WP_Query($arguments);
    }

    static function generate_date_for(string $local, string $gmt): array
    {
        return [
            "local" => $local,
            "gmt" => $gmt,
        ];
    }

    static function get_param(
        WP_REST_Request $request,
        string $parameter,
        string $default = ""
    ): string {
        return sanitize_text_field($request->get_param($parameter)) ?: $default;
    }

    static function validate_api_key(
        WP_REST_Request $request,
        callable $callback
    ) {
        if (REST_API_KEY == Common::get_param($request, "api_key")) {
            return $callback($request);
        }
        return Response::failure(
            "Invalid API key",
            StatusCode::UNAUTHORIZED,
            ErrorCode::API_KEY
        );
    }

    static function validate_authenticated_request(
        WP_REST_Request $request,
        callable $callback
    ) {
        return Common::validate_api_key($request, function ($request) use (
            $callback
        ) {
            if (is_user_logged_in()) {
                return $callback($request);
            }
            return Response::failure(
                "Unauthenticated request",
                StatusCode::UNAUTHORIZED,
                ErrorCode::AUTHENTICATION
            );
        });
    }

    /// https://weichie.com/blog/curl-api-calls-with-php/
    static function post_request(
        string $url,
        string $data,
        ?string $token = null
    ): string {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        if ($data) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        // OPTIONS:
        curl_setopt($curl, CURLOPT_URL, $url);
        if (isset($token)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json",
                "Authorization " . $token,
            ]);
        } else {
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                "Content-Type: application/json",
            ]);
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: " . $token,
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // EXECUTE:
        $result = curl_exec($curl);
        if (!$result) {
            return "Call failed";
        }
        curl_close($curl);
        return $result;
    }

    static function format_url(string $url): string
    {
        $components = parse_url($url);
        return $components["scheme"] .
            "://" .
            $components["host"] .
            $components["path"];
    }
}
