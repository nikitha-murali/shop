<?php 
    include_once('./includes/headerNav.php');
    include_once('./includes/restriction.php');
    if(!(isset($_SESSION['logged-in']))){
      header("Location: login.php?دسترسی_غیرمجاز");
      exit();
    }
?>

<h1>کاربران</h1>
<hr>

<?php
// نمایش پیام‌های موفقیت یا خطا
if (isset($_GET['message'])) {
    echo '<div style="color: green; margin-bottom: 10px;">' . htmlspecialchars($_GET['message']) . '</div>';
}
if (isset($_GET['error'])) {
    echo '<div style="color: red; margin-bottom: 10px;">' . htmlspecialchars($_GET['error']) . '</div>';
}
?>

<?php
  include "includes/config.php";
     
  /* define how much data to show in a page from database */
  $limit = 4;
  if(isset($_GET['page'])){
    $page = $_GET['page'];
  } else {
    $page = 1;
  }
  // define serial number starting point
  $sn = ($page - 1) * $limit;
  
  // define from which row to start extracting data from database
  $offset = ($page - 1) * $limit;

  $sql = "SELECT * FROM customer LIMIT ?, ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ii", $offset, $limit);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) { ?>
    
    <div class="table-cont">
    <table class="table">
      <thead>
        <tr>
          <th scope="col">شماره</th>
          <th scope="col">نام</th>
          <th scope="col">تلفن</th>
          <th scope="col">آدرس</th>
          <th scope="col">نقش</th>
          <th scope="col">ویرایش</th>      
          <th scope="col">حذف</th>      
        </tr>
      </thead>
      <tbody class="table-group-divider">
<?php 
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $sn = $sn + 1;
?>
        <tr>
          <th scope="row"><?php echo $sn ?></th>
          <td><?php echo htmlspecialchars($row["customer_fname"]) ?></td>
          <td><?php echo htmlspecialchars($row["customer_phone"]) ?></td>    
          <td scope="row"><?php echo htmlspecialchars($row["customer_address"]) ?></td>
          <td><?php echo $row["customer_role"] == 'admin' ? 'مدیر' : 'عادی' ?></td>
          <td>
            <a class="fn_link" href="update-user.php?id=<?php echo $row["customer_id"] ?>">
              <i class='fa fa-edit'></i>
            </a>
          </td>          
          <td scope="row">
            <a class="fn_link" href="remove-user.php?id=<?php echo $row["customer_id"] ?>" onclick="return confirm('آیا مطمئن هستید که می‌خواهید این کاربر را حذف کنید؟')">
              <i class='fa fa-trash'></i>
            </a>
          </td>         
        </tr>
<?php 
    }
    $stmt->close();
  } else { 
    echo "بدون نتیجه"; 
  }
  $conn->close(); 
?>
      </tbody>
    </table>
    </div>

    <!-- Pagination -->
<?php
    include "includes/config.php"; 
    // Pagination btn using php with active effects 
    $sql1 = "SELECT * FROM customer";
    $result1 = mysqli_query($conn, $sql1) or die("خطا در کوئری.");

    if(mysqli_num_rows($result1) > 0){
        $total_products = mysqli_num_rows($result1);
        $total_page = ceil($total_products / $limit);
?>
    <nav aria-label="..." style="margin-left: 10px;">
      <ul class="pagination pagination-sm">
<?php 
        for($i=1; $i<=$total_page; $i++){
            // important: active effects to denote current page
            $active = ($page == $i) ? "active" : "";
?>
        <li class="page-item">
            <a class="page-link <?php echo $active; ?>" href="users.php?page=<?php echo $i; ?>">
            <?php echo $i; ?>
            </a>
        </li>
<?php 
        }
    }
?>
      </ul>
    </nav>