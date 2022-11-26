<?php

/*
Plugin Name: Joshua T. Wood, Photography API, Version 1
Plugin URI: https://www.joshuatwood.com
Description: Custom REST API for Joshua T. Wood, Photography
Author: Joshua T. Wood
Author URI: https://www.joshuatwood.com
Version: 1.0.0
*/

require('includes/post.php');
require('includes/taxonomy.php');
require('constants.php');
require('common.php');

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
			sanitize_text_field($request->get_param('count')) ?: '10',
			'post',
			sanitize_text_field($request->get_param('page')) ?: '0',
			sanitize_text_field($request->get_param('search')) ?: '',
			array(
				array(
					'taxonomy' => sanitize_text_field($request->get_param('slug')),
					'field' => Contansts::slug,
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