<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    
     public function getPersonalDataFromUser(Request $request){
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

   public function storeApplication(Request $request){
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

   public function getOwnApplications(Request $request){
       $user = $request->user();

       $applications = $user->applications()->with('job')->get();

       return response()->json([
           'status' => 'success',
           'applications' => $applications
       ], 200);
   }
}
