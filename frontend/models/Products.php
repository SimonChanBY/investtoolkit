<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "products".
 *
 * @property int $product_id
 * @property string $product_name
 * @property string $product_type
 * @property string $product_description
 *
 * @property Bets[] $bets
 * @property Rooms[] $roomIdFks
 * @property RoomProducts[] $roomProducts
 */
class Products extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['product_name', 'product_type', 'product_description'], 'required'],
            [['product_description'], 'string'],
            [['product_name'], 'string', 'max' => 255],
            [['product_type'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'product_id' => 'Product ID',
            'product_name' => 'Product Name',
            'product_type' => 'Product Type',
            'product_description' => 'Product Description',
        ];
    }

    /**
     * Gets query for [[Bets]].
     *
     * @return \yii\db\ActiveQuery|BetsQuery
     */
    public function getBets()
    {
        return $this->hasMany(Bets::class, ['product_id_fk' => 'product_id']);
    }

    /**
     * Gets query for [[RoomIdFks]].
     *
     * @return \yii\db\ActiveQuery|RoomsQuery
     */
    public function getRoomIdFks()
    {
        return $this->hasMany(Rooms::class, ['room_id' => 'room_id_fk'])->viaTable('room_products', ['product_id_fk' => 'product_id']);
    }

    /**
     * Gets query for [[RoomProducts]].
     *
     * @return \yii\db\ActiveQuery|RoomProductsQuery
     */
    public function getRoomProducts()
    {
        return $this->hasMany(RoomProducts::class, ['product_id_fk' => 'product_id']);
    }

    /**
     * {@inheritdoc}
     * @return ProductsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProductsQuery(get_called_class());
    }

}
