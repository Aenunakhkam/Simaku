<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $activeYearId = $request->session()->get('academic_year_id');
        $activeYear = null;
        if ($activeYearId) {
            $activeYear = \App\Models\AcademicYear::find($activeYearId);
        } else {
            $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'status' => fn () => $request->session()->get('status'),
                'success' => fn () => $request->session()->get('success') ?? $request->session()->get('message'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'active_academic_year' => $activeYear,
        ];
    }
}
