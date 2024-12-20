<?php

require_once '../vendor/autoload.php';

use Tebex\Webhooks;
use Tebex\Webhook\Webhook;

// You must set your secret key first so that webhooks can be validated.
Webhooks::setSecretKey("2248c2227ac29e5cbdbab44ed6a0f961");

// Is read from php://input unless an argument is provided
$webhook = Webhook::parse();

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