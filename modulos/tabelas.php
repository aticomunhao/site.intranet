<?php
//tabelas necessárias
require_once("config/abrealas.php");
pg_query($Conec, "DELETE FROM ".$xProj.".calendev WHERE ativo = 0"); //Elimina dados apagados da tabela calendário
pg_query($Conec, "DELETE FROM ".$xProj.".calendev WHERE ((CURRENT_DATE - dataini)/365 > 5)"); //Apaga da tabela calendário eventos passados há mais de 5 anos
pg_query($Conec, "DELETE FROM ".$xProj.".leitura_agua WHERE ((CURRENT_DATE - dataleitura)/365 > 5)"); //Apaga da tabela lançamentos de leitura do hidrômetro passados há mais de 5 anos
pg_query($Conec, "DELETE FROM ".$xProj.".tarefas WHERE datains < CURRENT_DATE - interval '5 years' "); //Apaga da tabela lançamentos de tarefas há mais de 5 anos
pg_query($Conec, "DELETE FROM ".$xProj.".tarefas_msg WHERE datamsg < CURRENT_DATE - interval '5 years' "); //Apaga mensagens trocadas nas tarefas há mais de 5 anos
//pg_query($Conec, "DELETE FROM ".$xProj.".livroreg WHERE datains < CURRENT_DATE - interval '5 years' "); //Apaga registros do livro de ocorrências há mais de 5 anos

//$Senha = password_hash('123456789', PASSWORD_DEFAULT);
//pg_query($Conec, "UPDATE ".$xProj.".poslog SET senha = '$Senha' WHERE senha IS NULL");

//echo password_hash('123456', PASSWORD_DEFAULT);
//echo "<br>";
//echo password_hash('123456', PASSWORD_ARGON2I);
//echo "<br>";
//echo password_hash('123456', PASSWORD_BCRYPT);
echo "<br>";


//pg_query($Conec, "CREATE TABLE ".$xProj.".bensachados_ant AS TABLE ".$xProj.".bensachados");
if(strtotime('2024/05/15') > strtotime(date('Y/m/d'))){
   pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".bensachados");
}
pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".bensachados (
   id SERIAL PRIMARY KEY, 
   datareceb date, 
   dataachou date, 
   codusuins bigint NOT NULL DEFAULT 0,
   datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP, 

   descdobem text,
   localachou text, 
   nomeachou VARCHAR(200),
   telefachou VARCHAR(50),

   usumodif bigint NOT NULL DEFAULT 0,
   datamodif timestamp without time zone DEFAULT '3000-12-31',
   ativo smallint NOT NULL DEFAULT 1, 

   numprocesso VARCHAR(50), 
   usuguarda bigint NOT NULL DEFAULT 0,
   dataguarda timestamp without time zone DEFAULT '3000-12-31', 

   nomepropriet VARCHAR(200),
   cpfpropriet VARCHAR(20),
   telefpropriet VARCHAR(50),

   usurestit bigint NOT NULL DEFAULT 0,
   datarestit timestamp without time zone DEFAULT '3000-12-31', 

   usucsg bigint NOT NULL DEFAULT 0,
   datarcbcsg timestamp without time zone DEFAULT '3000-12-31', 

   usudestino bigint NOT NULL DEFAULT 0,
   datadestino date, 
   setordestino VARCHAR(200), 
   nomerecebeudestino VARCHAR(200), 
   destinonodestino integer NOT NULL DEFAULT 0, 
   dataarquivou date,
   usuarquivou bigint NOT NULL DEFAULT 0
   )
   ");

   $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".bensachados LIMIT 5");
   $row = pg_num_rows($rs);
   if($row == 0){
      pg_query($Conec, "INSERT INTO ".$xProj.".bensachados (datareceb, dataachou, codusuins, datains, descdobem, localachou, nomeachou, telefachou, numprocesso) 
      VALUES (NOW(), NOW(), 6, NOW(), 'Carteira marrom contendo duzentos e quarenta e dois reais e cinquenta centavos, e vários documentos de identidade, cartão de crédito número 0000 000 000 000 000 e um santinho....Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', 'Na calçada em frente ao prédio', 'Fulano de Tal', 'Não informou', '0001/2024'                      )  ");
      pg_query($Conec, "INSERT INTO ".$xProj.".bensachados (datareceb, dataachou, codusuins, datains, descdobem, localachou, nomeachou, telefachou, numprocesso) 
      VALUES ('2024-03-10', NOW(), 153, NOW(), 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'No salao', 'Sicrano de Tal', '(61) 9 999-9999', '0002/2024'                      )  ");

   }

   echo "Tabela ".$xProj.".bensachados checada. <br>";

   pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".bensdestinos (
      id SERIAL PRIMARY KEY, 
      numdest integer NOT NULL DEFAULT 0,
      descdest VARCHAR(50) ) 
   ");

   $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".bensdestinos LIMIT 5");
   $row = pg_num_rows($rs);
   if($row == 0){
      pg_query($Conec, "INSERT INTO ".$xProj.".bensdestinos (id, numdest, descdest)  VALUES (1, 0, '')");
      pg_query($Conec, "INSERT INTO ".$xProj.".bensdestinos (id, numdest, descdest)  VALUES (2, 1, 'Descarte')");
      pg_query($Conec, "INSERT INTO ".$xProj.".bensdestinos (id, numdest, descdest)  VALUES (3, 2, 'Destruição')");
      pg_query($Conec, "INSERT INTO ".$xProj.".bensdestinos (id, numdest, descdest)  VALUES (4, 3, 'Doação')");
      pg_query($Conec, "INSERT INTO ".$xProj.".bensdestinos (id, numdest, descdest)  VALUES (5, 4, 'Venda')");
   }


if(strtotime('2024/05/18') > strtotime(date('Y/m/d'))){
   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".livroreg ADD COLUMN IF NOT EXISTS enviado smallint NOT NULL DEFAULT 0;"); //  fechar registro no LRO 
	   
   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS lro smallint NOT NULL DEFAULT 0;"); //  preencher LRO 
   pg_query($Conec, "UPDATE ".$xProj.".poslog SET pessoas_id = 153 WHERE cpf = '13652176049'"); // acerto bd comunhão

   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS inslro smallint NOT NULL DEFAULT 2;");  //  preencher LRO 
   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS editlro smallint NOT NULL DEFAULT 4;"); //  editar LRO 
   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS editlroindiv smallint NOT NULL DEFAULT 4;"); // indiv editar LRO 

   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS insbens smallint NOT NULL DEFAULT 2;");  //  preencher Bens achados 
   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS editbens smallint NOT NULL DEFAULT 4;"); // editar Bens achados
   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS editbensindiv smallint NOT NULL DEFAULT 4;"); // indiv editar Bens achados

//   pg_query($Conec, "ALTER TABLE ".$xProj.".paramsis ALTER COLUMN insaguaindiv TYPE bigint ");
   
   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis DROP COLUMN IF EXISTS insaguaindiv");
   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis DROP COLUMN IF EXISTS inseletricindiv");
   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis DROP COLUMN IF EXISTS editlroindiv");
   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis DROP COLUMN IF EXISTS edibensindiv");

   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS insaguaindiv bigint NOT NULL DEFAULT 0;");
   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS inseletricindiv bigint NOT NULL DEFAULT 0;");
   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS editlroindiv bigint NOT NULL DEFAULT 0;");
   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS edibensindiv bigint NOT NULL DEFAULT 0;");
   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS bens smallint NOT NULL DEFAULT 0;"); // 1 - bens Achados e perdidos 
   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog DROP COLUMN IF EXISTS nome_completo");
   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog DROP COLUMN IF EXISTS dt_nascimento");

}


//pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".livroreg");
pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".livroreg (
   id SERIAL PRIMARY KEY, 
   codusu bigint NOT NULL DEFAULT 0,
   codsetor integer NOT NULL DEFAULT 0,
   usuant bigint NOT NULL DEFAULT 0,
   usuprox bigint NOT NULL DEFAULT 0,
   dataocor date, 
   datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP, 
   turno smallint NOT NULL DEFAULT 0, 
   descturno VARCHAR(30), 
   usumodif bigint NOT NULL DEFAULT 0,
   datamodif timestamp without time zone DEFAULT '3000-12-31 00:00:00',
   ativo smallint NOT NULL DEFAULT 1, 
   numrelato VARCHAR(50), 
   enviado smallint NOT NULL DEFAULT 0,  
   relato text ) 
   ");

   //pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".livroturnos");
   pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".livroturnos (
      id SERIAL PRIMARY KEY, 
      codturno smallint NOT NULL DEFAULT 0,
      descturno VARCHAR(30) 
   ) 
      ");
      $rs1 = pg_query($Conec, "SELECT id FROM ".$xProj.".livroturnos ");
      $row1 = pg_num_rows($rs1);
      if($row1 == 0){
         pg_query($Conec, "INSERT INTO ".$xProj.".livroturnos (id, codturno, descturno) VALUES 
         (1,1,'07h00/13h30'),
         (2,2,'13h15/19h00'),
         (3,3,'19h00/07h00')
         ");       
      }
   echo "Tabela ".$xProj.".livroreg checada. <br>";


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
      lro smallint NOT NULL DEFAULT 0,
      bens smallint NOT NULL DEFAULT 0,
      cpf character varying(20), 
      nomecompl character varying(150), 
      senha character varying(255), 
      sexo smallint NOT NULL DEFAULT 1 
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

