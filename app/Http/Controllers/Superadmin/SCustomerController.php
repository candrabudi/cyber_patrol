<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SCustomerController extends Controller
{
    public function index()
    {
        return view('superadmin.customers.index');
    }

    public function data(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = User::where('role', 'customer')->with('customer');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q2) use ($search) {
                        $q2->where('full_name', 'like', "%{$search}%");
                    });
            });
        }

        $paginated = $query->paginate($perPage);

        $data = $paginated->map(function ($user) {
            return [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'full_name' => $user->customer ? $user->customer->full_name : '',
                'register_at' => $user->register_at ? Carbon::parse($user->register_at)->format('Y-m-d H:i:s') : '',
            ];
        });

        return response()->json([
            'data' => $data,
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'total' => $paginated->total(),
        ]);
    }


    public function show($id)
    {
        $user = User::where('role', 'customer')->with('customer')->findOrFail($id);

        return response()->json([
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'full_name' => $user->customer ? $user->customer->full_name : '',
            'register_at' => $user->register_at ? Carbon::parse($user->register_at)->format('Y-m-d H:i:s') : '',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username|max:255',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
            'status' => true,
            'register_at' => now(),
            'ip_register_at' => $request->ip(),
        ]);

        Customer::create([
            'user_id' => $user->id,
            'full_name' => $validated['full_name'],
        ]);

        return response()->json(['message' => 'Customer created successfully']);
    }

    // Update user & customer data
    public function update(Request $request, $id)
    {
        $user = User::where('role', 'customer')->findOrFail($id);

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
        ]);

        $user->username = $validated['username'];
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        // Update customer full_name
        $customer = $user->customer;
        if ($customer) {
            $customer->full_name = $validated['full_name'];
            $customer->save();
        } else {
            // Jika customer belum ada, buat baru
            Customer::create([
                'user_id' => $user->id,
                'full_name' => $validated['full_name'],
            ]);
        }

        return response()->json(['message' => 'Customer updated successfully']);
    }

    public function destroy($id)
    {
        $user = User::where('role', 'customer')->findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Customer deleted successfully']);
    }
}
