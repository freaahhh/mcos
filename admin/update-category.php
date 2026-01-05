<?php 
    ob_start(); 
    include('partials/menu.php'); 

    // 1. Initial Data Fetching
    if(isset($_GET['id'])) {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        // Oracle SQL for Category
        $sql = "SELECT * FROM category WHERE category_ID = :id";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ":id", $id);
        oci_execute($stmt);
        
        // Fetch using OCI_ASSOC and handle CLOB
        if($row = oci_fetch_array($stmt, OCI_ASSOC)) {
            $id = $row['CATEGORY_ID'];
            // Handle CLOB if CATEGORY_DETAILS is a large object
            $details = is_object($row['CATEGORY_DETAILS']) ? $row['CATEGORY_DETAILS']->load() : $row['CATEGORY_DETAILS'];
            $current_image = $row['CATEGORY_PICT'];
        } else {
            header('location:'.SITEURL.'admin/manage-category.php');
            exit();
        }
        oci_free_statement($stmt);
    } else {
        header('location:'.SITEURL.'admin/manage-category.php');
        exit();
    }

    // 2. Process Form Submission
    if(isset($_POST['submit'])) {
        $id = $_POST['id'];
        $details = $_POST['details']; // oci_bind_by_name handles escaping
        $current_image = $_POST['current_image'];

        // Image Upload Logic (remains similar to PHP logic)
        if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
            $parts = explode('.', $_FILES['image']['name']);
            $ext = end($parts);
            $image_name = "Food_Category_".rand(000, 999).'.'.$ext;
            $source_path = $_FILES['image']['tmp_name'];
            $destination_path = "../images/category/".$image_name;
            
            if(move_uploaded_file($source_path, $destination_path)) {
                if($current_image != "") {
                    $remove_path = "../images/category/".$current_image;
                    if(file_exists($remove_path)) { unlink($remove_path); }
                }
            }
        } else {
            $image_name = $current_image;
        }

        // Oracle Update using Bind Variables
        $sql2 = "UPDATE category SET category_details = :details, category_pict = :image_name WHERE category_ID = :id";
        $stmt2 = oci_parse($conn, $sql2);
        
        oci_bind_by_name($stmt2, ":details", $details);
        oci_bind_by_name($stmt2, ":image_name", $image_name);
        oci_bind_by_name($stmt2, ":id", $id);
        
        if(oci_execute($stmt2)) {
            $_SESSION['update'] = "<div class='success'>Category Updated Successfully.</div>";
            header('location:'.SITEURL.'admin/manage-category.php');
            exit();
        }
        oci_free_statement($stmt2);
    }
?>

<div class="main-content" style="background-color: #f1f2f6; padding: 3% 0;">
    <div class="wrapper" style="max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        
        <h1 class="text-center" style="color: #2f3542; margin-bottom: 30px;">Update Category</h1>

        <form action="" method="POST" enctype="multipart/form-data">
            <table class="table-no-border" style="width: 100%;">
                <tr style="height: 60px;">
                    <td style="width: 30%;"><strong>Details (Title):</strong></td>
                    <td><input type="text" name="details" class="form-control" value="<?php echo htmlspecialchars($details); ?>" style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ced4da;"></td>
                </tr>
                <tr>
                    <td style="vertical-align: top; padding-top: 20px;"><strong>Current Image:</strong></td>
                    <td style="padding-top: 20px;">
                        <?php if($current_image != "") { ?>
                            <img src="<?php echo SITEURL; ?>images/category/<?php echo $current_image; ?>" width="150px" style="border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                        <?php } else { echo "<div class='error'>Image Not Added.</div>"; } ?>
                    </td>
                </tr>
                <tr style="height: 80px;">
                    <td><strong>New Image:</strong></td>
                    <td><input type="file" name="image" class="form-control-file"></td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-top: 30px; text-align: center;">
                        <input type="hidden" name="current_image" value="<?php echo $current_image; ?>">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <input type="submit" name="submit" value="Update Category" class="btn-primary" style="padding: 12px 30px; border: none; background-color: #ff4757; color: white; border-radius: 5px; cursor: pointer; font-weight: bold;">
                        <a href="manage-category.php" style="margin-left: 15px; text-decoration: none; color: #747d8c;">Cancel</a>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>

<?php include('partials/footer.php'); ?>