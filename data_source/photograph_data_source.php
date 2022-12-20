<?php 

interface PhotographDataSource {

	public function get_photographs(): array;
	public function get_photograph(int $identifier): Photograph; 

}