<?php
/**
 * RecurringPayment
 *
 * PHP version 7.4
 *
 * @category Class
 * @package  TebexCheckout
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */

/**
 * Tebex Checkout API
 *
 * The Checkout APIs are designed to allow our creators to use the Tebex Checkout flow and payment acceptance capabilities without the need to set up a Tebex-powered webstore. Using these APIs allows you to create baskets with custom products (as opposed to pre-created products on our webstore platform), and send customers directly to the checkout flow to proceed with payment options.  You must receive prior authorisation before the Checkout API is enabled on your account. Please contact customer support or your account manager to discover if you qualify to use the Checkout API before beginning integration.
 *
 * The version of the OpenAPI document: 1.1.2
 * Contact: tebex-integrations@overwolf.com
 * Generated by: https://openapi-generator.tech
 * Generator version: 7.5.0
 */

/**
 * NOTE: This class is auto generated by OpenAPI Generator (https://openapi-generator.tech).
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */

namespace TebexCheckout\Model;

use \ArrayAccess;
use \TebexCheckout\ObjectSerializer;

/**
 * RecurringPayment Class Doc Comment
 *
 * @category Class
 * @package  TebexCheckout
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 * @implements \ArrayAccess<string, mixed>
 */
class RecurringPayment implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'RecurringPayment';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'id' => 'int',
        'created_at' => '\DateTime',
        'updated_at' => '\DateTime',
        'paused_at' => '\DateTime',
        'paused_until' => '\DateTime',
        'next_payment_date' => 'string',
        'reference' => 'string',
        'account_id' => 'int',
        'interval' => 'string',
        'cancelled_at' => '\DateTime',
        'cancellation_requested_at' => '\DateTime',
        'status' => '\TebexCheckout\Model\RecurringPaymentStatus',
        'amount' => '\TebexCheckout\Model\RecurringPaymentAmount',
        'cancel_reason' => 'string',
        'links' => '\TebexCheckout\Model\RecurringPaymentLinks'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      * @phpstan-var array<string, string|null>
      * @psalm-var array<string, string|null>
      */
    protected static $openAPIFormats = [
        'id' => null,
        'created_at' => 'date-time',
        'updated_at' => 'date-time',
        'paused_at' => 'date-time',
        'paused_until' => 'date-time',
        'next_payment_date' => null,
        'reference' => null,
        'account_id' => null,
        'interval' => null,
        'cancelled_at' => 'date-time',
        'cancellation_requested_at' => 'date-time',
        'status' => null,
        'amount' => null,
        'cancel_reason' => null,
        'links' => null
    ];

    /**
      * Array of nullable properties. Used for (de)serialization
      *
      * @var boolean[]
      */
    protected static array $openAPINullables = [
        'id' => false,
        'created_at' => false,
        'updated_at' => false,
        'paused_at' => true,
        'paused_until' => true,
        'next_payment_date' => false,
        'reference' => false,
        'account_id' => false,
        'interval' => false,
        'cancelled_at' => true,
        'cancellation_requested_at' => true,
        'status' => false,
        'amount' => false,
        'cancel_reason' => true,
        'links' => false
    ];

    /**
      * If a nullable field gets set to null, insert it here
      *
      * @var boolean[]
      */
    protected array $openAPINullablesSetToNull = [];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function openAPITypes()
    {
        return self::$openAPITypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function openAPIFormats()
    {
        return self::$openAPIFormats;
    }

    /**
     * Array of nullable properties
     *
     * @return array
     */
    protected static function openAPINullables(): array
    {
        return self::$openAPINullables;
    }

    /**
     * Array of nullable field names deliberately set to null
     *
     * @return boolean[]
     */
    private function getOpenAPINullablesSetToNull(): array
    {
        return $this->openAPINullablesSetToNull;
    }

    /**
     * Setter - Array of nullable field names deliberately set to null
     *
     * @param boolean[] $openAPINullablesSetToNull
     */
    private function setOpenAPINullablesSetToNull(array $openAPINullablesSetToNull): void
    {
        $this->openAPINullablesSetToNull = $openAPINullablesSetToNull;
    }

    /**
     * Checks if a property is nullable
     *
     * @param string $property
     * @return bool
     */
    public static function isNullable(string $property): bool
    {
        return self::openAPINullables()[$property] ?? false;
    }

    /**
     * Checks if a nullable property is set to null.
     *
     * @param string $property
     * @return bool
     */
    public function isNullableSetToNull(string $property): bool
    {
        return in_array($property, $this->getOpenAPINullablesSetToNull(), true);
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'id' => 'id',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at',
        'paused_at' => 'paused_at',
        'paused_until' => 'paused_until',
        'next_payment_date' => 'next_payment_date',
        'reference' => 'reference',
        'account_id' => 'account_id',
        'interval' => 'interval',
        'cancelled_at' => 'cancelled_at',
        'cancellation_requested_at' => 'cancellation_requested_at',
        'status' => 'status',
        'amount' => 'amount',
        'cancel_reason' => 'cancel_reason',
        'links' => 'links'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'id' => 'setId',
        'created_at' => 'setCreatedAt',
        'updated_at' => 'setUpdatedAt',
        'paused_at' => 'setPausedAt',
        'paused_until' => 'setPausedUntil',
        'next_payment_date' => 'setNextPaymentDate',
        'reference' => 'setReference',
        'account_id' => 'setAccountId',
        'interval' => 'setInterval',
        'cancelled_at' => 'setCancelledAt',
        'cancellation_requested_at' => 'setCancellationRequestedAt',
        'status' => 'setStatus',
        'amount' => 'setAmount',
        'cancel_reason' => 'setCancelReason',
        'links' => 'setLinks'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'id' => 'getId',
        'created_at' => 'getCreatedAt',
        'updated_at' => 'getUpdatedAt',
        'paused_at' => 'getPausedAt',
        'paused_until' => 'getPausedUntil',
        'next_payment_date' => 'getNextPaymentDate',
        'reference' => 'getReference',
        'account_id' => 'getAccountId',
        'interval' => 'getInterval',
        'cancelled_at' => 'getCancelledAt',
        'cancellation_requested_at' => 'getCancellationRequestedAt',
        'status' => 'getStatus',
        'amount' => 'getAmount',
        'cancel_reason' => 'getCancelReason',
        'links' => 'getLinks'
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$openAPIModelName;
    }


    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->setIfExists('id', $data ?? [], null);
        $this->setIfExists('created_at', $data ?? [], null);
        $this->setIfExists('updated_at', $data ?? [], null);
        $this->setIfExists('paused_at', $data ?? [], null);
        $this->setIfExists('paused_until', $data ?? [], null);
        $this->setIfExists('next_payment_date', $data ?? [], null);
        $this->setIfExists('reference', $data ?? [], null);
        $this->setIfExists('account_id', $data ?? [], null);
        $this->setIfExists('interval', $data ?? [], null);
        $this->setIfExists('cancelled_at', $data ?? [], null);
        $this->setIfExists('cancellation_requested_at', $data ?? [], null);
        $this->setIfExists('status', $data ?? [], null);
        $this->setIfExists('amount', $data ?? [], null);
        $this->setIfExists('cancel_reason', $data ?? [], null);
        $this->setIfExists('links', $data ?? [], null);
    }

    /**
    * Sets $this->container[$variableName] to the given data or to the given default Value; if $variableName
    * is nullable and its value is set to null in the $fields array, then mark it as "set to null" in the
    * $this->openAPINullablesSetToNull array
    *
    * @param string $variableName
    * @param array  $fields
    * @param mixed  $defaultValue
    */
    private function setIfExists(string $variableName, array $fields, $defaultValue): void
    {
        if (self::isNullable($variableName) && array_key_exists($variableName, $fields) && is_null($fields[$variableName])) {
            $this->openAPINullablesSetToNull[] = $variableName;
        }

        $this->container[$variableName] = $fields[$variableName] ?? $defaultValue;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        return $invalidProperties;
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        return count($this->listInvalidProperties()) === 0;
    }


    /**
     * Gets id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->container['id'];
    }

    /**
     * Sets id
     *
     * @param int|null $id id
     *
     * @return self
     */
    public function setId($id)
    {
        if (is_null($id)) {
            throw new \InvalidArgumentException('non-nullable id cannot be null');
        }
        $this->container['id'] = $id;

        return $this;
    }

    /**
     * Gets created_at
     *
     * @return \DateTime|null
     */
    public function getCreatedAt()
    {
        return $this->container['created_at'];
    }

    /**
     * Sets created_at
     *
     * @param \DateTime|null $created_at created_at
     *
     * @return self
     */
    public function setCreatedAt($created_at)
    {
        if (is_null($created_at)) {
            throw new \InvalidArgumentException('non-nullable created_at cannot be null');
        }
        $this->container['created_at'] = $created_at;

        return $this;
    }

    /**
     * Gets updated_at
     *
     * @return \DateTime|null
     */
    public function getUpdatedAt()
    {
        return $this->container['updated_at'];
    }

    /**
     * Sets updated_at
     *
     * @param \DateTime|null $updated_at updated_at
     *
     * @return self
     */
    public function setUpdatedAt($updated_at)
    {
        if (is_null($updated_at)) {
            throw new \InvalidArgumentException('non-nullable updated_at cannot be null');
        }
        $this->container['updated_at'] = $updated_at;

        return $this;
    }

    /**
     * Gets paused_at
     *
     * @return \DateTime|null
     */
    public function getPausedAt()
    {
        return $this->container['paused_at'];
    }

    /**
     * Sets paused_at
     *
     * @param \DateTime|null $paused_at paused_at
     *
     * @return self
     */
    public function setPausedAt($paused_at)
    {
        if (is_null($paused_at)) {
            array_push($this->openAPINullablesSetToNull, 'paused_at');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('paused_at', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['paused_at'] = $paused_at;

        return $this;
    }

    /**
     * Gets paused_until
     *
     * @return \DateTime|null
     */
    public function getPausedUntil()
    {
        return $this->container['paused_until'];
    }

    /**
     * Sets paused_until
     *
     * @param \DateTime|null $paused_until paused_until
     *
     * @return self
     */
    public function setPausedUntil($paused_until)
    {
        if (is_null($paused_until)) {
            array_push($this->openAPINullablesSetToNull, 'paused_until');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('paused_until', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['paused_until'] = $paused_until;

        return $this;
    }

    /**
     * Gets next_payment_date
     *
     * @return string|null
     */
    public function getNextPaymentDate()
    {
        return $this->container['next_payment_date'];
    }

    /**
     * Sets next_payment_date
     *
     * @param string|null $next_payment_date next_payment_date
     *
     * @return self
     */
    public function setNextPaymentDate($next_payment_date)
    {
        if (is_null($next_payment_date)) {
            throw new \InvalidArgumentException('non-nullable next_payment_date cannot be null');
        }
        $this->container['next_payment_date'] = $next_payment_date;

        return $this;
    }

    /**
     * Gets reference
     *
     * @return string|null
     */
    public function getReference()
    {
        return $this->container['reference'];
    }

    /**
     * Sets reference
     *
     * @param string|null $reference reference
     *
     * @return self
     */
    public function setReference($reference)
    {
        if (is_null($reference)) {
            throw new \InvalidArgumentException('non-nullable reference cannot be null');
        }
        $this->container['reference'] = $reference;

        return $this;
    }

    /**
     * Gets account_id
     *
     * @return int|null
     */
    public function getAccountId()
    {
        return $this->container['account_id'];
    }

    /**
     * Sets account_id
     *
     * @param int|null $account_id account_id
     *
     * @return self
     */
    public function setAccountId($account_id)
    {
        if (is_null($account_id)) {
            throw new \InvalidArgumentException('non-nullable account_id cannot be null');
        }
        $this->container['account_id'] = $account_id;

        return $this;
    }

    /**
     * Gets interval
     *
     * @return string|null
     */
    public function getInterval()
    {
        return $this->container['interval'];
    }

    /**
     * Sets interval
     *
     * @param string|null $interval interval
     *
     * @return self
     */
    public function setInterval($interval)
    {
        if (is_null($interval)) {
            throw new \InvalidArgumentException('non-nullable interval cannot be null');
        }
        $this->container['interval'] = $interval;

        return $this;
    }

    /**
     * Gets cancelled_at
     *
     * @return \DateTime|null
     */
    public function getCancelledAt()
    {
        return $this->container['cancelled_at'];
    }

    /**
     * Sets cancelled_at
     *
     * @param \DateTime|null $cancelled_at cancelled_at
     *
     * @return self
     */
    public function setCancelledAt($cancelled_at)
    {
        if (is_null($cancelled_at)) {
            array_push($this->openAPINullablesSetToNull, 'cancelled_at');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('cancelled_at', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['cancelled_at'] = $cancelled_at;

        return $this;
    }

    /**
     * Gets cancellation_requested_at
     *
     * @return \DateTime|null
     */
    public function getCancellationRequestedAt()
    {
        return $this->container['cancellation_requested_at'];
    }

    /**
     * Sets cancellation_requested_at
     *
     * @param \DateTime|null $cancellation_requested_at cancellation_requested_at
     *
     * @return self
     */
    public function setCancellationRequestedAt($cancellation_requested_at)
    {
        if (is_null($cancellation_requested_at)) {
            array_push($this->openAPINullablesSetToNull, 'cancellation_requested_at');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('cancellation_requested_at', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['cancellation_requested_at'] = $cancellation_requested_at;

        return $this;
    }

    /**
     * Gets status
     *
     * @return \TebexCheckout\Model\RecurringPaymentStatus|null
     */
    public function getStatus()
    {
        return $this->container['status'];
    }

    /**
     * Sets status
     *
     * @param \TebexCheckout\Model\RecurringPaymentStatus|null $status status
     *
     * @return self
     */
    public function setStatus($status)
    {
        if (is_null($status)) {
            throw new \InvalidArgumentException('non-nullable status cannot be null');
        }
        $this->container['status'] = $status;

        return $this;
    }

    /**
     * Gets amount
     *
     * @return \TebexCheckout\Model\RecurringPaymentAmount|null
     */
    public function getAmount()
    {
        return $this->container['amount'];
    }

    /**
     * Sets amount
     *
     * @param \TebexCheckout\Model\RecurringPaymentAmount|null $amount amount
     *
     * @return self
     */
    public function setAmount($amount)
    {
        if (is_null($amount)) {
            throw new \InvalidArgumentException('non-nullable amount cannot be null');
        }
        $this->container['amount'] = $amount;

        return $this;
    }

    /**
     * Gets cancel_reason
     *
     * @return string|null
     */
    public function getCancelReason()
    {
        return $this->container['cancel_reason'];
    }

    /**
     * Sets cancel_reason
     *
     * @param string|null $cancel_reason cancel_reason
     *
     * @return self
     */
    public function setCancelReason($cancel_reason)
    {
        if (is_null($cancel_reason)) {
            array_push($this->openAPINullablesSetToNull, 'cancel_reason');
        } else {
            $nullablesSetToNull = $this->getOpenAPINullablesSetToNull();
            $index = array_search('cancel_reason', $nullablesSetToNull);
            if ($index !== FALSE) {
                unset($nullablesSetToNull[$index]);
                $this->setOpenAPINullablesSetToNull($nullablesSetToNull);
            }
        }
        $this->container['cancel_reason'] = $cancel_reason;

        return $this;
    }

    /**
     * Gets links
     *
     * @return \TebexCheckout\Model\RecurringPaymentLinks|null
     */
    public function getLinks()
    {
        return $this->container['links'];
    }

    /**
     * Sets links
     *
     * @param \TebexCheckout\Model\RecurringPaymentLinks|null $links links
     *
     * @return self
     */
    public function setLinks($links)
    {
        if (is_null($links)) {
            throw new \InvalidArgumentException('non-nullable links cannot be null');
        }
        $this->container['links'] = $links;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    public function offsetExists($offset): bool
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed|null
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->container[$offset] ?? null;
    }

    /**
     * Sets value based on offset.
     *
     * @param int|null $offset Offset
     * @param mixed    $value  Value to be set
     *
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param integer $offset Offset
     *
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->container[$offset]);
    }

    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     * @link https://www.php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed Returns data which can be serialized by json_encode(), which is a value
     * of any type other than a resource.
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
       return ObjectSerializer::sanitizeForSerialization($this);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        return json_encode(
            ObjectSerializer::sanitizeForSerialization($this),
            JSON_PRETTY_PRINT
        );
    }

    /**
     * Gets a header-safe presentation of the object
     *
     * @return string
     */
    public function toHeaderValue()
    {
        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}


