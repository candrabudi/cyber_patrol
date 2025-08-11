<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Channel;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

    public function show($id)
    {
        $channel = Channel::with('customer')->findOrFail($id);
        return response()->json($channel);
    }

    public function store(Request $request)
    {
        $validated = $this->validateChannel($request);

        $bank = Bank::where('id', $request->bank_code)
            ->first();
        $customer = Customer::where('id', $request->customer_id)
            ->first();
        $bank = Bank::where('id', $request->bank_code)
            ->orWhere('name', $customer->full_name)
            ->first();
        $channel = new Channel();
        $channel->customer_id = $validated['customer_id'];
        $channel->channel_name = $bank ? $bank->name : $customer->full_name;
        if ($request->type == "virtual_account" || $request->type == "transfer") {
            $channel->channel_code = $validated['channel_code'] ?? $bank->code;
        }
        $channel->channel_type = $validated['channel_type'];
        $channel->save();

        return response()->json($channel, 201);
    }

    public function update(Request $request, $id)
    {
        $channel = Channel::findOrFail($id);

        $validated = $this->validateChannel($request, $channel->id);

        $customer = Customer::where('id', $request->customer_id)
            ->first();
        $bank = Bank::where('id', $request->bank_code)
            ->orWhere('name', $customer->full_name)
            ->first();
        $channel->customer_id = $validated['customer_id'];
        $channel->channel_name = $bank ? $bank->name : $customer->full_name;
        if ($request->type == "virtual_account" || $request->type == "transfer") {
            $channel->channel_code = $validated['channel_code'] ?? $bank->code;
        }
        $channel->channel_type = $validated['channel_type'];
        $channel->save();

        return response()->json($channel);
    }

    private function validateChannel(Request $request, $ignoreId = null)
    {
        return $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'channel_code' => [
                'nullable',
                Rule::unique('channels', 'channel_code')->ignore($ignoreId),
            ],
            'channel_type' => 'required|in:transfer,ewallet,qris,virtual_account,pulsa',
        ]);
    }

    public function destroy($id)
    {
        $channel = Channel::findOrFail($id);
        $channel->delete();

        return response()->json(null, 204);
    }
}
