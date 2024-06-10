<?php
	session_start();
	require_once(dirname(__FILE__)."/config/abrealas.php");
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
			if(isset($_SESSION["NomeUsual"])){
				$Nome = $_SESSION["NomeUsual"];
			}else{
				$Nome = "";
			}
			if(isset($_SESSION["AdmUsu"])){
				$Adm = $_SESSION["AdmUsu"];
			}else{
				$Adm = 0;
			}
			if(isset($_SESSION["SiglaSetor"])){
				if($_SESSION["SiglaSetor"] != ""){
					$Setor = "(".$_SESSION["SiglaSetor"].")";
				}else{
					$Setor = "";	
				}
			}else{
				$Setor = "";
			}
			date_default_timezone_set('America/Sao_Paulo');
//            $data = date('Y-m-d');
//            $diaSemana = date('w', strtotime($data)); // date('w', time()); // também funciona
			//$diaSemana = 4;
            $rs = pg_query($Conec, "SELECT column_name, data_type, character_maximum_length FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'setores'");
            $row = pg_num_rows($rs);
            if($row == 0){
                $Erro = 1;
                echo "Faltam tabelas. Informe à ATI.";
				return false;
            }
        ?>
		<input type="hidden" id="guardadiasemana" value="<?php echo $diaSemana; ?>"/>		
		<input type="hidden" id="guardaAdm" value="<?php echo $Adm; ?>"/>	
		<!-- menu para as páginas seguintes  -->
        <ul id="example" class="sf-menu sf-js-enabled sf-arrows sf-menu-dia<?php echo $diaSemana; ?> ">
            <li>
				<a href="#" onclick="openhref(52);">Início</a>
			</li>
            <li>
				<a href="#" onclick="openhref(54);">Organograma</a>
			</li>
            <li class="current">
				<a href="#">Diretorias</a>
				<ul>
					<?php
						$Cont = 101;
						$rs1 = pg_query($Conec, "SELECT codset, siglasetor, descsetor FROM ".$xProj.".setores WHERE menu = 1 And ativo = 1 ORDER BY codset");
						while($tbl1 = pg_fetch_row($rs1)){
							echo "<li><a href='#' onclick='openhrefDir($tbl1[0]);'>$tbl1[1] - $tbl1[2]</a></li>";
							$Cont = $Cont+100;
						}
					?>

				</ul>
			</li>
            <li class="current">
				<a href="#">Assessorias</a>
				<ul>
					<?php
						$Cont = 901;
						$rs2 = pg_query($Conec, "SELECT codset, siglasetor, descsetor FROM ".$xProj.".setores WHERE menu = 2 And ativo = 1 ORDER BY codset");
						while($tbl2 = pg_fetch_row($rs2)){
							echo "<li><a href='#' onclick='openhrefDir($tbl2[0]);'>$tbl2[1] - $tbl2[2]</a></li>";
							$Cont++;
						}
					?>
				</ul>
			</li>
            <li>
				<a href="#">Telefones</a>
				<ul>
					<li>
						<a href="#" onclick="openhref(57);">Ramais Internos</a>
					</li>
					<li>
						<a href="#" onclick="openhref(58);">Ramais Externos</a>
					</li>
				</ul>
			</li>
            <li title="Livro de Registro de Ocorrências">
				<a href="#" onclick="openhref(36);">LRO</a>
			</li>
            <li>
				<a href="#" href="#" onclick="openhref(70);">Tarefas</a>
			</li>
            <li>
				<a href="#" href="#" onclick="openhref(80);">Trocas</a>
			</li>
			<?php

				echo "<li>";
					echo "<a href='#'>Ferramentas</a>";
					echo "<ul>";
						if($_SESSION["AdmUsu"] > 6){ // superusuário para construção
							echo "<li>";
								echo "<a href='#'>Acertos</a>";
								echo "<ul>";
									echo "<li>";
									echo "<a href='#' onclick='openhref(60);'>Tabelas</a>";
									echo "</li>";

									echo "<li>";
									echo "<a href='#' onclick='openhref(66);'>php Info</a>";
									echo "</li>";
								echo "</ul>";
							echo "</li>";
						}

						echo "<li>";
							echo "<a href='#' onclick='openhref(59);'>Aniversariantes</a>";
						echo "</li>";
						echo "<li>";
							echo "<a href='#' onclick='openhref(62);'>Atualizar Senha</a>";
						echo "</li>";

						echo "<li>";
							echo "<a href='#' onclick='openhref(64);'>Bens Encontrados</a>";
						echo "</li>";

						if($Adm > 6){
							echo "<li>";
					   			echo "<a href='#' onclick='openhref(61);'>Cadastro de Usuários</a>";
							echo "</li>";
						}
						echo "<li>";
							echo "<a href='#' onclick='openhref(63);'>Calendário</a>";
						echo "</li>";

						echo "<li>";
							echo "<a href='#'>Leituras</a>";
							echo "<ul>";
								echo "<li>";
								echo "<a href='#' onclick='openhref(34);'>Água</a>";
								echo "</li>";
								echo "<li>";
								echo "<a href='#' onclick='openhref(35);'>Eletricidade</a>";
								echo "</li>";
							echo "</ul>";
						echo "</li>";	

						if($_SESSION["AdmUsu"] > 6){ // superusuário para construção
							echo "<li>";
								echo "<a href='#'>Manutenção</a>";
								echo "<ul>";
									echo "<li>";
									echo "<a href='#' onclick='openhref(65);'>Ar Condicionado</a>";
									echo "</li>";
								echo "</ul>";
							echo "</li>";
						}


						if($_SESSION["AdmUsu"] > 6){ // superusuário
							echo "<li>";
								echo "<a href='#' onclick='openhref(31);'>Parâmetros do Sistema</a>";
							echo "</li>";
							echo "<li>";
								echo "<a href='#' onclick='openhref(33);'>Registro de Ocorrências</a>";
							echo "</li>";
						}
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

			?>
            <li style="border-right: 0; border-left: 0px;">
				<a href="#"><br></a>
			</li>
            <li style="border-right: 0; border-left: 0px;">
				<a href="#"><br></a>
			</li>
            <li style="border-right: 0; border-left: 0px;">
				<?php
					if($Adm < 4){
						echo "<a href='#'><img src='imagens/icousu.png' height='20px;' title='Usuário $Setor'></a>";
					}
					if($Adm >= 4 && $Adm < 7){
						echo "<a href='#'><img src='imagens/icoadm.png' height='20px; title='Administrador $Setor'></a>";
					}
					if($Adm > 6){
						echo "<a href='#'><img src='imagens/icosuper.png' height='20px;' title='Superusuário'></a>";
					}
				?>
			</li>
			<li>
				<a href="#" onclick="openhref(98);"><sup>Sair - Encerrar Sessão <div id="nomeLogado" style="padding-top: 2px;"> <?php echo $Nome; ?></sup> <?php echo $Setor; ?></div></a> <!-- vai para o  -->
			</li>
        </ul>
    </body>
</html>