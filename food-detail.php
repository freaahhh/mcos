<?php
ob_start();
include('partials-front/menu.php'); // Pastikan dbconnect ada dalam ni

// 1. Check ID
if (!isset($_GET['food_id'])) {
    header('location:' . SITEURL);
    exit();
}

$food_id = $_GET['food_id'];

// --- FETCH FOOD DETAILS ---
$sql = "SELECT * FROM MENU WHERE MENU_ID = :food_id";
$stid = oci_parse($conn, $sql);
oci_bind_by_name($stid, ":food_id", $food_id);
oci_execute($stid);

// 2. FIX UTAMA: Tambah '+ OCI_RETURN_NULLS'
// Ini arahan: "Oracle, kalau data kosong, bagi je NULL, jangan hilangkan column tu!"
$food = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS);
oci_free_statement($stid);

if (!$food) {
    header('location:' . SITEURL);
    exit();
}

// 3. MAP DATA (Selamat dari Error)
$title = $food['MENU_NAME'];
$price = $food['MENU_PRICE'];
$image_name = $food['MENU_PICT'];

// Kalau details NULL, kita letak ayat default
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

        <!--
        <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <h3 style="border-bottom: 2px solid #f1f2f6; padding-bottom: 15px; margin-bottom: 25px; color: #2f3542;">Customer Reviews</h3>

            <?php
            // Query Review
            $sql_reviews = "SELECT f.FEEDBACK, f.CREATED_AT, c.CUST_FIRST_NAME 
                            FROM FEEDBACK f 
                            JOIN CUSTOMER c ON f.CUST_ID = c.CUST_ID 
                            WHERE f.ORDER_ID IN (
                                SELECT ORDER_ID FROM ORDER_MENU WHERE MENU_ID = :food_id
                            )
                            ORDER BY f.CREATED_AT DESC";

            $stid_reviews = oci_parse($conn, $sql_reviews);
            oci_bind_by_name($stid_reviews, ":food_id", $food_id);
            oci_execute($stid_reviews);

            $reviews_exist = false;

            // Tambah OCI_RETURN_NULLS kat sini juga supaya tak error kalau feedback kosong
            while ($review = oci_fetch_array($stid_reviews, OCI_ASSOC + OCI_RETURN_LOBS + OCI_RETURN_NULLS)) {
                $reviews_exist = true;
            ?>
                <div style="border-bottom: 1px solid #f1f2f6; padding: 20px 0;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <strong style="color: #2f3542;"><?php echo $review['CUST_FIRST_NAME']; ?></strong>
                        <small style="color: #a4b0be;"><?php echo $review['CREATED_AT']; ?></small>
                    </div>
                    <p style="color: #57606f; font-style: italic; margin: 0;">
                        "<?php echo isset($review['FEEDBACK']) ? $review['FEEDBACK'] : ''; ?>"
                    </p>
                </div>
            <?php }

            if (!$reviews_exist) {
                echo "<p style='color: #a4b0be; text-align: center; padding: 20px;'>No reviews for this item yet.</p>";
            }
            oci_free_statement($stid_reviews);
            ?>
        </div>
        -->

    </div>
</div>

<?php include('partials-front/footer.php'); ?>