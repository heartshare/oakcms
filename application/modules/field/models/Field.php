<?php
/**
 * @package    oakcms
 * @author     Hryvinskyi Volodymyr <script@email.ua>
 * @copyright  Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 * @version    0.0.1
 */

namespace app\modules\field\models;

use yii;
use yii\helpers\ArrayHelper;

class Field extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%field}}';
    }

    public function rules()
    {
        return [
            [['name', 'slug', 'relation_model'], 'required'],
            [['category_id'], 'integer'],
            [['name', 'type', 'description', 'relation_model'], 'string'],
            ['slug', 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Имя',
            'slug' => 'Код',
            'description' => 'Описание',
            'options' => 'Опции',
            'type' => 'Тип полей',
            'category_id' => 'Категория',
            'relation_model' => 'Привязать к',
        ];
    }

    public function getVariants()
    {
        return $this->hasMany(FieldVariant::className(), ['field_id' => 'id']);
    }

    public function getSelected()
    {
        return ArrayHelper::map($this->hasMany(FieldRelationValue::className(), ['field_id' => 'id'])->all(), 'value', 'value');
    }

    public static function saveEdit($id, $name, $value)
    {
        $setting = Field::findOne($id);
        $setting->$name = $value;
        $setting->save();
    }

	public function getCategory()
    {
		return $this->hasOne(Category::className(), ['id' => 'category_id']);
	}

    public function beforeDelete()
    {
        foreach ($this->hasMany(FieldValue::className(), ['field_id' => 'id'])->all() as $frv) {
            $frv->delete();
        }

        foreach ($this->hasMany(FieldVariant::className(), ['field_id' => 'id'])->all() as $fv) {
            $fv->delete();
        }

		return true;
    }
}
