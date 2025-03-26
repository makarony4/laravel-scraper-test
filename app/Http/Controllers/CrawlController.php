<?php

namespace App\Http\Controllers;

use App\Service\CrawlManager;
use Illuminate\Http\Request;
class CrawlController extends Controller
{
    public function __construct(private CrawlManager $crawlManager)
    {
    }

    public function index()
    {
        $this->crawlManager->getArticles();
    }
}
