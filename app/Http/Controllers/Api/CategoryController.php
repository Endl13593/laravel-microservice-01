<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateCategory;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    protected $repository;

    public function __construct(Category $model)
    {
        $this->repository = $model;
    }

    public function index(): AnonymousResourceCollection
    {
        return CategoryResource::collection($this->repository->all());
    }

    public function store(StoreUpdateCategory $request): CategoryResource
    {
        $category = $this->repository->create($request->validated());

        return new CategoryResource($category);
    }

    public function show($url): CategoryResource
    {
        $category = $this->repository->where('url', $url)->firstOrFail();

        return new CategoryResource($category);
    }

    public function update(StoreUpdateCategory $request, $url): array
    {
        $category = $this->repository->where('url', $url)->firstOrFail();

        $category->update($request->validated());

        return ['message' => 'success'];
    }

    public function destroy($url): JsonResponse
    {
        $category = $this->repository->where('url', $url)->firstOrFail();

        $category->delete();

        return response()->json([], 204);
    }
}
