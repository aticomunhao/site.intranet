/* Ludinir
 *  Setembro/2023 para o site CEsB 
*/

function openhref(Num){
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
    if(parseInt(Num) === 53 || parseInt(Num) === 54){  // página livre ou página do logado
        $('#container3').load('modulos/organog.php');}
    if(parseInt(Num) === 55){
        $('#container3').load('modulos/ramaisInt.php?tipo=1'); // sem log
    }
    if(parseInt(Num) === 56){
        $('#container3').load('modulos/ramaisExt.php?tipo=1');
    }
    if(parseInt(Num) === 57){
        $('#container3').load('modulos/ramaisInt.php?tipo=2');  // com usu logado
    }
    if(parseInt(Num) === 58){
        $('#container3').load('modulos/ramaisExt.php?tipo=2');
    }
    if(parseInt(Num) === 59){
        $('#container3').load('modulos/aniverRel.php');
    }
    if(parseInt(Num) === 60){
        $.confirm({
            title: "Confirmação!",
            content: "Verificação das tabelas. Continua?",
            draggable: true,
            buttons: {
                Sim: function () {
                    $('#container3').load('modulos/tabelas.php');
                },
                Não: function () {
                }
            }
        });
    }
    if(parseInt(Num) === 61){
        $('#container3').load('modulos/config/cadUsu.php');
    }
    if(parseInt(Num) === 62){
        $('#container3').load('modulos/config/tpass.php');
    }
    if(parseInt(Num) === 63){
        $('#container3').load('modulos/calendario/calend.php');
    }
    if(parseInt(Num) === 64){
        $('#container3').load('modulos/bensachados/pagBensAch.php');
    }
    if(parseInt(Num) === 65){
        $('#container3').load('modulos/controleAr/controleAr.php');
    }
    if(parseInt(Num) === 66){
//        $('#container3').load('modulos/config/info.php');
        window.open("modulos/config/info.php", '_blank');
    }


    if(parseInt(Num) === 30){
        $('#container3').load('modulos/trafego/PagArq.php');
    }
    if(parseInt(Num) === 31){
        $('#container3').load('modulos/config/param.php');
    }
    if(parseInt(Num) === 32){
        $('#container3').load('modulos/slides/abreSlides.php');
    }
    if(parseInt(Num) === 33){
        $('#container3').load('modulos/ocorrencias/pagOcor.php');
    }
    if(parseInt(Num) === 34){
        $('#container3').load('modulos/leituras/pag_agua.php?tipo=1');
    }
    if(parseInt(Num) === 35){
        $('#container3').load('modulos/leituras/pag_eletric.php?tipo=1');
    }
    if(parseInt(Num) === 36){
        $('#container3').load('modulos/lro/livroReg.php');
    }
    if(parseInt(Num) === 70){
        $('#container3').load('modulos/conteudo/tarefas.php');
    }
    if(parseInt(Num) === 80){
        $('#container3').load('modulos/trocas/relTrocas.php');
    }
}

function openhrefDir(Num){
    $("#container3").load("modulos/conteudo/PagDir.php?Diretoria="+Num+"&Subdiretoria=1");
}