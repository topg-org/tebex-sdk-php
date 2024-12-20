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
Headless allows interaction with your Tebex project using pre-defined packages and is available for all stores.

```php
$project = Headless::setProject("your-public-token");

// Interact with the project through $project
$categories = $project->listCategories();
$packages = $project->listPackages();
$project->getCategory(12345);
$project->getPackage(67890);

// Create baskets through the project, providing completion and cancellation urls
$basket = $project->createBasket("https://tebex.io/completed", "https://tebex.io/cancelled");

// If the project requires auth, direct the user to authorize before adding packages
if ($project->requiresUserAuth()) {
    $authUrl = $project->getUserAuthUrl($basket, "https://tebex.io/auth-return");
    echo "- User auth required at: " . $authUrl . ".\n";
}

// Once user is successfully authed we can add packages via $basket
$package = $packages[0];

$basket = $basket->addPackage($package);
$basket = $basket->addGiftedPackage($package, "Tebex");
$basket = $basket->addGiftCardPackage($package, "tebex-integrations@overwolf.com");

// You can also add variable data as needed per each package
$basket->addPackage($package, [
    "server_id" => "127244"
]);

// Each function returns the remote basket after completion, but you can always request the current basket from the API
$basket = $basket->refreshBasket();

// Query the $basket object for any info
echo "Price: $" . $basket->getBasePrice() . "\n";

// Go to checkout
$checkoutLink = $basket->getLinks()->getCheckout();
echo "Checkout at: " . $checkoutLink;
```
### Webhooks

Webhooks are sent to authorized endpoints configured within your Tebex creator panel. They contain information about events that occur in your project such as payments, refunds, and disputes.

**Note:** The secret key must be your **webhook key** provided at [https://creator.tebex.io/webhooks/endpoints](https://creator.tebex.io/webhooks/endpoints)
```php
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
```

### Checkout API
The Checkout API allows collecting payment for ad-hoc products not defined in a Tebex project.

This API requires prior approval. Please contact Tebex support to enable on your account.

```php
// Authorize your store using your API keys
Checkout::setApiKeys("projectId", "privateKey");
```

#### Creating Baskets
Use the `Checkout\BasketBuilder` to create and manage your baskets.
```php
// Use the BasketBuilder to create your basket for Checkout
$builder = Checkout\BasketBuilder::new()
    ->email("tebex-integrations@overwolf.com")
    ->firstname("Tebex")
    ->lastname("Integrations")
    ->ip("127.0.0.1") // provide client IP if running on your backend server
    ->returnUrl("https://tebex.io/")
    ->completeUrl("https://tebex.io/");
```

#### Adding Packages
Use the `Checkout\PackageBuilder` to create the packages you wish to add to your basket.
```php
$package1 = Checkout\PackageBuilder::new()->name("100 Gold")->qty(1)->price(1.27)
    ->oneTime();
    
$package2 = Checkout\PackageBuilder::new()->name("1 Month Sub")->qty(1)->price(2.44)
    ->subscription()->monthly()->expiryLength(1);
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

### ❓ API Documentation

Our APIs are fully documented at https://docs.tebex.io/developers as a resource for all options, events, and advanced functionality possible through Tebex.

## 🔗 Useful Links

- [Tebex API Documentation](https://docs.tebex.io/developers)
- [Headless API Documentation](https://docs.tebex.io/developers/headless-api/overview)
- [Checkout API Documentation](https://docs.tebex.io/developers/checkout-api/overview)

## Contributions

This SDK is open source and we welcome contributions from the community. If you wish to make a contribution, please review **CONTRIBUTING.md** for guidelines and things to know before making your contribution.

## 🙋‍♂️ Support

For issues relating to this library, please raise an issue in its repository. Otherwise you may also contact [tebex-integrations@tebex.io](mailto:tebex-integrations@tebex.io).