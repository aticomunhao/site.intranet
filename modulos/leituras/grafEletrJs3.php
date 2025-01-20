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
        WHERE colec = 3 And dataleitura3 IS NOT NULL And leitura3 != 0 And ativo = 1 And DATE_PART('MONTH', dataleitura3) = $MesAtual");
        $DiasMesAtual = pg_num_rows($rs);
//        echo "<label style='font-size: 80%; color: #036; font-style: italic;' title='Desconsidera os dias não anotados'>Projeção de consumo para o mês atual pela média de $DiasMesAtual dias anotados.</label>";

        $rs0 = pg_query($Conec, "SELECT DATE_PART('MONTH', dataleitura3), DATE_PART('YEAR', dataleitura3), SUM(consdiario3) 
        FROM ".$xProj.".leitura_eletric 
        WHERE colec = 3 And dataleitura3 IS NOT NULL And leitura3 != 0 And ativo = 1 
        GROUP BY DATE_PART('YEAR', dataleitura3), DATE_PART('MONTH', dataleitura3) ORDER BY DATE_PART('YEAR', dataleitura3), DATE_PART('MONTH', dataleitura3)");

        $row0 = pg_num_rows($rs0);
        if($row0 > 0){
            $MaxY = 0;
            $datay = [];
            $datax = [];
            while($tbl0 = pg_fetch_row($rs0) ){
                array_push($datay, $tbl0[0]."/".$tbl0[1]);
                if($tbl0[0] == $MesAtual && $tbl0[1] == $AnoAtual){
                    $Media = (($tbl0[2]/$DiasMesAtual)*30); // projeção para consumo do mês em curso
                    array_push($datax, $Media);    
                }else{
                    array_push($datax, $tbl0[2]);
                }
                if($tbl0[2] > $MaxY){
                    $MaxY = $tbl0[2];  //number_format($tbl0[2], 0, ",","."); // Maior valor para o gráfico
                }
            }
        }
        ?>
        <div id="graficoAnual" style="width:100%; height: 500px;"></div>

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
                title: {
                    text: 'Mês atual projetado pela média de <?php echo $DiasMesAtual; ?> dias anotados'
                },
                xaxis: {range: [1, 31], title: "Mês/Ano"},
                yaxis: {range: [0, <?php echo round1stSignificant($MaxY); ?>], title: "m3"},
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