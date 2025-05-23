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
        $MesAtual = date("m");
        $AnoAtual = date("Y");
        $ValorKwh = parAdm("valorkwh", $Conec, $xProj);

        $Menu1 = escMenu($Conec, $xProj, 1);
        $Menu2 = escMenu($Conec, $xProj, 2);
        $Menu3 = escMenu($Conec, $xProj, 3);
        ?>
        <div style="text-align: center;"><label class="titRelat corPreta">Controle do Consumo de Eletricidade<?php echo " - ".$Menu1; ?><label></div>
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

        $FatorCor = parAdm("fatorcor_eletr", $Conec, $xProj); // fator a aplicar para acertar com o consumo das antenas
        //Dia adotado pela neoenergia para calcular o consumo de um mês para outro
        $DiaMedia = parAdm("dialeit_eletr", $Conec, $xProj);
        if(strLen($DiaMedia) < 2){
            $DiaMedia = "0".$DiaMedia;
        }

        $rs = pg_query($Conec, "SELECT valorinieletric, TO_CHAR(datainieletric, 'YYYY/MM/DD') FROM ".$xProj.".paramsis WHERE idpar = 1 ");
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

        $Condic = "colec = 1 And dataleitura1 IS NOT NULL And leitura1 != 0 And ativo = 1";
        if(isset($_REQUEST["mesano"])){
            $MesAno = addslashes(filter_input(INPUT_GET, 'mesano')); 
            if($MesAno != ""){
                $Proc = explode("/", $MesAno);
                $Mes = $Proc[0];
                if(strLen($Mes) < 2){
                    $Mes = "0".$Mes;
                }
                $Ano = $Proc[1];
                $Condic = "colec = 1 And dataleitura1 IS NOT NULL And leitura1 != 0 And ativo = 1 And DATE_PART('MONTH', dataleitura1) = '$Mes' And DATE_PART('YEAR', dataleitura1) = '$Ano'";
            }else{
                $Condic = "colec = 1 And dataleitura1 IS NOT NULL And leitura1 != 0 And ativo = 1";
            }
        }
        if(isset($_REQUEST["ano"])){
            $Ano = addslashes(filter_input(INPUT_GET, 'ano')); 
            if($Ano != ""){
                $Condic = "colec = 1 And dataleitura1 IS NOT NULL And leitura1 != 0 And ativo = 1 And DATE_PART('YEAR', dataleitura1) = '$Ano'";
            }else{
                $Condic = "colec = 1 And dataleitura1 IS NOT NULL And leitura1 != 0 And ativo = 1";
            }
        }

        $rs1 = pg_query($Conec, "SELECT DATE_PART('MONTH', dataleitura1), COUNT(id), SUM(leitura1), DATE_PART('YEAR', dataleitura1) 
        FROM ".$xProj.".leitura_eletric 
        WHERE $Condic 
        GROUP BY DATE_PART('MONTH', dataleitura1), DATE_PART('YEAR', dataleitura1) ORDER BY DATE_PART('YEAR', dataleitura1) DESC, DATE_PART('MONTH', dataleitura1) DESC ");

        $row1 = pg_num_rows($rs1);
        if($row1 > 0){
            while($tbl1 = pg_fetch_row($rs1)){ // messes e anos
                $Mes = $tbl1[0];
                $Ano = $tbl1[3];
                if(strLen($Mes) < 2){
                    $Mes = "0".$Mes;
                }                
                $QuantDias = $tbl1[1];
                $Cons1 = 0;
                $MediaDiaria = 0;
                $SomaLeit1 = 0;
                $SomaLeitAnt = 0;
                $SomaCons1Fator = 0;

                $rsCusto = pg_query($Conec, "SELECT valorkwh FROM ".$xProj.".leitura_eletric WHERE DATE_PART('MONTH', dataleitura1) = $Mes And DATE_PART('YEAR', dataleitura1) = '$Ano' And colec = 1 And ativo = 1 And leitura1 != 0 ");
                $rowCusto = pg_num_rows($rsCusto); // dá a quantidade de dias no mês

                $rsSoma = pg_query($Conec, "SELECT SUM(valorkwh) FROM ".$xProj.".leitura_eletric WHERE DATE_PART('MONTH', dataleitura1) = $Mes And DATE_PART('YEAR', dataleitura1) = '$Ano' And colec = 1 And ativo = 1 And leitura1 != 0 ");
                $tblSoma = pg_fetch_row($rsSoma);
                $CalcValorKwh = ($tblSoma[0]/$rowCusto);

                $rs2 = pg_query($Conec, "SELECT dataleitura1, leitura1, fator, valorkwh FROM ".$xProj.".leitura_eletric WHERE DATE_PART('MONTH', dataleitura1) = $Mes And DATE_PART('YEAR', dataleitura1) = '$Ano' And colec = 1 And ativo = 1 And leitura1 != 0 ");
                $row2 = pg_num_rows($rs2);
                if($row2 > 0){
                    while($tbl2 = pg_fetch_row($rs2)){
                        $DataLinha = $tbl2[0]; // dataleitura1
                        $SomaLeit1 = $SomaLeit1+$tbl2[1];

                        if(strtotime($DataLinha) == strtotime($DataIni)){ // datainieletric em cesb.paramsis
                            $SomaLeitAnt = ($SomaLeitAnt+$ValorIni);  // valorinieletric em cesb.paramsis
                        }

                        if($DataLinha != $DataIni){
                            $rs3 = pg_query($Conec, "SELECT leitura1 FROM ".$xProj.".leitura_eletric WHERE dataleitura1 = (date '$DataLinha' - 1) And colec = 1 And ativo = 1 And leitura1 != 0");
                            $tbl3 = pg_fetch_row($rs3);
                            $row3 = pg_num_rows($rs3);
                            if($row3 > 0){
                                $SomaLeitAnt = ($SomaLeitAnt+$tbl3[0]);
                            }
                        }
                        $Cons1 = ($SomaLeit1-$SomaLeitAnt);
                        $Cons2 = (($SomaLeit1-$SomaLeitAnt)*$tbl2[2]); // fator de correção do bc registrado junto com o consumo
                    }
                }
                //Consumo mensal calculado com a soma do consumo diário 
                $rs4 = pg_query($Conec, "SELECT SUM(consdiario1) FROM ".$xProj.".leitura_eletric WHERE DATE_PART('MONTH', dataleitura1) = $Mes And DATE_PART('YEAR', dataleitura1) = '$Ano' And colec = 1 And ativo = 1 And leitura1 != 0");
                $tbl4 = pg_fetch_row($rs4);
                $ConsMensal = $tbl4[0];

                $MesAnt = ($Mes-1);
                if(strLen($MesAnt) < 2){
                    $MesAnt = "0".$MesAnt;
                }

                $rsA = pg_query($Conec, "SELECT leitura1 FROM ".$xProj.".leitura_eletric 
                WHERE DATE_PART('YEAR', dataleitura1) = '$Ano' And TO_CHAR(dataleitura1, 'MM') = '$MesAnt' And TO_CHAR(dataleitura1, 'DD') = '$DiaMedia' And ativo = 1 ");
                $rowA = pg_num_rows($rsA);
                if($rowA > 0){
                    $tblA = pg_fetch_row($rsA);
                    $LeitMesAnt = $tblA[0];
                }else{
                    $LeitMesAnt = 0;
                }

                $rsB = pg_query($Conec, "SELECT leitura1 FROM ".$xProj.".leitura_eletric 
                WHERE DATE_PART('YEAR', dataleitura1) = '$Ano' And TO_CHAR(dataleitura1, 'MM') = '$Mes' And TO_CHAR(dataleitura1, 'DD') = '$DiaMedia' And ativo = 1 ");
                $rowB = pg_num_rows($rsB);
                if($rowB > 0){
                    $tblB = pg_fetch_row($rsB);
                    $LeitMesAtual = $tblB[0];
                }else{
                    $LeitMesAtual = 0;
                }

                //Energia Injetada
                $rsC = pg_query($Conec, "SELECT leitura4 FROM ".$xProj.".leitura_eletric 
                WHERE DATE_PART('YEAR', dataleitura4) = '$Ano' And TO_CHAR(dataleitura4, 'MM') = '$Mes' And ativo = 1 And leitura4 != 0");
                $rowC = pg_num_rows($rsC);
                if($rowC > 0){
                    $tblC = pg_fetch_row($rsC);
                    $Injet = $tblC[0];
                }else{
                    $Injet = 0;
                }

                $Eletric2Mes = buscaEletric2Mes($Conec, $xProj, $Ano, $Mes, $DiaMedia);
                $Eletric2MesAnt = buscaEletric2Mes($Conec, $xProj, $Ano, $MesAnt, $DiaMedia);
                $Eletric3Mes = buscaEletric3Mes($Conec, $xProj, $Ano, $Mes, $DiaMedia);
                $Eletric3MesAnt = buscaEletric3Mes($Conec, $xProj, $Ano, $MesAnt, $DiaMedia);

                if($LeitMesAtual == 0 || $LeitMesAnt == 0){ // ainda não chegou o dia
                    if($LeitMesAtual == 0){
                        $ConsCalc = "Agd dia ".$DiaMedia."/".$Mes;
                        $Fatura = "Agd dia ".$DiaMedia."/".$Mes;
                    }
                    if($LeitMesAnt == 0){
                        $ConsCalc = "Agd dia ".$DiaMedia."/".$MesAnt;
                        $Fatura = "Agd dia ".$DiaMedia."/".$MesAnt;    
                    }
                }else{
                    $ConsCalc = number_format((($LeitMesAtual - $LeitMesAnt)*$FatorCor), 0, ",",".")." kWh";
                    $Fatura = "R$ ".number_format((($LeitMesAtual - $LeitMesAnt)*$FatorCor-$Injet)*$CalcValorKwh, 2, ",",".");
                }
                ?>
                <div style="border: 1px solid; border-radius: 10px;">
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
                            <td style="border-bottom: 1px solid gray; font-size: 90%;">Consumo Mensal (leitura) <?php if($Mes == $MesAtual){if($QuantDias == 1){echo " (1 dia)";}else{echo "(".$QuantDias." dias)";}} ?></td>
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 90%;"><?php echo number_format(($ConsMensal), 0, ",","."); ?> kWh</td>
                        </tr>

                        <tr>
                            <td style="border-bottom: 1px solid gray; font-size: 90%;">Consumo Médio Diário <?php if($QuantDias == 1){echo " (1 dia)";}else{echo "(".$QuantDias." dias)";} ?></td>
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 90%;"><?php echo number_format(($Cons1/$QuantDias), 0, ",","."); ?> kWh</td>
                        </tr>
                        
                        <tr>
                            <td style="border-bottom: 1px solid gray; font-size: 90%;" title="Fator aplicado:  <?php echo $FatorCor; ?>">Consumo Mensal Corrigido <?php echo "(Fator: ".$FatorCor.")"; ?></td>
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 90%;"><?php echo number_format($Cons2, 0, ",","."); ?> kWh</td> 
                        </tr>

                        <tr>
                            <td style="border-bottom: 1px solid gray; font-size: 90%;" title="Aplicado fator de correção: <?php echo $FatorCor; ?>">Consumo Calculado 
                                <?php 
                                echo "(".$DiaMedia."/".$MesAnt." a ".$DiaMedia."/".$Mes.")";
                                if($LeitMesAtual > 0 && $LeitMesAnt > 0){
                                    echo "&rarr; (".$LeitMesAtual." - ".$LeitMesAnt." = ".($LeitMesAtual - $LeitMesAnt)." x ".$FatorCor.")";
                                }
                                ?> 
                            </td>
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 90%;"><?php echo $ConsCalc; ?> </td>
                        </tr>

                        <tr>
                            <td style="border-bottom: 1px solid gray; font-size: 90%;" title="Fator aplicado:  <?php echo $FatorCor; ?>">Energia Ativa Injetada </td>
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 90%;"><?php if($Injet == 0){echo "(Agd Info Mês ".$Mes.")";}else{echo number_format($Injet, 0, ",",".")." kWh";} ?></td> 
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid gray; font-size: 90%;" title="Fator aplicado:  <?php echo $FatorCor; ?>">Fatura: 
                                <?php 
                                if($LeitMesAtual > 0 && $LeitMesAnt > 0){ 
                                    echo number_format((($LeitMesAtual - $LeitMesAnt)*$FatorCor), 0, ",",".")." kWh - ".number_format($Injet, 0, ",",".")." kWh x R$ ".number_format($CalcValorKwh, 4, ",","."); 
                                }
                                ?>
                            </td>
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 90%;"><?php echo $Fatura; ?></td> 
                        </tr>

            <!-- Leitura das antenas -->
                        <tr>
                            <td style="border-bottom: 1px solid gray; font-size: 90%; color: #9C9C9C;">Consumo <?php echo $Menu2; ?> 
                                <?php 
                                    echo "(".$DiaMedia."/".$MesAnt." a ".$DiaMedia."/".$Mes.")";
                                    if($Eletric2Mes > 0 && $Eletric2MesAnt > 0){
//                                        echo "&rarr; (".$Eletric2Mes." - ".$Eletric2MesAnt.")";
                                        echo " &rarr; ".number_format(($Eletric2Mes - $Eletric2MesAnt), 0, ",",".")." kWh";
                                    }
                                ?>
                            </td>
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 90%; color: #9C9C9C;">
                                <?php 
                                if($Eletric2Mes > 0 && $Eletric2MesAnt > 0){
//                                    echo number_format(($Eletric2Mes - $Eletric2MesAnt), 0, ",",".")." kWh";
                                    $Total1 = "R$ ".number_format(($Eletric2Mes - $Eletric2MesAnt)*$CalcValorKwh, 2, ",",".");
                                    echo $Total1;

                                }else{
                                    if($Eletric2Mes == 0){
                                        echo "Agd dia ".$DiaMedia."/".$Mes;
                                    }else{
                                        if($Eletric2MesAnt == 0){
                                            echo "Agd dia ".$DiaMedia."/".$MesAnt;
                                        }
                                    }
                                }; 
                                ?> 
                            </td> 
                        </tr>

                        <tr>
                            <td style="border-bottom: 1px solid gray; font-size: 90%; color: #9C9C9C;">Consumo <?php echo $Menu3; ?> 
                                <?php 
                                    echo "(".$DiaMedia."/".$MesAnt." a ".$DiaMedia."/".$Mes.")";
                                    if($Eletric3Mes > 0 && $Eletric3MesAnt > 0){
//                                        echo " &rarr; ".number_format(($Eletric3Mes - $Eletric3MesAnt), 0, ",",".")." kWh";
                                        $Total1 = "R$ ".number_format(($Eletric3Mes - $Eletric3MesAnt)*$CalcValorKwh, 2, ",",".");
                                        echo $Total1;
                                    }
                                ?>
                            </td>
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 90%; color: #9C9C9C;">
                                <?php 
                                if($Eletric3Mes > 0 && $Eletric3MesAnt > 0){
                                    echo number_format(($Eletric3Mes - $Eletric3MesAnt), 0, ",",".")." kWh";
                                }else{
                                    if($Eletric3Mes == 0){
                                        echo "Agd dia ".$DiaMedia."/".$Mes." ";
                                    }else{
                                        if($Eletric3MesAnt == 0){
                                            echo "Agd dia ".$DiaMedia."/".$MesAnt;
                                        }
                                    }
                                }; 
                                ?> 
                            </td> 
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


        //Calcula o consumo das operadoras das antenas
        function buscaEletric2Mes($Conec, $xProj, $Ano, $Mes, $DiaMedia){
            $rs = pg_query($Conec, "SELECT leitura2 FROM ".$xProj.".leitura_eletric 
            WHERE DATE_PART('YEAR', dataleitura2) = '$Ano' And TO_CHAR(dataleitura2, 'MM') = '$Mes' And TO_CHAR(dataleitura2, 'DD') = '$DiaMedia' And ativo = 1  And leitura2 != 0");
            $row = pg_num_rows($rs);
            if($row > 0){
                $tbl = pg_fetch_row($rs);
                $Leitura = $tbl[0];
            }else{
                $Leitura = 0;
            }
            return $Leitura;
        }

        function buscaEletric2MesAnt($Conec, $xProj, $Ano, $MesAnt, $DiaMedia){
            $rs = pg_query($Conec, "SELECT leitura2 FROM ".$xProj.".leitura_eletric 
            WHERE DATE_PART('YEAR', dataleitura2) = '$Ano' And TO_CHAR(dataleitura2, 'MM') = '$MesAnt' And TO_CHAR(dataleitura2, 'DD') = '$DiaMedia' And ativo = 1  And leitura2 != 0");
            $row = pg_num_rows($rs);
            if($row > 0){
                $tbl = pg_fetch_row($rs);
                $Leitura = $tbl[0];
            }else{
                $Leitura = 0;
            }
            return $Leitura;
        }

        function buscaEletric3Mes($Conec, $xProj, $Ano, $Mes, $DiaMedia){
            $rs = pg_query($Conec, "SELECT leitura3 FROM ".$xProj.".leitura_eletric 
            WHERE DATE_PART('YEAR', dataleitura3) = '$Ano' And TO_CHAR(dataleitura3, 'MM') = '$Mes' And TO_CHAR(dataleitura3, 'DD') = '$DiaMedia' And ativo = 1  And leitura3 != 0");
            $row = pg_num_rows($rs);
            if($row > 0){
                $tbl = pg_fetch_row($rs);
                $Leitura = $tbl[0];
            }else{
                $Leitura = 0;
            }
            return $Leitura;
        }

        function buscaEletric3MesAnt($Conec, $xProj, $Ano, $MesAnt, $DiaMedia){
            $rs = pg_query($Conec, "SELECT leitura3 FROM ".$xProj.".leitura_eletric 
            WHERE DATE_PART('YEAR', dataleitura3) = '$Ano' And TO_CHAR(dataleitura3, 'MM') = '$MesAnt' And TO_CHAR(dataleitura3, 'DD') = '$DiaMedia' And ativo = 1  And leitura3 != 0");
            $row = pg_num_rows($rs);
            if($row > 0){
                $tbl = pg_fetch_row($rs);
                $Leitura = $tbl[0];
            }else{
                $Leitura = 0;
            }
            return $Leitura;
        }

        ?>
    </body>
</html>