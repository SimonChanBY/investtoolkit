<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Rooms $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="rooms-form">

    <?php $form = ActiveForm::begin([
            'id' => 'rooms-form',
          ]); 
    ?>

    <?= $form->field($model, 'room_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList([ 'active' => 'Active', 'inactive' => 'Inactive', 'closed' => 'Closed', ], ['prompt' => '']) ?>

    <!-- Products -->
    <div> Products to bid ( Hold Ctrl + Click to select multiple players )  </div>
    <select name="product_ids[]" id="productSelect" multiple >
<?php   
        foreach($product_list as $product){
?>
        <option value="<?php echo $product['product_id']; ?>"><?php echo $product['product_name']; ?></option>
<?php
        }
?>
    </select>

    <!-- Users -->
    <div> Players ( Hold Ctrl + Click to select at least 2 or up to a maximum of 4 players )  </div>
    <select name="user_ids[]" id="userSelect" multiple size="5" >
<?php   
        foreach($user_list as $user){
?>
        <option value="<?php echo $user['id']; ?>"><?php echo $user['username']; ?></option>
<?php
        }
?>
    </select>
<!--
    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>
-->
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

    // Get the form element (Yii2 ActiveForm)
    const form = document.getElementById('rooms-form');

    // Use jQuery to attach beforeSubmit handler
    $(form).on('beforeSubmit', function(e) {

        const productSelect = document.getElementById('productSelect');
        const userSelect = document.getElementById('userSelect');

        const selectedProducts = Array.from(productSelect.selectedOptions);
        const selectedUsers = Array.from(userSelect.selectedOptions);

        // Custom validation
        if (selectedProducts.length === 0) {
            alert('Please select at least one product.');
            return false; // prevent form submission
        }

        if (selectedUsers.length < 2 || selectedUsers.length > 4) {
            alert('Please select between 2 and 4 users.');
            return false; // prevent form submission
        }

        // allow submission if validation passes
        return true;
    });

});
</script>