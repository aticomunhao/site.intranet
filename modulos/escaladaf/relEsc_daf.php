<?php
    // esse é o primeiro quadro - relaciona nome, dias e letras
    session_start(); 
    if(!isset($_SESSION["usuarioID"])){
        session_destroy();
        header("Location: ../../index.php");
    }

    require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
    date_default_timezone_set('America/Sao_Paulo'); 
    //numeração do dia da semana da função extract() (DOW) é diferente da função to_char() (D)
    //Função para Extract no postgres
    $Semana_Extract2 = array(
        '0' => 'D',
        '1' => '2ª',
        '2' => '3ª',
        '3' => '4ª',
        '4' => '5ª',
        '5' => '6ª',
        '6' => 'S',
        'xª'=> ''
    );
    $Semana_Extract = array(
        '0' => 'D',
        '1' => 'S',
        '2' => 'T',
        '3' => 'Q',
        '4' => 'Q',
        '5' => 'S',
        '6' => 'S',
        'xª'=> ''
    );

    if(isset($_REQUEST["selecmes"])){
        $MesSalvo = $_REQUEST["selecmes"]; // quando vem do fiscal
    }else{
        $MesSalvo = parEsc("mes_escdaf", $Conec, $xProj, $_SESSION["usuarioID"]); 
    }

    if(is_null($MesSalvo) || $MesSalvo == ""){
        $MesSalvo = date("m")."/".date("Y");
    }
    $Proc = explode("/", $MesSalvo);
    if(is_null($Proc[1])){
        $Mes = date("m");
    }else{
        $Mes = $Proc[0];
    }
    if(strLen($Mes) < 2){
        $Mes = "0".$Mes;
    }
    if(is_null($Proc[1])){
        $Ano = date("Y");
    }else{
        $Ano = $Proc[1];
    }

    $EscalanteDAF = parEsc("esc_daf", $Conec, $xProj, $_SESSION["usuarioID"]);
    $Fiscal = parEsc("esc_fisc", $Conec, $xProj, $_SESSION["usuarioID"]);
    $visuCargo = parAdm("visucargo_daf", $Conec, $xProj); // visualisar cargo no quadro
    $PrimCargo = parAdm("primcargo_daf", $Conec, $xProj); // visualisar primeiro o cargo no quadro
    $CorListas = parEsc("corlistas_daf", $Conec, $xProj, $_SESSION["usuarioID"]);

    if(isset($_REQUEST["numgrupo"])){
        $NumGrupo = $_REQUEST["numgrupo"]; // quando vem do fiscal
    }else{
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);   
    }
    $MeuGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);

    if(isset($_REQUEST["guardatema"])){ // vem da pág carTema ao mudar o tema
        $Tema = $_REQUEST["guardatema"];
    }else{
        $Tema = 0;
    }
//    echo "Tema: ".$Tema;

    if(isset($_REQUEST["largTela"])){
        $LargTela = $_REQUEST["largTela"]; // largura da tela
        //Salva para uso futuro largura da tela usada por quem edita escala para escala
        $rsProc = pg_query($Conec, "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'poslog' AND COLUMN_NAME = 'largtela'");
        $rowProc = pg_num_rows($rsProc);
        if($rowProc > 0){
            pg_query($Conec, "UPDATE ".$xProj.".poslog SET largtela = '$LargTela' WHERE pessoas_id = ".$_SESSION["usuarioID"]." And ativo = 1");
        }
    }else{
        $LargTela = 1280; // laptop 14pol
    }

    if($LargTela > 1280){
        $Quant = 15; // Quantidade de caracteres no nome ou cargo
        $Campo = "115px"; // larg campo nome ou cargo 
    }else{
        $Quant = 15;
        $Campo = "105px";
    }
    if($LargTela < 1270){ // chrome - laptop 14pol
        $Quant = 10;
        $Campo = "90px";
    }
    if($LargTela == 1900){
        $Quant = 20;
        $Campo = "150px";
    }

    //Provisório - Apaga lançamentos em grupo diferente
    $rs0 = pg_query($Conec, "SELECT pessoas_id, esc_grupo FROM ".$xProj.".poslog WHERE ativo = 1 And esc_grupo = $NumGrupo");
    $row0 = pg_num_rows($rs0);
    if($row0 > 0){
        while($tbl0 = pg_fetch_row($rs0)){
            $Cod = $tbl0[0];
            pg_query($Conec, "UPDATE ".$xProj.".escaladaf_ins SET ativo = 0 
            WHERE poslog_id = $Cod And ativo = 1 And grupo_ins != $NumGrupo And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ");
        }
    }
    $rsGr = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf_esc WHERE usu_id = ".$_SESSION["usuarioID"]." And ativo = 1");
    $rowGr = pg_num_rows($rsGr); // quantidade de grupos em que é escalante

    $rs2 = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual, cargo_daf FROM ".$xProj.".poslog WHERE eft_daf = 1 And ativo = 1 And esc_grupo = $NumGrupo ORDER BY ordem_daf, nomeusual, nomecompl ");
    $row2 = pg_num_rows($rs2);

    $rs = pg_query($Conec, "SELECT editaesc FROM ".$xProj.".escalas_gr WHERE id = $NumGrupo;");
    $row = pg_num_rows($rs);
    if($row > 0){
        $tbl = pg_fetch_row($rs);
        $EscalaFechada = $tbl[0];
    }else{
        $EscalaFechada = 0;
    }
    ?>
        <div class="col" style="position: relative; float: left; width: 33%;">&nbsp;</div>
        <div class="col" style="position: relative; float: left; width: 33%; padding-bottom: 10px;"><?php echo "Mês: ".$MesSalvo; ?></div>
        <div class="col" style="position: relative; float: right; width: 33%;">
            <label id="etiqeditaEscala" for="editaEscala" class="etiq eItalic" style="padding-left: 20px; <?php if($EscalaFechada == 1){echo "color: red; ";} ?>" title="Considera/desconsidera as mudanças como Troca de Serviço">Escala Fechada</label>
            <input type="checkbox" id="editaEscala" <?php if($EscalaFechada == 1){echo "CHECKED";}; ?> title="Considera/desconsidera as mudanças como Troca de Serviço" onchange="editaEscala(this);" >
        </div>
    <?php

    if($row2 > 0){
        echo "<table style='margin: 0 auto; width: 99%;'>";
            echo "<tr>";
                echo "<td style='text-align: right;'>";
                    echo "<div style='width: 150px;'>&nbsp; </div>";

                    if($visuCargo == 1){ // Visualizar o cargo junto ao nome
                        if($PrimCargo == 1){ 
                            echo "<input disabled type='text' class='etiq' style='width: $Campo; font-size: 80%; border: 0px solid; text-align: center; background: transparent;' value='Cargo/FG' />";
                            echo "<input disabled type='text' class='etiq' style='width: $Campo; font-size: 80%; border: 0px solid; text-align: center; background: transparent;' value='Nome' />";
                        }else{
                            echo "<input disabled type='text' class='etiq' style='width: $Campo; font-size: 80%; border: 0px solid; text-align: center; background: transparent;' value='Nome' />";
                            echo "<input disabled type='text' class='etiq' style='width: $Campo; font-size: 80%; border: 0px solid; text-align: center; background: transparent;' value='Cargo/FG' />";
                        }
                    }else{
                        echo "<input disabled type='text' style='width: $Campo; font-size: 80%; border: 0px solid; text-align: center; background: transparent;' value='Nome' />";
                    }
                    $rs = pg_query($Conec, "SELECT id, TO_CHAR(dataescala, 'DD'), date_part('dow', dataescala), TO_CHAR(dataescala, 'DD/MM/YYYY'), feriado, dataescala FROM ".$xProj.".escaladaf WHERE ativo = 1 And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' And grupo_id = $NumGrupo ORDER BY dataescala");
                    $row = pg_num_rows($rs);
                    if($row > 0){
                        while($tbl = pg_fetch_row($rs)){
                            $IdDia = $tbl[0];
                            $DataDia = addslashes($tbl[3]);

                            $ProcFer = $tbl[5]; // dataescala  // $ProcFer = "2025/".$Mes."/".$tbl[1];
                            $diaFer = 0;
                            $rsFer = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf_fer WHERE dataescalafer = '$ProcFer' And ativo = 1");
                            $rowFer = pg_num_rows($rsFer);
                            if($rowFer > 0){ // dataescala = dataescalafer
                                $diaFer = 1;
                            }
//                            if($EscalanteDAF == 1 || $Fiscal == 1){
//                            if($EscalanteDAF == 1 && $MeuGrupo == $NumGrupo){
//                            if($EscalanteDAF == 1 && $MeuGrupo == $NumGrupo || $_SESSION["usuarioID"] == 83){ // Provisório Wil
                            if($EscalanteDAF == 1 && $MeuGrupo == $NumGrupo || $rowGr > 1 || $_SESSION["usuarioID"] == 83){ // Provisório Wil
                                ?>
                                <td><div <?php if($tbl[2] == 0 || $diaFer == 1){echo "class='quadrodiaClickCinza'";}else{echo "class='quadrodiaClick'";} ?> onclick="abreEdit(<?php echo $IdDia; ?>, '<?php echo $DataDia; ?>');"><?php echo $tbl[1]; ?><br><?php echo $Semana_Extract[$tbl[2]]; ?></div></td>
                                <?php
                            }else{
                                ?>
                                <td><div <?php if($tbl[2] == 0 || $diaFer == 1){echo "class='quadrodiaCinza'";}else{echo "class='quadrodia'";} ?> ><?php echo $tbl[1]; ?><br><?php echo $Semana_Extract[$tbl[2]]; ?></div></td>
                                <?php
                            }
                        }
                    } 
                echo "</td>";
                echo "<td><img src='imagens/Excel-icon.png' height='20px;' style='cursor: pointer; padding-left: 5px;' onclick='abreExcel();' title='Envia para arquivo Excel'></td>";

                $Dia = 1;
                $Cont = 1;
                while($tbl2 = pg_fetch_row($rs2)){
                    $Cod = $tbl2[0]; //pessoas_id de poslog
                    if($Cont % 2 == 0){ // linhas pares - 2ª inha

                        if($Tema == 1){ //escuro
                            //$CorFundo = "#FFFAFA"; // Branco - corFundo em indlog.css
                            $CorFundo = "transparent; opacity: 1; color: white;"; 
                        }else{
                            $CorFundo = "transparent; opacity: 1;"; 
                            //$CorFundo = "#FFFAFA; opacity: 1;"; // Branco - corFundo em indlog.css
                        }
                    }else{
                        if($CorListas == 0){
                            if($Tema == 1){ //escuro
                                $CorFundo = "white; opacity: 0.5; color: black;";
                            }else{
                                $CorFundo = "#FFFAFA; opacity: 1; color: black;"; //Branco
                            }
                        }
                        if($CorListas == 1){
                            if($Tema == 1){ //escuro
                                $CorFundo = "white; opacity: 0.5; color: black;";
                            }else{
                                $CorFundo = "#FFF8DC; opacity: 1; color: black;"; //Cornsilk1 
                            }
                        }
                        if($CorListas == 2){
                            if($Tema == 1){ //escuro
                                $CorFundo = "white; opacity: 0.5; color: black;";
                            }else{
                                $CorFundo = "#F0FFFF; opacity: 1; color: black;"; //Azure
                            }
                        }
                        if($CorListas == 3){
                            if($Tema == 1){ //escuro
                                $CorFundo = "white; opacity: 0.5; color: black;";
                            }else{
                                $CorFundo = "#E6E6FA; opacity: 1; color: black;"; //Lavanda
                            }
                        }
                        if($CorListas == 4){
                            if($Tema == 1){ //escuro
                                $CorFundo = "white; opacity: 0.5; color: black;";
                            }else{
                                $CorFundo = "#EEEEE0; opacity: 1; color: black;"; //Marfim
                            }
                        }
                    }
                    if(is_null($tbl2[2]) || $tbl2[2] == ""){
                        $Nome = substr($tbl2[1], 0, $Quant); //nome completo
                        if($visuCargo == 0){
                            $Nome = substr($tbl2[1], 0, 35); //nome completo
                        }
                    }else{
                        $Nome = substr($tbl2[2], 0, 35); //nome usual
                    }
                    if(!is_null($tbl2[3])){
                        $Cargo = substr($tbl2[3], 0, $Quant); // cargo
                    }else{
                        $Cargo = "&nbsp";    
                    }
                    echo "<tr>";
                        echo "<td style='text-align: right;'>";
                            if($LargTela > 1380){
                                echo "<input disabled type='text' style='background: transparent; font-size: 80%; width: 30px; border: 0px solid; text-align: center;' value=$Cont />";
                            }
                            if($visuCargo == 1){ // Visualizar o cargo junto ao nome
                                if($PrimCargo == 1){ // Primeiro o cargo depois o nome
//                                    echo "<input disabled type='text' style='width: $Campo; background: $CorFundo; font-size: 90%; border: 1px solid; border-radius: 5px; padding-left: 3px;' value='$Cargo' />";
//                                    echo "<input disabled type='text' style='width: $Campo; background: $CorFundo; font-size: 90%; border: 1px solid; border-radius: 5px; padding-left: 3px;' value='$Nome' />";
                                    echo "<label class='aEsq' style='width: $Campo; background: $CorFundo; font-size: 90%; border: 1px solid; border-radius: 5px; padding-left: 3px;'>$Cargo</label>";
                                    echo "<label class='aEsq' style='width: $Campo; background: $CorFundo; font-size: 90%; border: 1px solid; border-radius: 5px; padding-left: 3px;'>$Nome</label>";
                                }else{
                                    echo "<input disabled type='text' style='width: $Campo; background: $CorFundo; font-size: 90%; border: 1px solid; border-radius: 5px; padding-left: 3px;' value='$Nome' />";
                                    echo "<input disabled type='text' style='width: $Campo; background: $CorFundo; font-size: 90%; border: 1px solid; border-radius: 5px; padding-left: 3px;' value='$Cargo' />";
                                }
                            }else{
                                echo "<input disabled type='text' style='width: 170px; font-size: 90%; background: $CorFundo; border: 1px solid; border-radius: 5px; padding-left: 3px;' value='$Nome' />";
                            }
                            $rs3 = pg_query($Conec, "SELECT id, TO_CHAR(dataescala, 'DD'), date_part('dow', dataescala), feriado, dataescala FROM ".$xProj.".escaladaf WHERE ativo = 1 And TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' And grupo_id = $NumGrupo ORDER BY dataescala");
                            $row3 = pg_num_rows($rs3);
                            if($row3 > 0){
                                while($tbl3 = pg_fetch_row($rs3)){
                                    $CodEsc = $tbl3[0];
                                    $Dia = $tbl3[1];
                                    $Sem = $tbl3[2];

                                    $ProcFer = $tbl3[4]; // dataescala 
                                    $diaFer = 0;
                                    $rsFer = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf_fer WHERE dataescalafer = '$ProcFer' And ativo = 1");
                                    $rowFer = pg_num_rows($rsFer);
                                    if($rowFer > 0){
                                        $diaFer = 1;
                                    }

                                    $rs4 = pg_query($Conec, "SELECT letraturno, turnoturno, destaque, date_part('dow', dataescala), feriado, valepag, dataescala 
                                    FROM ".$xProj.".escaladaf INNER JOIN (".$xProj.".escaladaf_ins INNER JOIN ".$xProj.".poslog ON ".$xProj.".escaladaf_ins.poslog_id = ".$xProj.".poslog.pessoas_id) ON ".$xProj.".escaladaf.id = ".$xProj.".escaladaf_ins.escaladaf_id  
                                    WHERE escaladaf_id = $CodEsc And poslog_id = $Cod And TO_CHAR(dataescalains, 'DD') = '$Dia'");

                                    $row4 = pg_num_rows($rs4);
                                    echo "<td>";
                                    if($row4 > 0){
                                        $tbl4 = pg_fetch_row($rs4);
                                        $ValeRef = $tbl4[5]; // Vale refeição

                                        $ProcFer = $tbl4[6];
                                        $diaFer = 0;
                                        $rsFer = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf_fer WHERE dataescalafer = '$ProcFer' And ativo = 1");
                                        $rowFer = pg_num_rows($rsFer);
                                        if($rowFer > 0){
                                            $diaFer = 1;
                                        }

                                        //Procura troca de letra em escaladaf_trocas
                                        $ProcTroca = $tbl4[0];
                                        $Troca = 0;
                                        $rsTroc = pg_query($Conec, "SELECT letra_orig, ".$xProj.".escaladaf_trocas.id 
                                        FROM ".$xProj.".escaladaf_trocas INNER JOIN ".$xProj.".poslog ON ".$xProj.".escaladaf_trocas.poslog_id = ".$xProj.".poslog.pessoas_id 
                                        WHERE escaladaf_id = $CodEsc And poslog_id = $Cod And ".$xProj.".poslog.ativo = 1 And letra_orig != '$ProcTroca'");
                                        $rowTroc = pg_num_rows($rsTroc);
    
                                        if($rowTroc > 0){
                                            $LetraOrig = ""; // para o title da letra
                                            while($tblTroc = pg_fetch_row($rsTroc)){
                                                $idTroc = $tblTroc[1];
                                                $LetraOrig = $LetraOrig." ".$tblTroc[0];
                                                pg_query($Conec, "UPDATE ".$xProj.".escaladaf_trocas SET marca = 1 WHERE id = $idTroc");
                                            }
                                            if($rowTroc == 1){
                                                $LetraOrig = "Letra original: ".$LetraOrig;
                                            }
                                            if($rowTroc > 1){
                                                $LetraOrig = "Letras anteriores: ".$LetraOrig;
                                            }
                                            $Troca = 1;
                                        }else{
                                            $LetraOrig = "";
                                        }

                                        if($tbl4[2] == 0){ // sem destaque
                                            if($Sem == 0 || $diaFer == 1){ // domingo ou feriado
                                                if($ValeRef == 0){ // sem Vale refeição
                                                    echo "<div class='quadrodiaCinza' style='border-width: 2px; border-color: red; ";
                                                    if($Troca == 1){echo "border: 2px solid red; border-radius: 10px; ";};
                                                    echo "' title='Sem vale refeição. $LetraOrig'> $tbl4[0]";
                                                    echo "</div>";
                                                }else{
                                                    if($Troca == 1){
                                                        echo "<div class='quadrodiaCinza' style='border: 2px solid #FFD700; border-radius: 10px;' title='$LetraOrig'> $tbl4[0] </div>";
                                                    }else{
                                                        echo "<div class='quadrodiaCinza' title='$LetraOrig'> $tbl4[0] </div>";
                                                    }
                                                }
                                            }else{
                                                if($ValeRef == 0){ // sem Vale refeição
                                                    echo "<div class='quadrodia' style='border-width: 2px; border-color: red; background: $CorFundo; "; 
                                                    if($Troca == 1){echo "border: 2px solid red; border-radius: 10px;";}
                                                    echo "' title='Sem vale refeição. $LetraOrig'> $tbl4[0] ";
                                                    echo "</div>";
                                                }else{
                                                    echo "<div class='quadrodia' style='background: $CorFundo; "; 
                                                    if($Troca == 1){echo "border: 2px solid #FFD700; border-radius: 10px;";}
                                                    echo "' title='$LetraOrig'> $tbl4[0] ";
                                                    echo "</div>";
                                                }
                                            }
                                        }else{ // com destaque
                                            $Destaq = $tbl4[2];
                                            if($Destaq == 1){
                                                $Cor = "yellow; color: black;";
                                            }
                                            if($Destaq == 2){ // Azul
                                                $Cor = "#00BFFF; color: black;";
                                            }
                                            if($Destaq == 3){ // Verde
                                                $Cor = "#00FF7F; color: black;";
                                            }

                                            if($tbl4[0] != ""){
                                                if($tbl4[5] == 0){ // sem vale refeição
                                                    echo "<div class='quadrodia' style='background: $Cor; border-width: 2px; border-color: red; ";
                                                    if($Troca == 1){echo "border: 2px solid red; border-radius: 10px; ";};
                                                    echo "' title='Sem vale refeição. $LetraOrig'> $tbl4[0] ";
                                                    echo "</div>";
                                                }else{
                                                    echo "<div class='quadrodia' style='background: $Cor; ";
                                                    if($Troca == 1){echo "border: 2px solid #FFD700; border-radius: 10px; "; }; 
                                                    echo "' title='$LetraOrig'> $tbl4[0] ";
                                                    echo "</div>";
                                                }
                                            }else{
                                                echo "<div class='quadrodia' style='background: $Cor;'> &nbsp; </div>";
                                            }
                                        }
                                    }else{
                                        if($Sem == 0 || $diaFer == 1){ // dom ou feriado
                                            echo "<div class='quadrodiaCinza'> &nbsp; </div>";
                                        }else{
                                            echo "<div class='quadrodia'> &nbsp; </div>";
                                        }
                                    }
                                    echo "</td>";
                                }
                            echo "<td class='etiq eItalic aCentro' style='cursor: default;' title='Número de serviços no mês'>";
                                    //Conta número de serviços na escala
                                    $rs5 = pg_query($Conec, "SELECT COUNT(poslog_id) 
                                    FROM ".$xProj.".escaladaf_ins INNER JOIN ".$xProj.".escaladaf_turnos ON ".$xProj.".escaladaf_ins.turnos_id = ".$xProj.".escaladaf_turnos.id 
                                    WHERE poslog_id = $Cod And TO_CHAR(dataescalains, 'MM') = '$Mes' And grupo_ins = $NumGrupo And infotexto = 0 And valepag = 1");
                                    $tbl5 = pg_fetch_row($rs5);
                                    $Total = $tbl5[0];
                                echo "&nbsp;<sup>$Total</sup></td>";
                            }
                        echo "</td>";
                    echo "</tr>";
                    $Dia++;
                    $Cont++;
                }
            echo "</tr>";
        echo "</table>";
    }else{
         echo "<br><br>Nenhum usuário participa desta escala. Use as configurações ";
//          echo "<img src='imagens/settings.png' height='15px;' style='cursor: pointer;' onclick='abreEscalaConfig();'>";
        echo "<img src='imagens/settings.png' height='15px;'>";
        echo " para definir os participantes.";
    }