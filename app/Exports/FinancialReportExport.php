<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Illuminate\Support\Collection;

class FinancialReportExport
{
    protected $incomes;
    protected $expenses;
    protected $summary;
    protected $filters;

    public function __construct(Collection $incomes, Collection $expenses, array $summary, array $filters)
    {
        $this->incomes = $incomes;
        $this->expenses = $expenses;
        $this->summary = $summary;
        $this->filters = $filters;
    }

    public function download(string $filename)
    {
        $spreadsheet = new Spreadsheet();
        
        // Crear hoja de resumen
        $this->createSummarySheet($spreadsheet);
        
        // Crear hoja de ingresos
        if ($this->incomes->count() > 0) {
            $this->createIncomesSheet($spreadsheet);
        }
        
        // Crear hoja de egresos
        if ($this->expenses->count() > 0) {
            $this->createExpensesSheet($spreadsheet);
        }
        
        // Establecer la primera hoja como activa
        $spreadsheet->setActiveSheetIndex(0);
        
        // Crear el writer
        $writer = new Xlsx($spreadsheet);
        
        // Crear respuesta para descarga
        $temp = tempnam(sys_get_temp_dir(), 'excel_');
        $writer->save($temp);
        
        return response()->download($temp, $filename)->deleteFileAfterSend(true);
    }

    protected function createSummarySheet(Spreadsheet $spreadsheet)
    {
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Resumen');

        // Estilos
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4338CA']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];

        $subHeaderStyle = [
            'font' => ['bold' => true, 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E5E7EB']],
        ];

        $currencyFormat = '"$"#,##0.00';

        // Título
        $sheet->setCellValue('A1', 'REPORTE FINANCIERO - FLOWFAST');
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Filtros aplicados
        $row = 3;
        $sheet->setCellValue('A' . $row, 'Filtros Aplicados:');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;
        $sheet->setCellValue('A' . $row, 'Período:');
        $sheet->setCellValue('B' . $row, $this->filters['dateFrom'] . ' - ' . $this->filters['dateTo']);
        $row++;
        $sheet->setCellValue('A' . $row, 'Liga:');
        $sheet->setCellValue('B' . $row, $this->filters['league']);
        $row++;
        $sheet->setCellValue('A' . $row, 'Temporada:');
        $sheet->setCellValue('B' . $row, $this->filters['season']);
        $row += 2;

        // Resumen de Ingresos
        $sheet->setCellValue('A' . $row, 'RESUMEN DE INGRESOS');
        $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray($subHeaderStyle);
        $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setColor(new Color('10B981'));
        $row++;
        $sheet->setCellValue('A' . $row, 'Total Ingresos:');
        $sheet->setCellValue('B' . $row, $this->summary['income']['total']);
        $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode($currencyFormat);
        $row++;
        $sheet->setCellValue('A' . $row, 'Ingresos Confirmados:');
        $sheet->setCellValue('B' . $row, $this->summary['income']['confirmed']);
        $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode($currencyFormat);
        $row++;
        $sheet->setCellValue('A' . $row, 'Ingresos Pendientes:');
        $sheet->setCellValue('B' . $row, $this->summary['income']['pending']);
        $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode($currencyFormat);
        $row++;
        $sheet->setCellValue('A' . $row, 'Cantidad de Registros:');
        $sheet->setCellValue('B' . $row, $this->summary['income']['count']);
        $row += 2;

        // Resumen de Egresos
        $sheet->setCellValue('A' . $row, 'RESUMEN DE EGRESOS');
        $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray($subHeaderStyle);
        $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setColor(new Color('EF4444'));
        $row++;
        $sheet->setCellValue('A' . $row, 'Total Egresos:');
        $sheet->setCellValue('B' . $row, $this->summary['expense']['total']);
        $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode($currencyFormat);
        $row++;
        $sheet->setCellValue('A' . $row, 'Egresos Confirmados:');
        $sheet->setCellValue('B' . $row, $this->summary['expense']['confirmed']);
        $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode($currencyFormat);
        $row++;
        $sheet->setCellValue('A' . $row, 'Egresos Pendientes:');
        $sheet->setCellValue('B' . $row, $this->summary['expense']['pending']);
        $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode($currencyFormat);
        $row++;
        $sheet->setCellValue('A' . $row, 'Cantidad de Registros:');
        $sheet->setCellValue('B' . $row, $this->summary['expense']['count']);
        $row += 2;

        // Balance
        $sheet->setCellValue('A' . $row, 'BALANCE');
        $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray($subHeaderStyle);
        $sheet->getStyle('A' . $row . ':D' . $row)->getFont()->setColor(new Color('4338CA'));
        $row++;
        $sheet->setCellValue('A' . $row, 'Balance Total:');
        $sheet->setCellValue('B' . $row, $this->summary['balance']['total']);
        $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode($currencyFormat);
        if ($this->summary['balance']['total'] < 0) {
            $sheet->getStyle('B' . $row)->getFont()->setColor(new Color('EF4444'));
        } else {
            $sheet->getStyle('B' . $row)->getFont()->setColor(new Color('10B981'));
        }
        $row++;
        $sheet->setCellValue('A' . $row, 'Balance Confirmado:');
        $sheet->setCellValue('B' . $row, $this->summary['balance']['confirmed']);
        $sheet->getStyle('B' . $row)->getNumberFormat()->setFormatCode($currencyFormat);

        // Ajustar anchos de columna
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
    }

    protected function createIncomesSheet(Spreadsheet $spreadsheet)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Ingresos');

        // Headers
        $headers = ['Fecha', 'Liga', 'Temporada', 'Equipo', 'Tipo', 'Descripción', 'Estado', 'Método Pago', 'Referencia', 'Monto'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        // Estilo del header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '10B981']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Datos
        $row = 2;
        $typeLabels = [
            'registration_fee' => 'Cuota de Inscripción',
            'match_fee' => 'Pago por Partido',
            'penalty_fee' => 'Multa',
            'late_payment_fee' => 'Recargo',
            'championship_fee' => 'Cuota de Liguilla',
            'friendly_match_fee' => 'Pago por Amistoso',
            'other' => 'Otros',
        ];

        foreach ($this->incomes as $income) {
            $sheet->setCellValue('A' . $row, $income->created_at->format('d/m/Y'));
            $sheet->setCellValue('B' . $row, $income->league?->name ?? 'N/A');
            $sheet->setCellValue('C' . $row, $income->season?->name ?? 'N/A');
            $sheet->setCellValue('D' . $row, $income->team?->name ?? 'N/A');
            $sheet->setCellValue('E' . $row, $typeLabels[$income->income_type] ?? $income->income_type);
            $sheet->setCellValue('F' . $row, $income->description ?? '-');
            $sheet->setCellValue('G' . $row, $income->status_label);
            $sheet->setCellValue('H' . $row, $income->payment_method ?? '-');
            $sheet->setCellValue('I' . $row, $income->payment_reference ?? '-');
            $sheet->setCellValue('J' . $row, $income->amount);
            $sheet->getStyle('J' . $row)->getNumberFormat()->setFormatCode('"$"#,##0.00');
            $row++;
        }

        // Total
        $sheet->setCellValue('I' . $row, 'TOTAL:');
        $sheet->setCellValue('J' . $row, $this->incomes->sum('amount'));
        $sheet->getStyle('I' . $row . ':J' . $row)->getFont()->setBold(true);
        $sheet->getStyle('J' . $row)->getNumberFormat()->setFormatCode('"$"#,##0.00');
        $sheet->getStyle('I' . $row . ':J' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D1FAE5');

        // Ajustar anchos de columna
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    protected function createExpensesSheet(Spreadsheet $spreadsheet)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Egresos');

        // Headers
        $headers = ['Fecha', 'Liga', 'Temporada', 'Tipo', 'Descripción', 'Beneficiario', 'Estado', 'Método Pago', 'Referencia', 'Monto'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        // Estilo del header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EF4444']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Datos
        $row = 2;
        $typeLabels = [
            'referee_payment' => 'Pago a Árbitro',
            'venue_rental' => 'Alquiler de Cancha',
            'equipment' => 'Equipo Deportivo',
            'maintenance' => 'Mantenimiento',
            'utilities' => 'Servicios',
            'staff_salary' => 'Salario de Personal',
            'marketing' => 'Marketing',
            'insurance' => 'Seguros',
            'other' => 'Otros',
        ];

        foreach ($this->expenses as $expense) {
            $sheet->setCellValue('A' . $row, $expense->created_at->format('d/m/Y'));
            $sheet->setCellValue('B' . $row, $expense->league?->name ?? 'N/A');
            $sheet->setCellValue('C' . $row, $expense->season?->name ?? 'N/A');
            $sheet->setCellValue('D' . $row, $typeLabels[$expense->expense_type] ?? $expense->expense_type);
            $sheet->setCellValue('E' . $row, $expense->description ?? '-');
            $sheet->setCellValue('F' . $row, $expense->beneficiary?->name ?? ($expense->referee?->user?->name ?? 'N/A'));
            $sheet->setCellValue('G' . $row, $expense->status_label);
            $sheet->setCellValue('H' . $row, $expense->payment_method ?? '-');
            $sheet->setCellValue('I' . $row, $expense->payment_reference ?? '-');
            $sheet->setCellValue('J' . $row, $expense->amount);
            $sheet->getStyle('J' . $row)->getNumberFormat()->setFormatCode('"$"#,##0.00');
            $row++;
        }

        // Total
        $sheet->setCellValue('I' . $row, 'TOTAL:');
        $sheet->setCellValue('J' . $row, $this->expenses->sum('amount'));
        $sheet->getStyle('I' . $row . ':J' . $row)->getFont()->setBold(true);
        $sheet->getStyle('J' . $row)->getNumberFormat()->setFormatCode('"$"#,##0.00');
        $sheet->getStyle('I' . $row . ':J' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEE2E2');

        // Ajustar anchos de columna
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}
