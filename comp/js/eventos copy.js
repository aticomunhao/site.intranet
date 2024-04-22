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
    if(parseInt(Num) === 11){
        location.replace("index.php");
    }
    if(parseInt(Num) === 21){
        location.replace("indexb.php");
    }
    // página livre (12) ou página do logado (22)
    if(parseInt(Num) === 12 || parseInt(Num) === 22){
        $('#container3').load('modulos/organog.php');}
    if(parseInt(Num) === 13){
        $('#container3').load('modulos/ramaisInt.php?tipo=1'); // sem log
    }
    if(parseInt(Num) === 23){
        $('#container3').load('modulos/ramaisInt.php?tipo=2');  // com usu logado
    }
    if(parseInt(Num) === 14){
        $('#container3').load('modulos/ramaisExt.php?tipo=1');
    }
    if(parseInt(Num) === 24){
        $('#container3').load('modulos/ramaisExt.php?tipo=2');
    }
    if(parseInt(Num) === 25){
        $('#container3').load('modulos/aniverRel.php');
    }
    if(parseInt(Num) === 26){
        let Conf = confirm("Verificação das tabelas MySQL. Continua?");
        if(Conf){
            $('#container3').load('modulos/tabelas.php');
        }
    }
    if(parseInt(Num) === 27){
        $('#container3').load('modulos/config/cadUsu.php');
    }
    if(parseInt(Num) === 28){
        $('#container3').load('modulos/config/tpass.php');
    }
    if(parseInt(Num) === 29){
        $('#container3').load('modulos/calendario/calend.php');
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

    if(parseInt(Num) === 70){
        $('#container3').load('modulos/conteudo/tarefas.php');
    }
    if(parseInt(Num) === 80){
        $('#container3').load('modulos/trocas/relTrocas.php');
    }

// Diretoria Geral e Assessorias


//Diretorias
    if(parseInt(Num) === 101){ //Diretoria-Geral
        $('#container3').load('modulos/conteudo/PagDir.php?Diretoria=2&Subdiretoria=1');
    }

    if(parseInt(Num) === 201){ //Diretoria
        $('#container3').load('modulos/conteudo/PagDir.php?Diretoria=3&Subdiretoria=1');
    }
        if(parseInt(Num) === 202){  // Subdiretoria
            $('#container3').load('modulos/conteudo/PagDir.php?Diretoria=3&Subdiretoria=2');
        }
        if(parseInt(Num) === 203){
            $('#container3').load('modulos/conteudo/PagDir.php?Diretoria=3&Subdiretoria=3');
        }
        if(parseInt(Num) === 204){
            $('#container3').load('modulos/conteudo/PagDir.php?Diretoria=3&Subdiretoria=4');
        }
        if(parseInt(Num) === 205){
            $('#container3').load('modulos/conteudo/PagDir.php?Diretoria=3&Subdiretoria=5');
        }
        if(parseInt(Num) === 206){
            $('#container3').load('modulos/conteudo/PagDir.php?Diretoria=3&Subdiretoria=6');
        }
        if(parseInt(Num) === 207){
            $('#container3').load('modulos/conteudo/PagDir.php?Diretoria=3&Subdiretoria=7');
        }
        if(parseInt(Num) === 208){
            $('#container3').load('modulos/conteudo/PagDir.php?Diretoria=3&Subdiretoria=8');
        }


    if(parseInt(Num) === 301){
        $('#container3').load('modulos/conteudo/PagDir.php?Diretoria=4&Subdiretoria=1');
    }
    if(parseInt(Num) === 401){
        $('#container3').load('modulos/conteudo/PagDir.php?Diretoria=5&Subdiretoria=1');
    }
    if(parseInt(Num) === 501){
        $('#container3').load('modulos/conteudo/PagDir.php?Diretoria=6&Subdiretoria=1');
    }
    if(parseInt(Num) === 601){
        $('#container3').load('modulos/conteudo/PagDir.php?Diretoria=7&Subdiretoria=1');
    }
    if(parseInt(Num) === 701){
        $('#container3').load('modulos/conteudo/PagDir.php?Diretoria=8&Subdiretoria=1');
    }
    if(parseInt(Num) === 801){
        $('#container3').load('modulos/conteudo/PagDir.php?Diretoria=9&Subdiretoria=1');
    }

    if(parseInt(Num) === 901){
        $('#container3').load('modulos/conteudo/PagDir.php?Diretoria=10&Subdiretoria=1');
    }
    if(parseInt(Num) === 902){
        $('#container3').load('modulos/conteudo/PagDir.php?Diretoria=11&Subdiretoria=1');
    }
    if(parseInt(Num) === 903){
        $('#container3').load('modulos/conteudo/PagDir.php?Diretoria=12&Subdiretoria=1');
    }
    if(parseInt(Num) === 904){
        $('#container3').load('modulos/conteudo/PagDir.php?Diretoria=13&Subdiretoria=1');
    }
    if(parseInt(Num) === 905){
        $('#container3').load('modulos/conteudo/PagDir.php?Diretoria=14&Subdiretoria=1');
    }
    if(parseInt(Num) === 906){
        $('#container3').load('modulos/conteudo/PagDir.php?Diretoria=15&Subdiretoria=1');
    }
    if(parseInt(Num) === 907){
        $('#container3').load('modulos/conteudo/PagDir.php?Diretoria=16&Subdiretoria=1');
    }
    if(parseInt(Num) === 908){
        $('#container3').load('modulos/conteudo/PagDir.php?Diretoria=17&Subdiretoria=1');
    }
    if(parseInt(Num) === 909){
        $('#container3').load('modulos/conteudo/PagDir.php?Diretoria=18&Subdiretoria=1');
    }

}