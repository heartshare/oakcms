<?php
/**
 * Created by Vladimir Hryvinskyy.
 * Site: http://codice.in.ua/
 * Date: 11.10.2016
 * Project: kotsyubynsk
 * File name: CoreView.php
 */

namespace app\components;


use yii\helpers\Url;
use yii\helpers\VarDumper;

class CoreView extends \rmrevin\yii\minify\View
{
    public $bodyClass = [];
    public $adminPanel = true;
    public $modalLayout = '@app/modules/admin/views/layouts/_modal';

    public function isAdmin() {
        $rHostInfo = Url::home(true);
        if (!\Yii::$app->user->isGuest && strpos(\Yii::$app->request->absoluteUrl, $rHostInfo.'admin') !== false) {
            return true;
        } else {
            return false;
        }
    }

    public function applyModalLayout()
    {
        \Yii::$app->layout = $this->modalLayout;
    }
}
