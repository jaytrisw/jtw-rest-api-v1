<?php

class Photographer
{
    public int $identifier;
    public string $display_name;
    public ?string $first_name;
    public ?string $last_name;
    public ?string $description;
    public string $avatar_url;

    public function __construct(
        int $identifier,
        string $display_name,
        ?string $first_name,
        ?string $last_name,
        ?string $description,
        string $avatar_url
    ) {
        $this->identifier = $identifier;
        $this->display_name = $display_name;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->description = $description;
        $this->avatar_url = $avatar_url;
    }
}
