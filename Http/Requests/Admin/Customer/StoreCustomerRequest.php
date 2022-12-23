<?php

namespace App\Http\Requests\Admin\Customer;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
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
            'name'=>['required','string','between:5,30'],
            'email'=>['required','email','unique:customers'],
            'phone'=>['required','string','digits:11','unique:customers'],
            'status'=>['required','integer','digits:1','in:1,0,2'],
            'email_verified_at'=>['required','integer','digits:1','in:0,1'],
            'password'=>['required','regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/','confirmed'],
            'password_confirmation'=>['required'],
            'image'=>['nullable','file','max:1024']
        ];
    }
}
