<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\GamblingDeposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\GamblingReportsExport;
use Maatwebsite\Excel\Facades\Excel;

class SGamblingReportController extends Controller
{
    public function index()
    {
        return view('superadmin.gambling_reports.index');
    }

    public function data(Request $request)
    {
        $query = GamblingDeposit::with('channel')
            ->where('report_status', 'approved');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('website_name', 'like', "%{$search}%")
                    ->orWhere('website_url', 'like', "%{$search}%")
                    ->orWhere('account_name', 'like', "%{$search}%")
                    ->orWhere('account_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_solved')) {
            $query->where('is_solved', (bool) $request->is_solved);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59',
            ]);
        }

        $perPage = $request->get('per_page', 10);
        $perPage = $perPage === 'all' ? $query->count() : (int) $perPage;

        $data = $query->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($data);
    }

    public function export(Request $request)
    {
        $params = $request->only(['ids', 'export_all', 'search', 'is_solved', 'start_date', 'end_date']);
        $fileName = 'gambling_reports_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new GamblingReportsExport($params), $fileName);
    }
}
