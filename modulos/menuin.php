<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title></title>
		<style>
            body{ font: 16px sans-serif; }
			@media (max-width: 742px){
				.MenuEstend{
					width: 100%;
				}
			}
        </style>
        <script>
            $(document).ready(function(){
                jQuery(function(){jQuery('ul.sf-menu').superfish();});

				jQuery('ul.sf-menu').superfish({
					delay:       500,                // delay no mouseout
					speed:       'fast',             // animation speed
					autoArrows:  false               // desativa a geração de setas mark-up
				});
            });
        </script>
    </head>
    <body>
        <?php
			$diaSemana = filter_input(INPUT_GET, "diasemana");
			if(!isset($diaSemana)){
				$diaSemana = 1;
			}
			//Provisório
			if(strtotime('2025/07/30') > strtotime(date('Y/m/d'))){
				require_once(dirname(__FILE__)."/config/abrealas.php");
				//122
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS mostraniv smallint NOT NULL DEFAULT 1;");
				$rs = pg_query($Conec, "SELECT mostraniv FROM ".$xProj.".poslog WHERE mostraniv = 1");
				$row = pg_num_rows($rs);
				if($row == 0){
					pg_query($Conec, "UPDATE FROM ".$xProj.".poslog SET mostraniv = 1"); // marca todos
					//inserir na query mostraniv = 1 em aniverIni.php na atualização 123
				}
				//121
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS visucelcorp smallint NOT NULL DEFAULT 7;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS inseditcelcorp smallint NOT NULL DEFAULT 7;");

			} // fim data limite
        ?>
		<!-- menu para a página inicial  -->
        <ul id="example" class="sf-menu sf-js-enabled sf-arrows sf-menu-dia<?php echo $diaSemana; ?> ">
            <li class="MenuEstend">
				<a href="#" onclick="openhref(51);">Início</a>
			</li>
            <li class="MenuEstend">
				<a href="#" onclick="openhref(53);">Organograma</a>
			</li>
            <li class="current MenuEstend">
				<a href="#">Telefones</a>
				<ul>
					<li class="MenuEstend">
						<a href="#" onclick="openhref(55);">Ramais Internos</a>
					</li>
					<li class="MenuEstend">
						<a href="#" onclick="openhref(56);">Telefones Úteis</a>
					</li>
				</ul>
			</li>
            <li class="MenuEstend">
				<a href="#" onclick="openhref(99);">Login</a>
			</li>
        </ul>
    </body>
</html>