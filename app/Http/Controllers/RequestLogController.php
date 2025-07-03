<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RequestLogController extends Controller
{
    /**
     * Display a paginated list of request logs.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $list = DB::table('request_logs')
            ->orderByDesc('id')
            ->paginate(5);

        return response()->json($list);
    }

    /**
     * Clear all request logs from the database.
     *
     * @return JsonResponse
     */
    public function clear(): JsonResponse
    {
        DB::table('request_logs')->truncate();

        return response()->json([
            'message' => 'All request logs have been successfully cleared.'
        ]);
    }
}
