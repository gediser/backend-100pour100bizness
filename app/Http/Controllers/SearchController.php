<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Publication;
use App\Models\Product;
use App\Http\Resources\PublicationResource;
use App\Http\Resources\ProductResource;


class SearchController extends Controller
{
    //
    public function all(Request $request){
        $search = $request->input("q");

        $publications = Publication::query()
                            ->where("description", "LIKE", "%{$search}%")
                            ->orderBy("created_at", "desc")
                            ->limit(3)
                            ->get();

        $products = Product::query()
                            ->where("description", "LIKE", "%{$search}%")
                            ->orWhere("name", "LIKE", "%{$search}%")
                            ->orderBy("created_at", "desc")
                            ->limit(3)
                            ->get();

        return response([
            "publications" => PublicationResource::collection($publications),
            "products" => ProductResource::collection($products)
        ]);
    } 
}
