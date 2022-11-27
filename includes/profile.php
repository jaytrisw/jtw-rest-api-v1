<?php

function validate_post_callback(WP_REST_Request $request)
{
	return Common::validate_api_key($request, function ($request) {
		$bearer_token = $request->get_header('Authorization');
		$post_request = Common::post_request('https://www.joshuatwood.com/wp-json/jwt-auth/v1/token/validate', json_encode(array()), $bearer_token);
		$response = json_decode($post_request, true);
		$token = str_replace('Bearer ', '', $bearer_token);

		switch ($response['code']) {
			case 'jwt_auth_valid_token':
				return Response::success(array('token' => null, 'status' => 'valid_access_token'));
			case 'jwt_auth_no_auth_header':
				return Response::success(array('token' => $token, 'status' => 'no_authoriztion_header'));
			case 'jwt_auth_invalid_token':
				if ($response['message'] == 'Expired token') {
					return Response::success(array('token' => $token, 'status' => 'expired_access_token'));
				}
				return Response::success(array('token' => $token, 'status' => 'invalid_access_token'));
			default:
				return Response::success(array('token' => $token, 'status' => 'unknown_error'));
		}
	});
}

function profile_callback(WP_REST_Request $request): WP_REST_Response
{
	return Common::validate_authenticated_request($request, function ($request) {
		$current_user = wp_get_current_user();

		$user = array(
			'identifier' => intval($current_user->ID),
			'display_name' => get_the_author_meta('display_name', $current_user->ID),
			'first_name' => get_the_author_meta('first_name', $current_user->ID) ?: null,
			'last_name' => get_the_author_meta('last_name', $current_user->ID) ?: null,
            'email' => get_the_author_meta('user_email', $current_user->ID),
			'description' => get_the_author_meta('description', $current_user->ID) ?: null,
			'registered' => get_the_author_meta('user_registered', $current_user->ID) ?: null,
			'url' => get_the_author_meta('user_url', $current_user->ID) ?: null,
			'avatar_url' => get_avatar_url($current_user->ID)
		);

		return Response::success($user);
	});
}

function autenticate_post_callback(WP_REST_Request $request): WP_REST_Response
{
	return Common::validate_api_key($request, function ($request) {

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
					'token' => $response['token'],
					'status' => 'valid_access_token'
				)
			);
		}

		return Response::failure('Authentication failed.');

	});

}

function create_profile_callback(WP_REST_Request $request): WP_REST_Response
{
	return Common::validate_api_key($request, function ($request) {
		$encoded_username = Common::get_param($request, 'username');
		$encoded_password = Common::get_param($request, 'password');
		$username = base64_decode($encoded_username);
		$password = base64_decode($encoded_password);
		$display_name = Common::get_param($request, 'display_name');
		$first_name = Common::get_param($request, 'first_name');
		$last_name = Common::get_param($request, 'last_name');
		$description = Common::get_param($request, 'description');
		$url = Common::get_param($request, 'url');
		$email = Common::get_param($request, 'email');
		
		$userdata = array(
			'user_login' => $username,
			'user_pass' => $password,
			'display_name' => $display_name,
			'first_name' => $first_name,
			'last_name' => $last_name,
			'description' => $description,
			'user_url' => $url,
			'user_email' => $email,
			'role' => 'app_user',
		);

		if (empty($username) || empty($password) || empty($display_name) || empty($first_name) || empty($last_name) || empty($email)) {
			$message = array(
				'information' => 'Failed to parse required parameters from input',
				'parameters' => $userdata
			);
	
			return Response::failure($message);
		}
		
		if (get_user_by('email', $email) || get_user_by('login', $username)) {
		    return Response::failure('A user with already exists with specified parameters');
		}
		
		$new_user_id = wp_insert_user($userdata);
		
		if (is_wp_error($new_user_id)) {
		    Response::failure($new_user_id->get_error_message());
	    }

		return autenticate_post_callback($request);

	});
}