<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // عدّلها حسب صلاحياتك
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:150'],
            'slug'        => ['nullable', 'string', 'max:160', 'unique:categories,slug'],
            'description' => ['nullable', 'string'],
            'is_active'   => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'حقل الاسم مطلوب.',
            'name.max'      => 'أقصى طول للاسم 150 حرفًا.',
            'slug.max'      => 'أقصى طول للـ slug هو 160 حرفًا.',
            'slug.unique'   => 'قيمة الـ slug مستخدمة مسبقًا.',
        ];
    }
}
