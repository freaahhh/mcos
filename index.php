<?php include('partials-front/menu.php'); ?>

<section class="food-search text-center">
    <div class="container">
        <form action="<?php echo SITEURL; ?>food-search.php" method="POST">
            <input type="search" name="search" placeholder="Search for Food.." required>
            <input type="submit" name="submit" value="Search" class="btn btn-primary">
        </form>
    </div>
</section>

<?php 
    if(isset($_SESSION['order'])) {
        echo $_SESSION['order'];
        unset($_SESSION['order']);
    }
?>

<section class="categories">
    <div class="container">
        <h2 class="text-center">Explore Foods</h2>

        <?php 
            // Query matches your 'category' table
            $sql = "SELECT * FROM category LIMIT 3";
            $res = mysqli_query($conn, $sql);
            $count = mysqli_num_rows($res);

            if($count > 0) {
                while($row = mysqli_fetch_assoc($res)) {
                    // FIX: Updated keys to match your database columns
                    $id = $row['category_ID'];
                    $details = $row['category_details']; // Use details as the label
                    $image_name = $row['category_pict'];
                    ?>
                    
                    <a href="<?php echo SITEURL; ?>category-foods.php?category_id=<?php echo $id; ?>">
                        <div class="box-3 float-container">
                            <?php 
                                if($image_name == "") {
                                    echo "<div class='error'>Image not Available</div>";
                                } else {
                                    ?>
                                    <img src="<?php echo SITEURL; ?>images/category/<?php echo $image_name; ?>" alt="<?php echo $details; ?>" class="img-responsive img-curve">
                                    <?php
                                }
                            ?>
                            <h3 class="float-text text-white"><?php echo $details; ?></h3>
                        </div>
                    </a>

                    <?php
                }
            } else {
                echo "<div class='error'>Category not Added.</div>";
            }
        ?>
        <div class="clearfix"></div>
    </div>
</section>

<section class="food-menu">
    <div class="container">
        <h2 class="text-center">Featured Foods</h2>

        <?php 
            // Query matches your 'menu' table
            $sql2 = "SELECT * FROM menu WHERE menu_availability='1' LIMIT 6";
            $res2 = mysqli_query($conn, $sql2);
            $count2 = mysqli_num_rows($res2);

            if($count2 > 0) {
                while($row = mysqli_fetch_assoc($res2)) {
                    // FIX: Updated keys to match your database columns
                    $id = $row['menu_ID'];
                    $title = $row['menu_name'];
                    $price = $row['menu_price'];
                    $description = $row['menu_details'];
                    $image_name = $row['menu_pict'];
                    ?>

                    <div class="food-menu-box">
                        <div class="food-menu-img">
                            <?php 
                                if($image_name == "") {
                                    echo "<div class='error'>Image not available.</div>";
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
                echo "<div class='error'>Food not available.</div>";
            }
        ?>
        <div class="clearfix"></div>
    </div>

    <p class="text-center">
        <a href="<?php echo SITEURL; ?>foods.php">See All Foods</a>
    </p>
</section>

<?php include('partials-front/footer.php'); ?>
