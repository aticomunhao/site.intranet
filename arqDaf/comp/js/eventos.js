/* Ludinir
 *  Setembro/2023 para o site CEsB 
*/

function openhref(Num){
    document.getElementsByTagName("body")[0].style.background = "#FFFAFA"
    if(parseInt(Num) === 98){
        location.replace("modulos/cansei.php"); // location.replace(-> abre na mesma aba
    }
    if(parseInt(Num) === 99){
        document.getElementById("relacmodalLog").style.display = "block";
        document.getElementById("usuario").value = "";
        document.getElementById("senha").value = "";
        document.getElementById("usuario").focus();
    }
    if(parseInt(Num) === 51){ // inicio
        location.replace("index.php");
    }
    if(parseInt(Num) === 52){ // inicio
        location.replace("indexb.php");
    }


    if(parseInt(Num) === 61){
        $('#container3').load('modulos/config/cadUsu.php');
    }
    if(parseInt(Num) === 62){
        $('#container3').load('modulos/config/tpass.php');
    }


}

function openhrefDir(Num){
    $("#container3").load("modulos/conteudo/PagDir.php?Diretoria="+Num+"&Subdiretoria=1");
}