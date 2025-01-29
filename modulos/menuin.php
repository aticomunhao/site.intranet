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
			if(strtotime('2025/03/30') > strtotime(date('Y/m/d'))){
				require_once(dirname(__FILE__)."/config/abrealas.php");
				//0074
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".escaladaf_turnos ADD COLUMN IF NOT EXISTS infotexto smallint NOT NULL DEFAULT 0 ");
				pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET infotexto = 1 WHERE horaturno = 'FÉRIAS' Or horaturno = 'FOLGA' Or horaturno = 'INSS' Or horaturno = 'AULA IAQ'");

				$rs = pg_query($Conec, "SELECT numprocesso FROM ".$xProj.".bensachados WHERE numprocesso = '0525/2025'");
				$row = pg_num_rows($rs);
				if($row > 0){
					$Proc = 1;
					$rs1 = pg_query($Conec, "SELECT id, numprocesso FROM ".$xProj.".bensachados WHERE TO_CHAR(datareceb, 'YYYY') = '2025' ORDER BY datareceb, id");
					while($tbl1 = pg_fetch_row($rs1)){
						$Cod = $tbl1[0];
						$Num = str_pad(($Proc), 4, "0", STR_PAD_LEFT);
						$NumRelat = $Num."/2025";
						pg_query($Conec, "UPDATE ".$xProj.".bensachados SET numprocesso = '$NumRelat' WHERE id = $Cod ");
						$Proc++;
					}
				}
				//0075
				pg_query($Conec, "UPDATE ".$xProj.".escaladaf SET ativo = 0 WHERE id BETWEEN 696 And 700");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".escaladaf_ins ADD COLUMN IF NOT EXISTS horafolga VARCHAR(11) ");
				//0076
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".tarefas ADD COLUMN IF NOT EXISTS tipotar smallint NOT NULL DEFAULT 1 ");
				//0077
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS areatar smallint NOT NULL DEFAULT 1 ");
				
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