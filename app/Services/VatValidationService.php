<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class VatValidationService
{
    public function validarNifComVies(string $nif, string $countryCode = 'PT'): bool
    {
        $response = Http::get('https://ec.europa.eu/taxation_customs/vies/rest-api/check-vat-number', [
            'countryCode' => strtoupper($countryCode),
            'vatNumber' => $nif,
        ]);

        if ($response->successful()) {
            return $response->json()['isValid'] ?? false;
        }

        return false;
    }
}
