<?php
session_start(); 
if(!isset($_SESSION["usuarioID"])){
    session_destroy();
    header("Location: ../../index.php");
}

require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
date_default_timezone_set('America/Sao_Paulo'); 
//numeração do dia da semana da função extract() (DOW) é diferente da função to_char() (D)
//Função para Extract no postgres
$Semana_Extract = array(
    '0' => 'D',
    '1' => '2ª',
    '2' => '3ª',
    '3' => '4ª',
    '4' => '5ª',
    '5' => '6ª',
    '6' => 'S',
    'xª'=> ''
);

 
    $Mes = date("m");
    $Ano = date("Y");

    if(isset($_REQUEST["mesano"])){
        $Busca = addslashes(filter_input(INPUT_GET, 'mesano'));
        $Proc = explode("/", $Busca);
        $Mes = $Proc[0];
        if(strLen($Mes) < 2){
            $Mes = "0".$Mes;
        }
        $Ano = $Proc[1];
        $Data = date('01/'.$Mes.'/'.$Ano);
    }else{
        $rs = pg_query($Conec, "SELECT MIN(dataescala) FROM ".$xProj.".escala_daf WHERE ativo = 1 ");
        $tbl = pg_fetch_row($rs);
        $MaxData = $tbl[0];
        $Proc = explode("-", $MaxData);
        $Ano = $Proc[0];
        $Mes = $Proc[1];
        $Mes = ($Mes - 1);
        $Data = date('01/'.$Mes.'/'.$Ano);
    }

    $NumGrupo = filter_input(INPUT_GET, 'numgrupo');
    echo "Mês: ".$Mes.'/'.$Ano;

    echo "<br><br>";
        $rs2 = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE esc_eft = 1 And ativo = 1 And esc_grupo = $NumGrupo ORDER BY nomeusual, nomecompl ");
        $row2 = pg_num_rows($rs2);
        if($row2 > 0){
            echo "<table>";
                echo "<tr>";
                    echo "<td>";
                        echo "<div style='width: 150px;'> &nbsp; </div>";
                        $rs = pg_query($Conec, "SELECT id, TO_CHAR(dataescala, 'DD'), date_part('dow', dataescala) FROM ".$xProj.".escala_daf WHERE ativo = 1 And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                        $row = pg_num_rows($rs);
                        if($row > 0){
                            while($tbl = pg_fetch_row($rs)){
                                echo "<td>";
                                    echo "<div class='quadrodia'> $tbl[1]<br> ";
                                    echo $Semana_Extract[$tbl[2]];
                                    echo " </div>";
                                echo "</td>";
                            }
                        } 
                    echo "</td>";

                    while($tbl2 = pg_fetch_row($rs2)){
                        echo "<tr>";
                            echo "<td>";
                                echo "<div class='quadrodia' style='width: 150px; text-align: left; padding-left: 3px;'> $tbl2[2] </div>";
                                $rs = pg_query($Conec, "SELECT id, TO_CHAR(dataescala, 'DD'), date_part('dow', dataescala) FROM ".$xProj.".escala_daf WHERE ativo = 1 And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
                                $row = pg_num_rows($rs);
                                if($row > 0){
                                    while($tbl = pg_fetch_row($rs)){
                                        echo "<td>";
                                            echo "<div class='quadrodia' onclick='abreEdit($tbl2[0], $tbl[0])'> &nbsp; </div>";
                                        echo "</td>";
                                    }
                                } 
                            echo "</td>";
                        echo "</tr>";
                    }
                echo "</tr>";
            echo "</table>";
        }  
?>