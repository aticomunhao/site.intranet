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
				//0078
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".escaladaf_turnos ADD COLUMN IF NOT EXISTS valeref smallint NOT NULL DEFAULT 1 ");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".escaladaf_ins ADD COLUMN IF NOT EXISTS valepag smallint NOT NULL DEFAULT 1 ");
				//0079
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS cargo_daf VARCHAR(50) ");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS visucargo_daf smallint NOT NULL DEFAULT 1 ");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS primcargo_daf smallint NOT NULL DEFAULT 1 ");

				$rs = pg_query($Conec, "SELECT id, TO_CHAR(dataescalafer, 'DD/MM/YYYY') FROM ".$xProj.".escaladaf_fer WHERE DATE_PART('YEAR', dataescalafer) = 2025 ");
				$row = pg_num_rows($rs);
				if($row > 0){
					while($tbl = pg_fetch_row($rs)){
						$Cod = $tbl[0];
						$Data = $tbl[1];
						$Proc = explode("/", $Data);
						$Dia = $Proc[0];
						if(strLen($Dia) < 2){
							$Dia = "0".$Dia;
						}
						$Mes = $Proc[1];
						$Feriado = "2024/".$Mes."/".$Dia;
						pg_query($Conec, "UPDATE ".$xProj.".escaladaf_fer SET dataescalafer = '$Feriado' WHERE  id = $Cod ");
					}
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