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
				//0057
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS grupotarefa smallint NOT NULL DEFAULT 0 ;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS usumodifgrupo bigint NOT NULL DEFAULT 0 ;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS datamodifgrupo timestamp without time zone DEFAULT '3000-12-31' ;");

				//0058
				//Modificação na destinação dos bens era: Descarte, Destruição, Doação, Venda
				pg_query($Conec, "UPDATE ".$xProj.".bensdestinos SET descdest = 'Almoxarifado' WHERE id = 2 ;");
				pg_query($Conec, "UPDATE ".$xProj.".bensdestinos SET descdest = 'Bazar' WHERE id = 3 ;");
				pg_query($Conec, "UPDATE ".$xProj.".bensdestinos SET descdest = 'Livraria' WHERE id = 4 ;");
				pg_query($Conec, "UPDATE ".$xProj.".bensdestinos SET descdest = 'Manutenção' WHERE id = 5 ;");

				$rs = pg_query($Conec, "SELECT id FROM ".$xProj.".bensdestinos WHERE numdest = 5;");
				$row = pg_num_rows($rs);
				if($row == 0){
					pg_query($Conec, "INSERT INTO ".$xProj.".bensdestinos (id, numdest, descdest)  VALUES (6, 5, 'DIADM')");
					pg_query($Conec, "INSERT INTO ".$xProj.".bensdestinos (id, numdest, descdest)  VALUES (7, 6, 'DIFIN')");
				}
				pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".bensprocessos (
					id SERIAL PRIMARY KEY, 
					processo VARCHAR(50) ) 
				 ");

				$rs = pg_query($Conec, "SELECT id FROM ".$xProj.".bensprocessos WHERE processo != '';");
				$row = pg_num_rows($rs);
				if($row == 0){
					pg_query($Conec, "INSERT INTO ".$xProj.".bensprocessos (id, processo)  VALUES (1, '')");
					pg_query($Conec, "INSERT INTO ".$xProj.".bensprocessos (id, processo)  VALUES (2, 'Descarte')");
					pg_query($Conec, "INSERT INTO ".$xProj.".bensprocessos (id, processo)  VALUES (3, 'Destruição')");
					pg_query($Conec, "INSERT INTO ".$xProj.".bensprocessos (id, processo)  VALUES (4, 'Doação')");
					pg_query($Conec, "INSERT INTO ".$xProj.".bensprocessos (id, processo)  VALUES (5, 'Venda')");
				}

				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".bensachados ADD COLUMN IF NOT EXISTS usuencdestino bigint NOT NULL DEFAULT 0 ;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".bensachados ADD COLUMN IF NOT EXISTS dataencdestino timestamp without time zone DEFAULT '3000-12-31' ;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".bensachados ADD COLUMN IF NOT EXISTS codencdestino smallint NOT NULL DEFAULT 0 ;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".bensachados ADD COLUMN IF NOT EXISTS descencdestino VARCHAR(50) ;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".bensachados ADD COLUMN IF NOT EXISTS codencprocesso smallint NOT NULL DEFAULT 0 ;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".bensachados ADD COLUMN IF NOT EXISTS descencprocesso VARCHAR(50) ;");

				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS fisc_agua smallint NOT NULL DEFAULT 0 ;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS fisc_eletric smallint NOT NULL DEFAULT 0 ;");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET fisc_agua = 1, fisc_eletric = 1 WHERE pessoas_id = 2057 ;"); // Adilson

				pg_query($Conec, "UPDATE ".$xProj.".tarefas SET datasit1 = datains WHERE DATE_PART('YEAR', datasit1) = 3000");
				pg_query($Conec, "UPDATE ".$xProj.".tarefas SET setorexec = 7 WHERE setorexec != 19");

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