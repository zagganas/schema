<?php


namespace app\components;
use yii\helpers\Html;
use yii\helpers\Url;
use webvimark\modules\UserManagement\models\User;
use Yii;
use app\models\DownloadDataset;
use yii\widgets\ActiveForm; 



class DownloadDatasetModal
{
	public static function addModal()
	{
		$username=User::getCurrentUser()['username'];
		$datasets=['Helix'=>'Helix'];
		$select_icon='<i class="fas fa-folder-open"></i>';
		$clear_icon='<i class="fas fa-times"></i>';
		$osystemMount='';

		$model=new DownloadDataset;

		$form=ActiveForm::begin(['action'=>['filebrowser/download-dataset'], 'method'=> 'POST']);
		echo Html::hiddenInput('mountcaller',null ,['id'=>'mountcaller']);
		echo Html::hiddenInput('selectmounturl',Url::to(['software/select-mountpoint','username'=>$username]) ,['id'=>'selectmounturl']);

		echo "<div class='modal fade' tabindex='-1' role='dialog' id='download-modal' aria-labelledby='download-modal' aria-hidden='true'>";
		echo '<div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="width:600px;">';
		echo '<div class="modal-content" >';
		echo '<div class="modal-header">';
		echo "<h5 class='modal-title text-center' style='font-size:25px' id='exampleModalLongTitle'> Dataset details</h5>";
		echo '</div>';
		echo '<div class="modal-body">';
		echo  '<div class="row" style="font-size:15px; margin-bottom:10px;">
				 <span class="col-md-4" style="margin-top:5px;">Download dataset from: </span>
				 <span class="col-md-7" >'. $form->field($model,'provider')
				 ->dropdownList($datasets, ['class'=>'form-control'])->label("").'</span>
				</div>';
		echo  '<div class="row" style="font-size:15px; margin-bottom:10px;">
					<span class="col-md-4" style="margin-top:7px;"> Dataset id:</span> 
					<span class="col-md-7">'. $form->field($model,'dataset_id')
					->textInput(['class'=>'form-control'])->label("").'</span>
				</div>';
		echo  '<span class="row" style="font-size:15px;">
				<span class="col-md-4" style="margin-top:5px;">Store the dataset in:</span>
		 		<span class="col-md-8" style="margin-top:5px;">'.
		 	
				Html::textInput('osystemmount',$osystemMount,['id' => 'osystemmount','class'=>'mount-field','readonly'=>true,]).'&nbsp;&nbsp;' 
				. Html::a("$select_icon Select",'javascript:void(0);',['class'=>'select-mount-button btn btn-success btn-md']).'&nbsp'
		 			.Html::a("$clear_icon Clear",'javascript:void(0);',['class'=>'clear-mount-button btn btn-danger btn-md']).
		 		'</span>
			 	</span>';
		echo '</div>';
		echo '<div class="modal-footer">';
		echo '<div class="modal-loading hidden"><b>Downloading files <i class="fas fa-spinner fa-spin"></i></b></div>';
		echo Html::submitButton("Download",['class'=>"btn btn-success", 'id'=>'download-button']);
		echo "<button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>";
		echo '</div>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
		$form=ActiveForm::end();
		
	}
	
}

?>

