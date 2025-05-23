<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
            require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
            $rs0 = pg_query($Conec, "SELECT textopag FROM ".$xProj.".setores WHERE codset = 1");
            $row0 = pg_num_rows($rs0);
            if($row0 > 0){
                $Proc0 = pg_fetch_row($rs0);
                $TextoPag = html_entity_decode($Proc0[0]);
            }else{
                $TextoPag = "";
            }
        ?>
        <div id="contentPagIni" style="margin: 30px; margin-top: 10px;"> <?php echo $TextoPag; ?> </div>
    </body>
</html>