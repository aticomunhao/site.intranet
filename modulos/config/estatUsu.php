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
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-confirm.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <script src="comp/js/jquery.min.js"></script> <!-- versão 3.6.3 -->
        <script src="comp/js/plotly.min.js"></script>
        <script src="comp/js/jquery-confirm.min.js"></script> <!-- para alternar para tabelas no mesmo submenu -->
        <style>
            .modal-content-grafico{
                background: linear-gradient(180deg, white, #FFF8DC);
                margin: 15% auto;
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 90%;
            }
        </style>
        <script>
            $(document).ready(function(){
                if(parseInt(document.getElementById("Editor").value) === 1 || parseInt(document.getElementById("Fiscal").value) === 1 || parseInt(document.getElementById("UsuAdm").value) > 6){
//                    document.getElementById("relacgrafico").style.display = "block";
//                    $("#divgrafico").load("modulos/config/grafUsu.php");
                }else{
                    document.getElementById("faixaMensagem").style.display = "block";
                }
                $('#carregaTema').load('modulos/config/carTema.php?carpag=estatUsu');

            });
            function abreGrafico(){
                $("#divgrafico").load("modulos/config/grafUsu.php");
                document.getElementById("relacgrafico").style.display = "block";
            }
            function fechaModalGrafico(){
                document.getElementById("relacgrafico").style.display = "none";
            }
        </script>
    </head>
    <body class="corClara" onbeforeunload="return mudaTema(0)"> <!-- ao sair retorna os background claros -->
        <?php
        $Semana_Extract = array(
            '0' => 'Dom',
            '1' => 'Seg',
            '2' => 'Ter',
            '3' => 'Qua',
            '4' => 'Qui',
            '5' => 'Sex',
            '6' => 'Sab',
            'xª'=> ''
        );

        $Tema = parEsc("tema", $Conec, $xProj, $_SESSION["usuarioID"]); // Claro(0) Escuro(1)
        $Editor = 0;
        $Fiscal = 0;
        ?>
        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"]; ?>" />
        <input type="hidden" id="Editor" value="<?php echo $Editor; ?>" />
        <input type="hidden" id="Fiscal" value="<?php echo $Fiscal; ?>" />
       
        <!-- div três colunas -->
        <div id="tricoluna0" style="margin: 10px; padding: 10px; border: 2px solid; border-radius: 10px; min-height: 52px;">
            <div id="tricoluna1" class="box" style="position: relative; float: left; width: 17%;"></div>
            <div id="tricoluna2" class="box" style="position: relative; float: left; width: 55%; text-align: center;">
                <h5>Desempenho de Usuários</h5>
            </div>
            <div id="tricoluna3" class="box" style="position: relative; float: left; width: 25%; text-align: right;">
                <div id="selectTema" style="position: relative; float: left; padding-left: 8px;">
                    <label id="etiqcorFundo" class="etiq" style="color: #6C7AB3; font-size: 80%;">Tema: </label>
                    <input type="radio" name="corFundo" id="corFundo0" value="0" <?php if($Tema == 0){echo 'CHECKED';}; ?> title="Tema claro" onclick="mudaTema(0);" style="cursor: pointer;"><label for="corFundo0" class="etiq" style="cursor: pointer;">&nbsp;Claro</label>
                    <input type="radio" name="corFundo" id="corFundo1" value="1" <?php if($Tema == 1){echo 'CHECKED';}; ?> title="Tema escuro" onclick="mudaTema(1);" style="cursor: pointer;"><label for="corFundo1" class="etiq" style="cursor: pointer;">&nbsp;Escuro</label>
                    <label style="padding-right: 5px;"></label>
                </div>
                <img src="imagens/iconGraf.png" height="36px;" id="botgrafico" style="cursor: pointer;" onclick="abreGrafico();" title="Gráfico">
                <label style="padding-left: 20px;"></label>
            </div>

            <div id="faixaMensagem" style="display: none; position: relative; margin: 70px; padding: 20px; text-align: center;">
                <br><br><br>Usuário não cadastrado.
            </div>
        </div>

        <!-- Relação Data/Hora Logins -->
        <div style="position: relative; float: left; width: 45%; margin: 5px; min-height: 550px; border: 2px solid blue; border-radius: 15px;">
            <?php 
            //corrigir anteriores
            pg_query($Conec, "UPDATE ".$xProj.".usulog SET ativo = 0 WHERE datalogin < (CURRENT_DATE - 1) And DATE_PART('YEAR', datalogout) = '3000' ");

            $rs1 = pg_query($Conec, "SELECT id FROM ".$xProj.".usulog WHERE ativo = 1 And AGE(datalogin, CURRENT_DATE) <= '3 MONTH' ");
            $row1 = pg_num_rows($rs1);
            $rs2 = pg_query($Conec, "SELECT TO_CHAR(datalogin::date, 'DD/MM/YYYY'), date_part('dow', datalogin::date) FROM ".$xProj.".usulog WHERE ativo = 1 And AGE(datalogin, CURRENT_DATE) <= '3 MONTH' GROUP BY datalogin::date ORDER BY datalogin::date DESC");
            $row2 = pg_num_rows($rs2);
            if($row2 > 0){
                ?>
                <div style="position: relative; float: right; padding-right: 5px;"><label style="font-size: 80%;"><?php echo $row2." dias - ".$row1." registros "; ?></label></div>
                <table style="margin: 0 auto; margin-top: 30px; width: 90%;">
                    <td colspan="5" style="text-align: center; border-bottom: 1px solid gray;" title="Relação dos últimos 3 meses.">Data/Hora/Duração Logins</td>
                    <?php 
                    while($tbl2 = pg_fetch_row($rs2)){
                        $Data = $tbl2[0];
                        ?>
                        <tr>
                            <td style="text-align: left; border-bottom: 1px solid gray;"><?php echo $Data; ?><label style='font-size: 70%;'><?php echo '&nbsp;('. $Semana_Extract[$tbl2[1]].')'; ?></label></td>
                            <td class="etiq" style="text-align: left; border-bottom: 1px solid gray;">Nome</td>
                            <td class="etiq" style="text-align: left; border-bottom: 1px solid gray;">Login/Logout</td>
                            <td class="etiq" style="text-align: left; border-bottom: 1px solid gray;">Tempo</td>
                            <td class="etiq" style="text-align: left; border-bottom: 1px solid gray;">Browse</td>
                        </tr>
                        <?php

                        $rs3 = pg_query($Conec, "SELECT nomeusual, nomecompl, numacessos, TO_CHAR(datalogin, 'HH24:MI'), TO_CHAR(datalogout, 'HH24:MI'), TO_CHAR((datalogout-datalogin), 'HH24:MI'), navegador, TO_CHAR((datalogout), 'YYYY') 
                        FROM ".$xProj.".usulog INNER JOIN ".$xProj.".poslog ON ".$xProj.".usulog.pessoas_id = ".$xProj.".poslog.pessoas_id 
                        WHERE TO_CHAR(datalogin, 'DD/MM/YYYY') = '$Data' And ".$xProj.".usulog.ativo = 1
                        ORDER BY nomeusual, datalogin DESC");
                        $row3 = pg_num_rows($rs3);
                        if($row3 > 0){
                            while($tbl3 = pg_fetch_row($rs3)){
                        ?>
                        <tr>
                            <td style="text-align: left; border-bottom: 1px solid gray;"></td>
                            <td style="text-align: left; border-bottom: 1px solid gray;" title="<?php echo $tbl3[1]; ?>"><?php echo $tbl3[0]; ?></td>
                            <td style="text-align: left; border-bottom: 1px solid gray;"><?php if($tbl3[7] == '3000'){echo $tbl3[3]." / -- : --";}else{echo $tbl3[3]." / ".$tbl3[4];} ?></td>
                            <td style="text-align: left; border-bottom: 1px solid gray;"><?php if($tbl3[7] == '3000'){echo " -- : --";}else{echo $tbl3[5];} ?></td>
                            <td style="text-align: left; border-bottom: 1px solid gray; font-size: 80%;"><?php echo $tbl3[6]; ?></td>
                        </tr>
                        <?php
                            }
                        }
                    }
                    ?>
                </table>
                <?php
            }
            ?>
        </div>

        <div style="position: relative; float: left; width: 19%; margin: 5px; min-height: 550px; border: 2px solid blue; border-radius: 15px;">
            <?php 
            $rs2 = pg_query($Conec, "SELECT nomeusual, TO_CHAR(SUM((datalogout-datalogin)), 'HH24:MI') 
            FROM ".$xProj.".usulog INNER JOIN ".$xProj.".poslog ON ".$xProj.".usulog.pessoas_id = ".$xProj.".poslog.pessoas_id 
            WHERE ".$xProj.".usulog.ativo = 1 And TO_CHAR(datalogout, 'YYYY') != '3000' And AGE(datalogin, CURRENT_DATE) <= '3 MONTH' 
            GROUP BY ".$xProj.".usulog.pessoas_id, nomeusual ORDER BY nomeusual, SUM(datalogout-datalogin)");
            $row2 = pg_num_rows($rs2);
            if($row2 > 0){
                ?>
                <table style="margin: 0 auto; width: 90%;">
                    <td colspan="2" style="text-align: center;" title="Tempo logado em horas/minutos nos últimos 3 meses.">Tempo Total Logado</td>
                    <tr>
                        <td class="etiq" style="text-align: left; border-bottom: 1px solid gray;">Nome</td>
                        <td class="etiq" style="text-align: right; border-bottom: 1px solid gray;" title="Tempo logado em horas/minutos nos últimos três meses.">Tempo</td>
                    </tr>
                    <?php 
                    while($tbl2 = pg_fetch_row($rs2)){
                    ?>
                        <tr>
                            <td style="text-align: left; border-bottom: 1px solid gray;"><?php echo $tbl2[0]; ?></td>
                            <td style="text-align: right; border-bottom: 1px solid gray;" title="Tempo logado em horas:minutos nos últimos três meses."><?php echo $tbl2[1]; ?></td>
                        </tr>
                    <?php
                    }
                    ?>
                </table>
                <?php
            }
            ?>
        </div>

        <div style="position: relative; float: left; width: 19%; margin: 5px; min-height: 550px; border: 2px solid blue; border-radius: 15px;">
            <?php 
            $rs2 = pg_query($Conec, "SELECT nomeusual, TO_CHAR(SUM((datalogout-datalogin)), 'HH24:MI') 
            FROM ".$xProj.".usulog INNER JOIN ".$xProj.".poslog ON ".$xProj.".usulog.pessoas_id = ".$xProj.".poslog.pessoas_id 
            WHERE ".$xProj.".usulog.ativo = 1 And TO_CHAR(datalogout, 'YYYY') != '3000' And AGE(datalogin, CURRENT_DATE) <= '3 MONTH' 
            GROUP BY ".$xProj.".usulog.pessoas_id, nomeusual ORDER BY SUM(datalogout-datalogin) DESC, nomeusual");
            $row2 = pg_num_rows($rs2);
            if($row2 > 0){
                ?>
                <table style="margin: 0 auto; width: 90%;">
                    <td colspan="2" style="text-align: center;" title="Tempo logado em horas/minutos nos últimos 3 meses.">Tempo Total Logado</td>
                    <tr>
                        <td class="etiq" style="text-align: left; border-bottom: 1px solid gray;">Nome</td>
                        <td class="etiq" style="text-align: right; border-bottom: 1px solid gray;" title="Tempo logado em horas/minutos nos últimos três meses.">Tempo</td>
                    </tr>
                    <?php 
                    while($tbl2 = pg_fetch_row($rs2)){
                    ?>
                        <tr>
                            <td style="text-align: left; border-bottom: 1px solid gray;"><?php echo $tbl2[0]; ?></td>
                            <td style="text-align: right; border-bottom: 1px solid gray;" title="Tempo logado em horas:minutos nos últimos três meses."><?php echo $tbl2[1]; ?></td>
                        </tr>
                    <?php
                    }
                    ?>
                </table>
                <?php
            }
            ?>
        </div>

        <div style="position: relative; float: left; width: 12%; margin: 5px; min-height: 550px; border: 2px solid blue; border-radius: 15px;">
            <table style="margin: 0 auto; margin-top: 30px; width: 90%;">
                <td colspan="2" style="text-align: center; border-bottom: 1px solid gray; width: 50px">Navegador</td>
                <?php
                $rsBr = pg_query($Conec, "SELECT id FROM ".$xProj.".usulog WHERE ativo = 1 ");
                $rowBr = pg_num_rows($rsBr);
                $rsBr = pg_query($Conec, "SELECT navegador, COUNT(navegador) FROM ".$xProj.".usulog WHERE ativo = 1 GROUP BY navegador ");
                while($tblBr = pg_fetch_row($rsBr)){
                    ?>
                    <tr>
                        <td style="text-align: left; border-bottom: 1px solid gray; width: 50px"><?php echo $tblBr[0]; ?></td>
                        <td style="text-align: right; border-bottom: 1px solid gray; width: 10px;"><?php echo number_format(($tblBr[1]*100)/$rowBr, 2, ",",".")."%"; ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>


        <!-- div modal para mostrar gráfico anual -->
        <div id="relacgrafico" class="relacmodal">
            <div class="modal-content-grafico">
            <div>
                <span class="close" onclick="fechaModalGrafico();">&times;</span>
                <h5 id="titulomodal" style="text-align: center;color: #666;">Demonstrativo Número de Logins</h5>

                <div id="divgrafico" style="width:100%; border: 2px solid #C6E2FF; border-radius: 15px;"></div>

                <div style="padding-bottom: 20px;"></div>
            </div>
            </div>
            <br><br>
        </div> <!-- Fim Modal-->

        <div id="carregaTema"></div> <!-- carrega a pág modulos/config/carTema.php - onde está a função mudaTema() -->

    </body>
</html>