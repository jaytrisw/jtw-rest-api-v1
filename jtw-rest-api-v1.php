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
require('includes/profile.php');
require('includes/constants.php');
require('includes/common.php');
require('includes/response.php');

add_filter('jwt_auth_expire', 'on_jwt_expire_token', 10, 1);
function on_jwt_expire_token()
{
	return time() + (86400 * 365);
}

add_action('rest_api_init', 'register_posts_route');
add_action('rest_api_init', 'register_post_routes');
add_action('rest_api_init', 'register_taxonomy_route');
add_action('rest_api_init', 'register_profile_routes');

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

function register_post_routes()
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

	register_rest_route(
		'main/v1',
		'post/(?P<id>[\d]+)/discussion',
		array(
			'methods' => WP_REST_SERVER::READABLE,
			'callback' => 'get_main_discussion_for_post_with_id'
		)
	);

	register_rest_route(
		'main/v1',
		'post/(?P<id>[\d]+)/discussion',
		array(
			'methods' => WP_REST_SERVER::CREATABLE,
			'callback' => 'post_main_discussion_for_post_with_id'
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

function register_profile_routes()
{
	register_rest_route(
		'main/v1',
		'profile/authenticate',
		array(
			'methods' => WP_REST_SERVER::CREATABLE,
			'callback' => 'autenticate_post_callback'
		)
	);

	register_rest_route(
		'main/v1',
		'profile/validate',
		array(
			'methods' => WP_REST_SERVER::READABLE,
			'callback' => 'validate_post_callback'
		)
	);

	register_rest_route(
		'main/v1',
		'profile',
		array(
			'methods' => WP_REST_SERVER::READABLE,
			'callback' => 'profile_callback'
		)
	);

	register_rest_route(
		'main/v1',
		'profile',
		array(
			'methods' => WP_REST_SERVER::CREATABLE,
			'callback' => 'create_profile_callback'
		)
	);
}