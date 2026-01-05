<?php include('partials/menu.php'); ?>

<div class="main-content" style="background-color: #f1f2f6; padding: 3% 0;">
    <div class="wrapper">
        <h1 class="text-center" style="margin-bottom: 30px; color: #2f3542;">Manage Customers</h1>

        <div style="background: #2f3542; color: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
            <table style="width: 100%; border-collapse: collapse; background: rgba(255,255,255,0.05); border-radius: 10px; overflow: hidden;">
                <thead>
                    <tr style="background: rgba(255,255,255,0.1); text-align: left;">
                        <th style="padding: 15px;">No.</th>
                        <th style="padding: 15px;">Full Name</th>
                        <th style="padding: 15px;">Dorm/Address</th>
                        <th style="padding: 15px;">Contact</th>
                        <th style="padding: 15px;">Bank Details</th>
                        <th style="padding: 15px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        // Oracle SQL: Standard select from the CUSTOMER table
                        $sql = "SELECT * FROM CUSTOMER ORDER BY CUST_ID ASC";
                        $stmt = oci_parse($conn, $sql);
                        oci_execute($stmt);

                        $sn = 1;
                        $customer_exists = false;

                        // Fetch using OCI_ASSOC and UPPERCASE keys
                        while($rows = oci_fetch_array($stmt, OCI_ASSOC)) {
                            $customer_exists = true;
                            $id = $rows['CUST_ID'];
                            $full_name = $rows['CUST_FIRST_NAME']." ".$rows['CUST_LAST_NAME'];
                            $dorm = $rows['CUST_DORM'] ?? 'Not Set';
                            $contact = $rows['CUST_CONTACT_NO'] ?? 'N/A';
                            
                            // Handling potential nulls for bank info
                            $bank_name = $rows['BANK_NAME'] ?? '';
                            $bank_acc = $rows['BANK_ACCOUNT'] ?? '';
                            $bank = !empty($bank_name) ? $bank_name." (".$bank_acc.")" : 'No Bank Info';
                    ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td style="padding: 15px;"><?php echo $sn++; ?>.</td>
                        <td style="padding: 15px; font-weight: bold;"><?php echo $full_name; ?></td>
                        <td style="padding: 15px;"><?php echo $dorm; ?></td>
                        <td style="padding: 15px;"><?php echo $contact; ?></td>
                        <td style="padding: 15px; font-size: 0.85rem; color: #a4b0be;"><?php echo $bank; ?></td>
                        <td style="padding: 15px;">
                            <?php if($is_admin): ?>
                                <a href="delete-customer.php?id=<?php echo $id; ?>" class="btn-action" style="background: #ff4757; padding: 5px 10px; border-radius: 5px; color:white; text-decoration:none; font-size:0.8rem;">Remove</a>
                            <?php else: ?>
                                <span style="font-size: 0.8rem; color: #57606f;">View Only</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php 
                            }
                        
                        oci_free_statement($stmt);

                        if(!$customer_exists) {
                            echo "<tr><td colspan='6' style='padding:20px; text-align:center;'>No registered customers found.</td></tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('partials/footer.php'); ?>