<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<div class="product-add-modification-form">
    <?php $form = ActiveForm::begin(); ?>

    <fieldset>
        <legend>1. Выберите значения модификации</legend>
        <?php if($filters = $productModel->getFilters()) {  ?>
            <div class="filters form-group">
                <?php foreach($filters as $filter) { ?>
                    <?php if($variants = $filter->variants) { ?>
                        <div class="col-md-3 col-xs-6">
                            <p>
                                <label for="filterValue<?=$filter->id;?>"><?=$filter->name;?></label>
                                <select id="filterValue<?=$filter->id;?>" name="filterValue[<?=$filter->id;?>]">
                                    <option value="">-</option>
                                    <?php foreach($variants as $variant) { ?>
                                        <option <?php if(in_array($variant->id, $model->filtervariants)) echo ' selected="selected"'; ?> value="<?=$variant->id;?>"><?=$variant->value;?></option>
                                    <?php } ?>
                                </select>
                            </p>
                            <p><i><?=$filter->description;?></i></p>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        <?php } else { ?>
            <p>Значения задаются в <?=Html::a('фильтрах', ['/admin/filter/filter/index'], ['target' => '_blank']);?>. В настоящий момент к категории продукта не привязано ни одного фильтра.</p>
        <?php } ?>
    </fieldset>

    <fieldset>
        <legend>2. Задайте параметры модификации</legend>
        <?= $form->field($model, 'product_id')->textInput(['type' => 'hidden'])->label(false) ?>

        <div class="row form-group">
            <div class="col-md-6 col-xs-6">
                <?= $form->field($model, 'name')->textInput(['placeholder' => 'Наименование']) ?>
            </div>
            <div class="col-md-3 col-xs-3">
                <?= $form->field($model, 'code')->textInput() ?>
            </div>
            <div class="col-md-3 col-xs-3">
                <?= $form->field($model, 'sort')->textInput() ?>
            </div>
        </div>
        <div class="row form-group">
            <div class="col-md-3 col-xs-3">
                <?= $form->field($model, 'price')->textInput(['value' => $productModel->price]) ?>
            </div>
            <div class="col-md-3 col-xs-3">
                <?= $form->field($model, 'amount')->textInput() ?>
            </div>
            <div class="col-md-3 col-xs-3">
                <?= $form->field($model, 'available')->radioList(['yes' => 'Да','no' => 'Нет']); ?>
            </div>
        </div>
    </fieldset>

    <div class="form-group" style="text-align: right;">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Редактировать', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
