<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class ArticleController extends Controller
{
    public function index(){
      $articles= Article::latest('publish_date')->get();

      if ($articles->isEmpty()){
        return response()->json([
            'status'=>Response::HTTP_NOT_FOUND,
            'message'=>'Article empty'
        ],Response::HTTP_NOT_FOUND);
      }else {
        return response()->json([
            'data'=>$articles->map(function($article){
                return[
                    'title'=> $article->title,
                    'content'=> $article->content,
                    'publish_date'=> $article->publish_date,
                ];
            }),
            'message'=>'List articles',
            'status'=>Response::HTTP_OK,
        ],Response::HTTP_OK);
      }
    }
}
