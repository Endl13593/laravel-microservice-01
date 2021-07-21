<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUpdateCompany;
use App\Http\Resources\CompanyResource;
use App\Jobs\CompanyCreated;
use App\Models\Company;
use App\Services\CompanyService;
use App\Services\EvaluationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CompanyController extends Controller
{
    protected $evaluationService;
    protected $companyService;

    /**
     * CompanyController constructor.
     */
    public function __construct(EvaluationService $evaluationService, CompanyService $companyService)
    {
        $this->evaluationService = $evaluationService;
        $this->companyService = $companyService;
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $companies = $this->companyService->getCompanies($request->get('filter', ''));

        return CompanyResource::collection($companies);
    }

    public function store(StoreUpdateCompany $request): CompanyResource
    {
        $company = $this->companyService->createNewCompany($request->validated(), $request->file('image'));

        CompanyCreated::dispatch($company->email)->onQueue('queue_micro_email');

        return new CompanyResource($company);
    }

    public function show($uuid): CompanyResource
    {
        $company = $this->companyService->getCompanyByUuid($uuid);

        $evaluations = $this->evaluationService->getEvaluationsCompany($uuid);

        return (new CompanyResource($company))->additional(['evaluations' => json_decode($evaluations)]);
    }

    public function update(StoreUpdateCompany $request, $uuid): array
    {
        $this->companyService->updateCompany($request->validated(), $uuid, $request->file('image'));

        return ['update' => 'success'];
    }

    public function destroy($uuid): JsonResponse
    {
        $this->companyService->deleteCompany($uuid);

        return response()->json([], 204);
    }
}
