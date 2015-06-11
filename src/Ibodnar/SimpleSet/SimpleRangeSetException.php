<?php
/**
 * Created by PhpStorm.
 * User: Игорь
 * Date: 11.08.14
 * Time: 13:55
 */

namespace Ibodnar\SimpleSet;

/**
 * Класс-исключение возникающий у простых подмножеств
 *
 * @package Ibodnar\SimpleSet
 */
class SimpleRangeSetException extends \Exception
{
    /**
     * @var string
     */
    protected $message;

    /**
     * @param string $message
     */
    public function __construct($message)
    {
        $this->message = $message;
        parent::__construct($message);
    }
}
