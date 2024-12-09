<?php

require_once '../vendor/autoload.php';
use Tebex\Headless;

// Setting the project will provide you with a TebexProject(OverwolfProject, MinecraftProject, SteamProject, UniversalProject)
$project = Headless::setProject("your-public-token");

// Interact with the store/project through $project
$categories = $project->listCategories();
$packages = $project->listPackages();
$project->getCategory(12345);
$project->getPackage(67890);

$basket = $project->createBasket("https://tebex.io/completed", "https://tebex.io/cancelled");

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

// Query the $basket for any info
echo "Price: $" . $basket->getBasePrice() . "\n";

// Go to checkout
$checkoutLink = $basket->getLinks()->getCheckout();
echo "Checkout at: " . $checkoutLink;