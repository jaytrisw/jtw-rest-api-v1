<?php require_once('../includes.php');

class Coordinate {
	public float $latitude;
	public float $longitude;

	public function __construct(
		float $latitude, 
		float $longitude) {
			$this->latitude = $latitude;
			$this->longitude = $longitude;
	}
}