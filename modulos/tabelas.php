<?php
//tabelas necessárias
require_once("config/abrealas.php");
pg_query($Conec, "DELETE FROM ".$xProj.".calendev WHERE ativo = 0"); //Elimina dados apagados da tabela calendário
pg_query($Conec, "DELETE FROM ".$xProj.".calendev WHERE ((CURRENT_DATE - dataini)/365 > 5)"); //Apaga da tabela calendário eventos passados há mais de 5 anos
pg_query($Conec, "DELETE FROM ".$xProj.".leitura_agua WHERE ((CURRENT_DATE - dataleitura)/365 > 5)"); //Apaga da tabela lançamentos de leitura do hidrômetro passados há mais de 5 anos
pg_query($Conec, "DELETE FROM ".$xProj.".tarefas WHERE datains < CURRENT_DATE - interval '5 years' "); //Apaga da tabela lançamentos de tarefas há mais de 5 anos
pg_query($Conec, "DELETE FROM ".$xProj.".tarefas_msg WHERE datamsg < CURRENT_DATE - interval '5 years' "); //Apaga mensagens trocadas nas tarefas há mais de 5 anos
pg_query($Conec, "DELETE FROM ".$xProj.".livroreg WHERE datains < CURRENT_DATE - interval '5 years' "); //Apaga registros do livro de ocorrências há mais de 5 anos
pg_query($Conec, "DELETE FROM ".$xProj.".poslog WHERE datainat < CURRENT_DATE - interval '5 years' "); //Apaga registros de usuários inativados há mais de 5 anos
pg_query($Conec, "DELETE FROM ".$xProj.".poslog WHERE numacessos = 0 And datains < CURRENT_DATE - interval '5 years' "); //Apaga registros de usuários inseridos há mais de 5 anos sem nenhum login 

echo "<br>";


$rs = pg_query($Conec, "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'bensachados' AND COLUMN_NAME = 'cpfpropriet'");
$row = pg_num_rows($rs);
if($row == 0){
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
      VALUES (NOW(), NOW(), 6, NOW(), 'Carteira marrom contendo duzentos e quarenta e dois reais e cinquenta centavos, e vários documentos de identidade, cartão de crédito número 0000 000 000 000 000 e um santinho...', 'Na calçada em frente ao prédio', 'Fulano de Tal', 'Não informou', '0001/2024'                      )  ");
      pg_query($Conec, "INSERT INTO ".$xProj.".bensachados (datareceb, dataachou, codusuins, datains, descdobem, localachou, nomeachou, telefachou, numprocesso) 
      VALUES ('2024-03-10', NOW(), 153, NOW(), 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident.', 'No salao', 'Sicrano de Tal', '(61) 9 999-9999', '0002/2024' )  ");
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


pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".paramsis (
   idpar integer NOT NULL,
   admvisu smallint DEFAULT 0,
   admcad smallint DEFAULT 0,
   admedit smallint DEFAULT 0,
   insaniver smallint DEFAULT 4,
   editaniver smallint DEFAULT 4,
   insevento smallint DEFAULT 4,
   editevento smallint DEFAULT 4,
   instarefa smallint DEFAULT 4,
   edittarefa smallint DEFAULT 4,
   insocor smallint DEFAULT 2,
   editocor smallint DEFAULT 2,
   insramais smallint DEFAULT 7,
   editramais smallint DEFAULT 7,
   instelef smallint DEFAULT 4,
   edittelef smallint DEFAULT 4,
   instroca smallint DEFAULT 4,
   edittroca smallint DEFAULT 4,
   editpagina smallint DEFAULT 4,
   insarq smallint DEFAULT 4,
   icustom character varying(255),
   datainiagua date DEFAULT '2024-03-01'::date,
   valoriniagua double precision DEFAULT 1696.485,
   datainieletric date DEFAULT '2024-03-01'::date,
   valorinieletric double precision DEFAULT 1000.001,
   insleituraagua smallint DEFAULT 0 NOT NULL,
   editleituraagua smallint DEFAULT 0 NOT NULL,
   insleituraeletric smallint DEFAULT 0 NOT NULL,
   editleituraeletric smallint DEFAULT 0 NOT NULL,
   dataelim date DEFAULT '2023-10-09'::date,
   inslro smallint DEFAULT 2 NOT NULL,
   editlro smallint DEFAULT 4 NOT NULL,
   insbens smallint DEFAULT 2 NOT NULL,
   editbens smallint DEFAULT 4 NOT NULL,
   pico_online integer DEFAULT 0 NOT NULL,
   data_pico_online timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
   pico_dia integer DEFAULT 0 NOT NULL,
   data_pico_dia timestamp without time zone DEFAULT CURRENT_TIMESTAMP ) ");

   echo "Tabela ".$xProj.".paramsis checada. <br>";


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
      descturno VARCHAR(30) ) 
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
      pessoas_id bigint DEFAULT 0 NOT NULL,
      ativo smallint DEFAULT 1 NOT NULL,
      adm smallint DEFAULT 1 NOT NULL,
      codsetor smallint DEFAULT 1 NOT NULL,
      numacessos integer DEFAULT 0 NOT NULL,
      logini timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
      logfim timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
      usuins integer DEFAULT 0 NOT NULL,
      datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
      usumodif integer DEFAULT 0 NOT NULL,
      datamodif timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
      usuinat integer DEFAULT 0 NOT NULL,
      datainat timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
      motivoinat smallint DEFAULT 0 NOT NULL,
      avcalend smallint DEFAULT 1 NOT NULL,
      avhoje date,
      cpf character varying(20),
      nomeusual character varying(50),
      nomecompl character varying(150),
      senha character varying(255),
      sexo smallint DEFAULT 1 NOT NULL,
      lro smallint DEFAULT 0 NOT NULL,
      bens smallint DEFAULT 0 NOT NULL,
      agua smallint DEFAULT 0 NOT NULL,
      eletric smallint DEFAULT 0 NOT NULL,
      fisclro smallint DEFAULT 0 NOT NULL 
      ) ");
   
   echo "Tabela ".$xProj.".poslog checada. <br>";



   pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".leitura_agua (
      id SERIAL PRIMARY KEY, 
      dataleitura date,
      leitura1 double precision,
      leitura2 double precision,
      leitura3 double precision,
      ativo smallint DEFAULT 1 NOT NULL,
      usuins integer DEFAULT 0 NOT NULL,
      datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
      usumodif integer DEFAULT 0 NOT NULL,
      datamodif timestamp without time zone DEFAULT CURRENT_TIMESTAMP
      ) ");
   
      echo "Tabela ".$xProj.".leitura_agua. <br>";   

   pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".leitura_eletric (
      id SERIAL PRIMARY KEY, 
      dataleitura date,
      leitura1 double precision,
      ativo smallint DEFAULT 1 NOT NULL,
      usuins integer DEFAULT 0 NOT NULL,
      datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
      usumodif integer DEFAULT 0 NOT NULL,
      datamodif timestamp without time zone DEFAULT CURRENT_TIMESTAMP) 
      ");
   echo "Tabela ".$xProj.".leitura_eletric. <br>";   


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
      descarqant character varying(200) 
      ) 
      ");
      echo "Tabela ".$xProj.".carousel checada. <br>";

//   pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".controle_ar");
   pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".controle_ar (
      id SERIAL PRIMARY KEY, 
      num_ap integer NOT NULL DEFAULT 0,
      localap VARCHAR(50),

      data01 date,
      nome01 VARCHAR(100),
      usuins01 integer DEFAULT 0 NOT NULL,
      datains01 timestamp without time zone DEFAULT '1500-01-01',

      data02 date,
      nome02 VARCHAR(100),
      usuins02 integer DEFAULT 0 NOT NULL,
      datains02 timestamp without time zone DEFAULT '1500-01-01',

      data03 date,
      nome03 VARCHAR(100),
      usuins03 integer DEFAULT 0 NOT NULL,
      datains03 timestamp without time zone DEFAULT '1500-01-01',

      data04 date,
      nome04 VARCHAR(100),
      usuins04 integer DEFAULT 0 NOT NULL,
      datains04 timestamp without time zone DEFAULT '1500-01-01',

      data05 date,
      nome05 VARCHAR(100),
      usuins05 integer DEFAULT 0 NOT NULL,
      datains05 timestamp without time zone DEFAULT '1500-01-01',

      data06 date,
      nome06 VARCHAR(100),
      usuins06 integer DEFAULT 0 NOT NULL,
      datains06 timestamp without time zone DEFAULT '1500-01-01',
      
      data07 date,
      nome07 VARCHAR(100),
      usuins07 integer DEFAULT 0 NOT NULL,
      datains07 timestamp without time zone DEFAULT '1500-01-01',

      data08 date,
      nome08 VARCHAR(100),
      usuins08 integer DEFAULT 0 NOT NULL,
      datains08 timestamp without time zone DEFAULT '1500-01-01',

      data09 date,
      nome09 VARCHAR(100),
      usuins09 integer DEFAULT 0 NOT NULL,
      datains09 timestamp without time zone DEFAULT '1500-01-01',

      data10 date,
      nome10 VARCHAR(100),
      usuins10 integer DEFAULT 0 NOT NULL,
      datains10 timestamp without time zone DEFAULT '1500-01-01',

      data11 date,
      nome11 VARCHAR(100),
      usuins11 integer DEFAULT 0 NOT NULL,
      datains11 timestamp without time zone DEFAULT '1500-01-01',

      data12 date,
      nome12 VARCHAR(100),
      usuins12 integer DEFAULT 0 NOT NULL,
      datains12 timestamp without time zone DEFAULT '1500-01-01',

      empresa_id smallint DEFAULT 0 NOT NULL,
      ativo smallint DEFAULT 1 NOT NULL
      ) 
   ");

   echo "Tabela ".$xProj.".controle_ar checada. <br>";

   $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".controle_ar LIMIT 5");
   $row = pg_num_rows($rs);
   if($row == 0){
      for ($i = 1; $i <= 68; $i++) {
         pg_query($Conec, "INSERT INTO ".$xProj.".controle_ar (num_ap, empresa_id) VALUES ($i, 1)");
      }
   }

   pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".empresas_ar (
      id SERIAL PRIMARY KEY, 
      empresa VARCHAR(150),
      ativo smallint DEFAULT 1 NOT NULL
      ) 
   ");

   $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".empresas_ar LIMIT 5");
   $row = pg_num_rows($rs);
   if($row == 0){
      pg_query($Conec, "INSERT INTO ".$xProj.".empresas_ar (empresa, ativo) VALUES ('Empresa Contratada', 1)");
   }

   echo "<br><br>";
