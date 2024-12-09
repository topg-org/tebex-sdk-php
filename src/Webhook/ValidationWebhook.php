<?php

namespace Tebex\Webhook;

/**
 * The validation webhook is sent to ensure we are not sending HTTP requests to URLs that are not expecting our webhooks.
 * Webhooks will not be sent to an endpoint unless it has been validated first.
 *
 * Upon receiving a validation webhook you must respond with a 200 OK response containing a JSON object that has an id property representing the validation webhook's ID, please see the example response below.
 */
class ValidationWebhook extends Webhook
{

}