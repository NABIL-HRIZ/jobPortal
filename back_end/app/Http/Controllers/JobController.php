<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(
 *     name="Jobs",
 *     description="API Endpoints for Managing Jobs"
 * )
 */
class JobController extends Controller
{
    /**
     * @OA\Get(
     *     path="/jobs",
     *     summary="Get all jobs",
     *     tags={"Jobs"},
     *     @OA\Response(
     *         response=200,
     *         description="List of jobs"
     *     )
     * )
     */
    public function index()
    {
        $jobs = Job::with('employer:id,name,email')->paginate(10);

        return response()->json([
            'status' => 'success',
            'jobs' => $jobs,
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/jobs/{id}",
     *     summary="Get a single job",
     *     tags={"Jobs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Job details"
     *     )
     * )
     */
    public function show($id)
    {
        $job = Job::with('employer:id,name,email')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'job' => $job
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/jobs",
     *     summary="Create a new job",
     *     tags={"Jobs"},
     *     security={{ "sanctum": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","description","company_name","location","employment_type"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="company_name", type="string"),
     *             @OA\Property(property="location", type="string"),
     *             @OA\Property(property="employment_type", type="string", enum={"full-time","part-time","contract","internship"}),
     *             @OA\Property(property="salary", type="number", format="float", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Job posted successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function store(Request $request)
    {
        $this->authorize('create', Job::class);

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'company_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'employment_type' => 'required|in:full-time,part-time,contract,internship',
            'salary' => 'nullable|numeric',
        ]);

        $job = Job::create([
            ...$validatedData,
            'posted_by' => auth()->id(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Job posted successfully',
            'job' => $job
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/jobs/{id}",
     *     summary="Update a job",
     *     tags={"Jobs"},
     *     security={{ "sanctum": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="company_name", type="string"),
     *             @OA\Property(property="location", type="string"),
     *             @OA\Property(property="employment_type", type="string", enum={"full-time","part-time","contract","internship"}),
     *             @OA\Property(property="salary", type="number", format="float", nullable=true)
     *         )
     *     ),
      *     @OA\Response(
     *         response=200,
     *         description="Job updatedd successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * 
     * )
     */
    public function update(Request $request, $id)
    {
        $job = Job::findOrFail($id);
        $this->authorize('update', $job);

        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'company_name' => 'sometimes|required|string|max:255',
            'location' => 'sometimes|required|string|max:255',
            'employment_type' => 'sometimes|required|in:full-time,part-time,contract,internship',
            'salary' => 'nullable|numeric',
        ]);

        $job->update($validatedData);
        $job->refresh();

        return response()->json([
            'status' => 'success',
            'message' => 'Job updated successfully',
            'job' => $job
        ], 200);
    }

   /**
 * @OA\Delete(
 *     path="/jobs/{id}",
 *     summary="Delete a job",
 *     tags={"Jobs"},
 *     security={{ "bearerAuth": {} }},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the job to delete",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Job deleted successfully"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Unauthorized"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Job not found"
 *     )
 * )
 */

    public function destroy($id)
    {
        $job = Job::findOrFail($id);
        $this->authorize('delete', $job);
        $job->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Job deleted successfully'
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/jobs/search",
     *     summary="Search jobs",
     *     tags={"Jobs"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="location", type="string"),
     *             @OA\Property(property="company_name", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Filtered jobs"
     *     )
     * )
     */
    public function search(Request $request)
    {
        $query = Job::query();

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }
        if ($request->filled('company_name')) {
            $query->where('company_name', 'like', '%' . $request->company_name . '%');
        }

        $jobs = $query->with('employer:id,name,email')->paginate(10);

        return response()->json([
            'status' => 'success',
            'jobs' => $jobs
        ], 200);
    }
}
