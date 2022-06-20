<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HomeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {   
        $publicationsJuste = [];
        $publicationsMeilleur = [];
        for($i=0; $i<count($this->resource['justepourvous']); $i++){
            $publicationsJuste[] = new PublicationResource($this->resource['justepourvous'][$i]);
        }
        for($i=0; $i<count($this->resource['meilleurclassement']); $i++){
            $publicationsMeilleur[] = new PublicationResource($this->resource['meilleurclassement'][$i]);
        }
        return [
            'justepourvous' => $publicationsJuste,
            'meilleurclassement' => $publicationsMeilleur
        ];
    }
}
