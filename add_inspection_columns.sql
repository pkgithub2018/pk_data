-- Migration script to add missing columns to tbinspection table
-- Date: 2026-06-01
-- Purpose: Add additional_info, treatment_reason, and post_treatment_details columns

-- Add additonal_info column (note: keeping the typo to match local schema)
ALTER TABLE tbinspection 
ADD COLUMN IF NOT EXISTS additonal_info text;

-- Add treatment_reason column
ALTER TABLE tbinspection 
ADD COLUMN IF NOT EXISTS treatment_reason text NOT NULL DEFAULT '';

-- Add post_treatment_details column
ALTER TABLE tbinspection 
ADD COLUMN IF NOT EXISTS post_treatment_details text NOT NULL DEFAULT '';

-- Update the comment for clarity
COMMENT ON COLUMN tbinspection.additonal_info IS 'Additional information (note: column name has typo)';
COMMENT ON COLUMN tbinspection.treatment_reason IS 'Reason for using chemical treatment';
COMMENT ON COLUMN tbinspection.post_treatment_details IS 'Details of post treatment, if any';
