<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use app\models\forms\UploadForm;
use app\components\helpers\PollUrl;
use kartik\file\FileInput;

$model = new UploadForm();

Modal::begin([
    'header' => Html::tag('h2', Yii::t('app', 'Import From Excel')),
        'toggleButton' => [
            'label' => Yii::t('app', 'Import From Excel'),
            'class' => 'btn btn-primary',
        ],
]);
?>
<div class="member-excel-form">
<?php
    echo Html::tag('p', 'Importing members will delete all existing members.');
?>

    <?php $form = ActiveForm::begin(['action' => PollUrl::toRoute(['member/import', 'poll_id' => $poll->id]), 'options' => ['enctype' => 'multipart/form-data']]); ?>

    <? //$form->field($model, 'excelFile')->fileInput() ?>

<?
    echo $form->field($model, 'excelFile')->widget(FileInput::classname(), [
        'pluginOptions' => [
            'showPreview' => false,
            'showRemove' => false,
            'showUpload' => false,
        ],
    ]);
?>

    <div class="form-group">
        <?= Html::submitButton('Import', [
            'class' => 'btn btn-success',
            'data' => ['confirm' => 'This will delete all existing contacts. Are you sure you want to import?'],
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php Modal::end(); ?>