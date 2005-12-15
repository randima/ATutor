<?php
/************************************************************************/
/* ATutor																*/
/************************************************************************/
/* Copyright (c) 2002-2006 by Greg Gay, Joel Kronenberg & Heidi Hazelton*/
/* Adaptive Technology Resource Centre / University of Toronto			*/
/* http://atutor.ca														*/
/*																		*/
/* This program is free software. You can redistribute it and/or		*/
/* modify it under the terms of the GNU General Public License			*/
/* as published by the Free Software Foundation.						*/
/************************************************************************/
// $Id$

require('../common/body_header.inc.php'); ?>

<h2>4.1.1 Entering Content</h2>
	<p>Content can be created in either 'plain text' or 'HTML' mode. Plain text mode is useful for quickly writing up text content. HTML mode allows for extra features like text formatting and layout, but is a little more complex to use.</p>

	<dl>
		<dt>Formatting: Plain Text</dt>
		<dd><p>If using plain text mode, just type the content in the Body window. Note that any extra spaces between characters will be removed (i.e. two or more spaces), but any blank lines will be saved with the text.</p></dd>

		<dt>Formatting: HTML</dt>
		<dd><p>If using HTML mode, you can type HTML tags in the Body window along with your text. If you are unfamiliar with HTML, you can use the visual editor by clicking the <code>Switch to visual editor</code> button.</p></dd>

		<dt>File Manager</dt>
		<dd>
			<p>The File Manager is a tool that allows you to upload files from your local system to be used in your course. The popup File Manager can be open alongside the Content Editor by selecting <kbd>Open File Manager</kbd>.</p>
			
			<p>See the <a href="../instructor/7.0.file_manager.php">File Manager</a> section for details.</p>
		</dd>

		<dt>Terms</dt>
		<dd>
			<p>In either plain text or HTML formatting mode, you can insert <em>terms</em> to tell the ATutor system which words you wish to mark as glossary terms.</p>
			
			<p>Using the <em>Add Term</em> link will add <kbd>[?][/?]</kbd> into your content, and any text you put after <kbd>[?]</kbd> and before <kbd>[/?]</kbd> will specify the term you want to define. Alternatively, you can manually type <kbd>[?][/?]</kbd> into your text without having to use the <em>Add Term</em> link.</p>

			<p>Once you have specified the terms you would like to define, you can go to the <em>Glossary Terms</em> tab to write the definitions. Once this is done, the terms and their definitions will appear in the glossary and in the content.</p>
		</dd>

		<dt>Code</dt>
		<dd>
			<p>In either plain text or HTML formatting mode, you can insert <em>code</em> which is useful for differentiating blocks of text (like math equations, program code, or quotations) from the rest of the text content.</p>
			
			<p>Using the <em>Add Code</em> link will add <kbd>[code][/code]</kbd> into your content, and any text you put after <kbd>[code]</kbd> and before <kbd>[/code]</kbd> will specify the text you want to differentiate. Alternatively, you can manually type <kbd>[code][/code]</kbd> into your text without having to use the <em>Add Code</em> link.</p>
		</dd>

		<dt>Colours</dt>
		<dd><p>Like <em>code</em> and <em>terms</em>, colour may be added to text content in the same way. Use the appropriate colour icon to insert colour tags into the content. Valid colour options are blue, red, green, orange, purple, and gray. Also, colour codes can be typed in manually by using the following tags: <kbd>[blue][/blue]</kbd>, <kbd>[red][/red]</kbd>, <kbd>[green][/green]</kbd>, <kbd>[orange][/orange]</kbd>, <kbd>[purple][/purple]</kbd>, and <kbd>[gray][/gray]</kbd>.</p></dd>

		<dt>Upload from File</dt>
		<dd><p>Rather than typing out content, it can be uploaded from a text or HTML file on your local file system. Once uploaded, the content of that file will be displayed in the <em>Body</em> window. Keep in mind that uploading in this manner will replace any existing content in the <em>Body</em> window.</p></dd>

		<dt>Save and Close</dt>
		<dd><p>While editing or creating content, it is wise to frequently <kbd>Save</kbd> your content.  When you are finished, use <kbd>Close</kbd> to close the content editor.  Note that this does not save your content first so any unsaved content will be lost.</p></dd>
	</dl>

<?php require('../common/body_footer.inc.php'); ?>
