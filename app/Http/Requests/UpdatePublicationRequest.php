<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePublicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $publication = $this->route('publication');
        if ($this->user()->id != $publication->user_id){
            return false;
        }
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
            'user_id' => 'exists:users,id',
            'activate' => 'boolean',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'category_id' => 'exists:categories,id',
        ];
    }
}
