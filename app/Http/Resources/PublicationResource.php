<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class PublicationResource extends JsonResource
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
            'description' => $this->description,
            'activate' => $this->activate == 0 ? false : true,
            'image_url' => $this->image ? URL::to($this->image) : null,
            'category_id' => $this->category_id,
            'user' => new UserResource($this->user)
        ];
    }
}
