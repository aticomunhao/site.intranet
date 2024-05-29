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


if(strtotime('2024/05/30') > strtotime(date('Y/m/d'))){
   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".livroreg ADD COLUMN IF NOT EXISTS enviado smallint NOT NULL DEFAULT 0;"); //  fechar registro no LRO 
	   
   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS lro smallint NOT NULL DEFAULT 0;"); //  preencher LRO 
   pg_query($Conec, "UPDATE ".$xProj.".poslog SET pessoas_id = 153 WHERE cpf = '13652176049'"); // acerto bd comunhão

   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS inslro smallint NOT NULL DEFAULT 2;");  //  preencher LRO 
   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS editlro smallint NOT NULL DEFAULT 4;"); //  editar LRO 

   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS insbens smallint NOT NULL DEFAULT 2;");  //  preencher Bens achados 
   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis ADD COLUMN IF NOT EXISTS editbens smallint NOT NULL DEFAULT 4;"); // editar Bens achados
   
   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis DROP COLUMN IF EXISTS insaguaindiv");
   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis DROP COLUMN IF EXISTS inseletricindiv");
   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis DROP COLUMN IF EXISTS editlroindiv");
   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis DROP COLUMN IF EXISTS editbensindiv");
   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".paramsis DROP COLUMN IF EXISTS edibensindiv");

   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS bens smallint NOT NULL DEFAULT 0;"); // 1 - bens Achados e perdidos 
   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog DROP COLUMN IF EXISTS nome_completo");
   pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog DROP COLUMN IF EXISTS dt_nascimento");

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
   
      echo "Tabela ".$xProj.".poslog leitura_agua. <br>";   

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
echo "Tabela ".$xProj.".poslog leitura_eletric. <br>";   



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

