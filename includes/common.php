<?php

class Common
{

    static function generate_query(
        string $id = '',
        string $slug = '',
        string $posts_per_page = '',
        string $post_type = '',
        string $page = '',
        string $search = '',
        array $tax_query = array()): WP_Query
    {
        $arguments = array(
            'p' => $id,
            'name' => $slug,
            'posts_per_page' => $posts_per_page,
            'post_type' => $post_type,
            'paged' => $page,
            's' => $search,
            'tax_query' => $tax_query
        );

        return new WP_Query($arguments);
    }

    static function generate_date_for(string $local, string $gmt): array
    {
        return array(
            'local' => $local,
            'gmt' => $gmt
        );
    }

    static function get_param(WP_REST_Request $request, string $parameter, string $default = ''): string
    {
        return sanitize_text_field($request->get_param($parameter)) ?: $default;
    }

    static function validate_api_key(WP_REST_Request $request, callable $callback)
    {
        if (REST_API_KEY == Common::get_param($request, 'api_key')) {
            return $callback($request);
        }
        return Response::failure('Invalid API key');
    }

    static function validate_authenticated_request(WP_REST_Request $request, callable $callback)
    {
        return Common::validate_api_key($request, function ($request) use ($callback) {
            if (is_user_logged_in()) {
                return $callback($request);
            }
            return Response::failure('Unauthenticated request');
        });
    }

    /// https://weichie.com/blog/curl-api-calls-with-php/
    static function post_request(string $url, ?string $data): string
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, 1);
        if ($data) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        } else {
            curl_setopt($curl, CURLOPT_POSTFIELDS, []);
        }

        // OPTIONS:
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // EXECUTE:
        $result = curl_exec($curl);
        if (!$result) {
            return 'Call failed';
        }
        curl_close($curl);
        return $result;
    }

}