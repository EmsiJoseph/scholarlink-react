<?php

namespace App\Http\Controllers;

use App\Mail\UserUpdate;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollection;
use App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(new UserCollection(User::all()),Response::HTTP_OK);
    }

    /**
     * Show the total of users.
     */

    public function totalUsers()
    {
        $totalUsers = User::count();

        return response()->json(['total_users' => $totalUsers], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $user = User::create($request->only([
                'first_name', 'middle_name', 'last_name', 'user_mobile_num',
                'email_address', 'password', 'role_id', 'user_status',
            ]));
            
            return new UserResource($user);
        } 
        catch (QueryException $e) {
            if ($e->errorInfo[1] === 1062) { // MySQL error code for duplicate entry
                return response()->json(['message' => 'Email already exists'], 409); // 409 Conflict
            }
            
            // Handle other exceptions or errors
            return response()->json(['message' => 'Error occurred'], 500); // 500 Internal Server Error
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user, $id)
    {
        $user = User::find($id);
        $user->update($request->all());

        if($user) {
            try{
                Mail::mailer('smtp')->to($user->email_address)->send(new UserUpdate($user));
                return response()->json([
                    'status' => true,
                    'message' => 'User has been updated',
                    'method' => 'POST',
                ], 200);
            }
            catch (\Exception $err){
                $user->delete();
                return response()->json([
                    'status' => false,
                    'message' => 'Could not send update Notification',
                    'method' => 'POST',
                ], 500);
            }
        }
        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, $id)
    {
        $deleted = User::destroy($id);

        if ($deleted === 0) {
            return response()->json(['message' => 'User not found or already deleted'], 404);
        } elseif ($deleted === null) {
            return response()->json(['message' => 'Error deleting user'], 500);
        }
    
        return response()->json(['message' => 'User deleted successfully'], 200);

    }

    public function restore(User $user, $id)
    {
        $restored = User::withTrashed()->where('id', $id)->restore();

        if ($restored === 0) {
            return response()->json(['message' => 'User not found or already restored'], 404);
        } elseif ($restored === null) {
            return response()->json(['message' => 'Error restoring user'], 500);
        }
    
        return response()->json(['message' => 'User restored successfully'], 200);
    }

    public function getScholars()
    {
        // Fetch users with role_id of 3 or scholars
        $scholars = User::where('role_id', 3)->orWhereHas('scholar')->get();
        
        return response()->json(new UserCollection($scholars), Response::HTTP_OK);
    }

    public function updateOtherScholarProfile(Request $request, $id)
    {
        try {
            // Find the scholar by ID
            $scholar = Scholar::findOrFail($id);
            
            // Update the scholar with the request data
            $scholar->update($request->only([
                'scholarship_categ_id', 'project_partner_id', 'scholar_status_id', 'school_id',
            ]));
    
            // Return the updated scholar as a resource
            return new ScholarResource($scholar);
        } catch (\Exception $e) {
            // Log the exception for further investigation
            \Log::error('Error in updateOtherScholarProfile: ' . $e->getMessage());
    
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'method' => 'PUT'
            ], Response::HTTP_NOT_FOUND);
        }
    }



    // Get users based on RoleId 
    public function getUserRoles(Request $request)
    {
        $users = User::where('role_id', 3)->get();
    
        return response()->json([
            'status' => true,
            'data' => $users,
            'message' => 'Users with role_id 3 retrieved successfully',
            'method' => 'POST',
        ], 200);
    }

    // Get Users profile based on the auth users
    public function profile()
    {
        try {
            $userId = Auth::id(); // Retrieve the authenticated user's ID
            $user = User::findOrFail($userId);
    
            return new UserResource($user);
        } catch (\Exception $e) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
    }
    public function updateProfile(Request $request, $id)
{
    try {
        $userId = Auth::id();

        // Check if the authenticated user matches the provided $id
        if ($userId != $id) {
            return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $user = User::findOrFail($userId);

        // Validate the request data, including unique email validation
        $request->validate([
            'email' => 'sometimes|required|email|unique:users,email,' . $userId,
            // Add other validation rules as needed
        ]);

        $user->update($request->all());

        return new UserResource($user);
    } catch (\Exception $e) {
        // Check if the exception is due to a unique constraint violation on the email field
        if ($e instanceof \Illuminate\Database\QueryException && $e->errorInfo[1] == 1062) {
            return response()->json(['message' => 'Email already taken'], Response::HTTP_CONFLICT);
        }

        return response()->json(['message' => 'Error updating profile'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

    // public function search(Request $request, $first_name)
    // {
    //     $user = User::where('first_name', 'like', '%' . $first_name . '%')->get();
    //     return UserResource::collection($user);
    // }
}
