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
			if(strtotime('2025/06/30') > strtotime(date('Y/m/d'))){
				require_once(dirname(__FILE__)."/config/abrealas.php");
				//0117
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET ativo = 0 WHERE pessoas_id = 33");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET ativo = 0 WHERE pessoas_id = 16");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET ativo = 0 WHERE pessoas_id = 9");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET ativo = 0 WHERE pessoas_id = 39");

			   	pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".usuarios_elim (
			    	id SERIAL PRIMARY KEY, 
      				pessoas_id bigint DEFAULT 0 NOT NULL,
					cpf character varying(20),
					nomecompl character varying(150),
      				nomeusual character varying(50),
      				sexo smallint DEFAULT 1 NOT NULL,
					datanasc date DEFAULT '1500-01-01',
      				codsetor smallint DEFAULT 1 NOT NULL,
					siglasetor character varying(10),
      				numacessos integer DEFAULT 0 NOT NULL,
					datainat timestamp without time zone DEFAULT CURRENT_TIMESTAMP 
	  			) ");
				
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