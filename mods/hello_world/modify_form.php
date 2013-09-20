<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_FORM_UTIL);
$_custom_css = $_base_path . 'mods/hello_world/module.css';
$_custom_head .= '</script><script type="text/javascript" src="'.AT_BASE_HREF.'mods/hello_world/modify.js"></script>';// add java scripts to the header path need to be change
require (AT_INCLUDE_PATH.'header.inc.php');
use PFBC\Form;
use PFBC\Element;
use PFBC\View;
?>

<script>


</script>




<?php


$sql = "SELECT * FROM %sforms ORDER BY rank";
$result = queryDB($sql, array(TABLE_PREFIX)) or die(at_db_error());
$count=count($result);

//$text="ffffffffffffffffffffffffffffffffffffffaddsfsd";
include("PFBC/Form.php");
$form = new Form("form");
$form->configure(array("action"=>$_base_path .'mods/hello_world/modify_form.php',"view" => new View\SideBySide(array("classLabels" => array("formAction"=>"row buttons","controlGroup"=>"row","fieldsetLabel"=>"group_form")))));
$form->addElement(new Element\HTML('<fieldset class=content-settings style="height:'.($count*60).'px;">'));
$form->addElement(new Element\HTML('<legend class=group_form>'._AT('personal_information').' ('._AT('optional').')'.'</legend>'));



foreach($result as $row)
{
    //get the options from the database for the for input type radio,checkbox and select
    if($row['type']=="radio"||$row['type']=="select"||$row['type']=="checkbox")
    {
        $choice= array();
        $sql2 = "SELECT choice FROM %sforms_options WHERE element_id='$row[id]'";
        $result2=queryDB($sql2, array(TABLE_PREFIX)) or die(at_db_error());
        foreach($result2 as $val)
        {
            $choice[]=$val['choice'];
        }
    }
    if($row['required']==1)
    {
        if($row['type']=="radio")
            $form->addElement(new Element\Radio($row['label'], $row['name'],$choice,array("value"=>(isset($_POST[$row['name']]) ? $_POST[$row['name']]:""),"id"=>$row['id'],"required"=>$row['required'])));
        if($row['type']=="select")
            $form->addElement(new Element\Select($row['label'], $row['name'],$choice,array("value"=>(isset($_POST[$row['name']]) ? $_POST[$row['name']]:""),"id"=>$row['id'],"required"=>$row['required'])));
        if($row['type']=="checkbox")
            $form->addElement(new Element\Checkbox($row['label'], $row['name'],$choice,array("value"=>(isset($_POST[$row['name']]) ? $_POST[$row['name']]:""),"id"=>$row['id'],"required"=>$row['required'])));
        if($row['type']=="textarea")
            $form->addElement(new Element\Textarea($row['label'], $row['name'],array("value"=>(isset($_POST[$row['name']]) ? $_POST[$row['name']]:""),"id"=>$row['id'],"required"=>$row['required'])));
        if($row['type']=="country")
            $form->addElement(new Element\Country($row['label'], $row['name'],array("value"=>(isset($_POST[$row['name']]) ? $_POST[$row['name']]:""),"id"=>$row['id'],"required"=>$row['required'])));
        if($row['type']=="password")
            $form->addElement(new Element\Password($row['label'], $row['name'],array("value"=>(isset($_POST[$row['name']]) ? $_POST[$row['name']]:""),"id"=>$row['id'],"required"=>$row['required'])));
        if($row['type']=="date")
            $form->addElement(new Element\Date($row['label'], $row['name'],array("value"=>(isset($_POST[$row['name']]) ? $_POST[$row['name']]:""),"id"=>$row['id'],"required"=>$row['required'])));
        if($row['type']=="textbox")
            $form->addElement(new Element\Textbox($row['label'], $row['name'],array("value"=>(isset($_POST[$row['name']]) ? $_POST[$row['name']]:""),"id"=>$row['id'],"required"=>$row['required'])));
        if($row['type']=="email")
            $form->addElement(new Element\Email($row['label'], $row['name'],array("value"=>(isset($_POST[$row['name']]) ? $_POST[$row['name']]:""),"id"=>$row['id'],"required"=>$row['required'])));
    }
    else
    {
        if($row['type']=="radio")
            $form->addElement(new Element\Radio($row['label'], $row['name'],$choice,array("value"=>(isset($_POST[$row['name']]) ? $_POST[$row['name']]:""),"id"=>$row['id'])));
        if($row['type']=="select")
            $form->addElement(new Element\Select($row['label'], $row['name'],$choice,array("value"=>(isset($_POST[$row['name']]) ? $_POST[$row['name']]:""),"id"=>$row['id'])));
        if($row['type']=="checkbox")
            $form->addElement(new Element\Checkbox($row['label'], $row['name'],$choice,array("value"=>(isset($_POST[$row['name']]) ? $_POST[$row['name']]:""),"id"=>$row['id'])));
        if($row['type']=="textarea")
            $form->addElement(new Element\Textarea($row['label'], $row['name'],array("value"=>(isset($_POST[$row['name']]) ? $_POST[$row['name']]:""),"id"=>$row['id'])));
        if($row['type']=="country")
            $form->addElement(new Element\Country($row['label'], $row['name'],array("value"=>(isset($_POST[$row['name']]) ? $_POST[$row['name']]:""),"id"=>$row['id'])));
        if($row['type']=="password")
            $form->addElement(new Element\Password($row['label'], $row['name'],array("value"=>(isset($_POST[$row['name']]) ? $_POST[$row['name']]:""),"id"=>$row['id'])));
        if($row['type']=="date")
            $form->addElement(new Element\Date($row['label'], $row['name'],array("value"=>(isset($_POST[$row['name']]) ? $_POST[$row['name']]:""),"id"=>$row['id'])));
        if($row['type']=="textbox")
            $form->addElement(new Element\Textbox($row['label'], $row['name'],array("value"=>(isset($_POST[$row['name']]) ? $_POST[$row['name']]:""),"id"=>$row['id'])));
        if($row['type']=="email")
            $form->addElement(new Element\Email($row['label'], $row['name'],array("value"=>(isset($_POST[$row['name']]) ? $_POST[$row['name']]:""),"id"=>$row['id'])));
    }
}

$form->addElement(new Element\HTML('</fieldset>'));
$form->addElement(new Element\HTML('<fieldset class=content-settings style="height:'.($count*60).'px;">'));
$form->addElement(new Element\HTML('<legend>Code</legend>'));
//$form->addElement(new Element\Textarea('','code',array("value"=>$text, "rows"=>($count*3),"cols"=>50)));
$form->addElement(new Element\HTML('<table id="db" class="data"><thead><tr><th> </th><th>Order</th><th>Label</th><th>id</th></th></thead>'));
//$form->addElement(new Element\HTML('<tr><td>Order</td><td>Label</td><td>id</td></tr>'));
$i=1;
foreach($result as $row)
{

    $form->addElement(new Element\HTML('<tr class="editable">'));
    $form->addElement(new Element\HTML('<td>'));
    //$form->addElement(new Element\HTML('<input type="checkbox" name="no[]" id="c'.$i.'">'));
    $form->addElement(new Element\HTML('</td>'));
    $form->addElement(new Element\HTML('<td contenteditable="true">'.$row['rank'].'</td><td contenteditable="true">'.$row['label'].'</td><td class="nonedit">'.$row['id'].'</td>'));
    $form->addElement(new Element\HTML('</tr>'));
    $i++;
}
$form->addElement(new Element\HTML('</table>'));
$form->addElement(new Element\HTML('</fieldset>'));
$form->addElement(new Element\HTML('</fieldset>'));

$form->addElement(new Element\Button("Apply","submit",array( "onclick" => "update_table();")));
$form->render();

var_dump($_POST);

?>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>