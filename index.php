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
if (isset($_SESSION['order'])) {
    echo $_SESSION['order'];
    unset($_SESSION['order']);
}
?>

<section class="categories">
    <div class="container">
        <h2 class="text-center">Explore Foods</h2>

        <?php
        // 1. Fetch 3 Random Categories
        $sql = "SELECT * FROM (SELECT * FROM CATEGORY ORDER BY DBMS_RANDOM.VALUE) WHERE ROWNUM <= 3";
        $stid = oci_parse($conn, $sql);
        oci_execute($stid);
        $has_rows = false;

        while ($row = oci_fetch_array($stid, OCI_ASSOC + OCI_RETURN_NULLS + OCI_RETURN_LOBS)) {
            $has_rows = true;
            $id = $row['CATEGORY_ID'];

            $title = isset($row['CATEGORY_DETAILS']) ? $row['CATEGORY_DETAILS'] : "Category";

            $image_name = $row['CATEGORY_PICT'];
        ?>

            <a href="<?php echo SITEURL; ?>category-foods.php?category_id=<?php echo $id; ?>">
                <div class="box-3 float-container">
                    <?php
                    if ($image_name == "" || is_null($image_name)) {
                        echo "<div class='error'>Image not Available</div>";
                    } else {
                    ?>
                        <img src="<?php echo SITEURL; ?>images/category/<?php echo $image_name; ?>" alt="<?php echo $title; ?>" class="img-responsive img-curve" style="height: 250px; width: 100%; object-fit: cover;">
                    <?php
                    }
                    ?>
                    <h3 class="float-text text-white"><?php echo $title; ?></h3>
                </div>
            </a>

        <?php
        }

        if (!$has_rows) {
            echo "<div class='error'>Category not Added.</div>";
        }
        oci_free_statement($stid);
        ?>
        <div class="clearfix"></div>
    </div>
</section>

<section class="review-section">
    <div class="container">
        <h2 class="text-center" style="margin-bottom: 10px; color: #2f3542; font-weight: 800; font-size: 2rem;">What Our Customers Say</h2>

        <div class="review-container">

            <?php
            // SQL Query (Top 3 Latest)
            $sql_review = "SELECT * FROM (
                                SELECT f.FEEDBACK, c.CUST_FIRST_NAME, cat.FEEDBACK_CAT_NAME
                                FROM FEEDBACK f
                                JOIN CUSTOMER c ON f.CUST_ID = c.CUST_ID
                                JOIN FEEDBACK_CATEGORY cat ON f.FEEDBACK_CAT_ID = cat.FEEDBACK_CAT_ID
                                ORDER BY f.FEEDBACK_ID DESC
                           ) WHERE ROWNUM <= 3";

            $stid_review = oci_parse($conn, $sql_review);
            oci_execute($stid_review);

            $count_review = 0;

            while ($review = oci_fetch_assoc($stid_review)) {
                $count_review++;

                $feedback_text = $review['FEEDBACK'];
                if (is_object($feedback_text)) {
                    $feedback_text = $feedback_text->load();
                }
                if (strlen($feedback_text) > 120) {
                    $feedback_text = substr($feedback_text, 0, 120) . "...";
                }
                // -----------------------------
            ?>

                <div class="review-card">
                    <div class="quote-watermark">“</div>

                    <p class="review-text">
                        "<?php echo htmlspecialchars($feedback_text); ?>"
                    </p>

                    <div class="review-footer">
                        <div>
                            <h4 class="customer-name"><?php echo htmlspecialchars($review['CUST_FIRST_NAME']); ?></h4>
                            <small style="color: #a4b0be;">Verified Customer</small>
                        </div>
                        <span class="category-badge">
                            <?php echo htmlspecialchars($review['FEEDBACK_CAT_NAME']); ?>
                        </span>
                    </div>
                </div>
            <?php
            }

            if ($count_review == 0) {
                echo "<div style='text-align:center; padding: 40px; width: 100%; color:#a4b0be;'>";
                echo "<h3>No reviews yet. 😔</h3>";
                echo "<p>Be the first to order and share your experience!</p>";
                echo "</div>";
            }
            ?>

        </div>
    </div>
</section>

<?php include('partials-front/footer.php'); ?>

<style>
    .review-section {
        background-color: #f9f9fa;
        padding: 80px 0;
    }

    .review-container {
        display: flex;
        flex-wrap: wrap;
        gap: 30px;
        justify-content: center;
    }

    .review-card {
        background: white;
        padding: 35px 30px;
        border-radius: 20px;
        width: 100%;
        max-width: 350px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        transition: all 0.3s ease;
        position: relative;
        border: 1px solid #f0f0f0;
        overflow: hidden;
    }

    .review-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        border-color: #6347ffff;
    }

    .quote-watermark {
        position: absolute;
        top: -10px;
        left: 20px;
        font-size: 100px;
        font-family: serif;
        color: #ff4757;
        opacity: 0.05;
        pointer-events: none;
    }

    .review-text {
        color: #57606f;
        font-style: italic;
        line-height: 1.8;
        margin-bottom: 25px;
        font-size: 0.95rem;
        min-height: 80px;
        position: relative;
        z-index: 2;
    }

    .review-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-top: 1px solid #f1f2f6;
        padding-top: 20px;
    }

    .customer-name {
        font-weight: 700;
        color: #2f3542;
        font-size: 1rem;
        margin: 0;
    }

    .category-badge {
        font-size: 0.75rem;
        font-weight: 600;
        color: #ff4757;
        background: #ffeaa7;
        padding: 5px 12px;
        border-radius: 50px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>