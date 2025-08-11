<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\GamblingDeposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RGamblingDepositController extends Controller
{
    public function index()
    {
        return view('reviewer.gambling_deposits.index');
    }

    public function data(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search  = $request->get('search', '');
        $status  = $request->get('status', 'all');

        $query = GamblingDeposit::with([
            'channel.provider.customerProviders.customer',
            'creator'
        ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('website_name', 'like', "%{$search}%")
                    ->orWhere('website_url', 'like', "%{$search}%")
                    ->orWhere('account_name', 'like', "%{$search}%")
                    ->orWhere('account_number', 'like', "%{$search}%")
                    ->orWhereHas('channel.provider.customerProviders.customer', function ($q2) use ($search) {
                        $q2->where('full_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('creator', function ($q3) use ($search) {
                        $q3->where('username', 'like', "%{$search}%");
                    });
            });
        }

        if ($status !== 'all') {
            $query->where('report_status', $status);
        }

        $data = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($data);
    }

    public function edit($id)
    {
        $gamblingDeposit = GamblingDeposit::with([
            'channel.provider.customerProviders.customer',
            'attachments',
            'logs.changer'
        ])->findOrFail($id);

        return view('reviewer.gambling_deposits.edit', compact('gamblingDeposit'));
    }

    public function update(Request $request, $id)
    {
        $gamblingDeposit = GamblingDeposit::findOrFail($id);

        $statusRule = 'required|in:pending,approved,rejected';
        if (in_array($gamblingDeposit->report_status, ['approved', 'rejected'])) {
            $statusRule = 'in:' . $gamblingDeposit->report_status;
        }

        $validated = $request->validate([
            'report_status' => $statusRule,
            'remarks' => 'nullable|string',
            'is_confirmed_gambling' => 'required|boolean',
            'is_accessible' => 'required|boolean',
        ]);

        $changes = [];
        foreach ($validated as $field => $newValue) {
            $oldValue = $gamblingDeposit->{$field};

            if (in_array($field, ['is_confirmed_gambling', 'is_accessible'])) {
                $newValue = (bool) $newValue;
            }

            if ($oldValue != $newValue) {
                $changes[] = [
                    'field_changed' => $field,
                    'old_value' => is_bool($oldValue) ? ($oldValue ? 'Ya' : 'Tidak') : $oldValue,
                    'new_value' => is_bool($newValue) ? ($newValue ? 'Ya' : 'Tidak') : $newValue,
                    'changed_by' => Auth::id(),
                ];

                $gamblingDeposit->{$field} = $newValue;
            }
        }

        $gamblingDeposit->save();

        if (!empty($changes)) {
            foreach ($changes as $log) {
                $gamblingDeposit->logs()->create($log);
            }
        }

        return redirect()
            ->route('reviewer.gambling_deposits.edit', $gamblingDeposit->id)
            ->with('success', 'Data berhasil diperbarui.');
    }
}
