INSERT INTO "public"."tbusers" (
    name, 
    surname, 
    sex, 
    psw, 
    position, 
    unit, 
    phone, 
    email, 
    last_login, 
    group_id, 
    group_admin, 
    location_id, 
    enabled
) VALUES (
    'John', 
    'Doe', 
    'Male', 
    crypt('password123', gen_salt('bf')), -- Encrypt the password using bcrypt
    'Manager', 
    'DOA', 
    '1234567890', 
    'john.doe@example.com', 
    '2025-05-11 10:00:00', 
    1, 
    'yes', 
    101, 
    'yes'
);