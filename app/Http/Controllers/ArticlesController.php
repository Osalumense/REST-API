<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Articles;
use App\Models\Comments;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class ArticlesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Return all articles
     */
    public function index()
    {
        $articles = Articles::orderBy('id', 'desc')
            ->with('tags')
            ->paginate(10);
        foreach ($articles as $article) {
            $article->short_description = Str::limit($article->full_text, 100);
        }
        return $this->successResponse('All Articles', $articles, 200);   
    }

    /**
     * @param $id
     * Like article
     */
    public function likeArticle($id)
    {
        try {
            $article = Articles::findOrFail($id);
            $article->likes_counter = (int)($article->likes_counter) + 1;
            $article->save();
            return $this->successResponse('TotalLikes', $article->likes_counter, 200);
        } catch (\Throwable $th) {
            return $this->errorResponse('An error occurred', $th->getMessage(), 400);
        }
        
    }

    public function viewArticle($id)
    {
        try {

            $cacheKey = "article_{$id}_views";
            $cacheDuration = 5; // in seconds

            // Check if the view count is already in the cache
            $viewCount = Cache::get($cacheKey, 0);

            // If the view count is not in the cache, fetch it from the database
            if ($viewCount === 0) {
                $article = Articles::findOrFail($id);
                $viewCount = $article->views_counter;
            }


            $viewCount++;
            Cache::put($cacheKey, $viewCount, $cacheDuration);

            // Check if it's time to write the view count to the database
            if (time() % 5 === 0) {
                $article = Articles::findOrFail($id);
                $article->views_counter = $viewCount;
                $article->save();
            }
            return $this->successResponse('TotalViews', $article->views_counter, 200);  
        } catch (\Throwable $th) {
            return $this->errorResponse('An error occurred', $th->getMessage(), 400);
        }

    }

    public function commentOnArticle(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $comment = new Comments();
            $validation = Validator::make($request->all(), $comment->rules());
            if($validation->fails()) {
                return $this->errorResponse('Validation error', $validation->errors(), 400);
            }
            $articleId = Articles::lockForUpdate()->findOrFail($id);
            $comment->body = $request->body;
            $comment->subject = $request->subject;
            $comment->articles_id = $articleId->id;
            $comment->save();
            DB::commit();
            return $this->successResponse('Your comment has been successfully sent', $comment, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->errorResponse('Your comment was not saved', $e->getMessage(), 400);
        }
    }

    /**
     * @param $id
     */
    public function viewSingleArticle($id)
    {
        try {
            $article = Articles::findOrFail($id);
            $article->with('tags', 'comments');
            $article->makeVisible(['likes_counter', 'views_counter', 'tags_id', 'full_text']);
            return $this->successResponse('Single Article', $article, 200);
        } catch (\Throwable $th) {
            return $this->errorResponse('An error occurred error', $th->getMessage(), 400);
        }
    }
}
