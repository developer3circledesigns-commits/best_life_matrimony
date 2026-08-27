<?php
/**
 * Central Authorization Layer
 * 
 * This file provides unified authentication and authorization functions
 * to be used across all pages and API endpoints.
 */

/**
 * Require user to be logged in. Redirects to login if not authenticated.
 * Use at the top of any page that requires authentication.
 */
function require_login(): void {
    if (!isset($_SESSION['user_id']) || !$_SESSION['user_id']) {
        header('Location: ./login.php');
        exit;
    }
}

/**
 * Verify CSRF token for POST requests.
 * Returns true if valid, false otherwise.
 * Call this before processing any state-changing operation.
 */
function verify_csrf(): bool {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        return true; // CSRF not needed for GET requests
    }
    
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    return hash_equals(csrf_token(), $token);
}

/**
 * Require valid CSRF token. Exits with 403 if invalid.
 * Use for API endpoints and form handlers.
 */
function require_csrf(): void {
    if (!verify_csrf()) {
        http_response_code(403);
        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => 'Invalid request']);
        } else {
            echo 'Invalid request. Please try again.';
        }
        exit;
    }
}

/**
 * Require POST method for state-changing operations.
 * Exits with 405 if not POST.
 */
function require_post(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
        } else {
            echo 'Method not allowed';
        }
        exit;
    }
}

/**
 * Check if current user owns a record.
 * @param int $recordOwnerId The user_id of the record owner
 * @return bool True if current user owns the record or is admin
 */
function is_owner(int $recordOwnerId): bool {
    $currentUserId = $_SESSION['user_id'] ?? null;
    if (!$currentUserId) {
        return false;
    }
    // Admins can access any record
    if (is_admin()) {
        return true;
    }
    return (int)$currentUserId === (int)$recordOwnerId;
}

/**
 * Require user to own a record. Exits with 403 if not owner.
 * @param int $recordOwnerId The user_id of the record owner
 */
function require_ownership(int $recordOwnerId): void {
    if (!is_owner($recordOwnerId)) {
        http_response_code(403);
        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
            header('Content-Type: application/json');
            echo json_encode(['ok' => false, 'error' => 'Access denied']);
        } else {
            echo 'Access denied';
        }
        exit;
    }
}
