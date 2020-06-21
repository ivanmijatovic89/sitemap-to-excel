<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Sheet;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;



class SeoPageExport implements FromArray, WithHeadings, ShouldAutoSize, WithEvents
{
    protected $pages;

    public function __construct(array $pages)
    {
        $this->pages = $pages;
    }

    public function array(): array
    {
        return $this->pages;
    }

    public function headings(): array
    {
        return [
            'url short',
            'title' ,
            'description' ,
            'url' ,
            'canonical' ,
            'image' ,
            'og:title',
            'og:image',
            'og:url',
            'og:site_name',
            'og:description',
            'og:type',
            'h1',
            'h2',
        ];
    }

    public function registerEvents(): array
    {
        Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
            $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
        });
        return [
            AfterSheet::class => [self::class, 'afterSheet'],
            BeforeSheet::class => function(BeforeSheet $event) {
                $sheet = $event->getSheet()->getDelegate();
                $sheet->getParent()->getDefaultStyle()->applyFromArray([
                    'font' => [
                        'size' => 16,
                    ],
                ]);
            },

        ];
    }

    public static function afterSheet(AfterSheet $event){

        $event->sheet->freezePane('B2');

        $event->sheet->styleCells(
            'A1:'.$event->sheet->getDelegate()->getHighestColumn().'1',
            [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => 'AACCCCCC']
                ],
                'font' => [
                    'bold' => true,
                ]
            ]
        );

        $event->sheet
            ->getDelegate()
            ->getStyle('A1:A'.$event->sheet->getDelegate()->getHighestRow())
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('AACCCCCC');

        foreach ($event->sheet->getColumnIterator() as $row) {
            foreach ($row->getCellIterator() as $cell) {
                if ($cell->getValue() && str_contains($cell->getValue(), '://')) {
                    $cell->setHyperlink(new Hyperlink($cell->getValue(), $cell->getValue()));

                        $event->sheet->getStyle($cell->getCoordinate())->applyFromArray([
                        'font' => [
                            'color' => ['rgb' => '0000FF'],
                            'underline' => 'single'
                        ]
                    ]);
                }
            }
        }

    }
}
