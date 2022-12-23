<?php

namespace App\Http\Requests\Admin\Menu;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "name" => ['required', 'string', 'between:5,50'],
            'category_id'=>['required','integer','exists:categories,id'],
            "quantity" => ['required', 'integer', 'digits_between:1,1000'],
            "default_price" => ['required', 'numeric', 'max:20000'],
            "default_size" => ['required', 'string', 'between:2,10'],
            "description" => ['nullable', 'string', 'max:3000'],
            "prices_sizes" => ['required','array'],
            'prices_sizes.*.price'=>['required','numeric','max:20000'],
            'prices_sizes.*.size'=>['required','string','between:2,10'],
            'images'=>['required','array'],
            'images.*.width'=>['required','integer','max:1000'],
            'images.*.height'=>['required','integer','max:1000'],
            'images.*.file_name'=>['required','file','mimes:png,jpg','max:2048']


        ];
    }
}
