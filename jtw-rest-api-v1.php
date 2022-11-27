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
			'methods' => WP_REST_SERVER::CREATABLE,
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
}

function validate_post_callback(WP_REST_Request $request) {
	return Common::validate_api_key($request, function($request) {
		$post_request = Common::post_request('https://www.joshuatwood.com/wp-json/jwt-auth/v1/token/validate', json_encode($data_array));
		$response = json_decode($post_request, true);
		
		if ($response) {
		    return $response;
		}

		return Response::failure('Validation failed.');
	});
}

function profile_callback(WP_REST_Request $request): WP_REST_Response
{
	return Common::validate_authenticated_request($request, function($request) { 
		$current_user = wp_get_current_user();

		$user = array(
			'identifier' => intval($current_user->ID),
			'display_name' => get_the_author_meta('display_name', $current_user->ID),
			'first_name' => get_the_author_meta('first_name', $current_user->ID) ?: null,
			'last_name' => get_the_author_meta('last_name', $current_user->ID) ?: null,
			'description' => get_the_author_meta('description', $current_user->ID) ?: null,
			'registered' => get_the_author_meta('user_registered', $current_user->ID) ?: null,
			'url' => get_the_author_meta('user_url', $current_user->ID) ?: null,
			'avatar_url' => get_avatar_url($current_user->ID)
		);

		Response::success($user);
	});
}

function autenticate_post_callback(WP_REST_Request $request): WP_REST_Response
{
	return Common::validate_api_key($request, function($request) {

		$encoded_username = Common::get_param($request, 'username');
		$encoded_password = Common::get_param($request, 'password');

		$username = base64_decode($encoded_username);
		$password = base64_decode($encoded_password);

		$data_array = array(
			'username' => $username,
			'password' => $password
		);
		$post_request = Common::post_request('https://www.joshuatwood.com/wp-json/jwt-auth/v1/token/', json_encode($data_array));
		$response = json_decode($post_request, true);

		if ($response['token']) {
			return Response::success(
				array(
					'token' => $response['token']
				)
			);
		}

		return Response::failure('Authentication failed.');

	});

}