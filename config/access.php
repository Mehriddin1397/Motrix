<?php

/**
 * Single source of truth for the platform's role & permission system.
 *
 * Adding a new role or permission is a two-step change:
 *   1. Declare it here (roles / permissions / role_permissions).
 *   2. Run `php artisan db:seed --class="Database\Seeders\RolesAndPermissionsSeeder"`.
 *
 * All authorization in the codebase (policies, middleware, Blade @can) checks
 * permissions, never role names directly - roles are just named bundles of
 * permissions, and a user may hold several roles at once.
 */

return [

    'guard' => 'web',

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    |
    | slug => display label. A user can be assigned any combination of these
    | (e.g. "user" + "moto-seller"). New roles just get a new entry here.
    |
    */
    'roles' => [
        'administrator' => 'Administrator',
        'moderator' => 'Moderator',
        'moto-seller' => 'Moto sotuvchi',
        'parts-seller' => 'Ehtiyot qismlar sotuvchisi',
        'user' => 'Oddiy foydalanuvchi',
    ],

    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    |
    | Grouped purely for readability - the group key has no functional effect,
    | permissions are flattened before being seeded.
    |
    */
    'permissions' => [

        'platform' => [
            'platform.settings.manage',
            'platform.statistics.view',
            'platform.payments.view',
            'platform.access-admin',
            'promotions.manage',
        ],

        'users' => [
            'users.viewAny',
            'users.manage',
            'users.roles.manage',
        ],

        'motorcycle-encyclopedia' => [
            'motorcycles.brands.manage',
            'motorcycles.categories.manage',
            'motorcycles.models.manage',
            'motorcycles.videos.manage',
        ],

        'content' => [
            'news.manage',
            'service-centers.manage',
            'parts-categories.manage',
        ],

        'moderation' => [
            'listings.moderate',
            'parts.moderate',
            'reviews.moderate',
            'community.moderate',
            'reports.view',
        ],

        'listings' => [
            'listings.create',
            'listings.update',
            'listings.delete',
            'listings.publish',
            'listings.promote',
        ],

        'parts' => [
            'parts.create',
            'parts.update',
            'parts.delete',
            'parts.manageStock',
            'parts.promote',
            'parts.orders.manage',
        ],

        'community' => [
            'profile.manage',
            'motorcycles.save',
            'motorcycles.compare',
            'ai-assistant.use',
            'reviews.create',
            'community.post',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Role -> permission map
    |--------------------------------------------------------------------------
    |
    | "administrator" is synced with every permission declared above at seed
    | time. A Gate::before() safety net (see AppServiceProvider) also grants
    | administrators any permission added later without needing a reseed.
    |
    */
    'role_permissions' => [
        'administrator' => ['*'],

        'moderator' => [
            'platform.access-admin',
            'listings.moderate',
            'parts.moderate',
            'reviews.moderate',
            'community.moderate',
            'reports.view',
            'users.viewAny',
        ],

        'moto-seller' => [
            'listings.create',
            'listings.update',
            'listings.delete',
            'listings.publish',
            'listings.promote',
        ],

        'parts-seller' => [
            'parts.create',
            'parts.update',
            'parts.delete',
            'parts.manageStock',
            'parts.promote',
            'parts.orders.manage',
        ],

        'user' => [
            'profile.manage',
            'motorcycles.save',
            'motorcycles.compare',
            'ai-assistant.use',
            'reviews.create',
            'community.post',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Listing/part approval workflow
    |--------------------------------------------------------------------------
    */
    'trust' => [
        // Approved listings/parts a "new" seller needs before becoming "trusted"
        // and having their submissions auto-published instead of queued.
        'listings_required_for_trust' => 5,

        // Violations (reports upheld, rule-breaking content, etc.) a seller can
        // accumulate before being pushed back into moderator review.
        'violations_before_restriction' => 3,
    ],

    /*
    |--------------------------------------------------------------------------
    | Ad promotion tiers
    |--------------------------------------------------------------------------
    |
    | Purely descriptive config today (price/duration are placeholders until a
    | payment gateway is wired up) - AdPromotion rows reference the tier key.
    |
    */
    'promotions' => [
        'tiers' => [
            'standard' => [
                'label' => 'Oddiy',
                'price' => 0,
                'duration_days' => null,
                'weight' => 0,
                'homepage' => false,
                'badge' => false,
            ],
            'premium' => [
                'label' => 'Premium',
                'price' => 50000,
                'duration_days' => 30,
                'weight' => 10,
                'homepage' => false,
                'badge' => false,
            ],
            'top' => [
                'label' => 'TOP',
                'price' => 120000,
                'duration_days' => 30,
                'weight' => 20,
                'homepage' => true,
                'badge' => false,
            ],
            'vip' => [
                'label' => 'VIP',
                'price' => 250000,
                'duration_days' => 30,
                'weight' => 30,
                'homepage' => true,
                'badge' => true,
            ],
        ],
    ],

];
