<?php

namespace App\Http\Controllers;

use App\Models\LabResult;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\Role;
use App\Models\Vital;
use Illuminate\View\View;

/** FR8: simple summary report. Counts only — no patient names or clinical details. */
class ReportController extends Controller
{
    public function index(): View
    {
        $totals = [
            'Patients registered' => Patient::count(),
            'Diagnoses recorded'  => MedicalRecord::count(),
            'Vital sign entries'  => Vital::count(),
            'Prescriptions'       => Prescription::count(),
            'Lab results'         => LabResult::count(),
        ];

        // New patients per month, last 6 months (done in PHP so it works on MySQL and SQLite).
        $months = [];
        foreach (range(5, 0) as $i) {
            $months[now()->subMonths($i)->format('M Y')] = 0;
        }
        $dates = Patient::where('created_at', '>=', now()->subMonths(5)->startOfMonth())->pluck('created_at');
        foreach ($dates as $date) {
            $key = $date->format('M Y');
            if (isset($months[$key])) {
                $months[$key]++;
            }
        }

        $byGender = Patient::selectRaw('gender, COUNT(*) as total')->groupBy('gender')->pluck('total', 'gender');

        $staffByRole = Role::withCount('users')->orderBy('label')->get();

        return view('reports.index', compact('totals', 'months', 'byGender', 'staffByRole'));
    }
}
