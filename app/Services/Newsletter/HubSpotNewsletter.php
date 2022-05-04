<?php

namespace App\Services\Newsletter;

use HubSpot\Client\Crm\Contacts\Model\SimplePublicObjectInput;

class HubSpotNewsletter implements Interfaces\NewsletterInterface
{
    public function __construct(protected $client)
    {

    }

    public function subscribe(string $email, string $list = null)
    {
        $contactInput = new SimplePublicObjectInput();
        $contactInput->setProperties([
            'email' => $email
        ]);

        return $this->client->crm()->contacts()->basicApi()->create($contactInput);
    }
}
