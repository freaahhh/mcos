<?php 
ob_start();
include('partials/menu.php'); 
?>

<div class="main-content" style="background-color: #f1f2f6; padding: 3% 0;">
    <div class="wrapper" style="max-width: 600px; margin: 0 auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <h1 class="text-center" style="color: #2f3542; margin-bottom: 30px;">Add New Category</h1>

        <?php 
            if(isset($_SESSION['add'])) {
                echo $_SESSION['add'];
                unset($_SESSION['add']);
            }

            if(isset($_SESSION['upload'])) {
                echo $_SESSION['upload'];
                unset($_SESSION['upload']);
            }
        ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div style="margin-bottom: 20px;">
                <label style="display:block; margin-bottom:8px; font-weight: bold;">Category Title (Details)</label>
                <input type="text" name="details" placeholder="e.g. Beverages, Main Course" style="width:100%; padding:12px; border-radius:8px; border:1px solid #ddd;" required>
            </div>

            <div style="margin-bottom: 30px;">
                <label style="display:block; margin-bottom:8px; font-weight: bold;">Category Image</label>
                <input type="file" name="image" style="width:100%;">
                <p style="font-size: 0.8rem; color: #a4b0be; margin-top: 5px;">Recommended size: 400x400px (JPG/PNG)</p>
            </div>

            <input type="submit" name="submit" value="Create Category" style="width: 100%; background: #2f3542; color: white; border: none; padding: 15px; border-radius: 8px; font-weight: bold; cursor: pointer;">
            
            <div class="text-center" style="margin-top: 15px;">
                <a href="manage-category.php" style="color: #747d8c; text-decoration: none;">Cancel</a>
            </div>
        </form>

        <?php 
            if(isset($_POST['submit'])) {
                // 1. Get data from Form
                $details = $_POST['details']; 

                // 2. Handle Image Upload
                if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
                    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $image_name = "Food_Category_".rand(000, 999).'.'.$ext;
                    $source_path = $_FILES['image']['tmp_name'];
                    $destination_path = "../images/category/".$image_name;

                    $upload = move_uploaded_file($source_path, $destination_path);

                    if($upload == false) {
                        $_SESSION['upload'] = "<div class='error text-center'>Failed to upload image.</div>";
                        header('location:'.SITEURL.'admin/add-category.php');
                        exit();
                    }
                } else {
                    $image_name = "";
                }

                // 3. Oracle Insert Statement
                // Note: Bind variables (:details, :image_name) prevent SQL Injection and replace mysqli_real_escape_string
                $sql = "INSERT INTO CATEGORY (CATEGORY_DETAILS, CATEGORY_PICT) 
                        VALUES (:details, :image_name)";
                
                $stmt = oci_parse($conn, $sql);

                // Bind the parameters
                oci_bind_by_name($stmt, ":details", $details);
                oci_bind_by_name($stmt, ":image_name", $image_name);

                // Execute the statement
                $res = oci_execute($stmt); 

                if($res) {
                    $_SESSION['add'] = "<div class='success text-center' style='color: #2ecc71;'>Category added successfully.</div>";
                    header('location:'.SITEURL.'admin/manage-category.php');
                    exit();
                } else {
                    $e = oci_error($stmt); // Get the Oracle error
                    $_SESSION['add'] = "<div class='error text-center' style='color: #ff4757;'>Failed to add category: " . $e['message'] . "</div>";
                    header('location:'.SITEURL.'admin/add-category.php');
                    exit();
                }
                
                oci_free_statement($stmt);
            }
        ?>
    </div>
</div>

<?php include('partials/footer.php'); ?>