// rotina elaborada com base em videos no youtube - https://www.youtube.com/watch?v=rv7e9B1gCKE
//Recebe dados do formulário de upload da página relArq.php 

cadForm = document.getElementById("upload-arquivo");
if(cadForm){
    cadForm.addEventListener("submit", async (e) => { //Quando clicar no botão submit executa a função
      e.preventDefault(); // para não recarregar a página
      var arquivo = document.getElementById("arquivo").files[0]; //recebe o arquivo do form
      //Filtro extensão de arquivos - O js não está entendendo o MIME type pptx e ppsx - está sendo filtrado no arqUpload.php
//      if(arquivo['type'] == 'application/pdf' || arquivo['type'] == 'application/msword' || arquivo['type'] == 'application/vnd.openxmlformats' || arquivo['type'] == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || arquivo['type'] == 'text/plain' || $arquivo['type'] == 'application/vnd.openxmlformats-officedocument.presentationml.presentation' || $arquivo['type'] == 'application/vnd.openxmlformats-officedocument.presentationml.slideshow'){ 
          var dadosForm = new FormData(); // cria o objeto para receber os dados

          dadosForm.append("arquivo", arquivo);// atribui info do arquivo para o objeto que será enviado para o php

          const dados = await fetch("modulos/conteudo/arqUpload.php", { // envia os dados para o arquivo que fará o upload - await aguarda finalizar esse processamento
            method: "POST",
            body: dadosForm
          });
  
        const resposta = await dados.json(); // lê os dados retornados pelo php - não está vindo, há erro

      $('#relArquivos').load('modulos/conteudo/carRelArq.php');

      if(resposta['status']){
        document.getElementById("arquivo").value = ""; // limpar o nome do arquivo
        $('#relArquivos').load('modulos/conteudo/carRelArq.php');
        $('#msg').fadeIn("1000");
        document.getElementById("msg").innerHTML = resposta['msg'];
        $('#msg').fadeOut(4000);
      }else{
        document.getElementById("msg").innerHTML = resposta['msg'];
      }
//    }else{
//      document.getElementById("msg").innerHTML = "<p style='color: #f00;'>Arquivos permitidos: pdf, doc, docx e txt.</P>"
//    }
  });
}