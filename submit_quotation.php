<?php
// Set headers to allow CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database connection parameters
$host = 'localhost';
$dbname = 'admin_db';
$username = 'root';
$password = '';

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Handle POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get form data
        $to = $_POST['to'] ?? '';
        $city = $_POST['city'] ?? '';
        $startDate = $_POST['start-date'] ?? '';
        $duration = $_POST['duration'] ?? '';
        $months = $_POST['months'] ?? '';
        $total = $_POST['total'] ?? '';

        // Validate required fields
        if (empty($to) || empty($city) || empty($startDate) || 
            empty($duration) || empty($months) || empty($total)) {
            throw new Exception("All fields are required");
        }

        // Prepare SQL statement
        $stmt = $pdo->prepare("
            INSERT INTO quotations (
                to_name, 
                city, 
                start_date, 
                duration, 
                months, 
                total
            ) VALUES (?, ?, ?, ?, ?, ?)
        ");

        // Execute with parameters
        $stmt->execute([
            $to,
            $city,
            $startDate,
            $duration,
            $months,
            $total
        ]);

        // Send success response
        echo json_encode([
            'success' => true,
            'message' => 'Quotation saved successfully'
        ]);

    } else {
        throw new Exception("Invalid request method");
    }

} catch (PDOException $e) {
    // Handle database errors
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    // Handle other errors
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>