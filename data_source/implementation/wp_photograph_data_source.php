<?php 
require_once('../../includes.php');

class WP_PhotographDataSource implements PhotographDataSource {
	public function get_photographs(): array {
        return [];
	}

	public function get_photograph(int $identifier): Photograph {
		$query = Common::generate_query(
			id: $identifier,
			post_type: Post::POST_TYPE
		);
		$post = current($query->posts);
	    return new Photograph(
			identifier: $post->ID,
			title: $post->post_title,
			slug: $post->post_name,
			timestamp: new Timestamp($post->post_date, $post->post_date_gmt),
			content: wp_strip_all_tags($post->post_content),
			coordinate: $this->get_coordinate_for(post: $post),
			featured_image: $this->get_featured_image_for($post),
			url: Common::format_url(get_permalink($post)),
			photographer: $this->get_photographer_for($post),
			taxonomies: $this->get_taxonomies_for($post),
			discussion: $this->get_discussion_for($post)
		);
	}
	
	private function get_featured_image_for(WP_Post $post): FeaturedImage {
	    return new FeaturedImage(
			thumbnail: get_the_post_thumbnail_url($post, 'thumbnail'),
			full: Common::format_url(get_the_post_thumbnail_url($post, 'full'))
		);
	}
	
	private function get_photographer_for(WP_Post $post): Photographer {
	    return new Photographer(
			identifier: intval($post->post_author),
			display_name: get_the_author_meta('display_name', $post->post_author),
			first_name: get_the_author_meta('first_name', $post->post_author) ?: null,
			last_name: get_the_author_meta('last_name', $post->post_author) ?: null,
			description: get_the_author_meta('description', $post->post_author) ?: null,
			avatar_url: Common::format_url(get_avatar_url($post->post_author))
		);
	}

	private function get_discussion_for(WP_Post $post): Discussion {

		$comments = get_approved_comments($post->ID);
		$comment_array = array();
		foreach ($comments as $comment) {
			array_push($comment_array, $this->get_comment_object_for($comment));
		}

		return new Discussion(
			count: intval($post->comment_count),
			comments: $comment_array
		);
	}

	private function get_comment_author_for(WP_Comment $comment): CommentAuthor {
		return new CommentAuthor(
			identifier: ($comment->user_id != 0) ? intval($comment->user_id) : null,
			display_name: $comment->comment_author,
			email: $comment->comment_author_email,
			url: Common::format_url($comment->comment_author_url),
			avatar_url: Common::format_url(avatar_url(get_avatar($comment)))
		);
	}

	private function get_comment_object_for(WP_Comment $comment): Comment {
		return new Comment(
			identifier: intval($comment->comment_ID),
			post_identifier: intval($comment->comment_post_ID),
			parent: ($comment->comment_parent != 0) ? intval($comment->comment_parent) : null,
			timestamp: new Timestamp($comment->comment_date, $comment->comment_date_gmt),
			author: $this->get_comment_author_for($comment),
			content: $comment->comment_content
		);
	}
	
	private function get_coordinate_for(WP_Post $post): ?Coordinate {
	    if (get_post_meta($post->ID, 'latScrollBlog', true) && get_post_meta($post->ID, 'longScrollBlog', true)) {
			return new Coordinate(
				latitude: floatval(get_post_meta($post->ID, 'latScrollBlog', true)),
				longitude: floatval(get_post_meta($post->ID, 'longScrollBlog', true))
			);
		}
		return null;
	}

	private function get_taxonomies_for(WP_Post $post): array {
		$taxonomies = get_post_taxonomies($post);

		$terms_data = array();
		$i = 0;
		foreach ($taxonomies as $taxonomy) {
		    if ($taxonomy != 'post_format') {
			    $terms = wp_get_post_terms($post->ID, $taxonomy);
			    foreach ($terms as $term) {
			        $new_taxonomy = new TaxonomyNew(
			            identifier: $term->term_taxonomy_id,
			            name: $term->name,
			            slug: $term->slug,
			            type: $term->taxonomy,
			            description: wp_strip_all_tags($term->description) ?: null,
			            parent: ($term->parent != 0) ? $term->parent : null,
			            count: $term->count
			        );
			        array_push($terms_data, $new_taxonomy);
			    }
		    }
			$i++;
		}
		return $terms_data; // call_user_func_array('array_merge', $terms_data);
	}
}