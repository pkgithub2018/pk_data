CREATE TABLE IF NOT EXISTS "public"."tbapplication" (
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