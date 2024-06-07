<?php

class Comment {
	public int $identifier;
	public int $post_identifier;
	public ?int $parent;
	public Timestamp $timestamp;
	public CommentAuthor $author;
	public string $content;
	
	public function __construct(
		int $identifier,
		int $post_identifier,
		?int $parent,
		Timestamp $timestamp,
		CommentAuthor $author,
		string $content) {
			$this->identifier = $identifier;
			$this->post_identifier = $post_identifier;
			$this->parent = $parent;
			$this->timestamp = $timestamp;
			$this->author = $author;
			$this->content = $content;
		}
}