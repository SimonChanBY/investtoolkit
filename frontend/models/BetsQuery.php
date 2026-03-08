<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Bets]].
 *
 * @see Bets
 */
class BetsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Bets[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Bets|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
