
function options(id){

if(type=='radio'){
    var newdiv=document.createElement('div');
    newdiv.setAttribute("id","control-group2");
    newdiv.innerHTML ="<input type='button' value='Add'>";
    //document.getElementById(id).appendChild(addoption)
    document.getElementById(id).appendChild(addoption);
    }

}


var count=new Array();


function addButton(id,i){

    var d=document.getElementById("foptions"+i);
    while(d.hasChildNodes())
    {
        d.removeChild(d.lastChild);
    }
    count[i]=0;

    var type=document.getElementById(id).value;

    if(type=="radio"||type=="select"||type=="checkbox"){
        //alert("its radio");
        var newdiv=document.createElement('div');
        newdiv.setAttribute("id","control-group2");
        newdiv.innerHTML ="<input type='button' onclick=addOptions("+i+") value='Add a Option' id=add"+i+">";
        document.getElementById("foptions"+i).appendChild(newdiv);
        return newdiv;

    }

}

function addOptions(i){
     var newdiv=document.createElement('div');
     newdiv.setAttribute("id","options-"+i+"-"+count[i]);
     newdiv.innerHTML ="Option: " + (count[i] + 1) + " <br><input type='text' name='userOption"+i+"[]' id=opt-"+i+"-"+count[i]+"><input type='button' value='remove' onclick=removeOption("+i+","+count[i]+")>";
     document.getElementById("foptions"+i).appendChild(newdiv);
    count[i]++;
    return newdiv;
}

function removeOption(i,j){
    var element=document.getElementById("options-"+i+"-"+j);
    var parnt=element.parentNode;
    element.parentNode.removeChild(element);
    count[i]--;
    return parnt;
}

function preview(){
    document.getElementsByName('form').setAttribute('Value','preview')
}

function checkCancel(){
    var hid=document.getElementsByName("submit_form");
    for(var i=0; i<hid.length; i++)
    {
        if(hid[i].value=="save")
        {
            hid[i].value="cancel";
        }
    }
}

function addSettings(){
    var div=document.getElementById("");
    newdiv.setAttribute("id","options-"+i+"-"+count[i]);
    newdiv.innerHTML ="Option: " + (count[i] + 1) + " <br><input type='text' name='userOption"+i+"[]' id=opt-"+i+"-"+count[i]+"><input type='button' value='remove' onclick=removeOption("+i+","+count[i]+")>";
    document.getElementById("foptions"+i).appendChild(newdiv);
    count[i]++;
    return newdiv;
}

