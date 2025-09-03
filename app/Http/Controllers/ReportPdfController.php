<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use PDF;
use App\Models\Department;

class ReportPdfController extends Controller
{
    public function show(Report $report)
    {
        $this->authorize('view', $report);

        $report->load([
            'author.department',       // << AQUI
            'relator.department',      // << AQUI
        //    'department',              // << se report tiver department_id
            'author.roles',
            'team',
            'atividades.tipo',
            'atividades.situacao',
            'atividades.medida',
            'equipamentos',
            'condutores.veiculo',
            'condutores.motorista',
            'anexos',
        ]);

        $pdf = \PDF::loadView('reports.pdf', compact('report'))->setPaper('a4');
        return $pdf->stream('relatorio-'.$report->id.'.pdf');
    }
}
