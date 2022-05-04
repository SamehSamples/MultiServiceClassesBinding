<?php

namespace App\Providers;

use App\Services\Newsletter\ConvertKitNewsletter;
use App\Services\Newsletter\HubSpotNewsletter;
use App\Services\Newsletter\MailchimpNewsletter;
use App\Services\Newsletter\Interfaces\NewsletterInterface;
use HubSpot\Factory;
use Illuminate\Support\Facades\Http;
use MailchimpMarketing\ApiClient;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;


class NewsletterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //bind Newsletter class as per the selected Newsletter Service Provider configured in .env file
        if(config('newsletter.default')==='mailchimp') {                // mailchimp binding
            $newsletter=new MailchimpNewsletter((new ApiClient())->setConfig([
                'apiKey' => config('newsletter.mailchimp.key'),
                'server' => config('newsletter.mailchimp.server')
            ]));
        }elseif (config('newsletter.default')==='hubspot') {            // hubspot binding
            $newsletter=new HubSpotNewsletter(Factory::createWithApiKey(config('newsletter.hubspot.key')));
        }elseif (config('newsletter.default')==='convert_kit') {        // convert_kit binding
            $newsletter=new ConvertKitNewsletter(new Http());
        }else{                                                              // Invalid configuration handling
            throw new \Exception('invalid newsletter service provider configuration');
        }
        app()->bind(NewsletterInterface::class, fn() => $newsletter);
    }
}
