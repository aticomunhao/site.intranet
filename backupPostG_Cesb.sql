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

CREATE SCHEMA cesb;


ALTER SCHEMA cesb OWNER TO postgres;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: anivers; Type: TABLE; Schema: cesb; Owner: postgres
--

CREATE TABLE cesb.anivers (
    id integer NOT NULL,
    nomeusu character varying(50),
    nomecompl character varying(100),
    diaaniv character varying(2),
    mesaniv character varying(2),
    usucod bigint DEFAULT 0 NOT NULL,
    usuins integer DEFAULT 0 NOT NULL,
    usumodif integer DEFAULT 0 NOT NULL,
    datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    datamodif timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    ativo smallint DEFAULT 1 NOT NULL
);


ALTER TABLE cesb.anivers OWNER TO postgres;

--
-- Name: anivers_id_seq; Type: SEQUENCE; Schema: cesb; Owner: postgres
--

CREATE SEQUENCE cesb.anivers_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE cesb.anivers_id_seq OWNER TO postgres;

--
-- Name: anivers_id_seq; Type: SEQUENCE OWNED BY; Schema: cesb; Owner: postgres
--

ALTER SEQUENCE cesb.anivers_id_seq OWNED BY cesb.anivers.id;


--
-- Name: arqitr; Type: TABLE; Schema: cesb; Owner: postgres
--

CREATE TABLE cesb.arqitr (
    iditr integer NOT NULL,
    idtroca bigint DEFAULT 0,
    iduser bigint DEFAULT 0,
    idsetor integer DEFAULT 0,
    datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    nomearq character varying(200)
);


ALTER TABLE cesb.arqitr OWNER TO postgres;

--
-- Name: arqitr_iditr_seq; Type: SEQUENCE; Schema: cesb; Owner: postgres
--

CREATE SEQUENCE cesb.arqitr_iditr_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE cesb.arqitr_iditr_seq OWNER TO postgres;

--
-- Name: arqitr_iditr_seq; Type: SEQUENCE OWNED BY; Schema: cesb; Owner: postgres
--

ALTER SEQUENCE cesb.arqitr_iditr_seq OWNED BY cesb.arqitr.iditr;


--
-- Name: arqsetor; Type: TABLE; Schema: cesb; Owner: postgres
--

CREATE TABLE cesb.arqsetor (
    codarq integer NOT NULL,
    codsetor integer DEFAULT 0 NOT NULL,
    codsubsetor integer DEFAULT 0 NOT NULL,
    descarq character varying(200),
    usuins integer DEFAULT 0 NOT NULL,
    datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    usuapag integer DEFAULT 0 NOT NULL,
    dataapag date DEFAULT '3000-12-31'::date,
    ativo smallint DEFAULT 1 NOT NULL,
    nomearq character varying(200)
);


ALTER TABLE cesb.arqsetor OWNER TO postgres;

--
-- Name: arqsetor_codarq_seq; Type: SEQUENCE; Schema: cesb; Owner: postgres
--

CREATE SEQUENCE cesb.arqsetor_codarq_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE cesb.arqsetor_codarq_seq OWNER TO postgres;

--
-- Name: arqsetor_codarq_seq; Type: SEQUENCE OWNED BY; Schema: cesb; Owner: postgres
--

ALTER SEQUENCE cesb.arqsetor_codarq_seq OWNED BY cesb.arqsetor.codarq;


--
-- Name: calendev; Type: TABLE; Schema: cesb; Owner: postgres
--

CREATE TABLE cesb.calendev (
    idev integer NOT NULL,
    evnum bigint DEFAULT 0,
    titulo character varying(250),
    cor character varying(10),
    dataini date,
    localev text,
    ativo smallint DEFAULT 1,
    repet smallint DEFAULT 0,
    fixo smallint DEFAULT 0,
    usuins bigint DEFAULT 0,
    usumodif bigint DEFAULT 0,
    usuapag bigint DEFAULT 0,
    datains timestamp without time zone DEFAULT '3000-12-31 00:00:00'::timestamp without time zone,
    datamodif timestamp without time zone DEFAULT '3000-12-31 00:00:00'::timestamp without time zone,
    dataapag date DEFAULT '3000-12-31'::date,
    avobrig smallint DEFAULT 0,
    avok smallint DEFAULT 0
);


ALTER TABLE cesb.calendev OWNER TO postgres;

--
-- Name: calendev_idev_seq; Type: SEQUENCE; Schema: cesb; Owner: postgres
--

CREATE SEQUENCE cesb.calendev_idev_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE cesb.calendev_idev_seq OWNER TO postgres;

--
-- Name: calendev_idev_seq; Type: SEQUENCE OWNED BY; Schema: cesb; Owner: postgres
--

ALTER SEQUENCE cesb.calendev_idev_seq OWNED BY cesb.calendev.idev;


--
-- Name: carousel; Type: TABLE; Schema: cesb; Owner: postgres
--

CREATE TABLE cesb.carousel (
    codcar integer NOT NULL,
    descarq character varying(200),
    descarqant character varying(200)
);


ALTER TABLE cesb.carousel OWNER TO postgres;

--
-- Name: carousel_codcar_seq; Type: SEQUENCE; Schema: cesb; Owner: postgres
--

CREATE SEQUENCE cesb.carousel_codcar_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE cesb.carousel_codcar_seq OWNER TO postgres;

--
-- Name: carousel_codcar_seq; Type: SEQUENCE OWNED BY; Schema: cesb; Owner: postgres
--

ALTER SEQUENCE cesb.carousel_codcar_seq OWNED BY cesb.carousel.codcar;


--
-- Name: escolhas; Type: TABLE; Schema: cesb; Owner: postgres
--

CREATE TABLE cesb.escolhas (
    codesc integer NOT NULL,
    esc1 character varying(2),
    esc2 character varying(10),
    liberaproj smallint DEFAULT '0'::smallint NOT NULL,
    sit character varying(20),
    motinat character varying(20),
    sex character varying(20)
);


ALTER TABLE cesb.escolhas OWNER TO postgres;

--
-- Name: escolhas_codesc_seq; Type: SEQUENCE; Schema: cesb; Owner: postgres
--

CREATE SEQUENCE cesb.escolhas_codesc_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE cesb.escolhas_codesc_seq OWNER TO postgres;

--
-- Name: escolhas_codesc_seq; Type: SEQUENCE OWNED BY; Schema: cesb; Owner: postgres
--

ALTER SEQUENCE cesb.escolhas_codesc_seq OWNED BY cesb.escolhas.codesc;


--
-- Name: ocorrencias; Type: TABLE; Schema: cesb; Owner: postgres
--

CREATE TABLE cesb.ocorrencias (
    codocor integer NOT NULL,
    usuins bigint DEFAULT 0 NOT NULL,
    datains timestamp without time zone DEFAULT '3000-12-31 00:00:00'::timestamp without time zone,
    dataocor date DEFAULT '3000-12-31'::date,
    codsetor integer DEFAULT 0 NOT NULL,
    usumodif bigint DEFAULT 0 NOT NULL,
    datamodif timestamp without time zone DEFAULT '3000-12-31 00:00:00'::timestamp without time zone,
    ativo smallint DEFAULT 1 NOT NULL,
    ocorrencia text,
    numocor character varying(100)
);


ALTER TABLE cesb.ocorrencias OWNER TO postgres;

--
-- Name: ocorrencias_codocor_seq; Type: SEQUENCE; Schema: cesb; Owner: postgres
--

CREATE SEQUENCE cesb.ocorrencias_codocor_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE cesb.ocorrencias_codocor_seq OWNER TO postgres;

--
-- Name: ocorrencias_codocor_seq; Type: SEQUENCE OWNED BY; Schema: cesb; Owner: postgres
--

ALTER SEQUENCE cesb.ocorrencias_codocor_seq OWNED BY cesb.ocorrencias.codocor;


--
-- Name: ocorrideogr; Type: TABLE; Schema: cesb; Owner: postgres
--

CREATE TABLE cesb.ocorrideogr (
    codideo integer NOT NULL,
    coddaocor bigint DEFAULT 0 NOT NULL,
    descideo character varying(100),
    codprov bigint DEFAULT 0 NOT NULL
);


ALTER TABLE cesb.ocorrideogr OWNER TO postgres;

--
-- Name: ocorrideogr_codideo_seq; Type: SEQUENCE; Schema: cesb; Owner: postgres
--

CREATE SEQUENCE cesb.ocorrideogr_codideo_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE cesb.ocorrideogr_codideo_seq OWNER TO postgres;

--
-- Name: ocorrideogr_codideo_seq; Type: SEQUENCE OWNED BY; Schema: cesb; Owner: postgres
--

ALTER SEQUENCE cesb.ocorrideogr_codideo_seq OWNED BY cesb.ocorrideogr.codideo;


--
-- Name: paramsis; Type: TABLE; Schema: cesb; Owner: postgres
--

CREATE TABLE cesb.paramsis (
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
    insarq smallint DEFAULT 4
);


ALTER TABLE cesb.paramsis OWNER TO postgres;

--
-- Name: paramsis_idpar_seq; Type: SEQUENCE; Schema: cesb; Owner: postgres
--

CREATE SEQUENCE cesb.paramsis_idpar_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE cesb.paramsis_idpar_seq OWNER TO postgres;

--
-- Name: paramsis_idpar_seq; Type: SEQUENCE OWNED BY; Schema: cesb; Owner: postgres
--

ALTER SEQUENCE cesb.paramsis_idpar_seq OWNED BY cesb.paramsis.idpar;


--
-- Name: poslog; Type: TABLE; Schema: cesb; Owner: postgres
--

CREATE TABLE cesb.poslog (
    id integer NOT NULL,
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
    avhoje date
);


ALTER TABLE cesb.poslog OWNER TO postgres;

--
-- Name: poslog_id_seq; Type: SEQUENCE; Schema: cesb; Owner: postgres
--

CREATE SEQUENCE cesb.poslog_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE cesb.poslog_id_seq OWNER TO postgres;

--
-- Name: poslog_id_seq; Type: SEQUENCE OWNED BY; Schema: cesb; Owner: postgres
--

ALTER SEQUENCE cesb.poslog_id_seq OWNED BY cesb.poslog.id;


--
-- Name: ramais_ext; Type: TABLE; Schema: cesb; Owner: postgres
--

CREATE TABLE cesb.ramais_ext (
    codtel integer NOT NULL,
    siglaempresa character varying(50),
    nomeempresa character varying(100),
    contatonome character varying(100),
    codsetor integer DEFAULT 0 NOT NULL,
    setor character varying(20),
    telefonefixo character varying(20),
    telefonecel character varying(20),
    usuins integer DEFAULT 0 NOT NULL,
    usumodif integer DEFAULT 0 NOT NULL,
    datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    datamodif timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    ativo smallint DEFAULT 1 NOT NULL
);


ALTER TABLE cesb.ramais_ext OWNER TO postgres;

--
-- Name: ramais_ext_codtel_seq; Type: SEQUENCE; Schema: cesb; Owner: postgres
--

CREATE SEQUENCE cesb.ramais_ext_codtel_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE cesb.ramais_ext_codtel_seq OWNER TO postgres;

--
-- Name: ramais_ext_codtel_seq; Type: SEQUENCE OWNED BY; Schema: cesb; Owner: postgres
--

ALTER SEQUENCE cesb.ramais_ext_codtel_seq OWNED BY cesb.ramais_ext.codtel;


--
-- Name: ramais_int; Type: TABLE; Schema: cesb; Owner: postgres
--

CREATE TABLE cesb.ramais_int (
    codtel integer NOT NULL,
    nomeusu character varying(50),
    nomecompl character varying(100),
    codsetor integer DEFAULT 0 NOT NULL,
    setor character varying(20),
    ramal character varying(20),
    usuins integer DEFAULT 0 NOT NULL,
    usumodif integer DEFAULT 0 NOT NULL,
    datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    datamodif timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    coduser integer DEFAULT 0 NOT NULL,
    ativo smallint DEFAULT 1 NOT NULL
);


ALTER TABLE cesb.ramais_int OWNER TO postgres;

--
-- Name: ramais_int_codtel_seq; Type: SEQUENCE; Schema: cesb; Owner: postgres
--

CREATE SEQUENCE cesb.ramais_int_codtel_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE cesb.ramais_int_codtel_seq OWNER TO postgres;

--
-- Name: ramais_int_codtel_seq; Type: SEQUENCE OWNED BY; Schema: cesb; Owner: postgres
--

ALTER SEQUENCE cesb.ramais_int_codtel_seq OWNED BY cesb.ramais_int.codtel;


--
-- Name: setores; Type: TABLE; Schema: cesb; Owner: postgres
--

CREATE TABLE cesb.setores (
    codset integer NOT NULL,
    siglasetor character varying(10),
    descsetor character varying(100),
    mordem integer DEFAULT 1 NOT NULL,
    usuins character varying(100),
    usumodif character varying(100),
    datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    datamodif timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    ativo smallint DEFAULT 1 NOT NULL,
    textopag text
);


ALTER TABLE cesb.setores OWNER TO postgres;

--
-- Name: setores_codset_seq; Type: SEQUENCE; Schema: cesb; Owner: postgres
--

CREATE SEQUENCE cesb.setores_codset_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE cesb.setores_codset_seq OWNER TO postgres;

--
-- Name: setores_codset_seq; Type: SEQUENCE OWNED BY; Schema: cesb; Owner: postgres
--

ALTER SEQUENCE cesb.setores_codset_seq OWNED BY cesb.setores.codset;


--
-- Name: subsetores; Type: TABLE; Schema: cesb; Owner: postgres
--

CREATE TABLE cesb.subsetores (
    codsubset integer NOT NULL,
    coddosetor integer DEFAULT 0 NOT NULL,
    siglasubsetor character varying(10),
    descsubsetor character varying(100),
    smordem integer DEFAULT 1 NOT NULL,
    usuins integer DEFAULT 0 NOT NULL,
    usumodif integer DEFAULT 0 NOT NULL,
    datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    datamodif timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    ativo smallint DEFAULT 1 NOT NULL,
    textopag text
);


ALTER TABLE cesb.subsetores OWNER TO postgres;

--
-- Name: subsetores_codsubset_seq; Type: SEQUENCE; Schema: cesb; Owner: postgres
--

CREATE SEQUENCE cesb.subsetores_codsubset_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE cesb.subsetores_codsubset_seq OWNER TO postgres;

--
-- Name: subsetores_codsubset_seq; Type: SEQUENCE OWNED BY; Schema: cesb; Owner: postgres
--

ALTER SEQUENCE cesb.subsetores_codsubset_seq OWNED BY cesb.subsetores.codsubset;


--
-- Name: tarefas; Type: TABLE; Schema: cesb; Owner: postgres
--

CREATE TABLE cesb.tarefas (
    idtar integer NOT NULL,
    usuins bigint DEFAULT 0 NOT NULL,
    usuexec bigint DEFAULT 0 NOT NULL,
    tittarefa text,
    textotarefa text,
    prio smallint DEFAULT 2,
    sit smallint DEFAULT 1,
    datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    datasit1 timestamp without time zone DEFAULT '3000-12-31 00:00:00'::timestamp without time zone,
    datasit2 timestamp without time zone DEFAULT '3000-12-31 00:00:00'::timestamp without time zone,
    datasit3 timestamp without time zone DEFAULT '3000-12-31 00:00:00'::timestamp without time zone,
    datasit4 timestamp without time zone DEFAULT '3000-12-31 00:00:00'::timestamp without time zone,
    usumodifsit bigint DEFAULT 0 NOT NULL,
    usumodif bigint DEFAULT 0 NOT NULL,
    datamodif timestamp without time zone DEFAULT '3000-12-31 00:00:00'::timestamp without time zone,
    usucancel bigint DEFAULT 0 NOT NULL,
    datacancel timestamp without time zone DEFAULT '3000-12-31 00:00:00'::timestamp without time zone,
    ativo smallint DEFAULT 1
);


ALTER TABLE cesb.tarefas OWNER TO postgres;

--
-- Name: tarefas_idtar_seq; Type: SEQUENCE; Schema: cesb; Owner: postgres
--

CREATE SEQUENCE cesb.tarefas_idtar_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE cesb.tarefas_idtar_seq OWNER TO postgres;

--
-- Name: tarefas_idtar_seq; Type: SEQUENCE OWNED BY; Schema: cesb; Owner: postgres
--

ALTER SEQUENCE cesb.tarefas_idtar_seq OWNED BY cesb.tarefas.idtar;


--
-- Name: tarefas_msg; Type: TABLE; Schema: cesb; Owner: postgres
--

CREATE TABLE cesb.tarefas_msg (
    idmsg integer NOT NULL,
    iduser bigint DEFAULT 0,
    idtarefa bigint DEFAULT 0,
    usuinstar bigint DEFAULT 0 NOT NULL,
    usuexectar bigint DEFAULT 0 NOT NULL,
    datamsg timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    textomsg text,
    inslido smallint DEFAULT 0,
    execlido smallint DEFAULT 0,
    tarefa_ativ smallint DEFAULT 1,
    tarefa_lida smallint DEFAULT 0,
    elim smallint DEFAULT 0,
    dataelim timestamp without time zone DEFAULT '3000-12-31 00:00:00'::timestamp without time zone
);


ALTER TABLE cesb.tarefas_msg OWNER TO postgres;

--
-- Name: tarefas_msg_idmsg_seq; Type: SEQUENCE; Schema: cesb; Owner: postgres
--

CREATE SEQUENCE cesb.tarefas_msg_idmsg_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE cesb.tarefas_msg_idmsg_seq OWNER TO postgres;

--
-- Name: tarefas_msg_idmsg_seq; Type: SEQUENCE OWNED BY; Schema: cesb; Owner: postgres
--

ALTER SEQUENCE cesb.tarefas_msg_idmsg_seq OWNED BY cesb.tarefas_msg.idmsg;


--
-- Name: textopag; Type: TABLE; Schema: cesb; Owner: postgres
--

CREATE TABLE cesb.textopag (
    codset integer NOT NULL,
    textopag text
);


ALTER TABLE cesb.textopag OWNER TO postgres;

--
-- Name: textopag_codset_seq; Type: SEQUENCE; Schema: cesb; Owner: postgres
--

CREATE SEQUENCE cesb.textopag_codset_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE cesb.textopag_codset_seq OWNER TO postgres;

--
-- Name: textopag_codset_seq; Type: SEQUENCE OWNED BY; Schema: cesb; Owner: postgres
--

ALTER SEQUENCE cesb.textopag_codset_seq OWNED BY cesb.textopag.codset;


--
-- Name: trafego; Type: TABLE; Schema: cesb; Owner: postgres
--

CREATE TABLE cesb.trafego (
    codtraf integer NOT NULL,
    descarq character varying(200),
    nomearq character varying(200),
    usuins bigint DEFAULT 0 NOT NULL,
    datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    usudest bigint DEFAULT 0 NOT NULL,
    usuapag bigint DEFAULT 0 NOT NULL,
    dataapag date DEFAULT '3000-12-31'::date,
    ativo smallint DEFAULT 1 NOT NULL
);


ALTER TABLE cesb.trafego OWNER TO postgres;

--
-- Name: trafego_codtraf_seq; Type: SEQUENCE; Schema: cesb; Owner: postgres
--

CREATE SEQUENCE cesb.trafego_codtraf_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE cesb.trafego_codtraf_seq OWNER TO postgres;

--
-- Name: trafego_codtraf_seq; Type: SEQUENCE OWNED BY; Schema: cesb; Owner: postgres
--

ALTER SEQUENCE cesb.trafego_codtraf_seq OWNED BY cesb.trafego.codtraf;


--
-- Name: trocas; Type: TABLE; Schema: cesb; Owner: postgres
--

CREATE TABLE cesb.trocas (
    idtr integer NOT NULL,
    iduser bigint DEFAULT 0,
    idsetor integer DEFAULT 0,
    datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    textotroca text,
    trocaativa smallint DEFAULT 1 NOT NULL
);


ALTER TABLE cesb.trocas OWNER TO postgres;

--
-- Name: trocas_idtr_seq; Type: SEQUENCE; Schema: cesb; Owner: postgres
--

CREATE SEQUENCE cesb.trocas_idtr_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE cesb.trocas_idtr_seq OWNER TO postgres;

--
-- Name: trocas_idtr_seq; Type: SEQUENCE OWNED BY; Schema: cesb; Owner: postgres
--

ALTER SEQUENCE cesb.trocas_idtr_seq OWNED BY cesb.trocas.idtr;


--
-- Name: usuarios; Type: TABLE; Schema: cesb; Owner: postgres
--

CREATE TABLE cesb.usuarios (
    id integer NOT NULL,
    usuario character varying(50),
    nome character varying(50),
    nomecompl character varying(100),
    idpessoa bigint DEFAULT 0 NOT NULL,
    sexo smallint DEFAULT 1 NOT NULL,
    senha character varying(255),
    adm smallint DEFAULT 0 NOT NULL,
    codsetor integer DEFAULT 2 NOT NULL,
    codsubsetor integer DEFAULT 1 NOT NULL,
    diaaniv character varying(2),
    mesaniv character varying(2),
    usuins integer DEFAULT 0 NOT NULL,
    datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    usumodif integer DEFAULT 0 NOT NULL,
    datamodif timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    ativo smallint DEFAULT 1 NOT NULL,
    usuinat integer DEFAULT 0 NOT NULL,
    datainat timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    motivoinat smallint DEFAULT 0 NOT NULL,
    numacessos integer DEFAULT 0 NOT NULL,
    ultlog timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE cesb.usuarios OWNER TO postgres;

--
-- Name: usuarios_id_seq; Type: SEQUENCE; Schema: cesb; Owner: postgres
--

CREATE SEQUENCE cesb.usuarios_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE cesb.usuarios_id_seq OWNER TO postgres;

--
-- Name: usuarios_id_seq; Type: SEQUENCE OWNED BY; Schema: cesb; Owner: postgres
--

ALTER SEQUENCE cesb.usuarios_id_seq OWNED BY cesb.usuarios.id;


--
-- Name: usugrupos; Type: TABLE; Schema: cesb; Owner: postgres
--

CREATE TABLE cesb.usugrupos (
    id integer NOT NULL,
    adm_fl smallint DEFAULT 0 NOT NULL,
    adm_nome character varying(100),
    datacria timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    ativo smallint DEFAULT 1 NOT NULL
);


ALTER TABLE cesb.usugrupos OWNER TO postgres;

--
-- Name: usugrupos_id_seq; Type: SEQUENCE; Schema: cesb; Owner: postgres
--

CREATE SEQUENCE cesb.usugrupos_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE cesb.usugrupos_id_seq OWNER TO postgres;

--
-- Name: usugrupos_id_seq; Type: SEQUENCE OWNED BY; Schema: cesb; Owner: postgres
--

ALTER SEQUENCE cesb.usugrupos_id_seq OWNED BY cesb.usugrupos.id;


--
-- Name: anivers id; Type: DEFAULT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.anivers ALTER COLUMN id SET DEFAULT nextval('cesb.anivers_id_seq'::regclass);


--
-- Name: arqitr iditr; Type: DEFAULT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.arqitr ALTER COLUMN iditr SET DEFAULT nextval('cesb.arqitr_iditr_seq'::regclass);


--
-- Name: arqsetor codarq; Type: DEFAULT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.arqsetor ALTER COLUMN codarq SET DEFAULT nextval('cesb.arqsetor_codarq_seq'::regclass);


--
-- Name: calendev idev; Type: DEFAULT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.calendev ALTER COLUMN idev SET DEFAULT nextval('cesb.calendev_idev_seq'::regclass);


--
-- Name: carousel codcar; Type: DEFAULT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.carousel ALTER COLUMN codcar SET DEFAULT nextval('cesb.carousel_codcar_seq'::regclass);


--
-- Name: escolhas codesc; Type: DEFAULT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.escolhas ALTER COLUMN codesc SET DEFAULT nextval('cesb.escolhas_codesc_seq'::regclass);


--
-- Name: ocorrencias codocor; Type: DEFAULT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.ocorrencias ALTER COLUMN codocor SET DEFAULT nextval('cesb.ocorrencias_codocor_seq'::regclass);


--
-- Name: ocorrideogr codideo; Type: DEFAULT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.ocorrideogr ALTER COLUMN codideo SET DEFAULT nextval('cesb.ocorrideogr_codideo_seq'::regclass);


--
-- Name: paramsis idpar; Type: DEFAULT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.paramsis ALTER COLUMN idpar SET DEFAULT nextval('cesb.paramsis_idpar_seq'::regclass);


--
-- Name: poslog id; Type: DEFAULT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.poslog ALTER COLUMN id SET DEFAULT nextval('cesb.poslog_id_seq'::regclass);


--
-- Name: ramais_ext codtel; Type: DEFAULT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.ramais_ext ALTER COLUMN codtel SET DEFAULT nextval('cesb.ramais_ext_codtel_seq'::regclass);


--
-- Name: ramais_int codtel; Type: DEFAULT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.ramais_int ALTER COLUMN codtel SET DEFAULT nextval('cesb.ramais_int_codtel_seq'::regclass);


--
-- Name: setores codset; Type: DEFAULT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.setores ALTER COLUMN codset SET DEFAULT nextval('cesb.setores_codset_seq'::regclass);


--
-- Name: subsetores codsubset; Type: DEFAULT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.subsetores ALTER COLUMN codsubset SET DEFAULT nextval('cesb.subsetores_codsubset_seq'::regclass);


--
-- Name: tarefas idtar; Type: DEFAULT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.tarefas ALTER COLUMN idtar SET DEFAULT nextval('cesb.tarefas_idtar_seq'::regclass);


--
-- Name: tarefas_msg idmsg; Type: DEFAULT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.tarefas_msg ALTER COLUMN idmsg SET DEFAULT nextval('cesb.tarefas_msg_idmsg_seq'::regclass);


--
-- Name: textopag codset; Type: DEFAULT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.textopag ALTER COLUMN codset SET DEFAULT nextval('cesb.textopag_codset_seq'::regclass);


--
-- Name: trafego codtraf; Type: DEFAULT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.trafego ALTER COLUMN codtraf SET DEFAULT nextval('cesb.trafego_codtraf_seq'::regclass);


--
-- Name: trocas idtr; Type: DEFAULT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.trocas ALTER COLUMN idtr SET DEFAULT nextval('cesb.trocas_idtr_seq'::regclass);


--
-- Name: usuarios id; Type: DEFAULT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.usuarios ALTER COLUMN id SET DEFAULT nextval('cesb.usuarios_id_seq'::regclass);


--
-- Name: usugrupos id; Type: DEFAULT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.usugrupos ALTER COLUMN id SET DEFAULT nextval('cesb.usugrupos_id_seq'::regclass);


--
-- Data for Name: anivers; Type: TABLE DATA; Schema: cesb; Owner: postgres
--

COPY cesb.anivers (id, nomeusu, nomecompl, diaaniv, mesaniv, usucod, usuins, usumodif, datains, datamodif, ativo) FROM stdin;
1	Ludinir	Ludinir Picelli	01	01	0	1	1	2023-10-28 20:55:03	2023-10-28 20:55:03	1
2	Fulano	Fulano de Tal	30	10	0	1	1	2023-10-28 00:55:03	2023-10-28 00:55:03	1
3	Sicrano	Sicrano de Tal	31	10	0	1	1	2023-10-28 00:55:03	2023-10-28 00:55:03	1
4	Beltrano	Beltrano de Tal	31	10	0	1	1	2023-10-28 00:55:03	2023-10-28 00:55:03	1
5	João	João das Couves	31	10	0	1	1	2023-10-28 00:55:03	2023-10-28 00:55:03	1
6	José	José do Ovo	31	10	0	1	1	2023-10-28 00:55:03	2023-10-28 00:55:03	1
7	Bananéia	Bananéia da Silva	31	10	0	1	2	2023-10-28 00:55:03	2023-10-28 00:55:03	1
8	Viola	Benvindo Viola	31	10	0	1	1	2023-10-28 00:55:03	2023-10-28 00:55:03	1
9	Aparecido	Bispo Aparecido	31	12	0	1	1	2023-10-28 00:55:03	2023-12-17 13:14:30	1
10	Alpina 	Cafiaspirina da Cruz Alpina	29	12	0	1	1	2023-10-28 00:55:03	2023-12-17 13:14:24	1
11	Carabino	Carabino Tiro Certo	29	10	0	1	1	2023-10-28 00:55:03	2023-11-06 12:23:43	1
12	Chevrolet	Chevrolet da Silva Ford	15	11	0	1	1	2023-10-28 00:55:03	2023-11-06 12:23:51	1
13	Mirela	Mirela Tapioca	10	11	0	1	1	2023-10-28 00:55:03	2023-11-06 12:24:18	1
14	Linhares	Oceano Atlântico Linhares	13	11	0	1	1	2023-10-28 00:55:03	2023-11-06 12:24:05	1
15	Camisildo	Camisildo da Seleção	27	11	0	1	1	2023-10-28 00:55:03	2023-11-06 12:23:37	1
16	Beltrano	Beltrano da Silva Sauro	09	11	4	2	1	2023-10-28 00:55:03	2023-11-06 12:23:31	1
17	Fulano	Fulano de Tal	09	11	2	2	1	2023-10-28 00:55:03	2023-11-06 12:23:55	1
18	Sicrano	Sicrano Bananildo	21	11	3	2	1	2023-10-28 00:55:03	2023-11-06 12:24:31	1
\.


--
-- Data for Name: arqitr; Type: TABLE DATA; Schema: cesb; Owner: postgres
--

COPY cesb.arqitr (iditr, idtroca, iduser, idsetor, datains, nomearq) FROM stdin;
1	6524	1	3	2023-10-09 23:43:20	DAC-6524ba48211db-Alexander Strachan_Pixabay_buffalo-4728339_1920.jpg
2	3	1	3	2023-10-10 00:05:39	DAC-6524bf5b65188-Alexandra_Koch_plug-7785880_1920 - Copia.jpg
3	3	1	3	2023-10-10 00:08:11	DAC-6524c01495625-Alexander Strachan_Pixabay_earth-hour-4472693_1920.jpg
4	2	1	2	2024-04-17 00:34:05.236997	DG-661f42ef860ce-armario-amarelo.jpg
5	1	1	3	2024-04-17 00:43:07.73393	DAC-661f45408caa2-armario_medidas.jpg
\.


--
-- Data for Name: arqsetor; Type: TABLE DATA; Schema: cesb; Owner: postgres
--

COPY cesb.arqsetor (codarq, codsetor, codsubsetor, descarq, usuins, datains, usuapag, dataapag, ativo, nomearq) FROM stdin;
1	2	1	6522093d3f8b4-DG-Lua de mel da borboleta.pdf	2	2023-10-09 00:36:20	0	2023-10-09	1	\N
2	4	1	652206a1ced3d-DAE-Lua de mel da borboleta.pdf	2	2023-10-09 00:36:20	0	2023-10-09	1	\N
3	2	1	652205b90c80d-DG-AvisoAosNavegantes.pdf	2	2023-10-09 00:36:20	0	2023-10-09	1	\N
\.


--
-- Data for Name: calendev; Type: TABLE DATA; Schema: cesb; Owner: postgres
--

COPY cesb.calendev (idev, evnum, titulo, cor, dataini, localev, ativo, repet, fixo, usuins, usumodif, usuapag, datains, datamodif, dataapag, avobrig, avok) FROM stdin;
1	1	Confraternização Universal	#F5DEB3	2023-01-01	Feliz Ano Novo	1	2	1	1	1	0	3000-12-31 00:00:00	3000-12-31 00:00:00	3000-12-31	0	0
2	2	Tiradentes	#F5DEB3	2023-04-21		1	2	1	1	1	0	3000-12-31 00:00:00	3000-12-31 00:00:00	3000-12-31	0	0
3	3	Dia do Trabalho	#F5DEB3	2023-05-01		1	2	1	1	1	0	3000-12-31 00:00:00	3000-12-31 00:00:00	3000-12-31	0	0
4	4	Independência do Brasil	#F5DEB3	2023-09-07		1	2	1	1	1	0	3000-12-31 00:00:00	3000-12-31 00:00:00	3000-12-31	0	0
5	5	Padroeira do Brasil	#F5DEB3	2023-10-12	Nossa Senhora Aparecida	1	2	1	1	1	0	3000-12-31 00:00:00	3000-12-31 00:00:00	3000-12-31	0	0
6	6	Finados	#F5DEB3	2023-11-02		1	2	1	1	1	0	3000-12-31 00:00:00	3000-12-31 00:00:00	3000-12-31	0	0
7	7	Proclamação da República	#F5DEB3	2023-11-15		1	2	1	1	1	0	3000-12-31 00:00:00	3000-12-31 00:00:00	3000-12-31	0	0
8	8	Natal	#F5DEB3	2023-12-25	Feliz Natal	1	2	1	1	1	0	3000-12-31 00:00:00	3000-12-31 00:00:00	3000-12-31	0	0
\.


--
-- Data for Name: carousel; Type: TABLE DATA; Schema: cesb; Owner: postgres
--

COPY cesb.carousel (codcar, descarq, descarqant) FROM stdin;
1	imgfundo0.jpg	
2	imgfundo1.jpg	
3	imgfundo2.jpg	
4	imgfundo3.jpg	
\.


--
-- Data for Name: escolhas; Type: TABLE DATA; Schema: cesb; Owner: postgres
--

COPY cesb.escolhas (codesc, esc1, esc2, liberaproj, sit, motinat, sex) FROM stdin;
1			1			
2	01	Janeiro	1	Funcionário	Aposentadoria	Masculino
3	02	Fevereiro	0	Contratado	Desistência	Feminino
4	03	Março	0	Voluntário	Falecimento	Indeterminado
5	04	Abril	0	Excluído	Abandono	
6	05	Maio	0		Rescisão	
7	06	Junho	0			
8	07	Julho	0			
9	08	Agosto	0			
10	09	Setembro	0			
11	10	Outubro	0			
12	11	Novembro	0			
13	12	Dezembro	0			
14	13		0			
15	14		0			
16	15		0			
17	16		0			
18	17		0			
19	18		0			
20	19		0			
21	20		0			
22	21		0			
23	22		0			
24	23		0			
25	24		0			
26	25		0			
27	26		0			
28	27		0			
29	28		0			
30	29		0			
31	30		0			
32	31		0			
\.


--
-- Data for Name: ocorrencias; Type: TABLE DATA; Schema: cesb; Owner: postgres
--

COPY cesb.ocorrencias (codocor, usuins, datains, dataocor, codsetor, usumodif, datamodif, ativo, ocorrencia, numocor) FROM stdin;
1	2	2023-11-09 15:31:13	2023-11-09	2	0	3000-12-31 00:00:00	1	Caí da escada e quebrei o braço.	0001/2023
2	1	2023-11-09 23:28:15	2024-02-01	3	0	3000-12-31 00:00:00	1	Teste 2024	0001/2023
3	1	2023-11-09 23:34:37	2024-03-01	3	0	3000-12-31 00:00:00	1	Testre 2024	0001/2023
4	1	2023-11-09 23:36:06	2023-11-11	3	0	3000-12-31 00:00:00	1		0001/0
5	1	2023-11-09 23:54:11	2023-11-10	3	0	3000-12-31 00:00:00	1	Teste 2024 5	0003/2023
6	1	2023-11-09 23:54:52	2024-06-01	3	0	3000-12-31 00:00:00	1	teste 01 06 2024	0003/2024
7	1	2023-11-10 22:01:59	2023-11-11	3	0	3000-12-31 00:00:00	1	Maecenas pellentesque eros massa, quis consectetur elit semper nec. Sed cursus fermentum gravida. Phasellus sollicitudin blandit ex, vel aliquam arcu vulputate quis. Cras tincidunt commodo ullamcorper. Proin justo mi, cursus non laoreet vel, facilisis at nibh. Aenean tristique, lectus at interdum rutrum, tellus mi faucibus orci, eu venenatis purus sapien vitae lacus. Praesent iaculis accumsan neque. Morbi pharetra, leo a pulvinar molestie, leo odio varius metus, a lobortis enim nisi quis mauris. Morbi eu ultrices ligula. Curabitur sodales erat feugiat sem tincidunt hendrerit. Sed ut hendrerit diam, sit amet commodo sem. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse tempus consectetur diam, ac finibus orci aliquam nec. In venenatis elementum pellentesque. Etiam porta magna et est sagittis dictum. Ut eu urna sit amet diam faucibus vulputate. 	0004/2023
8	1	2023-11-10 22:45:23	2023-11-11	3	0	3000-12-31 00:00:00	1	Teste 10-11	0005/2023
\.


--
-- Data for Name: ocorrideogr; Type: TABLE DATA; Schema: cesb; Owner: postgres
--

COPY cesb.ocorrideogr (codideo, coddaocor, descideo, codprov) FROM stdin;
1	1	modulos/ocorrencias/imagens/CairEscada.png	0
8	6	modulos/ocorrencias/imagens/CairEscadaria.png	0
3	5	modulos/ocorrencias/imagens/paneCarro.png	0
4	5	modulos/ocorrencias/imagens/acidFogoCarro.png	0
5	6	modulos/ocorrencias/imagens/extintor.png	0
6	6	modulos/ocorrencias/imagens/acidMotoPlaca.png	0
7	1	modulos/ocorrencias/imagens/acidBracEng.png	0
12	4	modulos/ocorrencias/imagens/CairCadeira.png	0
15	7	modulos/ocorrencias/imagens/CairCadeira.png	0
16	7	modulos/ocorrencias/imagens/acidCarroArvore.png	0
17	7	modulos/ocorrencias/imagens/extintor.png	0
19	2	modulos/ocorrencias/imagens/acidMotoCarro.png	0
22	8	modulos/ocorrencias/imagens/fogo.png	0
38	8	modulos/ocorrencias/imagens/acidCarroFogo.png	0
\.


--
-- Data for Name: paramsis; Type: TABLE DATA; Schema: cesb; Owner: postgres
--

COPY cesb.paramsis (idpar, admvisu, admcad, admedit, insaniver, editaniver, insevento, editevento, instarefa, edittarefa, insocor, editocor, insramais, editramais, instelef, edittelef, instroca, edittroca, editpagina, insarq) FROM stdin;
1	0	0	0	4	4	4	4	4	4	2	2	7	7	4	4	4	4	4	4
\.


--
-- Data for Name: poslog; Type: TABLE DATA; Schema: cesb; Owner: postgres
--

COPY cesb.poslog (id, pessoas_id, ativo, adm, codsetor, numacessos, logini, logfim, usuins, datains, usumodif, datamodif, usuinat, datainat, motivoinat, avcalend, avhoje) FROM stdin;
1	1	1	7	3	1	2024-04-17 00:39:57.750246	2024-04-17 00:21:38.938867	1	2024-04-17 00:21:38.938867	1	2024-04-17 00:34:57.43664	0	2024-04-17 00:21:38.938867	0	1	2024-04-17
\.


--
-- Data for Name: ramais_ext; Type: TABLE DATA; Schema: cesb; Owner: postgres
--

COPY cesb.ramais_ext (codtel, siglaempresa, nomeempresa, contatonome, codsetor, setor, telefonefixo, telefonecel, usuins, usumodif, datains, datamodif, ativo) FROM stdin;
1	Bombeiros DF	Corpo de Bombeiros Militar do Distrito Federal 	Sicrano	0	Emergência	193		1	1	2023-09-11 16:56:43	2023-09-12 17:29:21	1
2	SAMU 	Serviço de Atendimento Móvel de Urgência		0	Emergência	192	(61) 9999-9999	1	0	2023-09-11 16:58:18	2023-09-25 16:30:45	1
3	PRF	Polícia Rodoviária Federal 		0	Patrulha	191		1	1	2023-09-11 17:00:23	2023-09-13 15:27:25	1
4	PM-DF	Polícia Militar do Distrito Federal	Fulano	0	Emergência	190		1	1	2023-09-11 17:01:17	2023-09-12 16:09:17	1
5	QGEx	Quartel General do Exército	Macumbaldo	0	Gabinete	(61) 3333-4444	(61) 99999-9999	1	1	2023-09-11 22:07:40	2023-09-13 15:40:15	1
\.


--
-- Data for Name: ramais_int; Type: TABLE DATA; Schema: cesb; Owner: postgres
--

COPY cesb.ramais_int (codtel, nomeusu, nomecompl, codsetor, setor, ramal, usuins, usumodif, datains, datamodif, coduser, ativo) FROM stdin;
1	Ludinir	Ludinir Picelli	0	ATI	2222333	1	1	2023-09-25 14:32:41	2023-09-25 14:32:41	683	1
2	Fulano	Fulano da Silva Sauro	0	DAC	1111	1	1	2023-09-25 14:32:41	2023-09-25 14:32:41	0	1
3	João	João das Couves	0	DAF	4445	1	1	2023-09-25 14:32:41	2023-09-25 14:32:41	0	1
4	Camisildo	Camisildo da Seleção	0	ATI	5555	1	1	2023-09-25 14:32:41	2023-09-25 14:32:41	0	1
5	Linhares	Oceano Atlântico Linhares	0	DIJ	R777777	1	1	2023-09-25 14:32:41	2023-09-25 14:32:41	0	1
6	Alpina	Cafiaspirina da Cruz Alpina das Alturas	0	DED	8888	1	1	2023-09-25 14:32:41	2023-09-25 14:32:41	0	1
7	Aparecido	Bispo Aparecido	0	FAEdd	6777999	1	1	2023-09-25 14:32:41	2023-09-25 14:32:41	0	1
8	Mirela	Mirela Tapióca com çedilha	0	ATI	2222	1	1	2023-09-25 14:32:41	2023-09-25 14:32:41	0	1
\.


--
-- Data for Name: setores; Type: TABLE DATA; Schema: cesb; Owner: postgres
--

COPY cesb.setores (codset, siglasetor, descsetor, mordem, usuins, usumodif, datains, datamodif, ativo, textopag) FROM stdin;
1			1	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
2	DG	Diretoria-Geral	1	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Diretoria-Geral&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 13pt;&quot;&gt;&lt;span class=&quot;BxUVEf ILfuVd&quot; lang=&quot;pt&quot;&gt;&lt;span class=&quot;hgKElc&quot;&gt;Dirigir, planejar, organizar e controlar as atividades de diversas &amp;aacute;reas da Comunh&amp;atilde;o Esp&amp;iacute;rita de Bras&amp;iacute;lia, fixando pol&amp;iacute;ticas de gest&amp;atilde;o dos recursos financeiros, &lt;/span&gt;&lt;/span&gt;&lt;span style=&quot;font-family: &quot;Open Sans&quot;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;&lt;img style=&quot;float: left;&quot; src=&quot;itr/DG-652203438beba-LogoComunhao.png&quot; alt=&quot;&quot; width=&quot;125&quot; height=&quot;110&quot; /&gt;&lt;/span&gt;&lt;span class=&quot;BxUVEf ILfuVd&quot; lang=&quot;pt&quot;&gt;&lt;span class=&quot;hgKElc&quot;&gt;administrativos, estrutura&amp;ccedil;&amp;atilde;o, racionaliza&amp;ccedil;&amp;atilde;o, e adequa&amp;ccedil;&amp;atilde;o dos diversos servi&amp;ccedil;os.&lt;/span&gt;&lt;/span&gt;&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 13pt;&quot;&gt;&lt;span style=&quot;font-family: &quot;Open Sans&quot;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp; &lt;/span&gt;&lt;span class=&quot;BxUVEf ILfuVd&quot; lang=&quot;pt&quot;&gt;&lt;span class=&quot;hgKElc&quot;&gt;Trata da assessoria pessoal e institucional da Presid&amp;ecirc;ncia, atendendo pessoas, organizando audi&amp;ecirc;ncias e agenda, viabilizando o relacionamento do Presidente com as diretorias e assessorias, exercendo atividades articuladas com todos os &amp;oacute;rg&amp;atilde;os da Casa.&lt;/span&gt;&lt;/span&gt;&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-family: &quot;Open Sans&quot;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;FIM&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;
3	DAC	Diretoria de Arte e Cultura	2	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Diretoria de Arte e Cultura&lt;br /&gt;&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;&lt;div class=&quot;wpb_text_column wpb_content_element&quot;&gt;&lt;div class=&quot;wpb_wrapper&quot;&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;A Diretoria de Arte e Cultura desenvolve a arte e a cultura esp&amp;iacute;rita da no &amp;acirc;mbito interno e externo da Comunh&amp;atilde;o Esp&amp;iacute;rita de Bras&amp;iacute;lia.&amp;nbsp; Coral Elos de Luz, Coral Elinhos &lt;/span&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;&lt;span style=&quot;font-family: &quot;Open Sans&quot;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;&lt;img style=&quot;float: left;&quot; src=&quot;itr/DAC-6522e4a17d51e-Clker-Free-Vector-Images_Pixabay_sun-g237bae17e_1280.png&quot; alt=&quot;&quot; width=&quot;132&quot; height=&quot;112&quot; /&gt;&lt;/span&gt;&lt;/span&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;de Luz, teatro m&amp;uacute;sica, bandas, bal&amp;eacute;, sapateado, musicas, todos envolvidos na divulg&lt;/span&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;a&amp;ccedil;&amp;atilde;o da Doutrina Esp&amp;iacute;rita.&lt;/span&gt;&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;&amp;nbsp; &amp;nbsp; &amp;nbsp;&amp;nbsp; &amp;ldquo;O espiritismo vem abrir para a arte novas perspectivas, horizontes sem limites. A comunica&amp;ccedil;&amp;atilde;o que ele estabelece entre os mundos vis&amp;iacute;vel e invis&amp;iacute;vel, as indica&amp;ccedil;&amp;otilde;es fornecidas sobre as condi&amp;ccedil;&amp;otilde;es da vida no al&amp;eacute;m, a revela&amp;ccedil;&amp;atilde;o que ele nos traz das leis de harmonia e de beleza que regem o universo v&amp;ecirc;m oferecer aos nossos pensadores, aos nossos artistas, motivos inesgot&amp;aacute;veis de inspira&amp;ccedil;&amp;atilde;o.&amp;rdquo;&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp; (a) L&amp;eacute;on Denis&lt;/p&gt;
4	DAE	Diretoria de Assistência Espiritual	3	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Diretoria de Assist&amp;ecirc;ncia Espiritual&lt;br /&gt;&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;&lt;div class=&quot;wpb_text_column wpb_content_element  vc_custom_1624446588787&quot;&gt;&lt;div class=&quot;wpb_wrapper&quot;&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;O atendimento espiritual presta aux&amp;iacute;lio aos irm&amp;atilde;os encarnados, atrav&amp;eacute;s da fluidoterapia, e aos irm&amp;atilde;os desencarnados pela pr&amp;aacute;tica do interc&amp;acirc;mbio medi&amp;uacute;nico em grupos espec&amp;iacute;ficos, al&amp;eacute;m de desenvolver a atividade de educa&amp;ccedil;&amp;atilde;o da mediunidade.&amp;nbsp; De todos os princ&amp;iacute;pios da Doutrina Esp&amp;iacute;rita, no seu contexto pr&amp;aacute;tico, cabe a Diretoria de Assist&amp;ecirc;ncia Espiritual (DAE) a lida di&amp;aacute;ria com a comunicabilidade dos esp&amp;iacute;ritos.&lt;/span&gt;&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;Da desobsess&amp;atilde;o &amp;agrave; educa&amp;ccedil;&amp;atilde;o da mediunidade, dos passes de harmoniza&amp;ccedil;&amp;atilde;o e o tratamento desobsessivo aos de restabelecimento da sa&amp;uacute;de integral do ser humano, abertos ao p&amp;uacute;blico, possibilitando, cada um em seu prop&amp;oacute;sito espec&amp;iacute;fico, a comunic&lt;/span&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;a&amp;ccedil;&amp;atilde;o dos esp&amp;iacute;ritos.&lt;/span&gt;&lt;/p&gt;&lt;p&gt;&lt;span style=&quot;font-family: &quot;Open Sans&quot;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;&lt;img style=&quot;float: left;&quot; src=&quot;itr/DAE-652206bd11b82-blossoms-2659967_1920.png&quot; alt=&quot;&quot; width=&quot;176&quot; height=&quot;129&quot; /&gt;&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;O interc&amp;acirc;&lt;/span&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;mbio medi&amp;uacute;nico &amp;eacute; realizado atrav&amp;eacute;s dos intermedi&amp;aacute;rios entre os dois mundos: os m&amp;eacute;&lt;/span&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;diuns. &lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;A t&lt;/span&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;arefa exige de cada trabalhador muito estudo, disciplina espartana e perseveran&amp;ccedil;a inamov&amp;iacute;vel. Em contrapartida, recebem dos Esp&amp;iacute;ritos Superiores for&amp;ccedil;as para o sustento da vida di&amp;aacute;ria, enquanto laboram na seara de Jesus em um aben&amp;ccedil;oado atendimento fraterno a irm&amp;atilde;os desencarnados, necessitados de caridade, benevol&amp;ecirc;ncia e amor.&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;
5	DAF	Diretoria Administrativa e Financeira	4	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Diretoria Administrativa e Financeira&lt;br /&gt;&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;&lt;span style=&quot;font-family: arial, helvetica, sans-serif; font-size: medium;&quot;&gt;A Diretoria Administrativa e Financeira &amp;ndash; DAF &amp;eacute; respons&amp;aacute;vel pela administra&amp;ccedil;&amp;atilde;o geral da Comunh&amp;atilde;o Esp&amp;iacute;rita de Bras&amp;iacute;lia, propiciando os meios e recursos necess&amp;aacute;rios &amp;agrave; realiza&amp;ccedil;&amp;atilde;o das a&amp;ccedil;&amp;otilde;es, programas, projetos e iniciativas desenvolvidas pela Casa.&amp;nbsp;&lt;/span&gt;&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;&lt;span style=&quot;font-family: arial, helvetica, sans-serif; font-size: medium;&quot;&gt;Entre suas atribui&amp;ccedil;&amp;otilde;es est&amp;atilde;o planejar e gerir as finan&amp;ccedil;as e or&amp;ccedil;amentos da Comunh&amp;atilde;o, tra&amp;ccedil;ando as diretrizes estrat&amp;eacute;gicas, or&amp;ccedil;ament&amp;aacute;rias, cont&amp;aacute;beis, fiscais e de custos.&lt;/span&gt;&lt;/span&gt;&lt;/p&gt;&lt;p&gt;Sob sua responsabilidade est&amp;atilde;o as tarefas administrativas e de gest&amp;atilde;o econ&amp;ocirc;mico-financeira da Comunh&amp;atilde;o, dentre as quais:&lt;/p&gt;&lt;ul&gt;&lt;li&gt;Administra&amp;ccedil;&amp;atilde;o&lt;/li&gt;&lt;li&gt;Recursos Humanos&lt;/li&gt;&lt;li&gt;Planejamento e Execu&amp;ccedil;&amp;atilde;o Or&amp;ccedil;ament&amp;aacute;ria&lt;/li&gt;&lt;li&gt;Compras, Recebimentos e Pagamentos&lt;/li&gt;&lt;li&gt;Presta&amp;ccedil;&amp;atilde;o de Contas&lt;/li&gt;&lt;li&gt;Acompanhamento e Controle de receitas e despesas&lt;/li&gt;&lt;li&gt;Almoxarifado&lt;/li&gt;&lt;li&gt;Bazar&lt;/li&gt;&lt;li&gt;Livraria&lt;/li&gt;&lt;li&gt;Acompanhamento e Controle do quadro de Associados&lt;/li&gt;&lt;li&gt;Recebimento de doa&amp;ccedil;&amp;otilde;es e contribui&amp;ccedil;&amp;otilde;es&lt;/li&gt;&lt;/ul&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&amp;nbsp;&lt;/p&gt;
6	DAO	Diretoria de Atendimento e Orientação	5	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Diretoria de Atendimento Fraterno&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;Atende fraternalmente as pessoas que procuram a Comunh&amp;atilde;o Esp&amp;iacute;rita de Bras&amp;iacute;lia, informando e encaminhando os solicitantes aos setores ou &amp;oacute;rg&amp;atilde;os onde dever&amp;atilde;o encontrar respostas para as suas buscas.&lt;/strong&gt;&lt;/p&gt;&lt;div class=&quot;wpb_text_column wpb_content_element&quot;&gt;&lt;div class=&quot;wpb_wrapper&quot;&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;O Atendimento Fraterno &amp;eacute; a porta de entrada da Comunh&amp;atilde;o Esp&amp;iacute;rita, por onde o p&amp;uacute;blico chega movido pela dor e pelo sofrimento. &amp;Eacute; o pronto-socorro espiritual, onde &amp;eacute; poss&amp;iacute;vel alcan&amp;ccedil;ar al&amp;iacute;vio para a dor da alma.&lt;/span&gt;&lt;/p&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt; &lt;/span&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;O atendimento on-line &amp;eacute; para todo e qualquer p&amp;uacute;blico, seja com problemas fisicos, espirituais ou emocionais, alcan&amp;ccedil;ando quase todos os estados do Brasil.&lt;/span&gt;&lt;/p&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt; &lt;/span&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;span style=&quot;font-size: 11pt;&quot;&gt;&lt;strong&gt;O sigilo, a privacidade e o n&amp;atilde;o julgamento s&amp;atilde;o os principios b&amp;aacute;sicos do atendimento&lt;/strong&gt;. &lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;Este servi&amp;ccedil;o se utiliza de tr&amp;ecirc;s pilares para apoiar pessoas: o acolhimento, o consolo e, se possivel, o esclarecimento daquela dor. A partir da&amp;iacute;, o tratamento &amp;eacute; indicado para cada um (harmoniza&amp;ccedil;&amp;atilde;o, irradia&amp;ccedil;&amp;atilde;o, estudo do Evangelho Segundo o Espiritismo, desobsess&amp;atilde;o ou tratamento integral).&lt;/span&gt;&lt;/p&gt;&lt;/div&gt;&lt;/div&gt;&lt;div class=&quot;wpb_text_column wpb_content_element&quot;&gt;&amp;nbsp;&lt;/div&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&amp;nbsp;&lt;/p&gt;
7	DED	Diretoria de Estudos Doutrinários	6	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Diretoria de Estudos Doutrin&amp;aacute;rios&lt;br /&gt;&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;A diretoria promove o Estudo Sistematizado da Doutrina Esp&amp;iacute;rita, ESDE.&amp;nbsp; &amp;Eacute; o estudo, s&amp;eacute;rio, regular, fraterno e cont&amp;iacute;nuo da Doutrina Esp&amp;iacute;rita, tendo como base os ensinamentos morais de Jesus e as obras b&amp;aacute;sicas compiladas por Allan Kardec, quais sejam: O Livro dos Esp&amp;iacute;ritos, O Livro dos M&amp;eacute;diuns, O Evangelho Segundo o Espiritismo, O C&amp;eacute;u e o Inferno, A G&amp;ecirc;nese, O que &amp;eacute; o Espiritismo, Obras P&amp;oacute;stumas, Revista Esp&amp;iacute;rita (1858 a 1869) etc.&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;Tem como objetivos: proporcionar a transforma&amp;ccedil;&amp;atilde;o moral; garantir a unidade de compreens&amp;atilde;o em torno dos princ&amp;iacute;pios doutrin&amp;aacute;rios esp&amp;iacute;ritas; divulgar a Doutrina Esp&amp;iacute;rita nas bases em que foi codificada como Doutrina Consoladora de tr&amp;iacute;plice aspecto: cient&amp;iacute;fico, filos&amp;oacute;fico e religioso; favorecer o desenvolvimento da f&amp;eacute; raciocinada; incentivar os seus participantes a um envolvimento maior nas atividades da Comunh&amp;atilde;o Esp&amp;iacute;rita de Bras&amp;iacute;lia.&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&amp;nbsp;&lt;/p&gt;
8	DIJ	Diretoria de Infância e Juventude	7	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Diretoria de Inf&amp;acirc;ncia e Juventude&lt;br /&gt;&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 12pt;&quot;&gt;A Evangeliza&amp;ccedil;&amp;atilde;o promovida pela Diretoria de Inf&amp;acirc;ncia e Juventude da Comunh&amp;atilde;o Esp&amp;iacute;rita de Bras&amp;iacute;lia est&amp;aacute; baseada na metodologia do Cristo e de Kardec e esfor&amp;ccedil;a-se em segui-la, na busca do despertar das consci&amp;ecirc;ncias rec&amp;eacute;m-reencarnadas que recebe, impelindo-as &amp;agrave; transforma&amp;ccedil;&amp;atilde;o &amp;iacute;ntima por interm&amp;eacute;dio do conhecimento e da pr&amp;aacute;tica do Cristianismo, &amp;agrave; luz da Doutrina Esp&amp;iacute;rita, atrav&amp;eacute;s do exemplo dos pais e evangelizadores e pelo est&amp;iacute;mulo &amp;agrave; pr&amp;aacute;tica da caridade.&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;Evangelizar &amp;eacute;...&lt;/p&gt;&lt;div class=&quot;wpb_column vc_column_container vc_col-sm-4 mpc-column&quot; data-column-id=&quot;mpc_column-63661caa91d2a14&quot;&gt;&lt;div class=&quot;vc_column-inner&quot;&gt;&lt;div class=&quot;wpb_wrapper&quot;&gt;&lt;div class=&quot;wpb_text_column wpb_content_element&quot;&gt;&lt;div class=&quot;wpb_wrapper&quot;&gt;&lt;ul&gt;&lt;li&gt;Uma oportunidade&lt;/li&gt;&lt;li&gt;Uma responsabilidade&lt;/li&gt;&lt;li&gt;Uma tarefa de amor&lt;/li&gt;&lt;li&gt;Um processo cont&amp;iacute;nuo&amp;nbsp;&amp;nbsp; de&amp;nbsp;&amp;nbsp; transforma&amp;ccedil;&amp;atilde;o&amp;nbsp;&amp;nbsp; &amp;iacute;ntima&amp;nbsp;&amp;nbsp; que&amp;nbsp;&amp;nbsp; necessita&amp;nbsp;&amp;nbsp; de&amp;nbsp;&amp;nbsp; um&amp;nbsp;&amp;nbsp; trabalho colaborativo e harmonioso&lt;/li&gt;&lt;/ul&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;/div&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;
9	DED	Diretoria de Promoção Social	8	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Diretoria de Promo&amp;ccedil;&amp;atilde;o Social&lt;br /&gt;&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-size: 13pt;&quot;&gt;A Diretoria de Promo&amp;ccedil;&amp;atilde;o Social da Comunh&amp;atilde;o Esp&amp;iacute;rita de Bras&amp;iacute;lia, busca acolher e promover pessoas e fam&amp;iacute;lias em estado de vulnerabilidade social, incentivando-as para o seu desenvolvimento espiritual, mental, f&amp;iacute;sico e social &amp;agrave; luz dos valores universais, contribuindo para amenizar o impacto da pobreza e proporcionar o bem-estar social.&lt;/span&gt;&lt;/p&gt;&lt;h2 class=&quot;bdt-heading-title&quot; style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span class=&quot;bdt-main-heading&quot; style=&quot;font-size: 14pt;&quot;&gt;&lt;span class=&quot;bdt-main-heading-inner&quot;&gt;Nossa inspira&amp;ccedil;&amp;atilde;o &amp;eacute; a Promo&amp;ccedil;&amp;atilde;o Social &lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/h2&gt;&lt;h2 class=&quot;bdt-heading-title&quot; style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span class=&quot;bdt-main-heading&quot; style=&quot;font-size: 14pt;&quot;&gt;&lt;span class=&quot;bdt-main-heading-inner&quot;&gt;e a Promo&amp;ccedil;&amp;atilde;o do Esp&amp;iacute;rito Imortal &lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/h2&gt;&lt;h2 class=&quot;bdt-heading-title&quot; style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span class=&quot;bdt-main-heading&quot; style=&quot;font-size: 14pt;&quot;&gt;&lt;span class=&quot;bdt-main-heading-inner&quot;&gt;Reabilita&amp;ccedil;&amp;atilde;o do Ser.&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/h2&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&amp;nbsp;&lt;/p&gt;
11	ACE	Assessoria de Comunicação e Eventos	10	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Assessoria&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;FIM&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;
15	APE	Assessoria de Planejamento Estratégico	14	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Assessoria&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;FIM&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;
16	APV	Assessoria da Pomada do Vovô Pedro	15	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Assessoria&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;FIM&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;
10	AAD	Assessoria de Assuntos Doutrinários	9	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Assessoria&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;FIM&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;
12	ADI	Assessoria de Desenvolvimento Institucional	11	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Assessoria&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;FIM&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;
13	AJU	Assessoria Jurídica	12	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Assessoria&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;FIM&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;
14	AME	Assessoria de Estudos e Aplicações de Medicina Espiritual	13	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Assessoria&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;FIM&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;
17	ATI	Assessoria de Tecnologia da Informação	16	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Assessoria&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;FIM&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;
18	OUV	Ouvidoria	17	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Assessoria&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;&lt;p&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?&lt;/span&gt;&lt;/p&gt;&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;FIM&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;
\.


--
-- Data for Name: subsetores; Type: TABLE DATA; Schema: cesb; Owner: postgres
--

COPY cesb.subsetores (codsubset, coddosetor, siglasubsetor, descsubsetor, smordem, usuins, usumodif, datains, datamodif, ativo, textopag) FROM stdin;
1	0			1	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
2	3	CODAC	Coordenação Administrativa DAC	1	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
3	3	DIPRA	Divisão de Produção Artística	2	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
4	3	DIDAN	Divisão de Dança	3	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
5	3	DITEA	Divisão de Teatro	4	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
6	3	DIMUS	Divisão de Música	5	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
7	3	DICIN	Divisão de Cinema	6	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
8	3	DIPPI	Divisão de Poesia e Pintura	7	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
9	4	DITAD	Divisão de Tratamento e Desobsessão	1	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
10	4	DIEME	Divisão de Educação da Mediunidade	2	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
11	4	DIPAH	Divisão de Passes de Harmonização	3	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
12	4	DIDES	Divisão de Desobsessão	4	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
13	4	DIAMO	Divisão de Apoio ao Médium Ostensivo em Eclosão da Mediunidade	5	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
14	5	DIADM	Divisão Administrativa DAF	1	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
15	5	DIFIN	Divisão Fianceira	2	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
16	5	LIVRARIA	Divisão de Livraria	3	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
17	5	BAZAR	Divisão de Bazar	4	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
18	5	ALMOX	Divisão de Almoxariafado	5	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
19	6	DIVAP	Divisão de Atendimento ao Público	1	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
20	6	DIVAF	Divisão de Atendimento Fraterno	2	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
21	6	DIVAT	Divisão de Atendimento Específico e Formação	3	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
22	7	DIFTE	Divisão de Formação do Trabalhador Espírita	1	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
23	7	DIVES	Divisão de Estudo Sistematizado da Doutrina Espírita	2	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
24	7	DIPAD	Divisão do Programa de Adaptação à Doutrina Espírita	3	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
25	7	DIMOC	Divisão da Mocidade Espírita da Comunhão	4	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
26	7	DIPAP	Divisão de Pesquisa e Aperfeçoamento	5	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
27	7	DIESP	Divisão de Especialização	6	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
28	8	DIRME	Divisão de Recursos e Meios	1	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
29	8	DEMAT	Divisão de Evangelização do Maternal	2	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
30	8	DEINF	Divisão de Evangelização da Infância	3	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
31	8	DEJUV	Divisão de Evangelização da Juventude	4	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
32	8	DEFAM	Divisão de Evangelização da Família	5	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
33	9	DIAFA	Divisão de Acompanhamento de Famílias	1	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
34	9	DIOFI	Divisão de Oficinas	2	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
35	9	DIADA	Divisão de Arrecadação e Distribuição de Alimentos	3	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
36	9	DIAFRA	Divisão Fraterna	4	1	1	2023-10-06 21:24:56	2023-10-06 21:24:56	1	
\.


--
-- Data for Name: tarefas; Type: TABLE DATA; Schema: cesb; Owner: postgres
--

COPY cesb.tarefas (idtar, usuins, usuexec, tittarefa, textotarefa, prio, sit, datains, datasit1, datasit2, datasit3, datasit4, usumodifsit, usumodif, datamodif, usucancel, datacancel, ativo) FROM stdin;
1	1	2	Conserto torradeira da cantina.	\N	2	2	2023-10-28 00:55:03	2023-10-29 00:38:51	2023-10-29 00:39:12	3000-12-31 00:00:00	3000-12-31 00:00:00	2	0	3000-12-31 00:00:00	0	3000-12-31 00:00:00	1
2	1	2	Acerto documentação do Setor.	\N	2	1	2023-10-28 00:55:03	2023-10-29 00:38:51	3000-12-31 00:00:00	3000-12-31 00:00:00	3000-12-31 00:00:00	0	0	3000-12-31 00:00:00	0	3000-12-31 00:00:00	1
\.


--
-- Data for Name: tarefas_msg; Type: TABLE DATA; Schema: cesb; Owner: postgres
--

COPY cesb.tarefas_msg (idmsg, iduser, idtarefa, usuinstar, usuexectar, datamsg, textomsg, inslido, execlido, tarefa_ativ, tarefa_lida, elim, dataelim) FROM stdin;
1	1	4	2	3	2023-11-18 22:29:29	Teste	0	1	1	1	1	2023-11-18 22:29:48
\.


--
-- Data for Name: textopag; Type: TABLE DATA; Schema: cesb; Owner: postgres
--

COPY cesb.textopag (codset, textopag) FROM stdin;
1	&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Diretoria&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;\\r\\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\\r\\n&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?&lt;/span&gt;&lt;/p&gt;\\r\\n&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;\\r\\n&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;FIM&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;
2	&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-size: 20pt;&quot;&gt;Assessoria&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;\\r\\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\\r\\n&lt;p style=&quot;text-align: justify;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?&lt;/span&gt;&lt;/p&gt;\\r\\n&lt;p style=&quot;text-align: justify;&quot;&gt;&amp;nbsp;&lt;/p&gt;\\r\\n&lt;p style=&quot;text-align: center;&quot;&gt;&lt;strong&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;span style=&quot;font-family: &#039;Open Sans&#039;, Arial, sans-serif; text-align: justify; background-color: #ffffff;&quot;&gt;FIM&lt;/span&gt;&lt;/span&gt;&lt;/strong&gt;&lt;/p&gt;
\.


--
-- Data for Name: trafego; Type: TABLE DATA; Schema: cesb; Owner: postgres
--

COPY cesb.trafego (codtraf, descarq, nomearq, usuins, datains, usudest, usuapag, dataapag, ativo) FROM stdin;
1	65412590062b4-A Boa Nova.pdf	A Boa Nova.pdf	1	2023-10-31 13:04:32	0	1	2023-10-31	1
2	6541967241fff-A Genese.pdf	A Gênese.pdf	1	2023-10-31 21:06:10	0	1	2023-10-31	1
\.


--
-- Data for Name: trocas; Type: TABLE DATA; Schema: cesb; Owner: postgres
--

COPY cesb.trocas (idtr, iduser, idsetor, datains, textotroca, trocaativa) FROM stdin;
2	1	2	2023-10-06 14:40:33	<p style="text-align: center;"><strong>Arm&aacute;rio Amarelo</strong> para doa&ccedil;&atilde;o, disponibilizado na diretoria-geral.</p>\n<p>&nbsp;</p>\n<p><strong><img style="display: block; margin-left: auto; margin-right: auto;" src="itr/DG-661f42ef860ce-armario-amarelo.jpg" alt="" width="200" height="125" /></strong></p>\n<p style="text-align: center;">&nbsp;</p>\n<p style="text-align: center;">Tratar com <strong>Sicrano de Tal</strong> - ramal 2222.</p>\n<p>&nbsp;</p>	1
1	1	3	2023-10-05 18:55:38	<p style="text-align: center;"><strong>Arm&aacute;rio vertical</strong></p>\n<p>&nbsp;</p>\n<p><img style="display: block; margin-left: auto; margin-right: auto;" src="itr/DAC-661f45408caa2-armario_medidas.jpg" alt="" width="200" height="200" /></p>\n<p>&nbsp;</p>\n<div style="text-align: center;">Arm&aacute;rio dispon&iacute;vel para troca, descarte ou doa&ccedil;&atilde;o.</div>\n<div style="text-align: center;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Tratar com Fulano pelo ramal interno 3333.</div>\n<div style="text-align: center;">Arm&aacute;rio dispon&iacute;vel para troca, descarte ou doa&ccedil;&atilde;o.</div>\n<div>\n<div style="text-align: center;">Grato.</div>\n</div>	1
\.


--
-- Data for Name: usuarios; Type: TABLE DATA; Schema: cesb; Owner: postgres
--

COPY cesb.usuarios (id, usuario, nome, nomecompl, idpessoa, sexo, senha, adm, codsetor, codsubsetor, diaaniv, mesaniv, usuins, datains, usumodif, datamodif, ativo, usuinat, datainat, motivoinat, numacessos, ultlog) FROM stdin;
3	Sicrano	Sicrano	Sicrano Bananildo	0	1	202cb962ac59075b964b07152d234b70	2	3	1	06	10	0	2023-10-20 01:11:44	1	2023-10-23 17:18:08	1	0	2023-10-20 01:11:44	0	15	2023-11-09 15:31:35
4	Beltrano	Beltrano	Beltrano da Silva Sauro	0	1	202cb962ac59075b964b07152d234b70	2	6	1	07	10	0	2023-10-20 01:11:44	0	2023-10-20 01:11:44	1	0	2023-10-20 01:11:44	0	0	2023-10-20 01:11:44
5	Moisés	Moisés	Moisés Patriarca	0	1	202cb962ac59075b964b07152d234b70	7	2	1	07	10	0	2023-10-20 01:11:44	0	2023-10-20 01:11:44	1	0	2023-10-20 01:11:44	0	0	2023-10-20 01:11:44
6	11122233344	Fulano	Fulano	1	1	202cb962ac59075b964b07152d234b70	2	1	1	26	10	0	2023-10-25 22:35:48	0	2023-10-25 22:35:48	1	0	2023-10-25 22:35:48	0	12	2023-10-26 00:38:32
7	11122233345	Sicrano	Sicrano	2	1	202cb962ac59075b964b07152d234b70	2	1	1	10	01	0	2023-10-25 23:01:17	0	3000-12-31 00:00:00	1	0	3000-12-31 00:00:00	0	1	2023-10-25 23:01:17
8	11122233346	Beltrano	Beltrano	3	1	202cb962ac59075b964b07152d234b70	2	1	1	11	10	0	2023-10-25 23:02:41	0	3000-12-31 00:00:00	1	0	3000-12-31 00:00:00	0	1	2023-10-25 23:02:41
9	13652176049	Ludinir Picelli	Ludinir Picelli	4	1	202cb962ac59075b964b07152d234b70	7	1	1	12	12	0	2023-10-26 00:42:25	0	3000-12-31 00:00:00	1	0	3000-12-31 00:00:00	0	17	2023-10-27 23:57:50
2	Fulano	Fulano	Fulano de Tal	0	1	202cb962ac59075b964b07152d234b70	4	2	1	05	10	0	2023-10-20 01:11:44	0	2023-10-20 01:11:44	1	0	2023-10-20 01:11:44	0	113	2023-11-09 18:21:04
1	13652176049	Ludinir Picelli	Ludinir Picelli	0	1	25f9e794323b453885f5181f1b624d0b	7	2	1	12	10	0	2023-10-20 01:11:44	1	2024-04-12 21:11:55.650978	1	0	2023-10-20 01:11:44	0	322	2024-02-05 18:40:30
\.


--
-- Data for Name: usugrupos; Type: TABLE DATA; Schema: cesb; Owner: postgres
--

COPY cesb.usugrupos (id, adm_fl, adm_nome, datacria, ativo) FROM stdin;
1	0	Público	2023-10-06 21:24:56	0
2	1	Convidado	2023-10-06 21:24:56	0
3	2	Registrado	2023-10-06 21:24:56	1
4	3	Gerente	2023-10-06 21:24:56	1
5	4	Administrador	2023-10-06 21:24:56	1
6	5	Checador	2023-10-06 21:24:56	0
7	6	Revisor	2023-10-06 21:24:56	1
8	7	Superusuário	2023-10-06 21:24:56	1
\.


--
-- Name: anivers_id_seq; Type: SEQUENCE SET; Schema: cesb; Owner: postgres
--

SELECT pg_catalog.setval('cesb.anivers_id_seq', 1, false);


--
-- Name: arqitr_iditr_seq; Type: SEQUENCE SET; Schema: cesb; Owner: postgres
--

SELECT pg_catalog.setval('cesb.arqitr_iditr_seq', 1, false);


--
-- Name: arqsetor_codarq_seq; Type: SEQUENCE SET; Schema: cesb; Owner: postgres
--

SELECT pg_catalog.setval('cesb.arqsetor_codarq_seq', 1, false);


--
-- Name: calendev_idev_seq; Type: SEQUENCE SET; Schema: cesb; Owner: postgres
--

SELECT pg_catalog.setval('cesb.calendev_idev_seq', 1, false);


--
-- Name: carousel_codcar_seq; Type: SEQUENCE SET; Schema: cesb; Owner: postgres
--

SELECT pg_catalog.setval('cesb.carousel_codcar_seq', 1, false);


--
-- Name: escolhas_codesc_seq; Type: SEQUENCE SET; Schema: cesb; Owner: postgres
--

SELECT pg_catalog.setval('cesb.escolhas_codesc_seq', 1, false);


--
-- Name: ocorrencias_codocor_seq; Type: SEQUENCE SET; Schema: cesb; Owner: postgres
--

SELECT pg_catalog.setval('cesb.ocorrencias_codocor_seq', 1, false);


--
-- Name: ocorrideogr_codideo_seq; Type: SEQUENCE SET; Schema: cesb; Owner: postgres
--

SELECT pg_catalog.setval('cesb.ocorrideogr_codideo_seq', 1, false);


--
-- Name: paramsis_idpar_seq; Type: SEQUENCE SET; Schema: cesb; Owner: postgres
--

SELECT pg_catalog.setval('cesb.paramsis_idpar_seq', 1, false);


--
-- Name: poslog_id_seq; Type: SEQUENCE SET; Schema: cesb; Owner: postgres
--

SELECT pg_catalog.setval('cesb.poslog_id_seq', 1, false);


--
-- Name: ramais_ext_codtel_seq; Type: SEQUENCE SET; Schema: cesb; Owner: postgres
--

SELECT pg_catalog.setval('cesb.ramais_ext_codtel_seq', 1, false);


--
-- Name: ramais_int_codtel_seq; Type: SEQUENCE SET; Schema: cesb; Owner: postgres
--

SELECT pg_catalog.setval('cesb.ramais_int_codtel_seq', 1, false);


--
-- Name: setores_codset_seq; Type: SEQUENCE SET; Schema: cesb; Owner: postgres
--

SELECT pg_catalog.setval('cesb.setores_codset_seq', 1, false);


--
-- Name: subsetores_codsubset_seq; Type: SEQUENCE SET; Schema: cesb; Owner: postgres
--

SELECT pg_catalog.setval('cesb.subsetores_codsubset_seq', 1, false);


--
-- Name: tarefas_idtar_seq; Type: SEQUENCE SET; Schema: cesb; Owner: postgres
--

SELECT pg_catalog.setval('cesb.tarefas_idtar_seq', 1, false);


--
-- Name: tarefas_msg_idmsg_seq; Type: SEQUENCE SET; Schema: cesb; Owner: postgres
--

SELECT pg_catalog.setval('cesb.tarefas_msg_idmsg_seq', 1, false);


--
-- Name: textopag_codset_seq; Type: SEQUENCE SET; Schema: cesb; Owner: postgres
--

SELECT pg_catalog.setval('cesb.textopag_codset_seq', 1, false);


--
-- Name: trafego_codtraf_seq; Type: SEQUENCE SET; Schema: cesb; Owner: postgres
--

SELECT pg_catalog.setval('cesb.trafego_codtraf_seq', 1, false);


--
-- Name: trocas_idtr_seq; Type: SEQUENCE SET; Schema: cesb; Owner: postgres
--

SELECT pg_catalog.setval('cesb.trocas_idtr_seq', 1, false);


--
-- Name: usuarios_id_seq; Type: SEQUENCE SET; Schema: cesb; Owner: postgres
--

SELECT pg_catalog.setval('cesb.usuarios_id_seq', 1, false);


--
-- Name: usugrupos_id_seq; Type: SEQUENCE SET; Schema: cesb; Owner: postgres
--

SELECT pg_catalog.setval('cesb.usugrupos_id_seq', 1, false);


--
-- Name: anivers anivers_pkey; Type: CONSTRAINT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.anivers
    ADD CONSTRAINT anivers_pkey PRIMARY KEY (id);


--
-- Name: arqitr arqitr_pkey; Type: CONSTRAINT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.arqitr
    ADD CONSTRAINT arqitr_pkey PRIMARY KEY (iditr);


--
-- Name: arqsetor arqsetor_pkey; Type: CONSTRAINT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.arqsetor
    ADD CONSTRAINT arqsetor_pkey PRIMARY KEY (codarq);


--
-- Name: calendev calendev_pkey; Type: CONSTRAINT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.calendev
    ADD CONSTRAINT calendev_pkey PRIMARY KEY (idev);


--
-- Name: carousel carousel_pkey; Type: CONSTRAINT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.carousel
    ADD CONSTRAINT carousel_pkey PRIMARY KEY (codcar);


--
-- Name: escolhas escolhas_pkey; Type: CONSTRAINT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.escolhas
    ADD CONSTRAINT escolhas_pkey PRIMARY KEY (codesc);


--
-- Name: ocorrencias ocorrencias_pkey; Type: CONSTRAINT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.ocorrencias
    ADD CONSTRAINT ocorrencias_pkey PRIMARY KEY (codocor);


--
-- Name: ocorrideogr ocorrideogr_pkey; Type: CONSTRAINT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.ocorrideogr
    ADD CONSTRAINT ocorrideogr_pkey PRIMARY KEY (codideo);


--
-- Name: paramsis paramsis_pkey; Type: CONSTRAINT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.paramsis
    ADD CONSTRAINT paramsis_pkey PRIMARY KEY (idpar);


--
-- Name: poslog poslog_pkey; Type: CONSTRAINT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.poslog
    ADD CONSTRAINT poslog_pkey PRIMARY KEY (id);


--
-- Name: ramais_ext ramais_ext_pkey; Type: CONSTRAINT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.ramais_ext
    ADD CONSTRAINT ramais_ext_pkey PRIMARY KEY (codtel);


--
-- Name: ramais_int ramais_int_pkey; Type: CONSTRAINT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.ramais_int
    ADD CONSTRAINT ramais_int_pkey PRIMARY KEY (codtel);


--
-- Name: setores setores_pkey; Type: CONSTRAINT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.setores
    ADD CONSTRAINT setores_pkey PRIMARY KEY (codset);


--
-- Name: subsetores subsetores_pkey; Type: CONSTRAINT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.subsetores
    ADD CONSTRAINT subsetores_pkey PRIMARY KEY (codsubset);


--
-- Name: tarefas_msg tarefas_msg_pkey; Type: CONSTRAINT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.tarefas_msg
    ADD CONSTRAINT tarefas_msg_pkey PRIMARY KEY (idmsg);


--
-- Name: tarefas tarefas_pkey; Type: CONSTRAINT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.tarefas
    ADD CONSTRAINT tarefas_pkey PRIMARY KEY (idtar);


--
-- Name: textopag textopag_pkey; Type: CONSTRAINT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.textopag
    ADD CONSTRAINT textopag_pkey PRIMARY KEY (codset);


--
-- Name: trafego trafego_pkey; Type: CONSTRAINT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.trafego
    ADD CONSTRAINT trafego_pkey PRIMARY KEY (codtraf);


--
-- Name: trocas trocas_pkey; Type: CONSTRAINT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.trocas
    ADD CONSTRAINT trocas_pkey PRIMARY KEY (idtr);


--
-- Name: usuarios usuarios_pkey; Type: CONSTRAINT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.usuarios
    ADD CONSTRAINT usuarios_pkey PRIMARY KEY (id);


--
-- Name: usugrupos usugrupos_pkey; Type: CONSTRAINT; Schema: cesb; Owner: postgres
--

ALTER TABLE ONLY cesb.usugrupos
    ADD CONSTRAINT usugrupos_pkey PRIMARY KEY (id);


--
-- PostgreSQL database dump complete
--

