# Laravel Backend Application Sample: Multi Service classes Dynamic Binding

## Problem Statement

In this sample application, it is a business requirement to be able to easily switch between Newsletter Sending Service Providers to which end-users subscribe their emails to receive the application's newsletter.
The **selected service provider specific info** is required to be configured through the **.env file**.

## Approach

To solve this problem, and accommodate 3 different service providers, the following approach was followed:

1. All the Newsletter Sending Service Providers Keys and Info were added to **.env file**. Utilized Newsletter Sending Service Providers are [mailchimp](https://mailchimp.com/), [ConvertKit](https://convertkit.com/), and [HubSpot](https://www.hubspot.com/).
2. a **config\newsletter.php** file was added to read values from .env file and present selected configuration to the application's code

```
<?php

return [
 'default' => env('SELECTED_SERVICE_PROVIDER', 'mailchimp'),

 'mailchimp' => [
 'key' => env('MAILCHIMP_KEY'),
 'server' => env('MAILCHIMP_SERVER'),
 'lists' => [
 'subscribers' => env('MAILCHIMP_SUBSCRIBERS_LIST_ID')
],
 ],

 'convert_kit' => [
 'key' => env('CONVERT_KIT_KEY'),
 'api_secret' => env('CONVERT_KIT_SECRET'),
 'lists' => [
 'subscribers' => env('CONVERT_KIT_SUBSCRIBERS_LIST_ID')
],
 ],

 'hubspot' => [
 'key' => env('HubSpot_KEY'),
 ],
];
```

4. an **Interface** (NewsletterInterface) was added: this interface is added to assure all service classes will implement the same set of required method(s). In this sample one method was enough to accomplish the requirements.

```
<?php

namespace App\Services\Newsletter\Interfaces;

interface NewsletterInterface
{
  public function subscribe(string $email, string $list = null);
}
```

5. Three **services classes** were added (all implementing NewsletterInterface), below is a sample class

```
<?php

namespace App\Services\Newsletter;

use HubSpot\Client\Crm\Contacts\Model\SimplePublicObjectInput;

class HubSpotNewsletter implements Interfaces\NewsletterInterface
{
  public function __construct(protected $client)
 {
 }
  public function subscribe(string $email, string $list = null)
 {  $contactInput = new SimplePublicObjectInput();
  $contactInput->setProperties([
  'email' => $email
  ]);

 return $this->client->crm()->contacts()->basicApi()->create($contactInput);
  }
}
```

6. **Newsletter Service Provider** (NewsletterServiceProvider) was added: with the objective of dynamically read the configurations from **.env** file and bind the proper newsletter service class:

```
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
 * Register services. * * @return void
 */  public function register()
 {  //bind Newsletter class as per the selected Newsletter Service Provider configured in .env file
  if(config('newsletter.default')==='mailchimp') { // mailchimp binding
  $newsletter=new MailchimpNewsletter((new ApiClient())->setConfig([
  'apiKey' => config('newsletter.mailchimp.key'),
  'server' => config('newsletter.mailchimp.server')
 ]));
  }elseif (config('newsletter.default')==='hubspot') { // hubspot binding
  $newsletter=new HubSpotNewsletter(Factory::createWithApiKey(config('newsletter.hubspot.key')));
  }elseif (config('newsletter.default')==='convert_kit') { // convert_kit binding
  $newsletter=new ConvertKitNewsletter(new Http());
  }else{ // Invalid configuration handling
  throw new \Exception('invalid newsletter service provider configuration');
  }
 app()->bind(NewsletterInterface::class, fn() => $newsletter);  // performing actual binding
  }
}
```

7. Registering (NewsletterServiceProvider) in **config/app.php**, for more information about [registering service providers](https://laravel.com/docs/9.x/providers#registering-providers)

```
/*
 * Application Service Providers...
 */
App\Providers\AppServiceProvider::class,
App\Providers\AuthServiceProvider::class,
App\Providers\EventServiceProvider::class,
App\Providers\RouteServiceProvider::class,
App\Providers\NewsletterServiceProvider::class, //here you go
```

8. A **Controller** was added (NewsletterController) to perform subscription. Due to the nature of the sample a [Single Action Controller](https://laravel.com/docs/9.x/controllers#single-action-controllers) were added. In this controller, only the interface (NewsletterInterface) were referenced not the actual service classes, but, the interface is now bound to actual configured service class (step 6).

```
<?php

namespace App\Http\Controllers;


use App\Services\Newsletter\Interfaces\NewsletterInterface;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class NewsletterController extends Controller
{
  // Single Action Controller Method
  public function __invoke(NewsletterInterface $newsletter)
 { request()->validate([
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
```

9. An API route was created to call the method of the single action class controller

```
<?php

use App\Http\Controllers\NewsletterController;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

// Single Action Controller Route Registration
Route::post('newsletter_subscription', NewsletterController::class);
```

## Contact me

Please feel free to contact me for any clarifications or enquiries at:  
Email: sameh74@gmail.com  
Mobile/WhatsApp: (965)99150372
