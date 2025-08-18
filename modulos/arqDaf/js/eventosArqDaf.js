/* Ludinir
 *  Julho/2025 para o site CEsB/ArqDaf 
*/

function openhref(Num){
    document.getElementsByTagName("body")[0].style.background = "#FFFAFA"
    if(parseInt(Num) === 98){
        location.replace("canseiArqDaf.php"); // location.replace(-> abre na mesma aba
    }
    if(parseInt(Num) === 99){
        document.getElementById("relacmodalLog").style.display = "block";
        document.getElementById("usuario").value = "";
        document.getElementById("senha").value = "";
        document.getElementById("usuario").focus();
    }
    if(parseInt(Num) === 51){ // inicio
        location.replace("indexc.php");
    }
    if(parseInt(Num) === 52){ // inicio
        location.replace("indexd.php");
    }

    if(parseInt(Num) === 61){
        $('#container3').load('config/cadUsuArqDaf.php');
    }
    if(parseInt(Num) === 62){
        $('#container3').load('config/tpassArqDaf.php');
    }
}

function openhrefDir(Num){
    $("#container3").load("conteudo/PagDir.php?Diretoria="+Num+"&Subdiretoria=1");
}