<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');


if(isset($_POST['content'])){

$content = $_POST['content']; //get posted data
$data=explode(",",$content);
$text="";
foreach($data as $row){
    $row = explode(":",$row);
    $sql = "UPDATE %sforms SET rank='".$row[0]."', label='".$row[1]."' WHERE id='".$row[2]."'";
    queryDB($sql, array(TABLE_PREFIX));
}

}

//$current = file_get_contents("people.txt");
//$current.=$content;
//file_put_contents("people.txt",$sql2);


?>