<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Module Access Middleware
 *
 * Usage:  ->middleware('module:module_name')          — any access level
 *         ->middleware('module:module_name,write')    — requires edit/own/create/full
 *         ->middleware('module:module_name,full')     — requires full only
 *
 * Access levels: full | view | edit | own | create | null (no access)
 */
class ModuleAccess
{
    /**
     * Module → role → access level
     * null means no access to that module.
     */
    public const MATRIX = [
        'talents' => [
            'super_admin'        => 'full',
            'pmo_admin'          => 'full',
            'mindef_viewer'      => 'view',
            'syarikat_pelaksana' => 'view',   // own graduates only (scoped in controller)
            'rakan_kolaborasi'   => null,
        ],
        'applications' => [
            'super_admin' => 'full',
            'pmo_admin'   => 'full',
        ],
        'syarikat_pelaksana' => [
            'super_admin'        => 'full',
            'pmo_admin'          => 'full',
            'mindef_viewer'      => 'view',
            'syarikat_pelaksana' => 'own',    // own company record only
        ],
        'syarikat_penempatan' => [
            'super_admin'      => 'full',
            'pmo_admin'        => 'full',
            'mindef_viewer'    => 'view',
            'rakan_kolaborasi' => 'own',      // own company record only
        ],
        'kehadiran' => [
            'super_admin'      => 'full',
            'pmo_admin'        => 'full',
            'mindef_viewer'    => 'view',
            'rakan_kolaborasi' => 'edit',     // own company graduates only (scoped in controller)
        ],
        'daily_logs' => [
            'super_admin'   => 'full',
            'pmo_admin'     => 'full',
            'mindef_viewer' => 'view',
        ],
        'logbook' => [
            'super_admin'        => 'full',
            'pmo_admin'          => 'full',
            'mindef_viewer'      => 'view',
            'syarikat_pelaksana' => 'edit',   // own company graduates only (scoped in controller)
            'rakan_kolaborasi'   => 'edit',   // own company graduates only (scoped in controller)
        ],
        'training' => [
            'super_admin'      => 'full',
            'pmo_admin'        => 'full',
            'mindef_viewer'    => 'view',
            'rakan_kolaborasi' => 'edit',     // own company graduates only
        ],
        'isu_risiko' => [
            'super_admin'   => 'full',
            'pmo_admin'     => 'full',
            'mindef_viewer' => 'view',
        ],
        'status_surat' => [
            'super_admin'        => 'full',
            'pmo_admin'          => 'full',
            'mindef_viewer'      => 'view',
            'syarikat_pelaksana' => 'edit',   // own graduates only (scoped in controller)
        ],
        'kewangan' => [
            'super_admin'        => 'full',
            'pmo_admin'          => 'full',
            'mindef_viewer'      => 'view',
            'syarikat_pelaksana' => 'edit',   // own graduates only (scoped in controller)
        ],
        'budget' => [
            'super_admin'   => 'full',
            'pmo_admin'     => 'full',
            'mindef_viewer' => 'view',
        ],
        'kpi' => [
            'super_admin'   => 'full',
            'pmo_admin'     => 'full',
            'mindef_viewer' => 'view',
        ],
        'reports' => [
            'super_admin'   => 'full',
            'pmo_admin'     => 'full',
            'mindef_viewer' => 'view',
        ],
        'feedback' => [
            'super_admin'   => 'full',
            'pmo_admin'     => 'full',
            'mindef_viewer' => 'view',
        ],
        'settings' => [
            'super_admin' => 'full',
            'pmo_admin'   => 'full',
        ],
        'placements' => [
            'super_admin'        => 'full',
            'pmo_admin'          => 'full',
            'mindef_viewer'      => 'view',
            'syarikat_pelaksana' => 'edit',
            'rakan_kolaborasi'   => 'view',
        ],
    ];

    /** Levels that permit write (create/edit/delete) operations */
    private const WRITE_LEVELS = ['full', 'edit', 'own', 'create'];

    public function handle(Request $request, Closure $next, string $module, string $require = 'any'): Response
    {
        $role  = auth()->user()?->role?->role_name;
        $level = self::MATRIX[$module][$role] ?? null;

        if ($level === null) {
            abort(403, 'Akses tidak dibenarkan.');
        }

        if ($require === 'write' && !in_array($level, self::WRITE_LEVELS)) {
            abort(403, 'Akses tulis tidak dibenarkan.');
        }

        if ($require === 'full' && $level !== 'full') {
            abort(403, 'Akses penuh tidak dibenarkan.');
        }

        // Expose access level to controllers via request attributes
        $request->attributes->set('module_access', $level);

        return $next($request);
    }

    /** Convenience: get the access level for a given role+module pair */
    public static function levelFor(string $roleName, string $module): ?string
    {
        return self::MATRIX[$module][$roleName] ?? null;
    }
}
