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
        <script type="text/javascript">
            $("#insdata").mask("99/99/9999");
        </script>
    </head>
    <body>
        <div style="text-align: center;"><label class="titRelat">Resumo<label></div>
        <?php
        $mes_extenso = array(
            '1' => 'Janeiro',
            '2' => 'Fevereiro',
            '3' => 'Março',
            '4' => 'Abril',
            '5' => 'Maio',
            '6' => 'Junho',
            '7' => 'Julho',
            '8' => 'Agosto',
            '9' => 'Setembro',
            '10' => 'Outubro',
            '11' => 'Novembro',
            '12' => 'Dezembro'
        ); 

        $rs0 = pg_query($Conec, "SELECT DATE_PART('YEAR', datacompra), DATE_PART('MONTH', datacompra) 
        FROM ".$xProj.".viaturas 
        WHERE datacompra IS NOT NULL And ativo = 1 And volume != 0 
        GROUP BY DATE_PART('YEAR', datacompra), DATE_PART('MONTH', datacompra) ORDER BY DATE_PART('YEAR', datacompra) DESC, DATE_PART('MONTH', datacompra) DESC ");
        $row0 = pg_num_rows($rs0);
        if($row0 > 0){
            while($tbl0 = pg_fetch_row($rs0)){
                $Ano = $tbl0[0];
                $Mes = $tbl0[1];
                ?>
                <div style="border: 1px solid; border-radius: 10px">
                    <table style="margin: 0 auto; width: 95%;">
                        <tr>
                            <td style="font-size: 120%; font-weight: bold;"><?php echo $mes_extenso[$Mes]." ".$Ano; ?></td>
                            <td colspan="3" style="text-align: center;"></td>
                            <td colspan="6" style="text-align: center;"></td>
                        </tr>
                        <?php
                        $rs1 = pg_query($Conec, "SELECT ".$xProj.".viaturas_tipo.id, desc_viatura, SUM(volume), SUM(CUSTO)  
                        FROM ".$xProj.".viaturas INNER JOIN ".$xProj.".viaturas_tipo ON ".$xProj.".viaturas.codveiculo = ".$xProj.".viaturas_tipo.id 
                        WHERE DATE_PART('YEAR', datacompra) = $Ano And DATE_PART('MONTH', datacompra) = $Mes And ".$xProj.".viaturas.ativo = 1 And volume != 0 And custo != 0 
                        GROUP BY ".$xProj.".viaturas_tipo.id 
                        ORDER BY desc_viatura ");
                        $row1 = pg_num_rows($rs1);
                        if($row1 > 0){
                            while($tbl1 = pg_fetch_row($rs1)){
                                $CodViat = $tbl1[0];
                                $DescViat = $tbl1[1];
                                
                                $rs4 = pg_query($Conec, "SELECT MIN(odometro) 
                                FROM ".$xProj.".viaturas 
                                WHERE codveiculo = $CodViat And DATE_PART('YEAR', datacompra) = $Ano And DATE_PART('MONTH', datacompra) = $Mes And ".$xProj.".viaturas.ativo = 1 And volume != 0 And custo != 0 ");
                                $tbl4 = pg_fetch_row($rs4);
                                $MinOdometro = $tbl4[0];

                                $rs4 = pg_query($Conec, "SELECT MAX(odometro) 
                                FROM ".$xProj.".viaturas 
                                WHERE codveiculo = $CodViat And DATE_PART('YEAR', datacompra) = $Ano And DATE_PART('MONTH', datacompra) = $Mes And ".$xProj.".viaturas.ativo = 1 And volume != 0 And custo != 0  ");
                                $tbl4 = pg_fetch_row($rs4);
                                $MaxOdometro = $tbl4[0];

                                $rs4 = pg_query($Conec, "SELECT SUM(volume) 
                                FROM ".$xProj.".viaturas 
                                WHERE codveiculo = $CodViat And DATE_PART('YEAR', datacompra) = $Ano And DATE_PART('MONTH', datacompra) = $Mes And ".$xProj.".viaturas.ativo = 1 And volume != 0 And custo != 0 ");
                                $tbl4 = pg_fetch_row($rs4);
                                $VolumeMes = $tbl4[0];

                                $rs4 = pg_query($Conec, "SELECT SUM(custo) 
                                FROM ".$xProj.".viaturas 
                                WHERE codveiculo = $CodViat And coddespesa = 1 And DATE_PART('YEAR', datacompra) = $Ano And DATE_PART('MONTH', datacompra) = $Mes And ".$xProj.".viaturas.ativo = 1 And custo != 0 ");
                                $tbl4 = pg_fetch_row($rs4);
                                $CustoMesAbast = $tbl4[0];

                                $rs4 = pg_query($Conec, "SELECT SUM(custo) 
                                FROM ".$xProj.".viaturas 
                                WHERE codveiculo = $CodViat And coddespesa = 2 And DATE_PART('YEAR', datacompra) = $Ano And DATE_PART('MONTH', datacompra) = $Mes And ".$xProj.".viaturas.ativo = 1 And custo != 0 ");
                                $tbl4 = pg_fetch_row($rs4);
                                $CustoMesManut = $tbl4[0];
                                ?>
                                <tr>
                                    <td><?php echo $DescViat; ?></td>
                                    <td style="font-size: 80%;">Totais: </td>
                                    <td style="font-size: 80%; text-align: right;" title="Volume gasto em combustível no mês."><?php echo number_format(($VolumeMes/100), 2, ",",".")." litros"; ?></td>
                                    <td colspan="3" style="font-size: 80%; text-align: right;" title="Valor gasto em combustível no mês."><?php echo "R$ ".number_format(($CustoMesAbast/100), 2, ",","."); ?></td>
                                    <td style="font-size: 80%; text-align: center;" title="Distância percorrida no mês - Diferença entre o maior e o menor registro do odômetro no mês."><?php echo number_format(($MaxOdometro-$MinOdometro), 0, ",",".")." Km"; ?></td>
                                    <td colspan="2" style="font-size: 80%; text-align: center;" title="Despesa com manutenção no mês."><?php echo "Manut: ".number_format(($CustoMesManut/100), 2, ",","."); ?></td>
                                </tr>
                                <?php
                                $rs2 = pg_query($Conec, "SELECT tipocomb, desc_combust 
                                FROM ".$xProj.".viaturas INNER JOIN ".$xProj.".viaturas_comb ON ".$xProj.".viaturas.tipocomb = ".$xProj.".viaturas_comb.id 
                                WHERE codveiculo = $CodViat And DATE_PART('YEAR', datacompra) = $Ano And DATE_PART('MONTH', datacompra) = $Mes And ".$xProj.".viaturas.ativo = 1 And volume != 0 And custo != 0 
                                GROUP BY tipocomb, desc_combust ORDER BY desc_combust");
                                $row2 = pg_num_rows($rs2);
                                if($row2 > 0){
                                    while($tbl2 = pg_fetch_row($rs2)){
                                        $TipoComb = $tbl2[0];
                                        $DescComb = $tbl2[1];

                                        $rs3 = pg_query($Conec, "SELECT SUM(volume), SUM(CUSTO) 
                                        FROM ".$xProj.".viaturas INNER JOIN ".$xProj.".viaturas_tipo ON ".$xProj.".viaturas.codveiculo = ".$xProj.".viaturas_tipo.id 
                                        WHERE codveiculo = $CodViat And tipocomb = $TipoComb And DATE_PART('YEAR', datacompra) = $Ano And DATE_PART('MONTH', datacompra) = $Mes And ".$xProj.".viaturas.ativo = 1 And volume != 0 And custo != 0 ");
                                        $row3 = pg_num_rows($rs3);
                                        if($row3 > 0){
                                            while($tbl3 = pg_fetch_row($rs3)){
                                                $Volume = $tbl3[0];
                                                $Custo = $tbl3[1];
                                                $rs4 = pg_query($Conec, "SELECT MIN(DATE_PART('DAY', datacompra)) 
                                                FROM ".$xProj.".viaturas 
                                                WHERE codveiculo = $CodViat And DATE_PART('YEAR', datacompra) = $Ano And DATE_PART('MONTH', datacompra) = $Mes And ".$xProj.".viaturas.ativo = 1 And volume != 0 And custo != 0 And odometro != 0 ");
                                                $tbl4 = pg_fetch_row($rs4);
                                                ?>
                                                <tr>
                                                    <td></td>
                                                    <td colspan="3" style="text-align: center; font-size: 80%; font-style: italic;"><?php echo number_format(($Volume/100), 2, ",",".")." litros"; ?></td>
                                                    <td colspan="3" style="text-align: center; font-size: 80%; font-style: italic;"><?php echo "R$ ".number_format(($Custo/100), 2, ",","."); ?></td>
                                                    <td colspan="3" style="text-align: left; font-size: 80%; font-style: italic;"><?php echo $DescComb; ?></td>
                                                </tr>
                                                
                                                <?php
                                            }
                                        }
                                    }
                                }
                                $rs5 = pg_query($Conec, "SELECT tipomanut, desc_manut 
                                FROM ".$xProj.".viaturas INNER JOIN ".$xProj.".viaturas_manut ON ".$xProj.".viaturas.tipomanut = ".$xProj.".viaturas_manut.id  
                                WHERE codveiculo = $CodViat And tipomanut != 0 And DATE_PART('YEAR', datacompra) = $Ano And DATE_PART('MONTH', datacompra) = $Mes And ".$xProj.".viaturas.ativo = 1 And custo != 0 
                                GROUP BY tipomanut, desc_manut ORDER BY desc_manut");
                                $row5 = pg_num_rows($rs5);
                                if($row5 > 0){
                                    while($tbl5 = pg_fetch_row($rs5)){
                                        $TipoManut = $tbl5[0];
                                        $DescManut = $tbl5[1];

                                        $rs4 = pg_query($Conec, "SELECT SUM(custo) 
                                        FROM ".$xProj.".viaturas 
                                        WHERE codveiculo = $CodViat And tipomanut = $TipoManut And DATE_PART('YEAR', datacompra) = $Ano And DATE_PART('MONTH', datacompra) = $Mes And ".$xProj.".viaturas.ativo = 1 And custo != 0 ");
                                        $tbl4 = pg_fetch_row($rs4);
                                        $Custo = $tbl4[0];
                                        
                                        ?>
                                        <tr>
                                            <td></td>
                                            <td colspan="3" style="text-align: center; font-size: 80%; font-style: italic;">Manutenção</td>
                                            <td colspan="3" style="text-align: center; font-size: 80%; font-style: italic;"><?php echo "R$ ".number_format(($Custo/100), 2, ",","."); ?></td>
                                            <td colspan="3" style="text-align: left; font-size: 80%; font-style: italic;"><?php echo $DescManut; ?></td>
                                        </tr>
                                        <?php
                                    }
                                }

                                ?>
                                <tr>
                                    <td colspan="10" style="text-align: center; border-top: 1px solid;"></td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                        <tr>
                            <td colspan="10" style="text-align: center;"></td>
                        </tr>
                    </table>
                    
                </div>
                <br>
                <?php
            }
        }
        ?>
    </body>
</html>