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

				//0049
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS clav2 smallint NOT NULL DEFAULT 0 ;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS chave2 smallint NOT NULL DEFAULT 0 ;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS fisc_clav2 smallint NOT NULL DEFAULT 0 ;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS clav3 smallint NOT NULL DEFAULT 0 ;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS chave3 smallint NOT NULL DEFAULT 0 ;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS fisc_clav3 smallint NOT NULL DEFAULT 0 ;");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET clav2 = 1, chave2 = 1, fisc_clav2 = 1, clav3 = 1, chave3 = 1, fisc_clav3 = 1 WHERE pessoas_id = 3 ;");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET clav2 = 1, chave2 = 1, fisc_clav2 = 1, clav3 = 1, chave3 = 1, fisc_clav3 = 1 WHERE pessoas_id = 83 ;");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET clav2 = 1, chave2 = 1, fisc_clav2 = 1, clav3 = 1, chave3 = 1, fisc_clav3 = 1 WHERE pessoas_id = 22 ;");

				//0050 - liberação dos claviculários DAF e Chaves Lacradas - OK
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS tempoinat smallint NOT NULL DEFAULT 1800 ");

				//0057
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS grupotarefa smallint NOT NULL DEFAULT 0 ;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS usumodifgrupo bigint NOT NULL DEFAULT 0 ;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS datamodifgrupo timestamp without time zone DEFAULT '3000-12-31' ;");
				$rs = pg_query($Conec, "SELECT grupotarefa FROM ".$xProj.".poslog WHERE grupotarefa > 0;");
				$row = pg_num_rows($rs);
				if($row == 0){
					pg_query($Conec, "UPDATE ".$xProj.".poslog SET grupotarefa = codsetor WHERE codsetor = 7;");
					pg_query($Conec, "UPDATE ".$xProj.".poslog SET grupotarefa = codsetor WHERE codsetor = 19;");
					pg_query($Conec, "UPDATE ".$xProj.".poslog SET grupotarefa = 0 WHERE pessoas_id = 3;");
					pg_query($Conec, "UPDATE ".$xProj.".poslog SET grupotarefa = 7 WHERE pessoas_id = 22;");
					pg_query($Conec, "UPDATE ".$xProj.".poslog SET grupotarefa = 7 WHERE pessoas_id = 34;");
					pg_query($Conec, "UPDATE ".$xProj.".poslog SET grupotarefa = 7 WHERE pessoas_id = 45;");
					pg_query($Conec, "UPDATE ".$xProj.".poslog SET grupotarefa = 7 WHERE pessoas_id = 73;");
					pg_query($Conec, "UPDATE ".$xProj.".poslog SET grupotarefa = 7 WHERE pessoas_id = 2057;");
				}
				//Modificação na destinação dos bens era: Descarte, Destruição, Doação, Venda
				pg_query($Conec, "UPDATE ".$xProj.".bensdestinos SET descdest = 'Descarte' WHERE id = 2 ;");
				pg_query($Conec, "UPDATE ".$xProj.".bensdestinos SET descdest = 'Destruição' WHERE id = 3 ;");
				pg_query($Conec, "UPDATE ".$xProj.".bensdestinos SET descdest = 'Doação' WHERE id = 4 ;");
				pg_query($Conec, "UPDATE ".$xProj.".bensdestinos SET descdest = 'Bazar' WHERE id = 5 ;");


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