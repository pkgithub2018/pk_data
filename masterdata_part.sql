CREATE TABLE IF NOT EXISTS "public"."tbapplication" ( /* DONE IN CLOUD SERVER */
    /* id and uid will be added first, and then update the rest. So, set empty fields for now */
    id SERIAL PRIMARY KEY,
    uid INTEGER NOT NULL, /* user_id from tbusers */
    application_no TEXT, /* Application ID=00000 + id + last two digits of year + 2 digits of province */
    application_date date,
    company_id INTEGER,
    reg_no TEXT, /* Registration number of application */
    export_point INTEGER, /* borderpoint_id from tblocations */
    contact_person TEXT, /* Name of the contact person from tbentity_exporter */
    address_person TEXT,
    phone TEXT,
    country_import INTEGER, /* country_id */
    import_point INTEGER, /* borderpoint_id from tblocations */
    certificate_type TEXT, /* For export OR transit */
    multi_item TEXT, /* Yes and no */
    print_support TEXT, /* Yes and no */
    commodity_id INTEGER, /* product_id from tbproduct */
    name_oncertificate TEXT, /* Name on certificate */
    name_scientific TEXT, /* Scientific name of the product */
    commodity_description TEXT, /* Description of the product */
    quantity_net REAL, /* Net quantity of the product */
    quantity_gross REAL, /* Gross quantity of the product */
    unit_id INTEGER, /* prounit from tbproduct_unit */
    marks_item TEXT, /* Marks and numbers of the product */
    place_origin INTEGER, /* Place of origin of the product- country_id from tbcountry */
    conveyance_id INTEGER, /* conveyance from tbconveyance */
    conveyance_sign TEXT, /* Conveyance sign */
    address_exporter TEXT, /* Address of the exporter- it would be from tbentity_export */
    address_importer TEXT, /* Address of the importer - it would be from tbentity_importer */
    purpose TEXT, /* Purpose of the product */
    place_quarantine INTEGER, /* Place of quarantine - borderpoint_id from tblocations */
    place_treatment INTEGER, /* Place of treatment - borderpoint_id from tblocations */
    date_certificate date, /* Date of certificate */
    guid INTEGER, /* Guid from tbusers */
    place_quarantine_other TEXT, /* If other, please specify */
    place_treatment_other TEXT /* If other, please specify */
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