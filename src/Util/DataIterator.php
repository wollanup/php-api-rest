<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 27/04/16
 * Time: 16:24
 */

namespace Eukles\Util;

use ArrayAccess;
use Iterator;

/**
 * Class DataIterator
 *
 * @package Eukles\Util
 */
class DataIterator implements Iterator, ArrayAccess
{

    protected $data = [];

    /**
     * @return mixed
     */
    final public function current()
    {
        return current($this->data);
    }

    /**
     * @return mixed
     */
    final public function key()
    {
        return key($this->data);
    }

    /**
     *
     */
    final public function next()
    {
        next($this->data);
    }

    /**
     * @param mixed $offset
     *
     * @return bool
     */
    final public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->data);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    final public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    final public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    /**
     * @param mixed $offset
     */
    final  public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     *
     */
    final public function rewind()
    {
        reset($this->data);
    }

    /**
     * @return bool
     */
    final public function valid()
    {
        return (bool)!(key($this->data) === null);
    }
}
