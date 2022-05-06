<?php

namespace Jwohlfert23\LaravelAvalara;

use Jwohlfert23\LaravelAvalara\Requests\TransactionModel;
use Jwohlfert23\LaravelAvalara\Responses\AvalaraTransaction;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class AvalaraClient
{
    protected static function getRequest(): PendingRequest
    {
        return Http::baseUrl(config('avalara.url'))
            ->withBasicAuth(config('avalara.username'), config('avalara.password'))
            ->asJson();
    }

    /**
     * @param  TransactionModel  $model
     * @return AvalaraTransaction|null
     * @throws AvalaraException
     * @throws UnknownProperties
     */
    public static function createTransaction(TransactionModel $model): ?AvalaraTransaction
    {
        $res = self::getRequest()->post('transactions/create', $model->toArray());

        if (! $res->successful()) {
            throw AvalaraException::fromResponse($res);
        }

        return new AvalaraTransaction($res->json());
    }

    /**
     * @param  string  $transCode
     * @param  AvalaraDocType  $documentType
     * @return Response
     */
    public static function voidTransaction(
        string $transCode,
        AvalaraDocType $documentType = AvalaraDocType::SALES_INVOICE
    ): Response {
        $companyCode = config('avalara.company_code');
        $url = "companies/$companyCode/transactions/$transCode/void?documentType=$documentType->value";

        return self::getRequest()->post($url, [
            'code' => 'DocVoided',
        ]);
    }
}
