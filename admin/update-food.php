<?php 
    // 1. Process database logic BEFORE any HTML
    ob_start(); 
    include('partials/menu.php'); 

    // 2. Initial Data Fetching
    if(isset($_GET['id'])) {
        // Use filter_input for basic security on the ID
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if($id) {
            // Oracle SQL using Bind Variables
            $sql2 = "SELECT * FROM menu WHERE menu_ID = :id";
            $stmt2 = oci_parse($conn, $sql2);
            oci_bind_by_name($stmt2, ":id", $id);
            oci_execute($stmt2);
            
            // Oracle returns column keys in UPPERCASE
            if($row2 = oci_fetch_array($stmt2, OCI_ASSOC)) {
                $name = $row2['MENU_NAME'];
                // Handle CLOB for menu_details
                $description = is_object($row2['MENU_DETAILS']) ? $row2['MENU_DETAILS']->load() : $row2['MENU_DETAILS'];
                $price = $row2['MENU_PRICE'];
                $current_image = $row2['MENU_PICT'];
                $current_availability = $row2['MENU_AVAILABILITY'];
            } else {
                header('location:'.SITEURL.'admin/manage-food.php');
                exit();
            }
            oci_free_statement($stmt2);
        } else {
            header('location:'.SITEURL.'admin/manage-food.php');
            exit();
        }
    } else {
        header('location:'.SITEURL.'admin/manage-food.php');
        exit();
    }

    // 3. Process Form Submission
    if(isset($_POST['submit'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description']; // Bind variable handles escaping
        $price = $_POST['price'];
        $available = $_POST['available'];
        $current_image = $_POST['current_image'];

        // Handle Image Upload logic remains similar
        if(isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
            $tmp = explode('.', $_FILES['image']['name']);
            $ext = end($tmp);
            $image_name = "Food-Name-".rand(0000, 9999).'.'.$ext;
            
            $source_path = $_FILES['image']['tmp_name'];
            $destination_path = "../images/food/".$image_name;
            
            if(move_uploaded_file($source_path, $destination_path)) {
                if($current_image != "" && file_exists("../images/food/".$current_image)) {
                    unlink("../images/food/".$current_image);
                }
            }
        } else {
            $image_name = $current_image;
        }

        // 4. Update Oracle Database
        $sql3 = "UPDATE menu SET 
                    menu_name = :name, 
                    menu_details = :description, 
                    menu_price = :price, 
                    menu_pict = :image_name, 
                    menu_availability = :available 
                 WHERE menu_ID = :id";
        
        $stmt3 = oci_parse($conn, $sql3);
        
        // Bind all parameters securely
        oci_bind_by_name($stmt3, ":name", $name);
        oci_bind_by_name($stmt3, ":description", $description);
        oci_bind_by_name($stmt3, ":price", $price);
        oci_bind_by_name($stmt3, ":image_name", $image_name);
        oci_bind_by_name($stmt3, ":available", $available);
        oci_bind_by_name($stmt3, ":id", $id);
        
        if(oci_execute($stmt3)) {
            $_SESSION['update'] = "<div class='success'>Menu Updated Successfully.</div>";
            header('location:'.SITEURL.'admin/manage-food.php');
            exit();
        }
        oci_free_statement($stmt3);
    }
?>

<div class="main-content" style="background-color: #f1f2f6; padding: 3% 0;">
    <div class="wrapper" style="max-width: 800px; margin: 0 auto; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        
        <h1 class="text-center" style="color: #2f3542; margin-bottom: 30px;">Update Menu Item</h1>

        <form action="" method="POST" enctype="multipart/form-data">
            <table class="table-no-border" style="width: 100%;">
                <tr style="height: 60px;">
                    <td style="width: 30%;"><strong>Name:</strong></td>
                    <td><input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 5px;"></td>
                </tr>
                <tr>
                    <td style="padding-top: 15px; vertical-align: top;"><strong>Description:</strong></td>
                    <td style="padding-top: 15px;">
                        <textarea name="description" class="form-control" cols="30" rows="5" style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 5px;"><?php echo htmlspecialchars($description); ?></textarea>
                    </td>
                </tr>
                <tr style="height: 60px;">
                    <td><strong>Price (RM):</strong></td>
                    <td><input type="number" step="0.01" name="price" class="form-control" value="<?php echo $price; ?>" style="width: 100%; padding: 8px; border: 1px solid #ced4da; border-radius: 5px;"></td>
                </tr>
                <tr>
                    <td style="padding-top: 20px; vertical-align: top;"><strong>Current Image:</strong></td>
                    <td style="padding-top: 20px;">
                        <?php if($current_image != "") { ?>
                            <img src="<?php echo SITEURL; ?>images/food/<?php echo $current_image; ?>" width="150px" style="border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                        <?php } else { echo "<span class='error'>No Image Available</span>"; } ?>
                    </td>
                </tr>
                <tr style="height: 60px;">
                    <td><strong>New Image:</strong></td>
                    <td><input type="file" name="image"></td>
                </tr>
                <tr style="height: 60px;">
                    <td><strong>Available:</strong></td>
                    <td>
                        <input <?php if($current_availability==1) echo "checked"; ?> type="radio" name="available" value="1"> Yes 
                        <input <?php if($current_availability==0) echo "checked"; ?> type="radio" name="available" value="0" style="margin-left: 15px;"> No
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-top: 30px; text-align: center;">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <input type="hidden" name="current_image" value="<?php echo $current_image; ?>">
                        <input type="submit" name="submit" value="Update Menu" class="btn-primary" style="padding: 12px 30px; border: none; background-color: #ff4757; color: white; border-radius: 5px; cursor: pointer; font-weight: bold;">
                        <a href="manage-food.php" style="margin-left: 15px; text-decoration: none; color: #747d8c;">Cancel</a>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>

<?php include('partials/footer.php'); ?>