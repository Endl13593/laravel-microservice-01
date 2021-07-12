<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateCompany;
use App\Http\Resources\CompanyResource;
use App\Jobs\CompanyCreated;
use App\Models\Company;
use App\Services\EvaluationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CompanyController extends Controller
{
    protected $repository;
    protected $evaluationService;

    /**
     * CompanyController constructor.
     */
    public function __construct(Company $model, EvaluationService $evaluationService)
    {
        $this->repository = $model;
        $this->evaluationService = $evaluationService;
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $companies = $this->repository->getCompanies($request->get('filter', ''));

        return CompanyResource::collection($companies);
    }

    public function store(StoreUpdateCompany $request): CompanyResource
    {
        $company = $this->repository->create($request->validated());

        CompanyCreated::dispatch($company->email);

        return new CompanyResource($company);
    }

    public function show($uuid): CompanyResource
    {
        $company = $this->repository->where('uuid', $uuid)->firstOrFail();

        $evaluations = $this->evaluationService->getEvaluationsCompany($uuid);

        return (new CompanyResource($company))->additional(['evaluations' => json_decode($evaluations)]);
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
