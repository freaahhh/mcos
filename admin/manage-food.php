<?php include('partials/menu.php'); ?>

<div class="main-content" style="background-color: #f1f2f6; padding: 3% 0;">
    <div class="wrapper">
        <h1 class="text-center" style="margin-bottom: 30px; color: #2f3542;">Manage Menu Items</h1>

        <div style="text-align: right; margin-bottom: 20px;">
            <a href="add-food.php" class="btn-primary" style="background: #2ecc71; padding: 10px 20px; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">+ Add New Food Item</a>
        </div>

        <div style="background: #2f3542; color: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
            <table style="width: 100%; border-collapse: collapse; background: rgba(255,255,255,0.05); border-radius: 10px; overflow: hidden;">
                <thead>
                    <tr style="background: rgba(255,255,255,0.1); text-align: left;">
                        <th style="padding: 15px;">No.</th>
                        <th style="padding: 15px;">Food Name</th>
                        <th style="padding: 15px;">Price</th>
                        <th style="padding: 15px;">Image</th>
                        <th style="padding: 15px;">Availability</th>
                        <th style="padding: 15px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $sql = "SELECT * FROM menu";
                        $res = mysqli_query($conn, $sql);
                        $sn = 1;

                        if(mysqli_num_rows($res) > 0) {
                            while($row = mysqli_fetch_assoc($res)) {
                                $id = $row['menu_ID'];
                                $name = $row['menu_name'];
                                $price = $row['menu_price'];
                                $image_name = $row['menu_pict'];
                                $available = $row['menu_availability'];
                    ?>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <td style="padding: 15px;"><?php echo $sn++; ?>.</td>
                        <td style="padding: 15px; font-weight: bold;"><?php echo $name; ?></td>
                        <td style="padding: 15px; color: #2ecc71; font-weight: bold;">RM <?php echo number_format($price, 2); ?></td>
                        <td style="padding: 15px;">
                            <?php if($image_name != "") { ?>
                                <img src="<?php echo SITEURL; ?>images/food/<?php echo $image_name; ?>" width="60px" style="border-radius: 8px;">
                            <?php } else { echo "<span style='color:#747d8c;'>N/A</span>"; } ?>
                        </td>
                        <td style="padding: 15px;">
                            <?php if($available == 1): ?>
                                <span style="background: #2ecc71; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem;">Active</span>
                            <?php else: ?>
                                <span style="background: #ff4757; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem;">Unavailable</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 15px;">
                            <a href="update-food.php?id=<?php echo $id; ?>" style="color: #3498db; text-decoration: none; font-weight: bold; margin-right: 15px;">Update</a>
                            <?php if($is_admin): ?>
                                <a href="delete-food.php?id=<?php echo $id; ?>" style="color: #ff4757; text-decoration: none; font-weight: bold;">Delete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php 
                            }
                        } else {
                            echo "<tr><td colspan='6' style='padding:20px; text-align:center;'>No food items found.</td></tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('partials/footer.php'); ?>
