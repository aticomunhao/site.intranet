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
			if(strtotime('2024/08/30') > strtotime(date('Y/m/d'))){
				require_once(dirname(__FILE__)."/config/abrealas.php");

				 //0035
				 pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".livroreg ADD COLUMN IF NOT EXISTS dataenviado timestamp without time zone DEFAULT '3000-12-31';");
				 pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".livroreg ADD COLUMN IF NOT EXISTS relsubstit text;");
				 pg_query($Conec, "UPDATE ".$xProj.".livroturnos SET descturno = '13h30/19h00' WHERE codturno = 2;");
				 pg_query($Conec, "UPDATE ".$xProj.".livroreg SET descturno = '13h30/19h00' WHERE descturno = '13h15/19h00';");

				//pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".livrocheck");
				pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".livrocheck (
					id SERIAL PRIMARY KEY, 
					setor smallint NOT NULL DEFAULT 0,
					itemverif VARCHAR(250),
					ativo smallint NOT NULL DEFAULT 1, 
					usuins bigint NOT NULL DEFAULT 0,
					datains timestamp without time zone DEFAULT '3000-12-31',
					usuedit bigint NOT NULL DEFAULT 0,
					dataedit timestamp without time zone DEFAULT '3000-12-31' 
					)
				");
				$rs = pg_query($Conec, "SELECT id FROM ".$xProj.".livrocheck LIMIT 3");
				$row = pg_num_rows($rs);
				if($row == 0){
					pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, setor, itemverif, ativo, usuins, datains) VALUES (1, 1, 'Bebedouro com Garrafão', 1, 3, NOW()); ");
					pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, setor, itemverif, ativo, usuins, datains) VALUES (2, 1, 'Cadeira', 1, 3, NOW()); ");
					pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, setor, itemverif, ativo, usuins, datains) VALUES (3, 1, 'Câmera', 1, 3, NOW()); ");
					pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, setor, itemverif, ativo, usuins, datains) VALUES (4, 1, 'Chaves do Claviculário', 1, 3, NOW()); ");
					pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, setor, itemverif, ativo, usuins, datains) VALUES (5, 1, 'Carregador dos Rádiocomunicadores', 1, 3, NOW()); ");
					pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, setor, itemverif, ativo, usuins, datains) VALUES (6, 1, 'Chaves lacradas do claviculário', 1, 3, NOW()); ");
					pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, setor, itemverif, ativo, usuins, datains) VALUES (7, 1, 'Computador', 1, 3, NOW()); ");
					pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, setor, itemverif, ativo, usuins, datains) VALUES (8, 1, 'Correspondências ou encomendas entregues para a Casa', 1, 3, NOW()); ");
					pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, setor, itemverif, ativo, usuins, datains) VALUES (9, 1, 'Doações recebidas dentro da Guarita', 1, 3, NOW()); ");
					pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, setor, itemverif, ativo, usuins, datains) VALUES (10, 1, 'Extintor de incêndio', 1, 3, NOW()); ");
					pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, setor, itemverif, ativo, usuins, datains) VALUES (11, 1, 'Formulário de Achados e Perdidos', 1, 3, NOW()); ");
					pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, setor, itemverif, ativo, usuins, datains) VALUES (12, 1, 'Formulário de Controle do Claviculário', 1, 3, NOW()); ");
					pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, setor, itemverif, ativo, usuins, datains) VALUES (13, 1, 'Formulário de Controle dos Rádiocomunicadores', 1, 3, NOW()); ");
					pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, setor, itemverif, ativo, usuins, datains) VALUES (14, 1, 'Formulário de Controle de viaturas - pernoite', 1, 3, NOW()); ");
					pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, setor, itemverif, ativo, usuins, datains) VALUES (15, 1, 'Formulário de Utilização de Vagas', 1, 3, NOW()); ");
					pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, setor, itemverif, ativo, usuins, datains) VALUES (16, 1, 'Formulário de Utilização de Vagas no Estacionamento', 1, 3, NOW()); ");
					pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, setor, itemverif, ativo, usuins, datains) VALUES (17, 1, 'LRO - Formulários', 1, 3, NOW()); ");
					pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, setor, itemverif, ativo, usuins, datains) VALUES (18, 1, 'Monitor Câmera', 1, 3, NOW()); ");
					pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, setor, itemverif, ativo, usuins, datains) VALUES (19, 1, 'Objetos Perdidos', 1, 3, NOW()); ");
					pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, setor, itemverif, ativo, usuins, datains) VALUES (20, 1, 'Pasta com Normas', 1, 3, NOW()); ");
					pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, setor, itemverif, ativo, usuins, datains) VALUES (21, 1, 'Problemas com elevadores', 1, 3, NOW()); ");
					pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, setor, itemverif, ativo, usuins, datains) VALUES (22, 1, 'Problemas nas instalações - Portas, janelas, etc.', 1, 3, NOW()); ");
					pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, setor, itemverif, ativo, usuins, datains) VALUES (23, 1, 'Rádiocomunicadores', 1, 3, NOW()); ");
					pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, setor, itemverif, ativo, usuins, datains) VALUES (24, 1, 'Relógio de parede', 1, 3, NOW()); ");
					pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, setor, itemverif, ativo, usuins, datains) VALUES (25, 1, 'Telefone Celular', 1, 3, NOW()); ");
					pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, setor, itemverif, ativo, usuins, datains) VALUES (26, 1, 'Telefone Ramal 1804', 1, 3, NOW()); ");
					pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, setor, itemverif, ativo, usuins, datains) VALUES (27, 1, 'Veículos e moto da Comunhão pernoitando na Casa', 1, 3, NOW()); ");
					pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, setor, itemverif, ativo, usuins, datains) VALUES (28, 1, 'Veículos particulares pernoitando na casa', 1, 3, NOW()); ");
					pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, setor, itemverif, ativo, usuins, datains) VALUES (29, 1, 'Ventilador', 1, 3, NOW()); ");
				}
				//pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".coletnomes");
				pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".coletnomes (
					id SERIAL PRIMARY KEY, 
					setor smallint NOT NULL DEFAULT 0,
					nomecolet VARCHAR(100),
					ativo smallint NOT NULL DEFAULT 1, 
					usuins bigint NOT NULL DEFAULT 0,
					datains timestamp without time zone DEFAULT '3000-12-31',
					usuedit bigint NOT NULL DEFAULT 0,
					dataedit timestamp without time zone DEFAULT '3000-12-31' 
					)
				");


				
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