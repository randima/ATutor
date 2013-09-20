<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2013                                                   */
/* Sandamal S.T.R                                                       */
/*                                                                      */
/* This page only visible for administrators and allow them to create   */
/* custom form to gather extra information they needed through          */
/* registration                                                         */
/************************************************************************/


define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_FORM_UTIL);
$_custom_css = $_base_path . 'mods/hello_world/module.css';
$_custom_head .= '<script type="text/javascript" src="'.AT_BASE_HREF.'mods/hello_world/element.js"></script>';// add java scripts to the header path need to be change
$path=$_base_path .'mods/hello_world/index_form_utility.php';//path should change to relevant directory
require (AT_INCLUDE_PATH.'header.inc.php');

$num='';
//echo 'This is Test form';
use PFBC\Form;
use PFBC\Element;
use PFBC\View;
?>

<script language="JavaScript" src="sha-1factory.js" type="text/javascript"></script>


<?php

if(isset($_POST['new'] ))
{
    $sql="TRUNCATE TABLE %sforms; TRUNCATE TABLE %sforms_options;";
    queryDB($sqlTable, array(TABLE_PREFIX));
}

//********check whether final form is submitted, if submitted update database
if(isset($_POST['form'] )){

    //if($_POST['form']=='preview')
      //  echo "its preview time!!!!!";
    $count=$_POST['count'];
    $existing_id = array();
    for($i=1;$i<=(int)$count;$i++){

         //*********check for existing ids
         if (isset($_POST['label-' . $i]) && isset($_POST['id-' . $i])) {
             $id = $_POST['id-' . $i];
             $sql="SELECT * FROM %sforms WHERE id='$id'";
             //echo $sql;
             $result = queryDB($sql, array(TABLE_PREFIX));
             $no_rows = count($result);

             if($no_rows!=0)
             {
                 $existing_id[]=$id;
             }

         }
    }
    $update=TRUE;

    if ($existing_id) {
        $existing_ids = implode(', ', $existing_id);
        //$msg->addError(array('ID_EXISTS','hyhhbvvvcccx'));
        //$msg->printAll();
        $update=FALSE;
        echo '<div id="error"><ul>
				<li>Following IDs already exist try different ones</li>
				<li>'.$existing_ids.'</li>
				</ul>
	            </div>';

    }
    if($update)
    {
        $sql="SELECT MAX(rank) as highestrank FROM %sforms";  // added newly
        $result = queryDB($sql, array(TABLE_PREFIX))or die(at_db_error());
        $start=$result[0]['highestrank'];

        $sqlTable="ALTER TABLE %smembers_extra ADD ( `";
        for($i=1;$i<=(int)$count;$i++)
        {
        //*********check for information
        if (isset($_POST['label-' . $i]) && isset($_POST['id-' . $i])) {
            $type = $_POST['select-' . $i];
            $label = $_POST['label-' . $i];
            //$name=$_POST['name-'.$i];
            $id = $_POST['id-' . $i];
            $size=30;
            $rank=$start+$i;

            //$sql="SELECT * FROM %sforms WHERE id=`$id`";

            $sqlTable=$sqlTable.$id."` varchar($size),`";


               //***SQL query if the form element is required
            if (isset($_POST['require-' . $i])) {

                $sql = "INSERT INTO %sforms
              (id,rank,type,label,name,required)
               VALUES ('$id', '$rank', '$type', '$label', '$id','1')";
            } else {
                $sql = "INSERT INTO %sforms
              (id,rank,type,label,name)
               VALUES ('$id', '$rank', '$type', '$label', '$id')";

            }


            $result = queryDB($sql, array(TABLE_PREFIX)) or die(at_db_error());
            echo $sql . "<br>";
            if (!$result) {
                //require(AT_INCLUDE_PATH.'header.inc.php');
                $msg->addError('DB_NOT_UPDATED');
                $msg->printAll();
                //require(AT_INCLUDE_PATH.'footer.inc.php');
                exit;
            }


             //if its a field with options, store those options in database
            if($type=="radio"||$type=="select"||$type=="checkbox")
            {
                if(isset( $_POST['userOption' . $i]))
                {
                    foreach($_POST['userOption' . $i] as $choice)
                    {

                        $sqlopt="INSERT INTO %sforms_options (element_id,choice)
                        VALUES ('$id','$choice')";
                        echo $sqlopt . "<br>";
                        $result2 = queryDB($sqlopt, array(TABLE_PREFIX)) or die(at_db_error());

                    }
                }
            }
        }

        }
        $sqlTable=substr_replace($sqlTable," ",-2).")";
        echo $sqlTable;
        queryDB($sqlTable, array(TABLE_PREFIX));
    }
}
?>


<?php

include("PFBC/Form.php");
$form = new Form("Form_Setup");
$form->configure(array("action"=>$path));
//$form->configure(array("action"=>$path,"view" => new View\SideBySide(array("classLabels" => array("formAction"=>"row buttons","controlGroup"=>"row","fieldsetLabel"=>"group_form")))));
//$form->configure(array("view" => new View\SideBySide(array("classLabels" => array( "formAction"=>"row buttons","controlGroup"=>"row")))));
//$form->configure(array("labels"=>array( "formAction"=>"frmact","controlGroup"=>"cntrl")));
$form->addElement(new Element\HTML('<legend class="group_form">Add New Elements (Optional)</legend>'));
$form->addElement(new Element\HTML('<div id=feedback style="width:90%;">Add new fields to your Registration Form.</div>'));
$form->addElement(new Element\Hidden("user", "login"));
//$form->addElement(new Element\Email("Email Address:", "Email", array("required" => 1)));
//$form->addElement(new Element\Textbox('Ty', $row['name']));


//***********sql Test function
function testSql(){

    $sql="INSERT INTO %sforms
              (id,rank,type,label,name)
               VALUES ('name', '2', 'textbox', 'First Name', 'ffname','1')";
    $result = queryDB($sql, array(TABLE_PREFIX)) or die(at_db_error());

    if (!$result) {
        $msg->addError('DB_NOT_UPDATED');
        $msg->printAll();
        exit;
    }

}

//****************check for number of elements in the form
if(isset($_POST['count']))
{
    if(isset($_POST['length']))
    {
        $form->addElement(new Element\Hidden("length",$_POST['length']+1));
    }
    else
    {
        $form->addElement(new Element\Hidden("length", "1"));
    }

    $num=$_POST['count'];
    $form->addElement(new Element\Textbox("Number of Inputs",'count',array("size"=>'4',"value"=>$num,"readonly" => "","title"=>"Go `Back` to change the number of inputs")));//make it read only if its set,to change select back
    $form->addElement(new Element\HTML('<br>'));
    //************for each element get details
    for($i=1;$i<=(int)$num;$i++){


        if(isset($_POST['select-'.$i])){
            $select=$_POST['select-'.$i];
            $label='';
            $name='';
            $id='';
            $chkd='';

            //***********used to keep details if it is already filled
            if(isset($_POST['label-'.$i]))
                $label=$_POST['label-'.$i];

            if(isset($_POST['name-'.$i]))
                $name=$_POST['name-'.$i];

            if(isset($_POST['id-'.$i]))
                $id=$_POST['id-'.$i];

            if(isset($_POST['require-'.$i]))
                $chkd=$_POST['require-'.$i][0];
            //********************

            $form->addElement(new Element\Select("Type:", "select-".$i, array("textbox","radio","checkbox","select","textarea","password","date","country"),array("value"=>$select,"onChange"=>"addButton('select$i',$i)","id"=>"select$i")));
            $form->addElement(new Element\Textbox("Text",'label-'.$i,array("size"=>'15',"shortDesc" => "Value for the label of the element","value"=>$label)));
            //$form->addElement(new Element\Textbox("Name",'name-'.$i,array("size"=>'15',"shortDesc" => "A varible to name the element","value"=>$name)));
            $form->addElement(new Element\Textbox("Id",'id-'.$i,array("size"=>'15',"shortDesc" => "A varible to identify element(must be unique)","value"=>$id)));
            $form->addElement(new Element\Checkbox("", 'require-'.$i,array("required"),array("value"=>$chkd)));
            $form->addElement(new Element\HTML("<div id=foptions$i></div>"));
            $form->addElement(new Element\HTML('<br>'));
        }
        else{
            $form->addElement(new Element\Select("Type:", "select-".$i, array("textbox","radio","checkbox","select","textarea","password","date","country"),array("onChange"=>"addButton('select$i',$i)","id"=>"select$i")));
            $form->addElement(new Element\Textbox("Text",'label-'.$i,array("size"=>'15',"shortDesc" => "Value for the label of the element")));
            //$form->addElement(new Element\Textbox("Name",'name-'.$i,array("size"=>'15',"shortDesc" => "A varible to name the element")));
            $form->addElement(new Element\Textbox("Id",'id-'.$i,array("size"=>'15',"shortDesc" => "A varible to identify element(must be unique)")));
            $form->addElement(new Element\Checkbox("", 'require-'.$i,array("required")));
            $form->addElement(new Element\HTML("<div id=foptions$i></div>"));
            $form->addElement(new Element\HTML('<br>'));
        }


    }
    $form->addElement(new Element\Hidden("form", "save"));

    $form->addElement(new Element\Button("Back","button",array( "onclick" => "history.go(-".($_POST['length']+1).");")));
    //$form->addElement(new Element\Button("Preview","submit",array( "onclick" => "preview();")));
    $form->addElement(new Element\Button("Submit","submit",array("name"=>"submt")));

}
else {
    $form->addElement(new Element\Textbox("Number of Inputs",'count',array("size"=>'4')));
    $form->addElement(new Element\Checkbox("", 'new',array("New (If unchecked it will add to current list)")));
    $form->addElement(new Element\Button("Next","submit"));
}



$form->render();

var_dump($_POST);

require (AT_INCLUDE_PATH.'footer.inc.php');

?>
