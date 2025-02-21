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
			if(strtotime('2025/03/30') > strtotime(date('Y/m/d'))){
				require_once(dirname(__FILE__)."/config/abrealas.php");
				//0090
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".extintores ALTER COLUMN ext_local TYPE VARCHAR(150) ");
				$rs = pg_query($ConecPes, "SELECT nome_resumido FROM ".$xPes.".pessoas WHERE id = 6273");
				$row = pg_num_rows($rs);
				if($row > 0){
					$tbl = pg_fetch_row($rs);
					if(is_null($tbl[0]) || $tbl[0] == ""){
						pg_query($ConecPes, "UPDATE ".$xPes.".pessoas SET nome_resumido = 'Alcir' WHERE id = 6273");
						pg_query($ConecPes, "UPDATE ".$xPes.".pessoas SET nome_resumido = 'Giovanna' WHERE id = 9055");
					}
				}
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS ordem_daf smallint NOT NULL DEFAULT 0 ");
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