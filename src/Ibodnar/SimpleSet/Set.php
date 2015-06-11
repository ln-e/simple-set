<?php
/**
 * Created by PhpStorm.
 * User: Игорь
 * Date: 20.08.14
 * Time: 23:33
 */

namespace Ibodnar\SimpleSet;

/**
 * Класс описывающий множество
 *
 * @package Ibodnar\SimpleSet
 */
class Set implements \Iterator
{

    /**
     * Текущий активный элемент
     *
     * @var int
     */
    private $currentKey;

    /**
     * @var SetArray
     */
    private $sets;

    /**
     * @var mixed
     */
    private $min;

    /**
     * @var mixed
     */
    private $max;

    /**
     * Метод-конструктор
     */
    public function __construct()
    {
        $this->sets = new SetArray();
        $this->currentKey = 0;
    }

    /**
     * Добавляет подмножество
     *
     * @param SimpleRangeSetInterface $set
     */
    public function addSet(SimpleRangeSetInterface $set)
    {
        $this->subtractSet($set->copy());
        $this->sets[]=($set->copy());
        $this->optimize();
    }

    /**
     * Добавляет новое подмножество от a до б
     *
     * @param mixed $a
     * @param mixed $b
     * @param mixed $value
     */
    public function add($a, $b, $value)
    {
        $start = $a;
        $end = $b;
        if ($this->min) {
            $start = max($this->min, $a);
        }
        if ($this->max) {
            $end = min($this->max, $b);
        }
        $this->addSet(new SimpleRangeSet($start, $end, $value));
    }

    /**
     * Заменяет пересекающиеся участки множетсва на новое подмножество от a до б
     *
     * @param mixed $a
     * @param mixed $b
     * @param mixed $value
     */
    public function replace($a, $b, $value)
    {
        $this->replaceSet(new SimpleRangeSet($a, $b, $value));
    }

    /**
     * Заменяет пересекающиеся участки множетсва на новое подмножество
     *
     * @param SimpleRangeSetInterface $set
     */
    public function replaceSet(SimpleRangeSetInterface $set)
    {
        $length = count($this->sets);
        for ($key = 0; $key < $length; $key++) {
            $existingSet = $this->sets[$key];
            if ($existingSet instanceof SimpleRangeSet) {
                $intersectSet = $existingSet->intersect($set);
                if ($intersectSet) {
                    $this->addSet($intersectSet);
                    $length = count($this->sets);
                }
            }
        }
    }

    /**
     * Проверяет есть ли пересечение с отрезком от а до б
     *
     * @param mixed $a
     * @param mixed $b
     *
     * @return bool
     */
    public function isIntersect($a, $b)
    {
        $set = new SimpleRangeSet($a, $b, null);
        $length = count($this->sets);
        for ($key = 0; $key < $length; $key++) {
            $existingSet = $this->sets[$key];
            if ($existingSet instanceof SimpleRangeSet) {
                if ($existingSet->intersect($set)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Вычитает подмножетсво от a до б
     *
     * @param mixed $a
     * @param mixed $b
     * @param mixed $value
     */
    public function subtract($a, $b, $value)
    {
        $this->subtractSet(new SimpleRangeSet($a, $b, $value));
    }

    /**
     * Вычитает подмножество
     *
     * @param SimpleRangeSetInterface $set
     */
    public function subtractSet(SimpleRangeSetInterface $set)
    {
        $result = array();
        $length = count($this->sets);
        for ($key = 0; $key < $length; $key++) {
            $existingSet = $this->sets[$key];
            if ($existingSet instanceof SimpleRangeSet) {
                if ($existingSet->intersect($set)) {
                    $result = array_merge($result, $existingSet->subtract($set));
                    $this->sets->remove($key);
                    $key--;
                    $length = count($this->sets);
                }
            }
        }
        $this->sets->merge($result);
    }

    /**
     * Содержит ли данный сет конкретную точку
     *
     * @param mixed $point
     *
     * @return bool
     */
    public function contain($point)
    {
        foreach ($this->sets as $set) {
            if ($set instanceof SimpleRangeSet && $set->contain($point)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param mixed $point
     *
     * @return mixed|null
     */
    public function getValue($point)
    {
        foreach ($this->sets as $set) {
            if ($set instanceof SimpleRangeSet && $set->contain($point)) {
                return $set->getValue();
            }
        }

        return null;
    }

    /**
     * Вызывает оптимизацию массива подмножеств
     */
    private function optimize()
    {
        $this->sets->optimize();
    }

    /**
     * Добавляет к темущему множетсву другое
     *
     * @param Set $set
     */
    public function merge(Set $set)
    {
        $this->sets->mergeSet($set->sets);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        return $this->sets[$this->currentKey];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->currentKey++;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->currentKey;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return $this->sets->offsetExists($this->currentKey);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->currentKey = 0;
    }

    /**
     * @return mixed
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @param mixed $min
     *
     * @return $this
     */
    public function setMin($min = null)
    {
        $this->min = $min;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @param mixed $max
     *
     * @return $this
     */
    public function setMax($max = null)
    {
        $this->max = $max;

        return $this;
    }
}
