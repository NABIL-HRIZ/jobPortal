<?php

namespace App\Http\Controllers;
use App\Models\User;

use Illuminate\Http\Request;

class AdminController extends Controller
{
   

   public function getAllUsers(){
$users = User::with('roles')->get();

$users = $users->map(function ($user) {
    return [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'roles' => $user->roles->pluck('name'), 
    ];
});

return response()->json([
    'status' => 'success',
    'users' => $users
], 200);
}

   public function getPersonalDataFromAdmin(Request $request){
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

   public function updateUserData(Request $request,$id){
         $user = User::findOrFail($id);
    
         $validatedData = $request->validate([
              'name' => 'sometimes|required|string|max:255',
              'email' => 'sometimes|required|string|email|unique:users,email,'.$user->id,
         ]);
    
         $user->update($validatedData);
    
         return response()->json([
              'status' => 'success',
              'message' => 'User data updated successfully',
              'user' => $user
         ], 200);
   }

   public function deleteUser($id){
       $user = User::findOrFail($id);
       $user->delete();

       return response()->json([
           'status' => 'success',
           'message' => 'User deleted successfully'
       ], 200);
   }

}
