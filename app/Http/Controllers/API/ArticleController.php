<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\User;
use function PHPUnit\Framework\isEmpty;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request) : JsonResponse
    {
        // return $request->all();
        $articles = Article::search($request->search)->whereNot('title', '[Removed]');
            
        if ($request->has('source')) {
            $articles->where('source', $request->source);
        }

        if ($request->has('date')) {
            $articles->where('created_at','>=', $request->date);
        }

        return response()->json([
            'data' => $articles->paginate(10)->withQueryString(),
            'status' => 'success',
        ]);
    }

    public function show(Request $request, $id) 
    {
        $article = Article::whereId($id)->first();
        
        return response()->json([
            'data' => $article ?? [],
            'status' => $article->exists() ? 'success' : 'error',
        ]);
    }

    public function preferredArticles(Request $request) 
    {
        $user = $request->user('api');
        // $user = User::first();
        $sources = $user->sources;
        $authors = $user->authors;

        $articles = Article::search($request->search)
            ->whereNot('title', '[Removed]')
            ->whereIn('source', $sources)
            ->orWhereIn('author', $authors);
            
        if ($request->has('category')) {
            $articles->where('category', '>', $request->category);
        }

        if ($request->has('source')) {
            $articles->where('source', $request->source);
        }

        if ($request->has('date')) {
            $articles->where('created_at','>=', $request->date);
        }

        return response()->json([
            'data' => $articles->paginate(10)->withQueryString(),
            'status' => 'success',
        ]);
    }

    public function articlesSources(Request $request) 
    {
        $articles = Article::get();
        $authors = collect($articles)->where('author', '!==', '')->pluck('author')->unique()->all();
        $sources = collect($articles)->where('source', '!==', '')->pluck('source')->unique()->all();


        return response()->json([
            'data' => [
                'authors' => array_values($authors),
                'sources' => array_values($sources),
            ],
            'status' => 'success',
        ]);
    }
}




