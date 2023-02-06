<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class LaporanTamiu implements FromView, WithStyles, ShouldAutoSize, WithEvents, WithColumnWidths, WithColumnFormatting
{
    protected $tamiu;

    public function __construct($tamiu, $request) {
        $this->tamiu = $tamiu;
        $this->request = $request;
    }

    public function view(): View
    {
        $time_limit = ini_get('max_execution_time');
        $memory_limit = ini_get('memory_limit');
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $data = [
            'tamiu' => $this->tamiu,
            'request' => $this->request
        ];

        if ($this->request->banjar_adat_tamu) {
            return view("vendor.export.excel.desa_adat.laporan-tamiu", compact('data'));
        } else{
            return view("vendor.export.excel.banjar_adat.laporan-tamiu", compact('data'));
        }
    }

    public function columnWidths(): array
    {
        return [
            'A' => 7,
            'B' => 20,
            'C' => 20,
            'D' => 50,
            'E' => 25,
            'F' => 25,
            'G' => 25,
            'H' => 100,
            'I' => 25,
            'J' => 22,
            'K' => 22,
            'L' => 30,
            'M' => 22,
            'N' => 22,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1   => [
                'font' => array(
                    'size'      =>  12,
                    'bold'      =>  true
                )
            ],
            3   => [
                'font' => array(
                    'size'      =>  12,
                    'bold'      =>  true
                )
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('A:N')->getFont()->setSize(12);
                $event->sheet->getDelegate()->getStyle('A:N')->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getStyle('A:N')->getFont()->setName("Times New Roman");
                $event->sheet->getDelegate()->getStyle(1)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $event->sheet->getDelegate()->getStyle(1)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle(3)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $event->sheet->getDelegate()->getStyle(3)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}
