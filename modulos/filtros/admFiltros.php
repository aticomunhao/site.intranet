<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
        <script type="text/javascript">
            $(document).ready(function(){
                $("#configselecSolicitante").change(function(){
                    if(document.getElementById("configselecSolicitante").value == ""){
                        document.getElementById("configcpfsolicitante").value = "";
                        document.getElementById("registrar").checked = false;
                        document.getElementById("fiscalizar").checked = false;
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/filtros/salvaFiltros.php?acao=buscausuario&codigo="+document.getElementById("configselecSolicitante").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("configcpfsolicitante").value = format_CnpjCpf(Resp.cpf);
                                        if(parseInt(Resp.filtros) === 1){
                                            document.getElementById("registrar").checked = true;
                                        }else{
                                            document.getElementById("registrar").checked = false;
                                        }
                                        if(parseInt(Resp.fiscfiltros) === 1){
                                            document.getElementById("fiscalizar").checked = true;
                                        }else{
                                            document.getElementById("fiscalizar").checked = false;
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

                $("#configcpfsolicitante").click(function(){
                    document.getElementById("configselecSolicitante").value = "";
                    document.getElementById("configcpfsolicitante").value = "";
                    document.getElementById("registrar").checked = false;
                    document.getElementById("fiscalizar").checked = false;
                });
                $("#configcpfsolicitante").change(function(){
                    if(document.getElementById("configcpfsolicitante").value == ""){
                        document.getElementById("registrar").checked = false;
                        document.getElementById("fiscalizar").checked = false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/filtros/salvaFiltros.php?acao=buscacpfusuario&cpf="+encodeURIComponent(document.getElementById("configcpfsolicitante").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("configselecSolicitante").value = Resp.PosCod;
                                        if(parseInt(Resp.filtros) === 1){
                                            document.getElementById("registrar").checked = true;
                                        }else{
                                            document.getElementById("registrar").checked = false;
                                        }
                                        if(parseInt(Resp.fiscfiltros) === 1){
                                            document.getElementById("fiscalizar").checked = true;
                                        }else{
                                            document.getElementById("fiscalizar").checked = false;
                                        }
                                    }
                                    if(parseInt(Resp.coderro) === 2){
                                        document.getElementById("registrar").checked = false;
                                        document.getElementById("fiscalizar").checked = false;
                                        $('#mensagemConfig').fadeIn("slow");
                                        document.getElementById("mensagemConfig").innerHTML = "Não encontrado";
                                        $('#mensagemConfig').fadeOut(2000);
                                    }
                                    if(parseInt(Resp.coderro) === 1){
                                        alert("Houve um erro no servidor.")
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                });

            }); // fim do ready

            function marcaCheckBox(obj, Campo){
                if(obj.checked === true){
                    Valor = 1;
                }else{
                    Valor = 0;
                }
                if(Valor == 0){ // tirando seu próprio acesso
                    if(parseInt(document.getElementById("configselecSolicitante").value) === parseInt(document.getElementById("guardaUsuId").value)){
                        if(parseInt(document.getElementById("UsuAdm").value) < 7){ // superusuário
                            $.confirm({
                                title: 'Alerta!',
                                content: 'Você perderá o acesso a este módulo no próximo login.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                        }
                    }
                }
                if(document.getElementById("configselecSolicitante").value == ""){
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
                    ajax.open("POST", "modulos/filtros/salvaFiltros.php?acao=configMarcaCheckBox&codigo="+document.getElementById("configselecSolicitante").value
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
                                            content: 'Não restaria outro marcado para gerenciar os filtros.',
                                            draggable: true,
                                            buttons: {
                                                OK: function(){}
                                            }
                                        });
                                        return false;
                                    }else{
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
        </script>
    </head>
    <body>
        <?php
            require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
            $OpConfig = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl, nomeusual");
        ?>
        <div style="margin-top: 15px; padding: 3px; border: 1px solid #666; border-radius: 15px;">
            <div style="position: relative; float: right;"><label style="color: #666; font-size: 70%;">Superusuários</label></div>
            <label class="etiqAzul">Selecione um usuário para ver a configuração:</label>
            <div style="position: relative; float: right; color: red; font-weight: bold; padding-right: 200px;" id="mensagemConfig"></div>
            <table style="margin: 0 auto; width: 85%;">
                <tr>
                    <td colspan="4" style="text-align: center;"></td>
                </tr>
                <tr>
                    <td colspan="4" class="etiqAzul" style="text-align: center;">Busca Nome ou CPF do Usuário</td>
                </tr>
                <tr>
                    <td class="etiqAzul">Procura nome: </td>
                    <td style="width: 100px;">
                        <select id="configselecSolicitante" style="max-width: 230px;" onchange="modif();" title="Selecione um usuário.">
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
                        <input type="text" id="configcpfsolicitante" style="width: 130px; text-align: center; border: 1px solid #666; border-radius: 5px;" onkeypress="if(event.keyCode===13){javascript:foco('configselecSolicitante');return false;}" title="Procura por CPF. Digite o CPF do usuário."/>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center; padding-top: 10px;"></td>
                </tr>
            </table>
            <table style="margin: 0 auto; width: 85%;">
                <tr>
                    <td class="etiq80" title="Registrar as trocas dos elementos filtrantes dos Filtros e Purificadores">Filtros:</td>
                    <td colspan="4">
                        <input type="checkbox" id="registrar" title="Registrar as trocas dos elementos filtrantes dos Filtros e Purificadores" onchange="marcaCheckBox(this, 'filtros');" >
                        <label for="registrar" class="etiqNorm" title="Registrar as trocas dos elementos filtrantes dos Filtros e Purificadores">Registrar e administrar o funcionamento dos Filtros e Purificadores</label>
                    </td>
                </tr>
                <tr>
                    <td class="etiq80" title="Fiscalizar o funcionamento dos Filtros e Purificadores">Filtros e Purificadores: </td>
                    <td colspan="4">
                        <input type="checkbox" id="fiscalizar" title="Fiscalizar o funcionamento dos Filtros e Purificadores" onchange="marcaCheckBox(this, 'fisc_filtros');" >
                        <label for="fiscalizar" class="etiqNorm" title="Fiscalizar o funcionamento dos Filtros e Purificadores">Fiscalizar o funcionamento dos Filtros e Purificadores</label>
                    </td>
                </tr>
                <tr>
                    <td colspan="5" style="text-align: center; padding-top: 5px;"></div></td>
                <tr>
            </table>
        </div>
        <hr class="etiqNorm">
    </body>
</html>