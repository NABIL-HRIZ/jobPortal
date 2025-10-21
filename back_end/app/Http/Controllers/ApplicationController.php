<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Job;

class ApplicationController extends Controller
{


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



    public function getAllApplications()
    {
        $jobs = Job::with('applications.poster')->get();

        return response()->json([
            'status' => 'success',
            'jobs' => $jobs
        ], 200);
    }
}

