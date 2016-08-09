<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Payum\LaravelPackage\Controller\PayumController;

class PaymentController extends PayumController
{
    public function preparePayment()
    {
        $storage = $this->getPayum()->getStorage('Payum\Core\Model\Payment');

        $payment = $storage->create();
        $payment->setNumber(uniqid());
        $payment->setCurrencyCode('EUR');
        $payment->setTotalAmount(123); // 1.23 EUR
        $payment->setDescription('A description');
        $payment->setClientId('anId');
        $payment->setClientEmail('foo@example.com');
        $payment->setDetails(array(
            // put here any fields in a gateway format.
            // for example if you use Paypal ExpressCheckout you can define a description of the first item:
            // 'L_PAYMENTREQUEST_0_DESC0' => 'A desc',
        ));
        $storage->update($payment);

        $captureToken = $payum->getTokenFactory()->createCaptureToken('offline', $payment, 'payment_done');

        return \Redirect::to($captureToken->getTargetUrl());
    }

}
