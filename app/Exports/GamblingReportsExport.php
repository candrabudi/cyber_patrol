<?php

namespace App\Exports;

use App\Models\GamblingDeposit;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Facades\URL;

class GamblingReportsExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithColumnFormatting, WithEvents
{
    protected $ids;
    protected $exportAll;
    protected $search;
    protected $isSolved;
    protected $startDate;
    protected $endDate;

    public function __construct($params)
    {
        $this->ids = $params['ids'] ?? [];
        $this->exportAll = $params['export_all'] ?? false;
        $this->search = $params['search'] ?? null;
        $this->isSolved = $params['is_solved'] ?? null;
        $this->startDate = $params['start_date'] ?? null;
        $this->endDate = $params['end_date'] ?? null;
    }

    public function collection()
    {
        $customerId = Auth::user()->customer->id;

        $query = GamblingDeposit::with(['channel', 'attachments'])
            ->whereHas('channel', function ($q) use ($customerId) {
                $q->where('customer_id', $customerId);
            })
            ->where('report_status', 'approved');

        if (!$this->exportAll && count($this->ids)) {
            $query->whereIn('id', $this->ids);
        }

        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('website_name', 'like', "%{$search}%")
                    ->orWhere('website_url', 'like', "%{$search}%")
                    ->orWhere('account_name', 'like', "%{$search}%")
                    ->orWhere('account_number', 'like', "%{$search}%");
            });
        }

        if (!is_null($this->isSolved)) {
            $query->where('is_solved', (bool) $this->isSolved);
        }

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [
                $this->startDate . ' 00:00:00',
                $this->endDate . ' 23:59:59',
            ]);
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        return $data->map(function ($item) {
            $websiteProofs = $item->attachments
                ->where('attachment_type', 'website_proof')
                ->pluck('file_path')
                ->map(fn($path) => URL::asset('storage/' . $path))
                ->implode(' | ');

            $accountProofs = $item->attachments
                ->where('attachment_type', 'account_proof')
                ->pluck('file_path')
                ->map(fn($path) => URL::asset('storage/' . $path))
                ->implode(' | ');

            $qrisProofs = $item->attachments
                ->where('attachment_type', 'qris_proof')
                ->pluck('file_path')
                ->map(fn($path) => URL::asset('storage/' . $path))
                ->implode(' | ');

            return [
                $item->website_name,
                $item->website_url,
                $item->is_confirmed_gambling ? 'Ya' : 'Tidak',
                $item->is_accessible ? 'Ya' : 'Tidak',
                $item->channel->channel_type,
                '`' . $item->account_number,
                $item->account_name,
                $item->report_date,
                $item->report_evidence,
                $item->link_closure_date,
                $item->link_closure_status,
                $item->account_validation_date,
                $item->account_validation_status,
                $item->report_status,
                $item->is_solved ? 'Ya' : 'Tidak',
                $item->remarks,
                $item->created_at,
                $item->updated_at,
                $websiteProofs,
                $accountProofs,
                $qrisProofs,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama Website',
            'URL Website',
            'Konfirmasi Judi',
            'Dapat Diakses',
            'ID Channel',
            'Nomor Akun',
            'Nama Akun',
            'Tanggal Laporan',
            'Bukti Laporan',
            'Tanggal Penutupan Link',
            'Status Penutupan Link',
            'Tanggal Validasi Akun',
            'Status Validasi Akun',
            'Status Laporan',
            'Sudah Selesai',
            'Catatan',
            'Dibuat Pada',
            'Diupdate Pada',
            'Bukti Website',
            'Bukti Akun',
            'Bukti QRIS',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:U1')->getFont()->setBold(true);
        $sheet->getStyle('A1:U1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $sheet->getStyle('A1:U1')->getFill()->getStartColor()->setARGB('FFB0C4DE');
        $sheet->getRowDimension(1)->setRowHeight(40);
        $sheet->getStyle('A1:U1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:U1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];

        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:U{$highestRow}")->applyFromArray($styleArray);
        $sheet->getStyle("A2:U{$highestRow}")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    }


    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 35,
            'C' => 15,
            'D' => 15,
            'E' => 12,
            'F' => 25,
            'G' => 25,
            'H' => 18,
            'I' => 30,
            'J' => 20,
            'K' => 20,
            'L' => 20,
            'M' => 20,
            'N' => 20,
            'O' => 15,
            'P' => 40,
            'Q' => 25,
            'R' => 25,
            'S' => 60,
            'T' => 60,
            'U' => 60,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_TEXT,
            'K' => NumberFormat::FORMAT_TEXT,
            'L' => NumberFormat::FORMAT_TEXT,
            'M' => NumberFormat::FORMAT_TEXT,
            'N' => NumberFormat::FORMAT_TEXT,
            'O' => NumberFormat::FORMAT_TEXT,
            'P' => NumberFormat::FORMAT_TEXT,
            'Q' => NumberFormat::FORMAT_TEXT,
            'R' => NumberFormat::FORMAT_TEXT,
            'S' => NumberFormat::FORMAT_TEXT,
            'T' => NumberFormat::FORMAT_TEXT,
            'U' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->freezePane('A2');
            },
        ];
    }
}
