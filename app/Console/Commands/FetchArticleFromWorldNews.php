<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchArticleFromWorldNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-article-from-world-news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiKey = env('WORLDNEWS_API_KEY');
        $apiUrl = env('WORLDNEWS_API_URL');

        // The news API endpoint (using NewsAPI as an example)
        // $url = "https://api.worldnewsapi.com/top-news?source-country=us&language=en&api-key=6548f1efd0114702a18d3e3c25506509";
        $url = $apiUrl . "/search-news?source-country=us&language=en&number=50&api-key=" . $apiKey;

        // Fetch data from the API
        $response = Http::get($url);
        return $response->json();
     
        if ($response->successful()) {
            $articles = $response->json()['news'];

            foreach ($articles as $article) {
                // Save each article to the database (you can customize the fields)
                Article::updateOrCreate(
                    ['title' => $article['title']], 
                    [
                        'author' => $article['author']  ?? '',
                        'description' => $article['text']  ?? '',
                        'url' => $article['url']  ?? '',
                        'content' => '',
                        'image' => $article['image']  ?? '',
                        'source' => 'World News-',
                    ]
                );
            }

            $this->info('News articles fetched and saved successfully.');
        } else {
            $this->error('Failed to fetch data from the news API.');
        }

    }
}

// /top-news?source-country=us&language=en&api-key=6548f1efd0114702a18d3e3c25506509
