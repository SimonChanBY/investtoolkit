<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Rooms]].
 *
 * @see Rooms
 */
class RoomsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Rooms[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Rooms|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
