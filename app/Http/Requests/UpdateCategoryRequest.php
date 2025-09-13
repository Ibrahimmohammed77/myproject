<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('category')?->id ?? $this->route('id');

        return [
            'name'        => ['sometimes', 'required', 'string', 'max:150'],
            'slug'        => [
                'nullable', 'string', 'max:160',
                Rule::unique('categories', 'slug')->ignore($id),
            ],
            'description' => ['nullable', 'string'],
            'is_active'   => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'حقل الاسم مطلوب عند التعديل.',
            'name.max'      => 'أقصى طول للاسم 150 حرفًا.',
            'slug.max'      => 'أقصى طول للـ slug هو 160 حرفًا.',
            'slug.unique'   => 'قيمة الـ slug مستخدمة مسبقًا.',
        ];
    }
}
