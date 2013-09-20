<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>JScript Unit Test</title>

    <link rel="stylesheet" href="qunit.css">
    <script src="qunit.js"></script>
    <script src="../element.js"></script>
    <script>
        test("Test form elements", function() {

            //for value=textbox no change
            equal(addButton('select1',1),null);
            document.getElementById('select1').value='radio';//set value
            //for value=radio check HTML code for input button
            equal(addButton('select1',1).outerHTML,'<div id="control-group2"><input type="button" onclick="addOptions(1)" value="Add a Option" id="add1"></div>');
            //add a option and check HTML code
            equal(addOptions(1).outerHTML,'<div id="options-1-0">Option: 1 <br><input type="text" name="userOption1[]" id="opt-1-0"><input type="button" value="remove" onclick="removeOption(1,0)"></div>');
            //add a another option and check HTML code
            equal(addOptions(1).outerHTML,'<div id="options-1-1">Option: 2 <br><input type="text" name="userOption1[]" id="opt-1-1"><input type="button" value="remove" onclick="removeOption(1,1)"></div>');
            //remove 1st option and check HTML code
            equal(removeOption(1,0).innerHTML,'<div id="control-group2"><input type="button" onclick="addOptions(1)" value="Add a Option" id="add1"></div><div id="options-1-1">Option: 2 <br><input type="text" name="userOption1[]" id="opt-1-1"><input type="button" value="remove" onclick="removeOption(1,1)"></div>');
        });
    </script>
</head>
<body>
<div id="qunit"></div>
<div id="qunit-fixture">
<select name="select-1" id="select1"><option value="textbox">textbox</option><option value="radio">radio</option><option value="checkbox">checkbox</option><option value="select">select</option><option value="textarea">textarea</option><option value="password">password</option><option value="date">date</option></select>
<div id=foptions1></div>


</body>
</html>