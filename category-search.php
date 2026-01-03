<?php include('partials-front/menu.php'); ?>

<section class="food-search text-center">
    <div class="container">
        <?php
        // Get the search term safely
        $search = isset($_POST['search']) ? trim($_POST['search']) : '';
        $search_display = htmlspecialchars($search, ENT_QUOTES);
        ?>
        <h2>Categories Matching <a href="#" class="text-white">"<?php echo $search_display; ?>"</a></h2>
    </div>
</section>

<section class="categories">
    <div class="container">
        <?php
        if ($search != "") {
            // FIX 1: Guna UPPER() untuk Case Insensitive Search
            // "pizza" akan jumpa "Pizza", "PIZZA", atau "PiZZa"
            $sql = "SELECT * FROM CATEGORY WHERE UPPER(CATEGORY_DETAILS) LIKE UPPER(:search)";

            $stid = oci_parse($conn, $sql);

            // Bind variable
            $search_param = "%" . $search . "%";
            oci_bind_by_name($stid, ":search", $search_param);

            oci_execute($stid);

            $found = false;

            // FIX 2: Guna Magic Flags (OCI_RETURN_NULLS + OCI_RETURN_LOBS)
            // Ini standard procedure kita untuk elak error "Undefined key"
            while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS + OCI_RETURN_LOBS)) {
                $found = true;

                $id = $row['CATEGORY_ID'];

                // Safety check untuk Title
                $title = isset($row['CATEGORY_DETAILS']) ? $row['CATEGORY_DETAILS'] : "Category";

                $image_name = $row['CATEGORY_PICT'];
        ?>
                <a href="<?php echo SITEURL; ?>category-foods.php?category_id=<?php echo $id; ?>">
                    <div class="box-3 float-container">
                        <?php
                        if ($image_name == "" || is_null($image_name)) {
                            echo "<div class='error'>Image not found</div>";
                        } else {
                            // Pastikan path image betul. Usually 'images/category/'
                        ?>
                            <img src="<?php echo SITEURL; ?>images/category/<?php echo $image_name; ?>" alt="<?php echo $title; ?>" class="img-responsive img-curve">
                        <?php } ?>

                        <h3 class="float-text text-white"><?php echo $title; ?></h3>
                    </div>
                </a>

        <?php
            } // End While Loop

            if (!$found) {
                echo "<div class='error'>No Categories found.</div>";
            }

            oci_free_statement($stid);
        } else {
            echo "<div class='error'>Please enter a search term.</div>";
        }
        ?>

        <div class="clearfix"></div>
    </div>
</section>

<?php include('partials-front/footer.php'); ?>