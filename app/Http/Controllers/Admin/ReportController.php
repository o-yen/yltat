<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Talent;
use App\Models\Company;
use App\Models\Placement;
use App\Models\BudgetTransaction;
use App\Models\BudgetAllocation;
use App\Models\IntakeBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $batches = IntakeBatch::orderByDesc('year')->get();
        $companies = Company::where('status', 'active')->orderBy('company_name')->get();
        $currentYear = date('Y');

        return view('admin.reports.index', compact('batches', 'companies', 'currentYear'));
    }

    public function talentReport(Request $request)
    {
        $query = Talent::with(['placements.company', 'placements.batch'])
            ->whereNotNull('id_graduan');

        if ($request->filled('status')) {
            $query->where(function ($statusQuery) use ($request) {
                $statusQuery->where('status', $request->status)
                    ->orWhere('status_aktif', $request->status);
            });
        }

        if ($request->filled('university')) {
            $query->where('university', 'like', "%{$request->university}%");
        }

        if ($request->filled('graduation_year')) {
            $query->where('graduation_year', $request->graduation_year);
        }

        $talents = $query->orderBy('full_name')->get();

        if ($request->filled('export') && $request->export === 'csv') {
            return $this->exportTalentCsv($talents);
        }

        return view('admin.reports.talent', compact('talents'));
    }

    public function companyReport(Request $request)
    {
        $currentYear = $request->input('fiscal_year', date('Y'));

        $companies = Company::withCount('placements')
            ->with(['placements' => function ($q) {
                $q->whereIn('placement_status', ['active', 'completed']);
            }])
            ->get()
            ->map(function ($company) use ($currentYear) {
                $company->budget_used = BudgetTransaction::where('company_id', $company->id)
                    ->where('status', 'approved')
                    ->whereYear('transaction_date', $currentYear)
                    ->sum('amount');
                $company->budget_allocated = BudgetAllocation::where('company_id', $company->id)
                    ->where('fiscal_year', $currentYear)
                    ->sum('allocated_amount');
                return $company;
            });

        if ($request->filled('export') && $request->export === 'csv') {
            return $this->exportCompanyCsv($companies, $currentYear);
        }

        return view('admin.reports.company', compact('companies', 'currentYear'));
    }

    public function budgetReport(Request $request)
    {
        $currentYear = $request->input('fiscal_year', date('Y'));

        $totalAllocated = BudgetAllocation::where('fiscal_year', $currentYear)->sum('allocated_amount');
        $totalSpent = BudgetTransaction::where('status', 'approved')
            ->whereYear('transaction_date', $currentYear)
            ->sum('amount');

        $byCategory = BudgetTransaction::select('category', DB::raw('SUM(amount) as total'))
            ->where('status', 'approved')
            ->whereYear('transaction_date', $currentYear)
            ->groupBy('category')
            ->get();

        $byMonth = BudgetTransaction::select(
                DB::raw('MONTH(transaction_date) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->where('status', 'approved')
            ->whereYear('transaction_date', $currentYear)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $transactions = BudgetTransaction::with(['talent', 'company'])
            ->where('status', 'approved')
            ->whereYear('transaction_date', $currentYear)
            ->orderByDesc('transaction_date')
            ->get();

        if ($request->filled('export') && $request->export === 'csv') {
            return $this->exportBudgetCsv($transactions, $currentYear);
        }

        return view('admin.reports.budget', compact(
            'currentYear', 'totalAllocated', 'totalSpent',
            'byCategory', 'byMonth', 'transactions'
        ));
    }

    public function placementReport(Request $request)
    {
        $query = Placement::with(['talent', 'company', 'batch', 'feedback']);

        if ($request->filled('status')) {
            $query->where('placement_status', $request->status);
        }

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->batch_id);
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $placements = $query->orderByDesc('start_date')->get();

        $batches = IntakeBatch::orderByDesc('year')->get();
        $companies = Company::orderBy('company_name')->get();

        if ($request->filled('export') && $request->export === 'csv') {
            return $this->exportPlacementCsv($placements);
        }

        return view('admin.reports.placement', compact('placements', 'batches', 'companies'));
    }

    private function exportTalentCsv($talents)
    {
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="talent_report.csv"'];

        $callback = function () use ($talents) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Kod Bakat', 'Nama Penuh', 'Universiti', 'Program', 'CGPA', 'Tahun Graduasi', 'Status', 'E-mel', 'Telefon']);

            foreach ($talents as $t) {
                fputcsv($handle, [
                    $t->talent_code, $t->full_name, $t->university, $t->programme,
                    $t->cgpa, $t->graduation_year, $t->resolved_status, $t->email, $t->phone
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportCompanyCsv($companies, $year)
    {
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="company_report.csv"'];

        $callback = function () use ($companies, $year) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Kod Syarikat', 'Nama Syarikat', 'Industri', 'Status Perjanjian', 'Jumlah Penempatan', 'Diperuntukkan', 'Dibelanjakan']);

            foreach ($companies as $c) {
                fputcsv($handle, [
                    $c->company_code, $c->company_name, $c->industry, $c->agreement_status,
                    $c->placements_count, $c->budget_allocated, $c->budget_used
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportBudgetCsv($transactions, $year)
    {
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="budget_report.csv"'];

        $callback = function () use ($transactions) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Tarikh', 'Rujukan', 'Kategori', 'Penerangan', 'Syarikat', 'Bakat', 'Jumlah (RM)', 'Status']);

            foreach ($transactions as $t) {
                fputcsv($handle, [
                    $t->transaction_date->format('d/m/Y'),
                    $t->reference_no,
                    $t->category,
                    $t->description,
                    $t->company?->company_name,
                    $t->talent?->full_name,
                    number_format($t->amount, 2),
                    $t->status
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportPlacementCsv($placements)
    {
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="placement_report.csv"'];

        $callback = function () use ($placements) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Bakat', 'Syarikat', 'Jabatan', 'Tarikh Mula', 'Tarikh Tamat', 'Elaun Bulanan', 'Status']);

            foreach ($placements as $p) {
                fputcsv($handle, [
                    $p->talent?->full_name,
                    $p->company?->company_name,
                    $p->department,
                    $p->start_date->format('d/m/Y'),
                    $p->end_date->format('d/m/Y'),
                    number_format($p->monthly_stipend, 2),
                    $p->placement_status
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
