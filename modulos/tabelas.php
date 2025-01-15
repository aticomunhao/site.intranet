<?php
//tabelas necessárias
require_once("config/abrealas.php");

echo "<br>";

$rs = pg_query($Conec, "SELECT prazodel FROM ".$xProj.".paramsis WHERE idpar = 1 ");
$row = pg_num_rows($rs);
if($row > 0){ 
    $tbl = pg_fetch_row($rs);
    $PrazoDel = $tbl[0];
}else{
   $PrazoDel = 10;
}
if($PrazoDel < 1000){
   echo "Prazo para eliminação de registros antigos fixado em $PrazoDel anos. <br>";
   pg_query($Conec, "DELETE FROM ".$xProj.".calendev WHERE ativo = 0"); //Elimina dados apagados da tabela calendário
   pg_query($Conec, "DELETE FROM ".$xProj.".calendev WHERE ((CURRENT_DATE - dataini)/365 > $PrazoDel)"); //Apaga da tabela calendário eventos passados há mais de $PrazoDel anos
   pg_query($Conec, "DELETE FROM ".$xProj.".leitura_agua WHERE ((CURRENT_DATE - dataleitura)/365 > $PrazoDel)"); //Apaga da tabela lançamentos de leitura do hidrômetro passados há mais de $PrazoDel anos
   pg_query($Conec, "DELETE FROM ".$xProj.".tarefas WHERE datains < CURRENT_DATE - interval '$PrazoDel years' "); //Apaga da tabela lançamentos de tarefas há mais de $PrazoDel anos
   pg_query($Conec, "DELETE FROM ".$xProj.".tarefas_msg WHERE datamsg < CURRENT_DATE - interval '$PrazoDel years' "); //Apaga mensagens trocadas nas tarefas há mais de $PrazoDel anos
   pg_query($Conec, "DELETE FROM ".$xProj.".livroreg WHERE datains < CURRENT_DATE - interval '$PrazoDel years' "); //Apaga registros do livro de ocorrências há mais de $PrazoDel anos
   pg_query($Conec, "DELETE FROM ".$xProj.".poslog WHERE datainat < CURRENT_DATE - interval '$PrazoDel years' "); //Apaga registros de usuários inativados há mais de $PrazoDel anos
   pg_query($Conec, "DELETE FROM ".$xProj.".poslog WHERE numacessos = 0 And datains < CURRENT_DATE - interval '$PrazoDel years' "); //Apaga registros de usuários inseridos há mais de $PrazoDel anos sem nenhum login 
   pg_query($Conec, "DELETE FROM ".$xProj.".bensachados WHERE datains < CURRENT_DATE - interval '$PrazoDel years' "); //Apaga registros do achados e perdidos há mais de $PrazoDel anos
   pg_query($Conec, "DELETE FROM ".$xProj.".visitas_ar WHERE datavis < CURRENT_DATE - interval '$PrazoDel years' "); 
   pg_query($Conec, "DELETE FROM ".$xProj.".ramais_int WHERE ativo = 0 And datains < CURRENT_DATE - interval '$PrazoDel years'"); 
   pg_query($Conec, "DELETE FROM ".$xProj.".ramais_ext WHERE ativo = 0 And datains < CURRENT_DATE - interval '$PrazoDel years'"); 
   pg_query($Conec, "DELETE FROM ".$xProj.".arqsetor WHERE dataapag < CURRENT_DATE - interval '$PrazoDel years'"); // apaga nome dos arquivos de upload
   pg_query($Conec, "DELETE FROM ".$xProj.".visitas_ar WHERE datavis < CURRENT_DATE - interval '$PrazoDel years'");
   pg_query($Conec, "DELETE FROM ".$xProj.".visitas_ar2 WHERE datavis < CURRENT_DATE - interval '$PrazoDel years'");
   pg_query($Conec, "DELETE FROM ".$xProj.".visitas_ar3 WHERE datavis < CURRENT_DATE - interval '$PrazoDel years'");
   pg_query($Conec, "DELETE FROM ".$xProj.".visitas_el WHERE datavis < CURRENT_DATE - interval '$PrazoDel years'");
   pg_query($Conec, "DELETE FROM ".$xProj.".chaves_ctl WHERE datavolta < CURRENT_DATE - interval '$PrazoDel years'");
   pg_query($Conec, "DELETE FROM ".$xProj.".escaladaf WHERE dataescala < CURRENT_DATE - interval '$PrazoDel years'");
   pg_query($Conec, "DELETE FROM ".$xProj.".escaladaf WHERE dataescala < CURRENT_DATE - interval '2 months' And ativo = 0;");

//   pg_query($Conec, "DELETE FROM ".$xProj.".escalas WHERE dataescala < CURRENT_DATE - interval '$PrazoDel years'");
   

}else{
   echo "Eliminação de registros antigos desativado. <br>";   
}


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
      pg_query($Conec, "INSERT INTO ".$xProj.".bensdestinos (id, numdest, descdest)  VALUES (2, 1, 'Almoxarifado')");
      pg_query($Conec, "INSERT INTO ".$xProj.".bensdestinos (id, numdest, descdest)  VALUES (3, 2, 'Bazar')");
      pg_query($Conec, "INSERT INTO ".$xProj.".bensdestinos (id, numdest, descdest)  VALUES (4, 3, 'Livraria')");
      pg_query($Conec, "INSERT INTO ".$xProj.".bensdestinos (id, numdest, descdest)  VALUES (5, 4, 'Manutenção')");
      pg_query($Conec, "INSERT INTO ".$xProj.".bensdestinos (id, numdest, descdest)  VALUES (6, 5, 'DIADM')");
      pg_query($Conec, "INSERT INTO ".$xProj.".bensdestinos (id, numdest, descdest)  VALUES (7, 6, 'DIFIN')");

   }


   pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".bensprocessos (
      id SERIAL PRIMARY KEY, 
      processo VARCHAR(50) ) 
    ");

   $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".bensprocessos WHERE processo != '';");
   $row = pg_num_rows($rs);
   if($row == 0){
      pg_query($Conec, "INSERT INTO ".$xProj.".bensprocessos (id, processo)  VALUES (1, 'Descarte')");
      pg_query($Conec, "INSERT INTO ".$xProj.".bensprocessos (id, processo)  VALUES (2, 'Destruição')");
      pg_query($Conec, "INSERT INTO ".$xProj.".bensprocessos (id, processo)  VALUES (3, 'Doação')");
      pg_query($Conec, "INSERT INTO ".$xProj.".bensprocessos (id, processo)  VALUES (4, 'Venda')");
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
   data_pico_dia timestamp without time zone DEFAULT CURRENT_TIMESTAMP, 
   prazodel smallint NOT NULL DEFAULT 5,
   vertarefa smallint NOT NULL DEFAULT 1,
   verarquivos smallint NOT NULL DEFAULT 1,
   datainieletric2 date DEFAULT '3000-12-31'::date,
   datainieletric3 date DEFAULT '3000-12-31'::date,
   valorinieletric2 double precision NOT NULL DEFAULT 0,
   valorinieletric3 double precision NOT NULL DEFAULT 0,
   editpagini smallint NOT NULL DEFAULT 2,
   marcaescala character varying(10)
   ) ");

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
         (2,2,'13h30/19h00'),
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
      fisclro smallint DEFAULT 0 NOT NULL, 
      arcond smallint NOT NULL DEFAULT 0,
      arfisc smallint NOT NULL DEFAULT 0,
      eletric2 smallint NOT NULL DEFAULT 0,
      eletric3 smallint NOT NULL DEFAULT 0,
      fisceletric smallint NOT NULL DEFAULT 0,
      fiscbens smallint NOT NULL DEFAULT 0,
      soinsbens smallint NOT NULL DEFAULT 0
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
      colec smallint NOT NULL DEFAULT 0, 
      dataleitura1 date DEFAULT CURRENT_TIMESTAMP,
      leitura1 double precision NOT NULL DEFAULT 0,
      dataleitura2 date DEFAULT CURRENT_TIMESTAMP,
      leitura2 double precision NOT NULL DEFAULT 0,
      dataleitura3 date DEFAULT CURRENT_TIMESTAMP,
      leitura3 double precision NOT NULL DEFAULT 0,
      dataleitura4 date DEFAULT CURRENT_TIMESTAMP,
      leitura4 double precision NOT NULL DEFAULT 0,
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


$rs = pg_query($Conec, "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'controle_ar' AND COLUMN_NAME = 'data01'");
$row = pg_num_rows($rs);
if($row > 0){
    pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".controle_ar");
    pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".visitas_ar");
}

pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".controle_ar (
   id SERIAL PRIMARY KEY, 
   num_ap integer NOT NULL DEFAULT 0,
   localap VARCHAR(50),
   empresa_id smallint DEFAULT 0 NOT NULL,
   ativo smallint DEFAULT 1 NOT NULL, 
   usuins integer DEFAULT 0 NOT NULL,
   datains timestamp without time zone DEFAULT '3000-12-31',
   usuedit integer DEFAULT 0 NOT NULL,
   dataedit timestamp without time zone DEFAULT '3000-12-31' 
   ) 
");
echo "Tabela ".$xProj.".controle_ar checada. <br>";

pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".visitas_ar (
   id SERIAL PRIMARY KEY, 
   controle_id integer NOT NULL DEFAULT 0,
   datavis date,
   tipovis smallint DEFAULT 1 NOT NULL,
   nometec VARCHAR(100),
   empresa_id smallint DEFAULT 0 NOT NULL,
   ativo smallint DEFAULT 1 NOT NULL,
   acionam timestamp without time zone DEFAULT '3000-12-31',
   atendim timestamp without time zone DEFAULT '3000-12-31',
   conclus timestamp without time zone DEFAULT '3000-12-31',
   contato VARCHAR(100),
   acompanh VARCHAR(100),
   defeito text,
   diagtec text,
   svcrealizado text,
   usuins integer DEFAULT 0 NOT NULL,
   datains timestamp without time zone DEFAULT '3000-12-31',
   usuedit integer DEFAULT 0 NOT NULL,
   dataedit timestamp without time zone DEFAULT '3000-12-31',
   usudel integer DEFAULT 0 NOT NULL,
   datadel timestamp without time zone DEFAULT '3000-12-31'
   ) 
");
echo "Tabela ".$xProj.".visitas_ar checada. <br>";


   //guarda os nomes das empresas de manutenção
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
   echo "Tabela ".$xProj.".empresas_ar checada. <br>";


   //guarda os nomes para o menu Controle Ar Cond e Eletricidade
   pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".cesbmenu (
      id SERIAL PRIMARY KEY, 
      descr VARCHAR(100), 
      ativo smallint NOT NULL DEFAULT 1,
         usumodif bigint NOT NULL DEFAULT 0,
      datamodif timestamp without time zone DEFAULT '3000-12-31'
      )
   ");
   $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".cesbmenu LIMIT 2");
   $row = pg_num_rows($rs);
   if($row == 0){
      pg_query($Conec, "INSERT INTO ".$xProj.".cesbmenu (id, descr) VALUES (1, 'Comunhão') ");
      pg_query($Conec, "INSERT INTO ".$xProj.".cesbmenu (id, descr) VALUES (2, 'Operadora Claro') ");
      pg_query($Conec, "INSERT INTO ".$xProj.".cesbmenu (id, descr) VALUES (3, 'Operadora SBA') ");
      pg_query($Conec, "INSERT INTO ".$xProj.".cesbmenu (id, descr) VALUES (4, 'Controle Ar Cond 1') ");
      pg_query($Conec, "INSERT INTO ".$xProj.".cesbmenu (id, descr) VALUES (5, 'Controle Ar Cond 2') ");
      pg_query($Conec, "INSERT INTO ".$xProj.".cesbmenu (id, descr) VALUES (6, 'Controle Ar Cond 3') ");
   }
   echo "Tabela ".$xProj.".cesbmenu checada. <br>";
   
   //coleciona o checklist para LRO (setor 1)
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
		pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, marca, itemnum, itemverif, ativo, usuins, datains) VALUES (1, 0, 1, 'Bebedouro com Garrafão', 1, 3, NOW()); ");
		pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, marca, itemnum, itemverif, ativo, usuins, datains) VALUES (2, 0, 2, 'Cadeira', 1, 3, NOW()); ");
		pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, marca, itemnum, itemverif, ativo, usuins, datains) VALUES (3, 0, 3, 'Câmera', 1, 3, NOW()); ");
		pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, marca, itemnum, itemverif, ativo, usuins, datains) VALUES (4, 0, 4, 'Chaves do Claviculário', 1, 3, NOW()); ");
		pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, marca, itemnum, itemverif, ativo, usuins, datains) VALUES (5, 0, 5, 'Carregador dos Rádiocomunicadores', 1, 3, NOW()); ");
		pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, marca, itemnum, itemverif, ativo, usuins, datains) VALUES (6, 0, 6, 'Chaves lacradas do claviculário', 1, 3, NOW()); ");
		pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, marca, itemnum, itemverif, ativo, usuins, datains) VALUES (7, 0, 7, 'Computador', 1, 3, NOW()); ");
		pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, marca, itemnum, itemverif, ativo, usuins, datains) VALUES (8, 0, 8, 'Correspondências ou encomendas entregues para a Casa', 1, 3, NOW()); ");
		pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, marca, itemnum, itemverif, ativo, usuins, datains) VALUES (9, 0, 9, 'Doações recebidas dentro da Guarita', 1, 3, NOW()); ");
		pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, marca, itemnum, itemverif, ativo, usuins, datains) VALUES (10, 0, 10, 'Extintor de incêndio', 1, 3, NOW()); ");
		pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, marca, itemnum, itemverif, ativo, usuins, datains) VALUES (11, 0, 11, 'Formulário de Achados e Perdidos', 1, 3, NOW()); ");
		pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, marca, itemnum, itemverif, ativo, usuins, datains) VALUES (12, 0, 12, 'Formulário de Controle do Claviculário', 1, 3, NOW()); ");
		pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, marca, itemnum, itemverif, ativo, usuins, datains) VALUES (13, 0, 13, 'Formulário de Controle dos Rádiocomunicadores', 1, 3, NOW()); ");
		pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, marca, itemnum, itemverif, ativo, usuins, datains) VALUES (14, 0, 14, 'Formulário de Controle de viaturas - pernoite', 1, 3, NOW()); ");
		pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, marca, itemnum, itemverif, ativo, usuins, datains) VALUES (15, 0, 15, 'Formulário de Utilização de Vagas', 1, 3, NOW()); ");
		pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, marca, itemnum, itemverif, ativo, usuins, datains) VALUES (16, 0, 16, 'Formulário de Utilização de Vagas no Estacionamento', 1, 3, NOW()); ");
		pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, marca, itemnum, itemverif, ativo, usuins, datains) VALUES (17, 0, 17, 'LRO - Formulários', 1, 3, NOW()); ");
		pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, marca, itemnum, itemverif, ativo, usuins, datains) VALUES (18, 0, 18, 'Monitor Câmera', 1, 3, NOW()); ");
		pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, marca, itemnum, itemverif, ativo, usuins, datains) VALUES (19, 0, 19, 'Objetos Perdidos', 1, 3, NOW()); ");
		pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, marca, itemnum, itemverif, ativo, usuins, datains) VALUES (20, 0, 20, 'Pasta com Normas', 1, 3, NOW()); ");
		pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, marca, itemnum, itemverif, ativo, usuins, datains) VALUES (21, 0, 21, 'Problemas com elevadores', 1, 3, NOW()); ");
		pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, marca, itemnum, itemverif, ativo, usuins, datains) VALUES (22, 0, 22, 'Problemas nas instalações - Portas, janelas, etc.', 1, 3, NOW()); ");
		pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, marca, itemnum, itemverif, ativo, usuins, datains) VALUES (23, 0, 23, 'Rádiocomunicadores', 1, 3, NOW()); ");
		pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, marca, itemnum, itemverif, ativo, usuins, datains) VALUES (24, 0, 24, 'Relógio de parede', 1, 3, NOW()); ");
		pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, marca, itemnum, itemverif, ativo, usuins, datains) VALUES (25, 0, 25, 'Telefone Celular', 1, 3, NOW()); ");
		pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, marca, itemnum, itemverif, ativo, usuins, datains) VALUES (26, 0, 26, 'Telefone Ramal 1804', 1, 3, NOW()); ");
		pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, marca, itemnum, itemverif, ativo, usuins, datains) VALUES (27, 0, 27, 'Veículos e moto da Comunhão pernoitando na Casa', 1, 3, NOW()); ");
		pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, marca, itemnum, itemverif, ativo, usuins, datains) VALUES (28, 0, 28, 'Veículos particulares pernoitando na casa', 1, 3, NOW()); ");
		pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, marca, itemnum, itemverif, ativo, usuins, datains) VALUES (29, 0, 29, 'Ventilador', 1, 3, NOW()); ");
	}

   
   echo "Tabela ".$xProj.".livrocheck checada. <br>";
   
   // coleta nomes para uso como substituto temporário no LRO (setor 1)
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
   echo "Tabela ".$xProj.".coletnomes checada. <br>";

   //controle elevadores
   pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".controle_el (
      id SERIAL PRIMARY KEY, 
      num_ap integer NOT NULL DEFAULT 0,
      localap VARCHAR(50),
      empresa_id smallint DEFAULT 0 NOT NULL,
      ativo smallint DEFAULT 1 NOT NULL, 
      usuins integer DEFAULT 0 NOT NULL,
      datains timestamp without time zone DEFAULT '3000-12-31',
      usuedit integer DEFAULT 0 NOT NULL,
      dataedit timestamp without time zone DEFAULT '3000-12-31' 
      ) 
   ");
   echo "Tabela ".$xProj.".controle_el checada. <br>";

   pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".visitas_el (
      id SERIAL PRIMARY KEY, 
      controle_id integer NOT NULL DEFAULT 0,
      datavis date,
      tipovis smallint DEFAULT 1 NOT NULL,
      nometec VARCHAR(100),
      empresa_id smallint DEFAULT 0 NOT NULL,
      ativo smallint DEFAULT 1 NOT NULL,
      acionam timestamp without time zone DEFAULT '3000-12-31',
      atendim timestamp without time zone DEFAULT '3000-12-31',
      conclus timestamp without time zone DEFAULT '3000-12-31',
      contato VARCHAR(100),
      acompanh VARCHAR(100),
      defeito text,
      diagtec text,
      svcrealizado text,
      usuins integer DEFAULT 0 NOT NULL,
      datains timestamp without time zone DEFAULT '3000-12-31',
      usuedit integer DEFAULT 0 NOT NULL,
      dataedit timestamp without time zone DEFAULT '3000-12-31',
      usudel integer DEFAULT 0 NOT NULL,
      datadel timestamp without time zone DEFAULT '3000-12-31'
      ) 
   ");
   echo "Tabela ".$xProj.".visitas_el checada. <br>";

   pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".empresas_el (
      id SERIAL PRIMARY KEY, 
      empresa VARCHAR(150),
      ativo smallint DEFAULT 1 NOT NULL,
      valorvisita double precision NOT NULL DEFAULT 0
      ) 
   ");
  
   $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".empresas_el LIMIT 3");
   $row = pg_num_rows($rs);
   if($row == 0){
      pg_query($Conec, "INSERT INTO ".$xProj.".empresas_el (empresa, ativo) VALUES ('Empresa Contratada', 1)");
   }
   echo "Tabela ".$xProj.".empresas_el checada. <br>";

   pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".controle_ar2 (
      id SERIAL PRIMARY KEY, 
      num_ap integer NOT NULL DEFAULT 0,
      localap VARCHAR(50),
      empresa_id smallint DEFAULT 0 NOT NULL,
      ativo smallint DEFAULT 1 NOT NULL, 
      usuins integer DEFAULT 0 NOT NULL,
      datains timestamp without time zone DEFAULT '3000-12-31',
      usuedit integer DEFAULT 0 NOT NULL,
      dataedit timestamp without time zone DEFAULT '3000-12-31' 
      ) 
   ");
   echo "Tabela ".$xProj.".controle_ar2 checada. <br>";

   pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".visitas_ar2 (
      id SERIAL PRIMARY KEY, 
      controle_id integer NOT NULL DEFAULT 0,
      datavis date,
      tipovis smallint DEFAULT 1 NOT NULL,
      nometec VARCHAR(100),
      empresa_id smallint DEFAULT 0 NOT NULL,
      ativo smallint DEFAULT 1 NOT NULL,
      acionam timestamp without time zone DEFAULT '3000-12-31',
      atendim timestamp without time zone DEFAULT '3000-12-31',
      conclus timestamp without time zone DEFAULT '3000-12-31',
      contato VARCHAR(100),
      acompanh VARCHAR(100),
      defeito text,
      diagtec text,
      svcrealizado text,
      usuins integer DEFAULT 0 NOT NULL,
      datains timestamp without time zone DEFAULT '3000-12-31',
      usuedit integer DEFAULT 0 NOT NULL,
      dataedit timestamp without time zone DEFAULT '3000-12-31',
      usudel integer DEFAULT 0 NOT NULL,
      datadel timestamp without time zone DEFAULT '3000-12-31'
      ) 
   ");
   echo "Tabela ".$xProj.".visitas_ar2 checada. <br>";
   
   pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".controle_ar3 (
      id SERIAL PRIMARY KEY, 
      num_ap integer NOT NULL DEFAULT 0,
      localap VARCHAR(50),
      empresa_id smallint DEFAULT 0 NOT NULL,
      ativo smallint DEFAULT 1 NOT NULL, 
      usuins integer DEFAULT 0 NOT NULL,
      datains timestamp without time zone DEFAULT '3000-12-31',
      usuedit integer DEFAULT 0 NOT NULL,
      dataedit timestamp without time zone DEFAULT '3000-12-31' 
      ) 
   ");
   echo "Tabela ".$xProj.".controle_ar3 checada. <br>";

   pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".visitas_ar3 (
      id SERIAL PRIMARY KEY, 
      controle_id integer NOT NULL DEFAULT 0,
      datavis date,
      tipovis smallint DEFAULT 1 NOT NULL,
      nometec VARCHAR(100),
      empresa_id smallint DEFAULT 0 NOT NULL,
      ativo smallint DEFAULT 1 NOT NULL,
      acionam timestamp without time zone DEFAULT '3000-12-31',
      atendim timestamp without time zone DEFAULT '3000-12-31',
      conclus timestamp without time zone DEFAULT '3000-12-31',
      contato VARCHAR(100),
      acompanh VARCHAR(100),
      defeito text,
      diagtec text,
      svcrealizado text,
      usuins integer DEFAULT 0 NOT NULL,
      datains timestamp without time zone DEFAULT '3000-12-31',
      usuedit integer DEFAULT 0 NOT NULL,
      dataedit timestamp without time zone DEFAULT '3000-12-31',
      usudel integer DEFAULT 0 NOT NULL,
      datadel timestamp without time zone DEFAULT '3000-12-31'
      ) 
   ");
   echo "Tabela ".$xProj.".visitas_ar3 checada. <br>";

   pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escaladaf (
      id SERIAL PRIMARY KEY, 
      dataescala date DEFAULT '3000-12-31',
      ativo smallint DEFAULT 1 NOT NULL, 
      usuins integer DEFAULT 0 NOT NULL,
      datains timestamp without time zone DEFAULT '3000-12-31',
      usuedit integer DEFAULT 0 NOT NULL,
      dataedit timestamp without time zone DEFAULT '3000-12-31' 
      ) 
  ");
  echo "Tabela ".$xProj.".escaladaf checada. <br>";
      pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escaladaf_ins (
        id SERIAL PRIMARY KEY, 
        escaladaf_id bigint NOT NULL DEFAULT 0,
        dataescalains date DEFAULT '3000-12-31',
        poslog_id INT NOT NULL DEFAULT 0,
        letraturno VARCHAR(3), 
        turnoturno VARCHAR(30), 
        destaque smallint NOT NULL DEFAULT 0,
        marcadaf smallint NOT NULL DEFAULT 0,
        ativo smallint NOT NULL DEFAULT 1, 
        usuins bigint NOT NULL DEFAULT 0, 
        datains timestamp without time zone DEFAULT '3000-12-31', 
        usuedit bigint NOT NULL DEFAULT 0, 
        dataedit timestamp without time zone DEFAULT '3000-12-31' 
        )
    ");
    echo "Tabela ".$xProj.".escaladaf_ins checada. <br>"; 

    pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escaladaf_turnos (
      id SERIAL PRIMARY KEY, 
      letra VARCHAR(3), 
      horaturno VARCHAR(30), 
      ordemletra smallint NOT NULL DEFAULT 0,
      destaq smallint NOT NULL DEFAULT 0,
      ativo smallint NOT NULL DEFAULT 1, 
      usuins bigint NOT NULL DEFAULT 0,
      datains timestamp without time zone DEFAULT '3000-12-31',
      usuedit bigint NOT NULL DEFAULT 0,
      dataedit timestamp without time zone DEFAULT '3000-12-31' 
      ) 
  ");


  $rs2 = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf_turnos LIMIT 3");
  $row2 = pg_num_rows($rs2);
  if($row2 == 0){
      pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(1, 'F', 'FÉRIAS', 13, 3, NOW() )");
      pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(2, 'X', 'FOLGA', 14, 3, NOW() )");
      pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(3, 'Y', 'INSS', 15, 3, NOW() )");
      pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(4, 'Q', 'AULA IAQ', 16, 3, NOW() )");
      pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(5, 'A', '08:00 / 17:00', 1, 3, NOW() )");
      pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(6, 'B', '07:00 / 16:00', 2, 3, NOW() )");
      pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(7, 'C', '07:00 / 17:00', 3, 3, NOW() )");
      pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(8, 'E', '09:00 / 18:00', 5, 3, NOW() )");
      pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(9, 'H', '14:00 / 18:00', 7, 3, NOW() )");
      pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(10, 'D', '11:00 / 15:00', 4, 3, NOW() )");
      pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(11, 'K', '08:00 / 14:15', 9, 3, NOW() )");
      pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(12, 'J', '06:50 / 15:50', 8, 3, NOW() )");
      pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(13, 'G', '10:50 / 19:50', 6, 3, NOW() )");
      pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(14, 'L', '07:00 / 13:15', 10, 3, NOW() )");
      pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(15, 'M', '13:35 / 19:50', 11, 3, NOW() )");
      pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(16, 'O', '08:00 / 18:00', 12, 3, NOW() )");
  
      //Acerta as colunas auxiliares para os turnos da escalaDAF
      $rsT = pg_query($Conec, "SELECT id, horaturno FROM ".$xProj.".escaladaf_turnos WHERE horaturno != 'FÉRIAS' And horaturno != 'FOLGA' And horaturno != 'INSS' And horaturno != 'AULA IAQ' And ativo = 1 And cargahora < '00:01' And cargahora IS NOT NULL ORDER BY letra");
      $rowT = pg_num_rows($rsT);
      if($rowT > 0){
         $Hoje = date('d/m/Y');
         while($tblT = pg_fetch_row($rsT)){  //Calcular carga horaria
            $Cod = $tblT[0];
            $Hora = $tblT[1]; 
             $Proc = explode("/", $Hora);
             $HoraI = $Proc[0];
             $HoraF = $Proc[1];
             $TurnoIni = $Hoje." ".$HoraI;
             $TurnoFim = $Hoje." ".$HoraF;
             pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET calcdataini = '$TurnoIni', calcdatafim = '$TurnoFim' WHERE id = $Cod");
             pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargahora = (calcdatafim - calcdataini) WHERE id = $Cod");
             pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargacont = (cargahora - time '01:00'), interv = '01:00' WHERE cargahora >= '08:00' And id = $Cod ");
             pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargacont = (cargahora - time '00:15'), interv = '00:15' WHERE cargahora >= '06:00' And cargahora < '08:00' And id = $Cod ");
             pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargacont = cargahora, interv = '00:00' WHERE cargahora <= '06:00' And id = $Cod ");
         }
      }
   }
  echo "Tabela ".$xProj.".escaladaf_turnos checada. <br>";

  pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escaladaf_notas (
      id SERIAL PRIMARY KEY, 
      numnota smallint NOT NULL DEFAULT 0,
      textonota text, 
      ativo smallint NOT NULL DEFAULT 1, 
      usuins bigint NOT NULL DEFAULT 0,
      datains timestamp without time zone DEFAULT '3000-12-31',
      usuedit bigint NOT NULL DEFAULT 0,
      dataedit timestamp without time zone DEFAULT '3000-12-31' 
      ) 
  ");
  $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf_notas LIMIT 2");
  $row3 = pg_num_rows($rs3);
  if($row3 == 0){
      pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_notas (id, numnota, textonota, usuins, datains) 
      VALUES(1, 1, 'Durante os turnos de 6 horas de duração, o funcionário deverá tirar 15 minutos de descanso, entre a terceira e quinta hora. Em consequência, o horário do turno de serviço deverá ser acrescido de 15 minutos  (Art. 71 - §1º e $2º da CLT). Nesses turnos não será necessário bater ponto quando do inicio e término do descanso. Exemplo: inicio do turno às 07h00 e saída para o descanso às 10h00. Regresso do descanso 10h15 e término do turno às 13h15.', 3, NOW() )");
      pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_notas (id, numnota, textonota, usuins, datains) 
      VALUES(2, 2, 'Durante os turnos de 8 horas de duração, o funcionário deverá tirar 1 h de descanso, entre a quarta e sexta hora. O horário de descanso de cada empregado será definido e obrigatoriamente informado à DAF pelo chefe responsável do setor, por email, até o dia 25 do mês que antecede o início da escala de serviço. É obrigatório bater o ponto quando do início e término do descanso.', 3, NOW() )");
      pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_notas (id, numnota, textonota, usuins, datains) 
      VALUES(3, 3, 'É obrigatório bater o ponto quando do início e término da jornada de trabalho.  Horas extras somente serão realizadas quando expressamente autorizadas pelo diretor da Área ou da Presidência. A utilização do banco de horas somente será possível para os empregados que assinaram o acordo individual - AI - NI-4.18-a DAF.', 3, NOW() ) ");
      pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_notas (id, numnota, textonota, usuins, datains) 
      VALUES(4, 4, 'As segundas, quartas e sextas feiras, o horário de funcionamento da comunhão será das 07h00 até as 21h30. Os setores funcionarão conforme as escalas de serviço.', 3, NOW() )");
  }
  echo "Tabela ".$xProj.".escaladaf_notas checada. <br>";

  pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escaladaf_fer (
      id SERIAL PRIMARY KEY, 
      dataescalafer date DEFAULT '3000-12-31',
      descr VARCHAR(200), 
      ativo smallint NOT NULL DEFAULT 1, 
      usuins bigint NOT NULL DEFAULT 0,
      datains timestamp without time zone DEFAULT '3000-12-31',
      usuedit bigint NOT NULL DEFAULT 0,
      dataedit timestamp without time zone DEFAULT '3000-12-31' 
      ) 
  ");
  $rs5 = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf_fer LIMIT 2");
  $row5 = pg_num_rows($rs5);
  if($row5 == 0){
      pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_fer (id, dataescalafer, descr, usuins, datains) VALUES(1, '2024/01/01', 'Confraternização Universal', 3, NOW() )");
      pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_fer (id, dataescalafer, descr, usuins, datains) VALUES(2, '2024/04/21', 'Tiradentes', 3, NOW() )");
      pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_fer (id, dataescalafer, descr, usuins, datains) VALUES(3, '2024/05/01', 'Dia do Trabalhador', 3, NOW() )");
      pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_fer (id, dataescalafer, descr, usuins, datains) VALUES(4, '2024/09/07', 'Proclamação da Independência', 3, NOW() )");
      pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_fer (id, dataescalafer, descr, usuins, datains) VALUES(5, '2024/10/12', 'Padroeira do Brasil', 3, NOW() )");
      pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_fer (id, dataescalafer, descr, usuins, datains) VALUES(6, '2024/11/02', 'Dia de Finados', 3, NOW() )");
      pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_fer (id, dataescalafer, descr, usuins, datains) VALUES(7, '2024/11/15', 'Proclamação da República', 3, NOW() )");
      pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_fer (id, dataescalafer, descr, usuins, datains) VALUES(8, '2024/12/25', 'Natal', 3, NOW() )");
  }
  echo "Tabela ".$xProj.".escaladaf_fer checada. <br>";



echo "<br><br>";

   