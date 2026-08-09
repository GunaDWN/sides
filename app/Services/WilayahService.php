<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WilayahService
{
    protected string $baseUrl = 'https://emsifa.github.io/api-wilayah-indonesia/api';

    /**
     * Get list of provinces.
     */
    public function getProvinces(): array
    {
        return Cache::remember('wilayah_provinces', 86400, function () {
            try {
                $response = Http::timeout(5)->get("{$this->baseUrl}/provinces.json");
                if ($response->successful()) {
                    return array_map(function ($item) {
                        return [
                            'id' => (string) $item['id'],
                            'nama' => ucwords(strtolower($item['name'])),
                        ];
                    }, $response->json());
                }
            } catch (\Throwable $e) {
                // Fallback
            }
            return [];
        });
    }

    /**
     * Get list of regencies (Kabupaten/Kota) by province ID.
     */
    public function getRegencies(string $provinceId): array
    {
        if (!$provinceId) {
            return [];
        }

        return Cache::remember("wilayah_regencies_{$provinceId}", 86400, function () use ($provinceId) {
            try {
                $response = Http::timeout(5)->get("{$this->baseUrl}/regencies/{$provinceId}.json");
                if ($response->successful()) {
                    return array_map(function ($item) {
                        return [
                            'id' => (string) $item['id'],
                            'nama' => ucwords(strtolower($item['name'])),
                        ];
                    }, $response->json());
                }
            } catch (\Throwable $e) {
                // Fallback
            }
            return [];
        });
    }

    /**
     * Get list of districts (Kecamatan) by regency ID.
     */
    public function getDistricts(string $regencyId): array
    {
        if (!$regencyId) {
            return [];
        }

        return Cache::remember("wilayah_districts_{$regencyId}", 86400, function () use ($regencyId) {
            try {
                $response = Http::timeout(5)->get("{$this->baseUrl}/districts/{$regencyId}.json");
                if ($response->successful()) {
                    return array_map(function ($item) {
                        return [
                            'id' => (string) $item['id'],
                            'nama' => ucwords(strtolower($item['name'])),
                        ];
                    }, $response->json());
                }
            } catch (\Throwable $e) {
                // Fallback
            }
            return [];
        });
    }
}
