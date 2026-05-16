<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class MembershipAccess
{
    public static function hasActivePaidMembership(User $user): bool
    {
        $table = Schema::hasTable('suscripciones')
            ? 'suscripciones'
            : (Schema::hasTable('suscripcions') ? 'suscripcions' : null);

        if ($table === null) {
            return false;
        }

        try {
            return DB::table($table)
                ->where('user_id', $user->id)
                ->whereIn('estatus', ['activa', 'activo', 'active', 'paid'])
                ->whereIn('plan', ['mensual', 'anual_promo', 'anual', 'pro', 'despacho'])
                ->where(function ($query): void {
                    $query->whereNull('periodo_fin')
                        ->orWhere('periodo_fin', '>', now());
                })
                ->exists();
        } catch (Throwable) {
            return false;
        }
    }

    public static function canUseMultipleRfcs(User $user): bool
    {
        $profile = $user->loadMissing('profile')->profile;

        return in_array($profile?->user_type, ['Contador independiente', 'Despacho contable'], true)
            && self::hasActivePaidMembership($user);
    }

    public static function isOwnProfileRfc(User $user, string $rfc): bool
    {
        $profileRfc = (string) $user->loadMissing('profile')->profile?->rfc;

        return mb_strtoupper($profileRfc, 'UTF-8') === mb_strtoupper($rfc, 'UTF-8');
    }
}
