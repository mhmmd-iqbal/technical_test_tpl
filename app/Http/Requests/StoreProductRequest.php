<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manage-products');
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'categories' => 'required|array',
            'categories.*' => 'string',
            'image' => 'nullable|image|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The product name is required.',
            'name.string' => 'The product name must be a string.',
            'name.max' => 'The product name must not be greater than 255 characters.',

            'description.required' => 'The product description is required.',
            'description.string' => 'The product description must be a string.',

            'price.required' => 'The product price is required.',
            'price.numeric' => 'The product price must be a number.',

            'categories.required' => 'At least one category is required.',
            'categories.array' => 'The categories must be an array.',
            'categories.*.string' => 'The categories must be strings.',

            'image.nullable' => 'The product image is optional.',
            'image.image' => 'The product image must be an image.',
            'image.max' => 'The product image must not be greater than 2048 kilobytes.',
        ];
    }
}
