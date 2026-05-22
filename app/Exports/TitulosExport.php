<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;

use App\Models\Parcela;

class TitulosExport implements FromCollection,
    WithHeadings, 
    WithMapping, 
    WithStyles, 
    WithColumnWidths, 
    ShouldAutoSize,
    WithTitle,
    WithEvents
{
    public $titulosId = [];
    public $query;

    public function __construct($titulosId = [], $query = null){
        $this->titulosId = $titulosId;
        $this->query = $query;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(){
        if(!empty($this->titulosId)){
            return Parcela::whereIn('id', $this->titulosId)->get();
        }else if($this->query !== null){
            return $this->query->get();
        }
    }

    public function headings(): array
    {
        return [
            'ID Parcela',
            'Nº Parcela',
            'Tipo',
            'Entidade',
            'Descrição',
            'Centro de Custo',
            'Categoria',
            'Valor Parcela',
            'Valor Pago',
            'Saldo Devedor',
            'Data Vencimento',
            'Data Emissão',
            'Status (Admin)',
            'Status Calculado',
            'Último Pagamento',
        ];
    }

    public function map($parcela): array{
        $titulo = $parcela->titulo;

        $ultimoPagamento = $parcela->movimentacoes
            ->sortByDesc('data_pagamento')
            ->first();

        return [
            $parcela->id,
            $parcela->numero_parcela,

            $titulo?->tipo,
            $titulo?->entidade?->razao_social ?? '-',
            $titulo?->descricao,

            $titulo?->centroCusto?->nome ?? '-',
            $titulo?->categoriaFinanceira?->nome ?? '-',

            number_format($parcela->valor, 2, ',', '.'),
            number_format($parcela->valor_pago, 2, ',', '.'),
            number_format($parcela->saldo_devedor, 2, ',', '.'),

            \Carbon\Carbon::parse($parcela->data_vencimento)->format('d/m/Y'),
            \Carbon\Carbon::parse($titulo?->data_emissao)->format('d/m/Y'),

            $parcela->status,
            $parcela->status_calculado,

            optional($ultimoPagamento?->data_pagamento)->format('d/m/Y'),
        ];
    }

    public function title(): string{
        return 'Lancamentos';
    }

    public function columnWidths(): array{
        return [
            'A' => 12,
            'B' => 12,
            'C' => 12,
            'D' => 25,
            'E' => 30,
            'F' => 25,
            'G' => 25,
            'H' => 15,
            'I' => 15,
            'J' => 15,
            'K' => 15,
            'L' => 15,
            'M' => 18,
            'N' => 18,
            'O' => 18,
        ];
    }

    public function styles(Worksheet $sheet){
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
            ],
        ];
    }

    public function registerEvents(): array{
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $sheet->getStyle('A1:O1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => 'solid',
                        'color' => ['rgb' => '1F4E78'], 
                    ],
                    'alignment' => [
                        'horizontal' => 'center',
                        'vertical' => 'center',
                    ],
                ]);

                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle("A1:O{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => 'thin',
                            'color' => ['rgb' => 'DDDDDD'],
                        ],
                    ],
                ]);

                for ($row = 2; $row <= $lastRow; $row++) {
                    if ($row % 2 == 0) {
                        $sheet->getStyle("A{$row}:O{$row}")
                            ->getFill()
                            ->setFillType('solid')
                            ->getStartColor()
                            ->setRGB('F7F7F7');
                    }
                }

                $sheet->getStyle("H2:J{$lastRow}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00');

                $sheet->getStyle("K2:O{$lastRow}")
                    ->getNumberFormat()
                    ->setFormatCode('dd/mm/yyyy');

                $sheet->setAutoFilter("A1:O{$lastRow}");

                $sheet->freezePane('A2');

                $sheet->getStyle("A2:O{$lastRow}")
                    ->getAlignment()
                    ->setVertical('center');

                $sheet->getStyle("A2:C{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal('center');

                $sheet->getStyle("K2:O{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal('center');
            },
        ];
    }
}
