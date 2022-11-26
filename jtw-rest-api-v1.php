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

add_action('rest_api_init', 'register_posts_route');
add_action('rest_api_init', 'register_post_route');
add_action('rest_api_init', 'register_taxonomy_route');

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

	register_rest_route(
		'main/v1',
		'authenticate',
		array(
			'methods' => WP_REST_SERVER::CREATABLE,
			'callback' => 'autenticate_post_callback'
		)
	);
}

function autenticate_post_callback(WP_REST_Request $request): WP_REST_Response
{
	$encoded_username = Common::get_param($request, 'username');
	$encoded_password = Common::get_param($request, 'password');

	$username = base64_decode($encoded_username);
	$password = base64_decode($encoded_password);

	$data_array = array(
		'username' => $username,
		'password' => $password
	);
	$make_call = callAPI('POST', 'https://www.joshuatwood.com/wp-json/jwt-auth/v1/token/', json_encode($data_array));
	$response = json_decode($make_call, true);

	if ($response['token']) {
		return Response::success(array('token' => $response['token']));
	}

	return Response::failure('Authentication failed.');
}

function callAPI(string $method, string $url, string $data): string
{
	$curl = curl_init();
	switch ($method) {
		case 'POST':
			curl_setopt($curl, CURLOPT_POST, 1);
			if ($data)
				curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			break;
		case 'PUT':
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
			if ($data)
				curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			break;
		default:
			if ($data)
				$url = sprintf("%s?%s", $url, http_build_query($data));
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