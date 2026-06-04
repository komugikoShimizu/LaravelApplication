<?php

namespace App\Http\Controllers;

use App\Services\SummaryService;
use App\Http\Requests\MonthRequest;

class SummaryController extends Controller
{
    public function __construct(private readonly SummaryService $summaryService)
    {
    }

    public function monthly(MonthRequest $request)
    {
        $month = $request->query('month');
        return $this->summaryService->getMonthlySummary($month);
    }
}
