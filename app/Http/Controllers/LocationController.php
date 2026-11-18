<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LocationController extends Controller
{
    /**
     * Return approximate latitude and longitude based on client IP or defaults.
     */
    public function approximate(Request $request): JsonResponse
    {
        $default = [
            'lat' => 41.311081,
            'lng' => 69.240562,
            'city' => 'Toshkent',
            'is_fallback' => true,
        ];

        $ip = $request->ip();

        // Local / private networks fallback to default coordinates (Tashkent center)
        if (
            ! $ip ||
            $ip === '127.0.0.1' ||
            str_starts_with($ip, '192.168.') ||
            str_starts_with($ip, '10.') ||
            str_starts_with($ip, '172.16.') ||
            str_starts_with($ip, 'fe80:') ||
            $ip === '::1'
        ) {
            return response()->json($default);
        }

        try {
            $response = Http::timeout(2)->get("http://ip-api.com/json/{$ip}?fields=status,message,country,city,lat,lon");
            if ($response->successful() && $response->json('status') === 'success') {
                return response()->json([
                    'lat' => (float) $response->json('lat'),
                    'lng' => (float) $response->json('lon'),
                    'city' => $response->json('city', 'Toshkent'),
                    'is_fallback' => false,
                ]);
            }
        } catch (\Throwable $e) {
            // fallback gracefully
        }

        return response()->json($default);
    }
}
