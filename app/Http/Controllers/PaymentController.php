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


    // Returned from paypal on success
    //{"status":"captured","details":{"TOKEN":"EC-9M24057676246470N","PAYERID":"FA5XLVU3CLN92","PROFILESTARTDATE":"2016-08-11T07:00:00Z","DESC":"Weather subscription","BILLINGPERIOD":"Day","BILLINGFREQUENCY":"7","AMT":"0.05","CURRENCYCODE":"USD","COUNTRYCODE":"US","MAXFAILEDPAYMENTS":"3","PROFILEID":"I-GS64JWYNJ2BC","PROFILESTATUS":"ActiveProfile","TIMESTAMP":"2016-08-11T14:26:38Z","CORRELATIONID":"5bdb03845495e","ACK":"Success","VERSION":"65.1","BUILD":"000000","STATUS":"Active","AUTOBILLOUTAMT":"NoAutoBill","SUBSCRIBERNAME":"omer farooq","NEXTBILLINGDATE":"2016-08-11T10:00:00Z","NUMCYCLESCOMPLETED":"0","NUMCYCLESREMAINING":"0","OUTSTANDINGBALANCE":"0.00","FAILEDPAYMENTCOUNT":"0","TRIALAMTPAID":"0.00","REGULARAMTPAID":"0.00","AGGREGATEAMT":"0.00","AGGREGATEOPTIONALAMT":"0.00","FINALPAYMENTDUEDATE":"1970-01-01T00:00:00Z","SHIPTONAME":"omer farooq","SHIPTOSTREET":"1 Main St","SHIPTOCITY":"San Jose","SHIPTOSTATE":"CA","SHIPTOZIP":"95131","SHIPTOCOUNTRYCODE":"US","SHIPTOCOUNTRY":"US","SHIPTOCOUNTRYNAME":"United States","SHIPADDRESSOWNER":"PayPal","SHIPADDRESSSTATUS":"Unconfirmed","TOTALBILLINGCYCLES":"0","SHIPPINGAMT":"0.00","TAXAMT":"0.00","REGULARBILLINGPERIOD":"Day","REGULARBILLINGFREQUENCY":"7","REGULARTOTALBILLINGCYCLES":"0","REGULARCURRENCYCODE":"USD","REGULARAMT":"0.05","REGULARSHIPPINGAMT":"0.00","REGULARTAXAMT":"0.00","PAYMENTREQUEST_0_PAYMENTACTION":"Sale","AUTHORIZE_TOKEN_USERACTION":"commit"}}
}
