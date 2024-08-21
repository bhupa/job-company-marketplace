<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\Translation\TranslationStoreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TranslationController extends BaseController
{
    public function store(TranslationStoreRequest $request)
    {
        try {
            $sourceText = $request->description;
            $sourceLang = $request->request_source;
            $targetLang = $request->request_target;

            // Build the URL for Google Translate API
            $url = 'https://translation.googleapis.com/language/translate/v2';
            $queryParams = [
                'q' => $sourceText,
                'source' => $sourceLang,
                'target' => $targetLang,
                'key' => env('GOOGLE_TRANSLATE_API_KEY'), // Replace with your actual API key
            ];
            // Send GET request to Google Translate API
            $response = Http::get($url, $queryParams);

            // Check if request was successful
            if ($response->successful()) {
                $translatedText =
                    $response->json()['data']['translations'][0][
                        'translatedText'
                    ] ?? null;
                if ($translatedText) {
                    return $this->success(
                        $translatedText,
                        'Translate successfully'
                    );
                } else {
                    return $this->success([], 'Translation not found');
                }
            } else {
                return $this->errors(['message' => $response->status()], 400);
            }
        } catch (\Throwable $th) {
            return $this->errors(['message' => $th->getMessage()], 400);
        }
    }
}
