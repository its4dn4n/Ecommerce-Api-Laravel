<?php

namespace App\Http\Requests\products;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return True;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name'=>['required','unique:products'],
            'image'=> ['required','image'],    //'mems:.jpg '
            'quantity'=> ['required'],
            'price'=> ['required'],
            'description'=>['required'],
            'category'=>['required','exists:categories,name'],
        ];
    }
}
