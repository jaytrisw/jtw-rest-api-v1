<?php

/*
Plugin Name: Joshua T. Wood, Photography REST API, Version 1
Plugin URI: https://www.joshuatwood.com
Description: Custom REST API for Joshua T. Wood, Photography
Author: Joshua T. Wood
Author URI: https://www.joshuatwood.com
Version: 1.0.0
*/

require('includes/post.php');
add_action('rest_api_init', 'register_posts_route');
add_action('rest_api_init', 'register_post_route');
add_action('rest_api_init', 'register_taxonomy_route');
add_action('rest_api_init', 'register_taxonomies_route');

function register_posts_route()
{
	register_rest_route(
		'main/v1',
		'posts',
		array(
			'methods' => WP_REST_SERVER::READABLE,
			'callback' => 'get_main_posts'
		)
	);
}

function register_taxonomy_route()
{
	register_rest_route(
		'main/v1',
		'taxonomy/(?P<slug>[a-zA-Z-]+)',
		array(
			'methods' => WP_REST_SERVER::READABLE,
			'callback' => 'get_main_taxonomy'
		)
	);
}

function register_taxonomies_route()
{
	register_rest_route(
		'main/v1',
		'taxonomies/(?P<slug>[a-zA-Z-]+)',
		array(
			'methods' => WP_REST_SERVER::READABLE,
			'callback' => 'get_main_taxonomies'
		)
	);
}

function register_post_route()
{
	register_rest_route(
		'main/v1',
		'post/(?P<id>[0-9-]+)',
		array(
			'methods' => WP_REST_SERVER::READABLE,
			'callback' => 'get_main_post_with_id'
		)
	);

	register_rest_route(
		'main/v1',
		'post/(?P<id>[a-zA-Z0-9-]+)',
		array(
			'methods' => WP_REST_SERVER::READABLE,
			'callback' => 'get_main_post_with_slug'
		)
	);
}

function get_main_taxonomy(WP_REST_Request $request)
{
	if ($request['term']) {
		$query = generate_query(
			$request['count'] ?: '10',
			'post',
			$request['page'] ?: '0',
			$request['search'] ?: '',
			array(
				array(
					'taxonomy' => $request['slug'],
					'field' => 'slug',
					'terms' => $request['term']
				)
			)
		);

		$data = array();
		$i = 0;

		foreach ($query->posts as $post) {
			$data[$i] = generate_element_for($post);
			$i++;
		}

		return $data;
	}
	return error('Please include a query for `term`');
}

function get_main_taxonomies(WP_REST_Request $request)
{
	$terms = get_terms($request['slug']);
	$terms_data = array();
	$i = 0;
	// Maybe revert this?
	// 'identifier' => $term->term_id,
	// 'term_taxonomy_identifier' => $term->term_taxonomy_id,
	foreach ($terms as $term) {
		$terms_data[$i] = array(
			'identifier' => $term->term_taxonomy_id,
			'name' => $term->name,
			'slug' => $term->slug,
			'type' => $term->taxonomy,
			'description' => wp_strip_all_tags($term->description) ?: null,
			'parent' => $term->parent,
			'count' => $term->count,
		);
		$i++;
	}

	$response = array(
		'status' => 200,
		'data' => $terms_data
	);

	return WP_REST_Response($response);
}

function generate_query(
	string $posts_per_page,
	string $post_type,
	string $page,
	string $search,
	array $tax_query
): WP_Query
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

function error(string $message): array
{
	return array(
		'title' => 'Error',
		'message' => $message
	);
}