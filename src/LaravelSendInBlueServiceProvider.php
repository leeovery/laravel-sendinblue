<?php

namespace Leeovery\LaravelSendInBlue;

use Illuminate\Support\Arr;
use Illuminate\Mail\MailManager;
use SendinBlue\Client\Api\SMTPApi;
use GuzzleHttp\Client as HttpClient;
use SendinBlue\Client\Configuration;
use Illuminate\Support\ServiceProvider;

class LaravelSendInBlueServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->app[MailManager::class]->extend('sendinblue', function () {
            return $this->app->make('laravel-sendinblue');
        });
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $config = $this->app['config']->get('services.sendinblue', []);
        $endpoint = Arr::get($config, 'options.endpoint', 'https://api.sendinblue.com/v3');

        $sendInBlueConfig = Configuration::getDefaultConfiguration()
                                         ->setApiKey('api-key', $config['secret'])
                                         ->setHost($endpoint);

        $apiInstance = new SMTPApi(
            new HttpClient(Arr::add(
                $config['guzzle'] ?? [], 'connect_timeout', 60
            )),
            $sendInBlueConfig
        );

        $this->app->singleton('laravel-sendinblue', function () use ($apiInstance) {
            return new SendInBlueTransport($apiInstance);
        });
    }
}
