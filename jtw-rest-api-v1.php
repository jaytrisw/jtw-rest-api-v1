<?php

/**
 * Plugin Name: Joshua T. Wood, Photography API, Version 1
 * Description: Custom REST API for Joshua T. Wood, Photography
 * Author: Joshua T. Wood
 * Author URI: https://www.joshuatwood.com
 * Version: 1.0.0
 * Plugin URI: https://www.joshuatwood.com
*/

require('includes/post.php');
require('includes/taxonomy.php');
require('includes/constants.php');
require('includes/common.php');

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

function get_main_taxonomy(WP_REST_Request $request): array
{
	if (sanitize_text_field($request->get_param('term'))) {
		$query = Common::generate_query(
			posts_per_page: sanitize_text_field($request->get_param('count')) ?: '10',
			post_type: 'post',
			page: sanitize_text_field($request->get_param('page')) ?: '0',
			search: sanitize_text_field($request->get_param('search')) ?: '',
			tax_query: array(
				array(
					'taxonomy' => sanitize_text_field($request->get_param('slug')),
					'field' => Constants::slug,
					'terms' => sanitize_text_field($request->get_param('term'))
				)
			)
		);

		return Post::generate_elements_for($query->posts);
	}
	return error('Please include a query for `term`');
}

function get_main_taxonomies(WP_REST_Request $request): array
{
	$terms = get_terms(
		sanitize_text_field($request->get_param('slug'))
	);
	return Taxonomy::generate_elements_for($terms);
}

function error(string $message): array
{
	return array(
		'title' => 'Error',
		'message' => $message
	);
}