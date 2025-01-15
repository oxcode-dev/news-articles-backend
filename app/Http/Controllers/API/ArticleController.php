<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class ArticleController extends Controller
{
    public function index(Request $request) 
    {
        // return $request->all();
        $articles = Article::search($request->search)->whereNot('title', '[Removed]');
            
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

    public function show(Request $request, $id) 
    {
        $article = Article::whereId($id)->firstOrFail();
        
        return response()->json([
            'data' => $article,
        ]);
    }
}