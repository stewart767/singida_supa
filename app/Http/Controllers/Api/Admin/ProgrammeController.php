<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProgrammeRequest;
use App\Http\Resources\ProgrammeResource;
use App\Models\Programme;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgrammeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $programmes = Programme::when($request->search, function ($q, $s) {
            $q->where('name', 'like', "%{$s}%")->orWhere('code', 'like', "%{$s}%");
        })->orderBy('name')->get();

        return response()->json([
            'programmes' => ProgrammeResource::collection($programmes),
        ]);
    }

    public function store(StoreProgrammeRequest $request): JsonResponse
    {
        $this->authorize('manage', Programme::class);

        $programme = Programme::create($request->validated());

        return response()->json([
            'message' => 'Programme created successfully.',
            'programme' => new ProgrammeResource($programme),
        ], 201);
    }

    public function update(StoreProgrammeRequest $request, Programme $programme): JsonResponse
    {
        $this->authorize('manage', Programme::class);

        $programme->update($request->validated());

        return response()->json([
            'message' => 'Programme updated successfully.',
            'programme' => new ProgrammeResource($programme),
        ]);
    }

    public function destroy(Programme $programme): JsonResponse
    {
        $this->authorize('manage', Programme::class);

        $programme->delete();

        return response()->json([
            'message' => 'Programme deleted successfully.',
        ]);
    }
}
