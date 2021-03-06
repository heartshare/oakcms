<?php

namespace app\modules\user\controllers\backend;

use app\components\BackendController;
use app\modules\user\forms\LoginForm;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\helpers\VarDumper;

class UserController extends BackendController
{
    /**
     * @var \app\modules\user\Module
     */
    public $module;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', ''],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = '//_clear';
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack(Url::to(['/admin']));
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionLockScreen()
    {
        $this->layout = '//_clear';

        // save current username
        $user = clone Yii::$app->user->identity;

        // force logout
        Yii::$app->user->logout();

        // render form lockscreen
        $model = new LoginForm();
        $model->username = $user->username;    //set default value
        return $this->render('lock-screen', [
            'model' => $model,
            'user' => $user
        ]);
    }
}
