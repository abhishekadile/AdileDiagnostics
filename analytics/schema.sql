-- Create analytics database
CREATE DATABASE IF NOT EXISTS adile_analytics;
USE adile_analytics;

-- Page Views table
CREATE TABLE IF NOT EXISTS page_views (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(64) NOT NULL,
    page_url VARCHAR(255) NOT NULL,
    referrer_url VARCHAR(255),
    user_agent VARCHAR(255),
    ip_address VARCHAR(45),
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    time_on_page INT,
    bounce BOOLEAN DEFAULT FALSE,
    INDEX idx_session (session_id),
    INDEX idx_timestamp (timestamp)
);

-- User Events table
CREATE TABLE IF NOT EXISTS user_events (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(64) NOT NULL,
    event_type VARCHAR(50) NOT NULL,
    event_category VARCHAR(50),
    event_action VARCHAR(50),
    event_label VARCHAR(255),
    event_value INT,
    page_url VARCHAR(255),
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_session (session_id),
    INDEX idx_event_type (event_type),
    INDEX idx_timestamp (timestamp)
);

-- User Sessions table
CREATE TABLE IF NOT EXISTS user_sessions (
    session_id VARCHAR(64) PRIMARY KEY,
    user_id VARCHAR(64),
    start_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    end_time DATETIME,
    device_type VARCHAR(50),
    browser VARCHAR(50),
    os VARCHAR(50),
    country VARCHAR(2),
    city VARCHAR(100),
    total_pageviews INT DEFAULT 0,
    total_events INT DEFAULT 0,
    INDEX idx_user (user_id),
    INDEX idx_start_time (start_time)
);

-- Form Submissions table
CREATE TABLE IF NOT EXISTS form_submissions (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(64) NOT NULL,
    form_type VARCHAR(50) NOT NULL,
    form_data JSON,
    success BOOLEAN,
    error_message TEXT,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_session (session_id),
    INDEX idx_form_type (form_type),
    INDEX idx_timestamp (timestamp)
);

-- User Behavior table
CREATE TABLE IF NOT EXISTS user_behavior (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(64) NOT NULL,
    behavior_type VARCHAR(50) NOT NULL,
    element_id VARCHAR(255),
    element_class VARCHAR(255),
    element_text TEXT,
    page_url VARCHAR(255),
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    additional_data JSON,
    INDEX idx_session (session_id),
    INDEX idx_behavior_type (behavior_type),
    INDEX idx_timestamp (timestamp)
); 