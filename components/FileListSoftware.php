<?php
/*
* Helper for showing files and folders. The reason it exists is because it needs to be called recursively.
*
*
* @author Kostis Zagganas
*/

namespace app\components;
use yii\helpers\Html;
use Yii;
use webvimark\modules\UserManagement\models\User;

class FileListSoftware
{
    public static function printFileList($files,$level,$root)
    {
        $indentation=0;
        for ($i=0; $i<$level; $i++) 
            $indentation+=30;
        // print_r($files);
        // exit(0);
        foreach ($files as $key=>$value)
        {
            if (strpos($key,'file_')===false)
            {
                $folder_value=str_replace($root,'',$key);
                $folder_split=explode('/',$folder_value);
                $folder_name=end($folder_split);

                echo "<div class='row selection-row'>";
                echo "<div class='non-selectable col-md-3' style='margin-left:" . $indentation 
                            . "px;''><i class='fa fa-folder' aria-hidden='true'></i>&nbsp;&nbsp;$folder_name";
                echo Html::hiddenInput('hiddenPath',$folder_value);
                echo "</div></div>";

                if (!empty($value))
                {
                    self::printFileList($value,$level+1,$root);
                }
            }
            else
            {
                $file_value=str_replace($root,'',$value);
                // print_r($root);
                // exit(0);
                $file_split=explode('/',$file_value);
                $file_name=end($file_split);
                echo "<div class='row selection-row'>";
                echo "<div class='selectable col-md-3' style='margin-left:" . $indentation 
                            . "px;''><i class='fa fa-file-alt' aria-hidden='true'></i>&nbsp;&nbsp;$file_name";
                echo Html::hiddenInput('hiddenPath',$file_value);
                echo "</div></div>";
                
            }

            
            
        }
        
    }
    
}
