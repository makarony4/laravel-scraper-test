<?php

namespace App\Service;


use App\DTO\ArticleDTO;
use App\Interface\CrawlerInterface;
use App\Models\Article;
use App\Service\Model\ArticleManager;
use Carbon\Carbon;
use Cassandra\Date;
use DateTime;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Http\Client\Response;

class CrawlManager implements CrawlerInterface
{
    private const MAIN_URL = "https://webmagic.agency/blog";
    private const LOGISTICS_INDUSTRY_CATEGORY_ID = 7;


    private ?string $token;

    private ?string $cookies;

    public function __construct(private ArticleManager $articleManager)
    {
    }

    public function sendRequest(string $url = self::MAIN_URL, int $page = null): Response
    {
        return Http::get($url);
    }

    public function getArticlesRequest(int $page = 1, string $url = self::MAIN_URL, int $categoryId = self::LOGISTICS_INDUSTRY_CATEGORY_ID): PromiseInterface|Response|\Exception
    {
        try {
            $response = Http::asForm()->withHeaders([
                "Cookie" => $this->cookies
            ])->post("$url?category=$categoryId", [
                "_token" => $this->token,
                "page" => $page,
            ]);

            if ($response->getStatusCode() === 200) {
                return $response;
            } else {
                Log::error("Error status code from " . self::MAIN_URL, [$response->getStatusCode()]);
                return new \Exception("Error on sending request to " . self::MAIN_URL, $response->getStatusCode());
            }
        } catch (\Throwable $e) {
            Log::error("Error while send request for getting articles", [$e->getMessage()]);
            return new \Exception("Error while send request for getting articles", $e->getCode());
        }
    }

    public function getTokenAndCookiesRequest(): void
    {
        $response = $this->sendRequest();
        $cookies = $response->getHeaders()['Set-Cookie'];
        $this->setFormattedHeaderCookies($cookies);
        $html = $response->getBody()->getContents();
        $crawler = new Crawler($html);
        $this->setCSRFToken($crawler->filter('input[name="_token"]')->attr('value'));
    }

    public function getArticles(DateTime $dateTimeDepth = null)
    {
        $this->getTokenAndCookiesRequest();
        $maxDepthDate = $this->getMaxDepthDate($dateTimeDepth ?? null);
        $isMaxDepth = false;
        try {
            for ($page = 1; $isMaxDepth === false; $page++) {
                $html = $this->getArticlesRequest($page)->getBody()->getContents();
                $crawler = new Crawler($html);
                foreach ($crawler->filter('.articles-row') as $node) {
                    $node = new Crawler($node);
                    $date = $node->filter('a > .articles-r > .articles-r__top > .articles-date')->text();

                    if ($this->isMaxDepthDate($date, $maxDepthDate)) {
                        break;
                    }
                    $href = $node->filter('a')->attr('href');
                    $articleTitle = $node->filter('a > .articles-r > .articles-ttl')->text();
                    $categories = $node->filter('a > .articles-r > .articles-r__top > .articles-categories > .articles-categories__item')
                        ->each(function (Crawler $node) {
                            return $node->text();
                        });
                    $articleDTO = $this->articleManager->createArticleDTO($articleTitle, $href, $date, $categories);
                    $this->storeArticles($articleDTO);
                }
                $isMaxDepth = true;
            }
        }catch (\Throwable $exception)
        {
            Log::error($exception->getMessage());
            return new \Exception("Error while getting articles" . $exception->getMessage());
        }
    }

    public function storeArticles(ArticleDTO $articleDTO): void
    {
        $this->articleManager->createArticle($articleDTO);
    }

    public function setFormattedHeaderCookies(array $cookies): void
    {
        $this->cookies = $cookies[0] . ";" . $cookies[1];
    }

    public function setCSRFToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function isMaxDepthDate(string $unformattedPublicationDate, DateTime $maxDepthDate): bool
    {
        $publicationDate = Carbon::createFromFormat("M d, Y", $unformattedPublicationDate);
        $publicationDate = new DateTime($publicationDate->format("d-m-Y"));

        return $publicationDate < $maxDepthDate;
    }

    public function getMaxDepthDate(DateTime $customDateDepth = null): DateTime
    {
        return $customDateDepth ?? new DateTime("-4 month");
    }

    public function checkForNewArticles(): array
    {
        try {
            $this->getTokenAndCookiesRequest();

            $article = Article::query()->orderBy("publication_date", "desc")->first();
            $html = $this->getArticlesRequest()->getBody()->getContents();
            $crawler = new Crawler($html);

            $firstParsedArticle = $crawler->filter('.articles-row')->first();
            $parsedArticleDate = $firstParsedArticle->filter('a > .articles-r > .articles-r__top > .articles-date')
                ->text();
            $parsedHref = $crawler->filter('a')->attr('href');
            $reformattedParsedArticleDate = $this->articleManager->reformatArticleDate($parsedArticleDate);
            if ($reformattedParsedArticleDate >= $article->publication_date && $article->url == $parsedHref) {
                return ["info" => "Nothing to parse now. There is no new articles in webmagic."];
            }
            else{
                $this->getArticles(new DateTime($article->publication_date));
                return ["info" => "Article parse success"];

            }
        } catch (\Throwable $e)
        {
            return ["error" => "Error while parsing the articles. " . $e->getMessage()];
        }
    }
}
