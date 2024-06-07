<?php 

class TaxonomyNew {

	public int $identifier;
	public string $name;
	public string $slug;
	public string $type;
	public ?string $description;
	public ?int $parent;
	public int $count;

	public function __construct(
		int $identifier,
		string $name,
		string $slug,
		string $type,
		?string $description,
		?int $parent,
		int $count) {
			$this->identifier = $identifier;
			$this->name = $name;
			$this->slug = $slug;
			$this->type = $type;
			$this->description = $description;
			$this->parent = $parent;
			$this->count = $count;
	}
}