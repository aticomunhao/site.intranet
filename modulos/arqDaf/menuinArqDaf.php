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
			openhref(99);
        </script>
    </head>
    <body>
        <?php
			$diaSemana = filter_input(INPUT_GET, "diasemana");
			if(!isset($diaSemana)){
				$diaSemana = 1;
			}
			//Provisório
//			if(strtotime('2025/08/30') > strtotime(date('Y/m/d'))){
				require_once("config/abrealasArqDaf.php");
				//001
	            $rsSis = pg_query($Conec, "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = 'cesb' And TABLE_NAME = 'daf_poslog'");
    	        $rowSis = pg_num_rows($rsSis);
        	    if($rowSis == 0){
				   	pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".daf_poslog (
      				id SERIAL PRIMARY KEY, 
    				pessoas_id bigint NOT NULL DEFAULT 0,
    				ativo smallint NOT NULL DEFAULT 1,
    				adm smallint NOT NULL DEFAULT 1,
    				codsetor smallint NOT NULL DEFAULT 1,
    				numacessos integer NOT NULL DEFAULT 0,
    				logini timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    				logfim timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
				    nomecompl character varying(100),
					nomeusual character varying(50),
					datanasc date DEFAULT '1500-01-01'::date,
    				cpf character varying(20),
    				sexo smallint NOT NULL DEFAULT 1,
    				senha character varying(255), 
					extsen smallint NOT NULL DEFAULT 0, 
    				usuins integer NOT NULL DEFAULT 0,
    				datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    				usumodif integer NOT NULL DEFAULT 0,
    				datamodif timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    				usuinat integer NOT NULL DEFAULT 0,
    				datainat timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    				motivoinat smallint NOT NULL DEFAULT 0 
	        		) 
  					");
	            }
			    $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".daf_poslog LIMIT 1");
    			$row = pg_num_rows($rs);
    			if($row == 0){
					pg_query($Conec, "INSERT INTO ".$xProj.".daf_poslog (id, pessoas_id, ativo, adm, codsetor, nomecompl, nomeusual, cpf, sexo, senha, usuins) 
					VALUES (1, 3, 1, 3, 1, 'Ludinir Picelli', 'Ludinir', '13652176049', 1, '$2y$10$1oN/IjTYrfz3xDyjCk4eruZp2IoRSO5GoScLYwcP4cTrc2DjoNRL.', 3)");
					$Senha = password_hash('01305760425', PASSWORD_DEFAULT);
					pg_query($Conec, "INSERT INTO ".$xProj.".daf_poslog (id, pessoas_id, ativo, adm, codsetor, nomecompl, nomeusual, cpf, sexo, senha, usuins) 
					VALUES (2, 83, 1, 2, 1, 'Will Wilson Furtado', 'Will', '01305760425', 1, '$Senha', 3)");
				}

	            $rsSis = pg_query($Conec, "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = 'cesb' And TABLE_NAME = 'daf_setores'");
    	        $rowSis = pg_num_rows($rsSis);
        	    if($rowSis == 0){
					pg_query($Conec, "SELECT * INTO ".$xProj.".daf_setores FROM ".$xProj.".setores WHERE codset < 8 ORDER BY codset ");
					pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".daf_setores DROP COLUMN IF EXISTS textopag ");
					pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".daf_setores ALTER COLUMN siglasetor TYPE VARCHAR(50) ");
					pg_query($Conec, "UPDATE ".$xProj.".daf_setores SET siglasetor = 'Diretório 01' WHERE codset = 2");
					pg_query($Conec, "UPDATE ".$xProj.".daf_setores SET siglasetor = 'Diretório 02' WHERE codset = 3");
					pg_query($Conec, "UPDATE ".$xProj.".daf_setores SET siglasetor = 'Diretório 03' WHERE codset = 4");
					pg_query($Conec, "UPDATE ".$xProj.".daf_setores SET siglasetor = 'Diretório 04' WHERE codset = 5");
					pg_query($Conec, "UPDATE ".$xProj.".daf_setores SET siglasetor = 'Diretório 05' WHERE codset = 6");
					pg_query($Conec, "UPDATE ".$xProj.".daf_setores SET descsetor = '', datains = NOW(), datamodif = NOW(), ativo = 1, cabec2 = 'Diretoria Administrativa e Financeira' ");
					pg_query($Conec, "DELETE FROM ".$xProj.".daf_setores WHERE codset > 6 ");
				}

	            $rsSis = pg_query($Conec, "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = 'cesb' And TABLE_NAME = 'daf_usugrupos'");
    	        $rowSis = pg_num_rows($rsSis);
        	    if($rowSis == 0){
					pg_query($Conec, "SELECT * INTO ".$xProj.".daf_usugrupos FROM ".$xProj.".usugrupos ORDER BY id ");
					pg_query($Conec, "UPDATE ".$xProj.".daf_usugrupos SET adm_fl = id, ativo = 1, datacria = NOW() ");
					pg_query($Conec, "UPDATE ".$xProj.".daf_usugrupos SET adm_nome = 'Usuário' WHERE id = 1 ");
					pg_query($Conec, "UPDATE ".$xProj.".daf_usugrupos SET adm_nome = 'Administrador' WHERE id = 2 ");
					pg_query($Conec, "UPDATE ".$xProj.".daf_usugrupos SET adm_nome = 'Superusuário' WHERE id = 3 ");
					pg_query($Conec, "DELETE FROM ".$xProj.".daf_usugrupos WHERE id > 3");
				}
				$rsSis = pg_query($Conec, "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = 'cesb' And TABLE_NAME = 'daf_arqsetor'");
    	        $rowSis = pg_num_rows($rsSis);
        	    if($rowSis == 0){
					pg_query($Conec, "CREATE TABLE ".$xProj.".daf_arqsetor AS SELECT * FROM ".$xProj.".arqsetor WITH NO DATA ");
					pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".daf_arqsetor DROP COLUMN IF EXISTS codsubsetor ");
				}
//			} // fim data limite
        ?>
		<!-- menu para a página inicial  -->
        <ul id="example" class="sf-menu sf-js-enabled sf-arrows sf-menu-dia<?php echo $diaSemana; ?> ">
            <li class="MenuEstend">
				<a href="#" onclick="openhref(51);">Início</a>
			</li>
            <li class="MenuEstend">
				<a href="#" onclick="openhref(99);">Login</a>
			</li>
        </ul>

    </body>
</html>