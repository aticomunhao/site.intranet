<?php
    date_default_timezone_set('America/Sao_Paulo');
    $date = date('Y-m-d');
    $mdate = date('m');
    $ddate = date('d');
    $xProj = "cesb";
    $ProxMes = ($mdate+1);
    if($ProxMes < 10){
        $ProxMes = "0".$ProxMes;
    }

    require_once("config/abrealas.php");
    if(!$Conec){
        echo "Sem contato com o PostGresql";
    }
    if(!$ConecPes){
        echo "Sem contato com o PostGresql - Pes";
    }

    $rs = pg_query($Conec, "SELECT * FROM information_schema.tables WHERE table_schema = 'cesb';");
    $row = pg_num_rows($rs);
    if($row == 0){
        die("<br>Faltam tabelas. Informe à ATI");
        return false;
    }

    //usando a data de nascimento na tabela pessoas
    function pegaAniver($param, $mdate, $ddate, $ConecPes, $xPes, $ProxMes) {
        $rs0 = pg_query($ConecPes, "SELECT nome_completo, nome_completo, TO_CHAR(dt_nascimento, 'DD'), TO_CHAR(dt_nascimento, 'MM') 
        FROM ".$xPes.".pessoas 
        WHERE TO_CHAR(dt_nascimento, 'MM') = '$mdate' And TO_CHAR(dt_nascimento, 'DD') $param '$ddate' Or TO_CHAR(dt_nascimento, 'MM') = '$ProxMes' 
        ORDER BY TO_CHAR(dt_nascimento, 'MM'), TO_CHAR(dt_nascimento, 'DD')");
        return $rs0;
    }
    $NiverHoje = 0;
    echo "<div style='text-align: center;'>";
        echo "<table style='margin: 0 auto;'>";
            echo "<tr>";
                echo "<td><td>";
                echo "<td><td>";
            echo "</tr>";
            //aniversariantes de hoje
            $aniver = pegaAniver('=', $mdate, $ddate, $ConecPes, $xPes, 0);
                if($aniver){
                    $row = pg_num_rows($aniver);
                    if($row >0){
                        while($tbl = pg_fetch_row($aniver)){
                            echo "<tr>";
                            echo "<td style='color: red; text-align: right; padding-right: 10px; font-size: 80%;'>";
                            echo $tbl[2]."/". $tbl[3];
                            echo "</td>";
                            echo "<td style='color: red; text-align: left; padding-left: 5px; font-size: 80%;'>";
                                echo "<div style='border: 1px solid #FF5580; border-radius: 5px; padding-left: 3px; padding-right: 3px;'>"."<b>" . $tbl[1] . "</b>"."</div>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        $NiverHoje = 1; // tem hoje
                    }else{
                        echo "<tr>";
                        echo "<td colspan='2' style='color: red; text-align: center; font-weight: normal; font-size: 80%;'>Nenhum aniversariante hoje</td>";
                        echo "</tr>";
                    }
                }

            //aniversariantes do mês seguinte:
            $aniver = pegaAniver('>', $mdate, $ddate, $ConecPes, $xPes, $ProxMes);
                if($aniver){
                    $row = pg_num_rows($aniver);
                    if($row >0){
                        while($tbl = pg_fetch_row($aniver)){
                            echo "<tr>";
                            echo "<td style='color: blue; text-align: right; padding-right: 10px; font-size: 80%;'>";
                                echo $tbl[2]."/". $tbl[3];
                            echo "</td>";
                            echo "<td style='color: blue; text-align: left; padding-left: 5px; font-size: 80%;'>";
                                echo "<div style='border: 1px solid #5C88C4; border-radius: 5px; padding-left: 3px; padding-right: 3px;'>"."<b>" . $tbl[1] . "</b>"."</div>";
                            echo "</td>";
                        }
                    }else{
                        if($NiverHoje == 0){ // se também não tem aniver hoje
                            echo "<tr>";
                            echo "<td colspan='2' style='color: red; text-align: center; font-weight: normal;'>Nenhum aniversariante neste mês</td>";
                            echo "</tr>";
                        }
                    }
                }
        echo "</table>";
    echo "</div>";