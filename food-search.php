<?php include('partials-front/menu.php'); ?>

<section class="food-search text-center">
    <div class="container">
        <?php 
            // Get the Search Keyword safely
            // Check if search exists to avoid errors on direct page access
            $search = isset($_POST['search']) ? mysqli_real_escape_string($conn, $_POST['search']) : "";
        ?>

        <h2>Foods on Your Search <a href="#" class="text-white">"<?php echo $search; ?>"</a></h2>

    </div>
</section>
<section class="food-menu">
    <div class="container">
        <h2 class="text-center">Food Menu</h2>

        <?php 
            // SQL Query to Get foods based on search keyword from your 'menu' table
            // We use your specific columns: menu_name and menu_details
            $sql = "SELECT * FROM menu 
                    WHERE menu_name LIKE '%$search%' 
                    OR menu_details LIKE '%$search%' 
                    AND menu_availability = 1"; // Only show items available for order

            // Execute the Query
            $res = mysqli_query($conn, $sql);

            // Count Rows
            $count = mysqli_num_rows($res);

            // Check whether food available or not
            if($count > 0)
            {
                // Food Available
                while($row = mysqli_fetch_assoc($res))
                {
                    // Get the details using your specific database attributes
                    $id = $row['menu_ID'];
                    $title = $row['menu_name'];
                    $price = $row['menu_price'];
                    $description = $row['menu_details'];
                    $image_name = $row['menu_pict'];
                    ?>

                    <div class="food-menu-box">
                        <div class="food-menu-img">
                            <?php 
                                // Check whether image name is available or not
                                if($image_name == "")
                                {
                                    // Image not Available
                                    echo "<div class='error'>Image not Available.</div>";
                                }
                                else
                                {
                                    // Image Available
                                    ?>
                                    <img src="<?php echo SITEURL; ?>images/food/<?php echo $image_name; ?>" alt="<?php echo $title; ?>" class="img-responsive img-curve">
                                    <?php 
                                }
                            ?>
                        </div>

                        <div class="food-menu-desc">
                            <h4><?php echo $title; ?></h4>
                            <p class="food-price">RM <?php echo number_format($price, 2); ?></p>
                            <p class="food-detail">
                                <?php echo $description; ?>
                            </p>
                            <br>

                            <a href="food-detail.php?food_id=<?php echo $id; ?>" class="btn btn-primary">Order Now</a>
                        </div>
                    </div>

                    <?php
                }
            }
            else
            {
                // Food Not Available
                echo "<div class='error text-center'>Food not found for this search.</div>";
            }
        ?>

        <div class="clearfix"></div>

    </div>
</section>
<?php include('partials-front/footer.php'); ?>
