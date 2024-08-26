<?php
session_start(); 
if(!isset($_SESSION["usuarioID"])){
    session_destroy();
    header("Location: ../../index.php");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" type="text/css" media="screen" href="class/bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-ui.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-confirm.min.css" />
        <script src="class/bootstrap/js/bootstrap.min.js"></script>
        <script src="comp/js/jquery.min.js"></script> <!-- versão 3.6.3 -->
        <script src="comp/js/jquery-ui.min.js"></script>
        <script src="comp/js/jquery.mask.js"></script>
        <script src="comp/js/jquery-confirm.min.js"></script>   <!-- https://craftpip.github.io/jquery-confirm/#quickfeatures -->
        <style>
            .modal-content-relacParticip{
                background: linear-gradient(180deg, white, #00BFFF);
                margin: 12% auto;
                padding: 10px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 60%;
            }
            .modal-content-escalaControle{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 10% auto;
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 70%;
            }
             .quadrodia {
                font-size: 90%;
                min-width: 30px;
                border: 1px solid;
                border-radius: 3px;
            }
            .quadrodiaClick {
                font-size: 90%;
                min-width: 30px;
                border: 1px solid;
                border-radius: 3px;
                cursor: pointer;
            }
            .etiq{
                text-align: right; font-size: 70%; font-weight: bold; padding-right: 1px; padding-bottom: 1px;
            }

        </style>
        <script>
            $(document).ready(function(){
                document.getElementById("imgEscalaConfig").style.visibility = "hidden"; 
                if(parseInt(document.getElementById("escalante").value) === 1 || parseInt(document.getElementById("UsuAdm").value) > 6){ // // se estiver marcado
                    document.getElementById("imgEscalaConfig").style.visibility = "visible"; 
                }

                document.getElementById("selecMesAnoEsc").value = document.getElementById("guardamesano").value;
                $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));


                $("#selecMesAnoEsc").change(function(){
                    if(parseInt(document.getElementById("selecMesAnoEsc").value) > 0){
                        $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));
                    }
                });


                $("#configSelecEscala").change(function(){
                    if(document.getElementById("configSelecEscala").value == ""){
                        document.getElementById("configCpfEscala").value = "";
                        document.getElementById("checkefetivo").checked = false;
                        document.getElementById("checkescalante").checked = false;
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=buscausuario&codigo="+document.getElementById("configSelecEscala").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("configCpfEscala").value = format_CnpjCpf(Resp.cpf);
                                        if(parseInt(Resp.eft) === 1){
                                            document.getElementById("checkefetivo").checked = true;
                                        }else{
                                            document.getElementById("checkefetivo").checked = false;
                                        }
                                        if(parseInt(Resp.esc) === 1){
                                            document.getElementById("checkescalante").checked = true;
                                        }else{
                                            document.getElementById("checkescalante").checked = false;
                                        }
                                    }else{
                                        alert("Houve um erro no servidor.")
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                });

                $("#configCpfEscala").click(function(){
                    document.getElementById("configSelecEscala").value = "";
                    document.getElementById("configCpfEscala").value = "";
                    document.getElementById("checkefetivo").checked = false;
                    document.getElementById("checkescalante").checked = false;
                });
                $("#configCpfEscala").change(function(){
                    document.getElementById("configSelecEscala").value = "";
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=buscacpf&cpf="+encodeURIComponent(document.getElementById("configCpfEscala").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("configSelecEscala").value = Resp.PosCod;
                                        document.getElementById("configCpfEscala").value = format_CnpjCpf(Resp.cpf);
                                        if(parseInt(Resp.eft) === 1){
                                            document.getElementById("checkefetivo").checked = true;
                                        }else{
                                            document.getElementById("checkefetivo").checked = false;
                                        }
                                        if(parseInt(Resp.esc) === 1){
                                            document.getElementById("checkescalante").checked = true;
                                        }else{
                                            document.getElementById("checkescalante").checked = false;
                                        }
                                    }
                                    if(parseInt(Resp.coderro) === 1){
                                        alert("Houve um erro no servidor.");
                                    }
                                    if(parseInt(Resp.coderro) === 2){
                                        document.getElementById("checkefetivo").checked = false;
                                        document.getElementById("checkescalante").checked = false;
                                        $('#mensagemConfig').fadeIn("slow");
                                        document.getElementById("mensagemConfig").innerHTML = "Não encontrado";
                                        $('#mensagemConfig').fadeOut(2000);
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                });


            }); // fim do ready

            function ajaxIni(){
                try{
                ajax = new ActiveXObject("Microsoft.XMLHTTP");}
                catch(e){
                try{
                   ajax = new ActiveXObject("Msxml2.XMLHTTP");}
                   catch(ex) {
                   try{
                       ajax = new XMLHttpRequest();}
                       catch(exc) {
                          alert("Esse browser não tem recursos para uso do Ajax");
                          ajax = null;
                       }
                   }
                }
            }

            function abreEdit(DiaId, DataDia){
                document.getElementById("guardaDiaId").value = DiaId; // id do dia em escaladaf
                document.getElementById("titulomodal").innerHTML = DataDia;
                $("#relacaoParticip").load("modulos/escaladaf/equipe.php");
                document.getElementById("relacParticip").style.display = "block";
            }


            function MarcaPartic(Cod){ // vem de equipe.php
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=marcaPartic&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }



            function marcaConfigEscala(obj, Campo){
                if(obj.checked === true){
                    Valor = 1;
                }else{
                    Valor = 0;
                }
                if(document.getElementById("configSelecEscala").value == ""){
                    if(obj.checked === true){
                        obj.checked = false;
                    }else{
                        obj.checked = true;
                    }
                    $('#mensagemConfig').fadeIn("slow");
                    document.getElementById("mensagemConfig").innerHTML = "Selecione um usuário.";
                    $('#mensagemConfig').fadeOut(2000);
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=configMarcaEscala&codigo="+document.getElementById("configSelecEscala").value
                    +"&campo="+Campo
                    +"&valor="+Valor
                    , true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    if(parseInt(Resp.coderro) === 2){
                                        obj.checked = true;
                                        $.confirm({
                                            title: 'Ação Suspensa!',
                                            content: 'Não restaria outro marcado para gerenciar a escala.',
                                            draggable: true,
                                            buttons: {
                                                OK: function(){}
                                            }
                                        });
                                        return false;
                                    }else{
                                        $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));
                                        $('#mensagemConfig').fadeIn("slow");
                                        document.getElementById("mensagemConfig").innerHTML = "Valor salvo.";
                                        $('#mensagemConfig').fadeOut(1000);
                                    }
                                }

                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function mudaTurno(CodPartic, CodTurno){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=salvaTurno&codpartic="+CodPartic+"&codturno="+CodTurno, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
//                                    $("#relacaoParticip").load("modulos/quadroHorario/jEquipes.php?codigo="+document.getElementById("guardanumgrupo").value);
                               }
                            }
                        }
                    };
                    ajax.send(null);
                }

            }


            function insParticipante(){
                //Cod = pessoas_id de poslog
                //GuardaCod = id de escalas
                //GuardaTurno =  1 a 4 de onde clica
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=insParticipante"
                    +"&diaIdEscala="+document.getElementById("guardaDiaId").value
                    , true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else if(parseInt(Resp.coderro) === 2){
                                    $.confirm({
                                        title: 'Atenção!',
                                        content: 'É necessário inserir os horários dos turnos.',
                                        autoClose: 'OK|10000',
                                        draggable: true,
                                        buttons: {
                                            OK: function(){}
                                        }
                                    });
                                    return false;
                                }else if(parseInt(Resp.coderro) === 3){ // escalado em outro grupo
                                    $.confirm({
                                        title: 'Atenção!',
                                        content: 'Parece que este participante já está escalado neste mesmo dia em outro grupo: '+Resp.siglagrupo,
                                        draggable: true,
                                        buttons: {
                                            OK: function(){}
                                        }
                                    });
                                    $("#faixacentral").load("modulos/QuadroHorario/jQuadro.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));
//                                    $("#estat").load("modulos/quadroHorario/jCarga.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));
                                    document.getElementById("relacParticip").style.display = "none";
                                }else{
                                    $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));
//                                    $("#estat").load("modulos/quadroHorario/jCarga.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));
                                    document.getElementById("relacParticip").style.display = "none";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }


            function abreEscalaConfig(){
                document.getElementById("checkefetivo").checked = false;
                document.getElementById("checkescalante").checked = false;
                document.getElementById("configCpfEscala").value = "";
                document.getElementById("configSelecEscala").value = "";
                document.getElementById("modalEscalaConfig").style.display = "block";
            }
            function fechaEscalaConfig(){
                document.getElementById("modalEscalaConfig").style.display = "none";
            }
            function fechaRelaPart(){
                document.getElementById("relacParticip").style.display = "none";
            }
            function resumoUsuEscala(){
                window.open("modulos/escaladaf/imprUsuEsc.php?acao=listaUsuarios", "EscalaUsu");
            }

            function format_CnpjCpf(value){
                //https://gist.github.com/davidalves1/3c98ef866bad4aba3987e7671e404c1e
                const CPF_LENGTH = 11;
                const cnpjCpf = value.replace(/\D/g, '');
                if (cnpjCpf.length === CPF_LENGTH) {
                    return cnpjCpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/g, "\$1.\$2.\$3-\$4");
                } 
                  return cnpjCpf.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/g, "\$1.\$2.\$3/\$4-\$5");
            }

        </script>
    </head>
    <body>
        <?php
            require_once(dirname(dirname(__FILE__))."/config/abrealas.php");

//Provisórios

//    pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".escaladaf");
    pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escaladaf (
        id SERIAL PRIMARY KEY, 
        dataescala date DEFAULT '3000-12-31',
        ativo smallint DEFAULT 1 NOT NULL, 
        usuins integer DEFAULT 0 NOT NULL,
        datains timestamp without time zone DEFAULT '3000-12-31',
        usuedit integer DEFAULT 0 NOT NULL,
        dataedit timestamp without time zone DEFAULT '3000-12-31' 
        ) 
    ");

//    pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".escaladaf_ins");
    pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escaladaf_ins (
        id SERIAL PRIMARY KEY, 
        escaladaf_id bigint NOT NULL DEFAULT 0,
        dataescalains date DEFAULT '3000-12-31',
        poslog_id INT NOT NULL DEFAULT 0,
        letraturno VARCHAR(3), 
        turnoturno VARCHAR(30), 
        ativo smallint NOT NULL DEFAULT 1, 
        usuins bigint NOT NULL DEFAULT 0, 
        datains timestamp without time zone DEFAULT '3000-12-31', 
        usuedit bigint NOT NULL DEFAULT 0, 
        dataedit timestamp without time zone DEFAULT '3000-12-31' 
        )
    ");

    //pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".escaladaf_turnos");
    pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escaladaf_turnos (
        id SERIAL PRIMARY KEY, 
        letra VARCHAR(3), 
        horaturno VARCHAR(30), 
        ativo smallint NOT NULL DEFAULT 1, 
        usuins bigint NOT NULL DEFAULT 0,
        datains timestamp without time zone DEFAULT '3000-12-31',
        usuedit bigint NOT NULL DEFAULT 0,
        dataedit timestamp without time zone DEFAULT '3000-12-31' 
        ) 
    ");
    $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf_turnos LIMIT 3");
    $row = pg_num_rows($rs);
    if($row == 0){
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, usuins, datains) VALUES(1, 'F', 'FÉRIAS', 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, usuins, datains) VALUES(2, 'X', 'FOLGA', 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, usuins, datains) VALUES(3, 'Y', 'INSS', 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, usuins, datains) VALUES(4, 'Q', 'AULA IAQ', 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, usuins, datains) VALUES(5, 'A', '08:00 / 17:00', 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, usuins, datains) VALUES(6, 'B', '07:00 / 16:00', 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, usuins, datains) VALUES(7, 'C', '07:00 / 17:00', 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, usuins, datains) VALUES(8, 'E', '09:00 / 18:00', 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, usuins, datains) VALUES(9, 'H', '14:00 / 18:00', 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, usuins, datains) VALUES(10, 'D', '11:00 / 15:00', 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, usuins, datains) VALUES(11, 'K', '08:00 / 14:15', 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, usuins, datains) VALUES(12, 'J', '06:50 / 15:50', 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, usuins, datains) VALUES(13, 'G', '10:50 / 19:50', 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, usuins, datains) VALUES(14, 'L', '07:00 / 13:15', 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, usuins, datains) VALUES(15, 'M', '13:35 / 19:50', 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, usuins, datains) VALUES(16, 'O', '08:00 / 18:00', 3, NOW() )");
    }


//------------

 
    $Escalante = parEsc("esc_daf", $Conec, $xProj, $_SESSION["usuarioID"]);


    $DiaIni = strtotime(date('Y/m/01')); // número - para começar com o dia 1
    $DiaIni = strtotime("-1 day", $DiaIni); // para começar com o dia 1 no loop for
    $ParamIni = date("m/Y");


    $OpcoesEscMes = pg_query($Conec, "SELECT CONCAT(TO_CHAR(dataescala, 'MM'), '/', TO_CHAR(dataescala, 'YYYY')) 
    FROM ".$xProj.".escaladaf GROUP BY TO_CHAR(dataescala, 'MM'), TO_CHAR(dataescala, 'YYYY') ORDER BY TO_CHAR(dataescala, 'YYYY') DESC, TO_CHAR(dataescala, 'MM') DESC ");
    $OpConfig = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl, nomeusual");


//    $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);

    //Mantem a tabela meses à frente
    for($i = 0; $i < 180; $i++){
        $Amanha = strtotime("+1 day", $DiaIni);
        $DiaIni = $Amanha;
        $Data = date("Y/m/d", $Amanha); // data legível
        $rs0 = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf WHERE dataescala = '$Data' ");
        $row0 = pg_num_rows($rs0);
        if($row0 == 0){
            pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf (dataescala) VALUES ('$Data')");
        }
    }
            
    ?>

        <input type="hidden" id="guardamesano" value="<?php echo $ParamIni; ?>" />
        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"]; ?>" />
        <input type="hidden" id="escalante" value="<?php echo $Escalante; ?>" />
        <input type="hidden" id="guardaDiaId" value="" />
        <input type="hidden" id="guardaUsuId" value="" />


        <div style="margin: 5px; border: 2px solid green; border-radius: 15px; padding: 5px;">
            <div class="row"> <!-- botões Inserir e Imprimir-->
                <div class="col" style="margin: 0 auto; text-align: left;" title="Inserir leitura do medidor de energia elétrica">
                    <img src="imagens/settings.png" height="20px;" id="imgEscalaConfig" style="cursor: pointer; padding-left: 30px;" onclick="abreEscalaConfig();" title="Configurar o acesso ao processamento">
                    <label style="padding-left: 40px;">Selecione o mês: </label>
                    <select id="selecMesAnoEsc" style="font-size: 1rem; width: 90px;" title="Selecione o mês/ano.">
                        <option value=""></option>
                            <?php 
                                if($OpcoesEscMes){
                                    while ($Opcoes = pg_fetch_row($OpcoesEscMes)){ ?>
                                        <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[0]; ?></option>
                                        <?php 
                                    }
                                }
                            ?>
                    </select>
                </div> <!-- quadro -->

                <div class="col" style="text-align: center;">Escala de Serviço DAF</div> <!-- espaçamento entre colunas  -->
                <div class="col" style="margin: 0 auto; text-align: center;">
                    <button class="botpadrblue" onclick="abreModal();">Participantes</button>
                    <button id="botImprimir" class="botpadrred" onclick="abreImprLeitura();">PDF</button>
                </div> <!-- quadro -->
            </div>
        </div>


        <div style="margin: 10px; border: 2px solid green; border-radius: 15px; padding: 10px; min-height: 70px; text-align: center;">
            <table style="margin: 0 auto; width: 90%;">
                <tr>
                <td>
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col quadro"></div>
                        <div class="col quadro" style="text-align: center;"></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col quadro" style="position: relative; float: rigth; text-align: right;"></div> 
                    </div>
                </div>
                    </td>
                </tr>
            </table>
            <div id="faixacentral"></div>
        </div>



        <!-- div modal relacionar escalado -->
        <div id="relacParticip" class="relacmodal">
            <div class="modal-content-relacParticip">
                <span class="close" onclick="fechaRelaPart();">&times;</span>
                <label style="color: #666;">Escala para o dia: &nbsp; </label><label id="titulomodal" style="color: #666; padding-bottom: 10px;"></label>
                <label id="turnotitmodal" style="color: #666; padding-bottom: 10px;"></label>
                <label id="nometitmodal" style="color: #666; padding-bottom: 10px; font-weight: bold;"></label>
                <label id="retiranomemodal" style="color: blue; text-decoration: underline; padding-bottom: 10px; padding-left: 30px; cursor: pointer;" onclick="apagaEscalado();" title="retira da escala deste dia e turno"></label>

                <!-- lista dos participantes da escala do grupo -->
                <div id="relacaoParticip" style="border: 2px solid #C6E2FF; border-radius: 10px;"></div>

                <button class="botpadrblue" style="font-size: 80%;" onclick="insParticipante();">Inserir Marcados</button>
            </div>
        </div> <!-- Fim Modal-->


         <!-- Modal configuração-->
         <div id="modalEscalaConfig" class="relacmodal">
            <div class="modal-content-escalaControle">
                <span class="close" onclick="fechaEscalaConfig();">&times;</span>
                <!-- div três colunas -->
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col quadro" style="margin: 0 auto;"></div>
                        <div class="col quadro"><h5 style="text-align: center; color: #666;">Escala DAF</h5></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col quadro" style="margin: 0 auto; text-align: center;"><button class="botpadrred" style="font-size: 70%;" onclick="resumoUsuEscala();">Resumo em PDF</button></div> 
                    </div>
                </div>
                <label class="etiqAzul">Selecione um usuário para ver a configuração:</label>
                <div style="position: relative; float: right; color: red; font-weight: bold; padding-right: 200px;" id="mensagemConfig"></div>
                <table style="margin: 0 auto; width: 85%;">
                    <tr>
                        <td colspan="4" style="text-align: center;"></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: center;">Busca Nome ou CPF do Usuário</td>
                    </tr>
                    <tr>
                        <td class="etiqAzul">Procura nome: </td>
                        <td style="width: 100px;">
                            <select id="configSelecEscala" style="max-width: 230px;" onchange="modif();" title="Selecione um usuário.">
                                <option value=""></option>
                                <?php 
                                if($OpConfig){
                                    while ($Opcoes = pg_fetch_row($OpConfig)){ ?>
                                        <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; if($Opcoes[2] != ""){echo " - ".$Opcoes[2];} ?></option>
                                    <?php 
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td class="etiqAzul"><label class="etiqAzul">ou CPF:</label></td>
                        <td>
                            <input type="text" id="configCpfEscala" style="width: 130px; text-align: center; border: 1px solid #666; border-radius: 5px;" onkeypress="if(event.keyCode===13){javascript:foco('configSelecEscala');return false;}" title="Procura por CPF. Digite o CPF."/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: center; padding-top: 10px;"></td>
                    </tr>
                </table>

                <table style="margin: 0 auto; width: 85%;">
                    <tr>
                        <td class="etiq80">Escala DAF:</td>
                        <td colspan="4">
                            <input type="checkbox" id="checkefetivo" onchange="marcaConfigEscala(this, 'eft_daf');" >
                            <label for="checkefetivo">efetivo da escala</label>
                        </td>
                    </tr>

                    <tr>
                        <td class="etiq80">Escala DAF:</td>
                        <td colspan="4">
                            <input type="checkbox" id="checkescalante" onchange="marcaConfigEscala(this, 'esc_daf');" >
                            <label for="checkescalante">escalante</label>
                        </td>
                    </tr>


                </table>
            </div>
        </div> <!-- Fim Modal-->

    </body>
</html>