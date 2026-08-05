<?php

namespace App\Providers;

use App\Models\AdPromotion;
use App\Models\Setting;
use App\Models\User;
use App\Policies\AdPromotionPolicy;
use App\Policies\BrandPolicy;
use App\Policies\CommentPolicy;
use App\Policies\CommunityGroupPolicy;
use App\Policies\ConversationPolicy;
use App\Policies\ListingPolicy;
use App\Policies\ListingReportPolicy;
use App\Policies\MotorcycleCategoryPolicy;
use App\Policies\MotorcyclePolicy;
use App\Policies\MotorcycleReviewPolicy;
use App\Policies\NewsCategoryPolicy;
use App\Policies\NewsPolicy;
use App\Policies\PartCategoryPolicy;
use App\Policies\PartPolicy;
use App\Policies\PostPolicy;
use App\Policies\ServiceCategoryPolicy;
use App\Policies\ServiceProviderPolicy;
use App\Policies\SettingPolicy;
use App\Policies\UserPolicy;
use App\Policies\VideoCategoryPolicy;
use App\Policies\VideoPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Brand\Models\Brand;
use Modules\Community\Models\Comment;
use Modules\Community\Models\CommunityGroup;
use Modules\Community\Models\Post;
use Modules\Market\Models\Conversation;
use Modules\Market\Models\Listing;
use Modules\Market\Models\ListingReport;
use Modules\Motorcycle\Models\Motorcycle;
use Modules\Motorcycle\Models\MotorcycleCategory;
use Modules\News\Models\News;
use Modules\News\Models\NewsCategory;
use Modules\Parts\Models\Part;
use Modules\Parts\Models\PartCategory;
use Modules\Review\Models\MotorcycleReview;
use Modules\ServiceCenter\Models\ServiceCategory;
use Modules\ServiceCenter\Models\ServiceProvider as ServiceProviderModel;
use Modules\Video\Models\Video;
use Modules\Video\Models\VideoCategory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Policies are mapped explicitly (rather than relying on Laravel's naming
     * convention discovery) because the models live under Modules\* namespaces.
     *
     * @var array<class-string, class-string>
     */
    protected array $policies = [
        User::class => UserPolicy::class,
        Listing::class => ListingPolicy::class,
        ListingReport::class => ListingReportPolicy::class,
        Conversation::class => ConversationPolicy::class,
        Part::class => PartPolicy::class,
        PartCategory::class => PartCategoryPolicy::class,
        Motorcycle::class => MotorcyclePolicy::class,
        MotorcycleCategory::class => MotorcycleCategoryPolicy::class,
        Brand::class => BrandPolicy::class,
        Video::class => VideoPolicy::class,
        VideoCategory::class => VideoCategoryPolicy::class,
        News::class => NewsPolicy::class,
        NewsCategory::class => NewsCategoryPolicy::class,
        ServiceProviderModel::class => ServiceProviderPolicy::class,
        ServiceCategory::class => ServiceCategoryPolicy::class,
        Post::class => PostPolicy::class,
        Comment::class => CommentPolicy::class,
        CommunityGroup::class => CommunityGroupPolicy::class,
        MotorcycleReview::class => MotorcycleReviewPolicy::class,
        AdPromotion::class => AdPromotionPolicy::class,
        Setting::class => SettingPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }

        // Administrators hold every permission via seeding already; this is a
        // safety net so a permission added later doesn't need a reseed to be
        // usable by administrators.
        Gate::before(fn (User $user, string $ability) => $user->hasRole('administrator') ? true : null);
    }
}
