<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\ProductResource;
use App\Http\Resources\PublicationResource;


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
            "products" => ProductResource::collection($products),
            "q" => $search
        ]);
    } 
    public function usersSearch(Request $request){
        $email = $request->query('email');
        if (strcmp($email, '') == 0){
            return response(User::with('roles')->limit(100)->orderBy('id', 'desc')->get());
        }else{
            return response(User::with('roles')->where('email', $email)->orderBy('id', 'desc')->limit(100)->get());
        }
        error_log("My user".$email);
    }

    public function usersMakeAdmin(Request $request){
        $id = $request->input('id');
        // 2 = id of role admin
        $role_user =  DB::table('role_user')
                        ->where([
                            "user_id" => $id,
                            "role_id" => 2
                        ])->first();
        if ($role_user === null){
            DB::table('role_user')->insert([
                "user_id" => $id,
                "role_id" => 2
            ]);
        }
        return response(["success" => true]);
    }

    public function usersMakeUser(Request $request){
        $id = $request->input('id');
        // 2 = id of role admin
        DB::table('role_user')
                ->where([
                    "user_id" => $id,
                    "role_id" => 2
                ])->delete();
        return response(["success" => true]);
    }

    public function category(int $id){

        $publications = Publication::query()
                            ->where("category_id", $id)
                            ->orderBy("created_at", "desc")
                            ->limit(3)
                            ->get();

        $products = Product::query()
                            ->where("category_id", $id)
                            ->orderBy("created_at", "desc")
                            ->limit(3)
                            ->get();

        return response([
            "publications" => PublicationResource::collection($publications),
            "products" => ProductResource::collection($products)
        ]);
    } 
}
