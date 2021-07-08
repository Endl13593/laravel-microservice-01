<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateCompany;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CompanyController extends Controller
{
    protected $repository;
    /**
     * CompanyController constructor.
     */
    public function __construct(Company $model)
    {
        $this->repository = $model;
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $companies = $this->repository->getCompanies($request->get('filter', ''));

        return CompanyResource::collection($companies);
    }

    public function store(StoreUpdateCompany $request): CompanyResource
    {
        $company = $this->repository->create($request->validated());

        return new CompanyResource($company);
    }

    public function show($uuid): CompanyResource
    {
        $company = $this->repository->where('uuid', $uuid)->firstOrFail();

        return new CompanyResource($company);
    }

    public function update(StoreUpdateCompany $request, $uuid): array
    {
        $company = $this->repository->where('uuid', $uuid)->firstOrFail();

        $company->update($request->validated());

        return ['message' => 'success'];
    }

    public function destroy($uuid): JsonResponse
    {
        $company = $this->repository->where('uuid', $uuid)->firstOrFail();

        $company->delete();

        return response()->json([], 204);
    }
}
