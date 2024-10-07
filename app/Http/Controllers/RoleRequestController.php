<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\RoleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class RoleRequestController extends Controller
{
    public function index(Request $request)
    {
        $roleRequests = RoleRequest::where('status', 'pending')->get();

        return view('role_requests.index', compact('roleRequests'));
    }
    public function create()
    {
        return view('role_requests.create');
    }
    public function store(Request $request)
    {
        $user = $request->user();

        // Check if the user already has a pending role request
        $existingRequest = RoleRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return redirect()->back()->with('error', 'You already have a pending role request.');
        }

        // Create a new role request
        RoleRequest::create([
            'user_id' => $user->id,
            'requested_role' => $request->requested_role,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Role request created successfully.');
    }
    public function approve(Request $request, RoleRequest $roleRequest)
    {
        $user = $request->user();


        // Log the role request details
        Log::info('Approving role request', [
            'user_id' => $roleRequest->user_id,
            'requested_role' => $roleRequest->role,
            'approver_id' => $user->id,
        ]);

        if ($roleRequest->requested_role == 'author' && Gate::denies('approveAuthor', $user)) {
            Log::warning('Unauthorized action: approveAuthor', ['user_id' => $user->id]);
            abort(403, 'Unauthorized action.');
        }

        if ($roleRequest->requested_role == 'editor' && Gate::denies('approveEditor', $user)) {
            Log::warning('Unauthorized action: approveEditor', ['user_id' => $user->id]);
            abort(403, 'Unauthorized action.');
        }

        // Approve the role request
        $roleRequest->user->assignRole($roleRequest->requested_role);
        Log::info('Role assigned', ['user_id' => $roleRequest->user_id, 'role' => $roleRequest->requested_role]);

        $roleRequest->delete();
        Log::info('Role request deleted', ['role_request_id' => $roleRequest->id]);

        return redirect()->route('roleRequests.index')->with('success', 'Role request approved.');
    }
}