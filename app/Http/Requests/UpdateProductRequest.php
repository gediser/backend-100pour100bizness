<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $product = $this->route('product');
        if ($this->user()->id != $product->user_id){
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
            'category_id' => 'exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'name' => 'string',
            'prix' => 'integer'
        ];
    }
}
