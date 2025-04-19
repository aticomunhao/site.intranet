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

//https://plotly.com/javascript/bar-charts/


        $DiaAtual = date('d');
        $MesAtual = date('m');
        $AnoAtual = date('Y');

        $MaxY = 30;
        $datay1 = [];
        $datax1 = [];
        $row1 = 0;
        $rs1 = pg_query($Conec, "SELECT nomeusual, numacessos 
        FROM ".$xProj.".poslog 
        WHERE ativo = 1 And DATE_PART('YEAR', logini) = $AnoAtual ORDER BY nomeusual");

        $row1 = pg_num_rows($rs1);
        if($row1 > 0){
            while($tbl1 = pg_fetch_row($rs1) ){
                array_push($datax1, $tbl1[0]);
                array_push($datay1, $tbl1[1]);
                if($tbl1[1] > $MaxY){
                    $MaxY = number_format($tbl1[1], 0, ",","."); // Maior valor para o gráfico
                }
            }
        }
        ?>

        <div id="graficoA" style="width:100%; height: 500px;"></div>

        <script>
            xArray1 = <?php echo json_encode($datax1); ?>;
            yArray1 = <?php echo json_encode($datay1); ?>;
  
            // Define Data
            data = [
                <?php 
                if($row1 > 0){
                    echo "{x: xArray1, y: yArray1, type: 'bar',},"; // echo "{x: xArray1, y: yArray1, mode:'lines+markers', name: $AnoAtual,},";
                }
                ?>
            ];

            // Define Layout
            layout = {
                title: {
                    text: ''
                },
                xaxis: {range: [1, 12], title: "Nome"},
                yaxis: {range: [0, <?php echo round1stSignificant($MaxY); ?>], title: "Número de Logins"},
                xaxis: {
                    autorange: true,
                    showgrid: false,
                    zeroline: false,
                    showline: false
                }
            };
            // Display using Plotly
            Plotly.newPlot("graficoA", data, layout, {displayModeBar: false});
        </script>
    </body>
</html>