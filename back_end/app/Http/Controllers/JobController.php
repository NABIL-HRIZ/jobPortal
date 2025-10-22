<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;
class JobController extends Controller
{
    

    public function store(Request $request){

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

  
  
    public function index(){

    
        $jobs=with('employer:id,name,email')->paginate(10);
        
        return response()->json([
            'status'=>'success',
            'jobs'=>$jobs,
        ],200);


    }

    public function show($id){
        $job = Job::with('employer:id,name,email')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'job' => $job
        ], 200);
    }

    
    
    public function update(Request $request, $id){
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

    public function destroy($id){
        $job = Job::findOrFail($id);
        $this->authorize('delete', $job);
        $job->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Job deleted successfully'
        ], 200);
    }


  
     public function search(Request $request){

        $query=Job::query();

        if($request->filled('title')){
            $query->where('title','Like','%'.$request->title.'%');
        }
        if($request->filled('location')){
            $query->where('location','Like','%'.$request->location.'%');
        }
        if($request->filled('company_name')){
            $query->where('company_name','Like','%'.$request->company_name.'%');
        }

        $jobs=$query->with('employer:id,name,email')->paginate(10);

        return response()->json([
            'status'=>'success',
            'jobs'=>$jobs
        ],200);
    }
}
