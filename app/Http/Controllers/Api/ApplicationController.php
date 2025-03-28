<?php

namespace App\Http\Controllers\Api;

use App\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ApplicationController extends Controller
{
    public function index(Request $request, Job $job)
    {
        // Only the employer who posted this job can view its applications
        if ($request->user()->role !== 'employer' || $job->employer_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Load applications with jobseeker info
        $applications = $job->applications()->with('jobseeker')->latest()->get();

        return response()->json([
            'job' => $job->only('id', 'title'),
            'applications' => $applications,
        ]);
    }

    public function store(Request $request, Job $job)
    {
        // Only jobseekers can apply
        if ($request->user()->role !== 'jobseeker') {
            return response()->json([
                'message' => 'Only jobseekers can apply to jobs.'
            ], 403);
        }

        // Check if already applied
        $alreadyApplied = Application::where('job_id', $job->id)
            ->where('jobseeker_id', $request->user()->id)
            ->exists();

        if ($alreadyApplied) {
            return response()->json([
                'message' => 'You have already applied to this job.'
            ], 409);
        }

        $application = Application::create([
            'job_id' => $job->id,
            'jobseeker_id' => $request->user()->id,
            'status' => ApplicationStatus::Pending,
        ]);

        return [
            'message' => 'Application submitted successfully.',
            'application' => $application,
        ];
    }

    public function update(Request $request, Application $application)
    {
        $user = $request->user();

        if ($user->role !== 'employer' || $application->job->employer_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(['accepted', 'rejected'])],
        ]);

        $application->update([
            'status' => ApplicationStatus::from($validated['status']), // convert string to Enum
        ]);

        return response()->json([
            'message' => 'Application status updated.',
            'application' => $application,
        ]);
    }



}
