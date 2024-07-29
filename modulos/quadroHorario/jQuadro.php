<?php
session_start(); 
if(!isset($_SESSION["usuarioID"])){
    session_destroy();
    header("Location: ../../index.php");
}

require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
date_default_timezone_set('America/Sao_Paulo'); 

$Mes_Extract = array(
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
//numeração do dia da semana da função extract() (DOW) é diferente da função to_char() (D)
//Função para Extract no postgres
$Semana_Extract = array(
    '0' => 'Dom',
    '1' => '2ª',
    '2' => '3ª',
    '3' => '4ª',
    '4' => '5ª',
    '5' => '6ª',
    '6' => 'Sab',
    'xª'=> ''
);

    if(isset($_REQUEST["mesano"])){
        if($_REQUEST["mesano"] != ""){
            $Busca = addslashes(filter_input(INPUT_GET, 'mesano'));
            $Proc = explode("/", $Busca);
            $Mes = $Proc[0];
            if(strLen($Mes) < 2){
                $Mes = "0".$Mes;
            }
            if($Proc[1] == ""){
                return false;
            }
            $Ano = $Proc[1];
            $Data = date('01/'.$Mes.'/'.$Ano);
        }else{
            $Mes = date("m");
            $Ano = date("Y");
        }
    }else{
        $Mes = date("m");
        $Ano = date("Y");
    }
    if(isset($_REQUEST["numgrupo"])){
        $NumGrupo = (int) filter_input(INPUT_GET, 'numgrupo');
    }
    $rsGr = pg_query($Conec, "SELECT qtd_turno FROM ".$xProj.".escalas_gr WHERE id = '$NumGrupo' ");
    $rowGr = pg_num_rows($rsGr);
    if($rowGr > 0){
        $tblGr = pg_fetch_row($rsGr);
        $Turnos = $tblGr[0];
    }else{
        $Turnos = 1;
    }

    $Turnos = 1;

    $Escalante = parEsc("esc_edit", $Conec, $xProj, $_SESSION["usuarioID"]); // escalante do grupo
    //Salva o último mês acessado
    pg_query($Conec, "UPDATE ".$xProj.".escalas_gr set guardaescala = '".$_REQUEST["mesano"]."' WHERE id = $NumGrupo ");

    $Ini = strtotime($Ano.'/'.$Mes.'/01'); //dia 1 do mês e ano selecionados
    $DiaIni = strtotime("-1 day", $Ini); // para começar com o dia 1 no loop          
    for($i = 0; $i < 31; $i++){ // completar os dias que faltam no mês            
        $Amanha = strtotime("+1 day", $DiaIni);
        $DiaIni = $Amanha;
        $Data = date("Y/m/d", $Amanha); // data legível            
        $rs0 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadrohor WHERE dataescala = '$Data' And grupo_id = $NumGrupo ");
        $row0 = pg_num_rows($rs0);
        if($row0 == 0){
            pg_query($Conec, "INSERT INTO ".$xProj.".quadrohor (dataescala, grupo_id, usuins, datains) VALUES ('$Data', $NumGrupo, ".$_SESSION["usuarioID"].", NOW() )");
        }
    }
    ?>

    <div style="text-align: center;">
        <h5><?php echo $Mes_Extract[$Mes].'/'.$Ano; ?></h5>
        <?php 
        $rs = pg_query($Conec, "SELECT ".$xProj.".quadrohor.id, grupo_id, TO_CHAR(dataescala, 'DD/MM/YYYY'), turno1_id, TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI'), 
        date_part('dow', dataescala), TO_CHAR(horafim1 - horaini1, 'HH24:MI'), quadrohor_id 
        FROM ".$xProj.".quadrohor LEFT JOIN ".$xProj.".quadroins ON ".$xProj.".quadrohor.id = ".$xProj.".quadroins.quadrohor_id 
        WHERE grupo_id = $NumGrupo And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ORDER BY dataescala, TO_CHAR(horaini1, 'HH24:MI'), TO_CHAR(horafim1, 'HH24:MI')");
        $row = pg_num_rows($rs);
        $Cont = 1;
        ?>
        <table style="margin: 0 auto;">
            <tr>
                <td class="etiq aCentro">Data</td>
                <td class="etiq aCentro">Sem</td>
                <td class="etiq aCentro">Início</td>
                <td class="etiq aCentro">Fim</td>
                <td class="etiq aCentro">Escala</td>
                <td></td>
            </tr>
            <?php
                if($row > 0){
                    while($tbl = pg_fetch_row($rs)){
                        $Cod = $tbl[0]; // id de quadrohor
                        if(!is_null($tbl[8]) || $tbl[8] != ""){
                            $CodQuadro = $tbl[8];
                        }else{
                            $CodQuadro = 0;
                            $Cont = 1;
                        }
                        //Para não repetir data e dia da semana
                        $rsCont = pg_query($Conec, "SELECT COUNT(id) FROM ".$xProj.".quadroins WHERE quadrohor_id = $CodQuadro;");
                        $tblCont = pg_fetch_row($rsCont);
                        $Num = $tblCont[0];
                        if($CodQuadro == 0){
                            $Num = 0;
                            $Cont = 0;
                        }
                        if($Cont > $Num && $CodQuadro > 0){
                            $Cont = 1;
                        }

                        $CodPartic1 = $tbl[3]; // pessoas_id de poslog - salvo em salvaQuadro.php
                        if(is_null($tbl[3]) || $tbl[3] == ""){
                            $CodPartic1 = 0;
                        }
                        ?>
                        <tr>
                            <td><div class="quadrinho"> <?php if($Cont == 1 || $CodQuadro == 0){echo $tbl[2];}else{echo "&nbsp;";} ?> </div></td>
                            <td><div class="quadrinho"> <?php if($Cont == 1 || $CodQuadro == 0){echo $Semana_Extract[$tbl[6]];}else{echo "&nbsp;";} ?> </div></td>
                            <td><div class="quadrinho"> <?php if(is_null($tbl[4]) || $tbl[4] == 0 && is_null($tbl[5]) || $tbl[5] == 0){echo "&nbsp;";}else{echo $tbl[4];}; ?> </div> </td>
                            <td><div class="quadrinho"> <?php if(is_null($tbl[5]) || $tbl[5] == 0){echo "&nbsp;";}else{echo $tbl[5];}; ?> </div> </td>

                            <?php

                            $rs1 = pg_query($Conec, "SELECT nomecompl, nomeusual FROM ".$xProj.".poslog WHERE pessoas_id = $CodPartic1;");
                            $row1 = pg_num_rows($rs1);
                            if($row1 > 0){
                                $tbl1 = pg_fetch_row($rs1);
                                $Nome1 = $tbl1[1];
                                if(is_null($tbl1[1]) || $tbl1[1] == ""){
                                    $Nome1 = $tbl1[0];    
                                }
                            }else{
                                $Nome1 = "&nbsp;";
                            }

                            ?>

                            <td><div <?php if($Escalante == 1){echo "class='quadrinhoClick'";}else{echo "class='quadrinho'";} ?> style="text-align: left; font-weight: bold;" onclick="abreParticip(1, <?php echo $Cod; ?>, <?php echo $CodPartic1; ?>, '<?php echo $tbl[2]; ?>', '<?php echo $Nome1; ?>');" title="Clique aqui para escalar ou editar o escalado"> <?php echo $Nome1; ?> </div> </td>
                            <td class="etiq60" title="Horas do turno"><?php if(!is_null($tbl[7]) && $tbl[7] != 0){echo $tbl[7]."h";} ?></td>
                        </tr>
                        <?php
                        $Cont++;
                    }
                }
            ?>
        </table>
        <br><br>
    </div>
    <br><br>