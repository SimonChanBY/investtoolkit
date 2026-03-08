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
<?php
/*print_r($data); print_r($bet_list); */?>

<style>

.main_table {
    place-items: center; 
    width: 100%;
    /*border: 1px solid black;
    border-collapse: collapse;*/
}

.main_table td{
    width: 50%;
    text-align: center;
    vertical-align: top;
    
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

<div class="rooms-view">
<!-- original view page codes left for reference -->
<!--
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Update', ['update', 'room_id' => $model->room_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'room_id' => $model->room_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'room_id',
            'room_name',
            'status',
            'updated_at',
            'created_at',
        ],
    ]) ?>
-->

<?php
$options = array_combine(range(0, 100), range(0, 100));
?>
<h1> SUMMARY </h1>
<div> Dear <?php echo Yii::$app->user->identity->username; ?>, </div><br>
<div> The following is the summary of the bets. Do note for pooled investment, actual amount will be projected only after all players completed betting. 
    <br> This page will autorefresh every 10 second to retrieve other players bids.
</div>
<?php //print_r($product_list) ?>

<!-- Display the record in a table format -->


<table class="main_table">
    <tr>
<?php
    foreach($product_list as $product){
        if($product['product_type'] == "Pooled Investment" ){
            $pooled_details = Bets::getPooledInvestmentDetails($room_id, $product['product_id']);
        }
?>
        <td>
            <table class="product_table">
                <tr>
                    <td colspan="3">
                        <?php echo $product['product_name']; ?><br>
                        (<?php echo $product['product_type'] ?>)<br><?php print_r(Bets::getPooledInvestmentDetails($room_id, $product['product_id'])) ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <?php echo $product['product_description']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="width:40%;">
                        Players Name 
                    </td>
                    <td style="width:30%;">
                        Bet Amount ($)
                    </td>
                    <td style="width:30%;">
                        Projected Return ($)
                    </td>
                </tr>
<?php
                foreach($bet_list as $bet){
                    if($product['product_id'] == $bet['product_id']){
?>
                <tr>
                    <td style="width:40%;">
                        <?php echo $bet['username']; ?>
                    </td>
                    <td style="width:30%;">
                        <?php echo $bet['bet_amount'] ;?>                        
                    </td>
                    <td style="width:30%;">
<?php
                        if($bet['product_type'] == "Riskless" ){
                            echo $bet['bet_amount'];
                        }
                        if($bet['product_type'] == "Pooled Investment" ){
                            if($model['status'] == "closed"){
                                echo $pooled_details['pooled_amt_individual'];
                            }else{
                                echo "TBA";
                            }
                        }
?>
                    </td>
                </tr>
<?php
                    }
                }   
?>  
            </table>    
        </td>
<?php
    }        
?>
    </tr>
</table>

</div>

