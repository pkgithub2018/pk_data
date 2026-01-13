
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

CREATE TABLE IF NOT EXISTS "public"."tbprofile" (
    "id" SERIAL PRIMARY KEY,
    "uid" INTEGER NOT NULL, /* user id - foreign key will be added later */
    "description" TEXT,  /* Profile description */
    "address" TEXT,
    "twitter" TEXT, /* Twitter handle */
    "facebook" TEXT, /* Facebook handle */
    "linkedin" TEXT, /* LinkedIn handle */
    "instagram" TEXT, /* Instagram handle */
    "imgfilename" TEXT, /* Image filename */
    "imgfilepath" TEXT /* Image file path */
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

CREATE TABLE IF NOT EXISTS "public"."tbdistricts" (
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

CREATE TABLE IF NOT EXISTS "public"."tbentity_export" (
    id SERIAL PRIMARY KEY,
    business_type INTEGER NOT NULL,
    entity_type INTEGER NOT NULL,
    title text NOT NULL, /* Company name */
    address text NOT NULL, /* Address of the company */
    zipcode text NOT NULL, /* Zip code */ 
    province text NOT NULL, /* province_id from tbprovinces */
    district text NOT NULL, /* district_id from tbdistricts */
    country_id INTEGER NOT NULL,
    phone text,
    email text, /* Email of the company */
    contact_name text NOT NULL, /* Contact person name */
    registered text NOT NULL, /* yes/no */
    registered_date_from date, /* Date of registration */
    registered_date_to date, /* Date of registration */
    check_list_registered text, /* yes/no - Checklist registered */
    license_export text NOT NULL, /* yes/no */
    gap text NOT NULL, /* yes/no - Good Agricultural Practices */
    datetime_created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, /* Date and time of creation */
    created_guid INTEGER NOT NULL /* Group ID of the user who created the record - auto assign as user created */
   /* uid INTEGER NOT NULL,  User ID who created the record 
    guid INTEGER NOT NULL  Guid from tbusers- No NEED because data on exporters and importers need to be shared */
)

CREATE TABLE IF NOT EXISTS "public"."tbentity_import" (
    id SERIAL PRIMARY KEY,
    business_type INTEGER NOT NULL,
    entity_type INTEGER NOT NULL,
    title text NOT NULL, /* Company name */
    address text NOT NULL, /* Address of the company */
    zipcode text NOT NULL, /* Zip code */ 
    province text NOT NULL, /* Province's name */
    district text NOT NULL, /* District/City's name */
    country_id INTEGER NOT NULL,
    phone text,
    email text, /* Email of the company */
    contact_name text, /* Contact person name */
    datetime_created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, /* Date and time of creation */
    created_guid INTEGER NOT NULL /* Group ID of the user who created the record - auto assign as user created */
)

CREATE TABLE IF NOT EXISTS "public"."tbinspection" (
    id SERIAL PRIMARY KEY,
    application_id INTEGER NOT NULL, /* application_id - auto increment from tbapplication */
    /* inspection_type INTEGER NOT NULL, inspection_method */
    inspection_date date NOT NULL,
    sample_no text, /* sample number */
    sample_quantity real NOT NULL, /* Quantity of sample */
    unit_id INTEGER NOT NULL, /* Unit ID from tbunits */
    sample_collected_by text, /* User ID who collected the sample */
    inspected_by text, /* User ID who screened the sample */
    certificate_fee real NOT NULL, /* Inspection fee */
    receipt_no text NOT NULL, /* Receipt number */
    lot_number text NOT NULL, /* Lot number */
    inspection_method INTEGER NOT NULL, /* inspection_method ID*/
    pest_detected text NOT NULL, /* yes/no */
    treat_ability text NOT NULL, /* yes/no */
    lab_required text NOT NULL, /* yes/no */
    treatment_method INTEGER NOT NULL, /* treatment_method ID */
    treatment_date date NOT NULL, /* Date of treatment */
    chemical_used text NOT NULL, /* Chemical used for treatment */
    chemical_fortreat text NOT NULL, /* Name of chemical treatment */
    duration_temp text NOT NULL, /* Duration and temperature of treatment */
    concentration text NOT NULL, /* Concentration of chemical used */
    sample_inspectedby text NOT NULL, /* inspection for treatment */
    additonal_info text, /* Additional information */
    treatment_reason text NOT NULL, /* Reason for using chemical */
    post_treatment_details text NOT NULL, /* Details of post treatment, if any */
    enabled text NOT NULL /* yes/no */
)


CREATE TABLE IF NOT EXISTS "public"."tbsample" (
    id SERIAL PRIMARY KEY,
    inspection_id INTEGER NOT NULL,
    sample_type INTEGER NOT NULL, /* ID from tbsample_type */
    code text NOT NULL, /* receipt number */
    collected_by text NOT NULL /* User ID-Person who collects sample */,
    screening_date date NOT NULL,
    product_id INTEGER NOT NULL, /* product_id from tbproduct */
    sample_qty  real NOT NULL, /* Quantity of sample collected */
    sample_unit INTEGER NOT NULL, /* gram, kg, litre, piece, package, box */
    screened_by INTEGER NOT NULL, /* User ID-Person who screens sample */
    pest_id INTEGER NOT NULL, /* pest_id from tbpest */
    picture_file_id text NOT NULL, /* Picture file ID from tbuploads */
    screening_result INTEGER NOT NULL, /* description of the screening result */
    compliant text NOT NULL, /* pass/fail */
    pest_type INTEGER NOT NULL, /* ID from tbpest_type */
    certificate_id INTEGER NOT NULL, /* certificate_id from tbcertificate */
    sample_location_id INTEGER NOT NULL, /* Alternate location ID from tblocations */
    internal_note text, /* Internal notes for sample */
    datetime_created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, /* Date and time of creation */
    updated_by INTEGER NOT NULL, /* User ID who updated the record */
    enabled text NOT NULL /* yes/no */
)

CREATE TABLE IF NOT EXISTS "public"."tbcertificate" (
    id SERIAL PRIMARY KEY, /* Auto increment - certificate_id */
    application_id INTEGER NOT NULL, /* application_id, NOT application_no - auto increment from tbapplication - K */
    certificate_no TEXT NOT NULL, /* Certificate No : 000001 based on id-autoincrement - Updated after id is created */
    carbonpaper_id TEXT NOT NULL, /* ID from carbon paper - K */
    approved_by INTEGER NOT NULL, /* User ID who approved/signed the certificate */
    position_approved text NOT NULL, /* Position of the approver */
    place_issued text NOT NULL, /* Place where certificate is issued */
    consignment_value real NOT NULL, /* Value of the consignment */
    value_currency text NOT NULL, /* Currency of the consignment value */
    additional_scientificname text, /* Additional scientific name if any */
    additional_declaration text, /* Additional declaration if any */
    datetime_created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, /* Date and time of creation */
    datetime_updated timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, /* Date and time of last update */
    created_uid INTEGER NOT NULL, /* User ID who created the record */
    updated_uid INTEGER NOT NULL, /* User ID who updated the record */
    gid INTEGER NOT NULL, /* Group ID of the user who created the record - auto assign as user created */
    date_issued date NOT NULL, /* Date of issue */
    certificate_status text NOT NULL, /* Status of the certificate: tbstatus - 1=issued, 2=cancelled, 3=amended, 4= printed, 5=ongoing */
    enabled text NOT NULL /* yes/no */
)

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

CREATE TABLE IF NOT EXISTS "public"."tbpurpose" (
    id SERIAL PRIMARY KEY,
    title text NOT NULL 
)

CREATE TABLE IF NOT EXISTS "public"."tbcurrency" (
    id SERIAL PRIMARY KEY,
    country text NOT NULL, 
    currency text NOT NULL, 
    code text NOT NULL /* USD, LAK, THB, VND, CNY, EUR, GBP, JPY, KRW, AUD, CAD, CHF, HKD, SGD, MYR, IDR */
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
);


CREATE TABLE IF NOT EXISTS "public"."tblabresults" (
    id SERIAL PRIMARY KEY,
    sample_id INTEGER NOT NULL,
    lab_date date NOT NULL,
    method INTEGER NOT NULL, /* 1=hot water, 2=steam, 3=chemical, 4=other */
    confirm_pest INTEGER NOT NULL, /* 1=Yes, 2=No */
    treat_ability INTEGER NOT NULL, /* 1=Yes, 2=No */
    result_needs INTEGER NOT NULL, /* 1=Yes, 2=No */
    result_descption text NOT NULL
);

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

CREATE TABLE IF NOT EXISTS "public"."tbapprovers" (
    id SERIAL PRIMARY KEY,
    nameap text NOT NULL,
    surname text NULL,
    roles text NOT NULL, /* Officer, Director */
    position text NOT NULL, /* 1=Yes, 2=No */
    workplace text NOT NULL, /* DOA, PAF */
    uid INTEGER NOT NULL, /* login-user: approvers are not users- user_id from tbusers */
    gid INTEGER NOT NULL, /* group_id from tbusergroup */
    enabled text NOT NULL /* yes/no */
)

CREATE TABLE IF NOT EXISTS "public"."tbpest" (
    id SERIAL PRIMARY KEY,
    pestname text NULL, /* Common name of the pest - Lao name */
    scientificname text NULL,
    category text NULL 
)

CREATE TABLE IF NOT EXISTS "public"."tbpest_detected" (
    id SERIAL PRIMARY KEY,
    application_id INTEGER NOT NULL, /* application_id - auto increment from tbapplication */
    pestid INTEGER NOT NULL, /* ID from tbpest to get Name, general name and specific name  */
    infestation_level text NOT NULL, /* 1=Low, 2=Medium, 3=High */
    alive_status text NOT NULL, /* 1=Alive, 2=Dead */
    risk_category text NOT NULL, /* Quarantine pest, Regulated None Quarantine pest, None Quarantine pest */
    result_measure text NOT NULL /* Immediately implement the treatment as specified, Regulated article was not accordance. Return to the original place */
)

CREATE TABLE IF NOT EXISTS "public"."tbmultiple_product" (
    id SERIAL PRIMARY KEY,
    application_id INTEGER NOT NULL, /* application_id - auto increment from tbapplication */
    product_id INTEGER NOT NULL, /* ID from tbproduct */
    number_description text NULL,
    quantity_net real NOT NULL,
    quantity_gross real NOT NULL,
    unit_id INTEGER NOT NULL /* product_unit */ 
)

CREATE TABLE IF NOT EXISTS "public"."tbcertificate_sources" (
    id SERIAL PRIMARY KEY,
    application_id INTEGER NOT NULL,
    certificate_id INTEGER NOT NULL,
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, /* Date and time of creation */
    updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, /* Date and time of last update */
    uid INTEGER NOT NULL, /* user_id from tbusers */
    gid INTEGER NOT NULL, /* group_id from tbusergroup */
    filelink text NOT NULL, /* Link to the source file */
    enabled text NOT NULL /* yes/no */
);

CREATE TABLE IF NOT EXISTS "public"."tbcertificate_print_log" (
    id SERIAL PRIMARY KEY,
    application_id INTEGER NOT NULL, 
    certificate_id  INTEGER NOT NULL,
    current_status  TEXT NOT NULL, /*get data from tbcertificate (Register) and print_log*/
    /* 1=ongoing if data is being processed- added into tbcertificate, 
    2=issued/printed- if button: Print Certificate is clicked, 
    3=amended/edited if button: Update on the form is clicked and carbon paper is updated,
     4=cancelled/rejected */
    current_carbonpaper_id TEXT NULL, /* ID from carbon paper - K */
    original_carbonpaper_id TEXT NULL, /* ID from carbon paper - K */
    updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, /* Date and time of last update */
    updated_by INTEGER NOT NULL /* User ID who updated the record */
);

CREATE TABLE IF NOT EXISTS "public"."tbcertificate_qr" (
    id SERIAL PRIMARY KEY,
    certificate_id INTEGER NOT NULL, 
    application_id INTEGER NOT NULL,
    qr_code_data TEXT NOT NULL, /* Data encoded in QR (e.g., URL or JSON) */
    qr_code_image TEXT, /* Base64 encoded image or file path */
    qr_format VARCHAR(10) DEFAULT 'PNG', /* Image format: PNG, SVG, etc. */
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP   
);



