<?php
// PostgreSQL connection
$conn = pg_connect("host=localhost dbname=student_addmission user=roots password=Nandakumarpn@!");
if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Get form data
    $full_name = $_POST['full_name'];
    $regno = $_POST['Regester'];
    $course_id = $_POST['course_id'];
    $semester = $_POST['Semester'];
    $phone = $_POST['phone'];
    $txn_id = $_POST['transaction_id'];

    // Generate 6-digit password
    $password = strval(rand(100000, 999999));

    // Handle uploaded receipt image
    $receipt_file = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $receipt_file = basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $receipt_file);
    }

    // Insert into database
    $query = "INSERT INTO registration1
        (full_name, regno, course_id, semester, phone, txn_id, receipt_image, password)
        VALUES ($1,$2,$3,$4,$5,$6,$7,$8)";
    $result = pg_query_params($conn, $query, array($full_name, $regno, $course_id, $semester, $phone, $txn_id, $receipt_file, $password));

    if ($result) {
        echo "<h2>Registration Successful!</h2>";
        echo "<p><strong>Transaction ID:</strong> $txn_id</p>";
        echo "<p><strong>Your Password:</strong> $password</p>";
        echo "<a href='verify.html'>Go to Verification</a>";
    } else {
        echo "Error: " . pg_last_error($conn);
    }

    // Close connection
    pg_close($conn);
}
?>
