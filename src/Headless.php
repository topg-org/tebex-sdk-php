<?php

namespace Tebex;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use RuntimeException;
use Tebex\Headless\BasketFacade;
use Tebex\Headless\Projects\DiscordProject;
use Tebex\Headless\Projects\MinecraftProject;
use Tebex\Headless\Projects\OverwolfProject;
use Tebex\Headless\Projects\SteamProject;
use Tebex\Headless\Projects\TebexProject;
use Tebex\Headless\Projects\UniversalProject;
use TebexHeadless\ApiException;
use TebexHeadless\Configuration;
use TebexHeadless\Model\Basket;
use TebexHeadless\Model\BasketLinks;
use TebexHeadless\Model\Category;
use TebexHeadless\Model\Coupon;
use TebexHeadless\Model\CreateBasketRequest;
use TebexHeadless\Model\Package;
use TebexHeadless\Model\Webstore;
use TebexHeadless\TebexHeadless\HeadlessApi;

/**
 * TebexHeadless allows you to access and create baskets for your Tebex project using pre-defined packages.
 */
class Headless extends TebexAPI {
    // Reference to the underlying OpenAPI headless instance
    protected static HeadlessApi $headlessApi;

    // Reference to the underlying webstore we're working with
    private static ?Webstore $_webstore;

    // Reference to the underlying guzzle client
    private static ?Client $_client;

    /**
     * @return HeadlessApi The underlying OpenAPI implementation
     */
    public static function getHeadlessApi() : HeadlessApi {
        return self::$headlessApi;
    }

    /**
     * @return Webstore|null The currently configured Webstore project
     */
    public static function getWebstore() : ?Webstore {
        return self::$_webstore;
    }

    /**
     * Sets the current Headless project to the one indicated by the public token.
     *
     * The public token is validated with the appropriately typed TebexProject being returned.
     *
     * @param string $publicToken The project's public token.
     * @return TebexProject
     * @throws GuzzleException
     */
    public static function setProject(string $publicToken): TebexProject
    {
        self::$_client = new Client();
        self::$_publicToken = $publicToken;
        self::$_areApiKeysSet = true;
        self::$headlessApi = new HeadlessApi(self::$_client, Configuration::getDefaultConfiguration());

        try {
            $response = self::$_client->send(new Request("GET", "https://headless.tebex.io/api/accounts/" . $publicToken))->getBody()->getContents();
            self::$_webstore = new Webstore(json_decode($response, true)["data"]);
        } catch (Exception $e) {
            throw new RuntimeException("Failed to get webstore information. Check your API keys and try again.");
        }

        // Assign the appropriate store class so that required parameter names such as username can be enforced
        switch (self::$_webstore->getPlatformType()) {
            // Minecraft and Overwolf require username
            case "Overwolf":
                $storeClass = OverwolfProject::class;
                break;
            case "Minecraft (Offline/Geyser)":
            case "Minecraft (Bedrock)":
            case "Minecraft: Java Edition":
                $storeClass = MinecraftProject::class;
                break;

            // Steam stores require the steam_id
            case "Conan Exiles":
            case "LEAP":
            case "CREY":
            case "Onset":
            case "RedM":
            case "FiveM":
            case "GTA V":
            case "ATLAS":
            case "Space Engineers":
            case "ARK: Survival Evolved":
            case "Hurtworld":
            case "Team Fortress 2":
            case "Counter-Strike: Global Offensive":
            case "Garry's Mod":
            case "7 Days to Die":
            case "Rust":
            case "Unturned":
                $storeClass = SteamProject::class;
                break;

            // Other apps
            case "Discord":
                $storeClass = DiscordProject::class;
                break;

            case "Universal (No Auth)":
                $storeClass = UniversalProject::class;
                break;
            default:
                throw new RuntimeException("The webstore you are trying to access is not supported by this library: " . self::$_webstore->getPlatformType());
        }

        return new $storeClass();
    }

    /**
     * Passes basket creation data to the OpenAPI implementation, returning the OpenAPI model
     *
     * @param array $basketData
     * @return Basket
     * @throws ApiException
     */
    public static function createBasket(array $basketData) : Basket
    {
        $createBasketRequest = new CreateBasketRequest($basketData);
        return self::$headlessApi->createBasket(self::$_publicToken, $createBasketRequest)->getData();
    }

    /**
     * Gets the URL the user can authorize at. The returned string will be empty if auth is not required for the project.
     *
     * @param Basket $basket
     * @param string $returnUrl
     * @return string
     * @throws ApiException
     */
    public static function getUserAuthUrl(Basket $basket, string $returnUrl) : string {
        $authResponseJson = self::_request("GET","baskets/" . $basket->getIdent() . "/auth?returnUrl=" . urlencode($returnUrl));
        $hasLinks = sizeof($authResponseJson) > 0 && sizeof($authResponseJson[0]) > 0;
        if ($hasLinks) {
            return $authResponseJson[0]['url'];
        } else {
            return "";
        }
    }

    /**
     * @throws ApiException
     */
    private static function _request(string $method, string $endpoint, array $data = []) : array
    {
        try {
            $response = self::$_client->send(new Request($method, "https://headless.tebex.io/api/accounts/" . self::$_publicToken . "/" . $endpoint, [
                "Content-Type" => "application/json",
                "Accept" => "application/json"
            ], json_encode($data)));
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            $errorResponse = json_decode($e->getResponse()->getBody()->getContents(), true);
            $message = $errorResponse["message"] ?? "";
            if(!isset($errorResponse["message"])){
                $message = $e->getMessage();
            }
            throw new ApiException($message, $e->getCode());
        }
    }
    /**
     * Gets the BasketLinks associated with this basket.
     *
     * @param Basket $basket
     * @return BasketLinks
     */
    public static function getCheckoutLinks(Basket $basket) : BasketLinks
    {
        return new BasketLinks((array)$basket->getLinks());
    }

    /**
     * Gets the basket indicated by the basket identifier provided
     *
     * @param string $ident
     * @return Basket
     * @throws ApiException
     */
    public static function getBasketByIdent(string $ident): Basket
    {
        return self::$headlessApi->getBasketById(self::$_publicToken, $ident)->getData();
    }

    /**
     * @return Category[]|null
     * @throws ApiException
     */
    public static function getAllCategories() : array
    {
        return self::$headlessApi->getAllCategories(self::$_publicToken)->getData();
    }

    /**
     * @return Package[]|null
     * @throws ApiException
     */
    public static function getAllPackages(): ?array
    {
        return self::$headlessApi->getAllPackages(self::$_publicToken)->getData();
    }

    /**
     * @return Client The underlying Guzzle client
     */
    public static function getClient() : Client
    {
        return self::$_client;
    }

    /**
     * Gets a specific category by ID.
     * @throws ApiException
     */
    public static function getCategory(int $categoryId) : Category
    {
        $categoryDataJson = self::_request("GET", "categories/" . $categoryId);
        return new Category($categoryDataJson["data"]);
    }

    /**
     * Gets a specific package by ID.
     * @throws ApiException
     */
    public static function getPackage(int $packageId) : Package
    {
        $packageDataJson = self::_request("GET", "packages/" . $packageId);
        return new Package($packageDataJson);
    }

    /**
     * Applies a coupon to the basket.
     *
     * @param BasketFacade $basket
     * @param Coupon $coupon
     * @throws ApiException
     */
    public static function applyCoupon(BasketFacade $basket, Coupon $coupon) : void
    {
        self::_request("POST", "baskets/" . $basket->getBasket()->getIdent() . "/coupons", [
            "coupon_code" => $coupon->getCouponCode()
        ]);
    }

    /**
     * Removes a coupon from the basket.
     *
     * @param BasketFacade $basket
     * @param Coupon $coupon
     *
     * @throws ApiException
     */
    public static function removeCoupon(BasketFacade $basket, Coupon $coupon) : void
    {
        self::_request("POST", "baskets/" . $basket->getBasket()->getIdent() . "/coupons/remove", [
            "coupon_code" => $coupon->getCouponCode()
        ]);
    }
}