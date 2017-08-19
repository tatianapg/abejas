<html>
<head>
<meta http-equiv="Content-Type" content="text/html charset=utf-8"/>
<script src="./js/jquery.js"></script>

</head>
<body>
<?php echo ("Antes de os tabs");?>
<form>
<div id="tabs">
 <ul>
    <li><a href="#tab1">Tab 1</a></li>
    <li><a href="#tab2">Tab 2</a></li>
    <li><a href="#tab3">Tab 3</a></li>
    <li><a href="#tab4">Tab 4</a></li>
 </ul>
    <div id="tab1">HTML5facil BLog de la comunidad HTML5</div>
    <div id="tab2">Otro Hola Mundo pero en jQueryUI</div>
    <div id="tab3">Lorem ipsum</div>
    <div id="tab4">Contenido pestaña 4</div>
 </div>
 <script> $("#tabs").tabs (); </script>
 <script>
 $("#tabs").tabs({
 }).tabs("add", "#tab5", "Tab 5");
       $("<i>Añadiendo Tabs dinamicos</i>").appendTo("#tab5");
 
 $("#tabs").tabs({
 }).tabs("url",0, "./lab.php").tabs("load", 0);
 
 $("#tabs").tabs({
            fx : { opacity : "toggle" },
            add: function(event, tab){
 $(tab.panel).load("./lab.php");
 }
 }).tabs("add", "#tab6", "Tab 6");
 </script>
 </form>
 </body>
 </html>