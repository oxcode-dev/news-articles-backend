<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchNewsFromApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-news-from-api';

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

        $apiKey = env('NEWS_API_KEY');
        $apiUrl = env('NEWS_API_URL');

        // The news API endpoint (using NewsAPI as an example)
        $url = $apiUrl . "/top-headlines?country=us&apiKey=" . $apiKey;

        // Fetch data from the API
        $response = Http::get($url);
        // return $response->json();
     
        if ($response->successful()) {
            $articles = $response->json()['articles'];

            foreach ($articles as $article) {
                // Save each article to the database (you can customize the fields)
                Article::updateOrCreate(
                    ['title' => $article['title']], 
                    [
                        'author' => $article['author']  ?? '',
                        'description' => $article['description']  ?? '',
                        'url' => $article['url']  ?? '',
                        'content' => $article['content']  ?? '',
                        'image' => $article['urlToImage']  ?? '',
                        'source' => $article['source']['name'] ?? '',
                    ]
                );
            }

            $this->info('News articles fetched and saved successfully.');
        } else {
            $this->error('Failed to fetch data from the news API.');
        }
    }
}
