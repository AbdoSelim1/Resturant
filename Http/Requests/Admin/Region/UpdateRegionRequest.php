<?php

namespace App\Http\Requests\Admin\Region;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRegionRequest extends FormRequest
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
            'id'=>['required','integer','exists:regions,id'],
            'name'=>['required','string',"unique:regions,name,{$this->id},id"],
            'status'=>['required','integer','in:0,1'],
            'city_id'=>['required','integer','exists:cities,id']
        ];
    }
}
