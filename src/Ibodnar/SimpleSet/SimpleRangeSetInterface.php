<?php
/**
 * Created by PhpStorm.
 * User: Игорь
 * Date: 11.08.14
 * Time: 13:21
 */

namespace Ibodnar\SimpleSet;

/**
 * Интерфейс для простого подмножества
 *
 * @package Ibodnar\SimpleSet
 */
interface SimpleRangeSetInterface
{

    /**
     * Возвращает конец интервала
     *
     * @return mixed
     */
    public function getBegin();

    /**
     * Устанавливает начало интервала
     *
     * @param mixed $begin
     *
     * @return mixed
     */
    public function setBegin($begin);

    /**
     * Возвращает конец интервала
     *
     * @return mixed
     */
    public function getEnd();

    /**
     * Устанавливает конец интервала
     *
     * @param mixed $end
     *
     * @return mixed
     */
    public function setEnd($end);

    /**
     * Значение интервала
     *
     * @return mixed
     */
    public function getValue();

    /**
     * Значение интервала
     *
     * @param mixed $value
     *
     * @return SimpleRangeSetInterface
     */
    public function setValue($value);

    /**
     * @return SimpleRangeSetInterface
     */
    public function copy();

    /**
     * Объединяет текущее множество и simpleRangeSet
     *
     * @param SimpleRangeSetInterface $simpleRangeSet
     *
     * @return void
     */
    public function merge(SimpleRangeSetInterface $simpleRangeSet);

    /**
     * Возвращает множество - пересечение двух множеств
     *
     * @param SimpleRangeSetInterface $simpleRangeSet
     *
     * @return SimpleRangeSetInterface|null
     */
    public function intersect(SimpleRangeSetInterface $simpleRangeSet);

    /**
     * Проверяет, граничат ли они в одной точке (в начале или в конце)
     *
     * @param SimpleRangeSetInterface $set
     *
     * @return bool
     */
    public function isBound(SimpleRangeSetInterface $set);


    /**
     * Возвращает массив состоящий из SimpleRangeSetInterface содержащий результат разбиения
     *
     * @param SimpleRangeSetInterface $subtrahend
     *
     * @return array
     */
    public function subtract(SimpleRangeSetInterface $subtrahend);

    /**
     * Проверяет равенство двух интервалов
     *
     * @param SimpleRangeSetInterface $set
     *
     * @return boolean
     */
    public function equalTo(SimpleRangeSetInterface $set);


    /**
     * Содержит ли интервал данную точку
     *
     * @param mixed $point
     *
     * @return boolean
     */
    public function contain($point);

    /**
     * Возвращает длину интервала
     *
     * @return mixed
     */
    public function getLength();

} 