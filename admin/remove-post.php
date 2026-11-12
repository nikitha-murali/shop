<?php
include "includes/config.php";

if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $sql = "DELETE FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    if ($stmt->execute()) {
        header("Location: post.php?status=deleted");
        exit();
    } else {
        echo "Error deleting product: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Product ID not provided";
}
$conn->close();
?>