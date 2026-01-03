<?php include('partials-front/menu.php'); ?>

<section class="food-search text-center">
    <div class="container">
        <?php
        // Ambil search keyword
        $search = isset($_POST['search']) ? trim($_POST['search']) : "";
        ?>
        <h2>Foods on Your Search <a href="#" class="text-white">"<?php echo htmlspecialchars($search); ?>"</a></h2>
    </div>
</section>

<section class="food-menu">
    <div class="container">
        <h2 class="text-center">Food Menu</h2>

        <?php
        if ($search != "") {
            // FIX 1: SQL Case Insensitive (Guna UPPER)
            // Oracle ni sensitif. 'Burger' tak sama dengan 'burger'.
            // Kita tukar dua-dua jadi UPPER supaya search lebih power.
            $sql = "SELECT * FROM MENU 
                    WHERE (UPPER(MENU_NAME) LIKE UPPER(:search) OR UPPER(MENU_DETAILS) LIKE UPPER(:search)) 
                    AND MENU_AVAILABILITY = '1'";

            $stid = oci_parse($conn, $sql);

            // Masukkan % siap-siap
            $like_search = "%" . $search . "%";
            oci_bind_by_name($stid, ":search", $like_search);

            oci_execute($stid);

            $found = false;

            // FIX 2: Tambah 'OCI_RETURN_LOBS + OCI_RETURN_NULLS'
            // Wajib ada supaya tak error bila jumpa Description kosong
            while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                $found = true;

                $id = $row['MENU_ID'];
                $title = $row['MENU_NAME'];
                $price = $row['MENU_PRICE'];
                $image_name = $row['MENU_PICT'];

                // FIX 3: Handle Description NULL
                $description = isset($row['MENU_DETAILS']) ? $row['MENU_DETAILS'] : "No description available.";
        ?>
                <div class="food-menu-box">
                    <div class="food-menu-img">
                        <?php
                        if ($image_name == "" || is_null($image_name)) {
                            echo "<div class='error'>Image not Available.</div>";
                        } else {
                            // Check path images/food/ atau images/
                        ?>
                            <img src="<?php echo SITEURL; ?>images/food/<?php echo $image_name; ?>" alt="<?php echo $title; ?>" class="img-responsive img-curve">
                        <?php } ?>
                    </div>

                    <div class="food-menu-desc">
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

                    </div>
                </div>
        <?php
            }

            if (!$found) {
                echo "<div class='error text-center'>Food not found for this search.</div>";
            }

            oci_free_statement($stid);
        } else {
            echo "<div class='error text-center'>Please enter a search keyword.</div>";
        }
        ?>

        <div class="clearfix"></div>
    </div>
</section>

<?php include('partials-front/footer.php'); ?>