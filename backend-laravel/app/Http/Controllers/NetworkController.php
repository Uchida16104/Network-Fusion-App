<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Services\NetworkAnalyzer;
use Carbon\Carbon;

class NetworkController extends Controller
{
    public function health(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'service' => 'network-fusion-backend',
            'timestamp' => Carbon::now()->toIso8601String(),
        ]);
    }

    public function analyze(Request $request, NetworkAnalyzer $analyzer): JsonResponse
    {
        $payload = $request->validate([
            'nodes' => 'required|array|min:1',
            'nodes.*' => 'string',
            'edges' => 'required|array',
            'edges.*' => 'array|size:2',
            'edges.*.0' => 'string',
            'edges.*.1' => 'string',
            'focus' => 'nullable|string',
        ]);

        return response()->json($analyzer->analyze(
            $payload['nodes'],
            $payload['edges'],
            $payload['focus'] ?? null
        ));
    }

    public function repair(Request $request, NetworkAnalyzer $analyzer): JsonResponse
    {
        $payload = $request->validate([
            'nodes' => 'required|array|min:1',
            'nodes.*' => 'string',
            'edges' => 'required|array',
            'edges.*' => 'array|size:2',
            'edges.*.0' => 'string',
            'edges.*.1' => 'string',
            'focus' => 'nullable|string',
        ]);

        return response()->json($analyzer->repair(
            $payload['nodes'],
            $payload['edges'],
            $payload['focus'] ?? null
        ));
    }
}
