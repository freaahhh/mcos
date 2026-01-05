<?php include('partials/menu.php'); ?>

<div class="main-content" style="background-color: #f1f2f6; padding: 3% 0; min-height: 80vh;">
    <div class="wrapper">
        <h1 class="text-center" style="margin-bottom: 30px; color: #2f3542;">Manage Staff Accounts</h1>

        <?php 
            if(isset($_SESSION['update'])) { echo $_SESSION['update']; unset($_SESSION['update']); }
            if(isset($_SESSION['delete'])) { echo $_SESSION['delete']; unset($_SESSION['delete']); }
            if(isset($_SESSION['add'])) { echo $_SESSION['add']; unset($_SESSION['add']); }
        ?>

        <?php if($is_admin): ?>
        <div style="text-align: right; margin-bottom: 20px;">
            <a href="add-staff.php" class="btn-action" style="background: #2ecc71; padding: 10px 20px; font-weight: bold; display: inline-block;">+ Add New Staff</a>
        </div>
        <?php endif; ?>

        <div style="background: #2f3542; color: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
            <table class="table-admin" style="width: 100%; border-collapse: collapse; background: rgba(255,255,255,0.05); border-radius: 10px; overflow: hidden;">
                <thead>
                    <tr style="background: rgba(255,255,255,0.1); text-align: left;">
                        <th style="padding: 15px;">No.</th>
                        <th style="padding: 15px;">Full Name</th>
                        <th style="padding: 15px;">Username</th>
                        <th style="padding: 15px;">Status</th>
                        <th style="padding: 15px;">Contact</th>
                        <th style="padding: 15px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        // Oracle Query: Standard SQL Joins remain the same
                        $sql = "SELECT s.*, 
                                CASE 
                                    WHEN ft.STAFF_ID IS NOT NULL THEN 'Full-Time'
                                    WHEN pt.STAFF_ID IS NOT NULL THEN 'Part-Time'
                                    ELSE 'Unassigned'
                                END AS EMP_STATUS
                                FROM STAFF s
                                LEFT JOIN FULL_TIME ft ON s.STAFF_ID = ft.STAFF_ID
                                LEFT JOIN PART_TIME pt ON s.STAFF_ID = pt.STAFF_ID
                                ORDER BY s.STAFF_ID ASC";

                        $stmt = oci_parse($conn, $sql); // Parse
                        oci_execute($stmt); // Execute

                        $sn = 1;
                        $has_data = false;

                        // Oracle returns column keys in UPPERCASE
                        while($rows = oci_fetch_array($stmt, OCI_ASSOC)) {
                            $has_data = true;
                            $id = $rows['STAFF_ID'];
                            $full_name = $rows['STAFF_FIRST_NAME']." ".$rows['STAFF_LAST_NAME'];
                            $username = $rows['STAFF_USERNAME'];
                            $status = $rows['EMP_STATUS'];
                            $contact = $rows['STAFF_CONTACT_NO'];
                            ?>
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <td style="padding: 15px;"><?php echo $sn++; ?>.</td>
                                <td style="padding: 15px; font-weight: bold;"><?php echo $full_name; ?></td>
                                <td style="padding: 15px; color: #a4b0be;"><?php echo $username; ?></td>
                                <td style="padding: 15px;">
                                    <span style="background: #57606f; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; color: white;">
                                        <?php echo $status; ?>
                                    </span>
                                </td>
                                <td style="padding: 15px;"><?php echo $contact; ?></td>
                                <td style="padding: 15px;">
                                    <?php if($is_admin && $id != 1): ?>
                                        <a href="update-staff.php?id=<?php echo $id; ?>" class="btn-action" style="background: #3498db; margin-right: 5px;">Update & Role</a>
                                        <a href="delete-staff.php?id=<?php echo $id; ?>" class="btn-action" style="background: #e74c3c;" onclick="return confirm('Are you sure you want to delete this staff member?')">Delete</a>
                                    <?php elseif($id == 1): ?>
                                        <span style="color: #2ecc71; font-size: 0.8rem; font-weight: bold;">Main Admin</span>
                                    <?php else: ?>
                                        <span style="color: #a4b0be; font-size: 0.8rem;">View Only</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php 
                        }
                        oci_free_statement($stmt); // Cleanup

                        if (!$has_data) {
                            echo "<tr><td colspan='6' style='padding: 20px; text-align: center; color: #a4b0be;'>No staff found.</td></tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('partials/footer.php'); ?>