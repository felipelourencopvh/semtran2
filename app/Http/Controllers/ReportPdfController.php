<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use PDF;

class ReportPdfController extends Controller
{
    public function show(Report $report)
    {
        $this->authorize('view', $report);

        $report->load([
            'author.roles',
            'relator',
            'team',
            'atividades.tipo',
            'atividades.situacao',
            'atividades.medida',
            'equipamentos',
            'condutores.veiculo',
            'condutores.motorista',
            'anexos',
        ]);

        $pdf = PDF::loadView('reports.pdf', [
            'report' => $report,
        ])->setPaper('a4');

        $filename = 'relatorio-'.$report->id.'.pdf';

        return $pdf->stream($filename);
    }
}
