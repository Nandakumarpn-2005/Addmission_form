<?php
// Enable error reporting (for debugging)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// PostgreSQL connection
$conn = pg_connect("host=localhost dbname=student_addmission user=nandakumarpn password=Nandakumarpn@!");
if (!$conn) {
    die("❌ Database connection failed: " . pg_last_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Collect and sanitize input
    $full_name = trim($_POST['full_name']);
    $regno = trim($_POST['Regester']);
    $course_id = intval($_POST['course_id']);
    $semester = intval($_POST['Semester']);
    $phone = trim($_POST['phone']);
    $txn_id = trim($_POST['transaction_id']);

    if (empty($full_name) || empty($regno) || empty($txn_id)) {
        die("❌ Required fields missing!");
    }

    // Generate 6-digit random password
    $password = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

    // File upload handling
    $receipt_file = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $upload_dir = __DIR__ . '/uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $safe_name = preg_replace("/[^a-zA-Z0-9._-]/", "_", basename($_FILES['photo']['name']));
        $target_path = $upload_dir . $safe_name;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_path)) {
            $receipt_file = $safe_name;
        } else {
            die("❌ Error uploading receipt image!");
        }
    }

    // Insert data
    $query = "INSERT INTO registration1 (full_name, regno, course_id, semester, phone, txn_id, receipt_image, password)
              VALUES ($1, $2, $3, $4, $5, $6, $7, $8)";
    $params = array($full_name, $regno, $course_id, $semester, $phone, $txn_id, $receipt_file, $password);
    $result = pg_query_params($conn, $query, $params);

    if ($result) {
        echo "
        <html>
        <head>
          <title>Registration Successful</title>
          <style>
            body { font-family:Poppins, sans-serif; background:#f6f8fa; color:#222; text-align:center; padding:40px; }
            h2 { color:#1f3c88; }
            a { display:inline-block; margin-top:20px; padding:12px 24px; background:#1f3c88; color:#fff; border-radius:6px; text-decoration:none; }
            a:hover { background:#162a5a; }
          </style>
        </head>
        <body>
          <h2>✅ Registration Successful!</h2>
          <p><strong>Transaction ID:</strong> {$txn_id}</p>
          <p><strong>Your Password:</strong> {$password}</p>
          <a href='app.html'>Go to Verification</a>
        </body>
        </html>";
    } else {
        echo "❌ Database error: " . pg_last_error($conn);
    }

    pg_close($conn);
}
?>
