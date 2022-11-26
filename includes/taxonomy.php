<?php

class Taxonomy
{
	private static function generate_taxonomy_element_for($term): array
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

	static function generate_taxonomy_elements_for(array $terms): array
	{
		$terms_data = array();
		$i = 0;
		foreach ($terms as $term) {
			$terms_data[$i] = generate_taxonomy_element_for($term);
			$i++;
		}

		return $terms_data;
	}
}