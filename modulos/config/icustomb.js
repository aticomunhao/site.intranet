//para o indexb 

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
$(document).ready(function(){ 
    document.getElementById("temTarefa").style.display = "none";
    $('#container1').load('modulos/cabec.php');
    $('#container2').load('modulos/menufim.php?diasemana='+document.getElementById('guardadiasemana').value);
    $('#container4').load('modulos/rodape.php');

    ajaxIni();
    if(ajax){
        ajax.open("POST", "modulos/conteudo/salvaTarefa.php?acao=temTarefa", true);
        ajax.onreadystatechange = function(){
            if(ajax.readyState === 4 ){
                if(ajax.responseText){
//alert(ajax.responseText);
                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                    if(parseInt(Resp.coderro) === 1){
                        alert("Houve um erro no servidor ao fechar as mensagens. Informe à ATI.")
                    }else{
                        if(parseInt(Resp.tem) > 0){
                            document.getElementById("temTarefa").innerHTML = Resp.msg;
                            document.getElementById("temTarefa").style.display = "block";
                        }else{
                            document.getElementById("temTarefa").style.display = "none";
                        }
                    }
                }
            }
        };
        ajax.send(null);
    }
});