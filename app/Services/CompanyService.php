<?php


namespace App\Services;


use App\Models\Company;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CompanyService
{
    protected $repository;

    public function __construct(Company $company)
    {
        $this->repository = $company;
    }

    public function getCompanies(string $filter = ''): LengthAwarePaginator
    {
        return $this->repository->getCompanies($filter);
    }

    public function createNewCompany(array $data, UploadedFile $image)
    {
        $path = $this->uploadImage($image);
        $data['image'] = $path;

        return $this->repository->create($data);
    }

    public function getCompanyByUuid(string $uuid)
    {
        return $this->repository->where('uuid', $uuid)->firstOrFail();
    }

    public function updateCompany(array $data, string $uuid = '', UploadedFile $image = null)
    {
        $company = $this->getCompanyByUuid($uuid);

        if ($image) {
            if (Storage::exists($company->image))
                Storage::delete($company->image);

            $path = $this->uploadImage($image);
            $data['image'] = $path;
        }

        return $company->update($data);
    }

    public function deleteCompany(string $uuid = '')
    {
        $company = $this->getCompanyByUuid($uuid);

        if (Storage::exists($company->image))
            Storage::delete($company->image);

        return $company->delete();
    }

    private function uploadImage(UploadedFile $image)
    {
        return $image->store('companies');
    }
}
