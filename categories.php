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
            // 1. Updated Table Name to 'category'
            $sql = "SELECT * FROM category";

            // Execute the Query
            $res = mysqli_query($conn, $sql);

            // Count Rows
            $count = mysqli_num_rows($res);

            // Check whether categories available or not
            if($count > 0)
            {
                // Categories Available
                while($row = mysqli_fetch_assoc($res))
                {
                    // 2. Map variables to your specific database columns
                    $id = $row['category_ID'];
                    $title = $row['category_details']; // Using details as the title
                    $image_name = $row['category_pict'];
                    ?>
                    
                    <a href="<?php echo SITEURL; ?>category-foods.php?category_id=<?php echo $id; ?>">
                        <div class="box-3 float-container">
                            <?php 
                                if($image_name == "")
                                {
                                    // Image not Available
                                    echo "<div class='error'>Image not found.</div>";
                                }
                                else
                                {
                                    // Image Available
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
            }
            else
            {
                // Categories Not Available
                echo "<div class='error'>Category not found.</div>";
            }
        ?>

        <div class="clearfix"></div>
    </div>
</section>
<?php include('partials-front/footer.php'); ?>
