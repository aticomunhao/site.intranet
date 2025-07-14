<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
if(!isset($_SESSION["usuarioID"])){
    session_destroy();
    header("Location: ../../index.php");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="comp/js/plotly.min.js"></script>
        <title></title>
    </head>
    <body>
        <?php
        function round1stSignificant($N){ // tks to greghenle at gmail dot com
            if ( $N == 0 ) {
                return 0;
            }
            $x = floor ( log10 ( abs( $N ) ) );
              return ( $N > 0 )
            ? ceil( $N * pow ( 10, $x * -1 ) ) * pow( 10, $x ) : floor( $N * pow ( 10, $x * -1 ) ) * pow( 10, $x );
        }

        $DiaAtual = date('d');
        $MesAtual = date('m');
        $AnoAtual = date('Y');

        //Calcular quantos dias do mês atual estão preenchidos
        $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".leitura_eletric 
        WHERE colec = 3 And dataleitura3 IS NOT NULL And leitura3 != 0 And ativo = 1 And DATE_PART('MONTH', dataleitura3) = $MesAtual And DATE_PART('YEAR', dataleitura3) = $AnoAtual");
        $DiasMesAtual = pg_num_rows($rs);

        $MaxY = 100;
        $datay1 = [];
        $datax1 = [];
        $row1 = 0;
        $rs1 = pg_query($Conec, "SELECT DATE_PART('MONTH', dataleitura3), DATE_PART('YEAR', dataleitura3), SUM(consdiario3) 
        FROM ".$xProj.".leitura_eletric 
        WHERE colec = 3 And dataleitura3 IS NOT NULL And leitura3 != 0 And ativo = 1 And DATE_PART('YEAR', dataleitura3) = $AnoAtual 
        GROUP BY DATE_PART('YEAR', dataleitura3), DATE_PART('MONTH', dataleitura3) ORDER BY DATE_PART('YEAR', dataleitura3), DATE_PART('MONTH', dataleitura3)");
        $row1 = pg_num_rows($rs1);
        if($row1 > 0){
            while($tbl1 = pg_fetch_row($rs1) ){
                array_push($datax1, $tbl1[0]);
                if($tbl1[0] == $MesAtual && $tbl1[1] == $AnoAtual){
                    $Media = (($tbl1[2]/$DiasMesAtual)*30); // projeção para consumo do mês em curso
                    array_push($datay1, $Media);    
                }else{
                    array_push($datay1, $tbl1[2]);
                }
                if($tbl1[2] > $MaxY){
                    $MaxY = $tbl1[2]; // Maior valor para o gráfico
                }
            }
        }

        $datay2 = [];
        $datax2 = [];
        $row2 = 0;
        $rs2 = pg_query($Conec, "SELECT DATE_PART('MONTH', dataleitura3), DATE_PART('YEAR', dataleitura3), SUM(consdiario3) 
        FROM ".$xProj.".leitura_eletric 
        WHERE colec = 3 And dataleitura3 IS NOT NULL And leitura3 != 0 And ativo = 1 And DATE_PART('YEAR', dataleitura3) = ($AnoAtual-1) 
        GROUP BY DATE_PART('YEAR', dataleitura3), DATE_PART('MONTH', dataleitura3) ORDER BY DATE_PART('YEAR', dataleitura3), DATE_PART('MONTH', dataleitura3)");
        $row2 = pg_num_rows($rs2);
        if($row2 > 0){
            while($tbl2 = pg_fetch_row($rs2) ){
                array_push($datax2, $tbl2[0]);
                if($tbl2[0] == $MesAtual && $tbl2[1] == $AnoAtual){
                    $Media = (($tbl2[2]/$DiasMesAtual)*30); // projeção para consumo do mês em curso
                    array_push($datay2, $Media);    
                }else{
                    array_push($datay2, $tbl2[2]);
                }
                if($tbl2[2] > $MaxY){
                    $MaxY = $tbl2[2]; // Maior valor para o gráfico
                }
            }
        }

        $datay3 = [];
        $datax3 = [];
        $row3 = 0;
        $rs3 = pg_query($Conec, "SELECT DATE_PART('MONTH', dataleitura3), DATE_PART('YEAR', dataleitura3), SUM(consdiario3) 
        FROM ".$xProj.".leitura_eletric 
        WHERE colec = 3 And dataleitura3 IS NOT NULL And leitura3 != 0 And ativo = 1 And DATE_PART('YEAR', dataleitura3) = ($AnoAtual-2) 
        GROUP BY DATE_PART('YEAR', dataleitura3), DATE_PART('MONTH', dataleitura3) ORDER BY DATE_PART('YEAR', dataleitura3), DATE_PART('MONTH', dataleitura3)");
        $row3 = pg_num_rows($rs3);
        if($row3 > 0){
            while($tbl3 = pg_fetch_row($rs3) ){
                array_push($datax3, $tbl3[0]);
                if($tbl3[0] == $MesAtual && $tbl3[1] == $AnoAtual){
                    $Media = (($tbl3[2]/$DiasMesAtual)*30); // projeção para consumo do mês em curso
                    array_push($datay3, $Media);    
                }else{
                    array_push($datay3, $tbl3[2]);
                }
                if($tbl3[2] > $MaxY){
                    $MaxY = $tbl3[2]; // Maior valor para o gráfico
                }
            }
        }
        
        $datay4 = [];
        $datax4 = [];
        $row4 = 0;
        $rs4 = pg_query($Conec, "SELECT DATE_PART('MONTH', dataleitura3), DATE_PART('YEAR', dataleitura3), SUM(consdiario3) 
        FROM ".$xProj.".leitura_eletric 
        WHERE colec = 3 And dataleitura3 IS NOT NULL And leitura3 != 0 And ativo = 1 And DATE_PART('YEAR', dataleitura3) = ($AnoAtual-3) 
        GROUP BY DATE_PART('YEAR', dataleitura3), DATE_PART('MONTH', dataleitura3) ORDER BY DATE_PART('YEAR', dataleitura3), DATE_PART('MONTH', dataleitura3)");
        $row4 = pg_num_rows($rs4);
        if($row4 > 0){
            while($tbl4 = pg_fetch_row($rs4) ){
                array_push($datax4, $tbl4[0]);
                if($tbl4[0] == $MesAtual && $tbl4[1] == $AnoAtual){
                    $Media = (($tbl4[2]/$DiasMesAtual)*30); // projeção para consumo do mês em curso
                    array_push($datay4, $Media);    
                }else{
                    array_push($datay4, $tbl4[2]);
                }
                if($tbl4[2] > $MaxY){
                    $MaxY = $tbl4[2]; // Maior valor para o gráfico
                }
            }
        }

        $datay5 = [];
        $datax5 = [];
        $row5 = 0;
        $rs5 = pg_query($Conec, "SELECT DATE_PART('MONTH', dataleitura3), DATE_PART('YEAR', dataleitura3), SUM(consdiario3) 
        FROM ".$xProj.".leitura_eletric 
        WHERE colec = 3 And dataleitura3 IS NOT NULL And leitura3 != 0 And ativo = 1 And DATE_PART('YEAR', dataleitura3) = ($AnoAtual-4) 
        GROUP BY DATE_PART('YEAR', dataleitura3), DATE_PART('MONTH', dataleitura3) ORDER BY DATE_PART('YEAR', dataleitura3), DATE_PART('MONTH', dataleitura3)");
        $row5 = pg_num_rows($rs5);
        if($row5 > 0){
            while($tbl5 = pg_fetch_row($rs5) ){
                array_push($datax5, $tbl5[0]);
                if($tbl5[0] == $MesAtual && $tbl5[1] == $AnoAtual){
                    $Media = (($tbl5[2]/$DiasMesAtual)*30); // projeção para consumo do mês em curso
                    array_push($datay5, $Media);    
                }else{
                    array_push($datay5, $tbl5[2]);
                }
                if($tbl5[2] > $MaxY){
                    $MaxY = $tbl5[2]; // Maior valor para o gráfico
                }
            }
        }
        ?>

        <div id="graficoAnual" style="width:100%; height: 500px;"></div>

        <script>
            xArray1 = <?php echo json_encode($datax1); ?>;
            yArray1 = <?php echo json_encode($datay1); ?>;

            xArray2 = <?php echo json_encode($datax2); ?>;
            yArray2 = <?php echo json_encode($datay2); ?>;

            xArray3 = <?php echo json_encode($datax3); ?>;
            yArray3 = <?php echo json_encode($datay3); ?>;

            xArray4 = <?php echo json_encode($datax4); ?>;
            yArray4 = <?php echo json_encode($datay4); ?>;

            xArray5 = <?php echo json_encode($datax5); ?>;
            yArray5 = <?php echo json_encode($datay5); ?>;

            // Define Data
            data = [
                <?php 
                if($row1 > 0){
                    echo "{x: xArray1, y: yArray1, mode:'lines+markers', name: $AnoAtual,},";
                }
                if($row2 > 0){
                    echo "{x: xArray2, y: yArray2, mode:'lines+markers', name: ($AnoAtual-1),},";
                }
                if($row3 > 0){
                    echo "{x: xArray3, y: yArray3, mode:'lines+markers', name: ($AnoAtual-2),},";
                }
                if($row4 > 0){
                    echo "{x: xArray4, y: yArray4, mode:'lines+markers', name: ($AnoAtual-3),},";
                }
                if($row5 > 0){
                    echo "{x: xArray5, y: yArray5, mode:'lines+markers', name: ($AnoAtual-4),}";
                }
                ?>
            ];

            // Define Layout
            layout = {
                title: {
                    text: 'Demostrativo dos últimos 5 anos'
                },
                xaxix: xArray1, 
                yaxis: {range: [0, <?php echo round1stSignificant($MaxY); ?>], title: "kWh"},
                xaxis: {
                    autorange: true,
                    showgrid: false,
                    zeroline: false,
                    showline: false
                }
            };
            // Display using Plotly
            Plotly.newPlot("graficoAnual", data, layout, {displayModeBar: false});
        </script>
    </body>
</html>