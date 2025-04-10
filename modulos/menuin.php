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
				//0101
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS filtros smallint NOT NULL DEFAULT 0 ");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS fisc_filtros smallint NOT NULL DEFAULT 0 ");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET filtros = 1 WHERE pessoas_id = 3");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET filtros = 1 WHERE pessoas_id = 83");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET filtros = 1 WHERE pessoas_id = 37");

				pg_query($Conec, "UPDATE ".$xProj.".carousel SET descarq = 'imgfundo0_C2025.jpg' WHERE codcar = 1");
                pg_query($Conec, "UPDATE ".$xProj.".carousel SET descarq = 'imgfundo1_C2025.jpg' WHERE codcar = 2");
                pg_query($Conec, "UPDATE ".$xProj.".carousel SET descarq = 'imgfundo2_C2025.jpg' WHERE codcar = 3");
                pg_query($Conec, "UPDATE ".$xProj.".carousel SET descarq = 'imgfundo3_C2025.jpg' WHERE codcar = 4");

				//0098
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".chaves ADD COLUMN IF NOT EXISTS chavecompl VARCHAR(10) ");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".chaves2 ADD COLUMN IF NOT EXISTS chavecompl VARCHAR(10) ");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".chaves3 ADD COLUMN IF NOT EXISTS chavecompl VARCHAR(10) ");

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