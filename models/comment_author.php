<?php require_once('../includes.php');

class CommentAuthor {
	public ?int $identifier;
	public string $display_name;
	public string $email;
	public ?string $url;
	public ?string $avatar_url;
	
	public function __construct(
		?int $identifier,
		string $display_name,
		string $email,
		?string $url,
		?string $avatar_url) {
			$this->identifier = $identifier;
			$this->display_name = $display_name;
			$this->email = $email;
			$this->url = $url;
			$this->avatar_url = $avatar_url;
		}
}