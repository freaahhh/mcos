<?php include('partials-front/menu.php'); ?>

<section class="food-search text-center">
    <div class="container">
        <form action="<?php echo SITEURL; ?>food-search.php" method="POST">
            <input type="search" name="search" placeholder="Search for Food.." required>
            <input type="submit" name="submit" value="Search" class="btn btn-primary">
        </form>
    </div>
</section>

<section class="food-menu">
    <div class="container">
        <h2 class="text-center">Menu</h2>

        <?php
        $sql = "SELECT * FROM MENU WHERE MENU_AVAILABILITY = '1'";
        $stid = oci_parse($conn, $sql);
        oci_execute($stid);

        $found = false;

        while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            $found = true;

            $id = $row['MENU_ID'];
            $title = $row['MENU_NAME'];
            $price = $row['MENU_PRICE'];
            $image_name = $row['MENU_PICT'];

            $description = isset($row['MENU_DETAILS']) ? $row['MENU_DETAILS'] : "No description available.";

            if (strlen($description) > 100) {
                $description = substr($description, 0, 100) . "...";
            }
        ?>

            <div class="food-menu-box">
                <div class="food-menu-img">
                    <?php
                    if ($image_name == "" || is_null($image_name)) {
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

        if ($found == false) {
            echo "<div class='error text-center'>Food not found.</div>";
        }

        oci_free_statement($stid);
        ?>

        <div class="clearfix"></div>
    </div>
</section>

<?php include('partials-front/footer.php'); ?>