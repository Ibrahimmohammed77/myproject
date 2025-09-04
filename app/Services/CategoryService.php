<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

/**
 * Class CategoryService
 *
 * مسؤولة عن منطق الأعمال الخاص بالأصناف: فهرسة/فلترة/بحث/إنشاء/تحديث/حذف...إلخ.
 */
class CategoryService
{
    /**
     * إرجاع قائمة مصنفة مع بحث/فلترة/ترتيب وتصفح.
     *
     * @param  array{
     *   search?: string|null,
     *   is_active?: string|bool|null,
     *   sort?: string|null,
     *   per_page?: int|null,
     *   with_trashed?: bool|null,
     *   only_trashed?: bool|null
     * }  $filters
     * @return LengthAwarePaginator
     */
    public function list(array $filters = []): LengthAwarePaginator
    {
        $query = Category::query();

        // المحذوفات
        if (!empty($filters['with_trashed'])) {
            $query->withTrashed();
        } elseif (!empty($filters['only_trashed'])) {
            $query->onlyTrashed();
        }

        // البحث
        if (!empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // حالة التفعيل
        if (array_key_exists('is_active', $filters) && $filters['is_active'] !== null) {
            $query->where(
                'is_active',
                filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN)
            );
        }

        // الترتيب
        $allowedSorts = ['name', 'slug', 'created_at', 'updated_at', 'is_active'];
        $sort = $filters['sort'] ?? '-created_at';
        $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
        $column = ltrim($sort, '-');
        if (!in_array($column, $allowedSorts, true)) {
            $column = 'created_at';
            $direction = 'desc';
        }
        $query->orderBy($column, $direction);

        // التصفح
        $perPage = (int)($filters['per_page'] ?? 15);
        $perPage = $perPage > 0 && $perPage <= 100 ? $perPage : 15;

        return $query->paginate($perPage);
    }

    /**
     * إنشاء صنف جديد.
     *
     * @param  array{name:string, slug?:string|null, description?:string|null, is_active?:bool|null} $data
     * @return Category
     */
    public function create(array $data): Category
    {
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return Category::create($data);
    }

    /**
     * تحديث صنف موجود.
     *
     * @param  Category $category
     * @param  array{name?:string, slug?:string|null, description?:string|null, is_active?:bool|null} $data
     * @return Category
     */
    public function update(Category $category, array $data): Category
    {
        if (array_key_exists('slug', $data) && blank($data['slug'])) {
            $data['slug'] = Str::slug($data['name'] ?? $category->name);
        }

        $category->update($data);
        return $category->refresh();
    }

    /**
     * حذف ناعم (Soft Delete).
     *
     * @param  Category $category
     * @return void
     */
    public function delete(Category $category): void
    {
        $category->delete();
    }

    /**
     * استرجاع صنف محذوف.
     *
     * @param  int $id
     * @return Category
     */
    public function restore(int $id): Category
    {
        $category = Category::withTrashed()->findOrFail($id);
        $category->restore();
        return $category->refresh();
    }

    /**
     * حذف نهائي.
     *
     * @param  int $id
     * @return void
     */
    public function forceDelete(int $id): void
    {
        $category = Category::withTrashed()->findOrFail($id);
        $category->forceDelete();
    }

    /**
     * تبديل حالة التفعيل.
     *
     * @param  Category $category
     * @return Category
     */
    public function toggle(Category $category): Category
    {
        $category->is_active = !$category->is_active;
        $category->save();

        return $category->refresh();
    }
}
