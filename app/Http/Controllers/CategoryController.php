<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    
    public function index(Request $request)
    {
        $query = Category::query();

        // عرض المحذوفين اختيارياً
        if ($request->boolean('with_trashed')) {
            $query->withTrashed();
        }

        // فلترة بالحالة
        if (!is_null($request->get('is_active'))) {
            $query->where('is_active', filter_var($request->get('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        // بحث بالاسم أو السلَغ
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('slug', 'LIKE', "%{$search}%");
            });
        }

        // فرز ديناميكي ?sort=created_at,-name
        if ($sort = $request->get('sort')) {
            $fields = explode(',', $sort);
            foreach ($fields as $field) {
                $direction = str_starts_with($field, '-') ? 'desc' : 'asc';
                $column = ltrim($field, '-');
                // تأكد من الأعمدة المسموحة للفرز
                if (in_array($column, ['id','name','slug','is_active','created_at','updated_at'])) {
                    $query->orderBy($column, $direction);
                }
            }
        } else {
            $query->latest('id');
        }

        $perPage = (int) ($request->get('per_page', 15));
        $categories = $query->paginate($perPage)->appends($request->query());

        return CategoryResource::collection($categories);
    }

    /**
     * POST /api/categories
     */
    public function store(CategoryRequest $request)
    {
        $data = $request->validated();
        // القيمة الافتراضية للحالة
        $data['is_active'] = $data['is_active'] ?? true;

        $category = Category::create($data);

        return (new CategoryResource($category))
            ->additional(['message' => 'Category created successfully.'])
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * GET /api/categories/{category}
     */
    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    /**
     * PUT/PATCH /api/categories/{category}
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return (new CategoryResource($category->refresh()))
            ->additional(['message' => 'Category updated successfully.']);
    }

    /**
     * DELETE /api/categories/{category}
     * حذف لطيف (Soft Delete)
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'message' => 'Category deleted (soft) successfully.',
        ], Response::HTTP_OK);
    }

    /**
     * POST /api/categories/{id}/restore
     * استرجاع من الحذف اللطيف
     */
    public function restore(int $id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->restore();

        return (new CategoryResource($category))
            ->additional(['message' => 'Category restored successfully.']);
    }

    /**
     * DELETE /api/categories/{id}/force
     * حذف نهائي
     */
    public function forceDelete(int $id)
    {
        $category = Category::onlyTrashed()->findOrFail($id);
        $category->forceDelete();

        return response()->json([
            'message' => 'Category permanently deleted.',
        ], Response::HTTP_OK);
    }

    /**
     * PATCH /api/categories/{category}/toggle
     * تبديل حالة التفعيل
     */
    public function toggleActive(Category $category)
    {
        $category->is_active = !$category->is_active;
        $category->save();

        return (new CategoryResource($category))
            ->additional(['message' => 'Category activation toggled.']);
    }
}
