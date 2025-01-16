<?php

namespace Tebex\Checkout;

use TebexCheckout\Model\CheckoutItem;
use TebexCheckout\Model\Package;
use InvalidArgumentException;

/**
 * Builds packages for Checkout API. Allows you to define the name, price, quantity, etc. for bespoke packages not defined
 * in a Tebex webstore.
 */
class PackageBuilder
{
    private string $_name;
    private float $_price;
    private string $_type;
    private int $_qty;
    private string $_expiryPeriod;
    private int $_expiryLength;
    private object $_custom;

    private function __construct()
    {
    }

    /**
     * Creates a new PackageBuilder used to define ad-hoc packages used in a Checkout API basket.
     * @return PackageBuilder
     */
    public static function new(): PackageBuilder
    {
        return new PackageBuilder();
    }

    /**
     * Builds the package as a CheckoutItem, which can be used in the all-in-one checkoutRequest.
     *
     * @return CheckoutItem A wrapped "package"
     */
    public function buildCheckoutItem(): CheckoutItem
    {
        return new CheckoutItem([
            "package" => $this->buildPackage()
        ]);
    }

    /**
     * Builds the package as a Package, which is the model more appropriate for display. Required parameters are automatically validated.
     *
     * @return Package
     * @throws InvalidArgumentException A required parameter is missing from the package builder.
     */
    public function buildPackage(): Package
    {
        // Verify parameters are not missing
        $missingParams = [];
        if (empty($this->_name)) {
            $missingParams[] = 'name';
        }
        if (empty($this->_price)) {
            $missingParams[] = 'price';
        }
        if (empty($this->_type)) {
            $missingParams[] = 'type';
        }
        if (empty($this->_qty) || $this->_qty < 1) {
            $missingParams[] = 'qty';
        }
        if ($this->_type === 'subscription') {
            if (empty($this->_expiryPeriod)) {
                $missingParams[] = 'expiry_period';
            }
            if (empty($this->_expiryLength)) {
                $missingParams[] = 'expiry_length';
            }
        }

        if (!empty($missingParams)) {
            throw new InvalidArgumentException("The following required package parameters are missing or invalid: " . implode(', ', $missingParams));
        }

        $packageCreateData = [
            'name' => $this->_name,
            'price' => $this->_price,
            'type' => $this->_type,
            'qty' => $this->_qty,
            'expiry_period' => $this->_expiryPeriod ?? null,
            'expiry_length' => $this->_expiryLength ?? null,
            'custom' => $this->_custom ?? null,
        ];

        // Pass to underlying OpenAPI model
        return new Package($packageCreateData);
    }

    /**
     * Sets the package name, returning the builder instance.
     *
     * @param string $name The package name.
     * @return PackageBuilder
     */
    public function name(string $name): PackageBuilder
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * Sets the package price, returning the builder instance.
     *
     * @param float $price The package price.
     * @return PackageBuilder
     */
    public function price(float $price): PackageBuilder
    {
        $this->_price = $price;
        return $this;
    }

    /**
     * Sets the type of the package. See also @see PackageBuilder::oneTime() and @see PackageBuilder::subscription()
     *
     * @param string $type The type of the package, either 'single' or 'subscription'.
     * @return PackageBuilder The current instance of the PackageBuilder for method chaining.
     */
    public function type(string $type): PackageBuilder
    {
        $this->_type = $type;
        return $this;
    }

    /**
     * Sets the type of the package to 'single', indicating a one-time purchase package.
     *
     * @return PackageBuilder The current instance of the PackageBuilder for method chaining.
     */
    public function oneTime(): PackageBuilder
    {
        $this->_type = 'single';
        return $this;
    }

    /**
     * Sets the package type to 'subscription'.
     *
     * @return PackageBuilder The current instance of the PackageBuilder for method chaining.
     */
    public function subscription(): PackageBuilder
    {
        $this->_type = 'subscription';
        return $this;
    }

    /**
     * Sets the quantity for the package.
     *
     * @param int $qty The quantity to be set for the package.
     * @return PackageBuilder The current instance of the PackageBuilder for method chaining.
     */
    public function qty(int $qty): PackageBuilder
    {
        $this->_qty = $qty;
        return $this;
    }

    /**
     * Sets the expiration period used against the expiry length.
     *
     * @param string $expiryPeriod 'month', 'year', or 'day'
     * @return $this
     */
    public function expiryPeriod(string $expiryPeriod): PackageBuilder
    {
        switch ($expiryPeriod) {
            case "day":
            case "year":
            case "month":
                $this->_expiryPeriod = $expiryPeriod;
                break;
            default:
                throw new InvalidArgumentException("Invalid expiry period: " . $expiryPeriod . ". Must be 'month', 'year' or 'day'.");
        }
        return $this;
    }

    /**
     * Sets the expiry length of the package.
     *
     * @param int $expiryLength The length of time until the package expires. @see PackageBuilder::expiryPeriod() to get
     * timeframe of expiration.
     *
     * @return PackageBuilder The current instance of the PackageBuilder for method chaining.
     */
    public function expiryLength(int $expiryLength): PackageBuilder
    {
        $this->_expiryLength = $expiryLength;
        return $this;
    }

    /**
     * Sets a custom data payload for the package. The array provided is converted to a JSON object.
     *
     * @param array $custom The array containing custom data to include with the package.
     * @return PackageBuilder The current instance of the PackageBuilder for method chaining.
     */
    public function custom(array $custom): PackageBuilder
    {
        $this->_custom = json_decode(json_encode($custom), false);
        return $this;
    }

    /**
     * Sets the package as a monthly subscription (each month).
     * @return $this
     */
    public function monthly() : PackageBuilder {
        $this->_type = 'subscription';
        $this->_expiryPeriod = 'month';
        $this->_expiryLength = 1;
        return $this;
    }

    /**
     * Sets the package as a quarterly subscription (each 3 months)
     * @return $this
     */
    public function quarterly() : PackageBuilder {
        $this->_type = 'subscription';
        $this->_expiryPeriod = 'month';
        $this->_expiryLength = 3;
        return $this;
    }

    /**
     * Sets the package as a semi-annual subscription (each 6 months)
     * @return $this
     */
    public function semiAnnual() : PackageBuilder {
        $this->_type = 'subscription';
        $this->_expiryPeriod = 'month';
        $this->_expiryLength = 6;
        return $this;
    }

    /**
     * Sets the package as a yearly subscription (each year)
     * @return $this
     */
    public function yearly() : PackageBuilder {
        $this->_type = 'subscription';
        $this->_expiryPeriod = 'year';
        $this->_expiryLength = 1;
        return $this;
    }

    /**
     * Retrieves the currently set name as a string.
     *
     * @return string The name value.
     */
    public function getName(): string
    {
        return $this->_name;
    }

    /**
     * Retrieves the currently set price.
     *
     * @return float The price value.
     */
    public function getPrice(): float
    {
        return $this->_price;
    }

    /**
     * Retrieves the package type as a string.
     *
     * @return string The type string
     */
    public function getType(): string
    {
        return $this->_type;
    }

    /**
     * Retrieves the currently set quantity.
     *
     * @return int The quantity value.
     */
    public function getQty(): int
    {
        return $this->_qty;
    }

    /**
     * Retrieves the currently set expiry period.
     *
     * @return string The expiry period.
     */
    public function getExpiryPeriod(): string
    {
        return $this->_expiryPeriod;
    }

    /**
     * Retrieves the currently set expiry length value.
     *
     * @return int The expiry length.
     */
    public function getExpiryLength(): int
    {
        return $this->_expiryLength;
    }

    /**
     * Retrieves the custom object associated with this instance.
     *
     * @return object The custom object.
     */
    public function getCustom(): object
    {
        return $this->_custom;
    }
}