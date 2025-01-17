<?php

namespace Tebex\Checkout;

use Tebex\Checkout;
use TebexCheckout\ApiException;
use TebexCheckout\Model\Basket;
use TebexCheckout\Model\CreateBasketRequest;
use InvalidArgumentException;

/**
 * Builds baskets for Checkout API. This should be used to create, validate, and manage baskets.
 */
class BasketBuilder
{
    // Required parameters
    private string $_email;
    private string $_firstname;
    private string $_lastname;
    private string $_returnUrl;
    private string $_completeUrl;

    private array $_custom = [];

    // Optional or filled by API
    private string $_ip;
    private bool $_completeAutoRedirect = true;
    private string $_creatorCode;

    private function __construct()
    {

    }

    /**
     * Creates a new BasketBuilder instance for dynamically creating new baskets.
     * @return BasketBuilder
     */
    public static function new() : BasketBuilder
    {
        return new BasketBuilder();
    }

    /**
     * Builds the basket, creating it remotely on Tebex.
     *
     * @return Basket The newly created Basket instance.
     * @throws ApiException | InvalidArgumentException
     */
    public function build(): Basket
    {
        $missingParams = [];
        if (empty($this->_email)) {
            $missingParams[] = 'email';
        }
        if (empty($this->_returnUrl)) {
            $missingParams[] = 'return_url';
        }
        if (empty($this->_completeUrl)) {
            $missingParams[] = 'complete_url';
        }
        if (empty($this->_firstname)) {
            $missingParams[] = 'firstname';
        }
        if (empty($this->_lastname)) {
            $missingParams[] = 'lastname';
        }
        if (!empty($missingParams)) {
            throw new InvalidArgumentException("The following required basket parameters are missing: " . implode(', ', $missingParams));
        }

        $basketCreateData = [];
        $basketCreateData['email'] = $this->_email;
        $basketCreateData['return_url'] = $this->_returnUrl;
        $basketCreateData['complete_url'] = $this->_completeUrl;
        $basketCreateData['first_name'] = $this->_firstname;
        $basketCreateData['last_name'] = $this->_lastname;
        $basketCreateData['custom'] = $this->_custom;
        $basketCreateData['completeAutoRedirect'] = $this->_completeAutoRedirect;

        // fill optionals
        if (!empty($this->_ip)) {
            $basketCreateData['ip'] = $this->_ip;
        }

        if (!empty($this->_creatorCode)) {
            $basketCreateData['creator_code'] = $this->_creatorCode;
        }
        // Pass to OpenAPI SDK
        $createBasketRequest = new CreateBasketRequest($basketCreateData);
        return Checkout::createBasket($createBasketRequest);
    }

    /**
     * Sets the customer email address for the basket builder.
     *
     * @param string $email The email address to be set.
     * @return BasketBuilder Returns the instance of the BasketBuilder.
     */
    public function email(string $email) : BasketBuilder {
        $this->_email = $email;
        return $this;
    }

    /**
     * Sets the first name associated with the basket
     *
     * @param string $firstname The first name to be set.
     * @return BasketBuilder Returns the current instance of BasketBuilder.
     */
    public function firstname(string $firstname) : BasketBuilder {
        $this->_firstname = $firstname;
        return $this;
    }

    /**
     * Sets the last name associated with the basket
     *
     * @param string $lastname The last name to be set.
     * @return BasketBuilder Returns the current instance of BasketBuilder.
     */
    public function lastname(string $lastname) : BasketBuilder {
        $this->_lastname = $lastname;
        return $this;
    }

    /**
     * Sets the return URL and returns the BasketBuilder instance.
     *
     * @param string $returnUrl The URL to be used for returning.
     * @return BasketBuilder The current instance of BasketBuilder.
     */
    public function returnUrl(string $returnUrl) : BasketBuilder {
        $this->_returnUrl = $returnUrl;
        return $this;
    }

    /**
     * Sets the complete URL and returns the BasketBuilder instance.
     *
     * @param string $completeUrl The URL to be used for completion.
     * @return BasketBuilder The current instance of BasketBuilder.
     */
    public function completeUrl(string $completeUrl) : BasketBuilder
    {
        $this->_completeUrl = $completeUrl;
        return $this;
    }

    /**
     * Sets custom data attached to the basket.
     *
     * @param array $data The custom data to be set.
     * @return BasketBuilder The current instance of BasketBuilder.
     */
    public function custom(array $data) : BasketBuilder
    {
        $this->_custom = $data;
        return $this;
    }

    /**
     * If true, auto-redirects the user to the basket's return URL after payment is completed.
     *
     * @param bool $value The value indicating whether to enable or disable auto-redirect completion.
     * @return BasketBuilder The current instance of BasketBuilder.
     */
    public function completeAutoRedirect(bool $value) : BasketBuilder {
        $this->_completeAutoRedirect = $value;
        return $this;
    }

    /**
     * Sets the creator code for the basket.
     *
     * @param string $creatorCode The code associated with the creator.
     * @return BasketBuilder Returns the current instance of the BasketBuilder.
     */
    public function creatorCode(string $creatorCode) : BasketBuilder {
        $this->_creatorCode = $creatorCode;
        return $this;
    }

    /**
     * Sets the IP address for the basket. If an IP is not provided, the origin IP of the backend server
     * will be used.
     *
     * IMPORTANT: Failure to set IP to your customer IP will cause false positives with Tebex fraud protection.
     *
     * @param string $ip The IP address to be assigned to the basket.
     * @return BasketBuilder Returns the current instance of the BasketBuilder.
     */
    public function ip(string $ip) : BasketBuilder {
        $this->_ip = $ip;
        return $this;
    }

    /**
     * Returns the email currently configured in the builder.
     *
     * @return string The current email.
     */
    public function getEmail(): string
    {
        return $this->_email;
    }

    /**
     * Returns the first name currently configured in the builder.
     *
     * @return string The current first name.
     */
    public function getFirstname(): string
    {
        return $this->_firstname;
    }

    /**
     * Returns the last name currently configured in the builder.
     *
     * @return string The current last name.
     */
    public function getLastname(): string
    {
        return $this->_lastname;
    }

    /**
     * Returns the return URL currently configured in the builder.
     *
     * @return string The current return URL.
     */
    public function getReturnUrl(): string
    {
        return $this->_returnUrl;
    }

    /**
     * Returns the complete URL currently configured in the builder.
     *
     * @return string The current complete URL.
     */
    public function getCompleteUrl(): string
    {
        return $this->_completeUrl;
    }

    /**
     * Returns the custom data currently configured in the builder.
     *
     * @return array The current custom data.
     */
    public function getCustom(): array
    {
        return $this->_custom;
    }

    /**
     * Returns the IP address currently configured in the builder.
     *
     * @return string The current IP address.
     */
    public function getIp(): string
    {
        return $this->_ip;
    }

    /**
     * Returns the complete auto-redirect flag currently configured in the builder.
     *
     * @return bool The current complete auto-redirect flag.
     */
    public function getCompleteAutoRedirect(): bool
    {
        return $this->_completeAutoRedirect;
    }

    /**
     * Returns the creator code currently configured in the builder.
     *
     * @return string The current creator code.
     */
    public function getCreatorCode(): string
    {
        return $this->_creatorCode;
    }
}