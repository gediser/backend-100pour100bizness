<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\HomeResource;

class HomeController extends Controller
{
    //
    public function homeData(){
        $publications_juste = DB::table('justepourvous')->get();
        $publications_meilleur = DB::table('meilleurclassement')->get();

        $publications_juste_ids = [];
        for($i =0; $i < count($publications_juste); $i++){
            $publications_juste_ids[] = intval($publications_juste[$i]->publication_id);
        }

        $publications_meilleur_ids = [];
        for($i =0; $i < count($publications_meilleur); $i++){
            $publications_meilleur_ids[] = intval($publications_meilleur[$i]->publication_id);
        }

        return new HomeResource([
            'justepourvous' => Publication::whereIn('id', $publications_juste_ids)->get(),
            'meilleurclassement' => Publication::whereIn('id', $publications_meilleur_ids)->get()
        ]);
    }
}
