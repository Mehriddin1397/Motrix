<?php

namespace Tests\Feature;

use App\Services\TelegramErrorNotifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class TelegramErrorNotifierTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_telegram_notification_on_exception(): void
    {
        Config::set('services.telegram.bot_token', 'test_token');
        Config::set('services.telegram.admin_chat_id', '12345678');

        Http::fake([
            'api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);

        $exception = new \RuntimeException('Database connection lost test error');
        $result = TelegramErrorNotifier::notify($exception);

        $this->assertTrue($result);
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'sendMessage')
                && str_contains($request['text'], 'Database connection lost test error');
        });
    }

    public function test_it_throttles_identical_errors(): void
    {
        Config::set('services.telegram.bot_token', 'test_token');
        Config::set('services.telegram.admin_chat_id', '12345678');

        Http::fake([
            'api.telegram.org/*' => Http::response(['ok' => true], 200),
        ]);

        $exception = new \RuntimeException('Throttled duplicate error');
        $first = TelegramErrorNotifier::notify($exception);
        $second = TelegramErrorNotifier::notify($exception);

        $this->assertTrue($first);
        $this->assertFalse($second);
        Http::assertSentCount(1);
    }

    public function test_it_ignores_404_not_found_exceptions(): void
    {
        Config::set('services.telegram.bot_token', 'test_token');
        Config::set('services.telegram.admin_chat_id', '12345678');

        Http::fake();

        $exception = new NotFoundHttpException('Page not found');
        $result = TelegramErrorNotifier::notify($exception);

        $this->assertFalse($result);
        Http::assertNothingSent();
    }
}
