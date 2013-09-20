<?php
$_user_location	= 'users';
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
$_custom_css = $_base_path . 'mods/hello_world/module.css'; // use a custom stylesheet
$_custom_head .= '<script type="text/javascript" src="'.AT_BASE_HREF.'mods/hello_world/sha-1factory.js"></script>';
require (AT_INCLUDE_PATH.'header.inc.php');
global $languageManager, $_config, $moduleFactory;
use PFBC\Form;
use PFBC\Element;
use PFBC\View;
?>


<?php

if ($_SESSION['valid_user'] !== true) {
    $info = array('INVALID_USER', $_SESSION['course_id']);
    $msg->printInfos($info);
    exit;
}

if (isset($_POST['submit_form'])) {

    $_POST['first_name'] = str_replace('<', '', $_POST['first_name']);
    $_POST['second_name'] = str_replace('<', '', $_POST['second_name']);
    $_POST['last_name'] = str_replace('<', '', $_POST['last_name']);
    $dob=$_POST['date'];

    if($_POST['gender'] == _AT('male'))
        $gender='m';

    if($_POST['gender'] == _AT('female'))
        $gender='f';

    if (($_POST['gender'] != _AT('male')) && ($_POST['gender'] !=  _AT('female')))
    {
        $gender = 'n'; // not specified
    }

    $login = strtolower($_POST['login']);

    if (!$msg->containsErrors()) {

        if (($_POST['website']) && (!strstr($_POST['website'], '://'))) { $_POST['website'] = 'http://'.$_POST['website']; }
        if ($_POST['website'] == 'http://') { $_POST['website'] = ''; }

        if (isset($_POST['private_email'])) {
            $_POST['private_email'] = 1;
        } else {
            $_POST['private_email'] = 0;
        }

        // insert into the db.
        $_POST['website']    = $addslashes($_POST['website']);
        $_POST['first_name'] = $addslashes($_POST['first_name']);
        $_POST['second_name']= $addslashes($_POST['second_name']);
        $_POST['last_name']  = $addslashes($_POST['last_name']);
        $_POST['address']    = $addslashes($_POST['address']);
        $_POST['postal']     = $addslashes($_POST['postal']);
        $_POST['city']       = $addslashes($_POST['city']);
        $_POST['province']   = $addslashes($_POST['province']);
        $_POST['country']    = $addslashes($_POST['country']);
        $_POST['phone']      = $addslashes($_POST['phone']);

        $sql = "UPDATE %smembers SET website='$_POST[website]', first_name='$_POST[first_name]', second_name='$_POST[second_name]', last_name='$_POST[last_name]', dob='$dob', gender='$_POST[gender]', address='$_POST[address]', postal='$_POST[postal]', city='$_POST[city]', province='$_POST[province]', country='$_POST[country]', phone='$_POST[phone]', language='$_SESSION[lang]', private_email=$_POST[private_email], creation_date=creation_date, last_login=last_login WHERE member_id=$_SESSION[member_id]";

        $result =queryDB($sql, array(TABLE_PREFIX)) or die(at_db_error());
        if (!$result) {
            $msg->printErrors('DB_NOT_UPDATED');
            exit;
        }
        else{
            //process any extra fields
            $sql = "SELECT * FROM %sforms";
            $result = queryDB($sql, array(TABLE_PREFIX));

            $column= array();
            $values= array();
            //$column[]="`login`";
            //$values[]="'$_POST[login]'";
            foreach($result as $row)
            {
               $values[]="`".$row['name']."`='".$addslashes($_POST[$row['name']])."'";
            }

            $values=implode(',', $values);
            $sqlExtra="UPDATE %smembers_extra SET ".$values." WHERE login='".$_POST['login']."'";
            $result2 = queryDB($sqlExtra, array(TABLE_PREFIX)) or die(at_db_error());

            if (!$result2)
            {
                $msg->addError('DB_NOT_UPDATED');
                $msg->printAll();
                exit;
            }
        }

        $msg->addFeedback('PROFILE_UPDATED');
        header('Location: ./profile.php');
        exit;
    }
}
    $sql= "SELECT * FROM %smembers WHERE member_id=".$_SESSION['member_id'];
    $result = queryDB($sql, array(TABLE_PREFIX));
    $row1=$result[0];

    $sqlex= "SELECT * FROM %smembers_extra WHERE login='".$row1['login']."'";
    $result2 = queryDB($sqlex, array(TABLE_PREFIX));
    $row2=$result2[0];

if (!isset($_POST['submit_form'])){
    $_POST = array_merge((array)$row1,(array)$row2);

    $_POST['date']=$_POST['dob'];

    if ($_POST['private_email']==1) {
        $_POST['private_email'] = _AT('keep_email_private');
    } else {
        $_POST['private_email'] = "";
    }

    if ($_POST['gender']=="m") {
        $_POST['gender'] = _AT('male');
    } elseif($_POST['gender']=="f") {
        $_POST['gender'] =_AT('female');
    }else{
        $_POST['gender']=_AT('not_specified');
    }
}

?>



<?php

include("PFBC/Form.php");
$form = new Form("form");
$form->configure(array("action"=>$_base_path .'mods/hello_world/user_profile.php',"view" => new View\SideBySide(array("classLabels" => array("formAction"=>"row buttons","controlGroup"=>"row","fieldsetLabel"=>"group_form")))));
$form->addElement(new Element\HTML('<legend class="group_form">'._AT('required_information').'</legend>'));
$form->addElement(new Element\HTML('<div class="row"><label for="login">'._AT('login_name').'</label><br/><span id="login">'.stripslashes(htmlspecialchars($_POST['login'])).'</span></div>'));
$form->addElement(new Element\Hidden("member_id", intval($_POST['member_id'])));
$form->addElement(new Element\Hidden("login", stripslashes(htmlspecialchars($_POST['login']))));
$form->addElement(new Element\Checkbox(_AT('email_address').'<br>'.stripslashes(htmlspecialchars($_POST['email'])), "private_email", array(_AT('keep_email_private')),array("value"=>$_POST['private_email'],"id"=>"priv")));
$form->addElement(new Element\Textbox(_AT('first_name'),"first_name",array("value"=>stripslashes(htmlspecialchars($_POST['first_name'])),"id"=>"first_name","required"=>1)));
$form->addElement(new Element\Textbox(_AT('second_name'),"second_name" ,array("value"=>stripslashes(htmlspecialchars($_POST['second_name'])),"id"=>"second_name" )));
$form->addElement(new Element\Textbox(_AT('last_name'),"last_name" ,array("value"=> stripslashes(htmlspecialchars($_POST['last_name'])),"id"=>"last_name","required"=>1)));

if (admin_authenticate(AT_ADMIN_PRIV_USERS, TRUE)){
    if ($_POST['status'] == AT_STATUS_INSTRUCTOR) {
        $stat = _AT('instructor');
    } else if ($_POST['status'] == AT_STATUS_STUDENT) {
        $stat= _AT('student');
    }  else if ($_POST['status'] == AT_STATUS_DISABLED) {
        $stat =  _AT('disabled');
    } else {
        $stat= _AT('unconfirmed');
    }

    $form->addElement(new Element\Hidden("id",$_POST['member_id']));
    if (defined('AT_EMAIL_CONFIRMATION') && AT_EMAIL_CONFIRMATION){
        $form->addElement(new Element\Radio(_AT('account_status'), "status", array( _AT('disabled'), _AT('unconfirmed'),_AT('student'),_AT('instructor')), array("id"=>'status',"required"=>1,"value"=>$stat)));
    }
    else{
        $form->addElement(new Element\Radio(_AT('account_status'), "status", array( _AT('disabled'), _AT('student'),_AT('instructor')), array("id"=>'status',"required"=>1,"value"=>$stat)));
    }
    $form->addElement(new Element\Hidden("old_status",$_POST['old_status']));
}

$form->addElement(new Element\HTML('</fieldset><fieldset class="group_form">'));
$form->addElement(new Element\HTML('<legend class="group_form">'._AT('personal_information').' ('._AT('optional').')'.'</legend>'));

if (admin_authenticate(AT_ADMIN_PRIV_USERS, TRUE) && defined('AT_MASTER_LIST') && AT_MASTER_LIST){
    $form->addElement(new Element\Textbox(_AT('student_id'),"student_id",array("value"=>$_POST['student_id'],"size"=>"20")));
    $form->addElement(new Element\Password(_AT('student_pin'),"student_pin",array("value"=> stripslashes(htmlspecialchars($_POST['student_pin'])),"id"=>"student_pin","size"=>"15","maxlength"=>"15")));
}

$form->addElement(new Element\Date(_AT('date_of_birth'), "date",array("value"=>$_POST['date'])));
$form->addElement(new Element\Radio(_AT('gender'), "gender", array( _AT('male'), _AT('female'),_AT('not_specified')), array("value"=>$_POST['gender'],"id"=>'sex')));
$form->addElement(new Element\Textbox(_AT('street_address'),"address",array("value"=>stripslashes(htmlspecialchars($_POST['address'])),"id"=>"address","size"=>"40")));
$form->addElement(new Element\Textbox(_AT('postal_code'),"postal",array("value"=>stripslashes(htmlspecialchars($_POST['postal'])),"id"=>"postal","size"=>"7")));
$form->addElement(new Element\Textbox(_AT('city'),"city",array("value"=>stripslashes(htmlspecialchars($_POST['city'])),"id"=>"city")));
$form->addElement(new Element\Textbox(_AT('province'),"province",array("value"=>stripslashes(htmlspecialchars($_POST['province'])),"id"=>"province",)));
$form->addElement(new Element\Textbox(_AT('country'),"country",array("value"=>stripslashes(htmlspecialchars($_POST['country'])),"id"=>"country")));
$form->addElement(new Element\Textbox(_AT('phone'),"phone",array("value"=>stripslashes(htmlspecialchars($_POST['phone'])),"id"=>"phone","size"=>"11")));
$form->addElement(new Element\Textbox(_AT('web_site'),"website",array("value"=>stripslashes(htmlspecialchars($_POST['website'])),"id"=>"website","size"=>"40" )));

$sql = "SELECT * FROM %sforms ORDER BY rank";
$result = queryDB($sql, array(TABLE_PREFIX));

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
            $form->addElement(new Element\Radio($row['label'], $row['name'],$choice,array("value"=>$_POST[$row['name']],"id"=>$row['id'],"required"=>$row['required'])));
        if($row['type']=="select")
            $form->addElement(new Element\Select($row['label'], $row['name'],$choice,array("value"=>$_POST[$row['name']],"id"=>$row['id'],"required"=>$row['required'])));
        if($row['type']=="checkbox")
            $form->addElement(new Element\Checkbox($row['label'], $row['name'],$choice,array("value"=> $_POST[$row['name']],"id"=>$row['id'],"required"=>$row['required'])));
        if($row['type']=="textarea")
            $form->addElement(new Element\Textarea($row['label'], $row['name'],array("value"=> $_POST[$row['name']],"id"=>$row['id'],"required"=>$row['required'])));
        if($row['type']=="country")
            $form->addElement(new Element\Country($row['label'], $row['name'],array("value"=>$_POST[$row['name']],"id"=>$row['id'],"required"=>$row['required'])));
        if($row['type']=="password")
            $form->addElement(new Element\Password($row['label'], $row['name'],array("value"=> $_POST[$row['name']],"id"=>$row['id'],"required"=>$row['required'])));
        if($row['type']=="date")
            $form->addElement(new Element\Date($row['label'], $row['name'],array("value"=>$_POST[$row['name']],"id"=>$row['id'],"required"=>$row['required'])));
        if($row['type']=="textbox")
            $form->addElement(new Element\Textbox($row['label'], $row['name'],array("value"=> $_POST[$row['name']],"id"=>$row['id'],"required"=>$row['required'])));
        if($row['type']=="email")
            $form->addElement(new Element\Email($row['label'], $row['name'],array("value"=>$_POST[$row['name']],"id"=>$row['id'],"required"=>$row['required'])));
    }
    else
    {
        if($row['type']=="radio")
            $form->addElement(new Element\Radio($row['label'], $row['name'],$choice,array("value"=>$_POST[$row['name']],"id"=>$row['id'])));
        if($row['type']=="select")
            $form->addElement(new Element\Select($row['label'], $row['name'],$choice,array("value"=> $_POST[$row['name']],"id"=>$row['id'])));
        if($row['type']=="checkbox")
            $form->addElement(new Element\Checkbox($row['label'], $row['name'],$choice,array("value"=>$_POST[$row['name']],"id"=>$row['id'])));
        if($row['type']=="textarea")
            $form->addElement(new Element\Textarea($row['label'], $row['name'],array("value"=>$_POST[$row['name']],"id"=>$row['id'])));
        if($row['type']=="country")
            $form->addElement(new Element\Country($row['label'], $row['name'],array("value"=>$_POST[$row['name']],"id"=>$row['id'])));
        if($row['type']=="password")
            $form->addElement(new Element\Password($row['label'], $row['name'],array("value"=>$_POST[$row['name']],"id"=>$row['id'])));
        if($row['type']=="date")
            $form->addElement(new Element\Date($row['label'], $row['name'],array("value"=>$_POST[$row['name']],"id"=>$row['id'])));
        if($row['type']=="textbox")
            $form->addElement(new Element\Textbox($row['label'], $row['name'],array("value"=> $_POST[$row['name']],"id"=>$row['id'])));
        if($row['type']=="email")
            $form->addElement(new Element\Email($row['label'], $row['name'],array("value"=>$_POST[$row['name']],"id"=>$row['id'])));
    }
}

$form->addElement(new Element\Hidden("submit_form", "save"));
$form->addElement(new Element\Button(_AT('save'),"submit",array("name"=>"submit","accesskey"=>"s","onclick"=>"encrypt_password()","class"=>"button")));
$form->addElement(new Element\Button(_AT('cancel'),"submit",array("name"=>"cancel","class"=>"button","onclick"=>"history.go(-1);")));


//$form->addElement(new Element\HTML('</fieldset>'));

$form->render()

?>

<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>