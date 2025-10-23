<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="User",
 *     description="Endpoints for authenticated user operations"
 * )
 */
class UserController extends Controller
{
 /**
 * @OA\Get(
 *     path="/personal-data",
 *     summary="Get personal data of the authenticated user",
 *     tags={"User"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="User data returned successfully"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 */

    public function getPersonalDataFromUser(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'status' => 'success',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
            ]
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/applications",
     *     summary="Submit a job application",
     *     tags={"User"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"job_id", "phone_number", "cover_letter", "resume_path"},
     *             @OA\Property(property="job_id", type="integer", example=1),
     *             @OA\Property(property="phone_number", type="string", example="123-456-7890"),
     *             @OA\Property(property="cover_letter", type="string", example="I'm very interested in this role..."),
     *             @OA\Property(property="resume_path", type="string", example="/storage/resumes/user123.pdf")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Application submitted successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */

    
    public function storeApplication(Request $request)
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'job_id' => 'required|exists:jobs,id',
            'phone_number' => 'required|string|max:20',
            'cover_letter' => 'required|string',
            'resume_path' => 'required|string',
        ]);

        $application = $user->applications()->create($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Application submitted successfully',
            'application' => $application
        ], 201);
    }

/**
 * @OA\Get(
 *     path="/user/applications",
 *     summary="Get applications submitted by the authenticated user",
 *     tags={"User"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of user's job applications",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="status",
 *                 type="string",
 *                 example="success"
 *             ),
 *             @OA\Property(
 *                 property="applications",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer"),
 *                     @OA\Property(property="user_id", type="integer"),
 *                     @OA\Property(property="job_id", type="integer"),
 *                     @OA\Property(property="status", type="string"),
 *                     @OA\Property(property="created_at", type="string", format="date-time"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized"
 *     )
 * )
 */
public function getOwnApplications(Request $request)
{
    $user = $request->user();

    $applications = $user->applications()->with('job')->get();

    return response()->json([
        'status' => 'success',
        'applications' => $applications
    ], 200);
}


}
