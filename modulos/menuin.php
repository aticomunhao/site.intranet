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
				//0093
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS viatura smallint NOT NULL DEFAULT 0 ");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS fisc_viat smallint NOT NULL DEFAULT 0 ");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS tema smallint NOT NULL DEFAULT 0 ");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET viatura = 1 WHERE pessoas_id = 3");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET viatura = 1 WHERE pessoas_id = 83");
                pg_query($Conec, "UPDATE ".$xProj.".poslog SET viatura = 1 WHERE pessoas_id = 22");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".contratos1 ADD COLUMN IF NOT EXISTS emvigor smallint NOT NULL DEFAULT 1 ");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".contratos2 ADD COLUMN IF NOT EXISTS emvigor smallint NOT NULL DEFAULT 1 ");

				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".leitura_eletric ADD COLUMN IF NOT EXISTS dataleitura5 date DEFAULT CURRENT_TIMESTAMP ");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".leitura_eletric ADD COLUMN IF NOT EXISTS leitura5 double precision NOT NULL DEFAULT 0 ");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".leitura_eletric ADD COLUMN IF NOT EXISTS consdiario5 double precision NOT NULL DEFAULT 0 ");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS eletric5 smallint NOT NULL DEFAULT 0 ;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS datainieletric5 date DEFAULT '3000-12-31' ");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS valorinieletric5 double precision NOT NULL DEFAULT 0 ");

				//0092
				pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".usulog (
					id SERIAL PRIMARY KEY, 
					pessoas_id bigint NOT NULL DEFAULT 0, 
					datalogin timestamp without time zone DEFAULT CURRENT_TIMESTAMP, 
					datalogout timestamp without time zone DEFAULT CURRENT_TIMESTAMP, 
					navegador VARCHAR(20),
					ativo smallint NOT NULL DEFAULT 1 
					)" 
				);

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