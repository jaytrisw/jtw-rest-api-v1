<?php 

class Timestamp {
	public string $local;
	public string $gmt;

	public function __construct(
		string $local, 
		string $gmt) {
			$this->local = $local;
			$this->gmt = $gmt;
	}
}