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
        $rs = pg_query($Conec, "SELECT MIN(dataescala) FROM ".$xProj.".escaladaf WHERE ativo = 1 ");
        $tbl = pg_fetch_row($rs);
        $MaxData = $tbl[0];
        $Proc = explode("-", $MaxData);
        $Ano = $Proc[0];
        $Mes = $Proc[1];
        $Mes = ($Mes - 1);
        $Data = date('01/'.$Mes.'/'.$Ano);
    }
    $EscalanteDAF = parEsc("esc_daf", $Conec, $xProj, $_SESSION["usuarioID"]);
    echo "Mês: ".$Mes.'/'.$Ano;
    echo "<br><br>";

        $rs2 = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE eft_daf = 1 And ativo = 1 ORDER BY nomeusual, nomecompl ");
        $row2 = pg_num_rows($rs2);
        if($row2 > 0){
//            echo "<div class='bContainer corFundo' style='position: relative; float: left; left: 50px;' onclick='abreDestacaDia()'>Destacar Dia</div>";
            echo "<table style='margin: 0 auto; width: 90%;'>";
                echo "<tr>";
                    echo "<td>";
                        echo "<div style='width: 150px;'> &nbsp; </div>";
                        $rs = pg_query($Conec, "SELECT id, TO_CHAR(dataescala, 'DD'), date_part('dow', dataescala), TO_CHAR(dataescala, 'DD/MM/YYYY'), feriado FROM ".$xProj.".escaladaf WHERE ativo = 1 And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ORDER BY dataescala");
                        $row = pg_num_rows($rs);
                        if($row > 0){
                            while($tbl = pg_fetch_row($rs)){
                                $IdDia = $tbl[0];
                                $DataDia = addslashes($tbl[3]);
                                
                                if($EscalanteDAF == 1){
                                    ?>
                                    <td><div <?php if($tbl[2] == 0 || $tbl[4] == 1){echo "class='quadrodiaClickCinza'";}else{echo "class='quadrodiaClick'";} ?> onclick="abreEdit(<?php echo $IdDia; ?>, '<?php echo $DataDia; ?>');"><?php echo $tbl[1]; ?><br><?php echo $Semana_Extract[$tbl[2]]; ?></div></td>
                                    <?php
                                }else{
                                    ?>
                                    <td><div <?php if($tbl[2] == 0 || $tbl[4] == 1){echo "class='quadrodiaCinza'";}else{echo "class='quadrodia'";} ?> ><?php echo $tbl[1]; ?><br><?php echo $Semana_Extract[$tbl[2]]; ?></div></td>
                                    <?php
                                }
                            }
                        } 
                    echo "</td>";

                    $Dia = 1;
                    while($tbl2 = pg_fetch_row($rs2)){
                        $Cod = $tbl2[0]; //pessoas_id de poslog
                        if(is_null($tbl2[2]) || $tbl2[2] == ""){
                            $Nome = substr($tbl2[1], 0, 20);
                        }else{
                            $Nome = substr($tbl2[2], 0, 22); //nomeusual
                        }
                        echo "<tr>";
                            echo "<td>";
                                echo "<div class='quadrodia' style='min-width: 150px; text-align: left; padding-left: 3px;'> $Nome </div>";
                                $rs3 = pg_query($Conec, "SELECT id, TO_CHAR(dataescala, 'DD'), date_part('dow', dataescala), feriado FROM ".$xProj.".escaladaf WHERE ativo = 1 And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ORDER BY dataescala");
                                $row3 = pg_num_rows($rs3);
                                if($row3 > 0){
                                    while($tbl3 = pg_fetch_row($rs3)){
                                        $CodEsc = $tbl3[0];
                                        $Dia = $tbl3[1];
                                        $Sem = $tbl3[2];
//                                        $rs4 = pg_query($Conec, "SELECT letraturno, turnoturno, destaque 
//                                        FROM ".$xProj.".escaladaf_ins INNER JOIN ".$xProj.".poslog ON ".$xProj.".escaladaf_ins.poslog_id = ".$xProj.".poslog.pessoas_id  
//                                        WHERE escaladaf_id = $CodEsc And poslog_id = $Cod And  TO_CHAR(dataescalains, 'DD') = '$Dia'");

                                        $rs4 = pg_query($Conec, "SELECT letraturno, turnoturno, destaque, date_part('dow', dataescala), feriado 
                                        FROM ".$xProj.".escaladaf INNER JOIN (".$xProj.".escaladaf_ins INNER JOIN ".$xProj.".poslog ON ".$xProj.".escaladaf_ins.poslog_id = ".$xProj.".poslog.pessoas_id) ON ".$xProj.".escaladaf.id = ".$xProj.".escaladaf_ins.escaladaf_id  
                                        WHERE escaladaf_id = $CodEsc And poslog_id = $Cod And  TO_CHAR(dataescalains, 'DD') = '$Dia'");

                                        $row4 = pg_num_rows($rs4);
                                        echo "<td>";
                                        if($row4 > 0){
                                            $tbl4 = pg_fetch_row($rs4);
                                            if($tbl4[2] == 0){
                                                if($Sem == 0 || $tbl4[4] == 1){ // dom ou feriado
                                                    echo "<div class='quadrodiaCinza'> $tbl4[0] </div>";
                                                }else{
                                                    echo "<div class='quadrodia'> $tbl4[0] </div>";
                                                }
                                            }else{
                                                if($tbl4[0] != ""){
                                                    echo "<div class='quadrodia' style='background-color: yellow;'> $tbl4[0] </div>";
                                                }else{
                                                    echo "<div class='quadrodia' style='background-color: yellow;'> &nbsp; </div>";
                                                }
                                            }
                                        }else{
                                            if($Sem == 0 || $tbl3[3] == 1){ // dom ou feriado
                                                echo "<div class='quadrodiaCinza'> &nbsp; </div>";
                                            }else{
                                                echo "<div class='quadrodia'> &nbsp; </div>";
                                            }
                                        }
                                        echo "</td>";
                                    }
                                }
                            echo "</td>";
                        echo "</tr>";
                        $Dia++;
                    }
                echo "</tr>";
            echo "</table>";
        }else{
            echo "Nenhum usuário participa desta escala. Use as configurações para definir os participantes.";
        }