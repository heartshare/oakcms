<?php
/**
 * Copyright (c) 2015 - 2016. Hryvinskyi Volodymyr
 */

namespace app\modules\shop\models;

use yii;

class Price extends \yii\db\ActiveRecord implements \app\modules\cart\interfaces\CartElement
{

    public static function tableName()
    {
        return '{{%shop_price}}';
    }

    public function rules()
    {
        return [
            [['name', 'product_id'], 'required'],
            [['name', 'available', 'code'], 'string', 'max' => 100],
            [['price'], 'number'],
            [['product_id', 'amount'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'product_id' => 'Продукт',
            'price' => 'Цена',
            'code' => 'Артикул',
            'available' => 'Наличие',
            'amount' => 'Остаток',
            'sort' => 'Приоритет',
        ];
    }

    public function minusAmount($count)
    {
        $this->amount = $this->product->amount-$count;

        return $this->save(false);
    }

    public function plusAmount($count)
    {
        $this->amount = $this->product->amount+$count;

        return $this->save(false);
    }

    public function getCartId() {
        return $this->id;
    }

    public function getCartName() {
        return $this->product->name;
    }

    public function getCartPrice() {
        return $this->price;
    }

    public function getCartOptions()
    {
        return '';
    }

    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    public static function editField($id, $name, $value)
    {
        $setting = Price::findOne($id);
        $setting->$name = $value;
        $setting->save();
    }
}
