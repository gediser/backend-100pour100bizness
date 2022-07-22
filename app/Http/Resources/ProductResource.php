<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'activate' => $this->activate,
            'name' => $this->name,
            'image_url' => $this->image ? URL::to($this->image) : null,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'prix' => $this->prix,
            'user' => new UserResource($this->user)
        ];
    }
}
