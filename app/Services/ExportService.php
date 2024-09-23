<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class ExportService
{
    public function downloadExcel($headings, $transactions)
    {
        $exportMsUrl = env('EXPORT_MS_URL', 'http://localhost:8002');

        try {
            $response = Http::post("$exportMsUrl/api/download-excel", [
                'headings' => $headings,
                'data' => $transactions
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return ['error' => 'Download failed', 'message' => $response->json()];

        } catch (\Exception $e) {
            return ['error' => 'Download Exception failed', 'message' => $e->getMessage()];
        }
    }
}
