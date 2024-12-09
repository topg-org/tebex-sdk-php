<?php

require_once '../vendor/autoload.php';

use Tebex\Webhooks;
use Tebex\Webhook\Webhook;

// dev
$exampleValidateJson = <<<json
    {
        "id": "679e12cd-b2bc-4c75-83fe-21a40f6b9f00",
        "type": "validation.webhook",
        "date": "2024-07-12T14:52:18+00:00",
        "subject": {}
    }
    json;
$_SERVER["HTTP_X_SIGNATURE"] = "7f682a71dff7b9440ca8e7f71f2c2ce9352efe3f8f7ef0c991b21cd7d184bcb1";
$_SERVER["REMOTE_ADDR"] = "18.209.80.3";
// dev


// You must set your secret key first so that webhooks can be validated.
Webhooks::setSecretKey("2248c2227ac29e5cbdbab44ed6a0f961");

// Is read from php://input unless an argument is provided
$webhook = Webhook::parse($exampleValidateJson);

// You can check for specific webhook types
if ($webhook->isType(\Tebex\Webhook\VALIDATION_WEBHOOK)) {
    echo json_encode(["id" => $webhook->getId()]); // Respond to validation endpoint
}

// You can quickly check the type of webhook with helper functions as well
else if ($webhook->isTypeOfPayment() || $webhook->isTypeOfDispute())
{
    // The "subject" contains data about the webhook action
    $pmtSubject = $webhook->getSubject(); // type is \TebexCheckout\Model\PaymentSubject
    // etc....
}
else if ($webhook->isTypeOfRecurringPayment()) {
    $recurringPmtSubject = $webhook->getSubject(); // \TebexCheckout\Model\RecurringPaymentSubject
    // etc...
}