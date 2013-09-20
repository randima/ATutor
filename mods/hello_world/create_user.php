<?php
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2013                                                   */
/* Sandamal S.T.R                                                       */
/************************************************************************/


define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
admin_authenticate(AT_ADMIN_PRIV_FORM_UTIL);
$_custom_css = $_base_path . 'mods/hello_world/module.css'; // use a custom stylesheet
$_custom_head .= '<script type="text/javascript" src="'.AT_BASE_HREF.'mods/hello_world/sha-1factory.js"></script>';
require (AT_INCLUDE_PATH.'header.inc.php');
global $languageManager, $_config, $moduleFactory;
use PFBC\Form;
use PFBC\Element;
use PFBC\View;
?>


<script type="text/javascript">
    function encrypt_password()
    {
        document.getElementById("password_error").value="";


        err = verify_password(document.getElementById("form_password1").value, document.getElementById("form_password2").value);

        if (err.length > 0)
        {
            document.getElementById("password_error").value = err;
        }
        else
        {
            document.getElementById("form_password_hidden").value = hex_sha1(document.getElementById("form_password1").value);
        }
    }
</script>


<?php


//if($_config['allow_registration'] != 1){
//    $msg->addInfo('REG_DISABLED');
//    exit;
//}
//process posted data
if (isset($_POST['submit_form']))
{
    if (!$_POST['overwrite'] && !empty($_POST['student_id'])) {
        $result = mysql_query("SELECT * FROM ".TABLE_PREFIX."master_list WHERE public_field='$_POST[student_id]' && member_id<>0",$db);
        if (mysql_num_rows($result) != 0) {
            $msg->addError('CREATE_MASTER_USED');
        }
    }

    /* email check */
    $chk_email = $addslashes($_POST['email']);
    $chk_login = $addslashes($_POST['login']);



    $_POST['password'] = $_POST['form_password_hidden'];
    $_POST['first_name'] = trim($_POST['first_name']);
    $_POST['second_name'] = trim($_POST['second_name']);
    $_POST['last_name'] = trim($_POST['last_name']);

    $_POST['first_name'] = str_replace('<', '', $_POST['first_name']);
    $_POST['second_name'] = str_replace('<', '', $_POST['second_name']);
    $_POST['last_name'] = str_replace('<', '', $_POST['last_name']);


    /* check for special characters */
    if (!(preg_match("/^[a-zA-Z0-9_.-]([a-zA-Z0-9_.-])*$/i", $_POST['login']))) {
        $msg->addError('LOGIN_CHARS');
    } else {
        $sql = "SELECT * FROM %smembers WHERE login='%s'";
        $rows_logins = queryDB($sql, array(TABLE_PREFIX, $chk_login));
        $num_rows_logins = count($rows_logins);

        if ($num_rows_logins != 0) {
            $msg->addError('LOGIN_EXISTS');
        } else {
            $sql = "SELECT * FROM %sadmins WHERE login='%s'";
            $rows_admins = queryDB($sql, array(TABLE_PREFIX, $chk_login));
            $num_rows_admins = count($rows_admins);
            if ($num_rows_admins != 0) {
                $msg->addError('LOGIN_EXISTS');
            }
        }
    }


    /* password check: password is verified front end by javascript. here is to handle the errors from javascript */
    $password_error = $_POST['password_error'];
    if ($password_error && $password_error <> "") {
        $separator = ',';
        $pwd_errors = explode($separator, $password_error);

        foreach ($pwd_errors as $pwd_error) {
            $pwd_error = strip_tags(urldecode($pwd_error));
            if ($pwd_error == "missing_password") {
                $missing_fields[] = _AT('password');
            } else {
                $msg->addError($pwd_error);
            }
        }
    }

    if (!preg_match("/^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,6}$/i", $_POST['email'])) {
        $msg->addError('EMAIL_INVALID');
    }
    $sql = "SELECT * FROM %smembers WHERE email='%s'";
    $rows_email = queryDB($sql,array(TABLE_PREFIX, $chk_email));
    $num_rows_email = count($rows_email);

    if ($num_rows_email != 0) {
        $msg->addError('EMAIL_EXISTS');
    } else if ($_POST['email'] != $_POST['email2']) {
        $msg->addError('EMAIL_MISMATCH');
    }

    $_POST['login'] = strtolower($_POST['login']);

    $dob=$_POST['date'];


    if($_POST['gender'] == _AT('male'))
        $gender='m';

    if($_POST['gender'] == _AT('female'))
        $gender='f';

    if (($_POST['gender'] != _AT('male')) && ($_POST['gender'] !=  _AT('female')))
    {
        $gender = 'n'; // not specified
    }

    if (!$msg->containsErrors())
    {
        if (($_POST['website']) && (!strstr($_POST['website'],"://")))
        {
            $_POST['website'] = "http://".$_POST['website'];
        }
        if ($_POST['website'] == 'http://')
        {
            $_POST['website'] = '';
        }
        if (isset($_POST['private_email']))
        {
            $_POST['private_email'] = 1;
        } else {
            $_POST['private_email'] = 0;
        }
        $_POST['postal'] = strtoupper(trim($_POST['postal']));

        $_POST['email']      = $addslashes($_POST['email']);
        $_POST['login']      = $addslashes($_POST['login']);
        $_POST['password']   = $addslashes($_POST['password']);
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



        if (defined('AT_EMAIL_CONFIRMATION') && AT_EMAIL_CONFIRMATION) {
            $status = AT_STATUS_UNCONFIRMED;
        } else {
            $status = AT_STATUS_STUDENT;
        }
        $now = date('Y-m-d H:i:s'); // we use this later for the email confirmation.

        /* insert into the db */
        $sql = "INSERT INTO %smembers
		              (login,
		               password,
		               email,
		               website,
		               first_name,
		               second_name,
		               last_name,
		               dob,
		               gender,
		               address,
		               postal,
		               city,
		               province,
		               country,
		               phone,
		               status,
		               preferences,
		               creation_date,
		               language,
		               inbox_notify,
		               private_email,
		               last_login)
		       VALUES ('$_POST[login]',
		               '$_POST[password]',
		               '$_POST[email]',
		               '$_POST[website]',
		               '$_POST[first_name]',
		               '$_POST[second_name]',
		               '$_POST[last_name]',
		               '$dob',
		               '$gender',
		               '$_POST[address]',
		               '$_POST[postal]',
		               '$_POST[city]',
		               '$_POST[province]',
		               '$_POST[country]',
		               '$_POST[phone]',
		               $status,
		               '$_config[pref_defaults]',
		               '$now',
		               '$_SESSION[lang]',
		               $_config[pref_inbox_notify],
		               $_POST[private_email],
		               '0000-00-00 00:00:00')";


        $result = queryDB($sql, array(TABLE_PREFIX)) or die(at_db_error());
        $m_id	= at_insert_id($db);

        if (!$result)
        {
            $msg->addError('DB_NOT_UPDATED');
            $msg->printAll();
            exit;
        }
        else
        {
            //process any extra fields
            $sql = "SELECT * FROM %sforms";
            $result = queryDB($sql, array(TABLE_PREFIX));

            $column= array();
            $values= array();
            $column[]="`login`";
            $values[]="'$_POST[login]'";
            foreach($result as $row)
            {
                $column[]="`".$row['name']."`";
                $values[]="'".$addslashes($_POST[$row['name']])."'";
            }

            $column= implode(',', $column);
            $values=implode(',', $values);
            $sqlExtra="INSERT INTO %smembers_extra (".$column.") VALUES (".$values.")";
            $result2 = queryDB($sqlExtra, array(TABLE_PREFIX)) or die(at_db_error());

            if (!$result2)
            {
                $msg->addError('DB_NOT_UPDATED');
                $msg->printAll();
                exit;
            }

        }


        if (defined('AT_MASTER_LIST') && AT_MASTER_LIST) {
            $student_id  = $addslashes($_POST['student_id']);
            $student_pin = md5($addslashes($_POST['student_pin']));
            if ($student_id) {
                $sql = "UPDATE ".TABLE_PREFIX."master_list SET member_id=$m_id WHERE public_field='$student_id'";
                mysql_query($sql, $db);
                if (mysql_affected_rows($db) == 0) {
                    $sql = "REPLACE INTO ".TABLE_PREFIX."master_list VALUES ('$student_id', '$student_pin', $m_id)";
                    mysql_query($sql, $db);
                }
            }
        }

        if ($_POST['pref'] == 'access') {
            $_SESSION['member_id'] = $m_id;
            save_prefs();
            unset($_SESSION['member_id']);
        }






        require(AT_INCLUDE_PATH . 'classes/phpmailer/atutormailer.class.php');
        $mail = new ATutorMailer();
        $mail->AddAddress($_POST['email']);
        $mail->From    = $_config['contact_email'];

        if (defined('AT_EMAIL_CONFIRMATION') && AT_EMAIL_CONFIRMATION && ($_POST['status'] == AT_STATUS_UNCONFIRMED)) {
            $code = substr(md5($_POST['email'] . $now . $m_id), 0, 10);
            $confirmation_link = AT_BASE_HREF . 'confirm.php?id='.$m_id.SEP.'m='.$code;

            /* send the email confirmation message: */
            $mail->Subject = $_config['site_name'] . ': ' . _AT('email_confirmation_subject');
            $body .= _AT('admin_new_account_confirm', $_config['site_name'], $confirmation_link)."\n\n";

        } else {
            $mail->Subject = $_config['site_name'].": "._AT('account_information');
            $body .= _AT('admin_new_account', $_config['site_name'])."\n\n";
        }
        $body .= _AT('web_site') .' : '.AT_BASE_HREF."\n";
        $body .= _AT('login_name') .' : '.$_POST['login'] . "\n";
//		$body .= _AT('password') .' : '.$_POST['password'] . "\n";
        $mail->Body    = $body;
        $mail->Send();
        $msg->addFeedback('PROFILE_CREATED_ADMIN');
        header('Location: '.AT_BASE_HREF.'mods/hello_world/create_user.php');
        exit;
    }
}

if (!isset($_POST['status'])) {
    if (defined('AT_EMAIL_CONFIRMATION') && AT_EMAIL_CONFIRMATION) {
        $_POST['status'] = AT_STATUS_UNCONFIRMED;
    } else {
        $_POST['status'] = AT_STATUS_STUDENT;
    }
}




?>

<?php


include("PFBC/Form.php");
$ml='';
$login='';
$form = new Form("form");
$form->configure(array("action"=>$_base_path .'mods/hello_world/signup.php',"view" => new View\SideBySide(array("classLabels" => array("formAction"=>"row buttons","controlGroup"=>"row","fieldsetLabel"=>"group_form")))));

if(isset($_POST['ml']))
{$ml=$_POST['ml'];}

$form->addElement(new Element\Hidden("ml",$ml,array("id"=>"ml")));
$form->addElement(new Element\Hidden("password_error", "",array("id"=>"password_error")));
$form->addElement(new Element\Hidden("form_password_hidden", "",array("id"=>"form_password_hidden")));
$form->addElement(new Element\Hidden("registration_token",sha1($_SESSION['token'])));

$form->addElement(new Element\HTML('<legend class="group_form">'._AT('required_information').'</legend>'));

if (!isset($_POST['member_id']) && defined('AT_MASTER_LIST') && AT_MASTER_LIST && !admin_authenticate(AT_ADMIN_PRIV_USERS, TRUE))
{
    $form->addElement(new Element\HTML('<h3>'._AT('account_authorization').'</h3>'));
    $form->addElement(new Element\Textbox(_AT('student_id'),"student_id",array("value"=>stripslashes(htmlspecialchars($_POST['student_id'])),"id"=>"student_id","size"=>"15","maxlength"=>"15","required"=>1)));
    $form->addElement(new Element\Password(_AT('student_pin'),"student_pin",array("value"=>stripslashes(htmlspecialchars($_POST['student_pin'])),"id"=>"student_id","size"=>"15","maxlength"=>"15","required"=>1)));
}

$table_title="<div class=\"row\"> <h3>" . _AT('course_to_auto_enroll'). "</h3><small>&middot; " ._AT('auto_enroll_msg')."</small></div>";
require(AT_INCLUDE_PATH.'html/auto_enroll_list_courses.inc.php');


if (isset($_POST['member_id']))
{
    $form->addElement(new Element\Hidden("member_id",intval($_POST['member_id'])));
    $form->addElement(new Element\Hidden("login",(isset($_POST['login']) ? stripslashes(htmlspecialchars($_POST['login'])):"")));
}
else{

    $form->addElement(new Element\Textbox(_AT('login_name'),"login",array("value"=>(isset($_POST['login']) ? stripslashes(htmlspecialchars($_POST['login'])):""),"id"=>"login","size"=>"30","maxlength"=>"20","title"=> _AT('login_name').':'._AT('contain_only'),"required"=>1,"longDesc" =>'<br><small>&middot'._AT('contain_only').'<br/>&middot'. _AT('20_max_chars').'</small>')));

}

if (!admin_authenticate(AT_ADMIN_PRIV_USERS, TRUE) || !$_POST['member_id'])
{
    $form->addElement(new Element\Password(_AT('password'),"form_password1",array("id"=>"form_password1","size"=>"15","maxlength"=>"15","title"=> _AT('password').':'._AT('combination'),"required"=>1,"longDesc" =>'<br><small>&middot'._AT('combination').'<br />&middot'. _AT('15_max_chars').'</small>')));
    $form->addElement(new Element\Password(_AT('password_again'),"form_password2",array("id"=>"form_password2","size"=>"15","maxlength"=>"15","required"=>1)));
}


$form->addElement(new Element\Email(_AT('email_address'),"email",array("value"=>(isset($_POST['email']) ? stripslashes(htmlspecialchars($_POST['email'])):""),"id"=>"email","size"=>"30","maxlength"=>"50","required"=>1)));
$form->addElement(new Element\Checkbox("", "private_email", array(_AT('keep_email_private')),array("value"=>(isset($_POST['private_email']) ?$_POST['private_email']:""),"id"=>"priv")));
$form->addElement(new Element\Email(_AT('email_again'),"email2",array("value"=>(isset($_POST['email2']) ? stripslashes(htmlspecialchars($_POST['email2'])):""),"id"=>"email2","size"=>"30","maxlength"=>"50","required"=>1)));
$form->addElement(new Element\Textbox(_AT('first_name'),"first_name",array("value"=>(isset($_POST['first_name']) ? stripslashes(htmlspecialchars($_POST['first_name'])):""),"id"=>"first_name","required"=>1)));
$form->addElement(new Element\Textbox(_AT('second_name'),"second_name" ,array("value"=>(isset($_POST['second_name']) ? stripslashes(htmlspecialchars($_POST['second_name'])):""),"id"=>"second_name" )));
$form->addElement(new Element\Textbox(_AT('last_name'),"last_name" ,array("value"=>(isset($_POST['last_name']) ? stripslashes(htmlspecialchars($_POST['last_name'])):""),"id"=>"last_name","required"=>1)));

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

//optional fields
//add new fieldset for optional inputs
$form->addElement(new Element\HTML('</fieldset><fieldset class="group_form">'));
$form->addElement(new Element\HTML('<legend class="group_form">'._AT('personal_information').' ('._AT('optional').')'.'</legend>'));

//following code relate to another module and needs refactor
$mod = $moduleFactory->getModule('_standard/profile_pictures');
if (admin_authenticate(AT_ADMIN_PRIV_USERS, TRUE) && $_POST['member_id'] && $mod->isEnabled() === TRUE): ?>
<div class="row">
    <?php echo _AT('picture'); ?><br/>
    <?php if (profile_image_exists($_POST['member_id'])): ?>
    <a href="get_profile_img.php?id=<?php echo $_POST['member_id'].SEP.'size=o'; ?>"><?php print_profile_img($_POST['member_id']); ?></a>
    <input type="checkbox" name="profile_pic_delete" value="1" id="profile_pic_delete" />
    <label for="profile_pic_delete"><?php echo _AT('delete'); ?></label>
    <?php else: ?>
    <?php echo _AT('none'); ?> <a href="mods/_standard/profile_pictures/admin/profile_picture.php?member_id=<?php echo $_POST['member_id']; ?>"><?php echo _AT('add'); ?></a>
    <?php endif; ?>
</div>
<?php endif; ?>
<?php if (admin_authenticate(AT_ADMIN_PRIV_USERS, TRUE) && defined('AT_MASTER_LIST') && AT_MASTER_LIST): ?>
<input type="hidden" name="old_student_id" value="<?php echo $_POST['old_student_id']; ?>" />
<div class="row">
    <label for="student_id"><?php echo _AT('student_id'); ?></label><br />
    <input type="text" id="student_id" name="student_id" value="<?php echo $_POST['student_id']; ?>" size="20" /><br />
</div>
<div class="row">
    <input type="checkbox" id="overwrite" name="overwrite" value="1" <?php if ($_POST['overwrite']) { echo 'checked="checked"'; } ?> /><label for="overwrite"><?php echo _AT('overwrite_master');?></label>
</div>
<?php endif;

$form->addElement(new Element\Date(_AT('date_of_birth'), "date",array("value"=>(isset($_POST['date']) ? $_POST['date']:""))));
$form->addElement(new Element\Radio(_AT('gender'), "gender", array( _AT('male'), _AT('female'),_AT('not_specified')), array("value"=>(isset($_POST['gender']) ? $_POST['gender']:""),"id"=>'sex')));
$form->addElement(new Element\Textbox(_AT('street_address'),"address",array("value"=>(isset($_POST['address']) ? stripslashes(htmlspecialchars($_POST['address'])):""),"id"=>"address","size"=>"40")));
$form->addElement(new Element\Textbox(_AT('postal_code'),"postal",array("value"=>(isset($_POST['postal']) ? stripslashes(htmlspecialchars($_POST['postal'])):""),"id"=>"postal","size"=>"7")));
$form->addElement(new Element\Textbox(_AT('city'),"city",array("value"=>(isset($_POST['city']) ? stripslashes(htmlspecialchars($_POST['city'])):""),"id"=>"city")));
$form->addElement(new Element\Textbox(_AT('province'),"province",array("value"=>(isset($_POST['province']) ? stripslashes(htmlspecialchars($_POST['province'])):""),"id"=>"province",)));
$form->addElement(new Element\Textbox(_AT('country'),"country",array("value"=>(isset($_POST['country']) ? stripslashes(htmlspecialchars($_POST['country'])):""),"id"=>"country")));
$form->addElement(new Element\Textbox(_AT('phone'),"phone",array("value"=>(isset($_POST['phone']) ? stripslashes(htmlspecialchars($_POST['phone'])):""),"id"=>"phone","size"=>"11")));
$form->addElement(new Element\Textbox(_AT('web_site'),"website",array("value"=>(isset($_POST['website']) ? stripslashes(htmlspecialchars($_POST['website'])):""),"id"=>"website","size"=>"40" )));

//*****************show element using db**************

$sql = "SELECT * FROM %sforms ORDER BY rank";
$result = queryDB($sql, array(TABLE_PREFIX)) or die(at_db_error());

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

$form->addElement(new Element\Hidden("submit_form", "save"));
$form->addElement(new Element\HTML('</fieldset>'));
$form->addElement(new Element\Button(_AT('save'),"submit",array("name"=>"submit","accesskey"=>"s","onclick"=>"encrypt_password()","class"=>"button")));
$form->addElement(new Element\Button(_AT('cancel'),"submit",array("name"=>"cancel","class"=>"button","onclick"=>"history.go(-1);")));



$form->addElement(new Element\HTML('</fieldset>'));

$form->render();
var_dump($_POST);
//var_dump($result) ;
//var_dump($choice);

?>

<?php require (AT_INCLUDE_PATH.'footer.inc.php'); ?>