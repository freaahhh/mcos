<?php include('partials-front/menu.php'); ?>

<?php 
    // Check whether id is passed or not
    if(isset($_GET['category_id']))
    {
        // Category id is set and get the id
        $category_id = $_GET['category_id'];

        // FIX: Updated table name to 'category' and column to 'category_ID'
        // Using 'category_details' as the title based on your screenshot
        $sql = "SELECT category_details FROM category WHERE category_ID=$category_id";

        // Execute the Query
        $res = mysqli_query($conn, $sql);

        // Get the value from Database
        $row = mysqli_fetch_assoc($res);
        
        // Get the Title from the correct column
        $category_title = $row['category_details'];
    }
    else
    {
        // Category not passed, redirect to Home page
        header('location:'.SITEURL);
    }
?>


<section class="food-search text-center">
    <div class="container">
        
        <h2>Foods on <a href="#" class="text-white">"<?php echo $category_title; ?>"</a></h2>

    </div>
</section>
<section class="food-menu">
    <div class="container">
        <h2 class="text-center">Food Menu</h2>

        <?php 
        
            // FIX: Updated table to 'menu' and column to 'category_ID'
            $sql2 = "SELECT * FROM menu WHERE category_ID=$category_id AND menu_availability='1'";

            // Execute the Query
            $res2 = mysqli_query($conn, $sql2);

            // Count the Rows
            $count2 = mysqli_num_rows($res2);

            // Check whether food is available or not
            if($count2 > 0)
            {
                // Food is Available
                while($row2 = mysqli_fetch_assoc($res2))
                {
                    // FIX: Updated keys to match your menu table columns
                    $id = $row2['menu_ID'];
                    $title = $row2['menu_name'];
                    $price = $row2['menu_price'];
                    $description = $row2['menu_details'];
                    $image_name = $row2['menu_pict'];
                    ?>
                    
                    <div class="food-menu-box">
                        <div class="food-menu-img">
                            <?php 
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
                            <p class="food-price">RM <?php echo $price; ?></p>
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
                // Food not available
                echo "<div class='error'>Food not Available in this category.</div>";
            }
        
        ?>

        <div class="clearfix"></div>

    </div>

</section>
<?php include('partials-front/footer.php'); ?>
