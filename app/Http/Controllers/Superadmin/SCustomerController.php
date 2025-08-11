<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Provider;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SCustomerController extends Controller
{
    public function index()
    {
        $providers = Provider::all();
        return view('superadmin.customers.index', compact('providers'));
    }
    public function data(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = User::where('role', 'customer')
            ->with(['customer.providers']); // ambil relasi providers juga

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
                'full_name' => $user->customer?->full_name ?? '',
                'register_at' => $user->register_at ? Carbon::parse($user->register_at)->format('Y-m-d H:i:s') : '',
                'providers' => $user->customer
                    ? $user->customer->providers->map(function ($p) {
                        return [
                            'id' => $p->id,
                            'name' => $p->name,
                            'type' => $p->provider_type
                        ];
                    })->toArray()
                    : []
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
        $user = User::where('role', 'customer')
            ->with('customer.providers')
            ->findOrFail($id);

        $providers = $user->customer ? $user->customer->providers : collect();

        return response()->json([
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'full_name' => $user->customer ? $user->customer->full_name : '',
            'register_at' => $user->register_at ? Carbon::parse($user->register_at)->format('Y-m-d H:i:s') : '',
            'master_data' => $providers->pluck('id')->toArray(),
            'providers' => $providers->map(function ($p) {
                return ['id' => $p->id, 'name' => $p->name];
            })->toArray(),
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username|max:255',
            'password' => 'required|string|min:6',
            'master_data' => 'nullable|array',
            'master_data.*' => 'exists:providers,id'
        ]);

        DB::transaction(function () use ($request, $validated) {
            $user = User::create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'customer',
                'status' => true,
                'register_at' => now(),
                'ip_register_at' => $request->ip(),
            ]);

            $customer = Customer::create([
                'user_id' => $user->id,
                'full_name' => $validated['full_name'],
                // 'is_active' => true, // set jika perlu
            ]);

            $customer->providers()->sync($request->input('master_data', []));
        });

        return response()->json(['message' => 'Customer created successfully']);
    }

    public function update(Request $request, $id)
    {
        $user = User::where('role', 'customer')->findOrFail($id);

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'master_data' => 'nullable|array',
            'master_data.*' => 'exists:providers,id'
        ]);

        DB::transaction(function () use ($request, $validated, $user) {
            $user->username = $validated['username'];
            $user->email = $validated['email'];
            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }
            $user->save();

            $customer = $user->customer;
            if ($customer) {
                $customer->full_name = $validated['full_name'];
                $customer->save();
            } else {
                $customer = Customer::create([
                    'user_id' => $user->id,
                    'full_name' => $validated['full_name'],
                ]);
            }

            $customer->providers()->sync($request->input('master_data', []));
        });

        return response()->json(['message' => 'Customer updated successfully']);
    }

    public function destroy($id)
    {
        $user = User::where('role', 'customer')->findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Customer deleted successfully']);
    }
}
