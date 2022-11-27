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

function create_profile_callback(WP_REST_Request $request)
{
	return Common::validate_api_key($request, function ($request) {
		$username = Common::get_param($request, 'username');
		$password = Common::get_param($request, 'password');
		$display_name = Common::get_param($request, 'display_name');
		$first_name = Common::get_param($request, 'first_name');
		$last_name = Common::get_param($request, 'last_name');
		$description = Common::get_param($request, 'description');
		$url = Common::get_param($request, 'url');
		$email = Common::get_param($request, 'email');
		$role = Common::get_param($request, 'role', 'app_user');

		$userdata = array(
			'user_login' => $username,
			'user_pass' => $password,
			'display_name' => $display_name,
			'first_name' => $first_name,
			'last_name' => $last_name,
			'description' => $description,
			'url' => $url,
			'user_email' => $email,
			'role' => $role,
		);

		return $userdata;
	
		if (empty($username) || empty($password) || empty($display_name) || empty($first_name) || empty($last_name) || empty($email)) {
			$message = array(
				'information' => 'Failed to parse required parameters from input',
				'parameters' => $userdata
			);
	
			return Response::failure($message);
		}

		return wp_insert_user($userdata);

	});
}