<?php

interface PhotographDataSource
{
    public function get_photographs(
        int $count,
        int $page,
        string $search
    ): array;
    public function get_photograph(int $identifier): Photograph;
}
