<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Channel;
use App\Models\Customer;
use Illuminate\Http\Request;

class SChannelController extends Controller
{
    public function index()
    {
        $customers = Customer::select('id', 'full_name')->get();
        $banks = Bank::all();
        return view('superadmin.channels.index', compact('customers', 'banks'));
    }

    public function data(Request $request)
    {
        $perPage = $request->per_page ?? 10;

        $customers = Customer::with('channels')
            ->when($request->search, function ($query) use ($request) {
                $query->where('full_name', 'like', '%' . $request->search . '%');
            })
            ->paginate($perPage);

        return response()->json($customers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'channel_code' => 'nullable|unique:channels,channel_code',
            'channel_type' => 'required|in:transfer,ewallet,qris,virtual_account,pulsa',
        ]);

        $channel = Channel::create($validated);

        return response()->json($channel, 201);
    }

    public function show($id)
    {
        $channel = Channel::with('customer')->findOrFail($id);
        return response()->json($channel);
    }

    public function update(Request $request, $id)
    {
        $channel = Channel::findOrFail($id);

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'channel_code' => 'nullable|unique:channels,channel_code,' . $channel->id,
            'channel_type' => 'required|in:transfer,ewallet,qris,virtual_account,pulsa',
        ]);

        $channel->update($validated);

        return response()->json($channel);
    }

    public function destroy($id)
    {
        $channel = Channel::findOrFail($id);
        $channel->delete();

        return response()->json(null, 204);
    }
}
