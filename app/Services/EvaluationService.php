<?php


namespace App\Services;


use Endl13593\CommunicationMicroservices\Services\Traits\ConsumeExternalService;

class EvaluationService
{
    use ConsumeExternalService;

    protected $url;
    protected $token;

    public function __construct()
    {
        $this->url   = config('services.micro_02.url');
        $this->token = config('services.micro_02.token');
    }

    public function getEvaluationsCompany(string $company)
    {
        $response = $this->request('get', "/evaluations/{$company}");

        return $response->body();
    }
}
