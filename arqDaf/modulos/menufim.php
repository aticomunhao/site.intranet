<?php
	session_name("arqAdm"); // sessão diferente da CEsB
	session_start();
	require_once("./config/abrealas.php");
	if(!isset($_SESSION['usuarioCPF'])){
        header("Location: ../index.php");
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
			$Adm = 0;
			$Adm = arqDafAdm("adm", $Conec, $xProj, $_SESSION["usuarioCPF"]);
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
				<a href="#">Diretórios</a>
				<ul>
					<?php
						//Pr e Vpr
						$rs1 = pg_query($Conec, "SELECT codset, siglasetor, descsetor FROM ".$xProj.".daf_setores WHERE menu = 1 And ativo = 1 ORDER BY codset");
						while($tbl1 = pg_fetch_row($rs1)){
							echo "<li><a href='#' onclick='openhrefDir($tbl1[0]);'>$tbl1[1]</a></li>";
						}
					?>
				</ul>
			</li>

			<?php
				$AdmUsu = arqDafAdm("adm", $Conec, $xProj, $_SESSION["usuarioCPF"]);
				echo "<li>";
					echo "<a href='#'>Ferramentas</a>";
					echo "<ul>";
						echo "<li>";
							echo "<a href='#' onclick='openhref(62);'>Atualizar Senha</a>";
						echo "</li>";
						if($Adm > 1){ // administrador
							echo "<li>";
					   			echo "<a href='#' onclick='openhref(61);'>Cadastro de Usuários</a>";
							echo "</li>";
						}
	
						if($AdmUsu > 1){ // superusuário
							echo "<li>";
//								echo "<a href='#' onclick='openhref(31);'>Parâmetros do Sistema</a>";
							echo "</li>";
						}
					echo "</ul>";
				echo "</li>";
			?>

            <li style="border-right: 0; border-left: 0px;">
				<?php
					if($Adm == 1){
						echo "<a href='#'><img src='imagens/icoadm.png' height='20px; title='Administrador $Setor'></a>";
					}
					if($Adm > 1){
						echo "<a href='#'><img src='imagens/icosuper.png' height='20px;' title='Superusuário'></a>";
					}
				?>
			</li>
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