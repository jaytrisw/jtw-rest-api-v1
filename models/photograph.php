<?php require_once('../includes.php');

class Photograph {
	public int $identifier;
	public string $title;
	public string $slug;
	public Timestamp $timestamp;
	public string $content;
	public ?Coordinate $coordinate;
	public FeaturedImage $featured_image;
	public string $url;
	public Photographer $photographer;
	public array $taxonomies;
	public Discussion $discussion;

	public function __construct(
		int $identifier,
		string $title,
		string $slug,
		Timestamp $timestamp,
		string $content,
		?Coordinate $coordinate,
		FeaturedImage $featured_image,
		string $url,
		Photographer $photographer,
		array $taxonomies,
		Discussion $discussion) {
			$this->identifier = $identifier;
			$this->title = $title;
			$this->slug = $slug;
			$this->timestamp = $timestamp;
			$this->content = $content;
			$this->coordinate = $coordinate;
			$this->featured_image = $featured_image;
			$this->url = $url;
			$this->photographer = $photographer;
			$this->taxonomies = $taxonomies;
			$this->discussion = $discussion;
	}
	
}