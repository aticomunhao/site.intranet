

html
        $(document).ready(function(){
            //Fecha a caixa ao clicar na página
            modalMsg = document.getElementById('relacmodalMsg'); //span[0] fecha ao clicar na página
            spanMsg = document.getElementsByClassName("close")[0];
            window.onclick = function(event){
                if(event.target === modalMsg){
                    modalMsg.style.display = "none";
                }
            };
        });



<?php


foreach ($Busca as $Valor){
        if($Valor == $Seq){
            $Erro = 2;  //echo "Sequencial <br>";
        }else{
            $Erro = 0;
        }
        $Seq++;
    }
    $Busca = str_split($Sen, 1);
    $Seq = $Busca[0];
    foreach ($Busca as $Valor){
        if($Valor == $Seq){
            $Erro = 2;  //echo "Sequencial <br>";
        }else{
            $Erro = 0;
        }
        $Seq--;
    }

?>

    <div id="relacmodalLog" class="relacmodal">  <!-- ("close")[0] -->
    <div class="modal-content-Login">
        <span class="close" onclick="fechaModalLog();">&times;</span>
        <div class="caixalog">
            <h2><img src="imagens/LogoComunhao.png" height="40px;"> Login</h2>
            <p>Por favor, preencha os campos abaixo.</p>
            <div class="mb-3">
                <label>Usuário</label>
                <input type="text" id="usuario" class="form-control" value="" onkeypress="if(event.keyCode===13){javascript:foco('senha');return false;}">
            </div>
            <div class="mb-3" style="padding-top: 5px;">
                <label>Senha</label>
                <input type="password" id="senha" class="form-control" value="" onkeypress="if(event.keyCode===13){javascript:foco('entrar');return false;}">
                <span class="invalid-feedback"></span>
            </div>
            <table style="margin: 0 auto; width: 90%">
                <tr>
                    <td style="text-align: center; padding-top: 5px;"><div id="mensagem" style="color: red; font-weight: bold;"></div></td>
                <tr>
                    <td style="text-align: center; padding-top: 10px;"><input type="button" class="btn btn-primary resetbot" id="entrar" value="Entrar" onclick="logModal();"></td>
                </tr>
            </table>
        </div>
   </div>
</div> <!-- Fim Modal-->