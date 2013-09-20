<?php

session_start();

//var_dump($_POST);

if(isset($_POST["submt"]))
{
    echo "Welcome your name is ".$_POST['First_Name']." ".$_POST['Last_Name']." ,email is ".$_POST['Email']. " and your password is ".$_POST['Password'];
}

use PFBC\Form;
use PFBC\Element;
include("PFBC/Form.php");
$form = new Form("form-test");
$form->configure(array( "prevent" => array("bootstrap", "jQuery")));

$con=mysqli_connect("localhost","root","","test");

// Check connection
if (mysqli_connect_errno($con))
{
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$result = mysqli_query($con,"SELECT * FROM form");

$form->addElement(new Element\HTML('<legend>Registration</legend>'));

//echo "<table>";

while($row = mysqli_fetch_array($result))
{
    if($row['type']=="Textbox")
        $form->addElement(new Element\Textbox($row['name'], $row['name']));
    if($row['type']=="Email")
        $form->addElement(new Element\Email($row['name'], "Email", array("required" => 1)));


    //echo "<tr><td>".$row['name']."</td><td>".$row['type']."</td></tr>";
}
//echo "</table>";
$form->addElement(new Element\Password("Password:", "Password"));
$form->addElement(new Element\Radio("Radio Buttons:", "RadioButtons",array('male','female'),array("value"=>"female","id"=>array("m","f"))));
$form->addElement(new Element\Button("Submit","submit",array("name"=>"submt")));
$form->addElement(new Element\Checkbox("Hey","hi",array('cat','dog','snake','crow'),array("value"=>array("dog","crow"))));
mysqli_close($con);

$form->render();
var_dump($_POST);
//$form = new Form("form-create");
//$form->addElement(new Element\HTML('<legend>Add Fields</legend>'));
//$form->addElement(new Element\Textbox("Type","type"));
//$form->addElement(new Element\Textbox("Label","label"));
//$form->render();


?>