<?php

namespace App\Concerns;

use App\Models\User;

/**
 * Applied to any moderatable listing model (Listing, Part) to decide whether a
 * new submission goes straight to "active"/"published" or is queued for
 * moderator review, based on the owning seller's SellerTrustProfile.
 *
 * The owner is passed explicitly rather than resolved through a model
 * relation, since Listing (user_id) and Part (seller_id) name that
 * relationship differently.
 */
trait HasApprovalWorkflow
{
    public static function determineInitialStatus(User $owner): string
    {
        $trust = $owner->trustProfile()->firstOrCreate([], ['status' => 'new']);

        return $trust->isTrusted() && ! $trust->isRestricted() ? 'active' : 'pending';
    }

    public function recordApprovalFor(User $owner): void
    {
        $trust = $owner->trustProfile()->firstOrCreate([], ['status' => 'new']);

        if ($trust->isRestricted()) {
            return;
        }

        $trust->increment('approved_listings_count');

        if (! $trust->isTrusted()
            && $trust->fresh()->approved_listings_count >= config('access.trust.listings_required_for_trust')) {
            $trust->update(['status' => 'trusted', 'trusted_at' => now()]);
        }
    }

    public function recordViolationFor(User $owner): void
    {
        $trust = $owner->trustProfile()->firstOrCreate([], ['status' => 'new']);

        $trust->increment('violations_count');
        $trust->update(['last_violation_at' => now()]);

        if ($trust->fresh()->violations_count >= config('access.trust.violations_before_restriction')) {
            $trust->update(['status' => 'restricted', 'restricted_at' => now()]);
        }
    }
}
