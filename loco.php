
<?php

 
	$con_string = "host = localhost port=5432 dbname=cesb user=postgres password=postgres";
            if(@pg_connect($con_string)){
                $Con = @pg_connect($con_string) or die("Não foi possível conectar-se ao banco de dados.");
				echo "Conexão Cesb OK <br>";
            }else{
                echo "Conexão Cesb Falhou <br>";
            }
		

	$con_stringpes = "host= 192.168.1.143 port=5432 dbname=pessoal user=postgres password=scga2298";
		if(@pg_connect($con_stringpes)){
            $Conpes = @pg_connect($con_stringpes) or die("Não foi possível conectar-se ao banco de dados.");
			echo "Conexão Pessoal OK <br>";
        }else{
            echo "Conexão Pessoal Falhou <br>";
        }
