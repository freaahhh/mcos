<?php include('partials-front/menu.php'); ?>

<section class="food-search text-center">
    <div class="container">
        <form action="<?php echo SITEURL; ?>category-search.php" method="POST">
            <input type="search" name="search" placeholder="Search for Category.." required>
            <input type="submit" name="submit" value="Search" class="btn btn-primary">
        </form>
    </div>
</section>

<section class="categories">
    <div class="container">
        <h2 class="text-center">Explore Foods</h2>

        <?php
        // --- Oracle Query ---
        $sql = "SELECT * FROM CATEGORY";
        $stid = oci_parse($conn, $sql);
        oci_execute($stid);

        $categories = [];
        while ($row = oci_fetch_assoc($stid)) {
            $categories[] = $row;
        }
        oci_free_statement($stid);

        $count = count($categories);

        if ($count > 0) {
            foreach ($categories as $row) {
                $id = $row['CATEGORY_ID'];
                $title = $row['CATEGORY_DETAILS']; // Oracle columns uppercase
                $image_name = $row['CATEGORY_PICT'];
        ?>

                <a href="<?php echo SITEURL; ?>category-foods.php?category_id=<?php echo $id; ?>">
                    <div class="box-3 float-container">
                        <?php
                        if (empty($image_name)) {
                            echo "<div class='error'>Image not found.</div>";
                        } else {
                        ?>
                            <img src="<?php echo SITEURL; ?>images/category/<?php echo $image_name; ?>" alt="<?php echo $title; ?>" class="img-responsive img-curve">
                        <?php
                        }
                        ?>
                        <h3 class="float-text text-white"><?php echo $title; ?></h3>
                    </div>
                </a>

        <?php
            }
        } else {
            echo "<div class='error'>Category not found.</div>";
        }
        ?>

        <div class="clearfix"></div>
    </div>
</section>

<?php include('partials-front/footer.php'); ?>