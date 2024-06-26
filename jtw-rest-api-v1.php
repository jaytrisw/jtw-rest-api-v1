<?php

/**
 * Plugin Name: Joshua T. Wood, Photography API, Version 1
 * Description: Custom REST API for Joshua T. Wood, Photography
 * Author: Joshua T. Wood
 * Author URI: https://www.joshuatwood.com
 * Version: 1.0.0
 * Plugin URI: https://www.joshuatwood.com
 */

require_once "includes/post.php";
require_once "includes/taxonomy.php";
require_once "includes/profile.php";
require_once "includes/constants.php";
require_once "includes/common.php";
require_once "includes/response.php";
require_once "includes/error_code.php";
require_once "includes/status_code.php";

// Models
require_once "models/comment.php";
require_once "models/coordinate.php";
require_once "models/featured_image.php";
require_once "models/photographer.php";
require_once "models/timestamp.php";
require_once "models/comment_author.php";
require_once "models/discussion.php";
require_once "models/photograph.php";
require_once "models/taxonomy.php";

// Controllers
require_once "controllers/photograph_controller.php";

// DataSource
require_once "data_source/photograph_data_source.php";
require_once "data_source/implementation/wp_photograph_data_source.php";

add_filter("jwt_auth_expire", "on_jwt_expire_token", 10, 1);
function on_jwt_expire_token()
{
    return time() + 86400 * 365;
}

add_action("rest_api_init", "register_posts_route");
add_action("rest_api_init", "register_post_routes");
add_action("rest_api_init", "register_taxonomy_route");
add_action("rest_api_init", "register_profile_routes");

function register_posts_route()
{
    register_rest_route("main/v1", "posts", [
        "methods" => WP_REST_SERVER::READABLE,
        "callback" => "get_main_posts",
    ]);
}

function register_post_routes()
{
    register_rest_route("main/v1", "post/(?P<id>[0-9-]+)", [
        "methods" => WP_REST_SERVER::READABLE,
        "callback" => "get_main_post_with_id",
    ]);

    register_rest_route("main/v1", "post/(?P<id>[a-zA-Z0-9-]+)", [
        "methods" => WP_REST_SERVER::READABLE,
        "callback" => "get_main_post_with_slug",
    ]);

    register_rest_route("main/v1", "post/(?P<id>[\d]+)/discussion", [
        "methods" => WP_REST_SERVER::READABLE,
        "callback" => "get_main_discussion_for_post_with_id",
    ]);

    register_rest_route("main/v1", "post/(?P<id>[\d]+)/discussion", [
        "methods" => WP_REST_SERVER::CREATABLE,
        "callback" => "post_main_discussion_for_post_with_id",
    ]);
}

function register_taxonomy_route()
{
    register_rest_route("main/v1", "taxonomy/(?P<slug>[a-zA-Z-]+)", [
        "methods" => WP_REST_SERVER::READABLE,
        "callback" => "get_taxonomy_callback",
    ]);
}

function register_profile_routes()
{
    register_rest_route("main/v1", "profile/authenticate", [
        "methods" => WP_REST_SERVER::CREATABLE,
        "callback" => "autenticate_post_callback",
    ]);

    register_rest_route("main/v1", "profile/validate", [
        "methods" => WP_REST_SERVER::READABLE,
        "callback" => "validate_post_callback",
    ]);

    register_rest_route("main/v1", "profile", [
        "methods" => WP_REST_SERVER::READABLE,
        "callback" => "profile_callback",
    ]);

    register_rest_route("main/v1", "profile", [
        "methods" => WP_REST_SERVER::CREATABLE,
        "callback" => "create_profile_callback",
    ]);

    register_rest_route("main/v1", "profile/(?P<id>[\d]+)", [
        "methods" => WP_REST_SERVER::EDITABLE,
        "callback" => "update_profile_callback",
    ]);

    register_rest_route("main/v1", "profile/(?P<id>[\d]+)", [
        "methods" => WP_REST_SERVER::DELETABLE,
        "callback" => "delete_profile_callback",
    ]);

    register_rest_route("main/v1", "photographs/(?P<id>[\d]+)", [
        "methods" => WP_REST_SERVER::READABLE,
        "callback" => "get_photograph",
    ]);

    register_rest_route("main/v1", "photographs", [
        "methods" => WP_REST_SERVER::READABLE,
        "callback" => "get_photographs",
    ]);
}

function get_photograph(WP_REST_Request $request): WP_REST_Response
{
    return Common::validate_api_key($request, function ($request) {
        $identifier = Common::get_param($request, "id");
        $data_source = new WP_PhotographDataSource();
        $controller = new PhotographsController($data_source);
        return Response::success(
            $controller->get_photograph(intval($identifier))
        );
    });
}

function get_photographs(WP_REST_Request $request): WP_REST_Response
{
    return Common::validate_api_key($request, function ($request) {
        $count = Common::get_param($request, "count", "10");
        $page = Common::get_param($request, "page", "0");
        $search = Common::get_param($request, "search");
        $data_source = new WP_PhotographDataSource();
        $controller = new PhotographsController($data_source);
        return Response::success(
            $controller->get_photographs(intval($count), intval($page), $search)
        );
    });
}
