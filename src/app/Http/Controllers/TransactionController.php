<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\MonthRequest;

class TransactionController extends Controller
{
    public function __construct(private readonly TransactionService $transactionService)
    {
    }

    public function store(StoreTransactionRequest $request)
    {
        $dataArray = [];
        $dataArray['occurred_on'] = $request->input('occurred_on');
        $dataArray['type'] = $request->input('type');
        $dataArray['category_id'] = $request->input('category_id');
        $dataArray['amount'] = $request->input('amount');
        $dataArray['memo'] = $request->input('memo');

        return $this->transactionService->createTransaction($dataArray);
    }

    public function index(MonthRequest $request)
    {
        $month = $request->query('month');
        return $this->transactionService->getMonthTransactions($month);
    }
}
