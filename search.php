<?php
require 'db_connection.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$place = isset($input['place']) ? strtolower(trim($input['place'])) : '';

if (empty($place)) {
    echo json_encode(['success' => false, 'message' => 'Please provide a search term.']);
    exit();
}

$stmt = $conn->prepare("SELECT r.username, r.picture, e.quote 
                        FROM nextplace np 
                        JOIN register r ON np.username = r.username 
                        JOIN extra e ON r.username = e.username
                        WHERE LOWER(np.nextVisit) = ?");
$stmt->bind_param("s", $place);
$stmt->execute();
$result = $stmt->get_result();

$travelers = [];
while ($row = $result->fetch_assoc()) {
    $travelers[] = [
        'username' => $row['username'],
        'picture' => base64_encode($row['picture']),
        'quote' => $row['quote']
    ];
}

if (count($travelers) > 0) {
    echo json_encode(['success' => true, 'results' => $travelers]);
} else {
    echo json_encode(['success' => false, 'message' => 'No buddies found for this location.']);
}

$stmt->close();
$conn->close();
?>
