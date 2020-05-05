<?php

namespace App\Http\Controllers;

use App\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // dd(\App\Article::all());
        // Log::info($request->items_per_page);
        return $articles = Article::latest('id')->paginate($request->items_per_page);
        // return view('articles.index', ['articles'=>$articles]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('articles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);
        $article = Article::create($validatedData);
        return $article;
        // return redirect(route('article.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function show(Article $article)
    {
        return view('articles.show', ['article'=>$article]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function edit(Article $article)
    {
        return $article;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Article $article)
    {
        $validatedData = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);
        $article->title = $request->title;
        $article->body = $request->body;
        $article->save();
        return $article;
        // return redirect()->route('article.show', $article->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy(Article $article)
    {
        $article->delete();
        return 'Article deleted';
    }
}
