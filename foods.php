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
        // 1. Query Data
        $sql = "SELECT * FROM MENU WHERE MENU_AVAILABILITY = '1'";
        $stid = oci_parse($conn, $sql);
        oci_execute($stid);

        $found = false;

        // 2. FIX UTAMA: Tambah '+ OCI_RETURN_NULLS'
        // Gabungan Mantap:
        // OCI_ASSOC -> Susun ikut nama column
        // OCI_RETURN_LOBS -> Baca text panjang (CLOB)
        // OCI_RETURN_NULLS -> Jangan error kalau data kosong
        while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
            $found = true;

            // 3. Map Data (Selamat sebab ada OCI_RETURN_NULLS)
            $id = $row['MENU_ID'];
            $title = $row['MENU_NAME'];
            $price = $row['MENU_PRICE'];
            $image_name = $row['MENU_PICT'];

            // Handle Description kalau kosong
            // Walaupun ada OCI_RETURN_NULLS, kita check 'isset' untuk extra safety
            $description = isset($row['MENU_DETAILS']) ? $row['MENU_DETAILS'] : "No description available.";

            // Potong description kalau panjang sangat (Optional styling)
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
                        // Note: Check path ni. Kalau gambar tak keluar, buang '/food'
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

                <!--<div class="food-menu-desc">
                    <h4><?php echo $title; ?></h4>
                    <p class="food-price">RM <?php echo number_format($price, 2); ?></p>

                    <p class="food-detail">
                        <?php echo $description; ?>
                    </p>
                    <br>

                    <form action="add-to-cart.php" method="POST">
                        <input type="hidden" name="menu_id" value="<?php echo $id; ?>">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" name="add_to_cart" class="btn btn-primary">Add to Cart</button>
                    </form>

                    <a href="food-detail.php?food_id=<?php echo $id; ?>" style="font-size: 12px; margin-left: 10px; color: gray;">View Details</a>

                </div>-->
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