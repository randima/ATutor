<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_FORM_UTIL);
$_custom_css = $_base_path . 'mods/hello_world/module.css';
$_custom_head .= '<script type="text/javascript" src="'.AT_BASE_HREF.'mods/hello_world/element.js"></script><script type="text/javascript" src="'.AT_BASE_HREF.'mods/hello_world/modify.js"></script>';// add java scripts to the header path need to be change
require (AT_INCLUDE_PATH.'header.inc.php');
use PFBC\Form;
use PFBC\Element;
use PFBC\View;
?>


<?php
if(isset($_POST['submit_form'])){

    if ($_POST['submit_form']=="cancel")
    {
    $msg->addFeedback('CANCELLED');
    header('Location: .php');
    exit;
    }

    if($_POST['allow_reg']=='Enable')
    {
        $val=1;
    }
    if($_POST['allow_reg']=='Disable'){
        $val=0;
    }

    $sql="REPLACE INTO %sconfig VALUES('allow_reg_tool',$val)";
    queryDB($sql, array(TABLE_PREFIX));
    $msg->addFeedback('ACTION_COMPLETED_SUCCESSFULLY');
    header('Location: '.$_SERVER['PHP_SELF']);
    exit;

}

$sql = "SELECT * FROM %sforms";
$result = queryDB($sql, array(TABLE_PREFIX)) or die(at_db_error());
$count=count($result);

//$text="ffffffffffffffffffffffffffffffffffffffaddsfsd";
include("PFBC/Form.php");
$form = new Form("form");

$form->configure(array("action"=>$_base_path .'mods/hello_world/config.php',"view" => new View\SideBySide(array("classLabels" => array("formAction"=>"row buttons","controlGroup"=>"row","fieldsetLabel"=>"group_form")))));
//$form->addElement(new Element\HTML('<fieldset class=content-settings style="height:'.($count*60).'px;">'));
$form->addElement(new Element\HTML('<legend class=group_form>Settings</legend>'));
$form->addElement(new Element\HTML('<div id=feedback style="width:90%;">Setup your general Form configuration options.</div>'));
$form->addElement(new Element\HTML('<fieldset>'));
$form->addElement(new Element\HTML('<legend>Allow Self Registration</legend>'));
$form->addElement(new Element\Radio('(Default:Enabled)','allow_reg',array('Enable','Disable'),array("value"=>$_config['allow_reg_tool']?'Enable':'Disable',"id"=>'allow_reg')));
$form->addElement(new Element\Hidden("submit_form","save"));
$form->addElement(new Element\HTML('</fieldset>'));

$form->addElement(new Element\Button(_AT('save'),"submit",array("name"=>"submit","accesskey"=>"s","class"=>"button")));
$form->addElement(new Element\Button(_AT('cancel'),"submit",array("name"=>"Cancel","class"=>"button","onClick"=>"checkCancel();")));
$form->render();
var_dump($_POST);

?>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>