<?php include('partials/menu.php'); ?>

<div class="main-content" style="background-color: #f1f2f6; padding: 3% 0; min-height: 80vh;">
    <div class="wrapper" style="max-width: 1200px; margin: 0 auto;">

        <h1 class="text-center" style="margin-bottom: 30px; color: #2f3542;">Manage Customer Feedback</h1>

        <div style="background: #2f3542; color: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
            <table style="width: 100%; border-collapse: collapse; background: rgba(255,255,255,0.05); border-radius: 10px; overflow: hidden;">
                <thead>
                    <tr style="background: rgba(255,255,255,0.1); text-align: left;">
                        <th style="padding: 15px;">ID</th>
                        <th style="padding: 15px;">Customer</th>
                        <th style="padding: 15px;">Category</th>
                        <th style="padding: 15px;">Feedback Comment</th>
                        <th style="padding: 15px;">Date</th>
                        <th style="padding: 15px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Oracle SQL: Using JOINs with standard Oracle naming
                    $sql = "SELECT f.*, 
                                        TO_CHAR(f.CREATED_AT, 'YYYY-MM-DD HH24:MI:SS') AS FORMATTED_DATE, 
                                        c.CUST_USERNAME, 
                                        fc.FEEDBACK_CAT_NAME 
                                    FROM FEEDBACK f 
                                    JOIN CUSTOMER c ON f.CUST_ID = c.CUST_ID 
                                    JOIN FEEDBACK_CATEGORY fc ON f.FEEDBACK_CAT_ID = fc.FEEDBACK_CAT_ID 
                                    ORDER BY f.FEEDBACK_ID DESC";

                    $stmt = oci_parse($conn, $sql);
                    oci_execute($stmt);

                    $has_data = false;

                    // Fetching rows using OCI_ASSOC (keys will be UPPERCASE)
                    while ($row = oci_fetch_array($stmt, OCI_ASSOC)) {
                        $has_data = true;
                        $f_id = $row['FEEDBACK_ID'];

                        // CLOB Fix: Handle the feedback comment object
                        $feedback_text = is_object($row['FEEDBACK']) ? $row['FEEDBACK']->load() : $row['FEEDBACK'];
                    ?>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 15px; font-weight: bold; color: #ff4757;">#<?php echo $f_id; ?></td>
                            <td style="padding: 15px;"><?php echo $row['CUST_USERNAME']; ?></td>
                            <td style="padding: 15px;">
                                <span style="background: #57606f; padding: 4px 10px; border-radius: 5px; font-size: 0.8rem;">
                                    <?php echo $row['FEEDBACK_CAT_NAME']; ?>
                                </span>
                            </td>
                            <td style="padding: 15px; font-style: italic; color: #a4b0be; max-width: 300px;">
                                "<?php echo $feedback_text; ?>"
                            </td>
                            <td style="padding: 15px; font-size: 0.85rem;">
                                <?php
                                // Guna column FORMATTED_DATE yang kita buat dalam SQL tadi
                                echo date('d M Y', strtotime($row['FORMATTED_DATE']));
                                ?>
                            </td>
                            <td style="padding: 15px;">
                                <?php if ($is_admin): ?>
                                    <a href="delete-feedback.php?id=<?php echo $f_id; ?>"
                                        class="btn-action" title="Delete" onclick="return confirm('Are you sure you want to delete this staff feedback?')"><img src="../images/icons/delete.png" alt="Delete">
                                    </a>
                                <?php else: ?>
                                    <span style="color: #57606f; font-size: 0.8rem;">View Only</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php
                    }

                    oci_free_statement($stmt);

                    if (!$has_data) {
                        echo "<tr><td colspan='6' style='padding: 30px; text-align: center; color: #a4b0be;'>No feedback received yet.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('partials/footer.php'); ?>