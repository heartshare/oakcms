<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\admin\widgets\Button;
use app\modules\language\models\Language;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('language', 'Languages');
$this->params['breadcrumbs'][] = $this->title;

$this->params['actions_buttons'] = [
    [
        'tagName' => 'a',
        'label' => Yii::t('language', 'Create'),
        'options' => [
            'href' => Url::to(['create'])
        ],
        'icon' => 'fa fa-plus',
        'iconPosition' => Button::ICON_POSITION_LEFT,
        'size' => Button::SIZE_SMALL,
        'disabled' => false,
        'block' => false,
        'type' => Button::TYPE_CIRCLE,
    ],
    [
        'label' => Yii::t('admin', 'Control'),
        'options' => [
        'class' => 'btn blue btn-outline btn-circle btn-sm',
        'data-hover' => "dropdown",
        'data-close-others' => "true",
    ],
    'dropdown' => [
        'options' => ['class' => 'pull-right'],
        'encodeLabels' => false,
        'items' => [
                [
                    'label' => '<span class="font-red"><i class="fa fa-trash-o"></i> ' . Yii::t('admin', 'Delete') . '</span>',
                    'url' => 'javascript:void(0)',
                        'linkOptions' => [
                        'onclick' => 'deleteA()',
                    ]
                ],
                [
                    'label' => '<span class="font-green-turquoise"><i class="fa fa-toggle-on"></i> ' . Yii::t('admin', 'Published') . '</span>',
                    'url' => 'javascript:void(0)',
                    'linkOptions' => ['onclick' => 'publishedA()']
                ],
                [
                    'label' => '<span class="font-blue-chambray"><i class="fa fa-toggle-off"></i> ' . Yii::t('admin', 'Unpublished') . '</span>',
                    'url' => 'javascript:void(0)',
                    'linkOptions' => ['onclick' => 'unpublishedA()']
                ],
            ],
        ],
    ]
];
?>
<div class="language-index">

    <div class="table-responsive">
        <?= GridView::widget([
            'id' => 'grid',
            'tableOptions' => ['class'=>'table table-striped table-bordered table-advance table-hover'],
            'dataProvider' => $dataProvider,
            'columns' => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'options' => ['style' => 'width:36px']
                ],
                [
                    'attribute' => 'language_id',
                    'options' => ['style' => 'width:100px']
                ],
                'language',
                'country',
                'url:url',
                'name',
                // 'name_ascii',
                [
                    'class' => \app\modules\admin\components\grid\EnumColumn::className(),
                    'attribute' => 'status',
                    'format' => 'raw',
                    'options' => ['width' => '50px'],
                    'value' => function ($model, $index, $widget) {
                        return Html::checkbox('', $model->status == Language::STATUS_PUBLISHED, [
                            'class' => 'switch toggle',
                            'data-id' => $model->primaryKey,
                            'data-link' => \yii\helpers\Url::to(['/admin/language/language']),
                            'data-reload' => '0'
                        ]);
                    },
                    'enum' => [
                        Yii::t('admin', 'Off'),
                        Yii::t('admin', 'On')
                    ]
                ],
                ['class' => 'app\modules\admin\components\grid\ActionColumn'],
            ],
        ]); ?>
    </div>
</div>
<script>
    function deleteA() {
        var keys = $('#grid').yiiGridView('getSelectedRows');
        window.location.href = '<?= Yii::$app->homeUrl ?>admin/language/language/delete-ids?id=' + keys.join();
    }
    function publishedA() {
        var keys = $('#grid').yiiGridView('getSelectedRows');
        window.location.href = '<?= Yii::$app->homeUrl ?>admin/language/language/published?id=' + keys.join();
    }
    function unpublishedA() {
        var keys = $('#grid').yiiGridView('getSelectedRows');
        window.location.href = '<?= Yii::$app->homeUrl ?>admin/language/language/unpublished?id=' + keys.join();
    }
</script>
