<?php


define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_FORM_UTIL);
require (AT_INCLUDE_PATH.'header.inc.php');

$path=$_base_path .'mods/hello_world/index_admin_test.php';

//echo 'This is Test form';
use PFBC\Form;
use PFBC\Element;
use PFBC\View;

?>

<script language="JavaScript" src="sha-1factory.js" type="text/javascript"></script>

<?php

include("PFBC/Form.php");
$form = new Form("Form_Setup");
$form->configure(array("action"=>$path));
//$form->configure(array("action"=>$path,"view" => new View\SideBySide(array("classLabels" => array("formAction"=>"row buttons","controlGroup"=>"row","fieldsetLabel"=>"group_form")))));
//$form->configure(array("view" => new View\SideBySide(array("classLabels" => array( "formAction"=>"row buttons","controlGroup"=>"row")))));
//$form->configure(array("labels"=>array( "formAction"=>"frmact","controlGroup"=>"cntrl")));
$form->addElement(new Element\HTML('<legend>Add New Elements</legend>'));
$form->addElement(new Element\Hidden("form", "login"));
//$form->addElement(new Element\Email("Email Address:", "Email", array("required" => 1)));
//$form->addElement(new Element\Textbox('Ty', $row['name']));


function testSql(){

    $sql="INSERT INTO %sforms
              (id,rank,type,label,name)
               VALUES (NULL, '2', 'textbox', 'First Name', 'fname')";
    $result = queryDB($sql, array(TABLE_PREFIX)) or die(at_db_error());
    if (!$result) {
        //require(AT_INCLUDE_PATH.'header.inc.php');
        $msg->addError('DB_NOT_UPDATED');
        $msg->printAll();
        //require(AT_INCLUDE_PATH.'footer.inc.php');
        exit;
    }

}
$num='';

if(isset($_POST['count']))
{
    $num=$_POST['count'];
    $form->addElement(new Element\Textbox("Number of Inputs",'count',array("size"=>'4',"value"=>$num)));
    $form->addElement(new Element\HTML('<br>'));
    for($i=1;$i<=(int)$num;$i++){


        if(isset($_POST['select-'.$i])){
            $select=$_POST['select-'.$i];
            $label='';
            $name='';

            if(isset($_POST['label-'.$i]))
                $label=$_POST['label-'.$i];

            if(isset($_POST['name-'.$i]))
                $name=$_POST['name-'.$i];

            $form->addElement(new Element\Select("Type:", "select-".$i, array("textbox","radio","checkbox","select","textarea","password","date"),array("value"=>$select)));
            $form->addElement(new Element\Textbox("Text",'label-'.$i,array("size"=>'15',"shortDesc" => "Value for the label of the element","value"=>$label)));
            $form->addElement(new Element\Textbox("Name",'name-'.$i,array("size"=>'15',"shortDesc" => "A varible to identify element","value"=>$name)));
            $form->addElement(new Element\HTML('<br>'));
        }
        else{
            $form->addElement(new Element\Select("Type:", "select-".$i, array("textbox","radio","checkbox","select","textarea","password","date")));
            $form->addElement(new Element\Textbox("Text",'label-'.$i,array("size"=>'15',"shortDesc" => "Value for the label of the element")));
            $form->addElement(new Element\Textbox("Name",'name-'.$i,array("size"=>'15',"shortDesc" => "A varible to identify element")));
            $form->addElement(new Element\HTML('<br>'));
        }


    }

}
else
    $form->addElement(new Element\Textbox("Number of Inputs",'count',array("size"=>'4')));

$form->addElement(new Element\Button("Next","submit",array("name"=>"submt")));
$form->render();

var_dump($_POST);

require (AT_INCLUDE_PATH.'footer.inc.php');

?>
