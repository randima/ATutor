
function update_table(){

    var text="";
    var content=new Array();
    var elements=document.getElementsByClassName("editable");
    for(var i=0; i<elements.length; i++)
    {
        var children=elements[i].childNodes;
        var rank=children[1].innerHTML;
        var label=children[2].innerHTML;
        var id=children[3].innerHTML;
        //text+="rank='"+rank+"' label='"+label+"' ; ";
        content[i] = rank+":"+label+":"+id+"";
    }

    jQuery.ajax({
        url: "http://localhost/ATutor/mods/hello_world/save.php",
        type: 'POST',
        data:{
            content: content.toString()
        }
    });
    alert(content.toString());

}