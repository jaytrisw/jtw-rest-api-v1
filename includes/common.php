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
        if (is_user_logged_in()) {
            return $callback($request);
        }
        return Response::failure('Unauthenticated request');
    }

}