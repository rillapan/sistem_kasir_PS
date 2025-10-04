<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FnbSalesExport implements FromView, ShouldAutoSize, WithHeadings, WithStyles
{
    protected $salesData;
    protected $startDate;
    protected $endDate;

    public function __construct($salesData, $startDate, $endDate)
    {
        $this->salesData = $salesData;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        return view('fnb.laporan-excel', [
            'salesData' => $this->salesData,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }

    public function headings(): array
    {
        return [
            'Nama Produk',
            'Jumlah Terjual',
            'Total Modal',
            'Total Penjualan',
            'Laba/Rugi',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $rowCount = $sheet->getHighestRow();

        $styleArray = [
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ];

        for ($row = 4; $row <= $rowCount; $row++) {
            $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray($styleArray);
        }
    }
}
