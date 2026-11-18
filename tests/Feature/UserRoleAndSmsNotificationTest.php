<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Country;
use App\Models\ShopRequest;
use App\Models\User;
use App\Services\EskizSmsService;
use Filament\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UserRoleAndSmsNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
        Country::firstOrCreate(['code' => 'UZ'], ['name' => 'Uzbekistan']);
    }

    public function test_admin_can_assign_administrator_role_to_user(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('administrator');

        $user = User::factory()->create();
        $user->assignRole('user');

        $this->assertFalse($user->hasRole('administrator'));
        $this->assertFalse($user->can('platform.access-admin'));

        // Admin assigns administrator role
        $user->syncRoles(['administrator']);

        $user->refresh();
        $this->assertTrue($user->hasRole('administrator'));
        $this->assertTrue($user->can('platform.access-admin'));
        $this->assertTrue($user->can('users.manage'));
    }

    public function test_submitting_shop_request_sends_sms_to_admin(): void
    {
        Http::fake([
            'https://notify.eskiz.uz/api/auth/login' => Http::response(['data' => ['token' => 'fake-test-token']], 200),
            'https://notify.eskiz.uz/api/message/sms/send' => Http::response(['status' => 'waiting', 'message' => 'Waiting for SMS delivery'], 200),
        ]);

        config([
            'services.eskiz.email' => 'test@eskiz.uz',
            'services.eskiz.password' => 'secret',
            'services.eskiz.admin_phone' => '+998942551397',
        ]);

        $user = User::factory()->create(['phone' => '+998901112233']);
        $city = City::create([
            'name' => 'Toshkent',
            'country_id' => Country::first()->id,
            'slug' => 'toshkent',
        ]);

        $response = $this->actingAs($user)->post(route('shop-requests.store'), [
            'shop_name' => 'Samarkand Moto Shop',
            'shop_type' => 'both',
            'city_id' => $city->id,
            'phone' => '+998901112233',
            'address' => 'Amir Temur ko\'chasi 10',
            'message' => 'Yangi filial ochmoqchimiz.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('shop_requests', [
            'shop_name' => 'Samarkand Moto Shop',
            'status' => 'pending',
        ]);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/api/message/sms/send') &&
                   $request['mobile_phone'] === '998942551397' &&
                   $request['message'] === "Motrix: Yangi do'kon arizasi qabul qilindi. Do'kon: Samarkand Moto Shop. Admin panel orqali tanishing.";
        });
    }

    public function test_approving_shop_request_sends_sms_to_applicant(): void
    {
        Http::fake([
            'https://notify.eskiz.uz/api/auth/login' => Http::response(['data' => ['token' => 'fake-test-token']], 200),
            'https://notify.eskiz.uz/api/message/sms/send' => Http::response(['status' => 'waiting', 'message' => 'Waiting for SMS delivery'], 200),
        ]);

        config([
            'services.eskiz.email' => 'test@eskiz.uz',
            'services.eskiz.password' => 'secret',
        ]);

        $admin = User::factory()->create();
        $admin->assignRole('administrator');

        $applicant = User::factory()->create(['phone' => '+998935557788']);
        $city = City::create([
            'name' => 'Samarqand',
            'country_id' => Country::first()->id,
            'slug' => 'samarqand',
        ]);

        $shopRequest = ShopRequest::create([
            'user_id' => $applicant->id,
            'shop_name' => 'Registon Moto',
            'shop_type' => 'moto',
            'city_id' => $city->id,
            'phone' => '+998935557788',
            'status' => 'pending',
        ]);

        $shopRequest->approve($admin);

        $shopRequest->refresh();
        $this->assertEquals('approved', $shopRequest->status);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/api/message/sms/send') &&
                   $request['mobile_phone'] === '998935557788' &&
                   str_contains($request['message'], 'Registon Moto') &&
                   str_contains($request['message'], 'qabul qilindi');
        });
    }

    public function test_eskiz_sms_service_cleans_various_phone_formats(): void
    {
        Http::fake([
            'https://notify.eskiz.uz/api/auth/login' => Http::response(['data' => ['token' => 'fake-token']], 200),
            'https://notify.eskiz.uz/api/message/sms/send' => Http::response(['status' => 'waiting'], 200),
        ]);

        config([
            'services.eskiz.email' => 'test@eskiz.uz',
            'services.eskiz.password' => 'secret',
        ]);

        $service = app(EskizSmsService::class);

        // 9-digit format
        $service->send('942551397', 'Test 1');
        // formatted format with spaces and dashes
        $service->send('+998 (94) 255-13-97', 'Test 2');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'sms/send') &&
                   $request['mobile_phone'] === '998942551397' &&
                   $request['message'] === 'Test 1';
        });

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'sms/send') &&
                   $request['mobile_phone'] === '998942551397' &&
                   $request['message'] === 'Test 2';
        });
    }
}
