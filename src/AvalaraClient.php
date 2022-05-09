<?php

namespace Jwohlfert23\LaravelAvalara;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Jwohlfert23\LaravelAvalara\Models\Certificate;
use Jwohlfert23\LaravelAvalara\Models\CreateTransaction;
use Jwohlfert23\LaravelAvalara\Models\Customer;
use Jwohlfert23\LaravelAvalara\Models\ExemptionReason;
use Jwohlfert23\LaravelAvalara\Models\Transaction;

class AvalaraClient
{
    protected int $companyId;
    protected string $companyCode;

    public function __construct(int $companyId = null, string $companyCode = null)
    {
        $this->companyId = $companyId ?? config('avalara.company_id') ?? throw new \Exception("Could not determine companyId");
        $this->companyCode = $companyCode ?? config('avalara.company_code') ?? throw new \Exception("Could not determine companyCode");
    }

    protected function getRequest(): PendingRequest
    {
        return Http::baseUrl(config('avalara.url'))
            ->withBasicAuth(config('avalara.username'), config('avalara.password'))
            ->asJson()
            // If we receive a gateway timeout from Avalara (not uncommon),
            // and the request was not mutating something,
            // retry it up to 3 times.
            ->retry(3, 100, function ($e, Request $request) {
                $is504 = $e instanceof RequestException && $e->response->status() === 504;
                $isConnection = $e instanceof ConnectionException;
                if ($is504 || $isConnection) {
                    if ($request->method() === 'GET') {
                        return true;
                    }
                    $isQuote = in_array(Arr::get($request->data(), 'type'), [
                        AvalaraDocType::SALES_ORDER->value,
                        AvalaraDocType::PURCHASE_ORDER->value,
                        AvalaraDocType::RETURN_ORDER->value,
                        AvalaraDocType::INVENTORY_TRANSFER_ORDER->value,
                    ]);
                    if ($isQuote) {
                        return true;
                    }
                }

                return false;
            });
    }

    protected function get(string $url, array $query = []): array
    {
        $res = $this->getRequest()->get($url, $query);

        if (! $res->successful()) {
            throw AvalaraException::fromResponse($res);
        }

        return $res->json();
    }

    protected function post(string $url, array $data): array
    {
        $res = $this->getRequest()->post($url, $data);

        if (! $res->successful()) {
            throw AvalaraException::fromResponse($res);
        }

        return $res->json();
    }

    protected function put(string $url, array $data): array
    {
        $res = $this->getRequest()->put($url, $data);

        if (! $res->successful()) {
            throw AvalaraException::fromResponse($res);
        }

        return $res->json();
    }

    protected function delete(string $url, array $data = []): array
    {
        $res = $this->getRequest()->delete($url, $data);

        if (! $res->successful()) {
            throw AvalaraException::fromResponse($res);
        }

        return $res->json();
    }

    /**
     * @param  string  $transCode
     * @param  AvalaraDocType  $documentType
     * @return Transaction|null
     * @throws AvalaraException
     */
    public function getTransactionByCode(
        string $transCode,
        AvalaraDocType $documentType = AvalaraDocType::SALES_INVOICE
    ): ?Transaction {
        $res = $this->get("companies/$this->companyCode/transactions/$transCode", [
            'documentType' => $documentType->value,
        ]);

        return new Transaction($res);
    }

    /**
     * @param  CreateTransaction  $model
     * @return Transaction|null
     * @throws AvalaraException
     */
    public function createTransaction(CreateTransaction $model): ?Transaction
    {
        if (! isset($model->companyCode)) {
            $model->companyCode = $this->companyCode;
        }
        $res = $this->post('transactions/create', $model->toArray());

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
     * @param  string  $transCode
     * @param  AvalaraDocType  $documentType
     * @param  string  $code
     * @return Transaction
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

    /**
     * @return ExemptionReason[]
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
     * @param  string  $customerCode
     * @return Customer
     * @throws AvalaraException
     */
    public function getCustomerByCode(string $customerCode): Customer
    {
        $res = $this->get("companies/$this->companyId/customers/$customerCode");

        return new Customer($res);
    }

    /**
     * @param  Customer  $model
     * @return Customer
     * @throws AvalaraException
     */
    public function createCustomer(Customer $model): Customer
    {
        $res = $this->post("companies/$this->companyId/customers", [$model->toArray()]);

        return new Customer($res[0]);
    }

    /**
     * @param  Customer  $model
     * @return Customer
     * @throws AvalaraException
     */
    public function updateCustomer(Customer $model): Customer
    {
        $res = $this->put("companies/$this->companyId/customers/$model->customerCode", $model->toArray());

        return new Customer($res);
    }

    /**
     * @param  string  $customerCode
     * @return void
     * @throws AvalaraException
     */
    public function deleteCustomer(string $customerCode): void
    {
        $this->delete("companies/$this->companyId/customers/$customerCode");
    }

    /**
     * @param  string  $customerCode
     * @return Certificate[]
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
     * @param  string  $customerCode
     * @param  array  $ids
     * @return Certificate[]
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
     * @param  string  $customerCode
     * @param  array  $ids
     * @return array
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
     * @param  Certificate  $model
     * @return Certificate
     * @throws AvalaraException
     */
    public function createCertificate(Certificate $model): Certificate
    {
        $res = $this->post("companies/$this->companyId/certificates", [$model->toArray()]);

        return new Certificate($res[0]);
    }

    /**
     * @param  Certificate  $model
     * @return Certificate
     * @throws AvalaraException
     */
    public function updateCertificate(Certificate $model)
    {
        $res = $this->put("companies/$this->companyId/certificates/$model->id", $model->toArray());

        return new Certificate($res);
    }

    /**
     * @param  int  $id
     * @return void
     * @throws AvalaraException
     */
    public function deleteCertificate(int $id): void
    {
        $this->delete("companies/$this->companyId/certificates/$id");
    }
}
