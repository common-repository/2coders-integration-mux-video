<?php
/**
 * Upload
 *
 * PHP version 7.2
 *
 * @category Class
 * @package  MuxPhp
 * @author   Mux API team
 * @link     https://docs.mux.com
 */

/**
 * Mux API
 *
 * Mux is how developers build online video. This API encompasses both Mux Video and Mux Data functionality to help you build your video-related projects better and faster than ever before.
 *
 * The version of the OpenAPI document: v1
 * Contact: devex@mux.com
 * Generated by: https://openapi-generator.tech
 * OpenAPI Generator version: 5.0.1
 */

/**
 * NOTE: This class is auto generated by OpenAPI Generator (https://openapi-generator.tech).
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */

namespace MuxPhp\Models;

use \ArrayAccess;
use \MuxPhp\ObjectSerializer;

/**
 * Upload Class Doc Comment
 *
 * @category Class
 * @package  MuxPhp
 * @author   Mux API team
 * @link     https://docs.mux.com
 * @implements \ArrayAccess<TKey, TValue>
 * @template TKey int|null
 * @template TValue mixed|null  
 */
class Upload implements ModelInterface, ArrayAccess, \JsonSerializable
{
    public const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $openAPIModelName = 'Upload';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $openAPITypes = [
        'id' => 'string',
        'timeout' => 'int',
        'status' => 'string',
        'new_asset_settings' => '\MuxPhp\Models\Asset',
        'asset_id' => 'string',
        'error' => '\MuxPhp\Models\UploadError',
        'cors_origin' => 'string',
        'url' => 'string',
        'test' => 'bool'
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
        'timeout' => 'int32',
        'status' => null,
        'new_asset_settings' => null,
        'asset_id' => null,
        'error' => null,
        'cors_origin' => null,
        'url' => null,
        'test' => 'boolean'
    ];

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
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        'id' => 'id',
        'timeout' => 'timeout',
        'status' => 'status',
        'new_asset_settings' => 'new_asset_settings',
        'asset_id' => 'asset_id',
        'error' => 'error',
        'cors_origin' => 'cors_origin',
        'url' => 'url',
        'test' => 'test'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        'id' => 'setId',
        'timeout' => 'setTimeout',
        'status' => 'setStatus',
        'new_asset_settings' => 'setNewAssetSettings',
        'asset_id' => 'setAssetId',
        'error' => 'setError',
        'cors_origin' => 'setCorsOrigin',
        'url' => 'setUrl',
        'test' => 'setTest'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        'id' => 'getId',
        'timeout' => 'getTimeout',
        'status' => 'getStatus',
        'new_asset_settings' => 'getNewAssetSettings',
        'asset_id' => 'getAssetId',
        'error' => 'getError',
        'cors_origin' => 'getCorsOrigin',
        'url' => 'getUrl',
        'test' => 'getTest'
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

    public const STATUS_WAITING = 'waiting';
    public const STATUS_ASSET_CREATED = 'asset_created';
    public const STATUS_ERRORED = 'errored';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_TIMED_OUT = 'timed_out';
    

    
    /**
     * Gets allowable values of the enum
     *
     * @return string[]
     */
    public function getStatusAllowableValues()
    {
        return [
            self::STATUS_WAITING,
            self::STATUS_ASSET_CREATED,
            self::STATUS_ERRORED,
            self::STATUS_CANCELLED,
            self::STATUS_TIMED_OUT,
        ];
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
        // MUX: enum hack (self::) due to OAS emitting problems.
        //      please re-integrate with mainline when possible.
        //      src: https://github.com/OpenAPITools/openapi-generator/issues/9038
        $this->container['id'] = $data['id'] ?? null;
        $this->container['timeout'] = $data['timeout'] ?? 3600;
        $this->container['status'] = $data['status'] ?? null;
        $this->container['new_asset_settings'] = $data['new_asset_settings'] ?? null;
        $this->container['asset_id'] = $data['asset_id'] ?? null;
        $this->container['error'] = $data['error'] ?? null;
        $this->container['cors_origin'] = $data['cors_origin'] ?? null;
        $this->container['url'] = $data['url'] ?? null;
        $this->container['test'] = $data['test'] ?? null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        if (!is_null($this->container['timeout']) && ($this->container['timeout'] > 604800)) {
            $invalidProperties[] = "invalid value for 'timeout', must be smaller than or equal to 604800.";
        }

        if (!is_null($this->container['timeout']) && ($this->container['timeout'] < 60)) {
            $invalidProperties[] = "invalid value for 'timeout', must be bigger than or equal to 60.";
        }

        $allowedValues = $this->getStatusAllowableValues();
        if (!is_null($this->container['status']) && !in_array($this->container['status'], $allowedValues, true)) {
            $invalidProperties[] = sprintf(
                "invalid value '%s' for 'status', must be one of '%s'",
                $this->container['status'],
                implode("', '", $allowedValues)
            );
        }

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
     * @return string|null
     */
    public function getId()
    {
        return $this->container['id'];
    }

    /**
     * Sets id
     *
     * @param string|null $id Unique identifier for the Direct Upload.
     *
     * @return self
     */
    public function setId($id)
    {
        $this->container['id'] = $id;

        return $this;
    }

    /**
     * Gets timeout
     *
     * @return int|null
     */
    public function getTimeout()
    {
        return $this->container['timeout'];
    }

    /**
     * Sets timeout
     *
     * @param int|null $timeout Max time in seconds for the signed upload URL to be valid. If a successful upload has not occurred before the timeout limit, the direct upload is marked `timed_out`
     *
     * @return self
     */
    public function setTimeout($timeout)
    {

        if (!is_null($timeout) && ($timeout > 604800)) {
            throw new \InvalidArgumentException('invalid value for $timeout when calling Upload., must be smaller than or equal to 604800.');
        }
        if (!is_null($timeout) && ($timeout < 60)) {
            throw new \InvalidArgumentException('invalid value for $timeout when calling Upload., must be bigger than or equal to 60.');
        }

        $this->container['timeout'] = $timeout;

        return $this;
    }

    /**
     * Gets status
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->container['status'];
    }

    /**
     * Sets status
     *
     * @param string|null $status status
     *
     * @return self
     */
    public function setStatus($status)
    {
        $allowedValues = $this->getStatusAllowableValues();
        if (!is_null($status) && !in_array($status, $allowedValues, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Invalid value '%s' for 'status', must be one of '%s'",
                    $status,
                    implode("', '", $allowedValues)
                )
            );
        }
        $this->container['status'] = $status;

        return $this;
    }

    /**
     * Gets new_asset_settings
     *
     * @return \MuxPhp\Models\Asset|null
     */
    public function getNewAssetSettings()
    {
        return $this->container['new_asset_settings'];
    }

    /**
     * Sets new_asset_settings
     *
     * @param \MuxPhp\Models\Asset|null $new_asset_settings new_asset_settings
     *
     * @return self
     */
    public function setNewAssetSettings($new_asset_settings)
    {
        $this->container['new_asset_settings'] = $new_asset_settings;

        return $this;
    }

    /**
     * Gets asset_id
     *
     * @return string|null
     */
    public function getAssetId()
    {
        return $this->container['asset_id'];
    }

    /**
     * Sets asset_id
     *
     * @param string|null $asset_id Only set once the upload is in the `asset_created` state.
     *
     * @return self
     */
    public function setAssetId($asset_id)
    {
        $this->container['asset_id'] = $asset_id;

        return $this;
    }

    /**
     * Gets error
     *
     * @return \MuxPhp\Models\UploadError|null
     */
    public function getError()
    {
        return $this->container['error'];
    }

    /**
     * Sets error
     *
     * @param \MuxPhp\Models\UploadError|null $error error
     *
     * @return self
     */
    public function setError($error)
    {
        $this->container['error'] = $error;

        return $this;
    }

    /**
     * Gets cors_origin
     *
     * @return string|null
     */
    public function getCorsOrigin()
    {
        return $this->container['cors_origin'];
    }

    /**
     * Sets cors_origin
     *
     * @param string|null $cors_origin If the upload URL will be used in a browser, you must specify the origin in order for the signed URL to have the correct CORS headers.
     *
     * @return self
     */
    public function setCorsOrigin($cors_origin)
    {
        $this->container['cors_origin'] = $cors_origin;

        return $this;
    }

    /**
     * Gets url
     *
     * @return string|null
     */
    public function getUrl()
    {
        return $this->container['url'];
    }

    /**
     * Sets url
     *
     * @param string|null $url The URL to upload the associated source media to.
     *
     * @return self
     */
    public function setUrl($url)
    {
        $this->container['url'] = $url;

        return $this;
    }

    /**
     * Gets test
     *
     * @return bool|null
     */
    public function getTest()
    {
        return $this->container['test'];
    }

    /**
     * Sets test
     *
     * @param bool|null $test Indicates if this is a test Direct Upload, in which case the Asset that gets created will be a `test` Asset.
     *
     * @return self
     */
    public function setTest($test)
    {
        $this->container['test'] = $test;

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
    public function offsetGet($offset): mixed
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
    public function jsonSerialize(): mixed
    {
       return ObjectSerializer::sanitizeForSerialization($this);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString(): string
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
    public function toHeaderValue(): string
    {
        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}

