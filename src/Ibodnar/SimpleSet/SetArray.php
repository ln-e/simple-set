<?php
/**
 * Created by PhpStorm.
 * User: Игорь
 * Date: 21.08.14
 * Time: 0:02
 */

namespace Ibodnar\SimpleSet;

/**
 * Хранилище простых подмножеств
 *
 * @package Ibodnar\SimpleSet
 */
class SetArray extends \ArrayObject
{

    /**
     * Метод для сравнения
     *
     * @param SimpleRangeSet $value1
     * @param SimpleRangeSet $value2
     *
     * @return int
     */
    public function compare(SimpleRangeSet $value1, SimpleRangeSet $value2)
    {
        if ($value1->getBegin() > $value2->getBegin()) {
            return 1;
        } else if ($value1->getBegin() == $value2->getBegin()) {
            return 0; // возвращает ноль, хотя фактически они могут быть и не идентичными, например конец одного позже другого
        } else {
            return -1;
        }
    }

    /**
     * Удаляет элемент из массива
     *
     * @param mixed $key
     */
    public function remove($key)
    {
        $array = $this->getArrayCopy();
        array_splice($array, $key, 1);
        $this->exchangeArray($array);
    }

    /**
     * Обновляет порядок следования ключей в массиве
     */
    private function refreshKeys()
    {
        $array = array_values($this->getArrayCopy());
        $this->exchangeArray($array);
    }

    /**
     * Оптимизирует набор подмножеств (сортирует, обновляет значения ключей, и объединяет
     * смешные подмножества с одинакомы значением)
     *
     * @throws SetArrayException
     */
    public function optimize()
    {
        $this->uasort(array(&$this,"compare"));
        $this->refreshKeys();
            for ($key = 0; $key+1 < count($this); $key++) {
                $set = $this[$key];
                $set2 = $this[$key+1];

                if (!($set instanceof SimpleRangeSetInterface && $set2 instanceof SimpleRangeSetInterface)) {
                    throw new SetArrayException(sprintf('All SetArray elements must be instance of SimpleRangeSetInterface, "%s" given.', ($set instanceof SimpleRangeSetInterface)?get_class($set2):get_class($set)));
                }

                if ($set->intersect($set2) && $set->getValue() === $set2->getValue()) {
                    $set2->merge($set); // объединяем смежные множества с одинаковым значением
                    $this->remove($key);
                    $key--;
                }

            }

        $this->uasort(array(&$this,"compare"));
        $this->refreshKeys();
    }


    /**
     * Добавляет подмножетсва из других массивов
     *
     * @param array $anotherArray
     *
     * @throws SetArrayException
     */
    public function merge(array $anotherArray)
    {
        foreach ($anotherArray as $item) {
            if (!($item instanceof SimpleRangeSetInterface)) {
                throw new SetArrayException(sprintf("You can add only SimpleRangeSetInterface elements, %s given", get_class($item)));
            }
        }

        $array = $this->getArrayCopy();
        $this->exchangeArray(array_merge($array, $anotherArray));
        $this->optimize();
    }

    /**
     * Добавляет к текущему SetArray подмножества из другого
     *
     * @param SetArray $anotherSet
     */
    public function mergeSet(SetArray $anotherSet)
    {
        $this->merge($anotherSet->getArrayCopy());
    }


}