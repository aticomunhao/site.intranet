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
			if(strtotime('2024/08/30') > strtotime(date('Y/m/d'))){
				require_once(dirname(__FILE__)."/config/abrealas.php");

				//0036
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS clav smallint NOT NULL DEFAULT 0 ;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS chave smallint NOT NULL DEFAULT 0 ;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS fisc_clav smallint NOT NULL DEFAULT 0 ;");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET clav = 1 WHERE lro = 1;");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET fisc_clav = 1 WHERE fisclro = 1;");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET chave = 1, fisc_clav = 1 WHERE pessoas_id = 83;");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET fisc_clav = 1 WHERE pessoas_id = 3;");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET chave = 1, fisc_clav = 1 WHERE pessoas_id = 2;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS datanasc date DEFAULT '1500-01-01' ;");

				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".tarefas ADD COLUMN IF NOT EXISTS setorins smallint NOT NULL DEFAULT 0 ;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".tarefas ADD COLUMN IF NOT EXISTS setorexec smallint NOT NULL DEFAULT 0 ;");

				$rs0 = pg_query($Conec, "SELECT setorins FROM ".$xProj.".tarefas WHERE setorins = 0 ");
				$row0 = pg_num_rows($rs0);
				if($row0 > 0){
					pg_query($Conec, "UPDATE ".$xProj.".paramsis SET vertarefa = 3 WHERE idpar = 1");

					$rs = pg_query($Conec, "SELECT pessoas_id, codsetor FROM ".$xProj.".poslog ");
					$row = pg_num_rows($rs);
					if($row > 0){
						while($tbl = pg_fetch_row($rs)){
							$Cod = $tbl[0];
							$CodSetor = $tbl[1];
							pg_query($Conec, "UPDATE ".$xProj.".tarefas SET setorins = $CodSetor WHERE usuins = $Cod");
							pg_query($Conec, "UPDATE ".$xProj.".tarefas SET setorexec = $CodSetor WHERE usuexec = $Cod");
						}
					}
				}

				pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".tarefas_gr (
					id SERIAL PRIMARY KEY, 
					usuindiv bigint NOT NULL DEFAULT 0,
					usugrupo bigint NOT NULL DEFAULT 0,
					ativo smallint NOT NULL DEFAULT 1, 
					usuins bigint NOT NULL DEFAULT 0,
					datains timestamp without time zone DEFAULT '3000-12-31',
					usuedit bigint NOT NULL DEFAULT 0,
					dataedit timestamp without time zone DEFAULT '3000-12-31' 
					)
				");
				//0037
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET fisc_clav = 0 WHERE fisc_clav = 1");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET fisc_clav = 1 WHERE pessoas_id = 3");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET fisc_clav = 1 WHERE pessoas_id = 83");

				pg_query($Conec, "UPDATE ".$xProj.".poslog SET bens = 0, fiscbens = 0");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET bens = 1, fiscbens = 1 WHERE fiscbens = 3");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET bens = 1, fiscbens = 1 WHERE fiscbens = 83");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET soinsbens = 1 WHERE lro = 1");

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