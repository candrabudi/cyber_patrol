<?php

namespace App\Exports;

use App\Models\GamblingDeposit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

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
            });

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
            $assetBase = asset('storage/');

            $websiteProofs = $item->attachments
                ->where('attachment_type', 'website_proof')
                ->pluck('file_path')
                ->map(fn($path) => $assetBase . '/' . ltrim($path, '/'))
                ->implode(' | ');

            $accountProofs = $item->attachments
                ->where('attachment_type', 'account_proof')
                ->pluck('file_path')
                ->map(fn($path) => $assetBase . '/' . ltrim($path, '/'))
                ->implode(' | ');

            $qrisProofs = $item->attachments
                ->where('attachment_type', 'qris_proof')
                ->pluck('file_path')
                ->map(fn($path) => $assetBase . '/' . ltrim($path, '/'))
                ->implode(' | ');

            return [
                $item->website_name,
                $item->website_url,
                $item->is_confirmed_gambling ? 'Ya' : 'Tidak',
                $item->is_accessible ? 'Ya' : 'Tidak',
                $item->channel_id,
                $item->account_number,
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
                $qrisProofs
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama Website',
            'URL Website',
            'Terkonfirmasi Judi',
            'Dapat Diakses',
            'ID Saluran',
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
            'Keterangan',
            'Dibuat Pada',
            'Diperbarui Pada',
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

        $sheet->getStyle('A1:U' . $sheet->getHighestRow())->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:U' . $sheet->getHighestRow())->getAlignment()->setWrapText(true);

        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
        ];
        $sheet->getStyle('A1:U' . $sheet->getHighestRow())->applyFromArray($styleArray);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 35,
            'C' => 18,
            'D' => 15,
            'E' => 12,
            'F' => 20,
            'G' => 25,
            'H' => 18,
            'I' => 20,
            'J' => 18,
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
            'F' => NumberFormat::FORMAT_TEXT,
            'H' => NumberFormat::FORMAT_TEXT,
            'P' => NumberFormat::FORMAT_TEXT,
            'Q' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->freezePane('A2');

                $highestRow = $sheet->getHighestRow();

                for ($row = 2; $row <= $highestRow; $row++) {
                    for ($colIndex = 19; $colIndex <= 21; $colIndex++) {
                        $cell = $sheet->getCellByColumnAndRow($colIndex, $row);
                        $proofUrls = explode(' | ', $cell->getValue());
                        $cell->setValue(null);

                        $offsetX = 0;
                        foreach ($proofUrls as $url) {
                            $url = trim($url);
                            if (empty($url)) continue;

                            try {
                                $tempFile = tempnam(sys_get_temp_dir(), 'proof_');
                                $imageContent = Http::get($url)->body();
                                file_put_contents($tempFile, $imageContent);

                                $drawing = new Drawing();
                                $drawing->setPath($tempFile);
                                $drawing->setCoordinates($cell->getColumn() . $row);
                                $drawing->setOffsetX($offsetX);
                                $drawing->setOffsetY(5);
                                $drawing->setHeight(50);
                                $drawing->setWorksheet($sheet);

                                $offsetX += 60;
                            } catch (\Exception $e) {
                                // skip kalau error ambil gambar
                            }
                        }
                    }
                }
            },
        ];
    }
}
