<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\StoreCategoryEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreCategoryRequest;
use App\Http\Requests\Api\V1\UpdateCategoryRequest;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return CategoryResource::collection(Category::all());
    }


    /**
     * Store a newly created resource in storage.
     * @throws Exception
     */
    /**
     * Store a newly created resource in storage.
     * @throws Exception
     */
    public function store(StoreCategoryRequest $request)
    {
        try {
            Log::info('Начало создания категории', ['data' => $request->validated()]);

            $data = $request->validated();
            $category = Category::create($data);

            Log::info('Подготовка к отправке события StoreCategoryEvent', ['category_id' => $category->id]);

            try {
                event(new StoreCategoryEvent($category));
                Log::info('Событие StoreCategoryEvent успешно отправлено', ['category_id' => $category->id]);
            } catch (Exception $e) {
                Log::error('Ошибка при отправке события StoreCategoryEvent', [
                    'category_id' => $category->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            // Возвращаем данные через ресурс без обертки
            return new CategoryResource($category);
        } catch (Exception $e) {
            Log::error('Ошибка при создании категории', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(Category $category): CategoryResource
    {
        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category): CategoryResource
    {
        $category->update($request->all());
        return new CategoryResource($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json([
            "message" => "Category deleted"
        ],204);
    }
}
