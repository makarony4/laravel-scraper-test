<?php

namespace App\DTO;

class ArticleDTO
{
    public function __construct(
        public string $title,
        public string $url,
        public string $publicationDate,
        public string $categories
    )
    {
    }
}
