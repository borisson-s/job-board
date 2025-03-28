<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class JobController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    public function index(Request $request)
    {
        $query = Job::with('category', 'employer')->latest();

        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->category . '%');
            });
        }

        return $query->get();
    }


    public function store(Request $request)
    {
        if ($request->user()->role !== 'employer') {
            abort(403, 'Only employers can create jobs.');
        }

        $validatedFields = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        $job = $request->user()->jobs()->create($validatedFields);
        $job->load('category', 'employer');

        return response()->json([
            'message' => 'Job created successfully.',
            'job' => $job,
        ], 201);

    }

    public function show(Job $job)
    {
        return $job->load('category', 'employer');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Job $job)
    {
        $user = $request->user();

        if ($user->role !== 'employer' || $job->employer_id !== $user->id) {
            abort(403, 'Unauthorized to update this job.');
        }

        $validatedFields = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'category_id' => 'sometimes|exists:categories,id',
        ]);

        $job->update($validatedFields);

        return [
            'message' => 'Job updated successfully.',
            'job' => $job->load('category', 'employer'),
        ];

    }

    public function destroy(Request $request, Job $job)
    {
        $user = $request->user();

        if ($user->role !== 'employer' || $job->employer_id !== $user->id) {
            abort(403, 'Unauthorized to delete this job.');
        }

        $job->delete();

        return response()->json([
            'message' => 'Job deleted successfully.',
        ]);
    }
}
