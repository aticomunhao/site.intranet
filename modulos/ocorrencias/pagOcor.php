<?php
session_start(); 
if(!isset($_SESSION["usuarioID"])){
    session_destroy();
    header("Location: ../../index.php");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
        <link rel="stylesheet" type="text/css" media="screen" href="class/dataTable/datatables.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-confirm.min.css" />
        <script src="class/tinymce5/tinymce.min.js"></script>
        <script src="comp/js/jquery.min.js"></script> <!-- versão 3.6.3 -->
        <script src="comp/js/jquery.mask.js"></script>
        <script src="class/dataTable/datatables.min.js"></script>
        <script src="comp/js/jquery-confirm.min.js"></script>   <!-- https://craftpip.github.io/jquery-confirm/#quickfeatures -->

        <style type="text/css">
            #container5{
                width: 25%;
                min-height: 300px;
                margin: 10px;
                margin-top: 12px;
                border: 2px solid; 
                border-radius: 10px; 
                padding: 10px;
            }
            #container6{
                width: 70%;
                min-height: 300px;
                margin: 10px;
                margin-top: 12px;
                border: 2px solid; 
                border-radius: 10px; 
                padding: 10px;
            }
            .cContainer{ /* encapsula uma frase no topo de uma div em reArq.php e pagArq.php */
                position: absolute; 
                left: 20px;
                margin-top: -20px; 
                border: 1px solid blue; 
                border-radius: 10px; 
                padding-left: 10px; 
                padding-right: 10px; 
            }
            .bContainer{ /* botão upload */
                position: absolute; 
                right: 30px;
                margin-top: -20px; 
                border: 1px solid blue;
                background-color: blue;
                color: white;
                cursor: pointer;
                border-radius: 10px; 
                padding-left: 10px; 
                padding-right: 10px; 
            }
            .modalMsg-content{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 7% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 50%; /* acertar de acordo com a tela */
            }
            .ideogr{
                display: inline;
                margin: 5px;
            }
        </style>
        <script type="text/javascript">
            new DataTable('#idTabela', {
                columnDefs: [
                {
                    targets: [2],
                    orderData: [1, 0]
                },
                {
                    targets: [5],
                    "orderable": false
                },
                {
                    targets: [6],
                    "orderable": false
                }
                ],
                lengthMenu: [
                    [50, 100, 200, 500],
                    [50, 100, 200, 500]
                ],
                language: {
                    info: 'Mostrando Página _PAGE_ of _PAGES_',
                    infoEmpty: 'Nenhum registro encontrado',
                    infoFiltered: '(filtrado de _MAX_ registros)',
                    lengthMenu: 'Mostrando _MENU_ registros por página',
                    zeroRecords: 'Nada foi encontrado'
                }
            });

            table = new DataTable('#idTabela');
            table.on('click', 'tbody tr', function () {
                data = table.row(this).data();
                $id = data[1];
                document.getElementById("guardacod").value = $id;                
                if($id !== 0){ // se nível adm for igual ao inserido em Parâmetros do Sistema ou se foi o prósprio usuário que inseriu a ocorrência
                    if(parseInt(document.getElementById("UsuAdm").value) >= parseInt(document.getElementById("admEdit").value) || parseInt(document.getElementById("usuarioID").value) === parseInt(data[7])){
                        carregaModal($id);
                    }

                }
            });

            function ajaxIni(){
                try{
                    ajax = new ActiveXObject("Microsoft.XMLHTTP");}
                    catch(e){
                        try{
                            ajax = new ActiveXObject("Msxml2.XMLHTTP");}
                        catch(ex) {
                            try{
                                ajax = new XMLHttpRequest();}
                            catch(exc){
                                alert("Esse browser não tem recursos para uso do Ajax");
                                ajax = null;
                        }
                    }
                }
            }
            $(document).ready(function(){
                if(parseInt(document.getElementById("UsuAdm").value) < parseInt(document.getElementById("admInsOcor").value)){
                    document.getElementById("botinserir").style.visibility = "hidden"; // botão de inserir
                }else{
                    document.getElementById("botinserir").style.visibility = "visible"; // botão de inserir
                }
                if(parseInt(document.getElementById("UsuAdm").value) >= parseInt(document.getElementById("admEdit").value)){
                    document.getElementById("botimpr").style.visibility = "visible"; // botão de imprimir
                }else{
                    document.getElementById("botimpr").style.visibility = "hidden"; // botão de imprimir
                }
                //Fecha caixa ao clicar na página
                modalHelp = document.getElementById('relacHelpOcorrencias'); //span[0]
                spanHelp = document.getElementsByClassName("close")[0];
                window.onclick = function(event){
                    if(event.target === modalHelp){
                        modalHelp.style.display = "none";
                    }
                };
            });

            function regOcor(){ //registar ocorrência
                $("#container5").load("modulos/ocorrencias/relIdeogr.php");
                $("#container6").load("modulos/ocorrencias/insOcor.php");
                document.getElementById("guardacod").value = 0;  // novo lançamento
                document.getElementById("relacmodalOcor").style.display = "block";
            }

            function fechaModal(){
                if(parseInt(document.getElementById("mudou").value) === 1){
                    $.confirm({
                        title: 'Sair sem salvar?',
                        content: 'Há modificações que não foram salvas. As informações serão PERDIDAS.  Fechar mesmo assim?',
                        draggable: true,
                        buttons: {
                            Sim: function () {
                                ajaxIni();
                                if(ajax){
                                    ajax.open("POST", "modulos/ocorrencias/salvaOcor.php?acao=sairSemSalvar", true);
                                    ajax.onreadystatechange = function(){
                                        if(ajax.readyState === 4 ){
                                            if(ajax.responseText){
//alert(ajax.responseText);
                                                Resp = eval("(" + ajax.responseText + ")");
                                                if(parseInt(Resp.coderro) === 1){
                                                    alert("Houve um erro no servidor.")
                                                }else{
                                                    document.getElementById("mudou").value = "0";
                                                    document.getElementById("guardacod").value = "0";
                                                    document.getElementById("relacmodalOcor").style.display = "none";
                                                }
                                            }
                                        }
                                    };
                                    ajax.send(null);
                                }
                            },
                            Não: function () {
                            }
                        }
                    });
                }else if(parseInt(document.getElementById("mudou").value) === 2){
                    $('#container3').load('modulos/ocorrencias/pagOcor.php');
                }else{
                    document.getElementById("mudou").value = "0";
                    document.getElementById("relacmodalOcor").style.display = "none";
                }
            }

            function salvaModal(){
                if(document.getElementById("textoocorrencia").value == "" && parseInt(document.getElementById("temImagem").value) === 0){
                    alert("Escreva algo ou insira uma imagem.");
                    document.getElementById("textoocorrencia").focus();
                    return false;
                }
                if(document.getElementById("textoocorrencia").value == "" && parseInt(document.getElementById("temImagem").value) > 0){
                    document.getElementById("textoocorrencia").value = "(a) "+document.getElementById("nomeUsuLogado").value;
                }

                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/ocorrencias/salvaOcor.php?acao=salvaOcor&dataocor="+encodeURIComponent(document.getElementById("dataocor").value)+"&textoocorrencia="+encodeURIComponent(document.getElementById("textoocorrencia").value)+"&codigo="+document.getElementById("guardacod").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    document.getElementById("mudou").value = "0";
                                    if(parseInt(Resp.codigonovo) !== 0){
                                        document.getElementById("guardacod").value = Resp.codigonovo;
                                    }
                                    document.getElementById("relacmodalOcor").style.display = "none";
                                    $('#container3').load('modulos/ocorrencias/pagOcor.php');
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function modif(){ // assinala se houve qualquer modificação nos campos do modal
                document.getElementById("mudou").value = "1";
            }

            function carregaModal(Cod){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/ocorrencias/salvaOcor.php?acao=buscaOcorr&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    document.getElementById("guardacod").value = Cod;
                                    $("#container5").load("modulos/ocorrencias/relIdeogr.php");
                                    $("#container6").load("modulos/ocorrencias/insOcor.php");
//                                    a= confirm("Confirma editar esta ocorrência?");
//                                    if(a){
                                    $.confirm({
                                        title: 'Confirmação!',
                                        content: 'Confirma editar esta ocorrência?',
                                        draggable: true,
                                        buttons: {
                                            Sim: function () {
                                                $("#mostraideogr").load("modulos/ocorrencias/carIdeogr.php?codocor="+Cod);
                                                document.getElementById("relacmodalOcor").style.display = "block";
                                                document.getElementById("dataocor").value = Resp.data;
                                                document.getElementById("textoocorrencia").value = Resp.texto;
                                                if(parseInt(document.getElementById("guardacod").value) > 0){ //  ocorrência já registrada
                                                    document.getElementById("etiqnomeusuins").innerHTML = "Ocorrência registrada por: "+Resp.nomeusuins;
                                                }
                                            },
                                            Não: function () {
//                                                $.alert('qq coisa');
                                            }
                                        }
                                    });
//                                    }
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function apagaArq(Cod){
                $.confirm({
                    title: 'Apagar',
                    content: 'Confirma apagar esta imagem?',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/ocorrencias/salvaOcor.php?acao=apagaIdeogr&codigo="+Cod, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                document.getElementById("mudou").value = "2";
                                                $("#mostraideogr").load("modulos/ocorrencias/carIdeogr.php?codocor="+document.getElementById("guardacod").value);
                                            }
                                        }
                                    }
                                };
                                ajax.send(null);
                            }
                        },
                        Não: function () {
                        }
                    }
                });
            }
            function imprOcor(){
                if(parseInt(document.getElementById("guardacod").value) != 0){
                    window.open("modulos/ocorrencias/imprOcor.php?acao=impr&codigo="+document.getElementById("guardacod").value, document.getElementById("guardacod").value);
                }
            }
            function carregaHelpOcor(){
                document.getElementById("relacHelpOcorrencias").style.display = "block";
            }
            function fechaModalHelp(){
                document.getElementById("relacHelpOcorrencias").style.display = "none";
            }
        </script>
    </head>
    <body>
        <div style="margin: 6px; padding: 10px; min-height: 500px; border: 2px solid blue; border-radius: 15px; text-align: center;">
        <!-- div três colunas -->
        <div class="container" style="margin: 0 auto;">
            <div class="row">
                <div class="col quadro" style="margin: 0 auto;"> <input type="button" id="botinserir" class="resetbot" value="Registrar Ocorrência" onclick="regOcor();"></div>
                <div class="col quadro"><h3>Registro de Ocorrências</h3></div> <!-- Central - espaçamento entre colunas  -->
                <div class="col quadro" style="margin: 0 auto; text-align: right;"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpOcor();" title="Guia rápido"></div> 
            </div>
        </div>
            <hr>
            <?php
            date_default_timezone_set('America/Sao_Paulo');
            require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
            $admIns = parAdm("insocor", $Conec, $xProj);   // nível para inserir 
            $admEdit = parAdm("editocor", $Conec, $xProj); // nível para editar
            $hoje = date('d/m/Y');
            $folder = "modulos/ocorrencias/imagens/";

            //Conta quantos registros já fez
            $rsQuant = pg_query($Conec, "SELECT codocor FROM ".$xProj.".ocorrencias WHERE ativo = 1 And usuins = ".$_SESSION["usuarioID"]." ORDER BY datains");
            $Quant = pg_num_rows($rsQuant);
    
            pg_query($Conec, "DELETE FROM ".$xProj.".ocorrideogr WHERE codprov = ".$_SESSION['usuarioID']); // limpar restos
            if($_SESSION["AdmUsu"] >= $admEdit){
                $rs0 = pg_query($Conec, "SELECT codocor, numocor, to_char(datains, 'DD/MM/YYYY'), usuins, to_char(dataocor, 'DD/MM/YYYY'), ocorrencia FROM ".$xProj.".ocorrencias WHERE ativo = 1 And AGE(datains, CURRENT_DATE) <= '10 YEAR' ORDER BY datains DESC LIMIT 50");
            }else{    
                $rs0 = pg_query($Conec, "SELECT codocor, numocor, to_char(datains, 'DD/MM/YYYY'), usuins, to_char(dataocor, 'DD/MM/YYYY'), ocorrencia FROM ".$xProj.".ocorrencias WHERE usuins = ".$_SESSION["usuarioID"]." And ativo = 1 And AGE(datains, CURRENT_DATE) <= '10 YEAR' ORDER BY datains DESC");
            }
            $row0 = pg_num_rows($rs0);
            ?>
                <table id="idTabela" class="display" style="width:85%;">
                <thead>
                    <tr>
                        <th style="display: none;">-</th>
                        <th style="display: none;"></th>
                        <th style="text-align: center; font-size: 80%;">Nº da<br>Ocorrência</th>
                        <th style="text-align: center; font-size: 80%;">Data da<br>Inserção</th>
                        <th style="text-align: center; font-size: 80%;">Data da<br>Ocorrência</th>
                        <th style="text-align: center; font-size: 80%;">Ideogramas</th>
                        <th style="text-align: center;">Ocorrência</th>
                        <th style="text-align: center; font-size: 80%;">Usuário</th>
                    </tr>
                </thead>
                <tbody>

                <?php 
                while ($Tbl0 = pg_fetch_row($rs0)){
                    $CodOcor = $Tbl0[0]; // codocor
                    $NumOcor = $Tbl0[1]; // NumOcor
                    $DataIns = $Tbl0[2]; // DataIns
                    $CodUsu = $Tbl0[3];  // usuIns

                    if($DataIns == "31/12/3000"){
                        $DataIns = "";
                    }
                    $DataOcor = $Tbl0[4]; // DataOcorrencia
                    if(is_null($Tbl0[5])){  // Ocorrencia
                        $Ocor = "";    
                    }else{
                        $Ocor = nl2br($Tbl0[5]); // Ocorrencia
                    }
                    ?>
                    <tr>
                        <td style="display: none;"></td>
                        <td style="display: none;"><?php echo $CodOcor; ?></td>
                        <td><?php echo $NumOcor; ?></td>
                        <td><?php echo $DataIns; ?></td>
                        <td><?php echo $DataOcor; ?></td>
                        <td><?php  
                        $rsIdeo = pg_query($Conec, "SELECT descideo FROM ".$xProj.".ocorrideogr WHERE coddaocor = $CodOcor ORDER BY codideo");
                        $rowIdeo = pg_num_rows($rsIdeo);
                        if($rowIdeo > 0){
                            while ($TblIdeo = pg_fetch_row($rsIdeo)){
                                $Ideogr = $folder.$TblIdeo[0];
                                echo "<div style='display: inline; padding: 2px;'><img src='$Ideogr' width='40px' height='40px;'></div>";
                            }
                        }
                        ?>
                        </td>
                        <td style="text-align: left;" title="Clique para editar."><?php echo $Ocor; ?></td>
                        <td style="text-align: center;"><?php echo $CodUsu; ?></td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"] ?>" />
        <input type="hidden" id="usuarioID" value="<?php echo $_SESSION["usuarioID"] ?>" />
        <input type="hidden" id="admInsOcor" value="<?php echo $admIns ?>" />
        <input type="hidden" id="admIns" value="<?php echo $admIns; ?>" /> <!-- nível mínimo para inserir  -->
        <input type="hidden" id="admEdit" value="<?php echo $admEdit; ?>" /> <!-- nível mínimo para editar -->
        <input type="hidden" id="guardacod" value="0" /> <!-- id ocorrência -->
        <input type="hidden" id="mudou" value="0" />
        <input type="hidden" id="nomeUsuLogado" value="<?php echo $_SESSION["NomeCompl"] ?>" />

        <!-- div modal para registrar ocorrência  -->
        <div id="relacmodalOcor" class="relacmodal">
            <div class="modal-content-InsOcor">
                <div style="position: absolute;"><button class="botpadrred" style="font-size: 80%;" id="botimpr" onclick="imprOcor();">PDF</button></div>
                <span class="close" onclick="fechaModal();">&times;</span>
                <h3 id="titulomodal" style="text-align: center; color: #666;">Registrar Ocorrência</h3>
                <div style="border: 2px solid blue; border-radius: 10px;">
                    <div id="container5"></div>
                    <div id="container6"></div>
                    <div style="text-align: center; padding-bottom: 20px;">
                        <button class="botpadrblue" onclick="salvaModal();">Salvar</button>
                    </div>
                </div>
           </div>
        </div> <!-- Fim Modal-->

        <!-- div modal para leitura instruções -->
        <div id="relacHelpOcorrencias" class="relacmodal">
            <div class="modalMsg-content">
                <span class="close" onclick="fechaModalHelp();">&times;</span>
                <h3 style="text-align: center; color: #666;">Informações</h3>
                <h4 style="text-align: center; color: #666;">Registro de Ocorrências</h4>
                <div style="border: 1px solid; border-radius: 10px; margin: 5px; padding: 5px;">
                    Regras inseridas:
                    <ul>
                        <li>1 - Uma ocorrência registrada só é visível para o usuário que a inseriu e para os administradores designados.</li>
                        <li>2 - O usuário que inseriu pode editar o relato já registrado.</li>
                        <li>3 - As imagens (ideogramas) servem para dar uma ideia inicial do ocorrido, antes de se efetuar a leitura.</li>
                        <li>4 - Do quadro da esquerda, os ideogramas podem ser arrastados com o mouse para o quadro da direita.</li>
                    </ul>
                </div>
            </div>
        </div>  <!-- Fim Modal Help-->
    </body>
</html>