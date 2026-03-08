<?php

namespace app\models;

use Yii;
use yii\db\Query;
use common\models\User;
use app\models\Rooms;

/**
 * This is the model class for table "bets".
 *
 * @property int $bet_id
 * @property int|null $room_id_fk
 * @property int|null $product_id_fk
 * @property int|null $user_id_fk
 * @property int $bet_amount
 * @property string|null $bet_time
 *
 * @property Products $productIdFk
 * @property Rooms $roomIdFk
 * @property User $userIdFk
 */
class Bets extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bets';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['room_id_fk', 'product_id_fk', 'user_id_fk', 'bet_time'], 'default', 'value' => null],
            [['room_id_fk', 'product_id_fk', 'user_id_fk', 'bet_amount'], 'integer'],
            [['bet_amount'], 'required'],
            [['bet_time'], 'safe'],
            [['room_id_fk'], 'exist', 'skipOnError' => true, 'targetClass' => Rooms::class, 'targetAttribute' => ['room_id_fk' => 'room_id']],
            [['user_id_fk'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id_fk' => 'id']],
            [['product_id_fk'], 'exist', 'skipOnError' => true, 'targetClass' => Products::class, 'targetAttribute' => ['product_id_fk' => 'product_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'bet_id' => 'Bet ID',
            'room_id_fk' => 'Room Id Fk',
            'product_id_fk' => 'Product Id Fk',
            'user_id_fk' => 'User Id Fk',
            'bet_amount' => 'Bet Amount',
            'bet_time' => 'Bet Time',
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
     * Gets query for [[UserIdFk]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getUserIdFk()
    {
        return $this->hasOne(User::class, ['id' => 'user_id_fk']);
    }

    /**
     * {@inheritdoc}
     * @return BetsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new BetsQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     * @return BetsQuery the active query used by this AR class.
     */
    public static function getRoomBetDetails($room_id)
    {
        $bet_query = new Query();
        $list = $bet_query->select(['bets.*', 'products.*', 'user.username'])
                ->from('bets')
                ->leftJoin('products', 'bets.product_id_fk = products.product_id')
                ->leftJoin('user', 'bets.user_id_fk = user.id')
                ->where(['room_id_fk' => $room_id])
                ->all();

        return $list;
    }

    /**
     * {@inheritdoc}
     * @return BetsQuery the active query used by this AR class.
     */
    public static function checkBetsStatus($room_id, $user_id)
    {
        
        $zeroCount= self::find()
            ->where(['room_id_fk' => $room_id])
            ->andWhere(['user_id_fk' => $user_id])
            ->andWhere(['bet_amount' => 0])
            ->count();
        
        if($zeroCount == 0){
            return $bet_flag = true;
        }else{
            return $bet_flag = false;
        }
    }

    /**
     * {@inheritdoc}
     * @return BetsQuery the active query used by this AR class.
     */
    public static function checkUpdateBetsEntry($room_id)
    {
        // Count any bets with 0
        $zeroCount = self::find()
            ->where(['room_id_fk' => $room_id])
            ->andWhere(['bet_time' => null])
            ->count();

        if(!$zeroCount){
            $room = Rooms::findOne($room_id);
            if ($room) {
                $room->status = 'closed';
                return $room->save();
            }
        }
    }

    /**
     * {@inheritdoc}
     * @return BetsQuery the active query used by this AR class.
     */
    public static function getPooledInvestmentDetails($room_id, $product_id)
    {
        // Count any bets with 0
        $pooled_amt = self::find()
            ->where(['room_id_fk' => $room_id])
            ->andWhere(['product_id_fk' => $product_id])
            ->sum('bet_amount');

        $player_count = self::find()
            ->where(['room_id_fk' => $room_id])
            ->andWhere(['product_id_fk' => $product_id])
            ->count();

            $pooled_amt_bonus = $pooled_amt * 0.5;
            $pooled_bonus_total = $pooled_amt + $pooled_amt_bonus;
            $pooled_amt_individual = $pooled_bonus_total / $player_count;
        return [
            'pooled_amt' => $pooled_amt,
            'pooled_amt_bonus' => $pooled_amt_bonus,
            'pooled_amt_individual' => $pooled_amt_individual,
            'player_count' => $player_count 
        ];
    }

}
