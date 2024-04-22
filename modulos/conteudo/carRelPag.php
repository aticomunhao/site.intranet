<?php
session_start(); 
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
        <style type="text/css">
        </style>
    </head>
    <body>
        <?php
            require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
            $rs0 = pg_query($Conec, "SELECT textopag FROM ".$xProj.".setores WHERE codset = ".$_SESSION["PagDir"]);
            $row0 = pg_num_rows($rs0);
            if($row0 > 0){
                $Proc0 = pg_fetch_row($rs0);
                $TextoPag = html_entity_decode($Proc0[0]);
            }else{
                $TextoPag = "";
            }
        ?>
        <div id="contentPagggg" style="margin: 30px;"> <?php echo $TextoPag; ?> </div>
    </body>
</html>