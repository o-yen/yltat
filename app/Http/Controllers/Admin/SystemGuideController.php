<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class SystemGuideController extends Controller
{
    private function modules(): array
    {
        return [
            ['key' => 'dashboard',           'name_key' => 'nav.dashboard',           'desc_key' => 'guide.desc_dashboard',           'route' => 'admin.dashboard',            'icon' => 'home',       'category' => 'management',  'guide_ready' => true],
            ['key' => 'graduan',             'name_key' => 'nav.graduan',             'desc_key' => 'guide.desc_graduan',             'route' => 'admin.talents.index',        'icon' => 'users',      'category' => 'management',  'guide_ready' => true],
            ['key' => 'applications',        'name_key' => 'nav.applications',        'desc_key' => 'guide.desc_applications',        'route' => 'admin.applications.index',   'icon' => 'clipboard',  'category' => 'management',  'guide_ready' => true],
            ['key' => 'syarikat_pelaksana',  'name_key' => 'nav.syarikat_pelaksana',  'desc_key' => 'guide.desc_syarikat_pelaksana',  'route' => 'admin.syarikat-pelaksana.index', 'icon' => 'building', 'category' => 'management', 'guide_ready' => true],
            ['key' => 'syarikat_penempatan', 'name_key' => 'nav.syarikat_penempatan', 'desc_key' => 'guide.desc_syarikat_penempatan', 'route' => 'admin.syarikat-penempatan.index', 'icon' => 'office', 'category' => 'management', 'guide_ready' => true],
            ['key' => 'kehadiran',    'name_key' => 'nav.kehadiran',    'desc_key' => 'guide.desc_kehadiran',    'route' => 'admin.kehadiran.index',    'icon' => 'check',     'category' => 'monitoring',  'guide_ready' => true],
            ['key' => 'daily_logs',   'name_key' => 'nav.daily_logs',   'desc_key' => 'guide.desc_daily_logs',   'route' => 'admin.daily-logs.index',   'icon' => 'pencil',    'category' => 'monitoring',  'guide_ready' => true],
            ['key' => 'logbook',      'name_key' => 'nav.logbook',      'desc_key' => 'guide.desc_logbook',      'route' => 'admin.logbook.index',      'icon' => 'book',      'category' => 'monitoring',  'guide_ready' => true],
            ['key' => 'training',     'name_key' => 'nav.training',     'desc_key' => 'guide.desc_training',     'route' => 'admin.training.index',     'icon' => 'academic',  'category' => 'monitoring',  'guide_ready' => true],
            ['key' => 'isu_risiko',   'name_key' => 'nav.isu_risiko',   'desc_key' => 'guide.desc_isu_risiko',   'route' => 'admin.isu-risiko.index',   'icon' => 'warning',   'category' => 'monitoring',  'guide_ready' => true],
            ['key' => 'status_surat', 'name_key' => 'nav.status_surat', 'desc_key' => 'guide.desc_status_surat', 'route' => 'admin.status-surat.index', 'icon' => 'mail',      'category' => 'monitoring',  'guide_ready' => true],
            ['key' => 'applicant_requests', 'name_key' => 'Applicant Request', 'desc_key' => 'guide.desc_applicant_requests', 'route' => 'admin.applicant-requests.index', 'icon' => 'chat', 'category' => 'management', 'guide_ready' => true],
            ['key' => 'kewangan', 'name_key' => 'nav.kewangan', 'desc_key' => 'guide.desc_kewangan', 'route' => 'admin.kewangan.index', 'icon' => 'currency', 'category' => 'finance', 'guide_ready' => true],
            ['key' => 'budget',   'name_key' => 'nav.budget',   'desc_key' => 'guide.desc_budget',   'route' => 'admin.budget.index',   'icon' => 'chart',    'category' => 'finance', 'guide_ready' => true],
            ['key' => 'kpi_dashboard', 'name_key' => 'nav.kpi_dashboard', 'desc_key' => 'guide.desc_kpi_dashboard', 'route' => 'admin.kpi-dashboard.index', 'icon' => 'trending', 'category' => 'analytics', 'guide_ready' => true],
            ['key' => 'reports',       'name_key' => 'nav.reports',       'desc_key' => 'guide.desc_reports',       'route' => 'admin.reports.index',       'icon' => 'document', 'category' => 'analytics', 'guide_ready' => true],
            ['key' => 'feedback',      'name_key' => 'nav.feedback',      'desc_key' => 'guide.desc_feedback',      'route' => 'admin.feedback.index',      'icon' => 'chat',     'category' => 'analytics', 'guide_ready' => true],
        ];
    }

    private function howtos(): array
    {
        return [
            ['key' => 'profile_password',  'title_key' => 'howto.profile_password_title',  'desc_key' => 'howto.profile_password_desc',  'icon' => 'user-cog'],
            ['key' => 'manage_talent',     'title_key' => 'howto.manage_talent_title',     'desc_key' => 'howto.manage_talent_desc',     'icon' => 'user-add'],
            ['key' => 'manage_pelaksana',  'title_key' => 'howto.manage_pelaksana_title',  'desc_key' => 'howto.manage_pelaksana_desc',  'icon' => 'building'],
            ['key' => 'manage_penempatan', 'title_key' => 'howto.manage_penempatan_title', 'desc_key' => 'howto.manage_penempatan_desc', 'icon' => 'office'],
            ['key' => 'budget_allocation', 'title_key' => 'howto.budget_allocation_title', 'desc_key' => 'howto.budget_allocation_desc', 'icon' => 'calculator'],
            ['key' => 'kpi_explained',     'title_key' => 'howto.kpi_explained_title',     'desc_key' => 'howto.kpi_explained_desc',     'icon' => 'chart-bar'],
        ];
    }

    public function index()
    {
        $modules   = $this->modules();
        $howtos    = $this->howtos();
        $doneCount = collect($modules)->where('guide_ready', true)->count();

        return view('admin.system-guide.index', compact('modules', 'doneCount', 'howtos'));
    }

    public function show(string $module)
    {
        $modules = $this->modules();
        $mod     = collect($modules)->firstWhere('key', $module);

        if (!$mod || !$mod['guide_ready']) {
            return redirect()->route('admin.system-guide.index');
        }

        return view("admin.system-guide.modules.{$module}", [
            'mod'     => $mod,
            'modules' => $modules,
        ]);
    }

    public function howto(string $key)
    {
        $howtos = $this->howtos();
        $howto  = collect($howtos)->firstWhere('key', $key);

        if (!$howto) {
            return redirect()->route('admin.system-guide.index');
        }

        return view("admin.system-guide.howto.{$key}", ['howto' => $howto]);
    }
}
