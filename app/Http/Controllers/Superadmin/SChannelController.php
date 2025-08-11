<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Channel;
use App\Models\Customer;
use App\Models\GamblingDeposit;
use App\Models\GamblingDepositAccount;
use App\Models\Provider;
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
        if (isset($validated['channel_code'])) {
            $channel->channel_code = $validated['channel_code'];
        } elseif (isset($bank->code)) {
            $channel->channel_code = $bank->code;
        } elseif ($request->channel_type == 'pulsa') {
            $provider = Provider::where('name', $customer->full_name)
                ->first();
            $channel->channel_code = $provider->prefixes;
        } else {
            $channel->channel_code = null;
        }
        $channel->channel_type = $validated['channel_type'];
        $channel->save();

        if ($request->channel_type == "pulsa") {
            $gamblingDepositIds = GamblingDepositAccount::where('channel_type', 'pulsa')
                ->where('channel_name', $customer->full_name)
                ->pluck('gambling_deposit_id');

            if ($gamblingDepositIds->isNotEmpty()) {
                GamblingDeposit::whereIn('id', $gamblingDepositIds)
                    ->update([
                        'channel_id' => $channel->id,
                        'is_non_member' => 0,
                    ]);
            }
        }

        if ($request->channel_type == 'transfer') {
            $gamblingDepositIds = GamblingDepositAccount::where('channel_type', 'transfer')
                ->where('channel_name', $customer->full_name)
                ->pluck('gambling_deposit_id');

            if ($gamblingDepositIds->isNotEmpty()) {
                GamblingDeposit::whereIn('id', $gamblingDepositIds)
                    ->update([
                        'channel_id' => $channel->id,
                        'is_non_member' => 0,
                    ]);
            }
        }

        if ($request->channel_type == 'qris') {
            $gamblingDepositIds = GamblingDepositAccount::where('channel_type', 'qris')
                ->where('channel_code', $validated['channel_code'])
                ->pluck('gambling_deposit_id');

            if ($gamblingDepositIds->isNotEmpty()) {
                GamblingDeposit::whereIn('id', $gamblingDepositIds)
                    ->update([
                        'channel_id' => $channel->id,
                        'is_non_member' => 0,
                    ]);
            }
        }


        if ($request->channel_type == 'virtual_account') {
            $gamblingDepositIds = GamblingDepositAccount::where('channel_type', 'virtual_account')
                ->where('channel_code', $validated['channel_code'])
                ->pluck('gambling_deposit_id');

            if ($gamblingDepositIds->isNotEmpty()) {
                GamblingDeposit::whereIn('id', $gamblingDepositIds)
                    ->update([
                        'channel_id' => $channel->id,
                        'is_non_member' => 0,
                    ]);
            }
        }

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
        if (isset($validated['channel_code'])) {
            $channel->channel_code = $validated['channel_code'];
        } elseif (isset($bank->code)) {
            $channel->channel_code = $bank->code;
        } elseif ($request->channel_type == 'pulsa') {
            $provider = Provider::where('name', $customer->full_name)
                ->first();
            $channel->channel_code = $provider->prefixes;
        } else {
            $channel->channel_code = null;
        }
        $channel->channel_type = $validated['channel_type'];
        $channel->save();
        $channel->fresh();


        if ($request->channel_type == "pulsa") {
            $gamblingDepositIds = GamblingDepositAccount::where('channel_type', 'pulsa')
                ->where('channel_name', $customer->full_name)
                ->pluck('gambling_deposit_id');

            if ($gamblingDepositIds->isNotEmpty()) {
                GamblingDeposit::whereIn('id', $gamblingDepositIds)
                    ->update([
                        'channel_id' => $channel->id,
                        'is_non_member' => 0,
                    ]);
            }
        }

        if ($request->channel_type == 'transfer') {
            $gamblingDepositIds = GamblingDepositAccount::where('channel_type', 'transfer')
                ->where('channel_name', $customer->full_name)
                ->pluck('gambling_deposit_id');

            if ($gamblingDepositIds->isNotEmpty()) {
                GamblingDeposit::whereIn('id', $gamblingDepositIds)
                    ->update([
                        'channel_id' => $channel->id,
                        'is_non_member' => 0,
                    ]);
            }
        }

        if ($request->channel_type == 'qris') {
            $gamblingDepositIds = GamblingDepositAccount::where('channel_type', 'qris')
                ->where('channel_code', $validated['channel_code'])
                ->pluck('gambling_deposit_id');

            if ($gamblingDepositIds->isNotEmpty()) {
                GamblingDeposit::whereIn('id', $gamblingDepositIds)
                    ->update([
                        'channel_id' => $channel->id,
                        'is_non_member' => 0,
                    ]);
            }
        }


        if ($request->channel_type == 'virtual_account') {
            $gamblingDepositIds = GamblingDepositAccount::where('channel_type', 'virtual_account')
                ->where('channel_code', $validated['channel_code'])
                ->pluck('gambling_deposit_id');

            if ($gamblingDepositIds->isNotEmpty()) {
                GamblingDeposit::whereIn('id', $gamblingDepositIds)
                    ->update([
                        'channel_id' => $channel->id,
                        'is_non_member' => 0,
                    ]);
            }
        }
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
