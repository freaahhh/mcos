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
                        // Using correct column casing: feedback_cat_ID
                        $sql = "SELECT f.*, c.cust_username, fc.feedback_cat_name 
                                FROM feedback f 
                                JOIN customer c ON f.cust_ID = c.cust_ID 
                                JOIN feedback_category fc ON f.feedback_cat_ID = fc.feedback_cat_ID 
                                ORDER BY f.feedback_ID DESC";
                        $res = mysqli_query($conn, $sql);

                        if(mysqli_num_rows($res) > 0) {
                            while($row = mysqli_fetch_assoc($res)) {
                                // FIX: Use feedback_ID (capitalized ID) to match database
                                $f_id = $row['feedback_ID']; 
                                ?>
                                <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                    <td style="padding: 15px; font-weight: bold; color: #ff4757;">#<?php echo $f_id; ?></td>
                                    <td style="padding: 15px;"><?php echo $row['cust_username']; ?></td>
                                    <td style="padding: 15px;">
                                        <span style="background: #57606f; padding: 4px 10px; border-radius: 5px; font-size: 0.8rem;">
                                            <?php echo $row['feedback_cat_name']; ?>
                                        </span>
                                    </td>
                                    <td style="padding: 15px; font-style: italic; color: #a4b0be; max-width: 300px;">
                                        "<?php echo $row['feedback']; ?>"
                                    </td>
                                    <td style="padding: 15px; font-size: 0.85rem;">
                                        <?php echo date('d M Y', strtotime($row['created_at'])); ?>
                                    </td>
                                    <td style="padding: 15px;">
                                        <?php if($is_admin): ?>
                                            <a href="delete-feedback.php?id=<?php echo $f_id; ?>" 
                                               style="color: #ff4757; text-decoration: none; font-weight: bold; font-size: 0.85rem;"
                                               onclick="return confirm('Delete this feedback permanently?')">
                                               Delete
                                            </a>
                                        <?php else: ?>
                                            <span style="color: #57606f; font-size: 0.8rem;">View Only</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='6' style='padding: 30px; text-align: center; color: #a4b0be;'>No feedback received yet.</td></tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('partials/footer.php'); ?>