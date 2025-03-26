<?php

namespace App\Interface;

use App\DTO\ArticleDTO;

interface CrawlerInterface
{
    public function sendRequest();

    public function getArticles();

    public function storeArticles(ArticleDTO $articleDTO);
}
