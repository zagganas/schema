
<?php

use app\components\FolderList;
use yii\helpers\Html;

echo Html::cssFile('@web/css/workflow/select-folder.css');
echo Html::cssFile('https://use.fontawesome.com/releases/v5.5.0/css/all.css', ['integrity'=> 'sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU', 'crossorigin'=> 'anonymous']);
echo Html::cssFile('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',['integrity'=> 'sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u', 'crossorigin'=> 'anonymous']);
$this->registerJsFile('@web/js/workflow/select-folder-output.js', ['depends' => [\yii\web\JqueryAsset::className()]] );

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <div class="container">
       <br /><!--these line breaks are required, or else the content will not be complente on the popup-->
		<br />

		<div class="row">
			<?=Html::a('Select','javascript:void(0);',['id'=>'select-confirm-button', 'class'=>'btn btn-success btn-md'])?>
			<?=Html::a('Cancel','javascript:void(0);',['id'=>'select-close-button', 'class'=>'btn btn-danger btn-md'])?>
		</div>
		<br />
		<?=FolderList::printFolderList($folders,0)?>
</div>


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>


	