<?php
/**
 * Created by PhpStorm.
 * User: Игорь
 * Date: 11.08.14
 * Time: 13:19
 */

namespace Ibodnar\SimpleSet;

/**
 * Базовая реализация простого подмножетства
 *
 * @package Ibodnar\SimpleSet
 */
class SimpleRangeSet implements SimpleRangeSetInterface
{

    /**
     * @var number
     */
    private $begin;

    /**
     * @var number
     */
    private $end;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @param mixed $begin
     * @param mixed $end
     * @param mixed $value
     *
     * @throws SimpleRangeSetException
     */
    public function __construct($begin, $end, $value)
    {
        if ($begin>$end) {
            throw new SimpleRangeSetException(sprintf("Невозможно создать множество со значениями %s %s. Начало не может быть после конца.", $begin, $end));
        }
        $this->begin = $begin;
        $this->end = $end;
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getBegin()
    {
        return $this->begin;
    }

    /**
     * @param mixed $begin
     *
     * @return SimpleRangeSetInterface
     */
    public function setBegin($begin)
    {
        $this->begin = $begin;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param mixed $end
     *
     * @return SimpleRangeSetInterface
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }


    /**
     * @return SimpleRangeSetInterface
     */
    public function copy()
    {
        return new SimpleRangeSet(clone $this->getBegin(), clone $this->getEnd(), $this->value);
    }


    /**
     * Добавляет новое множество к текущему
     *
     * @param SimpleRangeSetInterface $simpleRangeSet
     */
    public function merge(SimpleRangeSetInterface $simpleRangeSet)
    {
        if ($this->intersect($simpleRangeSet) && $this->getValue() == $simpleRangeSet->getValue()) {
            $this->setBegin(min($this->getBegin(), $simpleRangeSet->getBegin()));
            $this->setEnd(max($this->getEnd(), $simpleRangeSet->getEnd()));
        }
    }
    /**
     * Возвращает множество - пересечение двух множеств
     *
     * @param SimpleRangeSetInterface $simpleRangeSet
     *
     * @return SimpleRangeSetInterface|null
     */
    public function intersect(SimpleRangeSetInterface $simpleRangeSet)
    {
        $begin = max($this->getBegin(), $simpleRangeSet->getBegin());
        $end = min($this->getEnd(), $simpleRangeSet->getEnd());
        if ($begin>$end) {
            return null;
        }

        return new SimpleRangeSet($begin, $end, $simpleRangeSet->getValue());
    }

    /**
     * {@inheritDoc}
     */
    public function subtract(SimpleRangeSetInterface $subtrahend)
    {
        $intersect = $this->intersect($subtrahend);
        // у множеств нет пересечения => нет и разности
        if (!$intersect) {
            return array();
        }
        if ($subtrahend->getBegin()<=$this->getBegin() && $subtrahend->getEnd()>=$this->getEnd()) {
            return array(); // пусто
        }

        if ($intersect->getBegin() == $this->getBegin()) {
            return array(
                new SimpleRangeSet($intersect->getEnd(), $this->getEnd(), $this->getValue()),
            );
        }

        if ($intersect->getEnd() == $this->getEnd()) {
            return array(
                new SimpleRangeSet($this->getBegin(), $intersect->getBegin(), $this->getValue()),
            );
        }

        // в противном случае интервал разбирается на два интервала
        return array(
            new SimpleRangeSet($this->getBegin(), $intersect->getBegin(), $this->getValue()),
            new SimpleRangeSet($intersect->getEnd(), $this->getEnd(), $this->getValue()),
        );

    }

    /**
     * Проверяет равенство двух интервалов
     *
     * @param SimpleRangeSetInterface $set
     *
     * @return boolean
     */
    public function equalTo(SimpleRangeSetInterface $set)
    {
        return ($set->getBegin() == $this->getBegin() && $set->getEnd() == $this->getEnd() && $set->getValue() == $this->getValue());
    }

    /**
     * Содержит ли интервал данную точку
     *
     * @param mixed $point
     *
     * @return boolean
     */
    public function contain($point)
    {
        return ($this->getBegin()<=$point && $this->getEnd() >= $point);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return \Ibodnar\SimpleSet\SimpleRangeSetInterface
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Возвращает длину интервала
     *
     * @return mixed
     */
    public function getLength()
    {
        return $this->getEnd()-$this->getBegin();
    }
}