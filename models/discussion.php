<?php require_once('../includes.php');

class Discussion {
    public int $count;
    public array $comments;
    
    public function __construct(
        int $count,
        array $comments) {
            $this->count = $count;
			$this->comments = $comments;
    }
}