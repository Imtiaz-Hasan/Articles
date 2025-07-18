<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    // GET /api/articles/mine
    public function mine(Request $request)
    {
        $articles = $request->user()->articles()->with('category')->get();
        return response()->json($articles);
    }

    // POST /api/articles
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required',
            'status' => 'required|in:draft,published',
            'category_id' => 'required|exists:categories,id',
        ]);
        $slug = Str::slug($request->title);
        $slug = Article::where('slug', $slug)->exists() ? $slug . '-' . uniqid() : $slug;
        $article = $request->user()->articles()->create([
            'title' => $request->title,
            'slug' => $slug,
            'body' => $request->body,
            'status' => $request->status,
            'category_id' => $request->category_id,
        ]);
        return response()->json($article, 201);
    }

    // GET /api/articles/{id}
    public function show($id, Request $request)
    {
        $article = Article::findOrFail($id);
        if ($article->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        return response()->json($article);
    }

    // PUT /api/articles/{id}
    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        if ($article->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'body' => 'sometimes|required',
            'status' => 'sometimes|required|in:draft,published',
            'category_id' => 'sometimes|required|exists:categories,id',
        ]);
        if ($request->has('title')) {
            $slug = Str::slug($request->title);
            $slug = Article::where('slug', $slug)->where('id', '!=', $article->id)->exists() ? $slug . '-' . uniqid() : $slug;
            $article->slug = $slug;
            $article->title = $request->title;
        }
        if ($request->has('body')) $article->body = $request->body;
        if ($request->has('status')) $article->status = $request->status;
        if ($request->has('category_id')) $article->category_id = $request->category_id;
        $article->save();
        return response()->json($article);
    }

    // DELETE /api/articles/{id}
    public function destroy($id, Request $request)
    {
        $article = Article::findOrFail($id);
        if ($article->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $article->delete();
        return response()->json(['message' => 'Article soft deleted']);
    }

    // GET /api/articles (public)
    public function publicIndex(Request $request)
    {
        $query = Article::where('status', 'published');
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category)->orWhere('name', $request->category);
            });
        }
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        $articles = $query->with(['category', 'user'])->get();
        return response()->json($articles);
    }

    // GET /api/articles/public/{id} (public)
    public function publicShow($id)
    {
        $article = Article::where('id', $id)->where('status', 'published')->firstOrFail();
        return response()->json($article);
    }
}
