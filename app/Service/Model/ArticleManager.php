<?php

namespace App\Service\Model;

use App\DTO\ArticleDTO;
use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ArticleManager
{
    public function createArticle(ArticleDTO $articleDTO)
    {
        $article = new Article();

        try {
            $article->title = $articleDTO->title;
            $article->url = $articleDTO->url;
            $article->publication_date = $articleDTO->publicationDate;
            $article->categories = $articleDTO->categories;
            $article->save();
            return $article;
        } catch (\Throwable $e) {
            Log::error('Error while creating article with title: ' . $articleDTO->title, [$e->getMessage()]);
            return new \Exception('Error while creating article with title: ' . $articleDTO->title, $e->getCode());
        }
    }

    public function reformatArticleDate(string $publicationDate)
    {
        $date = Carbon::createFromFormat('M d, Y', $publicationDate);
        return $date->format('Y-m-d');
    }
    public function createArticleDTO(string $title, string $url, string $publicationDate, array $categories): ArticleDTO
    {
        $formattedDate = $this->reformatArticleDate($publicationDate);
        return new ArticleDTO($title, $url, $formattedDate, json_encode($categories));
    }

}
