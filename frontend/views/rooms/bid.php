<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\User;

/** @var yii\web\View $this */
/** @var app\models\Rooms $model */

$user = User::findOne($_GET['user_id']);

$this->title = $model->room_name;
$this->params['breadcrumbs'][] = ['label' => 'Rooms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

//check for user access
if (Yii::$app->user->id == 'admin') {
    throw new \yii\web\ForbiddenHttpException('Only admin can access this page.');
}
print_r($approved_users);
?>


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

<div> Dear <?php echo Yii::$app->user->identity->username; ?>, </div>
<div> You have $100 to bid in the following products, it is required for you to spend all the available credit. </div>
<?php //print_r($product_list) ?>

<!-- Display the record in a table format -->

<!-- <form id="my_form" action="" method="post"> -->
<?= Html::beginForm('', 'post', ['id' => 'my_form']) ?>
<table class="main_table">
    <tr>
<?php
    foreach($product_list as $product){
?>
        <td>
            <table class="product_table">
                <tr>
                    <td colspan="2">
                        <?php echo $product['product_name']; ?></br>
                        (<?php echo $product['product_type'] ?>)
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <?php echo $product['product_description']; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        Bet Amount:
                    </td>
                    <td>
                        <!-- <input type="text" id="<?php echo $product['product_id'].'_bet_amt'; ?>" name="<?php echo $product['product_id'].'_bet_amt'; ?>" value="$0">-->
                        <?php $input_name = $product['product_id'].'_bet_amt';?>
                        <?php echo Html::dropDownList($input_name, 0, $options, ['class' => 'form-control', 'id' => $input_name]) ?>
                    </td>
                </tr>
            </table>    
        </td>
<?php
    }        
?>
    </tr>
</table>
<input type="hidden" name="room_id" value="<?php echo $room_id; ?>">
<input type="hidden" name="user_id" value="<?php echo Yii::$app->user->id; ?>">
<input type="submit" value="Submit">
<!-- </form> -->
<?= Html::endForm() ?>
</div>

<!-- Validation check for total amount bet is 100 -->
 <script>

document.getElementById("my_form").addEventListener("submit", function(event) {

    let total = 0;
<?php

    foreach($product_list as $product){
?>
        let val<?php echo $product['product_id'] ?> = parseInt(document.getElementById("<?php echo $product['product_id'].'_bet_amt'; ?>").value) || 0;
        total = total + val<?php echo $product['product_id'] ?>;
<?php
    }
?>

    if (total !== 100) {
        alert('Total Bet should be exactly $100');
        event.preventDefault(); // Stop form submission
    }
});



</script>