# Tebex PHP SDK

The Tebex PHP library provides access to Tebex APIs from applications written using PHP.

## Requirements

PHP 7.4.0 or above.

## Composer

Install the SDK via [Composer](https://getcomposer.org/) using the following command:

```bash
composer require tebex/tebex-sdk-php
```

Use Composer's autoload to use the Tebex library.

```php
require_once 'vendor/autoload.php';
```

## Dependencies

The following PHP extensions must be installed in your PHP environment:
- `curl`
- `json`

Composer will install these extensions automatically. If installing manually, ensure these extensions are available and enabled for your PHP distribution.

## Examples

### Headless API
Headless allows interaction with your Tebex webstore using pre-defined packages. This API does not require any prior approval. See a more in-depth [example script here](example/headless.php).

```php
$project = Headless::setProject("publicToken"); // Authorize headless using your public token

// Interact with the store/project through $project
$categories = $project->listCategories();   // array of Category
$packages = $project->listPackages();       // array of Package

// Create a basket through the $project
$basket = $project->createBasket("https://tebex.io/completed", "https://tebex.io/cancelled");

// Authorize the user before adding packages (not required for Universal stores)
if ($project->requiresUserAuth()) {
    $authUrl = $project->getUserAuthUrl($basket, "https://tebex.io/auth-return");
    
    // send the user to the auth URL
}

// Add packages to the authorized basket
$basket->addPackage($packges[0])

// Direct the user to checkout
echo "Checkout at: " . $basket->getLinks()->getCheckout();
```
### Webhooks

Webhooks are send to authorized endpoints configured within your Tebex panel. They contain information about events that occur in your webstore such as payments, refunds, and disputes.

**Note:** The secret key must be your **webhook key** provided at [https://creator.tebex.io/webhooks/endpoints](https://creator.tebex.io/webhooks/endpoints)
```php
// You must set your secret key first so that webhooks can be validated.
Webhooks::setSecretKey("your-webhook-secret-key");

// Is read from php://input unless an argument is provided.
// The signing signature and IP will be automatically validated.
$webhook = Webhook::parse();

// To register your webhook endpoint you must respond to the validation webhook with its ID
if ($webhook->isType(\Tebex\Webhook\VALIDATION_WEBHOOK)) {
    return json_encode(["id" => $webhook->getId()]);
}

// Otherwise you can check for specific types or groups of types
if ($webhook->isType(\Tebex\Webhook\PAYMENT_DECLINED)) {
    // handle payment declined
}
else if ($webhook->isTypeOfPayment() || $webhook->isTypeOfDispute()){
    // The "payment subject" contains data about the webhook action
    $pmtSubject = $webhook->getSubject(); // PaymentSubject
    
    // interact with payment subject
}
else if ($webhook->isTypeOfRecurringPayment()) {
    $recurringPmtSubject = $webhook->getSubject(); // RecurringPaymentSubject
    
    // interact with recurring payment subject
}
```

### Checkout API
The Checkout API allows creation of ad-hoc products without being defined as a webstore package. This API requires prior approval. See [an example script here](/example/checkout.php).

```php
// Authorize your store using your API keys
Checkout::setApiKeys("projectId", "privateKey");
```

#### Creating Baskets
Use the `Checkout\BasketBuilder` to create and manage your baskets.
```php
$builder = Checkout\BasketBuilder::new()
    ->email("support@tebex.io")->firstname("Tebex")->lastname("Support")
    ->ip($_SERVER["REMOTE_ADDR"])
    ->returnUrl("https://tebex.io/")->completeUrl("https://tebex.io/");
```
#### Adding Packages
Use the `Checkout\PackageBuilder` to create the packages you wish to add to your basket.
```php
// one-time products
$package1 = Checkout\PackageBuilder::new()->name("100 Gold")->qty(1)->price(1.27)->oneTime();

// subscription product, each 1 month
$package2 = Checkout\PackageBuilder::new()->name("1 Month Sub")->qty(1)->price(2.44)
    ->monthly()->expiryLength(1);
```
#### Checkout Request (Recommended)
You can use `Checkout::checkoutRequest` to send basket information and its products in a single request. The list
of packages should be an array of `CheckoutItem`, which can be provided by the `PackageBuilder` with `buildCheckoutItem()`.

```php
$builder = Checkout\BasketBuilder::new()
    ->email("support@tebex.io")->firstname("Tebex")->lastname("Support")
    ->ip($_SERVER["REMOTE_ADDR"])                   
    ->returnUrl("https://tebex.io/")->completeUrl("https://tebex.io/");

$package1 = Checkout\PackageBuilder::new()->name("100 Gold")->qty(1)->price(1.27)->oneTime();
$basket = Checkout::checkoutRequest($builder, [$package1->buildCheckoutItem()]);

echo "Checkout at: " . $basket->getLinks()->getCheckout();
```