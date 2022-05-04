<?php

use App\Http\Controllers\NewsletterController;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

Route::any('/', function () {
    return "Welcome to " . env('APP_NAME') . " Backend";
});

Route::post('newsletter_subscription', NewsletterController::class); // Single Action Controller Route Registration

//Fallback - Unprotected
Route::fallback(function(){
    return response()->json(['message' => 'Endpoint not found in this project'], ResponseAlias::HTTP_NOT_FOUND);
});
