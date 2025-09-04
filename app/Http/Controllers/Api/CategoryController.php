<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @class CategoryController
 * @description CRUD للأصناف باستخدام طبقة Service
 */
class CategoryController extends Controller
{
    use ApiResponse;

    /**
     * @var CategoryService
     */
    protected CategoryService $service;

    /**
     * @param CategoryService $service
     */
    public function __construct(CategoryService $service)
    {
        $this->service = $service;
    }

    /**
     * GET /api/categories
     * عرض قائمة مع بحث/فلترة/ترتيب وتصفح.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $paginator = $this->service->list($request->only([
            'search', 'is_active', 'sort', 'per_page', 'with_trashed', 'only_trashed',
        ]));

        return CategoryResource::collection($paginator);
    }

    /**
     * POST /api/categories
     * إنشاء صنف جديد.
     *
     * @param  StoreCategoryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreCategoryRequest $request)
    {
        $category = $this->service->create($request->validated());

        return $this->success('تم إنشاء الصنف بنجاح', new CategoryResource($category))
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * GET /api/categories/{category}
     * عرض صنف محدد.
     *
     * @param  Category $category
     * @return CategoryResource
     */
    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    /**
     * PUT/PATCH /api/categories/{category}
     * تحديث صنف.
     *
     * @param  UpdateCategoryRequest $request
     * @param  Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category = $this->service->update($category, $request->validated());

        return $this->success('تم تحديث الصنف بنجاح', new CategoryResource($category));
    }

    /**
     * DELETE /api/categories/{category}
     * حذف ناعم.
     *
     * @param  Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Category $category)
    {
        $this->service->delete($category);

        return $this->success('تم حذف الصنف (Soft Delete) بنجاح', null);
    }

    /**
     * POST /api/categories/{id}/restore
     * استرجاع صنف محذوف.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore(int $id)
    {
        $category = $this->service->restore($id);

        return $this->success('تم استرجاع الصنف بنجاح', new CategoryResource($category));
    }

    /**
     * DELETE /api/categories/{id}/force
     * حذف نهائي.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceDelete(int $id)
    {
        $this->service->forceDelete($id);

        return $this->success('تم الحذف النهائي للصنف', null);
    }

    /**
     * PATCH /api/categories/{category}/toggle
     * تبديل حالة التفعيل.
     *
     * @param  Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(Category $category)
    {
        $category = $this->service->toggle($category);

        return $this->success('تم تحديث حالة التفعيل', new CategoryResource($category));
    }
}
