<?php

namespace Scrawler\Router\Cache;

use Psr\SimpleCache\CacheInterface;

class FileSystemCache implements CacheInterface
{
    /**
     * runtime cached data storage
     * @var array
     */
    private $cache = null;

    /**
     * cache path
     * @var string
     */
    private $cacheDirectory;

    /**
     * Create a cache instance
     * @param string $cacheDirectory
     * @throws \Exception
     */
    public function __construct($cacheDirectory = '')
    {
        if (empty($cacheDirectory)) {
            $cacheDirectory = sys_get_temp_dir() . DIRECTORY_SEPARATOR . __CLASS__;
        } elseif (preg_match('#^\./#', $cacheDirectory)) {
            $cacheDirectory = preg_replace('#^\./#', '', $cacheDirectory);
            $cacheDirectory = getcwd() . DIRECTORY_SEPARATOR . ltrim($cacheDirectory, DIRECTORY_SEPARATOR);
        }

        if (!is_dir($cacheDirectory)) {
            $uMask = umask(0);
            @mkdir($cacheDirectory, 0755, true);
            umask($uMask);
        }

        if (!is_dir($cacheDirectory) || !is_readable($cacheDirectory)) {
            throw new \Exception('The root path ' . $cacheDirectory . ' is not readable.');
        }

        $this->cacheDirectory = rtrim($cacheDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    /**
     * Store an item
     * @param string $key The key under which to store the value.
     * @param mixed $value The value to store.
     * @param integer $lifetime The expiration time, defaults to 3600
     * @return boolean
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function set(string $key, mixed $value, \DateInterval|int|null $lifetime = 3600) : bool
    {
        if (!$this->isKey($key)) {
            return false;
        }

        if ($data = json_encode(array('lifetime' => time() + $lifetime, 'data' => $value))) {
            if (file_put_contents($this->cacheDirectory . $key, $data) !== false) {
                $this->cache[$key] = $data;
                return true;
            }
        }

        return false;
    }

    /**
     * set a new expiration on an item
     * @param string $key The key under which to store the value.
     * @param integer $lifetime The expiration time, defaults to 3600
     * @return boolean
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function touch($key, $lifetime = 3600)
    {
        if ($data = $this->get($key)) {
            return $this->set($key, $data, $lifetime);
        }

        return false;
    }

    /**
     * returns the item that was previously stored under the key
     * @param string $key The key of the item to retrieve.
     * @param  mixed $default The default value (see @return)
     * @return mixed Returns the value stored in the cache or $default otherwise
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function get(string $key, mixed $default = null):mixed
    {
        if (!$this->isKey($key)) {
            return false;
        }

        $file = $this->cacheDirectory . $key;

        if (isset($this->cache[$key])) {
            $fileData = $this->cache[$key];
        } else {
            $fileData = @file_get_contents($file);
            $this->cache[$key] = $fileData;
        }

        if ($fileData !== false) {
            // check if empty (file with failed write/unlink)
            if (!empty($fileData)) {
                $fileData = json_decode($fileData, true);
                if (isset($fileData['lifetime'], $fileData['data'])) {
                    if ($fileData['lifetime'] >= time()) {
                        return $fileData['data'];
                    } else {
                        $this->deleteFile($file);
                    }
                }
            } else {
                $this->deleteFile($file);
            }
        }

        return $default;
    }

    /**
     * Delete an item
     * @param string $key The key to be deleted.
     * @return boolean Returns TRUE on success or FALSE on failure
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function delete(string $key):bool
    {
        if (!$this->isKey($key)) {
            return false;
        }

        return $this->deleteFile($this->cacheDirectory . $key);
    }

    /**
     * Fetched all the cached data.
     *
     * @return array
     */
    public function all()
    {
        return $this->cache;
    }

    /**
     * Wipes clean the entire cache's keys.
     *
     * @return bool True on success and false on failure.
     */
    public function clear(): bool
    {
        return $this->deleteByPattern('*');
    }

    /**
     * Obtains multiple cache items by their unique keys.
     *
     * @param iterable $keys A list of keys that can obtained in a single operation.
     * @param mixed $default Default value to return for keys that do not exist.
     *
     * @return iterable A list of key => value pairs. Cache keys that do not exist or are stale will have $default as value.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if $keys is neither an array nor a Traversable,
     *   or if any of the $keys are not a legal value.
     */
    public function getMultiple(iterable $keys, mixed $default = null) : iterable
    {
        $values = array();
        foreach ($keys as $key) {
            $values[$key] = $this->get($key, $default);
        }
        return $values;
    }

    /**
     * Persists a set of key => value pairs in the cache, with an optional TTL.
     *
     * @param iterable $values A list of key => value pairs for a multiple-set operation.
     * @param null|int|\DateInterval $ttl Optional. The TTL value of this item. If no value is sent and
     *                                       the driver supports TTL then the library may set a default value
     *                                       for it or let the driver take care of that.
     *
     * @return bool True on success and false on failure.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if $values is neither an array nor a Traversable,
     *   or if any of the $values are not a legal value.
     */
    public function setMultiple(iterable $values, \DateInterval|int|null $ttl = null) : bool
    {
        $return = false;
        foreach ($values as $key => $value) {
            $return = $this->set($key, $value, $ttl) || $return;
        }
        return $return;
    }

    /**
     * Deletes multiple cache items in a single operation.
     *
     * @param iterable $keys A list of string-based keys to be deleted.
     *
     * @return bool True if the items were successfully removed. False if there was an error.
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if $keys is neither an array nor a Traversable,
     *   or if any of the $keys are not a legal value.
     */
    public function deleteMultiple(iterable $keys): bool
    {
        $return = false;
        foreach ($keys as $key) {
            $values[$key] = $this->delete($key);
        }
        return $return;
    }

    /**
     * Determines whether an item is present in the cache.
     *
     * NOTE: It is recommended that has() is only to be used for cache warming type purposes
     * and not to be used within your live applications operations for get/set, as this method
     * is subject to a race condition where your has() will return true and immediately after,
     * another script can remove it making the state of your app out of date.
     *
     * @param string $key The cache item key.
     *
     * @return bool
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *   MUST be thrown if the $key string is not a legal value.
     */
    public function has(string $key):bool
    {
        $value = $this->get($key);
        return !empty($value);
    }

    /**
     * Delete item matching pattern sintax
     * @param string $pattern The pattern (@see glob())
     * @return bool Returns TRUE on success or FALSE on failure
     */
    public function deleteByPattern($pattern = '*')
    {
        $return = true;

        foreach (glob($this->cacheDirectory . $pattern, GLOB_NOSORT | GLOB_BRACE) as $cacheFile) {
            if (!$this->deleteFile($cacheFile)) {
                $return = false;
            }
        }

        return $return;
    }

    /**
     * check if $key is valid key name
     * @param string $key The key to validate
     * @return boolean Returns TRUE if valid key or FALSE otherwise
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    private function isKey($key)
    {
        try {
            return !preg_match('/[^a-z_\-0-9]/i', $key);
        } catch (\Exception $exception) {
            throw new Exception($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * delete a file
     * @param string $cacheFile
     * @return bool
     */
    private function deleteFile($cacheFile)
    {
        unset($this->cache[basename($cacheFile)]);

        clearstatcache(true, $cacheFile);

        if (file_exists($cacheFile)) {
            if (is_file($cacheFile) && !@unlink($cacheFile)) {
                return (file_put_contents($cacheFile, '') !== false);
            }

            clearstatcache(true, $cacheFile);
        }

        return true;
    }
}
