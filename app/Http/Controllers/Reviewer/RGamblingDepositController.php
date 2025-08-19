<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\ErrorLog;
use App\Models\GamblingDeposit;
use App\Models\GamblingDepositAttachment;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Zxing\QrReader;

class RGamblingDepositController extends Controller
{
    public function index()
    {
        return view('reviewer.gambling_deposits.index');
    }
    public function data(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');

        $query = GamblingDeposit::with(['channel.customer', 'creator', 'gamblingDepositAccounts']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('website_name', 'like', "%{$search}%")
                    ->orWhere('website_url', 'like', "%{$search}%")
                    ->orWhere('account_name', 'like', "%{$search}%")
                    ->orWhere('account_number', 'like', "%{$search}%")
                    ->orWhereHas('channel.customer', function ($q2) use ($search) {
                        $q2->where('full_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('creator', function ($q3) use ($search) {
                        $q3->where('username', 'like', "%{$search}%");
                    });
            });
        }

        $paginated = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $data = $paginated->getCollection()->transform(function ($item) {
            $accountChannel = $item->gamblingDepositAccounts->first();
            $channelName = $accountChannel ? $accountChannel->channel_name : null;

            return [
                'id' => $item->id,
                'website_name' => $item->website->website_name,
                'website_url' => $item->website->website_url,
                'website_attachment' => asset('storage/' . $item->website->website_proofs),
                'channel' => [
                    'channel_type' => $item->channel ? $item->channel->channel_type : $accountChannel->channel_type,
                    'customer' => [
                        'full_name' => $item->channel?->customer?->full_name,
                    ],
                ],
                'account_name' => $item->account_name,
                'account_number' => $item->account_number,
                'creator' => [
                    'username' => $item->creator?->username,
                ],
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
                'report_status' => $item->report_status,
                'is_non_member' => $item->is_non_member,
                'channel_name' => $channelName,
                'account_proof' => asset('storage/' . $item->attachmentAccount->file_path)
            ];
        });

        return response()->json([
            'data' => $data,
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'total' => $paginated->total(),
        ]);
    }


    public function edit($id)
    {
        $gamblingDeposit = GamblingDeposit::with([
            'channel',
            'attachments',
            'logs.changer'
        ])
            ->findOrFail($id);

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
                $gamblingDeposit->logs()->create([
                    'field_changed' => $log['field_changed'],
                    'old_value' => $log['old_value'],
                    'new_value' => $log['new_value'],
                    'changed_by' => $log['changed_by'],
                ]);
            }
        }

        return redirect()
            ->route('reviewer.gambling_deposits.edit', $gamblingDeposit->id)
            ->with('success', 'Data berhasil diperbarui.');
    }

    public function changeStatus($id, $status)
    {
        DB::beginTransaction();
        try {
            $find = GamblingDeposit::where('id', $id)
                ->where('report_status', 'pending')
                ->first();
            if (!$find) {
                return response()
                    ->json([
                        'success' => 0,
                        'message' => 'Data not found.'
                    ], 404);
            }

            $find->report_status = $status;
            $find->save();

            DB::commit();
            return response()
                ->json([
                    'success' => 1,
                    'message' => 'Success update report status'
                ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()
                ->json([
                    'success' => 0,
                    'message' => 'Internal server error',
                    'message_error' => $e->getMessage()
                ], 500);
        }
    }
}
