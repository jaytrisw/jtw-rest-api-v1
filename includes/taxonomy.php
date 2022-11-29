<?php

function get_taxonomy_callback(WP_REST_Request $request): WP_REST_Response
{
	return Common::validate_api_key($request, function($request) {
		$count = Common::get_param(request: $request, parameter: 'count', default: '10');
		$page = Common::get_param(request: $request, parameter: 'page');
		$search = Common::get_param(request: $request, parameter: 'search');
		$term =  Common::get_param(request: $request, parameter: 'term');
		$slug = Common::get_param(request: $request, parameter: 'slug');
		if ($term) {
			$query = Common::generate_query(
				posts_per_page: $count,
				post_type: Post::POST_TYPE,
				page: $page,
				search: $search,
				tax_query: array(
					array(
						'taxonomy' => $slug,
						'field' => Constants::slug,
						'terms' => $term
					)
				)
			);
			return Response::success(Post::generate_elements_for($query->posts));
		}
		if ($slug) {
			$terms = get_terms($slug);
			if (taxonomy_exists($slug)) {
				return Response::success(Taxonomy::generate_elements_for($terms));
			}
			return Response::failure('Taxonomy \'' . $slug . '\' does not exist');
		}
		return Response::failure('Unknown error occured');
	});
}

class Taxonomy
{

	public static function generate_elements_for(array $terms): array
	{
		$terms_data = array();
		$i = 0;
		foreach ($terms as $term) {
			if ($term == 'post_format') {
				i++;
				continue;
			}
			$terms_data[$i] = Taxonomy::generate_element_for($term);
			$i++;
		}

		return $terms_data;
	}

	private static function generate_element_for(WP_Term $term): array
	{
		// Maybe revert this?
		// 'identifier' => $term->term_id,
		// 'term_taxonomy_identifier' => $term->term_taxonomy_id,
		return array(
			'identifier' => $term->term_taxonomy_id,
			'name' => $term->name,
			'slug' => $term->slug,
			'type' => $term->taxonomy,
			'description' => wp_strip_all_tags($term->description) ?: null,
			'parent' => $term->parent,
			'count' => $term->count
		);
	}
}