
CREATE TABLE IF NOT EXISTS "public"."tbusers" (
    id SERIAL PRIMARY KEY,
    name text NOT NULL,
    surname text,
    sex text NOT NULL, /* m-male and f-female */
    psw text NOT NULL, /* encrypted password - In PostgreSQL, need to CREATE EXTENSION IF NOT EXISTS pgcrypto; */
    position text,
    unit text, /* workplace: 1-rootuser: DOA, 2-Provincial users:, 3-Viewuser: Borderpoints */
    phone text,
    email text, /* used as username: email should be unique */
    last_login timestamp,
    group_id INTEGER NOT NULL, /* 1=admin, 2=provincial, 3=borderpoint, 4=lab, 5=other */
    group_admin text NOT NULL, /* yes/no */
    location_id INTEGER NOT NULL, /* borderpoint_id */
    enabled text NOT NULL /* yes/no */
);

CREATE TABLE IF NOT EXISTS "public"."tbusergroup" (
    "id" SERIAL PRIMARY KEY,
    "title" TEXT NOT NULL,  /* 1=admin, 2=outdomxay, 3=borten border, 4=lab, 5=other */
    "desc" TEXT,
    enabled text NOT NULL /* yes/no */
);

CREATE TABLE IF NOT EXISTS "public"."tbgrouppermits" (
    "id" SERIAL PRIMARY KEY,
    "gid" INTEGER NOT NULL,  /* Group id - foreign key will be added later*/
    "mid" INTEGER NOT NULL,  /* module id - foreign key */
    "pread" TEXT NOT NULL,  /* yes/no */
    "padd" TEXT NOT NULL, /* yes/no */ 
    "pupdate" TEXT NOT NULL, /* yes/no */
    "pdelete" TEXT NOT NULL, /* yes/no */
    CONSTRAINT fk_gid FOREIGN KEY ("gid") REFERENCES "public"."tbusergroup"("id"),
    CONSTRAINT fk_mid FOREIGN KEY ("mid") REFERENCES "public"."tbmodules"("id")
);

CREATE TABLE IF NOT EXISTS "public"."tbmodules" (
    "id" SERIAL PRIMARY KEY,
    "code" TEXT NOT NULL,  
    "name" TEXT NOT NULL,  
    "desc" TEXT,
    enabled text NOT NULL /* yes/no */
);

CREATE TABLE IF NOT EXISTS "public"."tblocations" (
    "id" SERIAL PRIMARY KEY,
    "lid" TEXT NOT NULL,
    "name_eng" TEXT NOT NULL,
    "name_lao" TEXT NOT NULL,
    "location_type" TEXT NOT NULL, /* 1=International, 2=local, 3=traditional  */
    "pid" TEXT NOT NULL,
    "did" TEXT NOT NULL,
    CONSTRAINT fk_pid FOREIGN KEY ("pid") REFERENCES "public"."tbprovinces"("id"),
    CONSTRAINT fk_did FOREIGN KEY ("did") REFERENCES "public"."tbdistricts"("id")
);

CREATE TABLE IF NOT EXISTS "sch_ephyto"."tbbordertype" (
    "id" SERIAL PRIMARY KEY,
    "bordertype" TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS "sch_ephyto"."tbdistricts" (
    "id" SERIAL PRIMARY KEY,
    "pid" TEXT NOT NULL,
    "dname" TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS "public"."tbproduct" (
    id SERIAL PRIMARY KEY,
    code text NOT NULL,
    name text NOT NULL,
    name_scientific text NOT NULL, -- scientific name
    description text, -- was "desc" - avoid using reserved keywords
    hscode text, -- Harmonized System Code
    productgroup text, -- 1=plant, 2=animal, 3=other
    enabled text NOT NULL -- yes/no
);

CREATE TABLE IF NOT EXISTS "public"."tbproduct_group" (
    id SERIAL PRIMARY KEY,
    code text NOT NULL,
    title text NOT NULL,
    description text, -- was "desc" - avoid using reserved keywords
    enabled text NOT NULL -- yes/no
);

CREATE TABLE IF NOT EXISTS "public"."tbinspection_method" (
    id SERIAL PRIMARY KEY,
    code TEXT NOT NULL,
    title TEXT NOT NULL, 
    description TEXT NULL, 
    enabled TEXT NOT NULL -- yes/no 
);

CREATE TABLE IF NOT EXISTS "public"."tbtreatment_method" (
    id SERIAL PRIMARY KEY,
    code TEXT NOT NULL,
    title TEXT NOT NULL, 
    description TEXT NULL, 
    enabled TEXT NOT NULL -- yes/no 
);

CREATE TABLE IF NOT EXISTS "public"."entity_type" (
    id SERIAL PRIMARY KEY,
    code TEXT NOT NULL,
    title TEXT NOT NULL, 
    description TEXT NULL, 
    enabled TEXT NOT NULL -- yes/no 
);

CREATE TABLE IF NOT EXISTS "public"."tbcountry" (
   id SERIAL PRIMARY KEY,
    country_eng text NOT NULL,
    country_lao text NOT NULL,
    continent text NOT NULL,
    region text)

CREATE TABLE IF NOT EXISTS "public"."tbrequest" (
   id SERIAL PRIMARY KEY,
   request_id INTEGER NOT NULL, /* 000000 + id + / date/location, Example, 000001/20250417/DOA */
   request_date date NOT NULL,
   name text NOT NULL,
   surname text NOT NULL,   
   phone text NOT NULL,
   address text,
   request_type INTEGER NOT NULL, /* 1=import, 2=export, 3=transit (Checkbox) */
   location_id INTEGER NOT NULL, /* borderpoint_id where product passess in/out from tbloaction */
   country_id INTEGER NOT NULL /* country_id from tbcountry */
   )

CREATE TABLE IF NOT EXISTS "public"."tbcompany" (
    id SERIAL PRIMARY KEY,
    cname_eng text NOT NULL,
    cname_lao text NOT NULL,
    country_id INTEGER NOT NULL,
    province INTEGER NOT NULL,
    district text NOT NULL, 
    address text,  
    phone text,
    email text)


CREATE TABLE IF NOT EXISTS "public"."tbproduct_unit" (
    id SERIAL PRIMARY KEY,
    prounit text NOT NULL /* 1=kg, 2=ton, 3=litre, 4=piece, 5=package, 6=box, 7=other */
)

CREATE TABLE IF NOT EXISTS "public"."tbconveyance" (
    id SERIAL PRIMARY KEY,
    code text NOT NULL, 
    conveytype text NOT NULL, /* 1= byland (truck and train), 2=bysea, 3=byair */
    description text, /* was "desc" - avoid using reserved keywords */
    enabled text NOT NULL /* yes/no */
)

CREATE TABLE IF NOT EXISTS "public"."tbtransaction" (
    id SERIAL PRIMARY KEY,
    uid INTEGER NOT NULL, /* user_id from tbusers */
    request_id INTEGER NOT NULL, /* request_id from tbrequestor */
    transation_date date NOT NULL,
    company_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    quantity INTEGER NOT NULL,
    punit_id INTEGER NOT NULL, /* product_unit */
    conveyance_id INTEGER NOT NULL,
    conveyance_sign text NOT NULL,
    location_id INTEGER NOT NULL, /* borderpoint_id */
    tbtransaction_type INTEGER NOT NULL, /* 1=import, 2=export */
    destination_id INTEGER NOT NULL /* company_id */
)



CREATE TABLE IF NOT EXISTS "public"."tbsamples" (
    id SERIAL PRIMARY KEY,
    transaction_id INTEGER NOT NULL,
    collect_date date NOT NULL, 
    quantity real NOT NULL,
    sample_unit INTEGER NOT NULL, /* gram, kg, litre, piece, package, box */
    collected_by text NOT NULL /* Person who collects sample */
)

CREATE TABLE IF NOT EXISTS "public"."tblabresults" (
    id SERIAL PRIMARY KEY,
    sample_id INTEGER NOT NULL,
    lab_date date NOT NULL,
    method INTEGER NOT NULL, /* 1=hot water, 2=steam, 3=chemical, 4=other */
    confirm_pest INTEGER NOT NULL, /* 1=Yes, 2=No */
    treat_ability INTEGER NOT NULL, /* 1=Yes, 2=No */
    result_needs INTEGER NOT NULL, /* 1=Yes, 2=No */
    result_descption text NOT NULL
)

/* if chemical method, then chemical_id is needed */
CREATE TABLE IF NOT EXISTS "public"."tbmethod_chemical" (
    id SERIAL PRIMARY KEY,
    sample_id INTEGER NOT NULL,
    chemical_name INTEGER NOT NULL, /* CH2Br,... */
    treated_by INTEGER NOT NULL, /* Fumigation, ... */
    duration_temp INTEGER NOT NULL, /* 1=75 - 12 Hours, 2 = ... */
    concentration INTEGER NOT NULL, /* 1=125mg/m2, 2=... */
    chemical_reason text NOT NULL,
    post_treatment text NOT NULL, /* Details of post treatment, if any */
    chinspected_by text NOT NULL /* Person who inspects sample for chemical purpose */
)

