<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

/**
 * @class CategoryController
 * @package App\Http\Controllers\Api
 *
 * @description
 *  وحدة تحكم لإدارة الأصناف (Categories).
 *  توفر عمليات CRUD كاملة مع دعم البحث، الفلترة، الاسترجاع، والحذف النهائي.
 *
 * @author إبراهيم
 */
class CategoryController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of categories with support for filters and pagination.
     *
     * GET /api/categories
     *
     * Query Params:
     * - search: string (بحث بالاسم/الوصف/slug)
     * - is_active: bool (فلترة حسب حالة التفعيل)
     * - sort: string (مثل name أو -name للترتيب النازل)
     * - per_page: int (عدد النتائج في الصفحة، max 100)
     * - with_trashed: bool (جلب الكل مع المحذوف)
     * - only_trashed: bool (جلب المحذوف فقط)
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $query = Category::query();

        // مع المحذوفات
        if ($request->boolean('with_trashed')) {
            $query->withTrashed();
        } elseif ($request->boolean('only_trashed')) {
            $query->onlyTrashed();
        }

        // البحث
        if ($search = $request->string('search')->trim()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // حالة التفعيل
        if (! is_null($request->query('is_active'))) {
            $query->where('is_active', filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        // الترتيب
        $allowedSorts = ['name', 'slug', 'created_at', 'updated_at', 'is_active'];
        $sort = $request->query('sort', '-created_at');
        $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
        $column = ltrim($sort, '-');
        if (! in_array($column, $allowedSorts, true)) {
            $column = 'created_at';
            $direction = 'desc';
        }
        $query->orderBy($column, $direction);

        // التصفح
        $perPage = (int) ($request->query('per_page', 15));
        $perPage = $perPage > 0 && $perPage <= 100 ? $perPage : 15;

        return CategoryResource::collection($query->paginate($perPage));
    }

    /**
     * Store a newly created category in storage.
     *
     * POST /api/categories
     *
     * @param StoreCategoryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();

        if (blank($data['slug'] ?? null)) {
            $data['slug'] = Str::slug($data['name']);
        }

        $category = Category::create($data);

        return $this->success("تم إنشاء الصنف بنجاح", new CategoryResource($category));
    }

    /**
     * Display the specified category.
     *
     * GET /api/categories/{category}
     *
     * @param Category $category
     * @return CategoryResource
     */
    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    /**
     * Update the specified category in storage.
     *
     * PUT/PATCH /api/categories/{category}
     *
     * @param UpdateCategoryRequest $request
     * @param Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $data = $request->validated();

        if (array_key_exists('slug', $data) && blank($data['slug'])) {
            $data['slug'] = Str::slug($data['name'] ?? $category->name);
        }

        $category->update($data);

        return $this->success("تم تحديث الصنف بنجاح", new CategoryResource($category));
    }

    /**
     * Soft delete the specified category.
     *
     * DELETE /api/categories/{category}
     *
     * @param Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return $this->success("تم حذف الصنف (Soft Delete) بنجاح", null);
    }

    /**
     * Restore a soft deleted category.
     *
     * POST /api/categories/{id}/restore
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $category = Category::withTrashed()->findOrFail($id);
        $category->restore();

        return $this->success("تم استرجاع الصنف بنجاح", new CategoryResource($category));
    }

    /**
     * Permanently delete a category from storage.
     *
     * DELETE /api/categories/{id}/force
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceDelete($id)
    {
        $category = Category::withTrashed()->findOrFail($id);
        $category->forceDelete();

        return $this->success("تم الحذف النهائي للصنف", null);
    }

    /**
     * Toggle the active state of the specified category.
     *
     * PATCH /api/categories/{category}/toggle
     *
     * @param Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(Category $category)
    {
        $category->is_active = ! $category->is_active;
        $category->save();

        return $this->success("تم تحديث حالة التفعيل", new CategoryResource($category));
    }
}
