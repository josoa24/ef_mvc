<?php
/**
 * Flight: An extensible micro-framework.
 *
 * @copyright   Copyright (c) 2011, Mike Cao <mike@mikecao.com>
 * @license     MIT, http://flightphp.com/license
 */

namespace flight\util;

if (!interface_exists('JsonSerializable')) {
    require_once dirname(__FILE__) . '/LegacyJsonSerializable.php';
}

/**
 * The Collection class allows you to access a set of data
 * using both array and object notation.
 */
class Collection implements \ArrayAccess, \Iterator, \Countable, \JsonSerializable {
    /**
     * Collection data.
     *
     * @var array
     */
    private $data;

    /**
     * Constructor.
     *
     * @param array $data Initial data
     */
    public function __construct(array $data = array()) {
        $this->data = $data;
    }

    public function __get($key) {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function __set($key, $value) {
        $this->data[$key] = $value;
    }

    public function __isset($key) {
        return isset($this->data[$key]);
    }

    public function __unset($key) {
        unset($this->data[$key]);
    }

    // ---------- ArrayAccess ----------
    #[\ReturnTypeWillChange]
    public function offsetGet($offset) {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->data[] = $value;
        }
        else {
            $this->data[$offset] = $value;
        }
    }

    #[\ReturnTypeWillChange]
    public function offsetExists($offset) {
        return isset($this->data[$offset]);
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($offset) {
        unset($this->data[$offset]);
    }

    // ---------- Iterator ----------
    #[\ReturnTypeWillChange]
    public function rewind() {
        reset($this->data);
    }

    #[\ReturnTypeWillChange]
    public function current() {
        return current($this->data);
    }

    #[\ReturnTypeWillChange]
    public function key() {
        return key($this->data);
    }

    #[\ReturnTypeWillChange]
    public function next() {
        return next($this->data);
    }

    #[\ReturnTypeWillChange]
    public function valid() {
        $key = key($this->data);
        return ($key !== null && $key !== false);
    }

    // ---------- Countable ----------
    public function count(): int {
        return count($this->data);
    }

    public function keys(): array {
        return array_keys($this->data);
    }

    public function getData(): array {
        return $this->data;
    }

    public function setData(array $data) {
        $this->data = $data;
    }

    // ---------- JsonSerializable ----------
    #[\ReturnTypeWillChange]
    public function jsonSerialize() {
        return $this->data;
    }

    public function clear() {
        $this->data = array();
    }
}
