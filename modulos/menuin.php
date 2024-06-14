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
							pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS nomeusual VARCHAR(50)");
							pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS agua smallint NOT NULL DEFAULT 0");
							pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS eletric smallint NOT NULL DEFAULT 0");
							pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS lro smallint NOT NULL DEFAULT 0;");     // preencher LRO 
							pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS fisclro smallint NOT NULL DEFAULT 0;"); // fiscalizar o LRO 
							pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS arcond smallint NOT NULL DEFAULT 0");
							pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS arfisc smallint NOT NULL DEFAULT 0");
							pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS bens smallint NOT NULL DEFAULT 0;"); // 1 - bens Achados e perdidos 
							pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".pessoas ADD COLUMN IF NOT EXISTS nome_resumido VARCHAR(50)");
							pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS dataelim date DEFAULT '2023-10-09'");
							pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS pico_online int NOT NULL DEFAULT 0;");
							pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS data_pico_online timestamp without time zone DEFAULT CURRENT_TIMESTAMP;");
							pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS pico_dia int NOT NULL DEFAULT 0;");
							pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS data_pico_dia timestamp without time zone DEFAULT CURRENT_TIMESTAMP;");
							pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS inslro smallint NOT NULL DEFAULT 2;");  //  preencher LRO 
							pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS editlro smallint NOT NULL DEFAULT 4;"); //  editar LRO 
							pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS insbens smallint NOT NULL DEFAULT 2;");  //  preencher Bens achados 
							pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS editbens smallint NOT NULL DEFAULT 4;"); // editar Bens achados
							pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".livroreg ADD COLUMN IF NOT EXISTS ocor smallint NOT NULL DEFAULT 0;"); // se houve ocorrencias
							pg_query($Conec, "UPDATE ".$xProj.".poslog SET datainat = '3000-12-31' ");
							pg_query($Conec, "UPDATE ".$xProj.".poslog SET datamodif = '3000-12-31' WHERE datamodif IS NULL Or datamodif = '1500-01-01'");
							pg_query($Conec, "UPDATE ".$xProj.".poslog SET logini = '3000-12-31' WHERE logini IS NULL Or logini = '1500-01-01'");
							pg_query($Conec, "UPDATE ".$xProj.".poslog SET adm = 7, ativo = 1 WHERE cpf = '13652176049'");
							pg_query($Conec, "UPDATE ".$xProj.".poslog SET ativo = 1 WHERE cpf IS NOT NULL ");
							pg_query($Conec, "UPDATE ".$xProj.".poslog SET adm = 2 WHERE adm = 0 Or adm IS NULL ");
							pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".livroreg ADD COLUMN IF NOT EXISTS enviado smallint NOT NULL DEFAULT 0;"); //  fechar registro no LRO 
							pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis DROP COLUMN IF EXISTS insaguaindiv");
							pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis DROP COLUMN IF EXISTS inseletricindiv");
							pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis DROP COLUMN IF EXISTS editlroindiv");
							pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis DROP COLUMN IF EXISTS editbensindiv");
							pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis DROP COLUMN IF EXISTS edibensindiv");
						 	pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog DROP COLUMN IF EXISTS nome_completo");
							pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog DROP COLUMN IF EXISTS dt_nascimento");
							pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS prazodel smallint NOT NULL DEFAULT 5");
							pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".bensachados ADD COLUMN IF NOT EXISTS usuapagou smallint NOT NULL DEFAULT 0;");
							pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".bensachados ADD COLUMN IF NOT EXISTS dataapagou timestamp without time zone DEFAULT '3000-12-31'");
							pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".ramais_int ADD COLUMN IF NOT EXISTS poslog_id bigint NOT NULL DEFAULT 0;");

						}

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
						<a href="#" onclick="openhref(56);">Ramais Externos</a>
					</li>
				</ul>
			</li>
            <li class="MenuEstend">
				<a href="#" onclick="openhref(99);">Login</a>
			</li>
        </ul>
    </body>
</html>