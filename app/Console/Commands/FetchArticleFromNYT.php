<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class FetchArticleFromNYT extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-article-from-nyt';

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

        $apiKey = env('NEWYORKTIMES_API_KEY');
        $apiUrl = env('NEWYORKTIMES_API_URL');

        // The news API endpoint (using NewsAPI as an example)
        $url = $apiUrl . "?api-key=" . $apiKey;
        return $url;

        // Fetch data from the API
        $response = Http::get($url);
        // return $response->json();
     
        if ($response->successful()) {
            $articles = $response->json()['response']['docs'] ?? [];

            foreach ($articles as $article) {
                // Save each article to the database (you can customize the fields)
                Article::updateOrCreate(
                    ['title' => $article['headline']['main']], 
                    [
                        'author' => $article['byline']['original']  ?? '',
                        'description' => $article['snippet']  ?? '',
                        'url' => $article['web_url']  ?? '',
                        'content' => $article['lead_paragraph']  ?? '',
                        'image' => 'https://static01.nyt.com/' . $article['multimedia'][0]['image']  ?? '',
                        'source' => $article['source'] ?? '',
                    ]
                );
            }

            $this->info('News articles fetched and saved successfully.');
        } else {
            $this->error('Failed to fetch data from the news API.');
        }
    }
}
