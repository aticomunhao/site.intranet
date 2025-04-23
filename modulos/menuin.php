<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <title></title>
		<style>
            body{ font: 16px sans-serif; }
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
			if(strtotime('2025/04/30') > strtotime(date('Y/m/d'))){
				require_once(dirname(__FILE__)."/config/abrealas.php");
				//105
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS bebed smallint NOT NULL DEFAULT 0 ");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS bebed_fisc smallint NOT NULL DEFAULT 0 ");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET bebed = 1 WHERE pessoas_id = 3");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET bebed = 1 WHERE pessoas_id = 83");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET bebed = 1 WHERE pessoas_id = 22");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET bebed = 1 WHERE pessoas_id = 37");
				//0103
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".usulog ALTER COLUMN datalogout SET DEFAULT '3000-12-31 00:00:00' ");
				pg_query($Conec, "UPDATE ".$xProj.".usulog SET ativo = 0 WHERE datalogout = datalogin");
				pg_query($Conec, "UPDATE ".$xProj.".usulog SET ativo = 0 WHERE TO_CHAR(datalogin, 'MM') = '02' ");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS clav_edit smallint NOT NULL DEFAULT 0 ");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS clav_edit2 smallint NOT NULL DEFAULT 0 ");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS clav_edit3 smallint NOT NULL DEFAULT 0 ");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET clav_edit = fisc_clav WHERE fisc_clav = 1");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET clav_edit2 = fisc_clav2 WHERE fisc_clav2 = 1");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET clav_edit3 = fisc_clav3 WHERE fisc_clav3 = 1");

				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".extintores ADD COLUMN IF NOT EXISTS ext_compl VARCHAR(10) ");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET extsen = 0 WHERE pessoas_id = 30");
				pg_query($Conec, "DROP TABLE IF EXISTS ".$xPes.".chavesprov");
			
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