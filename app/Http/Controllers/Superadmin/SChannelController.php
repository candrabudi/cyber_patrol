<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\Customer;
use Illuminate\Http\Request;

class SChannelController extends Controller
{
    public function index()
    {
        $customers = Customer::select('id', 'full_name')->get();
        return view('superadmin.channels.index', compact('customers'));
    }

    public function data(Request $request)
    {
        $channels = Channel::with('customer')
            ->when($request->search, function ($query) use ($request) {
                $query->whereHas('customer', function ($q) use ($request) {
                    $q->where('full_name', 'like', '%' . $request->search . '%');
                });
            })
            ->paginate($request->per_page ?? 10);

        return response()->json($channels);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'channel_code' => 'nullable|unique:channels,channel_code',
            'channel_type' => 'required|in:bank,ewallet,qris,virtual_account,pulsa',
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
            'channel_type' => 'required|in:bank,ewallet,qris,virtual_account,pulsa',
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
