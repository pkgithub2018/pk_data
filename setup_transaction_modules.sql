-- Setup Transaction Module Permissions
-- This script creates the necessary modules and permissions for the transaction.php functionality

-- First, check and insert modules if they don't exist
INSERT INTO tbmodules (code, title, "desc", enabled)
SELECT 'APP-ENTITY', 'Application Processing', 'Application data entry and management', 'yes'
WHERE NOT EXISTS (SELECT 1 FROM tbmodules WHERE code = 'APP-ENTITY');

INSERT INTO tbmodules (code, title, "desc", enabled)
SELECT 'APP-INSPECT', 'Inspection Processing', 'Inspection data entry and management', 'yes'
WHERE NOT EXISTS (SELECT 1 FROM tbmodules WHERE code = 'APP-INSPECT');

INSERT INTO tbmodules (code, title, "desc", enabled)
SELECT 'APP-CERT', 'Certificate Issuance', 'Certificate processing and issuance', 'yes'
WHERE NOT EXISTS (SELECT 1 FROM tbmodules WHERE code = 'APP-CERT');

-- Get the module IDs
DO $$
DECLARE
    mid_entity INTEGER;
    mid_inspect INTEGER;
    mid_cert INTEGER;
    gid_dataoffice INTEGER;
BEGIN
    -- Get module IDs
    SELECT id INTO mid_entity FROM tbmodules WHERE code = 'APP-ENTITY';
    SELECT id INTO mid_inspect FROM tbmodules WHERE code = 'APP-INSPECT';
    SELECT id INTO mid_cert FROM tbmodules WHERE code = 'APP-CERT';
    
    -- Get Data Officer group ID (adjust group name if different)
    SELECT id INTO gid_dataoffice FROM tbusergroup WHERE LOWER(title) LIKE '%data%officer%' OR id = 19 LIMIT 1;
    
    -- If group not found, use group 19 directly
    IF gid_dataoffice IS NULL THEN
        gid_dataoffice := 19;
    END IF;
    
    -- Insert permissions for Data Officer group for APP-ENTITY
    INSERT INTO tbgrouppermits (gid, mid, pread, padd, pupdate, pdelete)
    SELECT gid_dataoffice, mid_entity, 'yes', 'yes', 'yes', 'no'
    WHERE NOT EXISTS (
        SELECT 1 FROM tbgrouppermits 
        WHERE gid = gid_dataoffice AND mid = mid_entity
    );
    
    -- Insert permissions for Data Officer group for APP-INSPECT
    INSERT INTO tbgrouppermits (gid, mid, pread, padd, pupdate, pdelete)
    SELECT gid_dataoffice, mid_inspect, 'yes', 'yes', 'yes', 'no'
    WHERE NOT EXISTS (
        SELECT 1 FROM tbgrouppermits 
        WHERE gid = gid_dataoffice AND mid = mid_inspect
    );
    
    -- Insert permissions for Data Officer group for APP-CERT
    INSERT INTO tbgrouppermits (gid, mid, pread, padd, pupdate, pdelete)
    SELECT gid_dataoffice, mid_cert, 'yes', 'yes', 'yes', 'no'
    WHERE NOT EXISTS (
        SELECT 1 FROM tbgrouppermits 
        WHERE gid = gid_dataoffice AND mid = mid_cert
    );
    
    RAISE NOTICE 'Permissions setup completed for group %', gid_dataoffice;
END $$;

-- Verify the setup
SELECT 'Modules Created:' as status;
SELECT id, code, title, enabled FROM tbmodules WHERE code IN ('APP-ENTITY', 'APP-INSPECT', 'APP-CERT');

SELECT 'Permissions for Group 19:' as status;
SELECT m.code, m.title, gp.pread, gp.padd, gp.pupdate, gp.pdelete
FROM tbgrouppermits gp
INNER JOIN tbmodules m ON m.id = gp.mid
WHERE gp.gid = 19
AND m.code IN ('APP-ENTITY', 'APP-INSPECT', 'APP-CERT')
ORDER BY m.code;
