<?php

class Common {

    static function generate_query(
        string $posts_per_page,
        string $post_type,
        string $page,
        string $search,
        array $tax_query = null): WP_Query
    {
        $arguments = array(
            'posts_per_page' => $posts_per_page,
            'post_type' => $post_type,
            'paged' => $page,
            's' => $search,
            'tax_query' => $tax_query
        );

        return new WP_Query($arguments);
    }

}