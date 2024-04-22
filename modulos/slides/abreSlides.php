<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
        <link rel="stylesheet" type="text/css" media="screen" href="class/bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <script src="comp/js/jquery.min.js"></script> 

        <script type="text/javascript">
            $(document).ready(function(){
                $('#conteudo').load('modulos/slides/gereSlide.php');
            });
        </script>
    </head>
    <body>
        <?php
            $_SESSION["msg"] = "";
            $_SESSION['arquivo'] = "";
            $_SESSION['geremsg'] = 0;
            $_SESSION['gerenum'] = 0;
        ?>
        <div id="conteudo" class="container"></div>
    </body>
</html>