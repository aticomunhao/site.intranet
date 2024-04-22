<?php
    date_default_timezone_set('America/Sao_Paulo');
    $date = date('Y-m-d H:i:s');
    $mdate = date('m');
    $ddate = date('d');
    $dias = 5; // total de dias a mostrar
    $xProj = "cesb";
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
    //usando a tabela anivers
    function pegaAniver___($param, $mdate, $ddate, $Conec, $xProj) {
        $rs0 = pg_query($Conec, "SELECT ".$xProj.".anivers.nomeUsu, ".$xProj.".anivers.nomeCompl, ".$xProj.".anivers.diaaniv, ".$xProj.".anivers.mesaniv FROM ".$xProj.".anivers 
        WHERE ".$xProj.".anivers.mesaniv = '$mdate' And ".$xProj.".anivers.diaaniv $param '$ddate' 
        ORDER BY ".$xProj.".anivers.mesaniv, ".$xProj.".anivers.diaaniv");
        return $rs0;
    }
    //usando a data de nascimento na tabela pessoas
    function pegaAniver($param, $mdate, $ddate, $ConecPes, $xPes) {
        $rs0 = pg_query($ConecPes, "SELECT nome_completo, nome_completo, TO_CHAR(dt_nascimento, 'DD'), TO_CHAR(dt_nascimento, 'MM') 
        FROM ".$xPes.".pessoas 
        WHERE TO_CHAR(dt_nascimento, 'MM') = '$mdate' And TO_CHAR(dt_nascimento, 'DD') $param '$ddate' 
        ORDER BY TO_CHAR(dt_nascimento, 'MM'), TO_CHAR(dt_nascimento, 'DD')");
        return $rs0;
    }

    echo "<div style='text-align: center;'>";
        echo "<table style='margin: 0 auto;'>";
            echo "<tr>";
                echo "<td><td>";
                echo "<td><td>";
            echo "</tr>";
            //aniversariantes de hoje
            $aniver = pegaAniver('=', $mdate, $ddate, $ConecPes, $xPes);
                if($aniver){
                    $row = pg_num_rows($aniver);
                    if($row >0){
                        while($tbl = pg_fetch_row($aniver)){
                            echo "<tr>";
                            echo "<td style='color: red; text-align: right; padding-right: 10px; font-size: 80%;'>";
                            echo $tbl[2]."/". $tbl[3];
                            echo "</td>";
                            echo "<td style='color: red; text-align: left; padding-left: 5px;'> font-size: 80%;";
                                echo "<b>" . $tbl[1] . "</b>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    }else{
                        echo "<tr>";
                        echo "<td colspan='2' style='color: red; text-align: center; font-weight: normal; font-size: 80%;'>Nenhum aniversariante hoje</td>";
                        echo "</tr>";
                    }
                }
                        
            //aniversariantes do $dias seguintes:
            $aniver = pegaAniver('>', $mdate, $ddate, $ConecPes, $xPes);
                if($aniver){
                    $row = pg_num_rows($aniver);
                    if($row >0){
                        while($tbl = pg_fetch_row($aniver)){
                            echo "<tr>";
                            echo "<td style='color: blue; text-align: right; padding-right: 10px;'>";
                                echo $tbl[2]."/". $tbl[3];
                            echo "</td>";
                            echo "<td style='color: blue; text-align: left; padding-left: 5px;'>";
                                echo "<b>" . $tbl[1] . "</b>";
                            echo "</td>";
                        }
                    }else{
                        echo "<tr>";
                        echo "<td colspan='2' style='color: red; text-align: center; font-weight: normal;'>Nenhum aniversariante neste mês</td>";
                        echo "</tr>";
                    }
                }
        echo "</table>";
    echo "</div>";