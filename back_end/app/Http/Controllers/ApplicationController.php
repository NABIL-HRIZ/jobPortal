<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;

/**
 * @OA\Tag(
 *     name="Applications",
 *     description="Endpoints related to job applications"
 * )
 */
class ApplicationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/employer/applications",
     *     summary="Get applications for jobs posted by the authenticated employer",
     *     tags={"Applications"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of applications for employer's jobs"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function getEmployerApplications(Request $request)
    {
        $jobs = Job::where('posted_by', $request->user()->id)
                   ->with('applications.poster')
                   ->get();

        return response()->json([
            'status' => 'success',
            'jobs' => $jobs
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/admin/applications",
     *     summary="Get all applications (Admin only)",
     *     tags={"Applications"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of all job applications"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function getAllApplications()
    {
        $jobs = Job::with('applications.poster')->get();

        return response()->json([
            'status' => 'success',
            'jobs' => $jobs
        ], 200);
    }
}
