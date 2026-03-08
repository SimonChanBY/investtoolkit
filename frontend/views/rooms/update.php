<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Rooms $model */

$this->title = 'Update Rooms: ' . $model->room_id;
$this->params['breadcrumbs'][] = ['label' => 'Rooms', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->room_id, 'url' => ['view', 'room_id' => $model->room_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="rooms-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
