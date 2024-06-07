<?php

function get_main_posts(WP_REST_Request $request): WP_REST_Response
{
    return Common::validate_api_key($request, function ($request) {
        $query = Common::generate_query(
            posts_per_page: Common::get_param($request, "count", "10"),
            post_type: Post::POST_TYPE,
            page: Common::get_param($request, "page"),
            search: Common::get_param($request, "search")
        );
        return Response::success(Post::generate_elements_for($query->posts));
    });
}

function get_main_post_with_id(WP_REST_Request $request): WP_REST_Response
{
    return Common::validate_api_key($request, function ($request) {
        $query = Common::generate_query(
            id: Common::get_param($request, "id"),
            post_type: Post::POST_TYPE
        );
        return Response::success(
            current(Post::generate_elements_for($query->posts))
        );
    });
}

function get_main_post_with_slug(WP_REST_Request $request): WP_REST_Response
{
    return Common::validate_api_key($request, function ($request) {
        $query = Common::generate_query(
            slug: Common::get_param($request, "slug"),
            post_type: Post::POST_TYPE
        );
        return Response::success(
            current(Post::generate_elements_for($query->posts))
        );
    });
}

function get_main_discussion_for_post_with_id(
    WP_REST_Request $request
): WP_REST_Response {
    return Common::validate_api_key($request, function ($request) {
        $query = Common::generate_query(
            id: Common::get_param($request, "id"),
            post_type: Post::POST_TYPE
        );
        return Response::success(
            current(Post::generate_elements_for($query->posts))["discussion"]
        );
    });
}

function post_main_discussion_for_post_with_id(
    WP_REST_Request $request
): WP_REST_Response {
    return Common::validate_authenticated_request($request, function (
        $request
    ) {
        $comment_content = Common::get_param($request, "content");
        $post_id = Common::get_param($request, "id");
        $comment_parent = Common::get_param($request, "parent");
        $current_user = wp_get_current_user();

        $commentdata = [
            "comment_content" => $comment_content,
            "comment_parent" => $comment_parent,
            "comment_post_ID" => $post_id,
            "user_id" => $current_user->ID,
        ];

        if (empty($comment_content) || empty($post_id)) {
            $message = [
                "information" =>
                    "Failed to parse required parameters from input",
                "parameters" => $commentdata,
            ];

            return Response::failure($message);
        }

        $new_comment_id = wp_insert_comment($commentdata);
        if (is_wp_error($new_comment_id)) {
            Response::failure($new_comment_id->get_error_message());
        }
        if (!empty($new_comment_id)) {
            return get_main_post_with_id($request);
        }
        return Response::failure("Unknown error occurred.");
    });
}

class Post
{
    const POST_TYPE = "post";

    public static function generate_elements_for(array $posts): array
    {
        $data = [];
        $i = 0;

        foreach ($posts as $post) {
            $data[$i] = Post::generate_element_for($post);
            $i++;
        }

        return $data;
    }

    private static function generate_element_for(WP_Post $post): array
    {
        return [
            "identifier" => $post->ID,
            "title" => $post->post_title,
            "slug" => $post->post_name,
            "date" => Common::generate_date_for(
                $post->post_date,
                $post->post_date_gmt
            ),
            "content" => wp_strip_all_tags($post->post_content),
            "coordinate" => Post::generate_location_for($post),
            "featured_image" => Post::generate_images_for($post),
            "url" => Common::format_url(get_permalink($post)),
            "photographer" => Post::generate_author_for($post),
            "taxonomies" => Post::generate_taxonomies_for($post),
            "discussion" => Post::generate_discussions_for($post),
        ];
    }

    private static function generate_location_for(WP_Post $post): ?array
    {
        if (
            get_post_meta($post->ID, "latScrollBlog", true) &&
            get_post_meta($post->ID, "longScrollBlog", true)
        ) {
            return [
                "latitude" => floatval(
                    get_post_meta($post->ID, "latScrollBlog", true)
                ),
                "longitude" => floatval(
                    get_post_meta($post->ID, "longScrollBlog", true)
                ),
            ];
        }
        return null;
    }

    private static function generate_images_for(WP_Post $post): array
    {
        return [
            "thumbnail" => get_the_post_thumbnail_url($post, "thumbnail"),
            "full" => Common::format_url(
                get_the_post_thumbnail_url($post, "full")
            ),
        ];
    }

    private static function generate_author_for(WP_Post $post): array
    {
        return [
            "identifier" => intval($post->post_author),
            "display_name" => get_the_author_meta(
                "display_name",
                $post->post_author
            ),
            "first_name" =>
                get_the_author_meta("first_name", $post->post_author) ?: null,
            "last_name" =>
                get_the_author_meta("last_name", $post->post_author) ?: null,
            "description" =>
                get_the_author_meta("description", $post->post_author) ?: null,
            "avatar_url" => Common::format_url(
                get_avatar_url($post->post_author)
            ),
        ];
    }

    private static function generate_taxonomies_for(WP_Post $post)
    {
        $taxonomies = get_post_taxonomies($post);

        $terms_data = [];
        $i = 0;
        foreach ($taxonomies as $taxonomy) {
            if ($taxonomy != "post_format") {
                $terms = wp_get_post_terms($post->ID, $taxonomy);
                $terms_data[$i] = Taxonomy::generate_elements_for($terms);
            }
            $i++;
        }
        return call_user_func_array("array_merge", $terms_data);
    }

    private static function generate_discussions_for(WP_Post $post): array
    {
        $comments = get_approved_comments($post->ID);
        $i = 0;
        $commentdata = [];
        foreach ($comments as $comment) {
            $commentdata[$i] = [
                "identifier" => intval($comment->comment_ID),
                "post_identifier" => intval($comment->comment_post_ID),
                "parent" => intval($comment->comment_parent),
                "date" => Common::generate_date_for(
                    $comment->comment_date,
                    $comment->comment_date_gmt
                ),
                "author" => [
                    "identifier" => intval($comment->user_id),
                    "display_name" => $comment->comment_author,
                    "email" => $comment->comment_author_email,
                    "url" => Common::format_url($comment->comment_author_url),
                    "avatar_url" => Common::format_url(
                        avatar_url(get_avatar($comment))
                    ),
                ],
                "content" => $comment->comment_content,
            ];
            $i++;
        }
        return [
            "count" => intval($post->comment_count),
            "comments" => $commentdata,
        ];
    }
}
