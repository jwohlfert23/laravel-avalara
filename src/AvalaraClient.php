<?php

namespace Jwohlfert23\LaravelAvalara;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Jwohlfert23\LaravelAvalara\Models\CertExpressInvitation;
use Jwohlfert23\LaravelAvalara\Models\Certificate;
use Jwohlfert23\LaravelAvalara\Models\CreateCertExpressInvitation;
use Jwohlfert23\LaravelAvalara\Models\CreateTransaction;
use Jwohlfert23\LaravelAvalara\Models\Customer;
use Jwohlfert23\LaravelAvalara\Models\ExemptionReason;
use Jwohlfert23\LaravelAvalara\Models\Region;
use Jwohlfert23\LaravelAvalara\Models\Transaction;

class AvalaraClient
{
    protected int $companyId;

    protected string $companyCode;

    public function __construct(?int $companyId = null, ?string $companyCode = null)
    {
        $this->companyId = $companyId ?? config('avalara.company_id') ?? throw new \Exception('Could not determine companyId');
        $this->companyCode = $companyCode ?? config('avalara.company_code') ?? throw new \Exception('Could not determine companyCode');
    }

    protected function getRequest(): PendingRequest
    {
        return Http::baseUrl(config('avalara.url'))
            ->throw()
            ->withBasicAuth(config('avalara.username'), config('avalara.password'))
            ->asJson();
    }

    public static function shouldRetry(HttpClientException $e): bool
    {
        $is504 = $e instanceof RequestException && $e->response->status() === 504;
        $isConnection = $e instanceof ConnectionException;

        return $is504 || $isConnection;
    }

    protected function get(string $url, array $query = []): array
    {
        try {
            return $this->getRequest()->retry(3, 100, [$this, 'shouldRetry'])->get($url, $query)->json();
        } catch (RequestException $e) {
            throw AvalaraException::fromResponse($e->response);
        }
    }

    protected function post(string $url, array $data): array
    {
        try {
            return $this->getRequest()->post($url, $data)->json();
        } catch (RequestException $e) {
            throw AvalaraException::fromResponse($e->response);
        }
    }

    protected function put(string $url, array $data): array
    {
        try {
            return $this->getRequest()->put($url, $data)->json();
        } catch (RequestException $e) {
            throw AvalaraException::fromResponse($e->response);
        }
    }

    protected function delete(string $url, array $data = []): array
    {
        try {
            return $this->getRequest()->delete($url, $data)->json();
        } catch (RequestException $e) {
            throw AvalaraException::fromResponse($e->response);
        }
    }

    /**
     * @throws AvalaraException
     */
    public function getTransactionByCode(
        string $transCode,
        AvalaraDocType $documentType = AvalaraDocType::SALES_INVOICE
    ): Transaction {
        $res = $this->get("companies/$this->companyCode/transactions/$transCode", [
            'documentType' => $documentType->value,
        ]);

        return new Transaction($res);
    }

    /**
     * @throws AvalaraException
     */
    public function createTransaction(CreateTransaction $model): Transaction
    {
        if (! isset($model->companyCode)) {
            $model->companyCode = $this->companyCode;
        }

        try {
            $res = $this->getRequest()
                ->when($model->type->isQuote(), function (PendingRequest $req) {
                    return $req->retry(3, 100, [$this, 'shouldRetry']);
                })
                ->post('transactions/create', $model->toArray())
                ->json();
        } catch (RequestException $e) {
            throw AvalaraException::fromResponse($e->response);
        }

        return new Transaction($res);
    }

    public function createOrAdjustTransaction(CreateTransaction $model, string $reason, string $description)
    {
        if (! isset($model->companyCode)) {
            $model->companyCode = $this->companyCode;
        }

        $res = $this->post('transactions/createoradjust', [
            'createTransactionModel' => $model->toArray(),
            'adjustmentReason' => $reason,
            'adjustmentDescription' => $description,
        ]);

        return new Transaction($res);
    }

    public function adjustTransaction(
        string $transCode,
        string $reason,
        string $description,
        CreateTransaction $model,
        AvalaraDocType $documentType = AvalaraDocType::SALES_INVOICE,
    ): Transaction {
        $res = $this->post(
            "companies/$this->companyCode/transactions/$transCode/adjust?documentType=$documentType->value",
            [
                'adjustmentReason' => $reason,
                'adjustmentDescription' => $description,
                'newTransaction' => $model->toArray(),
            ]
        );

        return new Transaction($res);
    }

    /**
     * @throws AvalaraException
     */
    public function voidTransaction(
        string $transCode,
        AvalaraDocType $documentType = AvalaraDocType::SALES_INVOICE,
        string $code = 'DocVoided'
    ): Transaction {
        $url = "companies/$this->companyCode/transactions/$transCode/void?documentType=$documentType->value";

        $res = $this->post($url, ['code' => $code]);

        return new Transaction($res);
    }

    public function commitTransaction(
        string $transCode,
    ): Transaction {
        $res = $this->post("companies/$this->companyCode/transactions/$transCode/commit", [
            'commit' => true,
        ]);

        return new Transaction($res);
    }

    /**
     * @return ExemptionReason[]
     *
     * @throws AvalaraException
     */
    public function listCertificateExemptReasons(): array
    {
        $res = $this->get('definitions/certificateexemptreasons');

        return collect($res['value'])->map(function ($row) {
            return new ExemptionReason($row);
        })->all();
    }

    /**
     * @return Region[]
     *
     * @throws AvalaraException
     */
    public function listRegions(): array
    {
        $res = $this->get('definitions/regions');

        return collect($res['value'])->map(function ($row) {
            return new Region($row);
        })->all();
    }

    /**
     * @throws AvalaraException
     */
    public function getCustomerByCode(string $customerCode): Customer
    {
        $res = $this->get("companies/$this->companyId/customers/$customerCode");

        return new Customer($res);
    }

    /**
     * @throws AvalaraException
     */
    public function createCustomer(Customer $model): Customer
    {
        $res = $this->post("companies/$this->companyId/customers", [$model->toArray()]);

        return new Customer($res[0]);
    }

    /**
     * @throws AvalaraException
     */
    public function updateCustomer(Customer $model): Customer
    {
        $res = $this->put("companies/$this->companyId/customers/$model->customerCode", $model->toArray());

        return new Customer($res);
    }

    /**
     * @throws AvalaraException
     */
    public function deleteCustomer(string $customerCode): void
    {
        $this->delete("companies/$this->companyId/customers/$customerCode");
    }

    /**
     * @return Certificate[]
     *
     * @throws AvalaraException
     */
    public function listCertificatesForCustomer(string $customerCode): array
    {
        $res = $this->get("companies/$this->companyId/customers/$customerCode/certificates");

        return collect($res['value'])->map(function ($row) {
            return new Certificate($row);
        })->all();
    }

    /**
     * @return Certificate[]
     *
     * @throws AvalaraException
     */
    public function linkCertificatesToCustomer(string $customerCode, array $ids): array
    {
        $res = $this->post("companies/$this->companyId/customers/$customerCode/certificates/link", [
            'certificates' => $ids,
        ]);

        return collect($res['value'])->map(function ($row) {
            return new Certificate($row);
        })->all();
    }

    /**
     * @throws AvalaraException
     */
    public function unlinkCertificatesFromCustomer(string $customerCode, array $ids): array
    {
        $res = $this->post("companies/$this->companyId/customers/$customerCode/certificates/unlink", [
            'certificates' => $ids,
        ]);

        return collect($res['value'])->map(function ($row) {
            return new Certificate($row);
        })->all();
    }

    /**
     * @throws AvalaraException
     */
    public function createCertificate(Certificate $model): Certificate
    {
        $res = $this->post("companies/$this->companyId/certificates", [$model->toArray()]);

        return new Certificate($res[0]);
    }

    /**
     * @return Certificate
     *
     * @throws AvalaraException
     */
    public function updateCertificate(Certificate $model)
    {
        $res = $this->put("companies/$this->companyId/certificates/$model->id", $model->toArray());

        return new Certificate($res);
    }

    /**
     * @throws AvalaraException
     */
    public function deleteCertificate(int $id): void
    {
        $this->delete("companies/$this->companyId/certificates/$id");
    }

    public function getCertExpressInvite(string $customerCode, int $id): CertExpressInvitation
    {
        $res = $this->get("companies/$this->companyId/customers/$customerCode/certexpressinvites/$id");

        return new CertExpressInvitation($res);
    }

    public function createCertExpressInvite(string $customerCode, CreateCertExpressInvitation $invitation): CertExpressInvitation
    {
        $res = $this->post("companies/$this->companyId/customers/$customerCode/certexpressinvites", $invitation->toArray());

        return new CertExpressInvitation($res[0]['invitation']);
    }
}
