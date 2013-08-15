<?php 
/************************************************************************/
/* ATutor                                                               */
/************************************************************************/
/* Copyright (c) 2002-2010                                              */
/* http://atutor.ca                                                     */
/*                                                                      */
/* This program is free software. You can redistribute it and/or        */
/* modify it under the terms of the GNU General Public License          */
/* as published by the Free Software Foundation.                        */
/************************************************************************/
/* This file was generated by the ATutor 2.1.1 installation script.     */
/* File generated 2013-08-15 10:08:32                                   */
/************************************************************************/
/************************************************************************/
/* the database user name                                               */
define('DB_USER',                      'root');

/* the database password                                                */
define('DB_PASSWORD',                  '');

/* the database host                                                    */
define('DB_HOST',                      'localhost');

/* the database tcp/ip port                                             */
define('DB_PORT',                      '3306');

/* the database name                                                    */
define('DB_NAME',                      'atutor');

/* The prefix to add to table names to avoid conflicts with existing    */
/* tables. Default: AT_                                                 */
define('TABLE_PREFIX',                 'AT_');

/* Where the course content files are located.  This includes all file  */
/* manager and imported files.  If security is a concern, it is         */
/* recommended that the content directory be moved outside of the web	*/
/* accessible area.														*/
define('AT_CONTENT_DIR', 'C:\wamp\www\ATutor\content\\');

/* Whether or not to use the default php.ini SMTP settings.             */
/* If false, then mail will try to be sent using sendmail.              */
define('MAIL_USE_SMTP', true);

/* Whether or not to use the AT_CONTENT_DIR as a protected directory.   */
/* If set to FALSE then the content directory will be hard coded        */
/* to ATutor_install_dir/content/ and AT_CONTENT_DIR will be ignored.   */
/* This option is used for compatability with IIS and Apache 2.         */
define('AT_FORCE_GET_FILE', TRUE);

/* DO NOT ALTER THIS LAST LINE                                          */
define('AT_INSTALL', TRUE);

?>