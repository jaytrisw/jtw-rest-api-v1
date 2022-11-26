<?php

define('POST_TYPE', 'post');

class Post
{

	public static function generate_elements_for(array $posts): array
	{
		$data = array();
		$i = 0;

		foreach ($posts as $post) {
			$data[$i] = Post::generate_element_for($post);
			$i++;
		}

		return $data;
	}

	private static function generate_element_for(WP_Post $post): array
	{
		return array(
			'identifier' => $post->ID,
			'title' => $post->post_title,
			'slug' => $post->post_name,
			'date' => Common::generate_date_for($post->post_date, $post->post_date_gmt),
			'content' => wp_strip_all_tags($post->post_content),
			'coordinate' => generate_location_for($post),
			'featured_image' => generate_images_for($post),
			'url' => get_permalink($post),
			'photographer' => generate_author_for($post),
			'taxonomies' => generate_taxonomies_for($post),
			'discussion' => generate_discussions_for($post)
		);
	}

}

function get_main_posts(WP_REST_Request $request)
{
	$arguments = array(
		'posts_per_page' => sanitize_text_field($request->get_param('count')) ?: '10',
		'post_type' => POST_TYPE,
		'paged' => sanitize_text_field($request->get_param('page')),
		's' => sanitize_text_field($request->get_param('search'))
	);

	return generate_json($arguments);
}

function get_main_post_with_id(WP_REST_Request $request)
{

	$query = Common::generate_query(
		sanitize_text_field($request->get_param('id')),
		null,
		POST_TYPE,
		null,
		null,
		null
	);

	return Post::generate_elements_for($query->posts);
	// $arguments = array(
	// 	'p' => sanitize_text_field($request->get_param('id')),
	// 	'post_type' => POST_TYPE
	// );

	// return current(generate_json($arguments));
}

function get_main_post_with_slug(WP_REST_Request $request)
{
	// $arguments = array(
	// 	'p' => sanitize_text_field($request->get_param('slug')),
	// 	'post_type' => POST_TYPE
	// );
	$query = Common::generate_query(
		sanitize_text_field($request->get_param('slug')),
		null,
		POST_TYPE,
		null,
		null,
		null
	);

	return current(Post::generate_elements_for($query->posts));
	// return current(generate_json($arguments));
}

function generate_json(array $arguments)
{
	$query = new WP_Query($arguments);
	return Post::generate_elements_for($query->posts);
}

function generate_discussions_for(WP_Post $post): array
{
	$comments = get_approved_comments($post->ID);
	$i = 0;
	$commentdata = array();
	foreach ($comments as $comment) {
		$commentdata[$i] = array(
			'identifier' => intval($comment->comment_ID),
			'post_identifier' => intval($comment->comment_post_ID),
			'parent' => intval($comment->comment_parent),
			'date' => Common::generate_date_for($comment->comment_date, $comment->comment_date_gmt),
			'author' => array(
				'identifier' => intval($comment->user_id),
				'display_name' => $comment->comment_author,
				'email' => $comment->comment_author_email,
				'url' => $comment->comment_author_url,
				'avatar_url' => avatar_url(get_avatar($comment))
			),
			'content' => $comment->comment_content
		);
		$i++;
	}
	return array(
		'count' => intval($post->comment_count),
		'comments' => $commentdata
	);
}

function generate_images_for(WP_Post $post): array
{
	return array(
		'thumbnail' => get_the_post_thumbnail_url($post, 'thumbnail'),
		'full' => get_the_post_thumbnail_url($post, 'full')
	);
}

function generate_location_for(WP_Post $post): ?array
{
	if (get_post_meta($post->ID, 'latScrollBlog', true) && get_post_meta($post->ID, 'longScrollBlog', true)) {
		return array(
			'latitude' => floatval(get_post_meta($post->ID, 'latScrollBlog', true)),
			'longitude' => floatval(get_post_meta($post->ID, 'longScrollBlog', true))
		);
	}
	return null;
}

function generate_author_for(WP_Post $post): array
{
	return array(
		'identifier' => intval($post->post_author),
		'display_name' => get_the_author_meta('display_name', $post->post_author),
		'first_name' => get_the_author_meta('first_name', $post->post_author) ?: null,
		'last_name' => get_the_author_meta('last_name', $post->post_author) ?: null,
		'description' => get_the_author_meta('description', $post->post_author) ?: null,
		'avatar_url' => get_avatar_url($post->post_author)
	);
}

function generate_taxonomies_for(WP_Post $post)
{
	$taxonomies = get_post_taxonomies($post);

	$terms_data = array();
	$i = 0;
	foreach ($taxonomies as $taxonomy) {
		$terms_data[$i] = generate_term_for($post, $taxonomy);
		$i++;
	}
	return call_user_func_array('array_merge', $terms_data);
}

function generate_term_for(WP_Post $post, string $term_name): array
{
	$terms = wp_get_post_terms($post->ID, $term_name);
	return Taxonomy::generate_elements_for($terms);
}