<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8">
        <title></title>
        <script>
            $(document).ready(function(){
                jQuery(function(){jQuery('ul.sf-menu').superfish();});

				jQuery('ul.sf-menu').superfish({
					delay:       500,                // one second delay on mouseout
					speed:       'fast',             // faster animation speed
					autoArrows:  false               // disable generation of arrow mark-up
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
			if(strtotime('2024/11/30') > strtotime(date('Y/m/d'))){
				require_once(dirname(__FILE__)."/config/abrealas.php");
				//0061
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".contrato_empr ADD COLUMN IF NOT EXISTS usuins bigint NOT NULL DEFAULT 0;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".contrato_empr ADD COLUMN IF NOT EXISTS datains timestamp without time zone DEFAULT '3000-12-31';");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".contrato_empr ADD COLUMN IF NOT EXISTS usuedit bigint NOT NULL DEFAULT 0;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".contrato_empr ADD COLUMN IF NOT EXISTS dataedit timestamp without time zone DEFAULT '3000-12-31';");
				pg_query($Conec, "UPDATE ".$xProj.".bensachados SET usuarquivou = usurestit, dataarquivou = datarestit WHERE usurestit > 0 And id > 200;");
				pg_query($Conec, "UPDATE ".$xProj.".bensachados SET usuencdestino = 37, descencdestino = 'DIADM', descencprocesso = 'Doação' WHERE id = 16;"); // Processo 0249/2024 já encerrado
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