<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/

	$page = 'preferences';
	$_user_location	= 'users';
	define('AT_INCLUDE_PATH', '../include/');

	require(AT_INCLUDE_PATH.'vitals.inc.php');
	$_section[0][0] = _AT('preferences');

	/* whether or not, any settings are being changed when this page loads. */
	/* ie. is ANY action being performed right now?							*/
	$action = false;

	if ($_GET['pref_id'] != '') {
		if ($_GET['pref_id'] > 0) {
			/* load a preset set of preferences */
			$my_prefs = get_prefs(intval($_GET['pref_id']));

			if ($my_prefs) {
				assign_session_prefs($my_prefs);
				$feedback[] = AT_FEEDBACK_PREFS_CHANGED;
				if ($_SESSION['valid_user']) {
					$feedback[] = array(AT_FEEDBACK_APPLY_PREFS, $_SERVER['PHP_SELF']);
				} else {
					/* we're not logged in */
					$feedback[] = AT_FEEDBACK_PREFS_LOGIN;
				}

				/* these prefs have not yet been saved */
				$_SESSION['prefs_saved'] = false;
			} else {
				$errors[] = AT_ERROR_THEME_NOT_FOUND;
			}

		} else {
			/* use this course's prefs */
			$sql	= "SELECT preferences FROM ".TABLE_PREFIX."courses WHERE course_id=$_SESSION[course_id]";
			$result	= mysql_query($sql,$db);
			$row	= mysql_fetch_array($result);

			if ($row['preferences']) {
				assign_session_prefs(unserialize(stripslashes($row['preferences'])));
				$feedback[] = AT_FEEDBACK_PREFS_CHANGED;
				if ($_SESSION['valid_user']) {
					$feedback[] = array(AT_FEEDBACK_APPLY_PREFS, $_SERVER['PHP_SELF']);
				} else {
					/* we're not logged in */
					$feedback[] = AT_FEEDBACK_PREFS_LOGIN;
				}

				/* these prefs have not yet been saved */
				$_SESSION['prefs_saved'] = false;

			} else {
				$errors[] = AT_ERROR_CPREFS_NOT_FOUND;
			}
		}
		$action = true;
	} else if ($_GET['submit']) {
		/* custom prefs */

		$temp_prefs[PREF_MAIN_MENU_SIDE]= intval($_GET['pos']);
		$temp_prefs[PREF_SEQ]		    = intval($_GET['seq']);
		$temp_prefs[PREF_TOC]		    = intval($_GET['toc']);
		$temp_prefs[PREF_NUMBERING]	    = intval($_GET['numering']);
		$temp_prefs[PREF_SEQ_ICONS]	    = intval($_GET['seq_icons']);
		$temp_prefs[PREF_NAV_ICONS]	    = intval($_GET['nav_icons']);
		$temp_prefs[PREF_LOGIN_ICONS]	= intval($_GET['login_icons']);
		$temp_prefs[PREF_CONTENT_ICONS]	= intval($_GET['content_icons']);
		$temp_prefs[PREF_HEADINGS]	    = intval($_GET['headings']);
		$temp_prefs[PREF_BREADCRUMBS]	= intval($_GET['breadcrumbs']);
		$temp_prefs[PREF_HELP]	        = intval($_GET['use_help']);
		$temp_prefs[PREF_MINI_HELP]	    = intval($_GET['use_mini_help']);
		$temp_prefs[PREF_THEME]	        = $_GET['theme'];

		for ($i = 0; $i< 6; $i++) {
			if ($_GET['stack'.$i] != '') {
				$stack_array[] = $_GET['stack'.$i];
			}
		}
		$temp_prefs[PREF_STACK]	= $stack_array;

		/* we do this instead of assigning to the $_SESSION directly, b/c	*/
		/* assign_session_prefs functionality might change slightly.		*/
		assign_session_prefs($temp_prefs);

		$feedback[] = AT_FEEDBACK_PREFS_CHANGED;
		if ($_SESSION['valid_user']) {
			/* we're logged in, and enrolled */
			$feedback[] = array(AT_FEEDBACK_APPLY_PREFS, $_SERVER['PHP_SELF']);
		} else {
			/* we're not logged in */
			$feedback[] = AT_FEEDBACK_PREFS_LOGIN;
		}

		/* these prefs have not yet been saved */
		$_SESSION['prefs_saved'] = false;
		$action = true;
	} else if ($_GET['save'] == 2) {
		/* save as pref for ALL courses */
		save_prefs( );
		$feedback[] = AT_FEEDBACK_PREFS_SAVED2;
		$_SESSION['prefs_saved'] = true;
		$action = true;

	} else if ($_GET['save'] == 3) {
		/* get prefs: */
		$sql	= "SELECT preferences FROM ".TABLE_PREFIX."members WHERE member_id=$_SESSION[member_id]";
		$result = mysql_query($sql, $db);
		if ($row2 = mysql_fetch_array($result)) {
			assign_session_prefs(unserialize(stripslashes($row2['preferences'])));
		}
		$feedback[] = AT_FEEDBACK_PREFS_RESTORED;
		$_SESSION['prefs_saved'] = true;
		$action = true;

	} else if (($_GET['save'] == 4) && authenticate(AT_PRIV_STYLES, AT_PRIV_RETURN)) {
		/* save prefs as this course's default, as an admin only. */

		$data	= addslashes(serialize($_SESSION['prefs']));
		$sql	= "UPDATE ".TABLE_PREFIX."courses SET preferences='$data' WHERE course_id=$_SESSION[course_id]";
		$result = mysql_query($sql, $db);

		header('Location: preferences.php?f='.urlencode_feedback(AT_FEEDBACK_COURSE_PREFS_SAVED));
		exit;
	}

	/* page contents starts here */
	require(AT_INCLUDE_PATH.'header.inc.php');

	echo '<h3>'._AT('preferences').'</h3>';

	if (($_SESSION['prefs_saved'] === false) && !$action && $_SESSION['valid_user']) {
		$feedback[] = array(AT_FEEDBACK_APPLY_PREFS, $_SERVER['PHP_SELF']);
	}

	print_errors($errors);

	/* this is where we want the feedback to appear */
	print_feedback($feedback);

	$help[] = AT_HELP_PREFERENCES;
	$help[] = AT_HELP_PREFERENCES1;
	$help[] = AT_HELP_PREFERENCES2;

	print_help($help);
	
		/************************************/
		/* presets							*/
		echo '<h3>'._AT('preset_preferences').'</h3>';
		$sql	= 'SELECT * FROM '.TABLE_PREFIX.'theme_settings ORDER BY name';
		$result	= mysql_query($sql, $db);

		?>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
		<table border="0" class="bodyline" cellspacing="1" cellpadding="0" align="center">
		<tr>
			<th colspan="2" class="cat"><?php print_popup_help(AT_HELP_PRESET); echo _AT('preset_preferences')?></th>
		</tr>
		<tr>
			<td class="row1"><label for="preset"><?php echo _AT('select_preset');  ?>:</label></td>
			<td class="row1">
				<select name="pref_id" id="preset">
					<?php
						if ($row = mysql_fetch_array($result)) {
							do {
								echo '<option  value="'.$row['theme_id'].'">'._AT($row['name']).'</option>';
							} while ($row = mysql_fetch_array($result));
						}								
					?>
				</select>&nbsp;<input type="submit" name="submit" value="<?php echo _AT('set_preset'); ?>" class="button" /></td>
		</tr>
		</table>
		</form>

	<br />
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get" name="prefs">
	<?php echo '<h3>'._AT('personal_preferences').'</h3>'; ?>
	<table cellspacing="5" width="100%" cellpadding="0" summary="" border="0">
	<tr>
		<td valign="top"><table border="0" width="100%" class="bodyline" cellspacing="1" cellpadding="0">
		<tr>
			<th colspan="2" class="cat"><?php print_popup_help(AT_HELP_POSITION_OPTIONS); echo _AT('pos_options')?></th>
		</tr>
		<tr>
			<td class="row1"><label for="pos"><?php echo _AT('menu');  ?>:</label></td>
			<td class="row1"><?php
								if ($_SESSION['prefs'][PREF_MAIN_MENU_SIDE] == MENU_LEFT) {
									$left = ' selected="selected"';
								} else {
									$right = ' selected="selected"';
								}
			?><select name="pos" id="pos">
				<option value="1" <?php echo $left;?>><?php echo _AT('left'); ?></option>
				<option value="2" <?php echo $right;?>><?php echo _AT('right'); ?></option>
			  </select><br /></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td class="row1"><label for="seq"><?php echo _AT('seq_links');  ?>:</label></td>
			<td class="row1"><?php
			/* sequence links preference */
			if ($_SESSION['prefs'][PREF_SEQ] == TOP) {
				$top = ' selected="selected"';
			} else if ($_SESSION['prefs'][PREF_SEQ] == BOTTOM) {
				$bottom = ' selected="selected"';
			} else {
				$both = ' selected="selected"';
			}
			?><select name="seq" id="seq">
				<option value="<?php echo TOP; ?>"<?php echo $top; ?>><?php echo _AT('top');  ?></option>
				<option value="<?php echo BOTTOM; ?>"<?php echo $bottom; ?> ><?php echo _AT('bottom');  ?></option>
				<option value="<?php echo BOTH; ?>"<?php echo $both; ?>><?php echo _AT('top_bottom');  ?></option>
			  </select><br /></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td class="row1"><label for="toc"><?php echo _AT('table_of_contents');  ?>:</label></td>
			<td class="row1"><?php
			// table of contents preference
			$top = $bottom = '';
			if ($_SESSION['prefs'][PREF_TOC] == TOP) {
				$top	= ' selected="selected"';
			} else if ($_SESSION['prefs'][PREF_TOC] == BOTTOM) {
				$bottom = ' selected="selected"';
			} else {
				$neither = ' selected="selected"';
			}
			?><select name="toc" id="toc">
				<option value="<?php echo TOP; ?>"<?php echo $top; ?>><?php echo _AT('top');  ?></option>
				<option value="<?php echo BOTTOM; ?>"<?php echo $bottom; ?>><?php echo _AT('bottom');  ?></option>
				<option value="<?php echo NEITHER; ?>"<?php echo $neither; ?>><?php echo _AT('neither');  ?></option>
			  </select></td>
		</tr>
		</table></td>

		<td valign="top" align="left"><table border="0" width="100%"  class="bodyline" cellspacing="1" cellpadding="0">
		<tr>
			<th colspan="2" class="cat"><?php print_popup_help(AT_HELP_DISPLAY_OPTIONS); ?><?php echo _AT('disp_options');  ?></th>
		</tr>
		<tr>
			<td class="row1"><?php
			/* Show Topic Numbering Preference */
			if ($_SESSION['prefs'][PREF_NUMBERING] == 1) {
				$num = ' checked="checked"';
			}
			?> <input type="checkbox" name="numering" value="1" <?php echo $num;?> id="numbering" /></td>
			<td class="row1"><label for="numbering"><?php echo _AT('show_numbers');  ?></label></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td class="row1"><?php
				/* Show Breadcrumbs Preference */
				$num = '';
				if ($_SESSION['prefs'][PREF_BREADCRUMBS] == 1) {
					$num = ' checked="checked"';
				}
				?><input type="checkbox" name="breadcrumbs" value="1" <?php echo $num;?> id="breadcrumbs" /></td>
			<td class="row1"><label for="breadcrumbs"><?php echo _AT('show_breadcrumbs');  ?></label></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td class="row1"><?php
				$num = '';
				if ($_SESSION['prefs'][PREF_HEADINGS] == 1) {
					$num = ' checked="checked"';
				}
				?> <input type="checkbox" name="headings" value="1" <?php echo $num;?> id="heading" /></td>
			<td class="row1"><label for="heading"><?php echo _AT('show_headings');  ?></label></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td class="row1"><?php
				$num = '';
				if ($_SESSION['prefs'][PREF_HELP] == 1) {
					$num = ' checked="checked"';
				}
				?><input type="checkbox" name ="use_help" id="use_help" value="1" <?php echo $num; ?> /></td>
			<td class="row1"><label for="use_help"><?php echo _AT('show_help');  ?></label><br /></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td class="row1"><?php
				$num = '';
				if ($_SESSION['prefs'][PREF_MINI_HELP] == 1) {
					$num = ' checked="checked"';
				}
				?><input type="checkbox" name ="use_mini_help" id="use_mini_help" value="1" <?php echo $num; ?> /></td>
			<td class="row1"><label for="use_mini_help"><?php echo _AT('show_mini_help');  ?></label><br /></td>
		</tr>
		</table></td>
	</tr>
	<tr>
		<td valign="top"><table border="0" width="100%" class="bodyline" cellspacing="1" cellpadding="0">
		<tr>
			<th colspan="2" class="cat"><?php print_popup_help(AT_HELP_TEXTICON_OPTIONS); ?><?php echo _AT('text_and_icons');  ?></th>
		</tr>
		<tr>
			<td class="row1"><label for="login_icons"><?php echo _AT('personal_bar');  ?>:</label></td>
			<td class="row1"><?php

					$both = '';
					$text = '';
					$icons = '';

					if ($_SESSION['prefs'][PREF_LOGIN_ICONS] == 1) {
						$icons = ' selected="selected"';
					} else if ($_SESSION['prefs'][PREF_LOGIN_ICONS] == 2) {
						$text = ' selected="selected"';
					} else {
						$both = ' selected="selected"';
					}
				?><select name="login_icons" id="login_icons">
					<option value="1" <?php echo $icons; ?>><?php echo _AT('icons_only');  ?></option>
					<option value="2" <?php echo $text; ?>><?php echo _AT('text_only');  ?></option>
					<option value="0" <?php echo $both; ?>><?php echo _AT('icons_and_text');  ?></option>
				</select></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td class="row1"><label for="nav_icons"><?php echo _AT('course_nav');  ?>:</label></td>
			<td class="row1"><?php

						$both	= '';
						$text	= '';
						$icons	= '';

						if ($_SESSION['prefs'][PREF_NAV_ICONS] == 1) {
							$icons = ' checked="checked"';
						} else if ($_SESSION['prefs'][PREF_NAV_ICONS] == 2) {
							$text = ' selected="selected"';
						} else {
							$both = ' selected="selected"';
						}
				?><select name="nav_icons" id="nav_icons">
					<option value="1" <?php echo $icons; ?>><?php echo _AT('icons_only');  ?></option>
					<option value="2" <?php echo $text; ?>><?php echo _AT('text_only');  ?></option>
					<option value="0" <?php echo $both; ?>><?php echo _AT('icons_and_text');  ?></option>
				</select></td>
		</tr>
		<tr><td height="1" class="row2" colspan="2"></td></tr>		
		<tr>
			<td class="row1"><label for="seq_icons"><?php echo _AT('seq_nav');  ?>:</label></td>
			<td class="row1"><?php

						$both = '';
						$text = '';
						$icons = '';

						if ($_SESSION['prefs'][PREF_SEQ_ICONS] == 1) {
							$icons = ' selected="selected"';
						} else if ($_SESSION['prefs'][PREF_SEQ_ICONS] == 2) {
							$text = ' selected="selected"';
						} else {
							$both = ' selected="selected"';
						}
					?><select name="seq_icons" id="seq_icons">
						<option value="1" <?php echo $icons; ?>><?php echo _AT('icons_only');  ?></option>
					<option value="2" <?php echo $text; ?>><?php echo _AT('text_only');  ?></option>
					<option value="0" <?php echo $both; ?>><?php echo _AT('icons_and_text');  ?></option>
				</select></td>
		</tr>
			<tr><td height="1" class="row2" colspan="2"></td></tr>
		<tr>
			<td class="row1"><label for="content_icons"><?php echo _AT('content_icons'); ?><?php //echo _AT('login_nav'];  ?>:</label></td>
			<td class="row1"><?php

					$both = '';
					$text = '';
					$icons = '';

					if ($_SESSION['prefs'][PREF_CONTENT_ICONS] == 1) {
						$icons = ' selected="selected"';
					} else if ($_SESSION['prefs'][PREF_CONTENT_ICONS] == 2) {
						$text = ' selected="selected"';
					} else {
						$both = ' selected="selected"';
					}
				?><select name="content_icons" id="content_icons">
					<option value="2" <?php echo $text; ?>><?php echo _AT('text_only');  ?></option>
					<option value="0" <?php echo $both; ?>><?php echo _AT('icons_and_text');  ?></option>
				</select></td>
		</tr>
		</table></td>
		<td valign="top" width="50%"><table border="0"  width="100%" class="bodyline" cellspacing="1" cellpadding="0">
			<tr>
				<th colspan="2" class="cat"><?php print_popup_help(AT_HELP_MENU_OPTIONS); ?><?php  echo _AT('menus'); ?></th>
			</tr>
			<tr>
				<td class="row1" align="center"><?php

				$num_stack = count($_stacks);

				for ($i = 0; $i< 6; $i++) {
					echo '<select name="stack'.$i.'">';
					echo '<option value="">'._AT('empty').'</option>';
					for ($j = 0; $j<$num_stack; $j++) {
						echo '<option value="'.$j.'"';
						if (isset($_SESSION['prefs'][PREF_STACK][$i]) && ($j == $_SESSION[prefs][PREF_STACK][$i])) {
							echo ' selected="selected"';
						}
						echo '>'._AT($_stacks[$j]['file']).'</option>';
					}
					echo '</select>';
					echo '<br />'; 
				}

			?></td>
			</tr>
			</table></td>
	</tr>
	<tr>
		<td colspan="2"><table border="0"  width="50%" class="bodyline" cellspacing="1" cellpadding="0">
			<tr>
				<th colspan="2" class="cat"><?php  echo _AT('theme'); ?></th>
			</tr>
			<tr>
				<td class="row1"><label for="seq_icons"><?php echo _AT('theme');  ?>:</label></td>
				<td class="row1"><select name="theme"><?php
								
								$_themes = explode(',' , AVAILABLE_THEMES);
								
								foreach ($_themes as $theme) {
									$theme = trim($theme);
									if (!$theme) {
										continue;
									}
									$theme_info = get_theme_info($theme);
									if (!$theme_info) {
										continue;
									}

									if ($theme == $_SESSION['prefs']['PREF_THEME']) {
										echo '<option value="'.$theme.'" selected="selected">'.$theme_info['name'].'</option>';
									} else {
										echo '<option value="'.$theme.'">'.$theme_info['name'].'</option>';
									}
								}
								?>
								</select></td>
			</tr>
			</table></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><br />
		<input type="submit" name="submit" value="<?php echo _AT('set_prefs'); ?>" title="<?php echo _AT('set_prefs'); ?>" accesskey="s" class="button" /></td>
	</tr>
	</table>
	</form>

<?php
	require(AT_INCLUDE_PATH.'footer.inc.php');
?>