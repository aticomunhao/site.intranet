<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
//Carregado em carTema.php
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
        <div style="text-align: center;"><label class="titRelat corPreta">Controle do Consumo de Água<label></div>
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
        $rs = pg_query($Conec, "SELECT valoriniagua, TO_CHAR(datainiagua, 'YYYY/MM/DD') FROM ".$xProj.".paramsis WHERE idpar = 1 ");
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

        //Definir valores máximos para o eixo y do gráfico
        $rsY = pg_query($Conec, "SELECT MAX(consdiario) FROM ".$xProj.".leitura_agua WHERE ativo = 1 And consdiario < 1000");
        $tblY = pg_fetch_row($rsY);
        $MaxY = number_format($tblY[0], 0, ",",".");

        $Condic = "dataleitura IS NOT NULL And leitura1 != 0 And ativo = 1";
        if(isset($_REQUEST["mesano"])){
            $MesAno = addslashes(filter_input(INPUT_GET, 'mesano')); 
            if($MesAno != ""){
                $Proc = explode("/", $MesAno);
                $Mes = $Proc[0];
                if(strLen($Mes) < 2){
                    $Mes = "0".$Mes;
                }
                $Ano = $Proc[1];
                $Condic = "dataleitura IS NOT NULL And leitura1 != 0 And ativo = 1 And DATE_PART('MONTH', dataleitura) = '$Mes' And DATE_PART('YEAR', dataleitura) = '$Ano'";
            }else{
                $Condic = "dataleitura IS NOT NULL And leitura1 != 0 And ativo = 1";
            }
        }

        if(isset($_REQUEST["ano"])){
            $Ano = filter_input(INPUT_GET, 'ano'); 
            if($Ano != ""){
                $Condic = "dataleitura IS NOT NULL And leitura1 != 0 And ativo = 1 And DATE_PART('YEAR', dataleitura) = $Ano";
            }else{
                $Condic = "dataleitura IS NOT NULL And leitura1 != 0 And ativo = 1";
            }
        }else{
            $Ano = date('Y');
        }

        //para o gráfico
        if(isset($_REQUEST["corTema"])){ // vem de carTema.php
            $VarCor = filter_input(INPUT_GET, 'corTema');
            $Cor = "#".$VarCor; 
        }else{
            $Cor = "#FFFAFA";
        }


        $rs1 = pg_query($Conec, "SELECT DATE_PART('YEAR', dataleitura), DATE_PART('MONTH', dataleitura), COUNT(id), SUM(leitura1), SUM(leitura2), SUM(leitura3) 
        FROM ".$xProj.".leitura_agua 
        WHERE $Condic 
        GROUP BY DATE_PART('YEAR', dataleitura), DATE_PART('MONTH', dataleitura) ORDER BY DATE_PART('YEAR', dataleitura) DESC, DATE_PART('MONTH', dataleitura) DESC ");
        $row1 = pg_num_rows($rs1);
        if($row1 > 0){
            while($tbl1 = pg_fetch_row($rs1) ){
                $Ano = $tbl1[0];
                $Mes = $tbl1[1];
                $Ident = $Ano.$Mes; // para o nome da DIV: graficoMensal.$Ident
                $QuantDias = $tbl1[2];
                $Cons1 = 0;
                $Cons2 = 0;
                $Cons3 = 0;
                $MediaDiaria = 0;
                $SomaLeit1 = 0;
                $SomaLeit2 = 0;
                $SomaLeit3 = 0;
                $SomaLeitAnt = 0;
//echo $Mes."/".$Ano." ";
                $rs2 = pg_query($Conec, "SELECT dataleitura, leitura1, leitura2, leitura3 FROM ".$xProj.".leitura_agua 
                WHERE DATE_PART('YEAR', dataleitura) = $Ano And DATE_PART('MONTH', dataleitura) = $Mes And ativo = 1 And leitura1 != 0 And leitura2 != 0 And leitura3 != 0 ");
                $row2 = pg_num_rows($rs2);
                if($row2 > 0){
                    while($tbl2 = pg_fetch_row($rs2) ){
                        $DataLinha = $tbl2[0]; // dataleitura
                        $SomaLeit1 = $SomaLeit1+$tbl2[1];
                        $SomaLeit2 = $SomaLeit2+$tbl2[2];
                        $SomaLeit3 = $SomaLeit3+$tbl2[3];

                        if(strtotime($DataLinha) == strtotime($DataIni)){ // "2024-03-01"
                            $SomaLeitAnt = ($SomaLeitAnt+$ValorIni);  //1696.485
                        }

                        if($DataLinha != $DataIni){
                            $rs3 = pg_query($Conec, "SELECT leitura3 FROM ".$xProj.".leitura_agua 
                            WHERE dataleitura = (date '$DataLinha' - 1) And ativo = 1 And leitura1 != 0 And leitura2 != 0 And leitura3 != 0");
                            $tbl3 = pg_fetch_row($rs3);
                            $row3 = pg_num_rows($rs3);
                            if($row3 > 0){
                                $SomaLeitAnt = ($SomaLeitAnt+$tbl3[0]);
                            }
                        }
                        $Cons1 = ($SomaLeit1-$SomaLeitAnt);
                        $Cons2 = ($SomaLeit2-$SomaLeit1);
                        $Cons3 = ($SomaLeit3-$SomaLeit2);
                        $MediaDiaria = ($Cons1+$Cons2+$Cons3)/$QuantDias;
                    }
                }
                $rs4 = pg_query($Conec, "SELECT SUM(consdiario) FROM ".$xProj.".leitura_agua WHERE DATE_PART('MONTH', dataleitura) = $Mes And DATE_PART('YEAR', dataleitura) = $Ano And ativo = 1 And leitura1 != 0 And leitura2 != 0 And leitura3 != 0 ");
                $tbl4 = pg_fetch_row($rs4);
                $ConsMensal = $tbl4[0];
                ?>
                <div style="border: 1px solid; border-radius: 10px">
                    <table style="margin: 0 auto; width: 95%;">
                        <tr>
                            <td style="font-size: 120%; font-weight: bold;"><?php echo $mes_extenso[$Mes]." ".$Ano; ?></td>
                            <td colspan="3" style="text-align: center;">Períodos</td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid gray; font-size: 80%; font-weight: bold;">Consumo Mensal: <?php echo number_format(($ConsMensal), 3, ",","."); ?> m<label style="vertical-align: 0.4em; font-size: 70%;">3</label></td>
                            <td style="border-bottom: 1px solid gray; font-size: 80%; text-align: center;">00h00 / 07h30</td>
                            <td style="border-bottom: 1px solid gray; font-size: 80%; text-align: center;">07h30 / 16h30</td>
                            <td style="border-bottom: 1px solid gray; font-size: 80%; text-align: center;">16h30 / 24h00</td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid gray; font-size: 90%;">Consumo Mensal por Período</td>
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 90%;"><?php echo number_format($Cons1, 3, ",","."); ?> m<label style="vertical-align: 0.4em; font-size: 70%;">3</label></td>
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 90%;"><?php echo number_format($Cons2, 3, ",","."); ?> m<label style="vertical-align: 0.4em; font-size: 70%;">3</label></td>
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 90%;"><?php echo number_format($Cons3, 3, ",","."); ?> m<label style="vertical-align: 0.4em; font-size: 70%;">3</label></td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid gray; font-size: 90%;">Consumo Mensal Diário por Período</td>
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 90%;"><?php echo number_format(($Cons1/$QuantDias), 3, ",","."); ?> m<label style="vertical-align: 0.4em; font-size: 70%;">3</label></td>
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 90%;"><?php echo number_format(($Cons2/$QuantDias), 3, ",","."); ?> m<label style="vertical-align: 0.4em; font-size: 70%;">3</label></td>
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 90%;"><?php echo number_format(($Cons3/$QuantDias), 3, ",","."); ?> m<label style="vertical-align: 0.4em; font-size: 70%;">3</label></td>
                        </tr>
                        <tr>
                            <td colspan="3" style="font-size: 90%; text-align: right;">Consumo Médio Diário: <?php if($QuantDias == 1){echo " (1 dia)";}else{echo "(".$QuantDias." dias)";} ?></td>
                            <td style="text-align: center; color: red;"><?php echo number_format($MediaDiaria, 3, ",","."); ?> m<label style="vertical-align: 0.4em; font-size: 70%;">3</label></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="padding-bottom: 2px;"></td>
                        </tr>
                    </table>
                </div>
                <div id="graficoMensal<?php echo $Ident; ?>" style="width:100%; height: 300px;"></div>
                <br>

                <?php
                    //Preenche arrays para o gráfico
                    $rs0 = pg_query($Conec, "SELECT id, TO_CHAR(dataleitura, 'DD') as Dia, consdiario As Leitura 
                    FROM ".$xProj.".leitura_agua WHERE ativo = 1 And DATE_PART('MONTH', dataleitura) = $Mes And DATE_PART('YEAR', dataleitura) = $Ano And leitura3 != 0 ORDER BY dataleitura ");
                    $row0 = pg_num_rows($rs0);
                    $datay = [];
                    $datax = [];
                    while($tbl0 = pg_fetch_row($rs0)){
                        array_push($datay, $tbl0[1]);
                        array_push($datax, $tbl0[2]);
                    }
                ?>

                <script>
                    xArray = <?php echo json_encode($datay); ?>;
                    yArray = <?php echo json_encode($datax); ?>;

                    // Define Data
                    data = [{
                        x: xArray,
                        y: yArray,
                        mode:"lines+markers"
                    }];

                    // Define Layout
                    layout = {
                        xaxis: {range: [1, 31], title: "Dia"},
                        yaxis: {range: [0, <?php echo $MaxY; ?>], title: "m3"},
                        title: "Consumo Diário - <?php echo $mes_extenso[$Mes]."/".$Ano; ?>",
                        paper_bgcolor: '<?php echo  $Cor; ?>', // ok fundo preto/branco fora do gráfico
                        plot_bgcolor: "<?php echo  $Cor; ?>"   // ok fundo preto/branco dentro do gráfico
                      };

                    // Display using Plotly
                    Plotly.newPlot("graficoMensal<?php echo $Ident; ?>", data, layout, {displayModeBar: false});
                </script>

                <?php
            }
        }
        ?>
    </body>
</html>
