<?php include('partials/menu.php'); ?>

<div class="main-content" style="background-color: #f1f2f6; padding: 3% 0;">
    <div class="wrapper">
        <h1 class="text-center" style="margin-bottom: 30px; color: #2f3542;">Manage Food Categories</h1>

        <div style="text-align: right; margin-bottom: 20px;">
            <a href="add-category.php" class="btn-primary" style="background: #2ecc71; padding: 10px 20px; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-flex; align-items: center; gap: 8px;">
                <img src="../images/icons/add.png" alt="Add" style="width: 20px; height: 20px;">New Category</a>
        </div>

        <div style="background: #2f3542; color: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
            <table style="width: 100%; border-collapse: collapse; background: rgba(255,255,255,0.05); border-radius: 10px; overflow: hidden;">
                <thead>
                    <tr style="background: rgba(255,255,255,0.1); text-align: left;">
                        <th style="padding: 15px;">No.</th>
                        <th style="padding: 15px;">Category Name</th>
                        <th style="padding: 15px;">Image Preview</th>
                        <th style="padding: 15px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Oracle SQL: Fetch all categories
                    $sql = "SELECT * FROM category ORDER BY CATEGORY_ID ASC";
                    $stmt = oci_parse($conn, $sql);
                    oci_execute($stmt);

                    $sn = 1;
                    $has_data = false;

                    // Oracle returns column keys in UPPERCASE by default
                    while ($row = oci_fetch_array($stmt, OCI_ASSOC)) {
                        $has_data = true;
                        $id = $row['CATEGORY_ID'];

                        // Handle CLOB if CATEGORY_DETAILS is a long text field
                        $details = is_object($row['CATEGORY_DETAILS']) ? $row['CATEGORY_DETAILS']->load() : $row['CATEGORY_DETAILS'];
                        $image_name = $row['CATEGORY_PICT'];
                    ?>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 15px;"><?php echo $sn++; ?>.</td>
                            <td style="padding: 15px; font-weight: bold;"><?php echo $details; ?></td>
                            <td style="padding: 15px;">
                                <?php if ($image_name != "" && $image_name != null) { ?>
                                    <img src="<?php echo SITEURL; ?>images/category/<?php echo $image_name; ?>" width="60px" style="border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.5);">
                                <?php } else {
                                    echo "<span style='color:#747d8c; font-style:italic;'>No Image</span>";
                                } ?>
                            </td>
                            <td style="padding: 15px;">
                                <a href="update-category.php?id=<?php echo $id; ?>" class="btn-action" title="Update & Role"><img src="../images/icons/edit.png" alt="Edit"></a>
                                <?php if ($is_admin): ?>
                                    <a href="delete-category.php?id=<?php echo $id; ?>" class="btn-action" title="Delete" onclick="return confirm('Are you sure you want to delete this category?')"><img src="../images/icons/delete.png" alt="Delete">
                                    <?php endif; ?>
                            </td>
                        </tr>
                    <?php
                    }

                    // Cleanup statement
                    oci_free_statement($stmt);

                    if (!$has_data) {
                        echo "<tr><td colspan='4' style='padding:20px; text-align:center;'>No categories found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include('partials/footer.php'); ?>