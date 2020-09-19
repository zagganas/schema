<?php
/**
 * View file that prints the logs of the running pod
 * 
 * @author: Kostis Zagganas
 * First version: Dec 2018
 */
use yii\helpers\Html;
	
	// echo Html::CssFile('@web/css/software.css');
?>
<div class="row">
	<div class="col-md-12"><h3>Runtime Info:</h3></div>
</div>

<div class="row">
	<div class="status-div align-middle col-md-3">
		<span class="status-label"><b>Workflow status:</b></span>
		<span id="status-value" class="status-<?=$status?>"><?=$status?></span>
	</div>
	<div class="status-div align-middle col-md-3">
		<span class="status-label"><b>Running time:</b></span>
		<span id="exec-time-value" class="status-<?=$status?>"><?=$time?></span>
	</div>
	<?php
	if ($status!='COMPLETE')
	{
	?>
		<div class="col-md-6"><?=Html::img('@web/img/schema-01-loading.gif',['class'=>'float-right run-gif-img'])?></div>
	<?php	
	}
	else
	{
	?>
		<div class="col-md-6">&nbsp;</div>
	<?php	
	}
	?>
</div>
<div class="row">&nbsp;</div>

<div class="steps-box">
	<?php
	foreach ($taskLogs as $log)
	{
	?>
	<div class="row">
		<div class=col-md-1>Step <?=$log['step']?>: </div>
		<div class=col-md-10><strong><?=$log['name']?></strong> (<?=$log['description']?>) ----> <span class="status-<?=$log['status']?>"><?=$log['status']?></span></div>
	</div>
		
	<?php	
	}
	?>
</div>