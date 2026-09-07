<?php

use Illuminate\Support\Facades\Http;
use Jwohlfert23\LaravelAvalara\AvalaraClient;
use Jwohlfert23\LaravelAvalara\AvalaraDocType;
use Jwohlfert23\LaravelAvalara\AvalaraException;
use Jwohlfert23\LaravelAvalara\Models\Address;
use Jwohlfert23\LaravelAvalara\Models\CreateLineItem;
use Jwohlfert23\LaravelAvalara\Models\CreateTransaction;
use Jwohlfert23\LaravelAvalara\Models\Transaction;

beforeEach(function () {
    config()->set([
        'avalara.username' => 'testing@gmail.com',
        'avalara.password' => '1234',
        'avalara.company_id' => 1,
        'avalara.company_code' => 'testing',
    ]);
});

function getAddress()
{
    return new Address(
        name: 'John Smith',
        line1: '123 Main Street',
        city: 'Charlotte',
        postalCode: '12345',
        country: 'US',
    );
}

it('can create transaction', function () {
    Http::fake([
        '*' => Http::response([
            'id' => 123,
            'totalTax' => 10.00,
            'lines' => [
                [
                    'lineNumber' => 0,
                    'lineAmount' => 50.00,
                    'quantity' => 2,
                    'taxCode' => 'P0000000',
                    'tax' => 10.00,
                ],
            ],
        ]),
    ]);

    $transaction = new CreateTransaction;

    $transaction->date = now();
    $transaction->type = AvalaraDocType::SALES_ORDER;
    $transaction->customerCode = 'jack@gmail.com';

    $transaction->addresses[Address::TYPE_SINGLE_LOCATION] = getAddress();

    $transaction->lines[] = new CreateLineItem(
        number: 0,
        amount: 50.00,
        quantity: 2,
        taxCode: 'P0000000'
    );

    $res = $transaction->create();

    expect($res)->toBeInstanceOf(Transaction::class);
    expect($res->totalTax)->toEqual('10.00');
    expect($res->lines[0]->tax)->toEqual(10.00);
});

it('retries quotes', function () {
    $transaction = new CreateTransaction;

    Http::fakeSequence()
        ->push('', 504)
        ->push(['totalTax' => 20.01]);

    $transaction->date = now();
    $transaction->type = AvalaraDocType::SALES_ORDER;
    $res = $transaction->create();

    expect($res->totalTax)->toEqual(20.01);
});

it('throws AvalaraException on failure', function () {
    $transaction = new CreateTransaction;

    Http::fake([
        '*' => Http::response('', 504),
    ]);

    $transaction->date = now();
    $transaction->type = AvalaraDocType::SALES_ORDER;
    $transaction->create();
})->throws(AvalaraException::class);

it('pages through listed transactions', function () {
    $page = fn (array $ids) => [
        '@recordsetCount' => 3,
        'value' => array_map(fn (int $id) => ['id' => $id, 'code' => "INV-$id", 'status' => 'Committed', 'locked' => false, 'totalTax' => 1.5], $ids),
    ];
    Http::fakeSequence()->push($page([1, 2]))->push($page([3]));

    $transactions = app(AvalaraClient::class)
        ->listTransactions("status eq 'Committed'", orderBy: 'date desc', pageSize: 2)
        ->all();

    expect($transactions)->toHaveCount(3)
        ->and($transactions[2])->toBeInstanceOf(Transaction::class)
        ->and($transactions[2]->code)->toBe('INV-3')
        ->and($transactions[2]->locked)->toBeFalse();

    Http::assertSentCount(2);
    Http::assertSent(fn ($request) => str_contains($request->url(), 'companies/testing/transactions?')
        && str_contains($request->url(), '%24filter=status%20eq%20%27Committed%27')
        && str_contains($request->url(), '%24orderBy=date%20desc')
        && str_contains($request->url(), '%24top=2')
        && str_contains($request->url(), '%24skip=2'));
});
