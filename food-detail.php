<?php
ob_start();
include('partials-front/menu.php');

if (!isset($_GET['food_id'])) {
    header('location:' . SITEURL);
    exit();
}

$food_id = $_GET['food_id'];

$sql = "SELECT * FROM MENU WHERE MENU_ID = :food_id";
$stid = oci_parse($conn, $sql);
oci_bind_by_name($stid, ":food_id", $food_id);
oci_execute($stid);

$food = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
oci_free_statement($stid);

if (!$food) {
    header('location:' . SITEURL);
    exit();
}

$title = $food['MENU_NAME'];
$price = $food['MENU_PRICE'];
$image_name = $food['MENU_PICT'];
$details = isset($food['MENU_DETAILS']) ? $food['MENU_DETAILS'] : "No description available for this item.";

?>

<div class="main-content" style="background-color: #f1f2f6; padding: 50px 0;">
    <div class="container" style="max-width: 1000px; margin: 0 auto; padding: 0 20px;">

        <div style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); display: flex; flex-wrap: wrap; margin-bottom: 40px;">

            <div style="flex: 1; min-width: 300px;">
                <?php if ($image_name == "" || is_null($image_name)): ?>
                    <div class='error' style="padding: 100px; text-align: center; background: #eee;">Image not Available.</div>
                <?php else: ?>
                    <img src="<?php echo SITEURL; ?>images/food/<?php echo $image_name; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                <?php endif; ?>
            </div>

            <div style="flex: 1; padding: 40px; min-width: 300px;">
                <h2 style="color: #2f3542; margin-top: 0;"><?php echo $title; ?></h2>
                <p style="font-size: 1.5rem; color: #ff4757; font-weight: bold; margin: 20px 0;">RM <?php echo number_format($price, 2); ?></p>

                <h4 style="color: #747d8c; margin-bottom: 10px;">Description</h4>
                <p style="line-height: 1.6; color: #57606f; margin-bottom: 30px;">
                    <?php echo $details; ?>
                </p>

                <form action="add-to-cart.php" method="POST">
                    <input type="hidden" name="menu_id" value="<?php echo $food_id; ?>">

                    <div style="margin-bottom: 20px;">
                        <label style="font-weight: bold; display: block; margin-bottom: 5px;">Quantity</label>
                        <input type="number" name="quantity" value="1" min="1" style="padding: 10px; width: 80px; border-radius: 5px; border: 1px solid #ddd;">
                    </div>

                    <button type="submit" name="add_to_cart" class="btn-primary" style="background: #2f3542; color: white; padding: 15px 30px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; width: 100%;">Add to Cart</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('partials-front/footer.php'); ?>