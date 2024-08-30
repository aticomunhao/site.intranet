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
			if(strtotime('2024/09/30') > strtotime(date('Y/m/d'))){
				require_once(dirname(__FILE__)."/config/abrealas.php");

				//0036 de volta
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".tarefas ADD COLUMN IF NOT EXISTS setorins smallint NOT NULL DEFAULT 0 ;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".tarefas ADD COLUMN IF NOT EXISTS setorexec smallint NOT NULL DEFAULT 0 ;");

								
				//0043
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS dialeit_eletr VARCHAR(2) DEFAULT '08' ");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS fatorcor_eletr VARCHAR(10) DEFAULT '40' ");

				//0044
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".livrocheck ADD COLUMN IF NOT EXISTS marca smallint NOT NULL DEFAULT 0 ");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS valorkwh double precision NOT NULL DEFAULT 0.5 ");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS esc_daf smallint NOT NULL DEFAULT 0 ");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS eft_daf smallint NOT NULL DEFAULT 0 ");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET esc_daf = 1 WHERE pessoas_id = 3 Or pessoas_id = 22 Or pessoas_id = 83");

				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS daf_marca smallint NOT NULL DEFAULT 0;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS daf_turno smallint NOT NULL DEFAULT 0;");
				

				//0045
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".leitura_eletric ADD COLUMN IF NOT EXISTS dataleitura4 date DEFAULT CURRENT_TIMESTAMP;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".leitura_eletric ADD COLUMN IF NOT EXISTS leitura4 double precision NOT NULL DEFAULT 0 ");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".leitura_eletric ADD COLUMN IF NOT EXISTS fator double precision NOT NULL DEFAULT 40 ");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".leitura_eletric ADD COLUMN IF NOT EXISTS valorkwh double precision NOT NULL DEFAULT 0.9925695 ");

				//0046
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS mes_escdaf VARCHAR(10);"); // para guardar o mês de consulta
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS chefe_escdaf smallint NOT NULL DEFAULT 0;"); // chefe da escala DAF
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS enc_escdaf smallint NOT NULL DEFAULT 0;"); // encarregado da escala DAF

				//0047
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".escaladaf_ins ADD COLUMN IF NOT EXISTS destaque smallint NOT NULL DEFAULT 0;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".escaladaf_turnos ADD COLUMN IF NOT EXISTS destaq smallint NOT NULL DEFAULT 0;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".escaladaf_turnos ADD COLUMN IF NOT EXISTS ordemletra smallint NOT NULL DEFAULT 0;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".escaladaf ADD COLUMN IF NOT EXISTS marcadaf smallint NOT NULL DEFAULT 0 ;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".escaladaf ADD COLUMN IF NOT EXISTS liberames smallint NOT NULL DEFAULT 0 ;");

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