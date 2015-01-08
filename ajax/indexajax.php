
<html>
    <head>
<script src="js/jquery-1.11.1.js"></script>

<script>
function loadXMLDoc()
{
var xmlhttp;

//alert('inicio');
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
 // alert('firefox');
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
    //  alert('ready');
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
        alert(xmlhttp.responseText);
    document.getElementById("ajax_tab").innerHTML=xmlhttp.responseText;
    //document.getElementById("ajax_tab").innerHTML="teste";
    }
  }
xmlhttp.open("GET","http://xxxx.com/",true);
xmlhttp.send();
}
</script>
    </head>

    <body>
        <script>


        </script>
        inicio ajax client
<button type="button" onclick="loadXMLDoc()"> Content</button>

        <div id="ajax_tab">

aqui!!!!
        </div>
    </body>

    <script>
    
    </script>
</html>
