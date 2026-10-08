<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomerStatementExport implements FromArray, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $customer;
    protected $currencyGroups;
    protected $dateFrom;
    protected $dateTo;

    public function __construct($customer, $currencyGroups, $dateFrom, $dateTo)
    {
        $this->customer = $customer;
        $this->currencyGroups = $currencyGroups;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    public function headings(): array
    {
        return ['التاريخ', 'البيان', 'المرجع', 'البيان التفصيلي', 'مدين', 'دائن', 'العملة', 'الرصيد'];
    }

    public function array(): array
    {
        $rows = [];

        $rows[] = [
            'كشف حساب',
            $this->customer->name ?? '',
            'الفترة',
            $this->dateFrom . ' إلى ' . $this->dateTo,
            '',
            '',
            '',
            '',
        ];
        $rows[] = ['', '', '', '', '', '', '', ''];

        foreach ($this->currencyGroups as $group) {
            $curCode = $group['currency']?->code ?? 'ج.م';

            $rows[] = ['حساب العملة: ' . $curCode, '', '', '', '', '', '', ''];

            $running = (float) $group['openingBalance'];
            if ($running != 0) {
                $rows[] = [
                    '-',
                    'رصيد افتتاحي',
                    '-',
                    '-',
                    $running > 0 ? $running : '-',
                    $running < 0 ? abs($running) : '-',
                    $curCode,
                    $running,
                ];
            }

            foreach ($group['transactions'] as $tx) {
                $running += $tx['amount'];
                $rows[] = [
                    $tx['date'] ? $tx['date']->format('Y-m-d') : '-',
                    $tx['type'],
                    $tx['reference'],
                    $tx['summary'] ?? '-',
                    $tx['amount'] > 0 ? $tx['amount'] : '-',
                    $tx['amount'] < 0 ? abs($tx['amount']) : '-',
                    $curCode,
                    $running,
                ];
            }

            $total = (float) $group['total'];
            $rows[] = [
                '',
                'إجمالي ' . $curCode,
                '',
                '',
                $total > 0 ? $total : '-',
                $total < 0 ? abs($total) : '-',
                $curCode,
                $total,
            ];
            $rows[] = ['', '', '', '', '', '', '', ''];
        }

        return $rows;
    }

    public function map($row): array
    {
        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}