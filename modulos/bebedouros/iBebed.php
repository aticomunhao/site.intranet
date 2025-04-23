<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="comp/js/plotly.min.js"></script>
        <title></title>
        <style>
            .quadroClick {
                position: relative; 
                float: left;
                font-size: 13px;
                min-width: 30px;
                border: 1px solid gray;
                border-radius: 5px;
                cursor: pointer;
                margin: 2px; 
                padding: 2px; 
                background-color: transparent;
            }
        </style>
        <script type="text/javascript">

        </script>
    </head>
    <body>
        <div style="text-align: center;">Recargas dos últimos 30 dias</div>
        <div class="box" style="margin: 0 auto; text-align: center; min-height: 200px;">
            <?php
            if(isset($_REQUEST["codigo"])){
                $Cod = $_REQUEST["codigo"];
            }else{
                $Cod = 1;
            }

            $rs1 = pg_query($Conec, "SELECT id, bebed_id, TO_CHAR(datatroca, 'DD/MM/YYYY'), volume 
            FROM ".$xProj.".bebed_ctl  
            WHERE bebed_id = $Cod And datatroca < CURRENT_DATE + interval '30 days' And ativo = 1 
            ORDER BY datatroca DESC ");

            $row1 = pg_num_rows($rs1);
            if($row1 > 0){
                while($tbl1 = pg_fetch_row($rs1) ){
                    ?>
                    <div class="quadroClick" onclick="carEditaAbastec(<?php echo $Cod; ?>, <?php echo $tbl1[0]; ?>, '<?php echo $tbl1[2]; ?>', <?php echo $tbl1[3]; ?>)"> <?php echo $tbl1[2]." ".$tbl1[3]." litros"; ?></div>
                    <?php
                }
            }else{
            ?>
            <div>
                <table style="margin: 0 auto; width: 95%;">
                    <tr>
                        <td style="padding-bottom: 2px; text-align: center;"><div style="margin: 5px; border: 1px solid; border-radius: 10px;">Nada encontrado</div></td>
                    </tr>
                </table>
            </div>
            <?php
        }
        ?>
        </div>
    </body>
</html>