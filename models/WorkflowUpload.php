<?php
/**
 * This model is used to upload a new docker software image (form and actions)
 * 
 * @author Kostis Zagganas
 * First version: December 2018
 */
namespace app\models;

use Yii;
use yii\db\Query;
use yii\web\UploadedFile;
use yii\helpers\Url;
use webvimark\modules\UserManagement\models\User;
use app\models\Workflow;

/**
 * This is the model class for table "software_upload".
 *
 * @property int $id
 * @property string $name
 * @property string $version
 * @property string $description
 * @property string $image
 * @property double $execution_time
 * @property double $cpu_time
 * @property double $memory_amount
 * @property bool $gpu
 * @property bool $io
 * @property string $default_command
 * @property file $imageFile
 */
class WorkflowUpload extends \yii\db\ActiveRecord
{
    public $workflowFile;
    public $dois='';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'workflow_upload';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [    
            [['name'], 'string', 'max' => 100],
            [['description'], 'string'],
            [['version'], 'string', 'max' => 80],
            [['location'], 'string'],
            [['name','version',],'required'],
            [['name',],'allowed_name_chars'],
            [['version',],'allowed_version_chars'],
            [['workflowFile'], 'file','skipOnEmpty' => false, 'checkExtensionByMimeType' => false, 'extensions' => ['yaml','cwl','zip','gz','tar']],
            [['visibility','description'],'required'],
            [['biotools'],'string','max'=>255],
            [['version'], 'uniqueSoftware'],
            [['covid19'],'required'],
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        
        return [
            'id' => 'ID',
            'name' => 'Workflow name * ',
            'version' => 'Workflow version * ',
            'workflowFile' => 'Upload your workflow files (either single file or compressed) * ',
            'biotools'=>'Link in bio.tools (optional)',
            'visibility' => 'Visible to',
            'description'=> 'Workflow description * ',
            'covid19' => 'Workflow is related to COVID-19 research',
        ];
    }


    public function upload()
    {
        $errors="";

        $username=User::getCurrentUser()['username'];
        $this->description=$this->quotes($this->description);
        $workflowFilePath=$this->quotes('');
        $workFlowFileExt=$this->quotes('');
        $this->covid19=($this->covid19=='1') ? "'t'" : "'f'";
        $this->biotools=$this->quotes($this->biotools);
        $this->github_link=$this->quotes('');

        $dataFolder=Yii::$app->params['tmpWorkflowPath'] . '/' . str_replace(' ','-',$this->name) . '/' . str_replace(' ','-',$this->version) . '/';
        if (!is_dir($dataFolder))
        {
            $command="mkdir -p $dataFolder";
            exec($command,$ret,$outdir); 
        }
        //add dois string in a file and pass it on to python

        if (!empty($this->dois))
        {
            $doiFile=$dataFolder . 'dois.txt';

            file_put_contents($doiFile, $this->dois . "\n");

            $doiFile=$this->quotes($doiFile);
        }
        else
        {
            $doiFile=$this->quotes('');;
        }
            
        if (!empty($this->workflowFile))
        {

            $workflowFilePath=$dataFolder . $this->workflowFile->baseName . '.' . $this->workflowFile->extension;
            $workflowFileExt=$this->workflowFile->extension;

            $this->workflowFile->saveAs($workflowFilePath);
        }

        $command="chmod 777 $dataFolder -R";
        exec($command,$ret,$outdir); 
        
        $workflowFilePath=$this->quotes($workflowFilePath);
        $this->name=$this->quotes($this->name);
        $this->version=$this->quotes($this->version);
        $username=$this->quotes($username);

        $arguments=[$this->name, $this->version, $workflowFilePath, $workflowFileExt, 
                    $username, $this->visibility, $this->description, $this->biotools, $doiFile, $this->covid19,$this->github_link];

        // $command="sudo -u user /data/www/schema_test/scheduler_files/imageUploader.py ";
        $command="sudo -u ". Yii::$app->params['systemUser'] . " " . Yii::$app->params['scriptsFolder'] . "workflowUploader.py ";
        $command.= implode(" ", $arguments) . " ";
        $command.= "2>&1";

        // print_r($command);
        // print_r("<br />");
        // exit(0);



        exec($command,$out,$ret);


        // print_r($out);
        // print_r("<br /><br />");
        // print_r($ret);
        // exit(0);


        $errors='';
        $warning='';
        $success='';
        $prog_output="";
        switch($ret)
        {
            case 0:
                $success="Workflow successfully uploaded!";
                exec("rm $doiFile");
                break;
            case 2:
                $errors.="Error: code $ret. ";
                $errors.="Missing \"inputs\" specification in your workflow.";
                $errors.="<br />Please correct the file syntax and try again or contact an administrator.";
                break;
            case 11:
                $errors.="Error: code $ret. ";
                $errors.="There was an problem decoding your CWL file:<br />";
                foreach ($out as $line)
                {
                    $errors.=$line . "<br />";
                }
                $errors.="<br />Please correct the file syntax and try again or contact an administrator.";
                break;
            case 12:
                $errors.="Error: code $ret. ";
                $errors.="One of your CWL files has does not specify a class.";
                $errors.="<br />Please correct the file syntax and try again or contact an administrator.";
                break;
            case 13:
                $errors.="Error: code $ret. ";
                $errors.="Your uploaded file contains more than one workflow files.";
                $errors.="<br />Please contact an administrator.";
                break;
            case 14:
                $errors.="Error: code $ret. ";
                $errors.="None of the uploaded files contain a \"class: Workflow\" specification";
                $errors.="<br />Please correct the file syntax and try again or contact an administrator.";
                break;
            case 30:
                $success.="Workflow successfully uploaded!<br />";
                $warning.="You did not specify any inputs for your workflow.";
                break;
            case 34:
                $errors.="Error: code $ret. ";
                $errors.="One of your workflow inputs does not have a type specification";
                $errors.="<br />Please correct the error and try again or contact an administrator.";
                break;
            case 35:
                $errors.="Error: code $ret. ";
                $errors.="One of your workflow inputs has an urecognized type specification.";
                // foreach ($out as $line)
                // {
                //     $errors.=$line . "<br />";
                // }
                $errors.="<br />Please correct the error and try again or contact an administrator.";
                break;
            case 36:
                $errors.="Error: code $ret. ";
                $errors.="One of your enum workflow inputs has an urecognized type specification.";
                $errors.="<br />Please correct the error and try again or contact an administrator.";
                break;
            case 37:
                $errors.="Error: code $ret. ";
                $errors.="One of your enum workflow inputs has an urecognized specification.";
                $errors.="<br />Please correct the error and try again or contact an administrator.";
                break;
            case 38:
                $errors.="Error: code $ret. ";
                $errors.="One of your enum workflow inputs does not contain any symbols";
                $errors.="<br />Please correct the error and try again or contact an administrator.";
                break;
                break;
            case 50:
                $errors.="Error: code $ret. ";
                $errors.="Input declaration structure not recognized";
                $errors.="<br />Please contact an administrator to assist you.";
                break;
            default:
                $errors.="Error: code $ret. ";
                $errors.="<br />An unexpected error occurred.";
                foreach ($out as $line)
                {
                    $errors.=$line . "<br />";
                }
                $errors.="<br />Please contact an administrator.";
                break;
        }        

        return [$errors,$success,$warning];
    }

    /*
     * This functions are used for validation
     * (doubtful if it works).
     */
    public function uniqueSoftware($attribute, $params, $validator)
    {
        // print_r($this->name);
        // exit(0);
        $workflows=Workflow::find()->where(['name'=>$this->name, 'version'=>$this->version])->all();
        if (!empty($workflows))
        {
                $this->addError($attribute, "Software $this->name v.$this->version already exists. Please specify another name or version.");
                return false;
        }
        return true;
    }


    public function quotes($string)
    {
        return "'" . $string . "'";
    }

    public function allowed_name_chars($attribute, $params, $validator)
    {
        if(preg_match('/[^A-Za-z_\-0-9]/', $this->$attribute))
        {
                $this->addError($attribute, "Software name can only contain letters, numbers, hyphens ( - ) and underscores ( _ )");
                return false;
        }
        return true;
    }

    public function allowed_version_chars($attribute, $params, $validator)
    {
        if(preg_match('/[^A-Va-z_\-0-9\.]/', $this->$attribute))
        {
                $this->addError($attribute, "Software version can only contain letters, numbers, hyphens ( - ) and underscores ( _ ) and full stops (.)");
                return false;
        }
        return true;
    }
}