<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
class JobController extends Controller
{
    

    public function store(Request $request){
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'company_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'employment_type' => 'required|in:full-time,part-time,contract,internship',
            'salary' => 'nullable|numeric',
        ]);

        $validatedData['posted_by'] = $request->user()->id;

        $job = Job::create($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Job posted successfully',
            'job' => $job
        ], 201);
    }

    public function index(){
        $jobs = Job::with('employer:id,name,email')->paginate(10);

        return response()->json([
            'status' => 'success',
            'jobs' => $jobs
        ], 200);

    }

    public function update(Request $request, $id){
        $job = Job::findOrFail($id);

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

    public function destroy($id){
        $job = Job::findOrFail($id);
        $job->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Job deleted successfully'
        ], 200);
    }

   
}
