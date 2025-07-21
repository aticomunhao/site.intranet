<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title></title>
		<style>
            body{ font: 16px sans-serif; }
			@media (max-width: 742px){
				.MenuEstend{
					width: 100%;
				}
			}
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
			if(strtotime('2025/08/30') > strtotime(date('Y/m/d'))){
				require_once(dirname(__FILE__)."/config/abrealas.php");
				//0123
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS verarqdaf smallint NOT NULL DEFAULT 0;");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET verarqdaf = 1 WHERE pessoas_id = 3 Or pessoas_id = 83");
				//0124
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".escaladaf_func ADD COLUMN IF NOT EXISTS id_disc smallint NOT NULL DEFAULT 1;");
				$rs = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf_funcadm WHERE id = 7 And ativo = 1");
				$row =  pg_num_rows($rs);
				if($row > 0){
					pg_query($Conec, "UPDATE ".$xProj.".escaladaf_funcadm SET ativo = 0 WHERE id = 7 Or id = 8 Or id = 9 "); 
				}
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
						<a href="#" onclick="openhref(97);">Celulares Corporativos</a>
					</li>
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