<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\cart\events;

use yii\base\Event;

class CartElement extends Event
{
    public $element;
    public $cost;
    public $stop;
}
