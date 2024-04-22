<?php
session_start(); 
if(!isset($_SESSION["usuarioID"])){
    header("Location: /cesb/index.php");
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
            .ideogr{
                display: inline;
                margin: 5px;
            }
            tr td{
                border: 0px solid;
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
                if($id !== 0){
                    if(parseInt(document.getElementById("UsuAdm").value) >= parseInt(document.getElementById("admEditOcor").value)){
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
                } 
            });

            function regOcor(){
                $("#container5").load("modulos/ocorrencias/relIdeogr.php");
                $("#container6").load("modulos/ocorrencias/insOcor.php");
                document.getElementById("guardacod").value = 0;  // novo lançamento
                document.getElementById("relacmodalOcor").style.display = "block";
            }

            function fechaModal(){
                if(parseInt(document.getElementById("mudou").value) === 1){
                    $.confirm({
                        title: 'Sair sem salvar?',
                        content: 'Há modificações que não foram salvas. As informações serão PERDIDAS. Continua?',
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
//                                                $.alert('Something else?');
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
        </script>
    </head>
    <body>
        <div style="margin: 6px; padding: 10px; min-height: 500px; border: 2px solid blue; border-radius: 15px; text-align: center;">
            <h3>Registro de Ocorrências</h3>
            <div class="box" style="position: absolute; left: 30px; top: 25px;">
                <input type="button" id="botinserir" class="resetbot" value="Registrar Ocorrência" onclick="regOcor();">
            </div>
            <hr>

            <?php
            date_default_timezone_set('America/Sao_Paulo');
            require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
            $admIns = parAdm("insocor", $Conec, $xProj);   // nível para inserir 
            $admEdit = parAdm("editocor", $Conec, $xProj); // nível para editar
            $hoje = date('d/m/Y');

            //Conta quantos registros já fez
            $rsQuant = pg_query($Conec, "SELECT codocor FROM ".$xProj.".ocorrencias WHERE ativo = 1 And usuins = ".$_SESSION["usuarioID"]." ORDER BY datains");
            $Quant = pg_num_rows($rsQuant);
    
            pg_query($Conec, "DELETE FROM ".$xProj.".ocorrideogr WHERE codprov = ".$_SESSION['usuarioID']); // limpar restos
            if($_SESSION["AdmUsu"] > 6){
                $rs0 = pg_query($Conec, "SELECT codocor, numocor, to_char(datains, 'DD/MM/YYYY'), usuins, to_char(dataocor, 'DD/MM/YYYY'), ocorrencia FROM ".$xProj.".ocorrencias WHERE ativo = 1 ORDER BY datains DESC LIMIT 50");
            }else{    
                $rs0 = pg_query($Conec, "SELECT codocor, numocor, to_char(datains, 'DD/MM/YYYY'), usuins, to_char(dataocor, 'DD/MM/YYYY'), ocorrencia FROM ".$xProj.".ocorrencias WHERE usuins = ".$_SESSION["usuarioID"]." And ativo = 1 ORDER BY datains DESC");
            }
            $row0 = pg_num_rows($rs0);
//            if($row0 > 0){
                ?>
                <table id="idTabela" class="display" style="width:85%">
                <thead>
                    <tr>
                        <th style="display: none;">-</th>
                        <th style="display: none;"></th>
                        <th style="text-align: center;">Nº Ocorrência</th>
                        <th style="text-align: center;">Data Ins</th>
                        <th style="text-align: center;">Data Ocor</th>
                        <th style="text-align: center;">Ideogramas</th>
                        <th style="text-align: center;">Ocorrência</th>
                        <th style="text-align: center;">Usuário</th>
<!--                        <th style="text-align: center;">Modif</th> -->
                    </tr>
                </thead>
                <tbody>

                <?php 
                while ($Tbl0 = pg_fetch_row($rs0)){
                    $CodOcor = $Tbl0[0]; // codocor
                    $NumOcor = $Tbl0[1]; // NumOcor
                    $DataIns = $Tbl0[2]; // DataIns
                    $CodUsu = $Tbl0[3];  // usuIns
                    
//                    if($DataIns == "31/12/3000 00:00"){
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
                                $Ideogr = $TblIdeo[0];
                                echo "<div style='display: inline; padding: 2px;'><img src='$Ideogr' width='40px' height='40px;'></div>";
                            }
                        }
                        ?>
                        </td>
                        <td style="text-align: left;"><?php echo $Ocor; ?></td>
                        <td style="text-align: center;"><?php echo $CodUsu; ?></td>
<!--                        <td style="text-align: center;">
                            <div title='Editar' style='cursor: pointer;' onclick='carregaModal($CodOcor);'>&#9997;</div>
                        </td>
-->
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
            <?php
//            }else{
//                echo "Nenhuma ocorrência registrada por ".$_SESSION["NomeCompl"];
//            }
            ?>
        </div>
        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"] ?>" />
        <input type="hidden" id="admInsOcor" value="<?php echo $admIns ?>" />
        <input type="hidden" id="admEditOcor" value="<?php echo $admEdit ?>" />
        <input type="hidden" id="admIns" value="<?php echo $admIns; ?>" /> <!-- nível mínimo para inserir  -->
        <input type="hidden" id="admEdit" value="<?php echo $admEdit; ?>" /> <!-- nível mínimo para editar -->
        <input type="hidden" id="guardacod" value="0" /> <!-- id ocorrência -->
        <input type="hidden" id="mudou" value="0" />
        
        <input type="hidden" id="nomeUsuLogado" value="<?php echo $_SESSION["NomeCompl"] ?>" />

        <!-- div modal para registrar ocorrêencia  -->
        <div id="relacmodalOcor" class="relacmodal">
            <div class="modal-content-InsOcor">
                <span class="close" onclick="fechaModal();">&times;</span>
                <h3 id="titulomodal" style="text-align: center; color: #666;">Registrar Ocorrência</h3>
                <div style="border: 2px solid blue; border-radius: 10px;">
                
                    <div id="container5"></div>
                    <div id="container6"></div>

                    <div style="text-align: center;">
                        <button class="resetbotazul" onclick="salvaModal();">Salvar</button>
                    </div>
                </div>
           </div>
        </div> <!-- Fim Modal-->
    </body>
</html>