-- =============================================================================
-- Krayin CRM - MySQL Initialization Script
-- This script runs automatically when the MySQL container starts for the first time
-- =============================================================================

-- Set character set and collation
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Create database if not exists (backup in case env vars fail)
CREATE DATABASE IF NOT EXISTS `krayin`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

-- Grant privileges to application user
GRANT ALL PRIVILEGES ON `krayin`.* TO 'krayin'@'%';
FLUSH PRIVILEGES;

-- Log successful initialization
SELECT 'Krayin CRM database initialized successfully' AS status;
