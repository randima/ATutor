<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2004 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
if (!defined('AT_INCLUDE_PATH')) { exit; }

?><br />
	<table border="0" summary="" width="100%" cellspacing="0" cellpadding="2">
	<?php
	
		$getcopyright_sql="select copyright from ".TABLE_PREFIX."courses where course_id='$_SESSION[course_id]'";	
		$result2 = @mysql_query($getcopyright_sql, $db);
		$row = @mysql_fetch_row($result2);
		$show_edit_copyright = $row[0];
		if(strlen($show_edit_copyright)>0){
			echo '<tr><td align="center" colspan="2"><small>' , $show_edit_copyright , '</small></td></tr>';
		} 

		?>
	<tr>
	<?php
	/****************************************************************************************/
	/* VERY IMPORTANT
	   IN KEEPING WITH THE TERMS OF THE ATUTOR LICENCE AGREEMENT (GNU GPL), THE FOLLOWING
	   COPYRIGHT LINES MAY NOT BE ALTERED IN ANY WAY.
	*/
	
		?><td><a href="http://www.atutor.ca" target="_new"><img src="<?php echo $_base_path;?>images/at-logo.gif" alt="ATutor.ca" height="26" width="80" border="0" style="height:1.6em; width:5em;" /></td><td align="center"><small><small><?php echo _AT('copyright'); ?><br /><span id="howto"><?php echo _AT('general_help'); ?></span></small></small></td>
	</tr>
	</table>