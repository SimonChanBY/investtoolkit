<?php

namespace app\models;

use Yii;
use app\models\Bets; 

/**
 * This is the model class for table "rooms".
 *
 * @property int $room_id
 * @property string $room_name
 * @property string $status
 * @property string|null $updated_at
 * @property string|null $created_at
 *
 * @property Bets[] $bets
 * @property Products[] $productIdFks
 * @property RoomProducts[] $roomProducts
 */
class Rooms extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_CLOSED = 'closed';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rooms';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['updated_at', 'created_at'], 'default', 'value' => null],
            [['room_name', 'status'], 'required'],
            [['status'], 'string'],
            [['updated_at', 'created_at'], 'safe'],
            [['room_name'], 'string', 'max' => 255],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'room_id' => 'Room ID',
            'room_name' => 'Room Name',
            'status' => 'Status',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Bets]].
     *
     * @return \yii\db\ActiveQuery|BetsQuery
     */
    public function getBets()
    {
        return $this->hasMany(Bets::class, ['room_id_fk' => 'room_id']);
    }

    /**
     * Gets query for [[ProductIdFks]].
     *
     * @return \yii\db\ActiveQuery|ProductsQuery
     */
    public function getProductIdFks()
    {
        return $this->hasMany(Products::class, ['product_id' => 'product_id_fk'])->viaTable('room_products', ['room_id_fk' => 'room_id']);
    }

    /**
     * Gets query for [[RoomProducts]].
     *
     * @return \yii\db\ActiveQuery|RoomProductsQuery
     */
    public function getRoomProducts()
    {
        return $this->hasMany(RoomProducts::class, ['room_id_fk' => 'room_id']);
    }

    /**
     * {@inheritdoc}
     * @return RoomsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RoomsQuery(get_called_class());
    }


    /**
     * column status ENUM value labels
     * @return string[]
     */
    public static function optsStatus()
    {
        return [
            self::STATUS_ACTIVE => 'active',
            self::STATUS_INACTIVE => 'inactive',
            self::STATUS_CLOSED => 'closed',
        ];
    }

    /**
     * @return string
     */
    public function displayStatus()
    {
        return self::optsStatus()[$this->status];
    }

    /**
     * @return bool
     */
    public function isStatusActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function setStatusToActive()
    {
        $this->status = self::STATUS_ACTIVE;
    }

    /**
     * @return bool
     */
    public function isStatusInactive()
    {
        return $this->status === self::STATUS_INACTIVE;
    }

    public function setStatusToInactive()
    {
        $this->status = self::STATUS_INACTIVE;
    }

    /**
     * @return bool
     */
    public function isStatusClosed()
    {
        return $this->status === self::STATUS_CLOSED;
    }

    public function setStatusToClosed()
    {
        $this->status = self::STATUS_CLOSED;
    }

    /**
     * Gets approved user for respective room.
     *
     */
    public static function getApprovedUsersOfRoom($room_id)
    {
        $approved_users = Bets::find()
            ->select('user_id_fk')
            ->where(['room_id_fk' => $room_id])
            ->distinct()
            ->column();

        return $approved_users;
    }

    /**
     * Gets approved user for respective room.
     *
     */
    public static function getRoomsOfApprovedUser($user_id)
    {
        $approved_rooms = Bets::find()
            ->select('room_id_fk')
            ->where(['user_id_fk' => $user_id])
            ->distinct()
            ->column();

        return $approved_rooms;
    }
}
