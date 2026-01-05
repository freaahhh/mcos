<?php include('partials-front/menu.php'); ?>

<?php
// --- Check if category_id is passed ---
if (isset($_GET['category_id'])) {
    $category_id = (int) $_GET['category_id'];

    // --- Fetch category title from Oracle ---
    $sql = "SELECT CATEGORY_DETAILS FROM CATEGORY WHERE CATEGORY_ID = :category_id";
    $stid = oci_parse($conn, $sql);
    oci_bind_by_name($stid, "category_id", $category_id);
    oci_execute($stid);
    $row = oci_fetch_assoc($stid);
    oci_free_statement($stid);

    if ($row) {
        $category_title = $row['CATEGORY_DETAILS'];
    } else {
        header('location:' . SITEURL);
        exit();
    }
} else {
    header('location:' . SITEURL);
    exit();
}
?>

<section class="food-search text-center">
    <div class="container">
        <h2>Foods on <a href="#" class="text-white">"<?php echo $category_title; ?>"</a></h2>
    </div>
</section>

<section class="food-menu">
    <div class="container">
        <h2 class="text-center">Menu</h2>

        <?php
        // --- Fetch foods in this category ---
        $sql2 = "SELECT * FROM MENU WHERE CATEGORY_ID = :category_id AND MENU_AVAILABILITY = '1'";
        $stid2 = oci_parse($conn, $sql2);
        oci_bind_by_name($stid2, "category_id", $category_id);
        oci_execute($stid2);

        $foods = [];
        while ($row2 = oci_fetch_assoc($stid2)) {
            $foods[] = $row2;
        }
        oci_free_statement($stid2);

        if (count($foods) > 0) {
            foreach ($foods as $row2) {
                $id = $row2['MENU_ID'];
                $title = $row2['MENU_NAME'];
                $price = $row2['MENU_PRICE'];
                $description = $row2['MENU_DETAILS'];

                if (is_object($description)) {
                    $description = $description->load();
                }
                $image_name = $row2['MENU_PICT'];
        ?>
                <div class="food-menu-box">
                    <div class="food-menu-img">
                        <?php
                        if (empty($image_name)) {
                            echo "<div class='error'>Image not Available.</div>";
                        } else {
                        ?>
                            <img src="<?php echo SITEURL; ?>images/food/<?php echo $image_name; ?>" alt="<?php echo $title; ?>" class="img-responsive img-curve">
                        <?php
                        }
                        ?>
                    </div>

                    <div class="food-menu-desc">
                        <h4><?php echo $title; ?></h4>
                        <p class="food-price">RM <?php echo $price; ?></p>
                        <p class="food-detail"><?php echo $description; ?></p>
                        <br>
                        <a href="food-detail.php?food_id=<?php echo $id; ?>" class="btn btn-primary">Order Now</a>
                    </div>
                </div>
        <?php
            }
        } else {
            echo "<div class='error'>Food not Available in this category.</div>";
        }
        ?>

        <div class="clearfix"></div>
    </div>
</section>

<?php include('partials-front/footer.php'); ?>