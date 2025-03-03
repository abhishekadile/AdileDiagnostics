<?php
require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid data format']);
    exit;
}

$conn = getDBConnection();
if (!$conn) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Process different types of analytics data
switch ($data['type']) {
    case 'pageview':
        trackPageView($conn, $data);
        break;
    case 'event':
        trackEvent($conn, $data);
        break;
    case 'behavior':
        trackBehavior($conn, $data);
        break;
    case 'form':
        trackFormSubmission($conn, $data);
        break;
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid tracking type']);
        exit;
}

function trackPageView($conn, $data) {
    try {
        $stmt = $conn->prepare("
            INSERT INTO page_views 
            (session_id, page_url, referrer_url, user_agent, ip_address)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['sessionId'],
            $data['pageUrl'],
            $data['referrerUrl'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null,
            $_SERVER['REMOTE_ADDR']
        ]);
        
        echo json_encode(['success' => true, 'id' => $conn->lastInsertId()]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to track pageview']);
        error_log($e->getMessage());
    }
}

function trackEvent($conn, $data) {
    try {
        $stmt = $conn->prepare("
            INSERT INTO user_events 
            (session_id, event_type, event_category, event_action, event_label, event_value, page_url)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['sessionId'],
            $data['eventType'],
            $data['category'] ?? null,
            $data['action'] ?? null,
            $data['label'] ?? null,
            $data['value'] ?? null,
            $data['pageUrl']
        ]);
        
        echo json_encode(['success' => true, 'id' => $conn->lastInsertId()]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to track event']);
        error_log($e->getMessage());
    }
}

function trackBehavior($conn, $data) {
    try {
        $stmt = $conn->prepare("
            INSERT INTO user_behavior 
            (session_id, behavior_type, element_id, element_class, element_text, page_url, additional_data)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['sessionId'],
            $data['behaviorType'],
            $data['elementId'] ?? null,
            $data['elementClass'] ?? null,
            $data['elementText'] ?? null,
            $data['pageUrl'],
            json_encode($data['additionalData'] ?? null)
        ]);
        
        echo json_encode(['success' => true, 'id' => $conn->lastInsertId()]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to track behavior']);
        error_log($e->getMessage());
    }
}

function trackFormSubmission($conn, $data) {
    try {
        $stmt = $conn->prepare("
            INSERT INTO form_submissions 
            (session_id, form_type, form_data, success, error_message)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $data['sessionId'],
            $data['formType'],
            json_encode($data['formData']),
            $data['success'] ?? true,
            $data['errorMessage'] ?? null
        ]);
        
        echo json_encode(['success' => true, 'id' => $conn->lastInsertId()]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to track form submission']);
        error_log($e->getMessage());
    }
}
?> 