<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_login_and_register_screens_render_google_button(): void
    {
        $loginRes = $this->get(route('login'));
        $loginRes->assertOk();
        $loginRes->assertSee(route('auth.google'));
        $loginRes->assertSee('Google orqali kirish');

        $registerRes = $this->get(route('register'));
        $registerRes->assertOk();
        $registerRes->assertSee(route('auth.google'));
        $registerRes->assertSee('Google orqali ro\'yxatdan o\'tish', false);
    }

    public function test_google_redirect_warns_when_not_configured(): void
    {
        config(['services.google.client_id' => '']);
        config(['services.google.client_secret' => '']);

        $response = $this->get(route('auth.google'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
    }

    public function test_google_redirects_to_provider_when_configured(): void
    {
        config(['services.google.client_id' => 'fake-client-id']);
        config(['services.google.client_secret' => 'fake-client-secret']);

        $response = $this->get(route('auth.google'));

        $this->assertTrue($response->isRedirection());
        $this->assertStringContainsString('accounts.google.com', $response->getTargetUrl());
    }

    public function test_google_callback_creates_new_user_and_logs_in(): void
    {
        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getId')->andReturn('google-unique-12345');
        $googleUser->shouldReceive('getName')->andReturn('Jasur Toshmatov');
        $googleUser->shouldReceive('getEmail')->andReturn('jasur@example.com');
        $googleUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/a/avatar123');

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();

        $user = User::where('email', 'jasur@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('google-unique-12345', $user->google_id);
        $this->assertSame('Jasur Toshmatov', $user->name);
        $this->assertSame('https://lh3.googleusercontent.com/a/avatar123', $user->avatar);
        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue($user->hasRole('user'));
    }

    public function test_google_callback_links_existing_user_by_email(): void
    {
        $existing = User::factory()->create([
            'email' => 'existing@example.com',
            'google_id' => null,
            'avatar' => null,
        ]);

        $googleUser = Mockery::mock(SocialiteUser::class);
        $googleUser->shouldReceive('getId')->andReturn('google-id-existing-999');
        $googleUser->shouldReceive('getName')->andReturn('Existing User');
        $googleUser->shouldReceive('getEmail')->andReturn('existing@example.com');
        $googleUser->shouldReceive('getAvatar')->andReturn('https://google.com/avatar.jpg');

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($existing);

        $existing->refresh();
        $this->assertSame('google-id-existing-999', $existing->google_id);
        $this->assertSame('https://google.com/avatar.jpg', $existing->avatar);
    }
}
