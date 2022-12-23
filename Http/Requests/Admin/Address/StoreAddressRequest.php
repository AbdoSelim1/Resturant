<?php

namespace App\Http\Requests\Admin\Address;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressRequest extends FormRequest
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
            'street' => ['required', 'string', 'max:50'],
            'buliding' => ['required', 'string', 'max:50'],
            'floor' => ['required', 'string', 'max:50'],
            'flat' => ['required', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:255'],
            'status'=>['required','integer','in:0,1'],
            'latitude' => ['nullable',"numeric", 'max:20'],
            'longitude' => ['nullable', 'numeric','max:20'],
            'region_id' => ['required', 'integer', "exists:regions,id"],
            'customer_id' => ['required', 'integer', "exists:customers,id"],

        ];
    }
}
