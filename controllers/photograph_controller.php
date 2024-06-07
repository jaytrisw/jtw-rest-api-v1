<?php

class PhotographsController
{
    private PhotographDataSource $data_source;

    public function __construct(PhotographDataSource $data_source)
    {
        $this->data_source = $data_source;
    }

    public function get_photograph(int $identifier): Photograph
    {
        return $this->data_source->get_photograph($identifier);
    }

    public function get_photographs(
        int $count,
        int $page,
        string $search
    ): array {
        return $this->data_source->get_photographs($count, $page, $search);
    }
}
