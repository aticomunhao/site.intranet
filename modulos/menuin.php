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
			if(strtotime('2024/12/30') > strtotime(date('Y/m/d'))){
				require_once(dirname(__FILE__)."/config/abrealas.php");
				//0063
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS encbens smallint NOT NULL DEFAULT 0 ;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS orgtarefa smallint NOT NULL DEFAULT 60 ;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS usumodiforg bigint NOT NULL DEFAULT 0 ;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS datamodiforg timestamp without time zone DEFAULT '3000-12-31' ;");
				pg_query($Conec, "UPDATE ".$xProj.".poslog SET orgtarefa = 60 WHERE orgtarefa = 0;");

				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".tarefas ADD COLUMN IF NOT EXISTS orgins smallint NOT NULL DEFAULT 60 ;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".tarefas ADD COLUMN IF NOT EXISTS orgexec smallint NOT NULL DEFAULT 60 ;");

				$rs = pg_query($Conec, "SELECT orgtarefa FROM ".$xProj.".poslog WHERE pessoas_id = 2057 And orgtarefa = 60;");
				$row = pg_num_rows($rs);
                if($row > 0){
					pg_query($Conec, "UPDATE ".$xProj.".poslog SET orgtarefa = 20 WHERE pessoas_id = 2057;");
					pg_query($Conec, "UPDATE ".$xProj.".poslog SET orgtarefa = 30 WHERE pessoas_id = 2;");
					pg_query($Conec, "UPDATE ".$xProj.".poslog SET orgtarefa = 30 WHERE pessoas_id = 83;");
					pg_query($Conec, "UPDATE ".$xProj.".poslog SET orgtarefa = 40 WHERE pessoas_id = 22;");
					pg_query($Conec, "UPDATE ".$xProj.".poslog SET orgtarefa = 40 WHERE pessoas_id = 44;");
					pg_query($Conec, "UPDATE ".$xProj.".poslog SET orgtarefa = 40 WHERE pessoas_id = 86;");
					pg_query($Conec, "UPDATE ".$xProj.".poslog SET orgtarefa = 40 WHERE pessoas_id = 8;");
					pg_query($Conec, "UPDATE ".$xProj.".poslog SET orgtarefa = 40 WHERE pessoas_id = 37;");

					$rs1 = pg_query($Conec, "SELECT usuins FROM ".$xProj.".tarefas WHERE ativo = 1 GROUP BY usuins;");
					$row1 = pg_num_rows($rs1);
					While ($tbl1 = pg_fetch_row($rs1)){
						$Cod = $tbl1[0];
						$rs2 = pg_query($Conec, "SELECT orgtarefa FROM ".$xProj.".poslog WHERE pessoas_id = $Cod;");
						$row2 = pg_num_rows($rs2);
						if($row2 > 0){
							$tbl2 = pg_fetch_row($rs2);
							$Valor = $tbl2[0];
							pg_query($Conec, "UPDATE ".$xProj.".tarefas SET orgins = $Valor WHERE usuins = $Cod;");
						}
					}
					$rs1 = pg_query($Conec, "SELECT usuexec FROM ".$xProj.".tarefas WHERE ativo = 1 GROUP BY usuexec;");
					$row1 = pg_num_rows($rs1);
					While ($tbl1 = pg_fetch_row($rs1)){
						$Cod = $tbl1[0];
						$rs2 = pg_query($Conec, "SELECT orgtarefa FROM ".$xProj.".poslog WHERE pessoas_id = $Cod;");
						$row2 = pg_num_rows($rs2);
						if($row2 > 0){
							$tbl2 = pg_fetch_row($rs2);
							$Valor = $tbl2[0];
							pg_query($Conec, "UPDATE ".$xProj.".tarefas SET orgexec = $Valor WHERE usuexec = $Cod;");
						}
					}
				}
				$rs = pg_query($Conec, "SELECT encbens FROM ".$xProj.".poslog WHERE pessoas_id = 83 And encbens = 0;");
				$row = pg_num_rows($rs);
                if($row > 0){
					pg_query($Conec, "UPDATE ".$xProj.".poslog SET encbens = 1 WHERE pessoas_id = 83;");
					pg_query($Conec, "UPDATE ".$xProj.".poslog SET encbens = 1 WHERE pessoas_id = 22;");
					pg_query($Conec, "UPDATE ".$xProj.".poslog SET encbens = 1 WHERE pessoas_id = 37;");
					pg_query($Conec, "UPDATE ".$xProj.".poslog SET encbens = 1 WHERE pessoas_id = 86;");
					pg_query($Conec, "UPDATE ".$xProj.".poslog SET adm = 4 WHERE pessoas_id = 22;");
					pg_query($Conec, "UPDATE ".$xProj.".poslog SET adm = 4 WHERE pessoas_id = 37;");
					pg_query($Conec, "UPDATE ".$xProj.".poslog SET adm = 4 WHERE pessoas_id = 86;");
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