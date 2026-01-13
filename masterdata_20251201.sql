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
