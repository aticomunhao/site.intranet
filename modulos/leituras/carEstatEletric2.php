<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
        <script type="text/javascript">
            $("#insdata").mask("99/99/9999");
        </script>
    </head>
    <body>
        <?php
            $Menu2 = escMenu($Conec, $xProj, 2);
        ?>
        <div style="text-align: center;"><label class="titRelat">Controle do Consumo de Eletricidade<?php echo " - ".$Menu2; ?><label></div>
        <?php
        $mes_extenso = array(
            '01' => 'Janeiro',
            '02' => 'Fevereiro',
            '03' => 'Março',
            '04' => 'Abril',
            '05' => 'Maio',
            '06' => 'Junho',
            '07' => 'Julho',
            '08' => 'Agosto',
            '09' => 'Setembro',
            '10' => 'Outubro',
            '11' => 'Novembro',
            '12' => 'Dezembro'
        ); 


        //Dia adotado pela neoenergia para calcular o consumo de um mês para outro
        $DiaMedia = parAdm("dialeit_eletr", $Conec, $xProj);
        if(strLen($DiaMedia) < 2){
            $DiaMedia = "0".$DiaMedia;
        }
        $ValorKwh = parAdm("valorkwh", $Conec, $xProj); // é o mesmo para pag_eletric2 e 3

        $rs = pg_query($Conec, "SELECT valorinieletric2, TO_CHAR(datainieletric2, 'YYYY/MM/DD') FROM ".$xProj.".paramsis WHERE idpar = 1 ");
        $row = pg_num_rows($rs);
        if($row > 0){
            $tbl = pg_fetch_row($rs);
            $ValorIni = $tbl[0];
            $DataIni = $tbl[1];
        }
        if($ValorIni == 0 || is_null($DataIni)){
            echo "<div style='text-align: center;'>É necessário inserir os valores iniciais da medição nos parâmetros do sistema.</div>";
            echo "<div style='text-align: center;'>Informe à ATI.</div>";
            return false;
        }

        $rs1 = pg_query($Conec, "SELECT DATE_PART('MONTH', dataleitura2), COUNT(id), SUM(leitura2), DATE_PART('YEAR', dataleitura2)  
        FROM ".$xProj.".leitura_eletric 
        WHERE colec = 2 And dataleitura2 IS NOT NULL And leitura2 != 0 And ativo = 1
        GROUP BY DATE_PART('MONTH', dataleitura2), DATE_PART('YEAR', dataleitura2) ORDER BY DATE_PART('YEAR', dataleitura2) DESC, DATE_PART('MONTH', dataleitura2) DESC ");

        $row1 = pg_num_rows($rs1);
        if($row1 > 0){
            while($tbl1 = pg_fetch_row($rs1) ){
                $Ano = $tbl1[3];
                $Mes = $tbl1[0];
                if(strLen($Mes) < 2){
                    $Mes = "0".$Mes;
                }
                $QuantDias = $tbl1[1];
                $Cons1 = 0;
                $MediaDiaria = 0;
                $SomaLeit1 = 0;
                $SomaLeitAnt = 0;

                $rsCusto = pg_query($Conec, "SELECT valorkwh FROM ".$xProj.".leitura_eletric WHERE DATE_PART('MONTH', dataleitura1) = $Mes And DATE_PART('YEAR', dataleitura1) = '$Ano' And colec = 1 And ativo = 1 And leitura1 != 0 ");
                $rowCusto = pg_num_rows($rsCusto); // dá a quantidade de dias no mês

                $rsSoma = pg_query($Conec, "SELECT SUM(valorkwh) FROM ".$xProj.".leitura_eletric WHERE DATE_PART('MONTH', dataleitura1) = $Mes And DATE_PART('YEAR', dataleitura1) = '$Ano' And colec = 1 And ativo = 1 And leitura1 != 0 ");
                $tblSoma = pg_fetch_row($rsSoma);
                $CalcValorKwh = ($tblSoma[0]/$rowCusto);

                $rs2 = pg_query($Conec, "SELECT dataleitura2, leitura2 FROM ".$xProj.".leitura_eletric WHERE DATE_PART('MONTH', dataleitura2) = $Mes And colec = 2 And ativo = 1 And leitura2 != 0 ");
                $row2 = pg_num_rows($rs2);
                if($row2 > 0){
                    while($tbl2 = pg_fetch_row($rs2) ){
                        $DataLinha = $tbl2[0]; // dataleitura2
                        $SomaLeit1 = $SomaLeit1+$tbl2[1];

                        if(strtotime($DataLinha) == strtotime($DataIni)){ // datainieletric em cesb.paramsis
                            $SomaLeitAnt = ($SomaLeitAnt+$ValorIni);  // valorinieletric em cesb.paramsis
                        }

                        if($DataLinha != $DataIni){
                            $rs3 = pg_query($Conec, "SELECT leitura2 FROM ".$xProj.".leitura_eletric WHERE dataleitura2 = (date '$DataLinha' - 1) And colec = 2 And ativo = 1 And leitura2 != 0");
                            $tbl3 = pg_fetch_row($rs3);
                            $row3 = pg_num_rows($rs3);
                            if($row3 > 0){
                                $SomaLeitAnt = ($SomaLeitAnt+$tbl3[0]);
                            }
                        }
                        $Cons1 = ($SomaLeit1-$SomaLeitAnt);
                        $MediaDiaria = $Cons1/$QuantDias;
                    }
                }

                $MesAnt = ($Mes-1);
                if(strLen($MesAnt) < 2){
                    $MesAnt = "0".$MesAnt;
                }

                $rsA = pg_query($Conec, "SELECT leitura2 FROM ".$xProj.".leitura_eletric 
                WHERE DATE_PART('YEAR', dataleitura2) = '$Ano' And  TO_CHAR(dataleitura2, 'MM') = '$MesAnt' And TO_CHAR(dataleitura2, 'DD') = '$DiaMedia' And ativo = 1 ");
                $rowA = pg_num_rows($rsA);
                if($rowA > 0){
                    $tblA = pg_fetch_row($rsA);
                    $LeitMesAnt = $tblA[0];
                }else{
                    $LeitMesAnt = 0;
                }
                $rsB = pg_query($Conec, "SELECT leitura2 FROM ".$xProj.".leitura_eletric 
                WHERE DATE_PART('YEAR', dataleitura2) = '$Ano' And  TO_CHAR(dataleitura2, 'MM') = '$Mes' And TO_CHAR(dataleitura2, 'DD') = '$DiaMedia' And ativo = 1");
                $rowB = pg_num_rows($rsB);
                if($rowB > 0){
                    $tblB = pg_fetch_row($rsB);
                    $LeitMesAtual = $tblB[0];
                }else{
                    $LeitMesAtual = 0;
                }

                if($LeitMesAtual == 0 || $LeitMesAnt == 0){ // ainda não chegou o dia
                    if($LeitMesAtual == 0){
                        $ConsCalc = "Agd dia ".$DiaMedia."/".$Mes;
                        $ValorCalc = "Agd dia ".$DiaMedia."/".$Mes;
                    }
                    if($LeitMesAnt == 0){
                        $ConsCalc = "Agd dia ".$DiaMedia."/".$MesAnt;
                        $ValorCalc = "Agd dia ".$DiaMedia."/".$MesAnt;
                    }
                }else{
                    $ConsCalc = number_format(($LeitMesAtual - $LeitMesAnt), 0, ",",".");
                    $ValorCalc = "R$ ".number_format(($ConsCalc*$CalcValorKwh), 2, ",",".");
                }

                ?>
                <div style="border: 1px solid; border-radius: 10px">
                    <table style="margin: 0 auto; width: 95%;">
                        <tr>
                            <td style="font-size: 120%; font-weight: bold;"><?php echo $mes_extenso[$Mes]." ".$Ano; ?></td>
                            <td colspan="3" style="text-align: center;"></td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid gray; font-size: 80%; font-weight: bold;"></td>
                            <td style="border-bottom: 1px solid gray; font-size: 80%; text-align: center;"></td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid gray; font-size: 90%;">Consumo Mensal</td>
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 90%;"><?php echo number_format(($Cons1), 0, ",","."); ?> kWh</td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid gray; font-size: 90%;">Consumo Médio Diário <?php if($QuantDias == 1){echo " (1 dia)";}else{echo "(".$QuantDias." dias)";} ?></td>
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 90%;"><?php echo number_format(($Cons1/$QuantDias), 0, ",","."); ?> kWh</td>
                        </tr>


                        <tr>
                            <td style="border-bottom: 1px solid gray; font-size: 90%;">Consumo Calculado 
                                <?php 
                                    echo "(".$DiaMedia."/".$MesAnt." a ".$DiaMedia."/".$Mes.") &rarr;";
                                    if($LeitMesAtual > 0 && $LeitMesAnt > 0){
                                        echo " (".$LeitMesAtual." - ".$LeitMesAnt.")";
                                    }
                                ?>
                            </td>
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 90%;">
                                <?php 
                                echo $ConsCalc; 
                                if(substr($ConsCalc, 0, 3) != "Agd"){
                                    echo " kWh"; 
                                }
                                ?> 
                            </td>
                        </tr>

                        <tr>
                            <td style="border-bottom: 1px solid gray; font-size: 90%;">Valor Consumo Mensal Calculado <?php echo "(Dia ".$DiaMedia.")"; ?> </td>
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 90%;"><?php echo $ValorCalc ; ?> </td>
                        </tr>

                        <tr>
                            <td colspan="3" style="font-size: 90%; text-align: right;"></td>
                            <td style="text-align: center; color: red;"></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="padding-bottom: 2px;"></td>
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