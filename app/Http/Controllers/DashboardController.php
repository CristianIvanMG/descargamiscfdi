<?php

namespace App\Http\Controllers;

use App\Support\MembershipAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController
{
    public function __invoke(Request $request): View
    {
        $user = $request->user()->loadMissing('profile');
        $profile = $user->profile;
        $periodStart = now()->startOfMonth();

        return view('dashboard.index', [
            'profile' => $profile,
            'periodLabel' => $periodStart->translatedFormat('F Y'),
            'canUseMultipleRfcs' => MembershipAccess::canUseMultipleRfcs($user),
        ]);
    }
}
