<?php


/**
 * This view file prints the history of software runs made by a user
 * 
 * @author: Kostis Zagganas
 * First version: March 2019
 */

use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\bootstrap\NavBar;
use yii\bootstrap\Nav;
use yii\helpers\Url;  


echo Html::CssFile('@web/css/project/request-list.css');

$this->title="Dockerhub image details";
$back_button='<i class="fas fa-arrow-left"> </i>'


/*
 * Users are able to view the name, version, start date, end date, mountpoint 
 * and running status of their previous software executions. 
 */


?>
<div class='title row'>
		<div class="col-md-10">
			<h1><?= Html::encode($this->title) ?></h1>
		</div>
		<div class="col-md-2" style="text-align: right;">
			<h1><?= Html::a("$back_button Back", ['administration/dockerhub-image-list'], ['class'=>'btn btn-default']) ?></h1>
		</div>
</div>
<div class="row">&nbsp;</div>


<div class="row" style="padding-left: 15px;">
	<?=Html::textarea('textdetails',$details, ['rows'=>6, 'cols'=>40])?>
</div>