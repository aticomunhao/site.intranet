<?php
	session_start();
	require_once(dirname(__FILE__)."/config/abrealas.php");
	if(!isset($_SESSION['AdmUsu'])){
		session_destroy();
        header("Location: ../index.html");
     }
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
//				document.getElementById("etiqtela").innerHTML = $(window).width();
//				var versaoJquery = $.fn.jquery; 
//				alert(versaoJquery);
				LargTela = $(window).width(); // largura da tela ao abrir o módulo
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
				$Nome = substr($_SESSION["NomeUsual"], 0, 20);
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
					$Setor = "(".substr($_SESSION["SiglaSetor"], 0, 5).")";
				}else{
					$Setor = "";
				}
			}else{
				$Setor = "";
			}
			
			$LargTela = 1280; // laptop 14pol
			if($LargTela > 1280){
				$Quant = 15; // Quantidade de caracteres no nome ou cargo
				$Campo = "115px"; // larg campo nome ou cargo 
			}else{
				$Quant = 15;
				$Campo = "105px";
			}
			if($LargTela < 1270){ // chrome - laptop 14pol
				$Quant = 10;
				$Campo = "90px";
			}
			if($LargTela == 1900){
				$Quant = 20;
				$Campo = "150px";
			}
			date_default_timezone_set('America/Sao_Paulo');
            $rs = pg_query($Conec, "SELECT column_name, data_type, character_maximum_length FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'setores'");
            $row = pg_num_rows($rs);
            if($row == 0){
                $Erro = 1;
                echo "Faltam tabelas. Informe à ATI.";
				return false;
            }
			$rs = pg_query($Conec, "SELECT column_name, data_type, character_maximum_length FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'cesbmenu'");
            $row = pg_num_rows($rs);
            if($row == 0){
                $Erro = 1;
                echo "Faltam tabelas de menu. Informe à ATI.";
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
			<li>
				<a href="#">Setores</a>
				<ul>
					<?php
						//Pr e Vpr
						$rs1 = pg_query($Conec, "SELECT codset, siglasetor, descsetor FROM ".$xProj.".setores WHERE menu = 1 And ativo = 1 And codset < 5 ORDER BY codset");
						while($tbl1 = pg_fetch_row($rs1)){
							echo "<li><a href='#' onclick='openhrefDir($tbl1[0]);'>$tbl1[1] - $tbl1[2]</a></li>";
						}
					?>
					<li>
						<a href="#">Diretorias</a>
						<ul>
							<?php
								$Cont = 101;
								$rs1 = pg_query($Conec, "SELECT codset, siglasetor, descsetor FROM ".$xProj.".setores WHERE menu = 1 And ativo = 1 And codset > 4 ORDER BY codset");
								while($tbl1 = pg_fetch_row($rs1)){
									echo "<li><a href='#' onclick='openhrefDir($tbl1[0]);'>$tbl1[1] - $tbl1[2]</a></li>";
									$Cont = $Cont+100;
								}
							?>
						</ul>
					</li>
					<li>
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
				</ul>
			</li>
             <li>
				<a href="#">Telefones</a>
				<ul>
					<li>
						<a href="#" onclick="openhref(57);">Ramais Internos</a>
					</li>
					<li>
						<a href="#" onclick="openhref(58);">Telefones Úteis</a>
					</li>
				</ul>
			</li>
            <li title="Livro de Registro de Ocorrências">
				<a href="#" onclick="openhref(36);">LRO</a>
			</li>
            <li>
				<a href="#" href="#" onclick="openhref(90);">Tarefas</a>
			</li>
            <li>
				<a href="#" href="#" onclick="openhref(89);">Trocas</a>
			</li>
			<li>
				<a href="#">Controles</a>
				<ul>
				<?php
					//Bens encontrados
					if(isset($_SESSION["AdmBens"])){
						if($_SESSION["AdmBens"] == 1 || $_SESSION["FiscBens"] == 1 || $_SESSION["SoInsBens"] == 1 || $_SESSION["AdmUsu"] > 6){ 
							echo "<li>";
							echo "<a href='#' onclick='openhref(64);'>Achados e Perdidos</a>";
							echo "</li>";
						}
					}
					?>
					<li>
						<a href='#' onclick='openhref(34);'>Água</a>
<!--						<ul>
							<li><a href='#' onclick='openhref(96);'>Bebedouros</a></li>
							<li><a href='#' onclick='openhref(94);'>Filtros de Água</a>
							<li><a href='#' onclick='openhref(34);'>Leituras Hidrômetro</a></li>
						</ul>
-->
					</li>
					<li>
						<a href='#'>Ar Condicionado</a>
						<ul>
							<?php
								echo "<li>";
								$Menu4 = escMenu($Conec, $xProj, 4);
								echo "<a href='#' onclick='openhref(65);'>$Menu4</a>";
								echo "</li>";
								echo "<li>";
								$Menu5 = escMenu($Conec, $xProj, 5);
								echo "<a href='#' onclick='openhref(69);'>$Menu5</a>";
								echo "</li>";
								echo "<li>";
								$Menu6 = escMenu($Conec, $xProj, 6);
								echo "<a href='#' onclick='openhref(70);'>$Menu6</a>";
								echo "</li>";
							?>
						</ul>
					</li>

					<?php
					$Bebed = parEsc("bebed", $Conec, $xProj, $_SESSION["usuarioID"]); // entrega e devolução
					$FiscBebed = parEsc("bebed_fisc", $Conec, $xProj, $_SESSION["usuarioID"]); // edita, modifica
					if($Bebed == 1 || $FiscBebed == 1 || $_SESSION["AdmUsu"] > 6){
						echo "<li>";
							echo "<a href='#' onclick='openhref(96);'>Bebedouros</a>";
						echo "</li>";
					}
					//Claviculário da DAF
					$Clav2 = parEsc("clav2", $Conec, $xProj, $_SESSION["usuarioID"]); // entrega e devolução
					$ClavEdit2 = parEsc("clav_edit2", $Conec, $xProj, $_SESSION["usuarioID"]); // edita, modifica
					$FiscClav2 = parEsc("fisc_clav2", $Conec, $xProj, $_SESSION["usuarioID"]); // fiscal de chaves
					if($ClavEdit2 == 1 || $Clav2 == 1 || $FiscClav2 == 1 || $_SESSION["AdmUsu"] > 6){
						echo "<li>";
							echo "<a href='#' onclick='openhref(79);'>Chaves DAF</a>";
						echo "</li>";
					}

					//Claviculário Chaves Lacradas
					$Clav3 = parEsc("clav3", $Conec, $xProj, $_SESSION["usuarioID"]); // entrega e devolução
					$ClavEdit3 = parEsc("clav_edit3", $Conec, $xProj, $_SESSION["usuarioID"]); // edita, modifica
					$FiscClav3 = parEsc("fisc_clav3", $Conec, $xProj, $_SESSION["usuarioID"]); // fiscal de chaves
					if($ClavEdit3 == 1 || $Clav3 == 1 || $FiscClav3 == 1 || $_SESSION["AdmUsu"] > 6){
						echo "<li>";
							echo "<a href='#' onclick='openhref(80);'>Chaves Lacradas</a>";
						echo "</li>";
					}

					//Claviculário da Portaria
					$Clav = parEsc("clav", $Conec, $xProj, $_SESSION["usuarioID"]); // entrega e devolução
					$ClavEdit = parEsc("clav_edit", $Conec, $xProj, $_SESSION["usuarioID"]); // edita, modifica
					$FiscClav = parEsc("fisc_clav", $Conec, $xProj, $_SESSION["usuarioID"]); // fiscal de chaves
					if($ClavEdit == 1 || $Clav == 1 || $FiscClav == 1 || $_SESSION["AdmUsu"] > 6){
						echo "<li>";
							echo "<a href='#' onclick='openhref(75);'>Chaves Portaria</a>";
						echo "</li>";
					}

					$Contr = parEsc("contr", $Conec, $xProj, $_SESSION["usuarioID"]);
					$FiscContr = parEsc("fisc_contr", $Conec, $xProj, $_SESSION["usuarioID"]);
					if($Contr == 1 || $FiscContr == 1 || $_SESSION["AdmUsu"] > 6){
						echo "<li>";
//							echo "<a href='#' onclick='openhref(76);'>Contratos</a>";
							echo "<a href='#'>Contratos</a>";
							echo "<ul>";
							echo "<li title='Empresas Contratadas'><a href='#' onclick='openhref(76);'>Contratadas</a></li>";
							echo "<li title='Empresas Contratantes'><a href='#' onclick='openhref(81);'>Contratantes</a></li>";
							echo "</ul>";
						echo "</li>";
					}

					?>
					<li>
						<a href='#'>Eletricidade</a>
						<ul>
							<?php
								echo "<li>";
								$Menu1 = escMenu($Conec, $xProj, 1); //escMenu em abrealas
								echo "<a href='#' onclick='openhref(35);'>$Menu1</a>";
									echo "<ul>";
										echo "<li>";
											echo "<a href='#' onclick='openhref(78);'>Eletricidade Injetada</a>";
										echo "</li>";
									echo "</ul>";
								echo "</li>";
								echo "<li>";
								$Menu2 = escMenu($Conec, $xProj, 2);
								echo "<a href='#' onclick='openhref(67);'>$Menu2</a>";
								echo "</li>";
								echo "<li>";
								$Menu3 = escMenu($Conec, $xProj, 3);
								echo "<a href='#' onclick='openhref(68);'>$Menu3</a>";
								echo "</li>";

								$Eletric5 = parEsc("eletric5", $Conec, $xProj, $_SESSION["usuarioID"]);
								$FiscEletric = parEsc("fisc_eletric", $Conec, $xProj, $_SESSION["usuarioID"]); 
								if($Eletric5 == 1 || $FiscEletric == 1|| $_SESSION["AdmUsu"] > 6){
									echo "<li><a href='#' onclick='openhref(93);'>Viaturas Elétricas</a></li>"; // eletricidade 5 - viaturas
								}
							?>
						</ul>
					</li>
					<li>
						<a href='#' onclick='openhref(73);'>Elevadores</a>
					</li>
					<?php
						$Extint = parEsc("extint", $Conec, $xProj, $_SESSION["usuarioID"]);
						$Fisc_Extint = parEsc("fisc_extint", $Conec, $xProj, $_SESSION["usuarioID"]);
						if($Extint == 1 || $Fisc_Extint == 1 || $_SESSION["AdmUsu"] > 6){
							echo "<li>";
								echo "<a href='#' onclick='openhref(91);'>Extintores</a>";
							echo "</li>";
						}

						$Filtro = parEsc("filtros", $Conec, $xProj, $_SESSION["usuarioID"]);
						$FiscFiltro = parEsc("fisc_filtros", $Conec, $xProj, $_SESSION["usuarioID"]);
						if($Filtro == 1 || $FiscFiltro == 1 || $_SESSION["AdmUsu"] > 6){
							echo "<li><a href='#' onclick='openhref(94);'>Filtros de Água</a></li>"; 
						}

						$Viatura = parEsc("viatura", $Conec, $xProj, $_SESSION["usuarioID"]);// combustíveis
						$FiscViat = parEsc("fisc_viat", $Conec, $xProj, $_SESSION["usuarioID"]); // fiscal
						if($Viatura == 1 || $FiscViat == 1 || $_SESSION["AdmUsu"] > 6){
							echo "<li><a href='#' onclick='openhref(92);'>Viaturas</a></li>";
						}
					?>
				</ul>
			</li>
			<?php
				echo "<li>";
					echo "<a href='#'>Ferramentas</a>";
					echo "<ul>";
						if($_SESSION["AdmUsu"] > 6){ // superusuário para construção
							echo "<li>";
								echo "<a href='#'>Acertos</a>";
								echo "<ul>";
									echo "<li><a href='#' onclick='openhref(66);'>php Info</a></li>";
									echo "<li><a href='#' onclick='openhref(60);'>Tabelas</a></li>";
									echo "<li><a href='#' onclick='openhref(95);'>Usuários</a></li>";
								echo "</ul>";
							echo "</li>";
						}
						echo "<li>";
							echo "<a href='#' onclick='openhref(59);'>Aniversariantes</a>";
						echo "</li>";
						echo "<li>";
							echo "<a href='#' onclick='openhref(62);'>Atualizar Senha</a>";
						echo "</li>";

						if($Adm > 6){
							echo "<li>";
					   			echo "<a href='#' onclick='openhref(61);'>Cadastro de Usuários</a>";
							echo "</li>";
						}
						echo "<li>";
							echo "<a href='#' onclick='openhref(63);'>Calendário</a>";
						echo "</li>";

						//usado para o Quadro Horário
//						$Efet = parEsc("esc_eft", $Conec, $xProj, $_SESSION["usuarioID"]); // procura marca Efetivo da escala em poslog
//						$FiscEscala = parEsc("esc_fisc", $Conec, $xProj, $_SESSION["usuarioID"]); // fiscal de escala
//						$Escalante = parEsc("esc_edit", $Conec, $xProj, $_SESSION["usuarioID"]); // escalante do grupo
//						$NumGrupo = 0;
//						if($Efet == 1){
//							$NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]); // procurar a que grupo de escala pertence
//						}  // Essas variáveis foram para o Quadro Horário

//						if($_SESSION["AdmUsu"] > 6){ // superusuário
//							if($NumGrupo > 0 || $FiscEscala > 0 || $Escalante > 0){
//								echo "<li>";
//									echo "<a href='#' onclick='openhref(72);'>Escala</a>";
//								echo "</li>";
//							}
//						}

						$NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]); // procurar a que grupo de escala pertence
						//Só escalante e fiscal vê a escala
						$EscalanteDAF = parEsc("esc_daf", $Conec, $xProj, $_SESSION["usuarioID"]);
						$Fiscal = parEsc("esc_fisc", $Conec, $xProj, $_SESSION["usuarioID"]);
//						$EfetivoEscalaDAF = parEsc("eft_daf", $Conec, $xProj, $_SESSION["usuarioID"]);
//						if($EscalanteDAF == 1 || $EfetivoEscalaDAF == 1){
						if($NumGrupo > 0 && $EscalanteDAF == 1 || $Fiscal == 1){
							echo "<li>";
								echo "<a href='#' onclick='openhref(77);'>Escala DAF</a>";
							echo "</li>";
						}

						if($_SESSION["AdmUsu"] > 6){ // superusuário
							echo "<li>";
								echo "<a href='#' onclick='openhref(31);'>Parâmetros do Sistema</a>";
							echo "</li>";
						}

//						if($NumGrupo > 0 || $FiscEscala > 0 || $Escalante > 0){
//							echo "<li>";
//								echo "<a href='#' onclick='openhref(74);'>Quadro Horário</a>";
//							echo "</li>";
//						}
//						if($_SESSION["AdmUsu"] > 6){ // superusuário
//							echo "<li>";
//								echo "<a href='#' onclick='openhref(33);'>Registro de Ocorrências</a>";
//							echo "</li>";
//						}
//						echo "<li>";
//							echo "<a href='#' onclick='openhref(30);'>Tráfego de Arquivos</a>";
//						echo "</li>";
						if($_SESSION["AdmUsu"] > 6){ // superusuário
							echo "<li>";
								echo "<a href='#' onclick='openhref(32);'>Troca de Slides</a>";
							echo "</li>";
						}
					echo "</ul>";
				echo "</li>";
			?>

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
<!--		<li>
				<a href="#" onclick="openhref(98);"><sup>Sair - Encerrar Sessão <div style="padding-top: 2px;"> <?php echo $Nome; ?></sup> <?php echo $Setor; ?></div></a>
			</li> 
-->
			<li>
				<table>
					<tr>
						<td rowspan="2" style="padding-right: 3px;"><a href="#" onclick="openhref(98);" style="padding: 0px; border: 0px;">Sair</a></td>
						<td style="font-size: 70%; font-weight: bold; padding-top: 3px;"><a href="#" onclick="openhref(98);" style="padding: 0px; border: 0px;">Encerrar Sessão</a></td>
					</tr>
					<tr>
						<td style="font-size: 70%; font-weight: bold;"><a href="#" onclick="openhref(98);" style="padding: 0px; border: 0px;"><?php echo $Nome." ".$Setor; ?></a></td>
					</tr>
				</table>
			</li>
        </ul>
    </body>
</html>