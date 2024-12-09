<?php

namespace Tebex\Headless;

use GuzzleHttp\Exception\GuzzleException;
use Tebex\Headless;
use Tebex\Headless\Projects\TebexProject;
use TebexHeadless\ApiException;
use TebexHeadless\Model\ApplyCreatorCodeRequest;
use TebexHeadless\Model\Basket;
use TebexHeadless\Model\BasketLinks;
use TebexHeadless\Model\Coupon;
use TebexHeadless\Model\GiftCard;
use TebexHeadless\Model\Package;
use TebexHeadless\Model\RemoveGiftCardRequest;

/**
 * Provides functionality for accessing and managing baskets on HeadlessApi
 */
class BasketFacade
{
    // Reference to the Tebex project this basket is attached to
    private TebexProject $_project;

    // OpenAPI
    private Basket $_basket;

    public function __construct(Basket $basket, TebexProject $project) {
        $this->_basket = $basket;
        $this->_project = $project;
    }

    /**
     * @return Basket The underlying OpenAPI basket
     */
    public function getBasket(): Basket {
        return $this->_basket;
    }

    /**
     * @return BasketLinks The underlying OpenAPI BasketLinks
     */
    public function getLinks(): BasketLinks {
        return $this->_basket->getLinks();
    }

    /**
     * Refreshes our current basket remotely from the API.
     * @throws ApiException
     */
    public function refreshBasket(): BasketFacade {
        $this->_basket = Headless::getBasketByIdent($this->_basket->getIdent());
        return $this;
    }

    /**
     * Gets the base price of the basket.
     * @return float
     */
    public function getBasePrice(): float {
        return $this->_basket->getBasePrice();
    }

    /**
     * @return bool True if the basket is successfully authed / we can add packages to it. If not authed, use getUserAuthUrl().
     */
    public function isAuthed() : bool {
        return sizeof($this->_project->getRequiredBasketParams()) > 0 && $this->_basket->getUsernameId() != null;
    }

    /**
     * Adds a package to the basket. Variable data may be provided as well as a quantity
     * @param Package $package          The package to add.
     * @param array|null $variableData  Optional variable_data parameters
     * @param int $qty                  Optional quantity, default 1.
     * @return BasketFacade     The updated basket with package added.
     * @throws GuzzleException
     */
    public function addPackage(Package $package, array $variableData=null, int $qty=1) : BasketFacade
    {
        $addBasketPackageRequest = [
            "package_id" => $package->getId(),
            "quantity" => $qty,
            "variable_data" => $variableData,
        ];

        return $this->_addPackage($addBasketPackageRequest);
    }

    /**
     * Adds a package with a gift card deliverable, requiring the email of the recipient.
     *
     * @param Package $package
     * @param string $giftcardToEmail
     * @param int $qty
     * @return BasketFacade
     * @throws GuzzleException
     */
    public function addGiftCardPackage(Package $package, string $giftcardToEmail, int $qty=1) : BasketFacade {
        $addBasketPackageRequest = [
            "package_id" => $package->getId(),
            "quantity" => $qty,
            "variable_data" => [
                "giftcard_to" => $giftcardToEmail,
            ]];
        return $this->_addPackage($addBasketPackageRequest);
    }

    /**
     * Adds a package containing a discord deliverable, requiring a Discord ID
     * @param Package $package
     * @param string $discordId
     * @param int $qty
     * @return BasketFacade
     * @throws GuzzleException
     */
    public function addPackageWithDiscordDeliverable(Package $package, string $discordId, int $qty=1) : BasketFacade {
        $addBasketPackageRequest = [
            "package_id" => $package->getId(),
            "quantity" => $qty,
            "variable_data" => [
                "discord_id" => $discordId,
            ]];
        return $this->_addPackage($addBasketPackageRequest);
    }

    /**
     * Adds a package that contains a game server deliverable, requiring the username ID.
     * @param Package $package
     * @param string $usernameId   The user identifier such as steam ID
     * @param int $qty
     * @return BasketFacade
     * @throws GuzzleException
     */
    public function addPackageWithGameServerCommand(Package $package, string $usernameId, int $qty=1) : BasketFacade {
        $addBasketPackageRequest = [
            "package_id" => $package->getId(),
            "quantity" => $qty,
            "variable_data" => [
                "username_id" => $usernameId,
            ]];
        return $this->_addPackage($addBasketPackageRequest);
    }

    /**
     * Adds a package containing variable data as well as custom data.
     * @param Package $package
     * @param array $variableData   When adding packages, there may be certain Variables associated with them. These can be filled by providing the variable_data object with mapped names to values.
     * @param array $customData     Custom data to attach to the basket. Can be referenced as part of the transaction.
     * @param int $qty
     * @return BasketFacade
     * @throws GuzzleException
     */
    public function addPackageWithCustomData(Package $package, array $variableData, array $customData, int $qty=1) : BasketFacade {
        $addBasketPackageRequest = [
            "package_id" => $package->getId(),
            "quantity" => $qty,
            "variable_data" => $variableData,
            "custom" => $customData
        ];
        return $this->_addPackage($addBasketPackageRequest);
    }

    /**
     * Adds a package as a gift to another target user.
     *
     * @param Package $package
     * @param string $targetUsernameId
     * @param $variableData
     * @param int $qty
     * @return BasketFacade
     * @throws ApiException
     */
    public function addGiftedPackage(Package $package, string $targetUsernameId, $variableData=null, int $qty=1) : BasketFacade
    {
        $addGiftedPackageRequest = [
            "package_id" => $package->getId(),
            "quantity" => $qty,
            "variable_data" => $variableData,
            "target_username_id" => $targetUsernameId
        ];
        return $this->_addPackage($addGiftedPackageRequest);
    }

    /**
     * Adds a package that is executed on a target game server.
     *
     * @param Package $package
     * @param string $usernameId
     * @param int $serverId
     * @param int $qty
     * @return BasketFacade
     * @throws GuzzleException
     */
    public function addPackageForTargetGameServer(Package $package, string $usernameId, int $serverId, int $qty=1) : BasketFacade {
        $targetGameServerPackageRequest = [
            "package_id" => $package->getId(),
            "quantity" => $qty,
            "variable_data" => [
                //"username_id" => $usernameId, Not required for target game server
                "server_id" => $serverId,
            ]];
        return $this->_addPackage($targetGameServerPackageRequest);
    }

    /**
     * Manually calls the AddPackage endpoint
     *
     * @throws ApiException
     */
    private function _addPackage(array $data) : BasketFacade {
        // OpenAPI types are struggling with handling wrapped 'data' responses, so we send and process the data ourselves
        try {
            $newBasket = json_decode(Headless::getClient()->post("https://headless.tebex.io/api/baskets/" . $this->_basket->getIdent() . "/packages",
                [
                    'json' => $data,
                ]
            )->getBody()->getContents());
        } catch (GuzzleException $e) {
            throw new ApiException($e->getMessage(), $e->getCode(), $e);
        }

        $this->_basket = new Basket((array)$newBasket->data);
        return $this;
    }

    /**
     * Adds the given creator code to the basket
     * @param string $code
     * @param bool $refreshBasket True if you want the basket to be refreshed (price updated, etc.) after setting creator code.
     * @throws ApiException
     */
    public function addCreatorCode(string $code, bool $refreshBasket=false) : void {
        Headless::getHeadlessApi()->applyCreatorCode(Headless::getPublicToken(), $this->_basket->getIdent(), new ApplyCreatorCodeRequest([
            'creator_code' => $code,
        ]));

        if ($refreshBasket) {
            $this->_basket = $this->refreshBasket()->getBasket();
        }
    }

    /**
     * Removes the given creator code from the basket.
     * @param string $code The code to remove.
     * @param bool $refreshBasket
     * @return void
     * @throws ApiException
     */
    public function removeCreatorCode(string $code, bool $refreshBasket = false) : void {
        Headless::getHeadlessApi()->removeCreatorCode(Headless::getPublicToken(), $this->_basket->getIdent(), $code);
        if ($refreshBasket) {
            $this->_basket = $this->refreshBasket()->getBasket();
        }
    }

    /**
     * Adds a coupon code to the basket.
     * @param string $couponCode The coupon code to apply.
     * @param bool $refreshBasket True to refresh the local basket after applying coupon. Sends additional request.
     * @throws ApiException
     */
    public function addCoupon(string $couponCode, bool $refreshBasket=false) : void {
        $coupon = new Coupon([
            "coupon_code" => $couponCode,
        ]);
        Headless::applyCoupon($this, $coupon);
        if ($refreshBasket) {
            $this->_basket = $this->refreshBasket()->getBasket();
        }
    }

    /**
     * Removes the coupon from the basket.
     *
     * @param string $couponCode
     * @param bool $refreshBasket
     * @return void
     * @throws ApiException
     */
    public function removeCoupon(string $couponCode, bool $refreshBasket = false) : void {
        $coupon = new Coupon([
            "coupon_code" => $couponCode,
        ]);

        Headless::removeCoupon($this, $coupon);
        if ($refreshBasket) {
            $this->_basket = $this->refreshBasket()->getBasket();
        }
    }

    /**
     * Adds a gift card to the basket.
     * @param string $cardNumber
     * @param bool $refreshBasket
     * @return void
     * @throws ApiException
     */
    public function addGiftCard(string $cardNumber, bool $refreshBasket = false) : void {
        $giftCard = new GiftCard([
            "card_number" => $cardNumber,
        ]);
        Headless::getHeadlessApi()->applyGiftCard(Headless::getPublicToken(), $this->_basket->getIdent(), $giftCard);
        if ($refreshBasket) {
            $this->_basket = $this->refreshBasket()->getBasket();
        }
    }

    /**
     * Removes the gift card from the basket.
     * @param string $cardNumber
     * @param bool $refreshBasket
     * @return void
     * @throws ApiException
     */
    public function removeGiftCard(string $cardNumber, bool $refreshBasket = false) : void {
        Headless::getHeadlessApi()->removeGiftCard(Headless::getPublicToken(), $this->_basket->getIdent(), new RemoveGiftCardRequest([
            "card_number" => $cardNumber,
        ]));
        if ($refreshBasket) {
            $this->_basket = $this->refreshBasket()->getBasket();
        }
    }
}