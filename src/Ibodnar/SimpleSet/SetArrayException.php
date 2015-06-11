<?php
/**
 * Created by PhpStorm.
 * User: Игорь
 * Date: 21.08.14
 * Time: 19:03
 */

namespace Ibodnar\SimpleSet;

/**
 * Класс для исключений в SetArray
 *
 * @package Ibodnar\SimpleSet
 */
class SetArrayException extends \Exception
{

    /**
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}
