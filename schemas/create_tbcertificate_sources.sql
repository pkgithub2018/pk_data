-- SQL script to create tbcertificate_sources table
-- This table stores information about generated PDF certificates

CREATE TABLE tbcertificate_sources (
    id SERIAL PRIMARY KEY,
    application_id INTEGER NOT NULL,
    certificate_id INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    uid VARCHAR(50),
    gid VARCHAR(50),
    filelink VARCHAR(255) NOT NULL,
    enabled VARCHAR(3) DEFAULT 'yes'
);

-- Create indexes for better performance
CREATE INDEX idx_tbcertificate_sources_application_id ON tbcertificate_sources(application_id);
CREATE INDEX idx_tbcertificate_sources_certificate_id ON tbcertificate_sources(certificate_id);
CREATE INDEX idx_tbcertificate_sources_enabled ON tbcertificate_sources(enabled);

-- Add comments for documentation
COMMENT ON TABLE tbcertificate_sources IS 'Stores generated PDF certificate files information';
COMMENT ON COLUMN tbcertificate_sources.id IS 'Auto-increment primary key';
COMMENT ON COLUMN tbcertificate_sources.application_id IS 'Reference to tbapplication table';
COMMENT ON COLUMN tbcertificate_sources.certificate_id IS 'Reference to tbcertificate table';
COMMENT ON COLUMN tbcertificate_sources.created_at IS 'Timestamp when record was created';
COMMENT ON COLUMN tbcertificate_sources.updated_at IS 'Timestamp when record was last updated';
COMMENT ON COLUMN tbcertificate_sources.uid IS 'User ID who generated the PDF';
COMMENT ON COLUMN tbcertificate_sources.gid IS 'Group ID';
COMMENT ON COLUMN tbcertificate_sources.filelink IS 'Path/filename of the generated PDF file';
COMMENT ON COLUMN tbcertificate_sources.enabled IS 'Whether the record is active (yes/no)';