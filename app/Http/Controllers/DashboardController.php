<?php

namespace App\Http\Controllers;

use App\Repositories\ReportingRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(protected ReportingRepository $reportingRepository)
    {
    }

    public function stats(Request $request): JsonResponse
    {
        $stats = $this->reportingRepository->getUserStats($request->user()->id);
        return response()->json($stats);
    }

    public function calendar(Request $request): JsonResponse
    {
        $calendar = $this->reportingRepository->getCalendar($request->user()->id);
        return response()->json($calendar);
    }

    public function storage(Request $request): JsonResponse
    {
        $storage = $this->reportingRepository->getStorageUsage($request->user()->id);
        return response()->json($storage);
    }
}
