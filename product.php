<head>
    <style>
        .product_image_box {
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .pimage {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 16px;
        }
        #image-pr {
            /* width: 40%; height: 450px; */
        }
        .img-magnifier-container {
            position: absolute;
            /* width: 65%; height: 450px; */
        }
        .img-magnifier-glass {
            position: absolute;
            left: 25%;
            opacity: 0.1;
            border-radius: 5%;
            cursor: none;
            width: 20px;
            height: 20px;
        }
        .img-magnifier-glass:hover {
            opacity: 1;
            border-radius: 10%;
            cursor: none;
            width: 130px;
            height: 130px;
        }
    </style>

    <script>
        function magnify(imgID, zoom) {
            var img, glass, w, h, bw;
            img = document.getElementById(imgID);
            glass = document.createElement("DIV");
            glass.setAttribute("class", "img-magnifier-glass");
            img.parentElement.insertBefore(glass, img);
            glass.style.backgroundImage = "url('" + img.src + "')";
            glass.style.backgroundRepeat = "no-repeat";
            glass.style.backgroundSize = (img.width * zoom) + "px " + (img.height * zoom) + "px";
            bw = 3;
            w = glass.offsetWidth / 2;
            h = glass.offsetHeight / 2;
            glass.addEventListener("mousemove", moveMagnifier);
            img.addEventListener("mousemove", moveMagnifier);
            glass.addEventListener("touchmove", moveMagnifier);
            img.addEventListener("touchmove", moveMagnifier);
            function moveMagnifier(e) {
                var pos, x, y;
                e.preventDefault();
                pos = getCursorPos(e);
                x = pos.x;
                y = pos.y;
                if (x > img.width - (w / zoom)) {x = img.width - (w / zoom);}
                if (x < w / zoom) {x = w / zoom;}
                if (y > img.height - (h / zoom)) {y = img.height - (h / zoom);}
                if (y < h / zoom) {y = h / zoom;}
                glass.style.left = (x - w) + "px";
                glass.style.top = (y - h) + "px";
                glass.style.backgroundPosition = "-" + ((x * zoom) - w + bw) + "px -" + ((y * zoom) - h + bw) + "px";
            }
            function getCursorPos(e) {
                var a, x = 0, y = 0;
                e = e || window.event;
                a = img.getBoundingClientRect();
                x = e.pageX - a.left;
                y = e.pageY - a.top;
                x = x - window.pageXOffset;
                y = y - window.pageYOffset;
                return {x: x, y: y};
            }
        }
    </script>
</head>

<?php
include "includes/config.php";

// تابع برای فرمت قیمت به تومان
function formatPrice($price) {
    return number_format($price, 0, '.', ',') . ' تومان';
}

$sql = "SELECT * FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_GET['id']);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<div class="product_image_box">
    <div class="img-magnifier-container" style="width: 18rem;">
        <img class="pimage" id='image-pr' src="admin/upload/<?php echo $row['product_img'] ?>" alt="تصویر محصول">
    </div>
</div>

<script>
    magnify("image-pr", 3);
</script>

<script src="./js/increament.js"></script>