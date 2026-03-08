<?php

use app\models\Rooms;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\roomsForm $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
use common\models\User;
use app\models\Bets;

$this->title = 'Rooms';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rooms-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php 
            if(Yii::$app->user->identity->username == "admin"){
        ?>
            <?= Html::a('Create Rooms', ['create'], ['class' => 'btn btn-success']) ; ?>
        <?php    
            }
        ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'room_id',
            //'room_name',
            [
                'attribute' => 'room_name',
                'format' => 'raw',
                'value' => function ($model) {
                    // Generates a link: /index.php?r=controller/view&id=1
                    $bet_flag = Bets::checkBetsStatus($model->room_id, Yii::$app->user->id);
                    print_r($bet_flag);
                    if (!$bet_flag) {
                        return Html::a(Html::encode($model->room_name), ['/rooms/bid', 'room_id' => $model->room_id, 'user_id' => Yii::$app->user->id]);
                    }
                    if($bet_flag || Yii::$app->user->identy->username = "admin"){
                        return Html::a(Html::encode($model->room_name), ['/rooms/bid_summary', 'room_id' => $model->room_id, 'user_id' => Yii::$app->user->id]);
                    }
                },
                
            ],
            'status',
            //'updated_at',
            /*[
                'attribute' => 'updated_at',
                'format' => ['datetime', 'php:d-M-Y H:i:s'],
                
            ],*/
            //'created_at',
            /*[
                'attribute' => 'created_at',
                'format' => ['datetime', 'php:d-M-Y H:i:s'],
                
            ],*/
            [
                'class' => ActionColumn::className(),
                'template' => '{link}',
                /*
                'urlCreator' => function ($action, Rooms $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'room_id' => $model->room_id]);
                 }*/
                'buttons' => [
                    'link' => function ($url, $model, $key) {
                        
                        $bet_flag = Bets::checkBetsStatus($model->room_id, Yii::$app->user->id);

                        if (!$bet_flag) {
                            return yii\helpers\Html::a(
                                'Join',
                                ['bid', 'room_id' => $model->room_id, 'user_id' => Yii::$app->user->id],
                                ['class' => 'btn btn-success btn-sm']
                            );
                        }

                        if ($bet_flag || Yii::$app->user->identy->username = "admin") {
                            return yii\helpers\Html::a(
                                'View Summary',
                                ['bid_summary', 'room_id' => $model->room_id, 'user_id' => Yii::$app->user->id],
                                ['class' => 'btn btn-primary btn-sm']
                            );
                        }

                        return '';
                    },
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
