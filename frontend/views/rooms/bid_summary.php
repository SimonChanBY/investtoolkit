<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\User;
use app\models\Bets;

/** @var yii\web\View $this */
/** @var app\models\Rooms $model */

//$user = User::findOne($_GET['user_id']);

$this->title = $model->room_name;
$this->params['breadcrumbs'][] = ['label' => 'Rooms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

// Auto-refresh every 10 seconds
$refreshSeconds = 10;
$js = <<<JS
setInterval(function() {
    location.reload();
}, {$refreshSeconds} * 1000);
JS;
$this->registerJs($js);

?>


<style>

.main_table {
    place-items: center; 
    width: 100%;
    border: 1px solid black;
    border-collapse: collapse;
}

.main_table td{
    text-align: center;
    border: 1px solid black;
    border-collapse: collapse;
}

.product_table {
    width: 100%;
    border: 1px solid black;
    border-collapse: collapse;
}
.product_table td{
    border: 1px solid black;
}
h1 {
    text-align: center;
}
</style>

<h1> SUMMARY </h1>
<div> Dear <?php echo Yii::$app->user->identity->username; ?>, </div><br>
<div> The following is the summary of the bets. Do note for pooled investment, actual amount will be projected only after all players completed betting. 
    <br> This page will autorefresh every 10 second to retrieve other players bids.
</div>
<br>


<table class="main_table">
    <tr>
        <td rowspan="2"> Player Name </td>
<?php 
    foreach($product_list as $product){
?>
        <td colspan="2"><?php echo $product['product_name']."<br>(".$product['product_type'].")"; ?></td>
<?php
    }
?>
        <td rowspan="2">Final Payout</td>
    </tr>
    <tr>
<?php
    foreach($product_list as $product){
?>
        <td>Bet Amount ($) </td>
        <td>Projected Return ($) </td>
<?php   
    }
?>
    </tr>
<?php 
    foreach($summary_display_array as $display){
?>
    <tr>
        <td> <?php echo $display['username'] ?> </td>
<?php
    $count = 0;
    foreach($product_list as $product){
        $count++;
        $key_id = "product_".$count."_id";
        $key_amt = "product_".$count."_amt";
        $key_projected = "product_".$count."_amt_projected";
        if($display[$key_id] == $product['product_id']){
?>
            <td> <?php echo $display[$key_amt] ?>  </td>
            <td> <?php echo $display[$key_projected] ?> </td>
<?php   
        }
    }
?>
    <td> <?php echo $display['final'] ?> </td>
    </tr>
<?php
    }
?>
</table>
