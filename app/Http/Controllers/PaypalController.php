<?php

namespace App\Http\Controllers;

use App;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Log;
use Payum\Core\Request\GetHumanStatus;
use Payum\Core\Request\Sync;
use Payum\LaravelPackage\Controller\PayumController;
use Payum\Paypal\ExpressCheckout\Nvp\Api;
use Payum\Paypal\ExpressCheckout\Nvp\Request\Api\CreateRecurringPaymentProfile;
use Redirect;
use Response;

class PaypalController extends PayumController
{
    public function prepareExpressCheckout()
    {
        $storage = $this->getPayum()->getStorage('Payum\Core\Model\ArrayObject');

        $details = $storage->create();
        $details['PAYMENTREQUEST_0_CURRENCYCODE'] = 'EUR';
        $details['PAYMENTREQUEST_0_AMT'] = 1.23;
        $storage->update($details);

        $captureToken = $this->getPayum()->getTokenFactory()->createCaptureToken('paypal_ec', $details, 'payment_done');

        return Redirect::to($captureToken->getTargetUrl());
    }

/*    public function done($payum_token)
    {
        $request = \App::make('request');
        $request->attributes->set('payum_token', $payum_token);

        $token = $this->getPayum()->getHttpRequestVerifier()->verify($request);
        $gateway = $this->getPayum()->getGateway($token->getGatewayName());

        $gateway->execute($status = new GetHumanStatus($token));

        return \Response::json(array(
            'status' => $status->getValue(),
            'details' => iterator_to_array($status->getFirstModel())
        ));
    }*/

    public function done(Request $request) {
        /** @var Request $request */
        //$request = \App::make('request');
        $request->attributes->set('payum_token', $request->input('payum_token'));

        $token = $this->getPayum()->getHttpRequestVerifier()->verify($request);
        $gateway = $this->getPayum()->getGateway($token->getGatewayName());

        $gateway->execute($status = new GetHumanStatus($token));

        return Response::json(array(
            'status' => $status->getValue(),
            'details' => iterator_to_array($status->getFirstModel())
        ));
    }

    /************************** For subscriptions *************************/
    public function prepareSubscribeAgreement() {

        //$storage = $this->getPayum()->getStorage('Payum\Core\Model\ArrayObject');
        $storage = App::make('payum')->getStorage('Payum\Core\Model\ArrayObject');

        $details = $storage->create();
        $details['PAYMENTREQUEST_0_AMT'] = 0;
        $details['L_BILLINGTYPE0'] = Api::BILLINGTYPE_RECURRING_PAYMENTS;
        $details['L_BILLINGAGREEMENTDESCRIPTION0'] = "Weather subscription";
        //$details['NOSHIPPING'] = 1;
        $storage->update($details);

        $captureToken = $this->getPayum()->getTokenFactory()->createCaptureToken('paypal_ec', $details, 'paypal_subscribe');

        return Redirect::to($captureToken->getTargetUrl());
    }

    public function createSubscribePayment(Request $request) {
        $request->attributes->set('payum_token', $request->input('payum_token'));

        $token = $this->getPayum()->getHttpRequestVerifier()->verify($request);
        $gateway = $this->getPayum()->getGateway($token->getGatewayName());
        $agreementStatus = new GetHumanStatus($token);
        $gateway->execute($agreementStatus);

        if (!$agreementStatus->isCaptured()) {
            header('HTTP/1.1 400 Bad Request', true, 400);
            exit;
        }

        $agreement = $agreementStatus->getModel();

        $storage = $this->getPayum()->getStorage('Payum\Core\Model\ArrayObject');

        $recurringPayment = $storage->create();
        $recurringPayment['TOKEN'] = $agreement['TOKEN'];
        $recurringPayment['PAYERID'] = $agreement['PAYERID'];
        $recurringPayment['PROFILESTARTDATE'] = date(DATE_ATOM);
        $recurringPayment['DESC'] = $agreement['L_BILLINGAGREEMENTDESCRIPTION0'];
        $recurringPayment['BILLINGPERIOD'] = Api::BILLINGPERIOD_DAY;
        $recurringPayment['BILLINGFREQUENCY'] = 7;
        $recurringPayment['AMT'] = 0.05;
        $recurringPayment['CURRENCYCODE'] = 'USD';
        $recurringPayment['COUNTRYCODE'] = 'US';
        $recurringPayment['MAXFAILEDPAYMENTS'] = 3;

        $gateway->execute(new CreateRecurringPaymentProfile($recurringPayment));
        $gateway->execute(new Sync($recurringPayment));

        $captureToken = $this->getPayum()->getTokenFactory()->createCaptureToken('paypal_ec', $recurringPayment, 'payment_done');

        return Redirect::to($captureToken->getTargetUrl());
    }

    public function resolveIpn(Request $request){

        return true;
    }


}