<?php

namespace App\Http\Controllers\API\v1;

use Carbon\Carbon;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

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

    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'title' => 'required',
        'content' => 'required',
        'publish_date' => 'required|date', // Assurez-vous que publish_date est une date valide
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    try {
        Article::create([
            'title' => $request->title,
            'content' => $request->content,
            'publish_date' => Carbon::create($request->publish_date)->toDateString(),
        ]);
        return response()->json([
            'status' => Response::HTTP_OK,
            'message' => 'Données enregistrées dans la base de données',
        ], Response::HTTP_OK);
    } catch (\Throwable $e) {
        Log::error('Erreur lors de l\'enregistrement des données : ' . $e->getMessage());

        return response()->json([
            'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'message' => 'Échec de l\'enregistrement des données dans la base de données',
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
}
