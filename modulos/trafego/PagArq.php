<?php
session_start(); 
if(!isset($_SESSION["usuarioID"])){
    header("Location: /cesb/index.php");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
        <style type="text/css">
            #container5{
                width: 35%;
                min-height: 500px;
                margin: 10px;
                margin-top: 12px;
                border: 2px solid blue; 
                border-radius: 10px; 
                padding: 10px;
            }
            #container6{
                width: 60%;
                min-height: 500px;
                margin: 10px;
                margin-top: 12px;
                border: 2px solid blue; 
                border-radius: 10px; 
                padding: 10px;
            }
            .cContainer{ /* encapsula uma frase no topo de uma div em reArq.php e pagArq.php */
                position: absolute; 
                left: 20px;
                margin-top: -20px; 
                border: 1px solid blue; 
                border-radius: 10px; 
                padding-left: 10px; 
                padding-right: 10px; 
            }
            .bContainer{ /* botão upload */
                position: absolute; 
                right: 30px;
                margin-top: -20px; 
                border: 1px solid blue;
                background-color: blue;
                color: white;
                cursor: pointer;
                border-radius: 10px; 
                padding-left: 10px; 
                padding-right: 10px; 
            }
        </style>
        <script type="text/javascript">
            $(document).ready(function(){
                $("#container5").load("modulos/trafego/pagTraf.php");
                $("#container6").load("modulos/trafego/relTraf.php");
            })
        </script>
    </head>
    <body>
        <div id="container5"></div>
        <div id="container6"></div> <!--  containers 6 na página relTraf.php -->  
    </body>
</html>