<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "room_products".
 *
 * @property int $room_id_fk
 * @property int $product_id_fk
 *
 * @property Products $productIdFk
 * @property Rooms $roomIdFk
 */
class RoomProducts extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'room_products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['room_id_fk', 'product_id_fk'], 'required'],
            [['room_id_fk', 'product_id_fk'], 'integer'],
            [['room_id_fk', 'product_id_fk'], 'unique', 'targetAttribute' => ['room_id_fk', 'product_id_fk']],
            [['room_id_fk'], 'exist', 'skipOnError' => true, 'targetClass' => Rooms::class, 'targetAttribute' => ['room_id_fk' => 'room_id']],
            [['product_id_fk'], 'exist', 'skipOnError' => true, 'targetClass' => Products::class, 'targetAttribute' => ['product_id_fk' => 'product_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'room_id_fk' => 'Room Id Fk',
            'product_id_fk' => 'Product Id Fk',
        ];
    }

    /**
     * Gets query for [[ProductIdFk]].
     *
     * @return \yii\db\ActiveQuery|ProductsQuery
     */
    public function getProductIdFk()
    {
        return $this->hasOne(Products::class, ['product_id' => 'product_id_fk']);
    }

    /**
     * Gets query for [[RoomIdFk]].
     *
     * @return \yii\db\ActiveQuery|RoomsQuery
     */
    public function getRoomIdFk()
    {
        return $this->hasOne(Rooms::class, ['room_id' => 'room_id_fk']);
    }

    /**
     * {@inheritdoc}
     * @return RoomProductsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RoomProductsQuery(get_called_class());
    }

}
