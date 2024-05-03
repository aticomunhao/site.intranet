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
            $rs = pg_query($Conec, "SELECT column_name, data_type, character_maximum_length FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'setores'");
            $row = pg_num_rows($rs);
            if($row == 0){
                $Erro = 1;
                echo "Faltam tabelas. Informe à ATI.";
				return false;
            }
			$rs1 = pg_query($Conec, "SELECT siglasetor, descsetor FROM ".$xProj.".setores WHERE codset = 2");
			$tbl1 = pg_fetch_row($rs1);
			$Dg = $tbl1[0]." - ".$tbl1[1];
			$rs2 = pg_query($Conec, "SELECT siglasetor, descsetor FROM ".$xProj.".setores WHERE codset = 3");
			$tbl2 = pg_fetch_row($rs2);
			$Dac = $tbl2[0]." - ".$tbl2[1];
			$rs3 = pg_query($Conec, "SELECT siglasetor, descsetor FROM ".$xProj.".setores WHERE codset = 4");
			$tbl3 = pg_fetch_row($rs3);
			$Dae = $tbl3[0]." - ".$tbl3[1];
			$rs4 = pg_query($Conec, "SELECT siglasetor, descsetor FROM ".$xProj.".setores WHERE codset = 5");
			$tbl4 = pg_fetch_row($rs4);
			$Daf = $tbl4[0]." - ".$tbl4[1];
			$rs5 = pg_query($Conec, "SELECT siglasetor, descsetor FROM ".$xProj.".setores WHERE codset = 6");
			$tbl5 = pg_fetch_row($rs5);
			$Dao = $tbl5[0]." - ".$tbl5[1];
			$rs6 = pg_query($Conec, "SELECT siglasetor, descsetor FROM ".$xProj.".setores WHERE codset = 7");
			$tbl6 = pg_fetch_row($rs6);
			$Ded = $tbl6[0]." - ".$tbl6[1];
			$rs7 = pg_query($Conec, "SELECT siglasetor, descsetor FROM ".$xProj.".setores WHERE codset = 8");
			$tbl7 = pg_fetch_row($rs7);
			$Dij = $tbl7[0]." - ".$tbl7[1];
			$rs8 = pg_query($Conec, "SELECT siglasetor, descsetor FROM ".$xProj.".setores WHERE codset = 9");
			$tbl8 = pg_fetch_row($rs8);
			$Dps = $tbl8[0]." - ".$tbl8[1];


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
					<?php
						$Cont = 101;
						$rs1 = pg_query($Conec, "SELECT codset, siglasetor, descsetor FROM ".$xProj.".setores WHERE codset > 1 And codset < 10 ORDER BY codset");
						while($tbl1 = pg_fetch_row($rs1)){
							echo "<li><a href='#' onclick='openhref($Cont);'>$tbl1[1] - $tbl1[2]</a></li>";
							$Cont = $Cont+100;
						}
					?>
<!--
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
-->
				</ul>
			</li>
            <li class="current">
				<a href="#">Assessorias</a>
				<ul>
					<?php
						$Cont = 901;
						$rs2 = pg_query($Conec, "SELECT codset, siglasetor, descsetor FROM ".$xProj.".setores WHERE codset >= 10 ORDER BY codset");
						while($tbl2 = pg_fetch_row($rs2)){
							echo "<li><a href='#' onclick='openhref($Cont);'>$tbl2[1] - $tbl2[2]</a></li>";
							$Cont++;
						}
					?>
<!--
					<li><a href="#" onclick="openhref(901);">AAD - Assessoria de Assuntos Doutrinários</a></li>
					<li><a href="#" onclick="openhref(902);">ACE - Assessoria de Comunicação e Eventos</a></li>
					<li><a href="#" onclick="openhref(903);">ADI - Assessoria de Desenvolvimento Institucional</a></li>
					<li><a href="#" onclick="openhref(904);">AJU - Assessoria Jurídica</a></li>
					<li><a href="#" onclick="openhref(905);">AME - Assessoria de Estudos e Aplicações de Medicina Espiritual</a></li>
					<li><a href="#" onclick="openhref(906);">APE - Assessoria Planejamento Esgtratégico</a></li>
					<li><a href="#" onclick="openhref(907);">APV - Assessoria da Pomada do Vovô Pedro</a></li>
					<li><a href="#" onclick="openhref(908);">ATI - Assessoria de Tecnologia da Informação</a></li>
					<li><a href="#" onclick="openhref(909);">Ouvidoria</a></li>
-->
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
						echo "<li>";
							echo "<a href='#' onclick='openhref(28);'>Atualizar Senha</a>";
						echo "</li>";
						if($Adm == 4 && $_SESSION['AdmVisu'] == 1 || $Adm == 7){ // administrador pode ver lista de usuários ou superusu
							echo "<li>";
					   			echo "<a href='#' onclick='openhref(27);'>Cadastro de Usuários</a>";
							echo "</li>";
						}
						if($_SESSION["AdmUsu"] >= 3){ // gerente
							echo "<li>";
								echo "<a href='#'>Leituras</a>";
//								echo "<a href='#' onclick='openhref(34);'>Leituras>";
								echo "<ul>";
									echo "<li>";
									echo "<a href='#' onclick='openhref(34);'>Água</a>";
									echo "</li>";
									echo "<li>";
									echo "<a href='#' onclick='openhref(35);'>Eletricidade</a>";
									echo "</li>";
								echo "</ul>";
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