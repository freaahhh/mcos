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
                        // Oracle Query: Using oci_parse and oci_execute
                        $sql_cat = "SELECT * FROM CATEGORY";
                        $stmt_cat = oci_parse($conn, $sql_cat);
                        oci_execute($stmt_cat);
                        
                        // Fetching with UPPERCASE keys for Oracle
                        while($cat = oci_fetch_array($stmt_cat, OCI_ASSOC)) {
                            echo "<option value='".$cat['CATEGORY_ID']."'>".$cat['CATEGORY_DETAILS']."</option>";
                        }
                        oci_free_statement($stmt_cat);
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
                // Get data from Form
                $title = $_POST['title'];
                $description = $_POST['description'];
                $price = $_POST['price'];
                $category = $_POST['category'];
                $available = $_POST['availability'];

                // Handle Image Upload logic remains similar
                if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
                    $image_name = "Food_Name_".rand(000, 999).'.'.pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $source_path = $_FILES['image']['tmp_name'];
                    $destination_path = "../images/food/".$image_name;
                    move_uploaded_file($source_path, $destination_path);
                } else {
                    $image_name = "";
                }

                // Oracle Insert Statement using Bind Variables
                $sql_food = "INSERT INTO MENU (MENU_NAME, MENU_DETAILS, MENU_PRICE, MENU_AVAILABILITY, MENU_PICT, CATEGORY_ID) 
                             VALUES (:title, :description, :price, :available, :image_name, :category)";
                
                $stmt_food = oci_parse($conn, $sql_food);

                // Bind parameters to handle data types and security
                oci_bind_by_name($stmt_food, ":title", $title);
                oci_bind_by_name($stmt_food, ":description", $description);
                oci_bind_by_name($stmt_food, ":price", $price);
                oci_bind_by_name($stmt_food, ":available", $available);
                oci_bind_by_name($stmt_food, ":image_name", $image_name);
                oci_bind_by_name($stmt_food, ":category", $category);
                
                if(oci_execute($stmt_food)) {
                    $_SESSION['add'] = "<div class='success text-center' style='color: #2ecc71;'>Food added successfully.</div>";
                    header('location:'.SITEURL.'admin/manage-food.php');
                    exit();
                } else {
                    $e = oci_error($stmt_food);
                    $_SESSION['add'] = "<div class='error text-center' style='color: #ff4757;'>Failed to add food: " . $e['message'] . "</div>";
                }
                oci_free_statement($stmt_food);
            }
        ?>
    </div>
</div>

<?php include('partials/footer.php'); ?>