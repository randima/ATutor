<?php
/*******
 * doesn't allow this file to be loaded with a browser.
 */
if (!defined('AT_INCLUDE_PATH')) { exit; }

/******
 * this file must only be included within a Module obj
 */
if (!isset($this) || (isset($this) && (strtolower(get_class($this)) != 'module'))) { exit(__FILE__ . ' is not a Module'); }

/*******
 * assign the instructor and admin privileges to the constants.
 */
define('AT_PRIV_FORM_UTIL',       $this->getPrivilege());
define('AT_ADMIN_PRIV_FORM_UTIL', $this->getAdminPrivilege());


/*******
 * add the admin pages.
 */
if (admin_authenticate(AT_ADMIN_PRIV_FORM_UTIL, TRUE) || admin_authenticate(AT_ADMIN_PRIV_ADMIN, TRUE)) {
    $this->_pages['admin/config_edit.php']['children'] = array('mods/hello_world/index_form_utility.php');
    $this->_pages['mods/hello_world/index_form_utility.php']['title_var'] = 'form_util';
    $this->_pages['mods/hello_world/index_form_utility.php']['parent']    ='admin/config_edit.php';
    $this->_pages['mods/hello_world/index_form_utility.php']['children']  = array('mods/hello_world/modify_form.php','mods/hello_world/create_user.php','mods/hello_world/config.php');


    $this->_pages['mods/hello_world/modify_form.php']['title_var'] = 'preview';
    $this->_pages['mods/hello_world/modify_form.php']['parent']    = 'mods/hello_world/index_form_utility.php';

    $this->_pages['mods/hello_world/create_user.php']['title_var'] = 'create_user';
    $this->_pages['mods/hello_world/create_user.php']['parent']    = 'mods/hello_world/index_form_utility.php';

    $this->_pages['mods/hello_world/config.php']['title_var'] = 'config_tool';
    $this->_pages['mods/hello_world/config.php']['parent']    = 'mods/hello_world/index_form_utility.php';
}


/* public pages */
$this->_pages[AT_NAV_PUBLIC] = array('mods/hello_world/signup.php');
$this->_pages['mods/hello_world/signup.php']['title_var'] = 'register';
$this->_pages['mods/hello_world/signup.php']['parent'] = AT_NAV_PUBLIC;

/* my start page pages */
$this->_pages['users/profile.php']['children']  = array('mods/hello_world/user_profile.php');
$this->_pages['mods/hello_world/user_profile.php']['title_var'] = 'profile';
$this->_pages['mods/hello_world/user_profile.php']['parent'] = 'users/profile.php';



/*******
 * Use the following array to define a tool to be added to the Content Editor's icon toolbar. 
 * id = a unique identifier to be referenced by javascript or css, prefix with the module name
 * class = reference to a css class in the module.css or the primary theme styles.css to style the tool icon etc
 * src = the src attribute for an HTML img element, referring to the icon to be embedded in the Content Editor toolbar
 * title = reference to a language token rendered as an HTML img title attribute
 * alt = reference to a language token rendered as an HTML img alt attribute
 * text = reference to a language token rendered as the text of a link that appears below the tool icon
 * js = reference to the script that provides the tool's functionality
 */

//$this->_content_tools[] = array("id"=>"form_utility_tool",
//                                "class"=>"fl-col clickable",
//                                "src"=>AT_BASE_HREF."mods/hello_world/hello_world.jpg",
//                                "title"=>'title',
//                                "alt"=>'alt',
//                                "text"=>'text',
//                                "js"=>AT_BASE_HREF."mods/hello_world/content_tool_action.js");
//

/*$this->_content_tools[] = array("id"=>"helloworld_tool",
    "class"=>"fl-col clickable",
    "src"=>AT_BASE_HREF."mods/hello_world/hello_world.jpg",
    "title"=>_AT('hello_world_tool'),
    "alt"=>_AT('hello_world_tool'),
    "text"=>_AT('hello_world'),
    "js"=>AT_BASE_HREF."mods/hello_world/content_tool_action.js");*/
/*******
 * Register the entry of the callback class. Make sure the class name is properly namespaced, 
 * for instance, prefixed with the module name, to enforce its uniqueness.
 * This class must be defined in "ModuleCallbacks.class.php".
 * This class is an API that contains the static methods to act on core functions.
 */
//$this->_callbacks['hello_world'] = 'HelloWorldCallbacks';

/*function hello_world_get_group_url($group_id) {
	return 'mods/hello_world/index.php';
}*/
?>