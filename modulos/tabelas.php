<?php
//tabelas necessárias
require_once("config/abrealas.php");
pg_query($Conec, "DELETE FROM ".$xProj.".calendev WHERE ativo = 0"); //Elimina dados apagados da tabela calendário
pg_query($Conec, "DELETE FROM ".$xProj.".calendev WHERE ((CURRENT_DATE - dataini)/365 > 5)"); //Apaga da tabela calendário eventos passados há mais de 5 anos
pg_query($Conec, "DELETE FROM ".$xProj.".leituras WHERE ((CURRENT_DATE - dataleitura)/365 > 5)"); //Apaga da tabela lançamentos de leitura do hidrômetro passados há mais de 5 anos
//Colunas acrescentadaS
pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS avcalend smallint NOT NULL DEFAULT 1;"); // 1 - emitir avisos do calendário
pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS avhoje date ;"); // não quer mais avisos odo calendário só por hoje
pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".calendev ADD COLUMN IF NOT EXISTS avobrig smallint NOT NULL DEFAULT 0 ;"); // marca para mensagem com aviso obrigatório
pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".calendev ADD COLUMN IF NOT EXISTS avok smallint NOT NULL DEFAULT 0 ;"); // marca de que o aviso foi dispensado 


//$Senha = password_hash('123456789', PASSWORD_DEFAULT);
//pg_query($Conec, "UPDATE ".$xProj.".poslog SET senha = '$Senha' WHERE senha IS NULL");


//echo password_hash('123456', PASSWORD_DEFAULT);
//echo "<br>";
//echo password_hash('123456', PASSWORD_ARGON2I);
//echo "<br>";
//echo password_hash('123456', PASSWORD_BCRYPT);
echo "<br>";



pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".poslog (
   id SERIAL PRIMARY KEY, 
   pessoas_id bigint NOT NULL DEFAULT 0, 
   ativo smallint NOT NULL DEFAULT 1, 
   adm smallint NOT NULL DEFAULT 1, 
   codsetor smallint NOT NULL DEFAULT 1, 
   numacessos integer NOT NULL DEFAULT 0, 
   logini timestamp without time zone DEFAULT CURRENT_TIMESTAMP, 
   logfim timestamp without time zone DEFAULT CURRENT_TIMESTAMP, 
   usuins integer NOT NULL DEFAULT 0, 
   datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP, 
   usumodif integer NOT NULL DEFAULT 0, 
   datamodif timestamp without time zone DEFAULT CURRENT_TIMESTAMP, 
   usuinat integer NOT NULL DEFAULT 0, 
   datainat timestamp without time zone DEFAULT CURRENT_TIMESTAMP, 
   motivoinat smallint NOT NULL DEFAULT 0, 
   avcalend smallint NOT NULL DEFAULT 1, 
   avhoje date, 
   cpf character varying(20), 
   nomecompl character varying(150), 
   senha character varying(255) 
   ) ");
   
   echo "Tabela ".$xProj.".poslog checada. <br>";


   pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".arqsetor (
      codarq SERIAL PRIMARY KEY, 
      codsetor int NOT NULL DEFAULT 0,
      codsubsetor int NOT NULL DEFAULT 0,
      descarq character varying(200), 
      usuins int NOT NULL DEFAULT 0,
      datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP, 
      usuapag int NOT NULL DEFAULT 0,
      dataapag date DEFAULT '3000-12-31', 
      ativo smallint NOT NULL DEFAULT 1,
      nomearq character varying(200) ) ");

      echo "Tabela ".$xProj.".arqsetor checada. <br>";


      pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".calendev (
         idev SERIAL PRIMARY KEY, 
         evnum bigint DEFAULT 0,
         titulo character varying(250), 
         cor character varying(10), 
         dataini date, 
         localev text , 
         ativo smallint DEFAULT 1,
         repet smallint DEFAULT 0,
         fixo smallint DEFAULT 0,
         usuins bigint DEFAULT 0,
         usumodif bigint DEFAULT 0,
         usuapag bigint DEFAULT 0,
         datains timestamp without time zone DEFAULT '3000-12-31 00:00:00',
         datamodif timestamp without time zone DEFAULT '3000-12-31 00:00:00',
         dataapag date DEFAULT '3000-12-31',
         avobrig smallint DEFAULT 0,
         avok smallint DEFAULT 0 ) ");

         echo "Tabela ".$xProj.".calendev checada. <br>";


         pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".carousel (
            codcar SERIAL PRIMARY KEY,
            descarq character varying(200), 
            descarqant character varying(200) ) ");

            echo "Tabela ".$xProj.".carousel checada. <br>";

