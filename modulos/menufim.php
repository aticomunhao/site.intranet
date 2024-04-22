<?php
	session_start();
	if(!isset($_SESSION['AdmUsu'])){
        header("Location: ../index.html");
     }
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8">
        <title></title>
		<script src="comp/js/eventos.js"></script>
        <script>
            $(document).ready(function(){
                jQuery(function(){jQuery('ul.sf-menu').superfish();});
				jQuery('ul.sf-menu').superfish({
					delay:       500,   
					speed:       'fast', 
					autoArrows:  false   
				});
//var versaoJquery = $.fn.jquery; 
//alert(versaoJquery);
            });
        </script>
    </head>
    <body>
        <?php
			$diaSemana = filter_input(INPUT_GET, "diasemana");
			if(!isset($diaSemana)){
				$diaSemana = 1;
			}
			$UsuAdm = filter_input(INPUT_GET, 'guardaAdm');
			if(!isset($UsuAdm)){
				$UsuAdm = 0;
			}
			if(isset($_SESSION["NomeCompl"])){
				$Nome = $_SESSION["NomeCompl"];
			}else{
				$Nome = "";
			}
			if(isset($_SESSION["AdmUsu"])){
				$Adm = $_SESSION["AdmUsu"];
			}else{
				$Adm = 0;
			}
			if(isset($_SESSION["SiglaSetor"])){
				$Setor = "(".$_SESSION["SiglaSetor"].")";
			}else{
				$Setor = "";
			}

//			date_default_timezone_set('America/Sao_Paulo');
//            $data = date('Y-m-d');
//            $diaSemana = date('w', strtotime($data)); // date('w', time()); // também funciona
			//$diaSemana = 4;
        ?>
		<input type="hidden" id="guardadiasemana" value="<?php echo $diaSemana; ?>"/>		
		<input type="hidden" id="guardaAdm" value="<?php echo $Adm; ?>"/>	
		<!-- menu para as páginas seguintes  -->
        <ul id="example" class="sf-menu sf-js-enabled sf-arrows sf-menu-dia<?php echo $diaSemana; ?> ">
            <li>
				<a href="#" onclick="openhref(21);">Início</a>
			</li>
            <li>
				<a href="#" onclick="openhref(22);">Organograma</a>
			</li>
            <li class="current">
				<a href="#">Diretorias</a>
				<ul>
					<li class="current">
						<a href="#" onclick="openhref(101);">DG - Diretoria-Geral</a>
					</li>

					<li class="current">
						<a href="#" onclick="openhref(201);">DAC - Diretoria de Arte e Cultura</a>
					</li>

					<li class="current">
						<a href="#" onclick="openhref(301);">DAE - Diretoria de Assistência Espiritual</a>
					</li>

					<li class="current">
						<a href="#" onclick="openhref(401);">DAF - Diretoria Administrativa e Financeira</a>
					</li>

					<li class="current">
						<a href="#" onclick="openhref(501);">DAO - Diretoria de Atendimento e Orientação</a>
					</li>

					<li class="current">
						<a href="#" onclick="openhref(601);">DED - Diretoria de Estudos Doutrinários</a>
					</li>

					<li class="current">
						<a href="#" onclick="openhref(701);">DIJ - Diretoria de Infância e Juventude</a>
					</li>

					<li class="current">
						<a href="#" onclick="openhref(801);">DPS - Diretoria de Promoção Social</a>
					</li>

				</ul>
			</li>
            <li class="current">
				<a href="#">Assessorias</a>
				<ul>
					<li><a href="#" onclick="openhref(901);">AAD - Assessoria de Assuntos Doutrinários</a></li>
					<li><a href="#" onclick="openhref(902);">ACE - Assessoria de Comunicação e Eventos</a></li>
					<li><a href="#" onclick="openhref(903);">ADI - Assessoria de Desenvolvimento Institucional</a></li>
					<li><a href="#" onclick="openhref(904);">AJU - Assessoria Jurídica</a></li>
					<li><a href="#" onclick="openhref(905);">AME - Assessoria de Estudos e Aplicações de Medicina Espiritual</a></li>
					<li><a href="#" onclick="openhref(906);">APE - Assessoria Planejamento Esgtratégico</a></li>
					<li><a href="#" onclick="openhref(907);">APV - Assessoria da Pomada do Vovô Pedro</a></li>
					<li><a href="#" onclick="openhref(908);">ATI - Assessoria de Tecnologia da Informação</a></li>
					<li><a href="#" onclick="openhref(909);">Ouvidoria</a></li>
				</ul>
			</li>
            <li>
				<a href="#">Telefones</a>
				<ul>
					<li>
						<a href="#" onclick="openhref(23);">Ramais Internos</a>
					</li>
					<li>
						<a href="#" onclick="openhref(24);">Ramais Externos</a>
					</li>
				</ul>
			</li>
            <li>
				<a href="#" onclick="openhref(29);">Calendário</a>
			</li>
            <li>
				<a href="#" href="#" onclick="openhref(70);">Tarefas</a>
			</li>
            <li>
				<a href="#" href="#" onclick="openhref(80);">Trocas</a>
			</li>
			<?php

//			if($Adm > 3){ // maior que gerente
				echo "<li>";
					echo "<a href='#'>Ferramentas</a>";
					echo "<ul>";
						if($_SESSION["AdmUsu"] > 6){ // superusuário
							echo "<li>";
								echo "<a href='#' onclick='openhref(26);'>Acertos MySql</a>";
							echo "</li>";
						}
						echo "<li>";
							echo "<a href='#' onclick='openhref(25);'>Aniversariantes</a>";
						echo "</li>";
						if($Adm == 4 && $_SESSION['AdmVisu'] == 1 || $Adm == 7){ // administrador pode ver lista de usuários ou superusu
							echo "<li>";
					   			echo "<a href='#' onclick='openhref(27);'>Cadastro de Usuários</a>";
							echo "</li>";
						}
						if($_SESSION["AdmUsu"] > 6){ // superusuário
							echo "<li>";
								echo "<a href='#' onclick='openhref(31);'>Parâmetros do Sistema</a>";
							echo "</li>";
						}
						echo "<li>";
							echo "<a href='#' onclick='openhref(33);'>Registro de Ocorrências</a>";
						echo "</li>";
						echo "<li>";
							echo "<a href='#' onclick='openhref(30);'>Tráfego de Arquivos</a>";
						echo "</li>";
						if($_SESSION["AdmUsu"] > 6){ // superusuário
							echo "<li>";
								echo "<a href='#' onclick='openhref(32);'>Troca de Slides</a>";
							echo "</li>";
						}
					echo "</ul>";
				echo "</li>";
//			}
			?>
            <li style="border-right: 0; border-left: 0px;">
				<a href="#"><br></a>
			</li>
            <li style="border-right: 0; border-left: 0px;">
				<a href="#"><br></a>
			</li>
            <li style="border-right: 0; border-left: 0px;">
				<?php
					if($Adm > 3){
						echo "<a href='#'><img src='imagens/icoadm.png' height='20px;'></a>";
					}else{
						echo "<a href='#'><img src='imagens/icousu.png' height='20px;'></a>";
					}
				?>
			</li>
			<li>
				<a href="#" onclick="openhref(98);"><sup>Sair - Encerrar Sessão <div id="nomeLogado"  style="padding-top: 2px;"> <?php echo $Nome; ?></sup> <?php echo $Setor; ?></div></a> <!-- vai para o  -->
			</li>
        </ul>
    </body>
</html>