<?php

namespace Solution10\Atlas;

/**
 * Class Results
 *
 * Represents a resultset from a query against a mapper. Will clone
 * and populate new model instances for you based on an "example"
 * model instance in the constructor.
 *
 * @package     Solution10\Atlas
 * @author      Alex Gisby<alex@solution10.com>
 * @license     MIT
 */
class Results implements \Countable, \Iterator, \ArrayAccess
{
    use ReflectionPopulate;

    /**
     * @var  object
     */
    protected $model;

    /**
     * @var     array
     */
    protected $results = [];

    /**
     * @var     object[]
     */
    protected $built = [];

    /**
     * @var     int
     */
    protected $pointer = 0;

    /**
     * Results constructor.
     * @param   object $model Model instance to clone and populate.
     * @param   array $data Dataset to represent.
     */
    public function __construct($model, array $data)
    {
        $this->model = $model;
        $this->results = $data;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->results);
    }

    /* -------------- Iterator ----------------- */

    /**
     * @return  object|null
     */
    public function current()
    {
        return $this->offsetGet($this->pointer);
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->pointer;
    }

    /**
     * @return  void
     */
    public function rewind()
    {
        $this->pointer = 0;
    }

    /**
     * @return  void
     */
    public function next()
    {
        $this->pointer++;
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return $this->pointer < count($this);
    }

    /* --------------- ArrayAccess ------------- */

    /**
     * @param   mixed   $offset
     * @return  bool
     */
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->results);
    }

    /**
     * @param   mixed   $offset
     * @param   mixed   $value
     * @return  void
     */
    public function offsetSet($offset, $value)
    {
        $this->built[$offset] = $value;
    }

    /**
     * @param   mixed   $offset
     * @return  void
     */
    public function offsetUnset($offset)
    {
        unset($this->built[$offset]);
        unset($this->results[$offset]);
    }

    /**
     * This function actually does the work of building and populating
     * the model object to return.
     *
     * @param   mixed   $offset
     * @return  object|null
     */
    public function offsetGet($offset)
    {
        if (!array_key_exists($offset, $this->results)) {
            return null;
        }

        if (!array_key_exists($offset, $this->built)) {
            $i = clone $this->model;
            $i = $this->populate($i, $this->results[$offset]);
            $this->built[$offset] = $i;
        }
        return $this->built[$offset];
    }

    /**
     * @return  object|null
     */
    public function getFirst()
    {
        return $this->offsetGet(0);
    }

    /**
     * @param   object  $model
     * @param   array $data
     * @return  object
     */
    protected function populate($model, array $data)
    {
        if ($model instanceof HasMapper) {
            return $model->getMapper()->load($model, $data);
        }
        return $this->populateWithReflection($model, $data);
    }
}