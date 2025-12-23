<?php 
ob_start();
include('partials/menu.php'); 
?>

<div class="main-content" style="background-color: #f1f2f6; padding: 3% 0;">
    <div class="wrapper" style="max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <h1 class="text-center" style="color: #2f3542; margin-bottom: 30px;">Add New Menu Item</h1>

        <form action="" method="POST" enctype="multipart/form-data">
            <div style="margin-bottom: 20px;">
                <label style="display:block; margin-bottom:8px; font-weight: bold;">Food Name</label>
                <input type="text" name="title" placeholder="Food Title" style="width:100%; padding:12px; border-radius:8px; border:1px solid #ddd;" required>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display:block; margin-bottom:8px; font-weight: bold;">Description</label>
                <textarea name="description" rows="3" placeholder="Description of the food" style="width:100%; padding:12px; border-radius:8px; border:1px solid #ddd;"></textarea>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display:block; margin-bottom:8px; font-weight: bold;">Price (RM)</label>
                <input type="number" step="0.01" name="price" placeholder="0.00" style="width:100%; padding:12px; border-radius:8px; border:1px solid #ddd;" required>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display:block; margin-bottom:8px; font-weight: bold;">Select Image</label>
                <input type="file" name="image" style="width:100%;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display:block; margin-bottom:8px; font-weight: bold;">Category</label>
                <select name="category" style="width:100%; padding:12px; border-radius:8px; border:1px solid #ddd;" required>
                    <?php 
                        $res_cat = mysqli_query($conn, "SELECT * FROM category");
                        while($cat = mysqli_fetch_assoc($res_cat)) {
                            echo "<option value='".$cat['category_ID']."'>".$cat['category_details']."</option>";
                        }
                    ?>
                </select>
            </div>

            <div style="margin-bottom: 30px;">
                <label style="display:block; margin-bottom:8px; font-weight: bold;">Availability</label>
                <input type="radio" name="availability" value="1" checked> Yes 
                <input type="radio" name="availability" value="0" style="margin-left: 15px;"> No
            </div>

            <input type="submit" name="submit" value="Add Food to Menu" style="width: 100%; background: #2f3542; color: white; border: none; padding: 15px; border-radius: 8px; font-weight: bold; cursor: pointer;">
            
            <div class="text-center" style="margin-top: 15px;">
                <a href="manage-food.php" style="color: #747d8c; text-decoration: none;">Cancel</a>
            </div>
        </form>

        <?php 
            if(isset($_POST['submit'])) {
                $title = mysqli_real_escape_string($conn, $_POST['title']);
                $description = mysqli_real_escape_string($conn, $_POST['description']);
                $price = $_POST['price'];
                $category = $_POST['category'];
                $available = $_POST['availability'];

                // Handle Image Upload
                if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
                    $image_name = "Food_Name_".rand(000, 999).'.'.pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $source_path = $_FILES['image']['tmp_name'];
                    $destination_path = "../images/food/".$image_name;
                    move_uploaded_file($source_path, $destination_path);
                } else {
                    $image_name = "";
                }

                $sql_food = "INSERT INTO MENU (menu_name, menu_details, menu_price, menu_availability, menu_pict, category_ID) 
                             VALUES ('$title', '$description', $price, $available, '$image_name', $category)";
                
                if(mysqli_query($conn, $sql_food)) {
                    $_SESSION['add'] = "<div class='success text-center'>Food added successfully.</div>";
                    header('location:'.SITEURL.'admin/manage-food.php');
                    exit();
                }
            }
        ?>
    </div>
</div>

<?php include('partials/footer.php'); ?>
