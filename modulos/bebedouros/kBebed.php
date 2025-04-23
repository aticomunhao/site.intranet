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
    </head>
    <body>
        <div style="text-align: center; border: 1px solid; border-radius: 15px; min-height: 500px;">Agenda
        <?php
            $Bebed = parEsc("bebed", $Conec, $xProj, $_SESSION["usuarioID"]);
            $FiscBebed = parEsc("bebed_fisc", $Conec, $xProj, $_SESSION["usuarioID"]);

        $rs1 = pg_query($Conec, "SELECT ".$xProj.".bebed.id, numapar, descmarca, desctipo, localinst, TO_CHAR(datavencim, 'DD/MM/YYYY'), TO_CHAR(datavencim, 'YYYY'), 
        CASE WHEN dataaviso <= CURRENT_DATE AND notific = 1 THEN 'aviso' END
        FROM ".$xProj.".bebed_tipos INNER JOIN (".$xProj.".bebed INNER JOIN ".$xProj.".bebed_marcas ON ".$xProj.".bebed.codmarca = ".$xProj.".bebed_marcas.id) ON ".$xProj.".bebed.codtipo =  ".$xProj.".bebed_tipos.id 
        WHERE ".$xProj.".bebed.ativo = 1 ORDER BY numapar ");

        $row1 = pg_num_rows($rs1);
        if($row1 > 0){
            while($tbl1 = pg_fetch_row($rs1)){
                $Cod = $tbl1[0];
                $Num = $tbl1[1];
                $NumFormat = str_pad($Num, 3, 0, STR_PAD_LEFT);
                $Marca = $tbl1[2];
                $Tipo = $tbl1[3];
                $Local = substr($tbl1[4], 0, 60);

                $rs2 = pg_query($Conec, "SELECT SUM(volume) FROM ".$xProj.".bebed_ctl WHERE bebed_id = $Cod And datatroca < CURRENT_DATE + interval '1 years' And ativo = 1");
                $tbl2 = pg_fetch_row($rs2);
                if(!is_null($tbl2[0]) && $tbl2[0] != ""){
                    $TotalAno = $tbl2[0];
                }else{
                    $TotalAno = 0;
                }
                ?>
                <div style="margin: 8px; padding-top: 3px; border: 1px solid gray; border-radius: 10px">
                    <table style="margin: 0 auto; width: 95%;">
                        <tr>
                            <td style="font-weight: bold; width: 15%; text-align: center;"><div style="border: 1px solid; border-radius: 10px;"> <?php echo str_pad($Num, 3, 0, STR_PAD_LEFT); ?> </div></td>
                            <td style="text-align: left; padding-left: 2px;"><?php echo $Local; ?></td>
                        </tr>
                    </table>
                    <table style="margin: 0 auto; width: 95%;">
                        <tr>
                            <td style="font-size: 80%; text-align: right; padding-right: 3px;"><?php if($tbl1[6] == "3000"){echo "<label style='color: red;'>Definir data para limpeza da base</label>";}else{if($tbl1[7] == 'aviso'){echo "<label style='color: red; font-weight: bold;'>Limpeza da base em: </label>";}} ?></td>
                            <td style="font-size: 80%; font-weight: bold; text-align: center; width: 80px;"><?php if($tbl1[6] != "3000" && $tbl1[7] == "aviso"){echo $tbl1[5];} ?> </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-bottom: 2px;"></td>
                        </tr>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 2px; text-align: center; border-top: 1px solid gray; font-size: 70%;"><?php echo "Cons: ".number_format($TotalAno, 0, ",",".")." litros"; ?></td>
                            <td style="padding-bottom: 2px; text-align: center; border-top: 1px solid gray; width: 80px;">
                                <?php
                                if($Bebed == 1 || $_SESSION["AdmUsu"] > 6){
                                ?>
                                    <button class="botpadrblue" style="font-size: 70%; padding-left: 2px; padding-right: 2px;" onclick="insAbastec(<?php echo $Cod; ?>, '<?php echo $NumFormat; ?>');">Abastec</button>
                                <?php
                                }
                                ?>
                            </td>
                        </tr>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-bottom: 2px;"></td>
                        </tr>
                    </table>
                </div>
                <?php
            }
        }else{
            ?>
            <div>
                <table style="margin: 0 auto; width: 95%;">
                    <tr>
                        <td style="padding-bottom: 2px; text-align: center;"><div style="margin: 5px; border: 1px solid; border-radius: 10px;">Tudo em Ordem</div></td>
                    </tr>
                </table>
            </div>
            <?php
        }
        ?>
        </div>
    </body>
</html>