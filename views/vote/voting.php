<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\AlertBlock;
use kartik\widgets\Alert;

/**
 * @var yii\web\View $this
 * @var app\models\Poll $model
 */
/*
$this->title = Yii::t('app', 'voting for {pollQuestion}: ', [
    'pollQuestion' => 'Poll Question',
]) . ' ' . $model->__toString();
*/
?>
<div class="voting">
   <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?php
    // only display the token-error flash messages as alerts
    echo AlertBlock::widget([
        'useSessionFlash' => true,
        'type' => AlertBlock::TYPE_ALERT,
        'delay'=> false,
        'closeButton' => false,
        'alertSettings' => [
            'token-error' => ['type' => Alert::TYPE_DANGER ],
        ],
    ]);

    if ($show_form === false) {
        ?><p><a class="btn btn-primary btn-lg" href="<?=Url::home()?>" role="button">Back to Home</a></p><?php
    } elseif ($show_form === true && isset($model)) {
        echo $this->render('_form', [
            'model' => $model,
        ]);
    }
    ?>
</div>
