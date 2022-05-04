<?php

namespace App\Http\Controllers;


use App\Services\Newsletter\Interfaces\NewsletterInterface;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class NewsletterController extends Controller
{
    // Single Action Controller Method
    public function __invoke(NewsletterInterface $newsletter)
    {
        request()->validate([
            'email' => ['required', 'email'],
            'list' => ['nullable', 'string'],
        ]);

        try {
            $list ??= request()->list;
            $newsletter->subscribe(request()->email);
        } catch (\Exception $e) {
            return response()->json(['message' => 'email can not be signed up to newsletter'], ResponseAlias::HTTP_BAD_REQUEST);
        }
        return response()->json(['message' => 'email subscribed to newsletter successfully'], ResponseAlias::HTTP_OK);
    }
}
