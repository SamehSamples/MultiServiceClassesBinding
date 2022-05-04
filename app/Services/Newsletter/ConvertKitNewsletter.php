<?php

namespace App\Services\Newsletter;

use Illuminate\Support\Facades\Http;

class ConvertKitNewsletter implements Interfaces\NewsletterInterface
{
    public function __construct(protected Http $client)
    {

    }

    public function subscribe(string $email, string $list =null)
    {
        $list ??= config('newsletter.convert_kit.lists.subscribers');

        return $this->client::post('https://api.convertkit.com/v3/forms/' . $list . '/subscribe', [
            'api_secret' => config('newsletter.convert_kit.api_secret'),
            'email'=>$email
        ]);
    }
}
