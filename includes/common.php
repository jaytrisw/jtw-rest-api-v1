<?php

class Common {

    static function generate_query(
        string $id,
        string $posts_per_page,
        string $post_type,
        string $page,
        string $search,
        array $tax_query): WP_Query
    {
        $arguments = array(
            'p' => $id,
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

}