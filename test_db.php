<?php
include('config/constants.php');

if ($conn) {
    echo "<h2 style='color: green;'>✅ Success! Connected to Oracle (FREEPDB1).</h2>";

    // Let's try to query the CUSTOMER table we created earlier
    $sql = "SELECT USERNAME FROM (SELECT cust_username AS USERNAME FROM customer) WHERE ROWNUM = 1";
    $stmt = oci_parse($conn, $sql);

    if (oci_execute($stmt)) {
        $row = oci_fetch_array($stmt, OCI_ASSOC);
        echo "<p>Database test: Found user <strong>" . ($row['USERNAME'] ?? 'None') . "</strong></p>";
    } else {
        $e = oci_error($stmt);
        echo "<p style='color: red;'>Query Failed: " . $e['message'] . "</p>";
    }
} else {
    echo "<h2 style='color: red;'>❌ Connection Failed.</h2>";
}
