<?php require_once('../includes.php');

class FeaturedImage {

	public string $thumbnail;
    public string $full;

	public function __construct(
		string $thumbnail, 
		string $full) {
			$this->thumbnail = $thumbnail;
			$this->full = $full;
	}
}