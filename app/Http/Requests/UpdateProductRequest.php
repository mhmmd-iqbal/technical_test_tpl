<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'name.max' => 'The product name must be less than 255 characters.',
            'description.required' => 'The product description is required.',
            'description.string' => 'The product description must be a string.',
            'price.required' => 'The product price is required.',
            'price.numeric' => 'The product price must be a number.',
            'categories.required' => 'The product categories are required.',
            'categories.array' => 'The product categories must be an array.',
            'categories.*.string' => 'The product category must be a string.',
            'image.nullable' => 'The product image is not required.',
            'image.image' => 'The product image must be an image.',
            'image.max' => 'The product image must be less than 2048 kilobytes.',
        ];
    }
}
