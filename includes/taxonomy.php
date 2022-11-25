<?php

function generate_element_for(WP_Term $term): array
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
		'count' => $term->count,
	);
}