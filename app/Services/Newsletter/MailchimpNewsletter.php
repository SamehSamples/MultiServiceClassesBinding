<?php

namespace App\Services\Newsletter;

use MailchimpMarketing\ApiClient;

class MailchimpNewsletter implements Interfaces\NewsletterInterface
{
    public function __construct(protected ApiClient $client)
    {

    }

    public function subscribe(string $email, string $list =null)
    {
        $list ??= config('newsletter.mailchimp.lists.subscribers');

        return $this->client->lists->addListMember($list ,[
            "email_address" => $email,
            "status" => "subscribed",
        ]);
    }
}
