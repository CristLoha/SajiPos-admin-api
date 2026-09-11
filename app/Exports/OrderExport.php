<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrderExport implements FromView, ShouldAutoSize, WithStyles, WithEvents
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('pages.reports.export', $this->data);
    }

    public function styles(Worksheet $sheet): ?array
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Set lebar manual biar lega dan teks gak tumpang tindih
                $sheet->getColumnDimension('A')->setWidth(25); // Waktu / Header
                $sheet->getColumnDimension('B')->setWidth(20); // No. Struk / Kolom Nilai
                $sheet->getColumnDimension('C')->setWidth(25); // Kasir
                $sheet->getColumnDimension('D')->setWidth(18); // Pembayaran
                $sheet->getColumnDimension('E')->setWidth(25); // Total Pendapatan
            },
        ];
    }
}
