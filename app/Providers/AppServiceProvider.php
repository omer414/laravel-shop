<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Payum\Core\PayumBuilder;
use Payum\LaravelPackage\Model\GatewayConfig;
use Payum\LaravelPackage\Model\Token;
use Payum\LaravelPackage\Model\Payment;
use Payum\LaravelPackage\Storage\EloquentStorage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->resolving('payum.builder', function(PayumBuilder $payumBuilder) {
            $payumBuilder
                // this method registers filesystem storages, consider to change them to something more
                // sophisticated, like eloquent storage
                ->addDefaultStorages()
                ->addStorage(Payment::class, new EloquentStorage(Payment::class))
                ->setTokenStorage(new EloquentStorage(Token::class))
                ->addGateway('offline', ['factory' => 'offline'])
                ->addGateway('paypal_ec', [
                    'factory' => 'paypal_express_checkout',
                    'username' => 'warlox414-merchant_api1.gmail.com',
                    'password' => 'Z9VFDKJA2FJQ5DZW',
                    'signature' => 'ALGGHNwYi1b3f.SAFEBGIVyGMKAwAxeOTUcB8qI8ZeSPXiP02XSlsQtG',
                    'sandbox' => true
                ]);
              //  ->setGatewayConfigStorage(new EloquentStorage(GatewayConfig::class));
        });
    }
}
