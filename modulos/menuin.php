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
				//0026
				 pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS guardaescala character varying(10);");

				 //0029
				 pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS elev smallint NOT NULL DEFAULT 0;");
				 pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS fiscelev smallint NOT NULL DEFAULT 0;");

				//0030
				 pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".escalas_gr ADD COLUMN IF NOT EXISTS qtd_turno smallint NOT NULL DEFAULT 1;");
				 pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".escalas_gr ADD COLUMN IF NOT EXISTS ativo smallint NOT NULL DEFAULT 1;");
				 pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".escalas_gr ADD COLUMN IF NOT EXISTS usuins bigint NOT NULL DEFAULT 0;");
				 pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".escalas_gr ADD COLUMN IF NOT EXISTS datains timestamp without time zone DEFAULT '3000-12-31';");
				 pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".escalas_gr ADD COLUMN IF NOT EXISTS usuedit bigint NOT NULL DEFAULT 0;");
				 pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".escalas_gr ADD COLUMN IF NOT EXISTS dataedit timestamp without time zone DEFAULT '3000-12-31';");

				 pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS esc_eft smallint NOT NULL DEFAULT 0;");
				 pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS esc_edit smallint NOT NULL DEFAULT 0;");
				 pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS esc_grupo smallint NOT NULL DEFAULT 0;");
				 pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS esc_fisc smallint NOT NULL DEFAULT 0;");
				 

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