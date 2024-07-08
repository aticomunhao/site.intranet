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
			if(strtotime('2024/07/30') > strtotime(date('Y/m/d'))){
				require_once(dirname(__FILE__)."/config/abrealas.php");
							
				//0023
				pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".cesbmenu (
					id SERIAL PRIMARY KEY, 
					descr VARCHAR(100), 
					ativo smallint NOT NULL DEFAULT 1,
				   	usumodif bigint NOT NULL DEFAULT 0,
					datamodif timestamp without time zone DEFAULT '3000-12-31'
					)
				");
				$rs = pg_query($Conec, "SELECT id FROM ".$xProj.".cesbmenu LIMIT 2");
				$row = pg_num_rows($rs);
				if($row == 0){
				 	pg_query($Conec, "INSERT INTO ".$xProj.".cesbmenu (id, descr) VALUES (1, 'Comunhão') ");
					pg_query($Conec, "INSERT INTO ".$xProj.".cesbmenu (id, descr) VALUES (2, 'Operadora Claro') ");
					pg_query($Conec, "INSERT INTO ".$xProj.".cesbmenu (id, descr) VALUES (3, 'Operadora SBA') ");
					pg_query($Conec, "INSERT INTO ".$xProj.".cesbmenu (id, descr) VALUES (4, 'Controle Ar Cond 1') ");
					pg_query($Conec, "INSERT INTO ".$xProj.".cesbmenu (id, descr) VALUES (5, 'Controle Ar Cond 2') ");
					pg_query($Conec, "INSERT INTO ".$xProj.".cesbmenu (id, descr) VALUES (6, 'Controle Ar Cond 3') ");
				}

				//0026
				 pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS guardaescala character varying(10);");

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