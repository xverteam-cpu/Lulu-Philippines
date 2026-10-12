<?php

namespace App\Http\Controllers;

use App\Support\InvestorMarketQuoteService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class InvestorMarketQuoteController extends Controller
{
    public function __invoke(): JsonResponse
    {
        try {
            return response()->json(InvestorMarketQuoteService::latest());
        } catch (ConnectionException|RequestException|RuntimeException $exception) {
            Log::warning('Unable to load the Lulu Retail market quote.', [
                'message' => $exception->getMessage(),
            ]);

            return response()->json(['message' => 'Market quote is currently unavailable.'], 503);
        }
    }
}
