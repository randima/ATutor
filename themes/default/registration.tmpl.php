<?php 
require(AT_INCLUDE_PATH.'header.inc.php');
use PFBC\Form;
use PFBC\Element;
use PFBC\View;
include( AT_INCLUDE_PATH.'PFBC/Form.php');
//var_dump($_POST);
//$form->configure(array( "prevent" => array("bootstrap", "jQuery")));
?>

<script language="JavaScript" src="sha-1factory.js" type="text/javascript"></script>

<script type="text/javascript">
function encrypt_password()
{
	document.form.password_error.value = "";

	err = verify_password(document.form.form_password1.value, document.form.form_password2.value);
	
	if (err.length > 0)
	{
		document.form.password_error.value = err;
	}
	else
	{
		document.form.form_password_hidden.value = hex_sha1(document.form.form_password1.value);
		document.form.form_password1.value = "";
		document.form.form_password2.value = "";
	}
}
</script>

<form method="post" action="<?php $getvars = ''; if (isset($_REQUEST["en_id"]) && $_REQUEST["en_id"] <> "") $getvars = '?en_id='. $_REQUEST["en_id"]; echo $_SERVER['PHP_SELF'] . $getvars; ?>" name="form">
<?php global $languageManager, $_config, $moduleFactory; ?>
<input name="ml" type="hidden" value="<?php if(isset($this->ml)){ echo $this->ml; } ?>" />
<input name="password_error" type="hidden" />
<input type="hidden" name="form_password_hidden" value="" />
<input type="hidden" name="registration_token" value="<?php echo sha1($_SESSION['token']); ?>" />

<div class="input-form">
<fieldset class="group_form"><legend class="group_form"><?php echo _AT('required_information'); ?></legend>

<p><span class="required">*</span><?php echo _AT('required_field'); ?></p>
	<?php if (!isset($_POST['member_id']) && defined('AT_MASTER_LIST') && AT_MASTER_LIST && !admin_authenticate(AT_ADMIN_PRIV_USERS, TRUE)): ?>
		<div class="row">
			<h3><?php echo _AT('account_authorization'); ?></h3>
		</div>

		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="student_id"><?php echo _AT('student_id'); ?></label><br />
			<input id="student_id" name="student_id" type="text" size="15" maxlength="15" value="<?php echo stripslashes(htmlspecialchars($_POST['student_id'])); ?>" /><br />
		</div>

		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="student_pin"><?php echo _AT('student_pin'); ?></label><br />
			<input id="student_pin" name="student_pin" type="password" size="15" maxlength="15" value="<?php echo stripslashes(htmlspecialchars($_POST['student_pin'])); ?>" /><br />
		</div>
	<?php endif; ?>

	<?php 
		$table_title="
		<div class=\"row\">
			<h3>" . _AT('course_to_auto_enroll'). "</h3>
			<small>&middot; " ._AT('auto_enroll_msg')."</small>
		</div>";
		
		require(AT_INCLUDE_PATH.'html/auto_enroll_list_courses.inc.php'); 
	?>
	

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="login"><?php echo _AT('login_name'); ?></label><br />
		<?php if (isset($_POST['member_id'])) : ?>
				<span id="login"><?php echo stripslashes(htmlspecialchars($_POST['login'])); ?></span>
				<input name="member_id" type="hidden" value="<?php echo intval($_POST['member_id']); ?>" />
				<input name="login" type="hidden" value="<?php if(isset($_POST['login'])){ echo stripslashes(htmlspecialchars($_POST['login'])); } ?>" />
		<?php else: ?>
			<input id="login" name="login" type="text" maxlength="20" size="30" value="<?php if(isset($_POST['login'])){echo stripslashes(htmlspecialchars($_POST['login']));} ?>" title="<?php echo _AT('login_name').':'._AT('contain_only'); ?>"/><br />
			<small>&middot; <?php echo _AT('contain_only'); ?><br />
				   &middot; <?php echo _AT('20_max_chars'); ?></small>
		<?php endif; ?>
	</div>

	<?php if (!admin_authenticate(AT_ADMIN_PRIV_USERS, TRUE) || !$_POST['member_id']): ?>
		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="form_password1"><?php echo _AT('password'); ?></label><br />
			<input id="form_password1" name="form_password1" type="password" size="15" maxlength="15" title="<?php echo _AT('password').':'._AT('combination'); ?>"/><br />
			<small>&middot; <?php echo _AT('combination'); ?><br />
				   &middot; <?php echo _AT('15_max_chars'); ?></small>
		</div>

		<div class="row">
			<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="form_password2"><?php echo _AT('password_again'); ?></label><br />
			<input id="form_password2" name="form_password2" type="password" size="15" maxlength="15" />
		</div>
	<?php endif; ?>

	<?php if (isset($_config['use_captcha']) && $_config['use_captcha']==1 && !$this->no_captcha): ?>
	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span>
		<label for="secret"><img src="<?php echo AT_INCLUDE_PATH; ?>securimage/securimage_show.php?sid=<?php echo md5(uniqid(time())); ?>" id="simage" align="left" /></label>
		<a href="<?php echo AT_INCLUDE_PATH; ?>securimage/securimage_play.php" title="<?php echo _AT('audible_captcha'); ?>"><img src="<?php echo AT_INCLUDE_PATH; ?>securimage/images/audio_icon.gif" alt="<?php echo _AT('audible_captcha'); ?>" onclick="this.blur()" align="top" border="0"></a><br>
		<a href="#" title="<?php echo _AT('refresh_image'); ?>" onclick="document.getElementById('simage').src = '<?php echo AT_INCLUDE_PATH; ?>securimage/securimage_show.php?sid=' + Math.random(); return false"><img src="<?php echo AT_INCLUDE_PATH; ?>securimage/images/refresh.gif" alt="<?php echo _AT('refresh_image'); ?>" onclick="this.blur()" align="bottom" border="0"></a>

		<br />
		<p><?php echo _AT('image_validation_text'); ?><br />
		<input id="secret" name="secret" type="text" size="6" maxlength="6" value="" />
		<br />
		<small><?php echo _AT('image_validation_text2'); ?><br /></small>
	</div>
	<?php endif; ?>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="email"><?php echo _AT('email_address'); ?></label><br />
		<input id="email" name="email" type="text" size="30" maxlength="50" value="<?php if(isset($_POST['email'])){ echo stripslashes(htmlspecialchars($_POST['email']));} ?>" /><br />
		<input type="checkbox" id="priv" name="private_email" value="1" <?php if (isset($_POST['private_email']) || !isset($_POST['submit'])) { echo 'checked="checked"'; } ?> /><label for="priv"><?php echo _AT('keep_email_private');?></label>
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="email2"><?php echo _AT('email_again'); ?></label><br />
		<input id="email2" name="email2" type="text" size="30" maxlength="60" value="<?php if(isset($_POST['email2'])){  echo stripslashes(htmlspecialchars($_POST['email2']));} ?>" />
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="first_name"><?php echo _AT('first_name'); ?></label><br />
		<input id="first_name" name="first_name" type="text" value="<?php if(isset($_POST['first_name'])){ echo stripslashes(htmlspecialchars($_POST['first_name']));} ?>" />
	</div>

	<div class="row">
		<label for="second_name"><?php echo _AT('second_name'); ?></label><br />
		<input id="second_name" name="second_name" type="text" value="<?php if(isset($_POST['second_name'])){ echo stripslashes(htmlspecialchars($_POST['second_name'])); } ?>" />
	</div>

	<div class="row">
		<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><label for="last_name"><?php echo _AT('last_name'); ?></label><br />
		<input id="last_name" name="last_name" type="text" value="<?php if(isset($_POST['last_name'])){ echo stripslashes(htmlspecialchars($_POST['last_name']));} ?>" />
	</div>
	
	<?php if (admin_authenticate(AT_ADMIN_PRIV_USERS, TRUE)): 
			if ($_POST['status'] == AT_STATUS_INSTRUCTOR) {
				$inst = ' checked="checked"';
			} else if ($_POST['status'] == AT_STATUS_STUDENT) {
				$stud = ' checked="checked"';
			}  else if ($_POST['status'] == AT_STATUS_DISABLED) {
				$disa = ' checked="checked"';
			} else {
				$uncon = ' checked="checked"';
			}?>
			<input type="hidden" name="id" value="<?php echo $_POST['member_id']; ?>" />
			<div class="row">
				<span class="required" title="<?php echo _AT('required_field'); ?>">*</span><?php echo _AT('account_status'); ?><br />

				<input type="radio" name="status" value="0" id="disa" <?php echo $disa; ?> /><label for="disa"><?php echo _AT('disabled'); ?></label>
				<?php if (defined('AT_EMAIL_CONFIRMATION') && AT_EMAIL_CONFIRMATION): ?>
					<input type="radio" name="status" value="1" id="uncon" <?php echo $uncon; ?> /><label for="uncon"><?php echo _AT('unconfirmed'); ?></label>
				<?php endif; ?>

				<input type="radio" name="status" value="2" id="stud" <?php echo $stud; ?> /><label for="stud"><?php echo _AT('student'); ?></label>

				<input type="radio" name="status" value="3" id="inst" <?php echo $inst; ?> /><label for="inst"><?php echo _AT('instructor'); ?></label>

				<input type="hidden" name="old_status" value="<?php echo $_POST['old_status']; ?>" />
			</div>
	<?php endif; ?>

    <?php


//@Randima's work for option fields (needs to adapt generator)
    echo '</fieldset>';

    $form = new Form("form-create");
    //$form->addElement(new Element\HTML('</form></fieldset>'));
    $form->configure(array("view" => new View\SideBySide(array("classLabels" => array("formAction"=>"row buttons","controlGroup"=>"row","fieldsetLabel"=>"group_form")))));
    $form->addElement(new Element\HTML('<legend class="group_form">'._AT('personal_information').' ('._AT('optional').')'.'</legend>'));


    // following code needs refactoring
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


    $form->addElement(new Element\Date( _AT('date_of_birth'), "date"));
    $form->addElement(new Element\Radio(_AT('gender'), "gender", array( _AT('male'), _AT('female'),_AT('not_specified')), array("id"=>'sex')));
    $form->addElement(new Element\Textbox(_AT('street_address'),"address",array("id"=>"address","size"=>"40")));
    $form->addElement(new Element\Textbox(_AT('postal_code'),"postal",array("id"=>"postal","size"=>"7")));
    $form->addElement(new Element\Textbox(_AT('city'),"city",array("id"=>"city")));
    $form->addElement(new Element\Textbox(_AT('province'),"province",array("id"=>"province",)));
    $form->addElement(new Element\Textbox(_AT('country'),"country",array("id"=>"country")));
    $form->addElement(new Element\Textbox(_AT('phone'),"phone",array("id"=>"phone","size"=>"11")));
    $form->addElement(new Element\Textbox(_AT('web_site'),"website",array("id"=>"website","size"=>"40" )));
    $form->addElement(new Element\HTML('</fieldset>'));
    $form->addElement(new Element\Button(_AT('save'),"submit",array("name"=>"submit","accesskey"=>"s","onclick"=>"encrypt_password()","class"=>"button")));
    $form->addElement(new Element\Button(_AT('cancel'),"submit",array("name"=>"cancel","class"=>"button")));
    $form->render();
    ?>

<!--</div>-->
</form>


<?php require(AT_INCLUDE_PATH.'footer.inc.php'); ?>