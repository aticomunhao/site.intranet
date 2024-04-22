--
-- PostgreSQL database dump
--

-- Dumped from database version 16.2
-- Dumped by pg_dump version 16.2

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: cesb; Type: SCHEMA; Schema: -; Owner: postgres
--

--CREATE TABLE IF NOT EXISTS public.pessoas
--(
--    id SERIAL PRIMARY KEY, 
--    cpf character varying(50) COLLATE pg_catalog."default" NOT NULL,
--    usuario character varying(50) COLLATE pg_catalog."default",
--    nome_completo character varying(150) COLLATE pg_catalog."default",
--    dt_nascimento date,
--    senha character varying(100) COLLATE pg_catalog."default"
--);
--BEGIN;
--INSERT INTO public.pessoas ("id", "cpf", "uisuario", "nome_completo", "dt_nascimento", "senha") VALUES
--(1, '13652176049', 'Ludinir', 'Ludinir Picelli', '1952-11-12', MD5('123')),
--(2, '12345678900', 'Fulano', 'Fulano de Tal', '1950-01-01', MD5('123'));
--COMMIT;

--SET CLIENT_ENCODING TO 'utf8';
--\i c:/wamp64/www/cesb_PostG/cesb_PostGres.sql



CREATE SCHEMA IF NOT EXISTS cesb;

ALTER SCHEMA cesb OWNER TO postgres;

--
-- PostgreSQL database dump complete
--


DROP TABLE IF EXISTS cesb.poslog;

CREATE TABLE IF NOT EXISTS cesb.poslog (
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
  avhoje date 
);

ALTER TABLE cesb.poslog OWNER TO postgres;

BEGIN;
INSERT INTO cesb.poslog ("id", "pessoas_id", "ativo", "adm", "codsetor", "usuins") VALUES
(1, 1, 1, 7, 2, 1);
COMMIT;


DROP TABLE IF EXISTS cesb.anivers;

CREATE TABLE IF NOT EXISTS cesb.anivers (
  id SERIAL PRIMARY KEY,
  nomeusu character varying(50) COLLATE pg_catalog."default",
  nomecompl character varying(100) COLLATE pg_catalog."default", 
  diaaniv character varying(2) COLLATE pg_catalog."default", 
  mesaniv character varying(2) COLLATE pg_catalog."default",  
  usucod bigint NOT NULL DEFAULT 0,
  usuins integer NOT NULL DEFAULT 0,
  usumodif integer NOT NULL DEFAULT 0,
  datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP, 
  datamodif timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  ativo smallint NOT NULL DEFAULT 1 
);

ALTER TABLE cesb.anivers OWNER TO postgres;

BEGIN;
INSERT INTO cesb.anivers ("id", "nomeusu", "nomecompl", "diaaniv", "mesaniv", "usucod", "usuins", "usumodif", "datains", "datamodif", "ativo") VALUES 
(1, 'Ludinir', 'Ludinir Picelli', '01', '01', 0, 1, 1, '2023-10-28 20:55:03', '2023-10-28 20:55:03', 1),
(2, 'Fulano', 'Fulano de Tal', '30', '10', 0, 1, 1, '2023-10-28 00:55:03', '2023-10-28 00:55:03', 1),
(3, 'Sicrano', 'Sicrano de Tal', '31', '10', 0, 1, 1, '2023-10-28 00:55:03', '2023-10-28 00:55:03', 1),
(4, 'Beltrano', 'Beltrano de Tal', '31', '10', 0, 1, 1, '2023-10-28 00:55:03', '2023-10-28 00:55:03', 1),
(5, 'João', 'João das Couves', '31', '10', 0, 1, 1, '2023-10-28 00:55:03', '2023-10-28 00:55:03', 1),
(6, 'José', 'José do Ovo', '31', '10', 0, 1, 1, '2023-10-28 00:55:03', '2023-10-28 00:55:03', 1),
(7, 'Bananéia', 'Bananéia da Silva', '31', '10', 0, 1, 2, '2023-10-28 00:55:03', '2023-10-28 00:55:03', 1),
(8, 'Viola', 'Benvindo Viola', '31', '10', 0, 1, 1, '2023-10-28 00:55:03', '2023-10-28 00:55:03', 1),
(9, 'Aparecido', 'Bispo Aparecido', '31', '12', 0, 1, 1, '2023-10-28 00:55:03', '2023-12-17 13:14:30', 1),
(10, 'Alpina ', 'Cafiaspirina da Cruz Alpina', '29', '12', 0, 1, 1, '2023-10-28 00:55:03', '2023-12-17 13:14:24', 1),
(11, 'Carabino', 'Carabino Tiro Certo', '29', '10', 0, 1, 1, '2023-10-28 00:55:03', '2023-11-06 12:23:43', 1),
(12, 'Chevrolet', 'Chevrolet da Silva Ford', '15', '11', 0, 1, 1, '2023-10-28 00:55:03', '2023-11-06 12:23:51', 1),
(13, 'Mirela', 'Mirela Tapioca', '10', '11', 0, 1, 1, '2023-10-28 00:55:03', '2023-11-06 12:24:18', 1),
(14, 'Linhares', 'Oceano Atlântico Linhares', '13', '11', 0, 1, 1, '2023-10-28 00:55:03', '2023-11-06 12:24:05', 1),
(15, 'Camisildo', 'Camisildo da Seleção', '27', '11', 0, 1, 1, '2023-10-28 00:55:03', '2023-11-06 12:23:37', 1),
(16, 'Beltrano', 'Beltrano da Silva Sauro', '09', '11', 4, 2, 1, '2023-10-28 00:55:03', '2023-11-06 12:23:31', 1),
(17, 'Fulano', 'Fulano de Tal', '09', '11', 2, 2, 1, '2023-10-28 00:55:03', '2023-11-06 12:23:55', 1),
(18, 'Sicrano', 'Sicrano Bananildo', '21', '11', 3, 2, 1, '2023-10-28 00:55:03', '2023-11-06 12:24:31', 1);
COMMIT;


DROP TABLE IF EXISTS cesb.arqitr;

CREATE TABLE IF NOT EXISTS cesb.arqitr (
  iditr SERIAL PRIMARY KEY,
  idtroca bigint DEFAULT 0,
  iduser bigint DEFAULT 0,
  idsetor int DEFAULT 0,
  datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP, 
  nomearq character varying(200) COLLATE pg_catalog."default" 
);
ALTER TABLE cesb.arqitr OWNER TO postgres;

BEGIN;
INSERT INTO cesb.arqitr ("iditr", "idtroca", "iduser", "idsetor", "datains", "nomearq") VALUES
(1, 6524, 1, 3, '2023-10-09 23:43:20', 'DAC-6524ba48211db-Alexander Strachan_Pixabay_buffalo-4728339_1920.jpg'),
(2, 3, 1, 3, '2023-10-10 00:05:39', 'DAC-6524bf5b65188-Alexandra_Koch_plug-7785880_1920 - Copia.jpg'),
(3, 3, 1, 3, '2023-10-10 00:08:11', 'DAC-6524c01495625-Alexander Strachan_Pixabay_earth-hour-4472693_1920.jpg');
COMMIT;


DROP TABLE IF EXISTS cesb.arqsetor;

CREATE TABLE IF NOT EXISTS cesb.arqsetor (
  codarq SERIAL PRIMARY KEY, 
  codsetor int NOT NULL DEFAULT 0,
  codsubsetor int NOT NULL DEFAULT 0,
  descarq character varying(200) COLLATE pg_catalog."default", 
  usuins int NOT NULL DEFAULT 0,
  datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP, 
  usuapag int NOT NULL DEFAULT 0,
  dataapag date DEFAULT '3000-12-31', 
  ativo smallint NOT NULL DEFAULT 1,
  nomearq character varying(200) COLLATE pg_catalog."default" 
); 

BEGIN;
INSERT INTO cesb.arqsetor ("codarq", "codsetor", "codsubsetor", "descarq", "usuins", "datains", "usuapag", "dataapag", "ativo", "nomearq") VALUES
(1, 2, 1, '6522093d3f8b4-DG-Lua de mel da borboleta.pdf', 2, '2023-10-09 00:36:20', 0, '2023-10-09', 1, NULL),
(2, 4, 1, '652206a1ced3d-DAE-Lua de mel da borboleta.pdf', 2, '2023-10-09 00:36:20', 0, '2023-10-09', 1, NULL),
(3, 2, 1, '652205b90c80d-DG-AvisoAosNavegantes.pdf', 2, '2023-10-09 00:36:20', 0, '2023-10-09', 1, NULL);
COMMIT;



DROP TABLE IF EXISTS cesb.calendev;

CREATE TABLE IF NOT EXISTS cesb.calendev (
  idev SERIAL PRIMARY KEY, 
  evnum bigint DEFAULT 0,
  titulo character varying(250) COLLATE pg_catalog."default", 
  cor character varying(10) COLLATE pg_catalog."default", 
  dataini date, 
  localev text COLLATE pg_catalog."default", 
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
  avok smallint DEFAULT 0 
);

BEGIN;
INSERT INTO cesb.calendev ("idev", "evnum", "titulo", "cor", "dataini", "localev", "ativo", "repet", "fixo", "usuins", "usumodif", "usuapag", "datains", "datamodif", "dataapag") VALUES
(1, 1, 'Confraternização Universal', '#F5DEB3', '2023-01-01', 'Feliz Ano Novo', 1, 2, 1, 1, 1, 0, '3000-12-31 00:00:00', '3000-12-31 00:00:00', '3000-12-31'),
(2, 2, 'Tiradentes', '#F5DEB3', '2023-04-21', '', 1, 2, 1, 1, 1, 0, '3000-12-31 00:00:00', '3000-12-31 00:00:00', '3000-12-31'),
(3, 3, 'Dia do Trabalho', '#F5DEB3', '2023-05-01', '', 1, 2, 1, 1, 1, 0, '3000-12-31 00:00:00', '3000-12-31 00:00:00', '3000-12-31'),
(4, 4, 'Independência do Brasil', '#F5DEB3', '2023-09-07', '', 1, 2, 1, 1, 1, 0, '3000-12-31 00:00:00', '3000-12-31 00:00:00', '3000-12-31'),
(5, 5, 'Padroeira do Brasil', '#F5DEB3', '2023-10-12', 'Nossa Senhora Aparecida', 1, 2, 1, 1, 1, 0, '3000-12-31 00:00:00', '3000-12-31 00:00:00', '3000-12-31'),
(6, 6, 'Finados', '#F5DEB3', '2023-11-02', '', 1, 2, 1, 1, 1, 0, '3000-12-31 00:00:00', '3000-12-31 00:00:00', '3000-12-31 00:00:00'),
(7, 7, 'Proclamação da República', '#F5DEB3', '2023-11-15', '', 1, 2, 1, 1, 1, 0, '3000-12-31 00:00:00', '3000-12-31 00:00:00', '3000-12-31'),
(8, 8, 'Natal', '#F5DEB3', '2023-12-25', 'Feliz Natal', 1, 2, 1, 1, 1, 0, '3000-12-31 00:00:00', '3000-12-31 00:00:00', '3000-12-31');
COMMIT;


DROP TABLE IF EXISTS cesb.carousel;

CREATE TABLE IF NOT EXISTS cesb.carousel (
  codcar SERIAL PRIMARY KEY,
  descarq character varying(200) COLLATE pg_catalog."default", 
  descarqant character varying(200) COLLATE pg_catalog."default" 
);

BEGIN;
INSERT INTO cesb.carousel ("codcar", "descarq", "descarqant") VALUES
(1, 'imgfundo0.jpg', ''),
(2, 'imgfundo1.jpg', ''),
(3, 'imgfundo2.jpg', ''),
(4, 'imgfundo3.jpg', '');
COMMIT;


DROP TABLE IF EXISTS cesb.escolhas;

CREATE TABLE IF NOT EXISTS cesb.escolhas (
  codesc SERIAL PRIMARY KEY,
  esc1 character varying(2) COLLATE pg_catalog."default", 
  esc2 character varying(10) COLLATE pg_catalog."default", 
  liberaproj smallint NOT NULL DEFAULT '0',
  sit character varying(20) COLLATE pg_catalog."default", 
  motinat character varying(20) COLLATE pg_catalog."default", 
  sex varchar(20) COLLATE pg_catalog."default"
);

BEGIN;
INSERT INTO cesb.escolhas (codesc, esc1, esc2, liberaproj, sit, motinat, sex) VALUES
(1, '', '', 1, '', '', ''),
(2, '01', 'Janeiro', 1, 'Funcionário', 'Aposentadoria', 'Masculino'),
(3, '02', 'Fevereiro', 0, 'Contratado', 'Desistência', 'Feminino'),
(4, '03', 'Março', 0, 'Voluntário', 'Falecimento', 'Indeterminado'),
(5, '04', 'Abril', 0, 'Excluído', 'Abandono', ''),
(6, '05', 'Maio', 0, '', 'Rescisão', ''),
(7, '06', 'Junho', 0, '', '', ''),
(8, '07', 'Julho', 0, '', '', ''),
(9, '08', 'Agosto', 0, '', '', ''),
(10, '09', 'Setembro', 0, '', '', ''),
(11, '10', 'Outubro', 0, '', '', ''),
(12, '11', 'Novembro', 0, '', '', ''),
(13, '12', 'Dezembro', 0, '', '', ''),
(14, '13', '', 0, '', '', ''),
(15, '14', '', 0, '', '', ''),
(16, '15', '', 0, '', '', ''),
(17, '16', '', 0, '', '', ''),
(18, '17', '', 0, '', '', ''),
(19, '18', '', 0, '', '', ''),
(20, '19', '', 0, '', '', ''),
(21, '20', '', 0, '', '', ''),
(22, '21', '', 0, '', '', ''),
(23, '22', '', 0, '', '', ''),
(24, '23', '', 0, '', '', ''),
(25, '24', '', 0, '', '', ''),
(26, '25', '', 0, '', '', ''),
(27, '26', '', 0, '', '', ''),
(28, '27', '', 0, '', '', ''),
(29, '28', '', 0, '', '', ''),
(30, '29', '', 0, '', '', ''),
(31, '30', '', 0, '', '', ''),
(32, '31', '', 0, '', '', '');
COMMIT;



DROP TABLE IF EXISTS cesb.ocorrencias;

CREATE TABLE IF NOT EXISTS cesb.ocorrencias (
  codocor SERIAL PRIMARY KEY, 
  usuins bigint NOT NULL DEFAULT 0,
  datains timestamp without time zone DEFAULT '3000-12-31 00:00:00',
  dataocor date DEFAULT '3000-12-31',
  codsetor int NOT NULL DEFAULT 0,
  usumodif bigint NOT NULL DEFAULT 0,
  datamodif timestamp without time zone DEFAULT '3000-12-31 00:00:00',
  ativo smallint NOT NULL DEFAULT 1, 
  ocorrencia text COLLATE pg_catalog."default", 
  numocor varchar(100) COLLATE pg_catalog."default" 
);


BEGIN;
INSERT INTO cesb.ocorrencias (codocor, usuins, datains, dataocor, codsetor, usumodif, datamodif, ativo, ocorrencia, numocor) VALUES
(1, 2, '2023-11-09 15:31:13', '2023-11-09', 2, 0, '3000-12-31 00:00:00', 1, 'Caí da escada e quebrei o braço.', '0001/2023'),
(2, 1, '2023-11-09 23:28:15', '2024-02-01', 3, 0, '3000-12-31 00:00:00', 1, 'Teste 2024', '0001/2023'),
(3, 1, '2023-11-09 23:34:37', '2024-03-01', 3, 0, '3000-12-31 00:00:00', 1, 'Testre 2024', '0001/2023'),
(4, 1, '2023-11-09 23:36:06', '2023-11-11', 3, 0, '3000-12-31 00:00:00', 1, '', '0001/0'),
(5, 1, '2023-11-09 23:54:11', '2023-11-10', 3, 0, '3000-12-31 00:00:00', 1, 'Teste 2024 5', '0003/2023'),
(6, 1, '2023-11-09 23:54:52', '2024-06-01', 3, 0, '3000-12-31 00:00:00', 1, 'teste 01 06 2024', '0003/2024'),
(7, 1, '2023-11-10 22:01:59', '2023-11-11', 3, 0, '3000-12-31 00:00:00', 1, 'Maecenas pellentesque eros massa, quis consectetur elit semper nec. Sed cursus fermentum gravida. Phasellus sollicitudin blandit ex, vel aliquam arcu vulputate quis. Cras tincidunt commodo ullamcorper. Proin justo mi, cursus non laoreet vel, facilisis at nibh. Aenean tristique, lectus at interdum rutrum, tellus mi faucibus orci, eu venenatis purus sapien vitae lacus. Praesent iaculis accumsan neque. Morbi pharetra, leo a pulvinar molestie, leo odio varius metus, a lobortis enim nisi quis mauris. Morbi eu ultrices ligula. Curabitur sodales erat feugiat sem tincidunt hendrerit. Sed ut hendrerit diam, sit amet commodo sem. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse tempus consectetur diam, ac finibus orci aliquam nec. In venenatis elementum pellentesque. Etiam porta magna et est sagittis dictum. Ut eu urna sit amet diam faucibus vulputate. ', '0004/2023'),
(8, 1, '2023-11-10 22:45:23', '2023-11-11', 3, 0, '3000-12-31 00:00:00', 1, 'Teste 10-11', '0005/2023');
COMMIT;



DROP TABLE IF EXISTS cesb.ocorrideogr;

CREATE TABLE IF NOT EXISTS cesb.ocorrideogr (
  codideo SERIAL PRIMARY KEY,
  coddaocor bigint NOT NULL DEFAULT 0,
  descideo varchar(100) COLLATE pg_catalog."default", 
  codprov bigint NOT NULL DEFAULT 0 
);


BEGIN;
INSERT INTO cesb.ocorrideogr (codideo, coddaocor, descideo, codprov) VALUES
(1, 1, 'modulos/ocorrencias/imagens/CairEscada.png', 0),
(8, 6, 'modulos/ocorrencias/imagens/CairEscadaria.png', 0),
(3, 5, 'modulos/ocorrencias/imagens/paneCarro.png', 0),
(4, 5, 'modulos/ocorrencias/imagens/acidFogoCarro.png', 0),
(5, 6, 'modulos/ocorrencias/imagens/extintor.png', 0),
(6, 6, 'modulos/ocorrencias/imagens/acidMotoPlaca.png', 0),
(7, 1, 'modulos/ocorrencias/imagens/acidBracEng.png', 0),
(12, 4, 'modulos/ocorrencias/imagens/CairCadeira.png', 0),
(15, 7, 'modulos/ocorrencias/imagens/CairCadeira.png', 0),
(16, 7, 'modulos/ocorrencias/imagens/acidCarroArvore.png', 0),
(17, 7, 'modulos/ocorrencias/imagens/extintor.png', 0),
(19, 2, 'modulos/ocorrencias/imagens/acidMotoCarro.png', 0),
(22, 8, 'modulos/ocorrencias/imagens/fogo.png', 0),
(38, 8, 'modulos/ocorrencias/imagens/acidCarroFogo.png', 0);
COMMIT;



DROP TABLE IF EXISTS cesb.paramsis;

CREATE TABLE IF NOT EXISTS cesb.paramsis (
  idpar SERIAL PRIMARY KEY,
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
  insarq smallint DEFAULT 4 
);

BEGIN;
INSERT INTO cesb.paramsis (idpar, admvisu, admcad, admedit, insaniver, editaniver, insevento, editevento, instarefa, edittarefa, insocor, editocor, insramais, editramais, instelef, edittelef, instroca, edittroca, editpagina, insarq) VALUES 
(1, 0, 0, 0, 4, 4, 4, 4, 4, 4, 2, 2, 7, 7, 4, 4, 4, 4, 4, 4);
COMMIT;


DROP TABLE IF EXISTS cesb.ramais_ext;

CREATE TABLE IF NOT EXISTS cesb.ramais_ext (
  codtel SERIAL PRIMARY KEY,
  siglaempresa varchar(50) COLLATE pg_catalog."default", 
  nomeempresa varchar(100) COLLATE pg_catalog."default", 
  contatonome varchar(100) COLLATE pg_catalog."default", 
  codsetor integer NOT NULL DEFAULT 0,
  setor varchar(20) COLLATE pg_catalog."default", 
  telefonefixo varchar(20) COLLATE pg_catalog."default", 
  telefonecel varchar(20) COLLATE pg_catalog."default", 
  usuins integer NOT NULL DEFAULT 0,
  usumodif int NOT NULL DEFAULT 0,
  datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  datamodif timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  ativo smallint NOT NULL DEFAULT 1 
  );


BEGIN;
INSERT INTO cesb.ramais_ext (codtel, siglaempresa, nomeempresa, contatonome, codsetor, setor, telefonefixo, telefonecel, usuins, usumodif, datains, datamodif, ativo) VALUES
(1, 'Bombeiros DF', 'Corpo de Bombeiros Militar do Distrito Federal ', 'Sicrano', 0, 'Emergência', '193', '', 1, 1, '2023-09-11 16:56:43', '2023-09-12 17:29:21', 1),
(2, 'SAMU ', 'Serviço de Atendimento Móvel de Urgência', '', 0, 'Emergência', '192', '(61) 9999-9999', 1, 0, '2023-09-11 16:58:18', '2023-09-25 16:30:45', 1),
(3, 'PRF', 'Polícia Rodoviária Federal ', '', 0, 'Patrulha', '191', '', 1, 1, '2023-09-11 17:00:23', '2023-09-13 15:27:25', 1),
(4, 'PM-DF', 'Polícia Militar do Distrito Federal', 'Fulano', 0, 'Emergência', '190', '', 1, 1, '2023-09-11 17:01:17', '2023-09-12 16:09:17', 1),
(5, 'QGEx', 'Quartel General do Exército', 'Macumbaldo', 0, 'Gabinete', '(61) 3333-4444', '(61) 99999-9999', 1, 1, '2023-09-11 22:07:40', '2023-09-13 15:40:15', 1);

COMMIT;


DROP TABLE IF EXISTS cesb.ramais_int;

CREATE TABLE IF NOT EXISTS cesb.ramais_int (
  codtel SERIAL PRIMARY KEY, 
  nomeusu varchar(50) COLLATE pg_catalog."default", 
  nomecompl varchar(100) COLLATE pg_catalog."default", 
  codsetor int NOT NULL DEFAULT 0, 
  setor varchar(20) COLLATE pg_catalog."default", 
  ramal varchar(20) COLLATE pg_catalog."default", 
  usuins int NOT NULL DEFAULT 0, 
  usumodif int NOT NULL DEFAULT 0, 
  datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP, 
  datamodif timestamp without time zone DEFAULT CURRENT_TIMESTAMP, 
  coduser int NOT NULL DEFAULT 0, 
  ativo smallint NOT NULL DEFAULT 1 
);

BEGIN;
INSERT INTO cesb.ramais_int (codtel, nomeusu, nomecompl, codsetor, setor, ramal, usuins, usumodif, datains, datamodif, coduser, ativo) VALUES
(1, 'Ludinir', 'Ludinir Picelli', 0, 'ATI', '2222333', 1, 1, '2023-09-25 14:32:41', '2023-09-25 14:32:41', 683, 1),
(2, 'Fulano', 'Fulano da Silva Sauro', 0, 'DAC', '1111', 1, 1, '2023-09-25 14:32:41', '2023-09-25 14:32:41', 0, 1),
(3, 'João', 'João das Couves', 0, 'DAF', '4445', 1, 1, '2023-09-25 14:32:41', '2023-09-25 14:32:41', 0, 1),
(4, 'Camisildo', 'Camisildo da Seleção', 0, 'ATI', '5555', 1, 1, '2023-09-25 14:32:41', '2023-09-25 14:32:41', 0, 1),
(5, 'Linhares', 'Oceano Atlântico Linhares', 0, 'DIJ', 'R777777', 1, 1, '2023-09-25 14:32:41', '2023-09-25 14:32:41', 0, 1),
(6, 'Alpina', 'Cafiaspirina da Cruz Alpina das Alturas', 0, 'DED', '8888', 1, 1, '2023-09-25 14:32:41', '2023-09-25 14:32:41', 0, 1),
(7, 'Aparecido', 'Bispo Aparecido', 0, 'FAEdd', '6777999', 1, 1, '2023-09-25 14:32:41', '2023-09-25 14:32:41', 0, 1),
(8, 'Mirela', 'Mirela Tapióca com çedilha', 0, 'ATI', '2222', 1, 1, '2023-09-25 14:32:41', '2023-09-25 14:32:41', 0, 1);
COMMIT;


DROP TABLE IF EXISTS cesb.setores;

CREATE TABLE IF NOT EXISTS cesb.setores (
  codset SERIAL PRIMARY KEY, 
  siglasetor varchar(10) COLLATE pg_catalog."default", 
  descsetor varchar(100) COLLATE pg_catalog."default", 
  mordem int NOT NULL DEFAULT 1, 
  usuins varchar(100) COLLATE pg_catalog."default", 
  usumodif varchar(100) COLLATE pg_catalog."default", 
  datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP, 
  datamodif timestamp without time zone DEFAULT CURRENT_TIMESTAMP, 
  ativo smallint NOT NULL DEFAULT 1, 
  textopag text COLLATE pg_catalog."default" 
);

BEGIN;
INSERT INTO cesb.setores (codset, siglasetor, descsetor, mordem, usuins, usumodif, datains, datamodif, ativo, textopag) VALUES
(1, '', '', 1, '1', '1', '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(2, 'DG', 'Diretoria-Geral', 1, '1', '1', '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, '&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Diretoria-Geral&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 13pt;&quot;&gt;&lt;span class=&quot;BxUVEf ILfuVd&quot; lang=&quot;pt&quot;&gt;&lt;span class=&quot;hgKElc&quot;&gt;Dirigir, planejar, organizar e controlar as atividades de diversas &amp;aacute;reas da Comunh&amp;atilde;o Esp&amp;iacute;rita de Bras&amp;iacute;lia, fixando pol&amp;iacute;ticas de gest&amp;atilde;o dos recursos financeiros, &lt;/span&gt;&lt;/span&gt;&lt;span style=&quot;font-family: &quot;Open Sans&quot;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;&lt;img style=&quot;float: left;&quot; src=&quot;itr/DG-652203438beba-LogoComunhao.png&quot; alt=&quot;&quot; width=&quot;125&quot; height=&quot;110&quot; /&gt;&lt;/span&gt;&lt;span class=&quot;BxUVEf ILfuVd&quot; lang=&quot;pt&quot;&gt;&lt;span class=&quot;hgKElc&quot;&gt;administrativos, estrutura&amp;ccedil;&amp;atilde;o, racionaliza&amp;ccedil;&amp;atilde;o, e adequa&amp;ccedil;&amp;atilde;o dos diversos servi&amp;ccedil;os.&lt;/span&gt;&lt;/span&gt;&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 13pt;&quot;&gt;&lt;span style=&quot;font-family: &quot;Open Sans&quot;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp; &lt;/span&gt;&lt;span class=&quot;BxUVEf ILfuVd&quot; lang=&quot;pt&quot;&gt;&lt;span class=&quot;hgKElc&quot;&gt;Trata da assessoria pessoal e institucional da Presid&amp;ecirc;ncia, atendendo pessoas, organizando audi&amp;ecirc;ncias e agenda, viabilizando o relacionamento do Presidente com as diretorias e assessorias, exercendo atividades articuladas com todos os &amp;oacute;rg&amp;atilde;os da Casa.&lt;/span&gt;&lt;/span&gt;&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-family: &quot;Open Sans&quot;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;FIM&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;'),
(3, 'DAC', 'Diretoria de Arte e Cultura', 2, '1', '1', '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, '&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Diretoria de Arte e Cultura&lt;br /&gt;&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;&lt;div class=&quot;wpb_text_column wpb_content_element&quot;&gt;&lt;div class=&quot;wpb_wrapper&quot;&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;A Diretoria de Arte e Cultura desenvolve a arte e a cultura esp&amp;iacute;rita da no &amp;acirc;mbito interno e externo da Comunh&amp;atilde;o Esp&amp;iacute;rita de Bras&amp;iacute;lia.&amp;nbsp; Coral Elos de Luz, Coral Elinhos &lt;/span&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;&lt;span style=&quot;font-family: &quot;Open Sans&quot;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;&lt;img style=&quot;float: left;&quot; src=&quot;itr/DAC-6522e4a17d51e-Clker-Free-Vector-Images_Pixabay_sun-g237bae17e_1280.png&quot; alt=&quot;&quot; width=&quot;132&quot; height=&quot;112&quot; /&gt;&lt;/span&gt;&lt;/span&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;de Luz, teatro m&amp;uacute;sica, bandas, bal&amp;eacute;, sapateado, musicas, todos envolvidos na divulg&lt;/span&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;a&amp;ccedil;&amp;atilde;o da Doutrina Esp&amp;iacute;rita.&lt;/span&gt;&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;&amp;nbsp; &amp;nbsp; &amp;nbsp;&amp;nbsp; &amp;ldquo;O espiritismo vem abrir para a arte novas perspectivas, horizontes sem limites. A comunica&amp;ccedil;&amp;atilde;o que ele estabelece entre os mundos vis&amp;iacute;vel e invis&amp;iacute;vel, as indica&amp;ccedil;&amp;otilde;es fornecidas sobre as condi&amp;ccedil;&amp;otilde;es da vida no al&amp;eacute;m, a revela&amp;ccedil;&amp;atilde;o que ele nos traz das leis de harmonia e de beleza que regem o universo v&amp;ecirc;m oferecer aos nossos pensadores, aos nossos artistas, motivos inesgot&amp;aacute;veis de inspira&amp;ccedil;&amp;atilde;o.&amp;rdquo;&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp; (a) L&amp;eacute;on Denis&lt;/p&gt;'),
(4, 'DAE', 'Diretoria de Assistência Espiritual', 3, '1', '1', '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, '&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Diretoria de Assist&amp;ecirc;ncia Espiritual&lt;br /&gt;&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;&lt;div class=&quot;wpb_text_column wpb_content_element  vc_custom_1624446588787&quot;&gt;&lt;div class=&quot;wpb_wrapper&quot;&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;O atendimento espiritual presta aux&amp;iacute;lio aos irm&amp;atilde;os encarnados, atrav&amp;eacute;s da fluidoterapia, e aos irm&amp;atilde;os desencarnados pela pr&amp;aacute;tica do interc&amp;acirc;mbio medi&amp;uacute;nico em grupos espec&amp;iacute;ficos, al&amp;eacute;m de desenvolver a atividade de educa&amp;ccedil;&amp;atilde;o da mediunidade.&amp;nbsp; De todos os princ&amp;iacute;pios da Doutrina Esp&amp;iacute;rita, no seu contexto pr&amp;aacute;tico, cabe a Diretoria de Assist&amp;ecirc;ncia Espiritual (DAE) a lida di&amp;aacute;ria com a comunicabilidade dos esp&amp;iacute;ritos.&lt;/span&gt;&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;Da desobsess&amp;atilde;o &amp;agrave; educa&amp;ccedil;&amp;atilde;o da mediunidade, dos passes de harmoniza&amp;ccedil;&amp;atilde;o e o tratamento desobsessivo aos de restabelecimento da sa&amp;uacute;de integral do ser humano, abertos ao p&amp;uacute;blico, possibilitando, cada um em seu prop&amp;oacute;sito espec&amp;iacute;fico, a comunic&lt;/span&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;a&amp;ccedil;&amp;atilde;o dos esp&amp;iacute;ritos.&lt;/span&gt;&lt;/p&gt;&lt;p&gt;&lt;span style=&quot;font-family: &quot;Open Sans&quot;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;&lt;img style=&quot;float: left;&quot; src=&quot;itr/DAE-652206bd11b82-blossoms-2659967_1920.png&quot; alt=&quot;&quot; width=&quot;176&quot; height=&quot;129&quot; /&gt;&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;O interc&amp;acirc;&lt;/span&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;mbio medi&amp;uacute;nico &amp;eacute; realizado atrav&amp;eacute;s dos intermedi&amp;aacute;rios entre os dois mundos: os m&amp;eacute;&lt;/span&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;diuns. &lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;A t&lt;/span&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;arefa exige de cada trabalhador muito estudo, disciplina espartana e perseveran&amp;ccedil;a inamov&amp;iacute;vel. Em contrapartida, recebem dos Esp&amp;iacute;ritos Superiores for&amp;ccedil;as para o sustento da vida di&amp;aacute;ria, enquanto laboram na seara de Jesus em um aben&amp;ccedil;oado atendimento fraterno a irm&amp;atilde;os desencarnados, necessitados de caridade, benevol&amp;ecirc;ncia e amor.&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;'),
(5, 'DAF', 'Diretoria Administrativa e Financeira', 4, '1', '1', '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, '&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Diretoria Administrativa e Financeira&lt;br /&gt;&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;&lt;span style=&quot;font-family: arial, helvetica, sans-serif; font-size: medium;&quot;&gt;A Diretoria Administrativa e Financeira &amp;ndash; DAF &amp;eacute; respons&amp;aacute;vel pela administra&amp;ccedil;&amp;atilde;o geral da Comunh&amp;atilde;o Esp&amp;iacute;rita de Bras&amp;iacute;lia, propiciando os meios e recursos necess&amp;aacute;rios &amp;agrave; realiza&amp;ccedil;&amp;atilde;o das a&amp;ccedil;&amp;otilde;es, programas, projetos e iniciativas desenvolvidas pela Casa.&amp;nbsp;&lt;/span&gt;&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;&lt;span style=&quot;font-family: arial, helvetica, sans-serif; font-size: medium;&quot;&gt;Entre suas atribui&amp;ccedil;&amp;otilde;es est&amp;atilde;o planejar e gerir as finan&amp;ccedil;as e or&amp;ccedil;amentos da Comunh&amp;atilde;o, tra&amp;ccedil;ando as diretrizes estrat&amp;eacute;gicas, or&amp;ccedil;ament&amp;aacute;rias, cont&amp;aacute;beis, fiscais e de custos.&lt;/span&gt;&lt;/span&gt;&lt;/p&gt;&lt;p&gt;Sob sua responsabilidade est&amp;atilde;o as tarefas administrativas e de gest&amp;atilde;o econ&amp;ocirc;mico-financeira da Comunh&amp;atilde;o, dentre as quais:&lt;/p&gt;&lt;ul&gt;&lt;li&gt;Administra&amp;ccedil;&amp;atilde;o&lt;/li&gt;&lt;li&gt;Recursos Humanos&lt;/li&gt;&lt;li&gt;Planejamento e Execu&amp;ccedil;&amp;atilde;o Or&amp;ccedil;ament&amp;aacute;ria&lt;/li&gt;&lt;li&gt;Compras, Recebimentos e Pagamentos&lt;/li&gt;&lt;li&gt;Presta&amp;ccedil;&amp;atilde;o de Contas&lt;/li&gt;&lt;li&gt;Acompanhamento e Controle de receitas e despesas&lt;/li&gt;&lt;li&gt;Almoxarifado&lt;/li&gt;&lt;li&gt;Bazar&lt;/li&gt;&lt;li&gt;Livraria&lt;/li&gt;&lt;li&gt;Acompanhamento e Controle do quadro de Associados&lt;/li&gt;&lt;li&gt;Recebimento de doa&amp;ccedil;&amp;otilde;es e contribui&amp;ccedil;&amp;otilde;es&lt;/li&gt;&lt;/ul&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&amp;nbsp;&lt;/p&gt;'),
(6, 'DAO', 'Diretoria de Atendimento e Orientação', 5, '1', '1', '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, '&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Diretoria de Atendimento Fraterno&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;Atende fraternalmente as pessoas que procuram a Comunh&amp;atilde;o Esp&amp;iacute;rita de Bras&amp;iacute;lia, informando e encaminhando os solicitantes aos setores ou &amp;oacute;rg&amp;atilde;os onde dever&amp;atilde;o encontrar respostas para as suas buscas.&lt;/strong&gt;&lt;/p&gt;&lt;div class=&quot;wpb_text_column wpb_content_element&quot;&gt;&lt;div class=&quot;wpb_wrapper&quot;&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;O Atendimento Fraterno &amp;eacute; a porta de entrada da Comunh&amp;atilde;o Esp&amp;iacute;rita, por onde o p&amp;uacute;blico chega movido pela dor e pelo sofrimento. &amp;Eacute; o pronto-socorro espiritual, onde &amp;eacute; poss&amp;iacute;vel alcan&amp;ccedil;ar al&amp;iacute;vio para a dor da alma.&lt;/span&gt;&lt;/p&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt; &lt;/span&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;O atendimento on-line &amp;eacute; para todo e qualquer p&amp;uacute;blico, seja com problemas fisicos, espirituais ou emocionais, alcan&amp;ccedil;ando quase todos os estados do Brasil.&lt;/span&gt;&lt;/p&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt; &lt;/span&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;span style=&quot;font-size: 11pt;&quot;&gt;&lt;strong&gt;O sigilo, a privacidade e o n&amp;atilde;o julgamento s&amp;atilde;o os principios b&amp;aacute;sicos do atendimento&lt;/strong&gt;. &lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;Este servi&amp;ccedil;o se utiliza de tr&amp;ecirc;s pilares para apoiar pessoas: o acolhimento, o consolo e, se possivel, o esclarecimento daquela dor. A partir da&amp;iacute;, o tratamento &amp;eacute; indicado para cada um (harmoniza&amp;ccedil;&amp;atilde;o, irradia&amp;ccedil;&amp;atilde;o, estudo do Evangelho Segundo o Espiritismo, desobsess&amp;atilde;o ou tratamento integral).&lt;/span&gt;&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class=&quot;wpb_text_column wpb_content_element&quot;&gt;&amp;nbsp;&lt;/div&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&amp;nbsp;&lt;/p&gt;'),
(7, 'DED', 'Diretoria de Estudos Doutrinários', 6, '1', '1', '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, '&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Diretoria de Estudos Doutrin&amp;aacute;rios&lt;br /&gt;&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;A diretoria promove o Estudo Sistematizado da Doutrina Esp&amp;iacute;rita, ESDE.&amp;nbsp; &amp;Eacute; o estudo, s&amp;eacute;rio, regular, fraterno e cont&amp;iacute;nuo da Doutrina Esp&amp;iacute;rita, tendo como base os ensinamentos morais de Jesus e as obras b&amp;aacute;sicas compiladas por Allan Kardec, quais sejam: O Livro dos Esp&amp;iacute;ritos, O Livro dos M&amp;eacute;diuns, O Evangelho Segundo o Espiritismo, O C&amp;eacute;u e o Inferno, A G&amp;ecirc;nese, O que &amp;eacute; o Espiritismo, Obras P&amp;oacute;stumas, Revista Esp&amp;iacute;rita (1858 a 1869) etc.&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;Tem como objetivos: proporcionar a transforma&amp;ccedil;&amp;atilde;o moral; garantir a unidade de compreens&amp;atilde;o em torno dos princ&amp;iacute;pios doutrin&amp;aacute;rios esp&amp;iacute;ritas; divulgar a Doutrina Esp&amp;iacute;rita nas bases em que foi codificada como Doutrina Consoladora de tr&amp;iacute;plice aspecto: cient&amp;iacute;fico, filos&amp;oacute;fico e religioso; favorecer o desenvolvimento da f&amp;eacute; raciocinada; incentivar os seus participantes a um envolvimento maior nas atividades da Comunh&amp;atilde;o Esp&amp;iacute;rita de Bras&amp;iacute;lia.&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&amp;nbsp;&lt;/p&gt;'),
(8, 'DIJ', 'Diretoria de Infância e Juventude', 7, '1', '1', '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, '&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Diretoria de Inf&amp;acirc;ncia e Juventude&lt;br /&gt;&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;A Evangeliza&amp;ccedil;&amp;atilde;o promovida pela Diretoria de Inf&amp;acirc;ncia e Juventude da Comunh&amp;atilde;o Esp&amp;iacute;rita de Bras&amp;iacute;lia est&amp;aacute; baseada na metodologia do Cristo e de Kardec e esfor&amp;ccedil;a-se em segui-la, na busca do despertar das consci&amp;ecirc;ncias rec&amp;eacute;m-reencarnadas que recebe, impelindo-as &amp;agrave; transforma&amp;ccedil;&amp;atilde;o &amp;iacute;ntima por interm&amp;eacute;dio do conhecimento e da pr&amp;aacute;tica do Cristianismo, &amp;agrave; luz da Doutrina Esp&amp;iacute;rita, atrav&amp;eacute;s do exemplo dos pais e evangelizadores e pelo est&amp;iacute;mulo &amp;agrave; pr&amp;aacute;tica da caridade.&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;Evangelizar &amp;eacute;...&lt;/p&gt;&lt;div class=&quot;wpb_column vc_column_container vc_col-sm-4 mpc-column&quot; data-column-id=&quot;mpc_column-63661caa91d2a14&quot;&gt;&lt;div class=&quot;vc_column-inner&quot;&gt;&lt;div class=&quot;wpb_wrapper&quot;&gt;&lt;div class=&quot;wpb_text_column wpb_content_element&quot;&gt;&lt;div class=&quot;wpb_wrapper&quot;&gt;&lt;ul&gt;&lt;li&gt;Uma oportunidade&lt;/li&gt;&lt;li&gt;Uma responsabilidade&lt;/li&gt;&lt;li&gt;Uma tarefa de amor&lt;/li&gt;&lt;li&gt;Um processo cont&amp;iacute;nuo&amp;nbsp;&amp;nbsp; de&amp;nbsp;&amp;nbsp; transforma&amp;ccedil;&amp;atilde;o&amp;nbsp;&amp;nbsp; &amp;iacute;ntima&amp;nbsp;&amp;nbsp; que&amp;nbsp;&amp;nbsp; necessita&amp;nbsp;&amp;nbsp; de&amp;nbsp;&amp;nbsp; um&amp;nbsp;&amp;nbsp; trabalho colaborativo e harmonioso&lt;/li&gt;&lt;/ul&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;'),
(9, 'DED', 'Diretoria de Promoção Social', 8, '1', '1', '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, '&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Diretoria de Promo&amp;ccedil;&amp;atilde;o Social&lt;br /&gt;&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 13pt;&quot;&gt;A Diretoria de Promo&amp;ccedil;&amp;atilde;o Social da Comunh&amp;atilde;o Esp&amp;iacute;rita de Bras&amp;iacute;lia, busca acolher e promover pessoas e fam&amp;iacute;lias em estado de vulnerabilidade social, incentivando-as para o seu desenvolvimento espiritual, mental, f&amp;iacute;sico e social &amp;agrave; luz dos valores universais, contribuindo para amenizar o impacto da pobreza e proporcionar o bem-estar social.&lt;/span&gt;&lt;/p&gt;&lt;h2 class=&quot;bdt-heading-title&quot; style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span class=&quot;bdt-main-heading&quot; style=&quot;font-size: 14pt;&quot;&gt;&lt;span class=&quot;bdt-main-heading-inner&quot;&gt;Nossa inspira&amp;ccedil;&amp;atilde;o &amp;eacute; a Promo&amp;ccedil;&amp;atilde;o Social &lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/h2&gt;&lt;h2 class=&quot;bdt-heading-title&quot; style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span class=&quot;bdt-main-heading&quot; style=&quot;font-size: 14pt;&quot;&gt;&lt;span class=&quot;bdt-main-heading-inner&quot;&gt;e a Promo&amp;ccedil;&amp;atilde;o do Esp&amp;iacute;rito Imortal &lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/h2&gt;&lt;h2 class=&quot;bdt-heading-title&quot; style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span class=&quot;bdt-main-heading&quot; style=&quot;font-size: 14pt;&quot;&gt;&lt;span class=&quot;bdt-main-heading-inner&quot;&gt;Reabilita&amp;ccedil;&amp;atilde;o do Ser.&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/h2&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&amp;nbsp;&lt;/p&gt;'),
(10, 'AAD', 'Assessoria de Assuntos Doutrinários', 9, '1', '1', '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, '&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Assessoria&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?&lt;/span&gt;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;FIM&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;'),
(11, 'ACE', 'Assessoria de Comunicação e Eventos', 10, '1', '1', '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, '&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Assessoria&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?&lt;/span&gt;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;FIM&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;'),
(12, 'ADI', 'Assessoria de Desenvolvimento Institucional', 11, '1', '1', '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, '&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Assessoria&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?&lt;/span&gt;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;FIM&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;'),
(13, 'AJU', 'Assessoria Jurídica', 12, '1', '1', '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, '&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Assessoria&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?&lt;/span&gt;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;FIM&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;'),
(14, 'AME', 'Assessoria de Estudos e Aplicações de Medicina Espiritual', 13, '1', '1', '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, '&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Assessoria&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?&lt;/span&gt;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;FIM&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;'),
(15, 'APE', 'Assessoria de Planejamento Estratégico', 14, '1', '1', '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, '&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Assessoria&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?&lt;/span&gt;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;FIM&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;'),
(16, 'APV', 'Assessoria da Pomada do Vovô Pedro', 15, '1', '1', '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, '&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Assessoria&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?&lt;/span&gt;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;FIM&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;'),
(17, 'ATI', 'Assessoria de Tecnologia da Informação', 16, '1', '1', '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, '&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Assessoria&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\n&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. N&lt;img style=&quot;float: left;&quot; src=&quot;itr/ATI-652208dc4506d-TecnologiaInformarm.png&quot; alt=&quot;&quot; width=&quot;156&quot; height=&quot;156&quot; /&gt;emo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?&lt;/span&gt;&lt;/p&gt;\n&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;\n&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;FIM&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;'),
(18, 'OUV', 'Ouvidoria', 17, '1', '1', '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, '&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Assessoria&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?&lt;/span&gt;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;FIM&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;');

COMMIT;



DROP TABLE IF EXISTS cesb.subsetores;

CREATE TABLE IF NOT EXISTS cesb.subsetores (
  codsubSet SERIAL PRIMARY KEY, 
  coddosetor int NOT NULL DEFAULT 0,
  siglasubsetor varchar(10) COLLATE pg_catalog."default", 
  descsubsetor varchar(100) COLLATE pg_catalog."default", 
  smordem int NOT NULL DEFAULT 1,
  usuins int NOT NULL DEFAULT 0,
  usumodif int NOT NULL DEFAULT 0,
  datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP, 
  datamodif timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  ativo smallint NOT NULL DEFAULT 1,
  textopag text COLLATE pg_catalog."default" 
);

BEGIN;
INSERT INTO cesb.subsetores (codsubset, coddosetor, siglasubsetor, descsubsetor, smordem, usuins, usumodif, datains, datamodif, ativo, textopag) VALUES
(1, 0, '', '', 1, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(2, 3, 'CODAC', 'Coordenação Administrativa DAC', 1, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(3, 3, 'DIPRA', 'Divisão de Produção Artística', 2, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(4, 3, 'DIDAN', 'Divisão de Dança', 3, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(5, 3, 'DITEA', 'Divisão de Teatro', 4, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(6, 3, 'DIMUS', 'Divisão de Música', 5, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(7, 3, 'DICIN', 'Divisão de Cinema', 6, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(8, 3, 'DIPPI', 'Divisão de Poesia e Pintura', 7, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(9, 4, 'DITAD', 'Divisão de Tratamento e Desobsessão', 1, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(10, 4, 'DIEME', 'Divisão de Educação da Mediunidade', 2, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(11, 4, 'DIPAH', 'Divisão de Passes de Harmonização', 3, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(12, 4, 'DIDES', 'Divisão de Desobsessão', 4, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(13, 4, 'DIAMO', 'Divisão de Apoio ao Médium Ostensivo em Eclosão da Mediunidade', 5, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(14, 5, 'DIADM', 'Divisão Administrativa DAF', 1, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(15, 5, 'DIFIN', 'Divisão Fianceira', 2, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(16, 5, 'LIVRARIA', 'Divisão de Livraria', 3, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(17, 5, 'BAZAR', 'Divisão de Bazar', 4, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(18, 5, 'ALMOX', 'Divisão de Almoxariafado', 5, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(19, 6, 'DIVAP', 'Divisão de Atendimento ao Público', 1, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(20, 6, 'DIVAF', 'Divisão de Atendimento Fraterno', 2, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(21, 6, 'DIVAT', 'Divisão de Atendimento Específico e Formação', 3, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(22, 7, 'DIFTE', 'Divisão de Formação do Trabalhador Espírita', 1, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(23, 7, 'DIVES', 'Divisão de Estudo Sistematizado da Doutrina Espírita', 2, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(24, 7, 'DIPAD', 'Divisão do Programa de Adaptação à Doutrina Espírita', 3, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(25, 7, 'DIMOC', 'Divisão da Mocidade Espírita da Comunhão', 4, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(26, 7, 'DIPAP', 'Divisão de Pesquisa e Aperfeçoamento', 5, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(27, 7, 'DIESP', 'Divisão de Especialização', 6, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(28, 8, 'DIRME', 'Divisão de Recursos e Meios', 1, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(29, 8, 'DEMAT', 'Divisão de Evangelização do Maternal', 2, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(30, 8, 'DEINF', 'Divisão de Evangelização da Infância', 3, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(31, 8, 'DEJUV', 'Divisão de Evangelização da Juventude', 4, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(32, 8, 'DEFAM', 'Divisão de Evangelização da Família', 5, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(33, 9, 'DIAFA', 'Divisão de Acompanhamento de Famílias', 1, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(34, 9, 'DIOFI', 'Divisão de Oficinas', 2, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(35, 9, 'DIADA', 'Divisão de Arrecadação e Distribuição de Alimentos', 3, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, ''),
(36, 9, 'DIAFRA', 'Divisão Fraterna', 4, 1, 1, '2023-10-06 21:24:56', '2023-10-06 21:24:56', 1, '');

COMMIT;



DROP TABLE IF EXISTS cesb.tarefas;

CREATE TABLE IF NOT EXISTS cesb.tarefas (
  idtar SERIAL PRIMARY KEY, 
  usuins bigint NOT NULL DEFAULT 0,
  usuexec bigint NOT NULL DEFAULT 0,
  tittarefa text COLLATE pg_catalog."default", 
  textotarefa text COLLATE pg_catalog."default",
  prio smallint DEFAULT 2,
  sit smallint DEFAULT 1,
  datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  datasit1 timestamp without time zone DEFAULT '3000-12-31 00:00:00',
  datasit2 timestamp without time zone DEFAULT '3000-12-31 00:00:00',
  datasit3 timestamp without time zone DEFAULT '3000-12-31 00:00:00',
  datasit4 timestamp without time zone DEFAULT '3000-12-31 00:00:00',
  usumodifsit bigint NOT NULL DEFAULT 0,
  usumodif bigint NOT NULL DEFAULT 0,
  datamodif timestamp without time zone DEFAULT '3000-12-31 00:00:00',
  usucancel bigint NOT NULL DEFAULT 0,
  datacancel timestamp without time zone DEFAULT '3000-12-31 00:00:00',
  ativo smallint DEFAULT 1 
);


BEGIN;
INSERT INTO cesb.tarefas (idtar, usuins, usuexec, tittarefa, textotarefa, prio, sit, datains, datasit1, datasit2, datasit3, datasit4, usumodifsit, usumodif, datamodif, usucancel, datacancel, ativo) VALUES
(1, 1, 2, 'Conserto torradeira da cantina.', NULL, 2, 2, '2023-10-28 00:55:03', '2023-10-29 00:38:51', '2023-10-29 00:39:12', '3000-12-31 00:00:00', '3000-12-31 00:00:00', 2, 0, '3000-12-31 00:00:00', 0, '3000-12-31 00:00:00', 1),
(2, 1, 2, 'Acerto documentação do Setor.', NULL, 2, 1, '2023-10-28 00:55:03', '2023-10-29 00:38:51', '3000-12-31 00:00:00', '3000-12-31 00:00:00', '3000-12-31 00:00:00', 0, 0, '3000-12-31 00:00:00', 0, '3000-12-31 00:00:00', 1);
COMMIT;



DROP TABLE IF EXISTS cesb.tarefas_msg;

CREATE TABLE IF NOT EXISTS cesb.tarefas_msg (
  idmsg SERIAL PRIMARY KEY,  
  iduser bigint DEFAULT 0, 
  idtarefa bigint DEFAULT 0, 
  usuinstar bigint NOT NULL DEFAULT 0, 
  usuexectar bigint NOT NULL DEFAULT 0, 
  datamsg timestamp without time zone DEFAULT CURRENT_TIMESTAMP, 
  textomsg text COLLATE pg_catalog."default", 
  inslido smallint DEFAULT 0, 
  execlido smallint DEFAULT 0, 
  tarefa_ativ smallint DEFAULT 1, 
  tarefa_lida smallint DEFAULT 0, 
  elim smallint DEFAULT 0, 
  dataelim timestamp without time zone DEFAULT '3000-12-31 00:00:00' 
);

BEGIN;
INSERT INTO cesb.tarefas_msg (idmsg, iduser, idtarefa, usuinstar, usuexectar, datamsg, textomsg, inslido, execlido, tarefa_ativ, tarefa_lida, elim, dataelim) VALUES 
(1, 1, 4, 2, 3, '2023-11-18 22:29:29', 'Teste', 0, 1, 1, 1, 1, '2023-11-18 22:29:48');
COMMIT;


DROP TABLE IF EXISTS cesb.textopag;

CREATE TABLE IF NOT EXISTS cesb.textopag (
  codset SERIAL PRIMARY KEY, 
  textopag text COLLATE pg_catalog."default"  
);

BEGIN;
INSERT INTO cesb.textopag (codset, textopag) VALUES
(1, '&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Diretoria&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?&lt;/span&gt;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;FIM&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;'),
(2, '&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Assessoria&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?&lt;/span&gt;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;\r\n&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;FIM&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;');

COMMIT;



DROP TABLE IF EXISTS cesb.trafego;

CREATE TABLE IF NOT EXISTS cesb.trafego (
  codtraf SERIAL PRIMARY KEY, 
  descarq varchar(200) COLLATE pg_catalog."default", 
  nomearq varchar(200) COLLATE pg_catalog."default", 
  usuins bigint NOT NULL DEFAULT 0, 
  datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP, 
  usudest bigint NOT NULL DEFAULT 0, 
  usuapag bigint NOT NULL DEFAULT 0, 
  dataapag date DEFAULT '3000-12-31', 
  ativo smallint NOT NULL DEFAULT 1 
  );


BEGIN;
INSERT INTO cesb.trafego (codtraf, descarq, nomearq, usuins, datains, usudest, usuapag, dataapag, ativo) VALUES
(1, '65412590062b4-A Boa Nova.pdf', 'A Boa Nova.pdf', 1, '2023-10-31 13:04:32', 0, 1, '2023-10-31', 1),
(2, '6541967241fff-A Genese.pdf', 'A Gênese.pdf', 1, '2023-10-31 21:06:10', 0, 1, '2023-10-31', 1);
COMMIT;



DROP TABLE IF EXISTS cesb.trocas;

CREATE TABLE IF NOT EXISTS cesb.trocas (
  idtr SERIAL PRIMARY KEY, 
  iduser bigint DEFAULT 0,
  idsetor int DEFAULT 0,
  datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  textotroca text COLLATE pg_catalog."default", 
  trocaativa smallint NOT NULL DEFAULT 1 
);


BEGIN;
INSERT INTO cesb.trocas (idtr, iduser, idsetor, datains, textotroca, trocaativa) VALUES 
(1, 1, 3, '2023-10-05 18:55:38', '<p><span style=\"text-decoration: underline;\"><strong>Arm&aacute;rio vertical</strong></span></p>\n<p style=\"padding-left: 40px;\"><img src=\"itr/DAC-65204088d61f4-armario_medidas.jpg\" alt=\"\" width=\"261\" height=\"261\" /><img src=\"itr/DAO-65203ac1089c8-Clker-Free-Vector-Images_Pixabay_christmas-tree-g6a4fa2ed4_1280.png\" alt=\"\" width=\"176\" height=\"244\" /></p>\n<p style=\"padding-left: 40px;\"><img src=\"itr/DAO-65203a39b615c-ComunBannerLongo.jpg\" alt=\"\" width=\"400\" height=\"69\" /></p>\n<div style=\"text-align: center;\"><span style=\"font-size: 14pt;\">Arm&aacute;rio dispon&iacute;vel para troca, descarte ou doa&ccedil;&atilde;o.</span></div>\n<div style=\"text-align: center;\"><span style=\"font-size: 14pt;\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Tratar com <span style=\"background-color: #e67e23;\">Fulano </span>pelo ramal interno 3333.</span></div>\n<div style=\"text-align: center;\"><span style=\"color: #000000; background-color: #f1c40f;\"><span style=\"font-size: 14pt; background-color: #f1c40f;\">Arm&aacute;rio dispon&iacute;vel para troca</span><span style=\"font-size: 14pt; background-color: #f1c40f;\">, descarte ou doa&ccedil;&atilde;o</span></span><span style=\"font-size: 14pt;\">. <br /></span></div>\n<div style=\"text-align: center;\">\n<div style=\"text-align: center;\"><span style=\"font-size: 14pt;\">Grato.</span></div>\n</div>', 1),
(2, 1, 2, '2023-10-06 14:40:33', '<p><span style=\"color: #000000; background-color: #fbeeb8;\"><strong>Arm&aacute;rio Amarelo</strong></span> para doa&ccedil;&atilde;o, disponibilizado na diretoria-geral.</p>\n<p><img src=\"itr/DG-652048073b97d-armario-amarelo.jpg\" alt=\"\" width=\"400\" height=\"250\" /></p>\n<p>Tratar com <strong>Sicrano de Tal</strong> - ramal 2222.</p>\n<p>&nbsp;</p>', 1);
COMMIT;



DROP TABLE IF EXISTS cesb.usugrupos;

CREATE TABLE IF NOT EXISTS cesb.usugrupos (
  id SERIAL PRIMARY KEY, 
  adm_fl smallint NOT NULL DEFAULT 0,
  adm_nome varchar(100) COLLATE pg_catalog."default", 
  datacria timestamp without time zone DEFAULT CURRENT_TIMESTAMP, 
  ativo smallint NOT NULL DEFAULT 1 
);


BEGIN;
INSERT INTO cesb.usugrupos (id, adm_fl, adm_nome, datacria, ativo) VALUES 
(1, 0, 'Público', '2023-10-06 21:24:56', 0),
(2, 1, 'Convidado', '2023-10-06 21:24:56', 0),
(3, 2, 'Registrado', '2023-10-06 21:24:56', 1),
(4, 3, 'Gerente', '2023-10-06 21:24:56', 1),
(5, 4, 'Administrador', '2023-10-06 21:24:56', 1),
(6, 5, 'Checador', '2023-10-06 21:24:56', 0),
(7, 6, 'Revisor', '2023-10-06 21:24:56', 1),
(8, 7, 'Superusuário', '2023-10-06 21:24:56', 1);
COMMIT;

