<?php


define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_FORM_UTIL);
//$custom_head.=""

require (AT_INCLUDE_PATH.'header.inc.php');

$path=$_base_path .'mods/hello_world/index_admin_test.php';
$num='';
//echo 'This is Test form';
use PFBC\Form;
use PFBC\Element;
use PFBC\View;

?>

<script language="JavaScript" src="sha-1factory.js" type="text/javascript"></script>

<script language="javascript">

function options(id){

if(type=='radio'){
    var newdiv=document.createElement('div');
    newdiv.setAttribute("id","control-group2");
    newdiv.innerHTML ="<input type='button' value='Add'>";
    //document.getElementById(id).appendChild(addoption)
    document.getElementById(id).appendChild(addoption);
    }

}


var count=new Array();

function addButton(id,i){


    var type=document.getElementById(id).value;

    if(type=="radio"||type=="select"||type=="checkbox"){
        count[i]=0;
        //alert("its radio");
        var newdiv=document.createElement('div');
        newdiv.setAttribute("id","control-group2");
        newdiv.innerHTML ="<input type='button' onclick=addOptions("+i+") value='Add a Option' id=add"+i+">";
        document.getElementById("foptions"+i).appendChild(newdiv);

    }
    else{
        var d=document.getElementById("foptions"+i);
        //var limit=d.childElementCount;
        //alert(limit);
        while(d.hasChildNodes())
       {
           d.removeChild(d.lastChild);
       }
         count[i]=0;

    }
}

function addOptions(i){
     var newdiv=document.createElement('div');
     newdiv.setAttribute("id","options-"+i+"-"+count[i]);
     newdiv.innerHTML ="Option: " + (count[i] + 1) + " <br><input type='text' name='userOption"+i+"[]' id=opt-"+i+"-"+count[i]+"><input type='button' value='remove' onclick=removeOption("+i+","+count[i]+")>";
     document.getElementById("foptions"+i).appendChild(newdiv);
    count[i]++;
}

function removeOption(i,j){
    var element=document.getElementById("options-"+i+"-"+j);
    element.parentNode.removeChild(element);
    count[i]--;
}

function preview(){
    document.getElementsByName('form').setAttribute('Value','preview')
}



</script>

<!--<fieldset>                                                                                                     -->
<!--    <form name=editform method="POST">                                                                         -->
<!--        <div id="dynamicInput">                                                                                -->
<!--            Entry 1<br><input type="text" name="myInputs[]">                                                   -->
<!--        </div>                                                                                                 -->
<!--        <input type="button" value="Add another text input" onClick="addInput('foptions2');">                  -->
<!--    </form>                                                                                                    -->
<!--                                                                                                               -->
<!--</fieldset>                                                                                                    -->
<!--                                                                                                               -->

<?php

//testSql();

//check whether final form is submitted
if(isset($_POST['form'] )){

    if($_POST['form']=='preview')
        echo "its preview time!!!!!";

    $count=$_POST['count'];
   /* for($i=1;$i<=(int)$count;$i++){

        //check for information
        if (isset($_POST['label-' . $i]) && isset($_POST['id-' . $i])) {
            $type = $_POST['select-' . $i];
            $label = $_POST['label-' . $i];
            //$name=$_POST['name-'.$i];
            $id = $_POST['id-' . $i];




            if (isset($_POST['require-' . $i])) {

                $sql = "INSERT INTO %sforms
              (id,rank,type,label,name,required)
               VALUES ('$id', '$i', '$type', '$label', '$id','1')";
            } else {
                $sql = "INSERT INTO %sforms
              (id,rank,type,label,name)
               VALUES ('$id', '$i', '$type', '$label', '$id')";

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


             //if its a field with option store those options in database
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


    }*/
}
?>


<?php

include("PFBC/Form.php");
$form = new Form("Form_Setup");
$form->configure(array("action"=>$path));
//$form->configure(array("action"=>$path,"view" => new View\SideBySide(array("classLabels" => array("formAction"=>"row buttons","controlGroup"=>"row","fieldsetLabel"=>"group_form")))));
//$form->configure(array("view" => new View\SideBySide(array("classLabels" => array( "formAction"=>"row buttons","controlGroup"=>"row")))));
//$form->configure(array("labels"=>array( "formAction"=>"frmact","controlGroup"=>"cntrl")));
$form->addElement(new Element\HTML('<legend>Add New Elements</legend>'));
$form->addElement(new Element\Hidden("user", "login"));
//$form->addElement(new Element\Email("Email Address:", "Email", array("required" => 1)));
//$form->addElement(new Element\Textbox('Ty', $row['name']));


function testSql(){

    $sql="INSERT INTO %sforms
              (id,rank,type,label,name)
               VALUES ('name', '2', 'textbox', 'First Name', 'fname','1')";
    $result = queryDB($sql, array(TABLE_PREFIX)) or die(at_db_error());
    if (!$result) {
        //require(AT_INCLUDE_PATH.'header.inc.php');
        $msg->addError('DB_NOT_UPDATED');
        $msg->printAll();
        //require(AT_INCLUDE_PATH.'footer.inc.php');
        exit;
    }

}


if(isset($_POST['count']))
{
    $num=$_POST['count'];
    $form->addElement(new Element\Textbox("Number of Inputs",'count',array("size"=>'4',"value"=>$num,"readonly" => "")));
    $form->addElement(new Element\HTML('<br>'));
    for($i=1;$i<=(int)$num;$i++){


        if(isset($_POST['select-'.$i])){
            $select=$_POST['select-'.$i];
            $label='';
            $name='';
            $id='';
            $chkd='';


            if(isset($_POST['label-'.$i]))
                $label=$_POST['label-'.$i];

            if(isset($_POST['name-'.$i]))
                $name=$_POST['name-'.$i];

            if(isset($_POST['id-'.$i]))
                $id=$_POST['id-'.$i];

            if(isset($_POST['require-'.$i]))
                $chkd=$_POST['require-'.$i][0];

            $form->addElement(new Element\Select("Type:", "select-".$i, array("textbox","radio","checkbox","select","textarea","password","date"),array("value"=>$select,"onChange"=>"addButton('select$i',$i)","id"=>"select$i")));
            $form->addElement(new Element\Textbox("Text",'label-'.$i,array("size"=>'15',"shortDesc" => "Value for the label of the element","value"=>$label)));
            //$form->addElement(new Element\Textbox("Name",'name-'.$i,array("size"=>'15',"shortDesc" => "A varible to name the element","value"=>$name)));
            $form->addElement(new Element\Textbox("Id",'id-'.$i,array("size"=>'15',"shortDesc" => "A varible to identify element(must be unique)","value"=>$id)));
            $form->addElement(new Element\Checkbox("", 'require-'.$i,array("required"),array("value"=>$chkd)));
            $form->addElement(new Element\HTML("<div id=foptions$i></div>"));
            $form->addElement(new Element\HTML('<br>'));
        }
        else{
            $form->addElement(new Element\Select("Type:", "select-".$i, array("textbox","radio","checkbox","select","textarea","password","date"),array("onChange"=>"addButton('select$i',$i)","id"=>"select$i")));
            $form->addElement(new Element\Textbox("Text",'label-'.$i,array("size"=>'15',"shortDesc" => "Value for the label of the element")));
            //$form->addElement(new Element\Textbox("Name",'name-'.$i,array("size"=>'15',"shortDesc" => "A varible to name the element")));
            $form->addElement(new Element\Textbox("Id",'id-'.$i,array("size"=>'15',"shortDesc" => "A varible to identify element(must be unique)")));
            $form->addElement(new Element\Checkbox("", 'require-'.$i,array("required")));
            $form->addElement(new Element\HTML("<div id=foptions$i></div>"));
            $form->addElement(new Element\HTML('<br>'));
        }


    }
    $form->addElement(new Element\Hidden("form", "save"));
    $form->addElement(new Element\Button("Back","button",array( "onclick" => "history.go(-1);")));
    $form->addElement(new Element\Button("Preview","submit",array( "onclick" => "preview();")));
    $form->addElement(new Element\Button("Submit","submit",array("name"=>"submt")));

}
else {
    $form->addElement(new Element\Textbox("Number of Inputs",'count',array("size"=>'4')));
    $form->addElement(new Element\Button("Next","submit"));
}



$form->render();

var_dump($_POST);

require (AT_INCLUDE_PATH.'footer.inc.php');

?>
