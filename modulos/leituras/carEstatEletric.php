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
        $Menu1 = escMenu($Conec, $xProj, 1);
        ?>
        <div style="text-align: center;"><label class="titRelat">Controle do Consumo de Eletricidade<?php echo " - ".$Menu1; ?><label></div>
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
            '9' => 'Novembro',
            '10' => 'Setembro',
            '11' => 'Outubro',
            '12' => 'Dezembro'
        ); 
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

        $rs1 = pg_query($Conec, "SELECT DATE_PART('MONTH', dataleitura1), COUNT(id), SUM(leitura1) 
        FROM ".$xProj.".leitura_eletric 
        WHERE colec = 1 And dataleitura1 IS NOT NULL And leitura1 != 0 
        GROUP BY DATE_PART('MONTH', dataleitura1) ORDER BY DATE_PART('MONTH', dataleitura1) DESC ");

        $row1 = pg_num_rows($rs1);
        if($row1 > 0){
            while($tbl1 = pg_fetch_row($rs1) ){
                $Mes = $tbl1[0];
                $QuantDias = $tbl1[1];
                $Cons1 = 0;
                $MediaDiaria = 0;
                $SomaLeit1 = 0;
                $SomaLeitAnt = 0;

                $rs2 = pg_query($Conec, "SELECT dataleitura1, leitura1 FROM ".$xProj.".leitura_eletric WHERE DATE_PART('MONTH', dataleitura1) = $Mes And colec = 1 And ativo = 1 And leitura1 != 0 ");
                $row2 = pg_num_rows($rs2);
                if($row2 > 0){
                    while($tbl2 = pg_fetch_row($rs2) ){
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
                        $MediaDiaria = $Cons1/$QuantDias;
                    }
                }
                ?>
                <div style="border: 1px solid; border-radius: 10px">
                    <table style="margin: 0 auto; width: 95%;">
                        <tr>
                            <td style="font-size: 140%; font-weight: bold;"><?php echo $mes_extenso[$Mes]; ?></td>
                            <td colspan="3" style="text-align: center;"></td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid gray; font-size: 80%; font-weight: bold;"></td>
                            <td style="border-bottom: 1px solid gray; font-size: 80%; text-align: center;"></td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid gray; font-size: 90%;">Consumo Mensal</td>
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 90%;"><?php echo $Cons1; ?> kWh</td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid gray; font-size: 90%;">Consumo Médio Diário <?php if($QuantDias == 1){echo " (1 dia)";}else{echo "(".$QuantDias." dias)";} ?></td>
                            <td style="border-bottom: 1px solid gray; text-align: center; font-size: 90%;"><?php echo ($Cons1/$QuantDias); ?> kWh</td>
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