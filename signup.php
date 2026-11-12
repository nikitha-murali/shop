<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
      integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65"
      crossorigin="anonymous"
    />
    <title>Sign up</title>
    <link rel="stylesheet" href="css/hmd-marketplace.css">
  </head>
  <body>
    <?php include __DIR__ . '/includes/hmd-header.php'; ?>
    <main class="auth-card registeration-box">
      <div class="logo-box">
        <strong style="font-size:1.55rem;color:#1034a6;">HandMeDown</strong>
      </div>
      <h1 class="signup-title">Please sign up</h1>
      <hr>
      <?php
      if (isset($_GET['error'])) {
          $error = urldecode($_GET['error']);
          echo '<p style="color: red; text-align: center;">' . htmlspecialchars($error) . '</p>';
      } elseif (isset($_GET['success'])) {
          echo '<p style="color: green; text-align: center;">Registration successful!</p>';
      }
      ?>
      <form action="includes/signup.inc.php" method="post" class="row g-3">
        <div class="col-md-6">
          <label for="inputAddress2" class="form-label">Full name</label>
          <input
            type="text"
            class="form-control"
            name="name"
            placeholder="Full name..."
            required
          />
        </main>
        <div class="col-md-6">
          <label for="inputAddress" class="form-label">Phone number</label>
          <input
            type="text"
            class="form-control"
            name="number"
            placeholder="+98 912 345 6789"
            required
          />
        </div>
        <div class="col-md-6">
          <label for="inputEmail4" class="form-label">College email</label>
          <input
            type="email"
            class="form-control"
            name="email"
            placeholder="name@presidencyuniversity.in"
            required
          />
        </div>
        <div class="col-md-6">
          <label for="inputAddress" class="form-label">Campus address</label>
          <input
            type="text"
            class="form-control"
            name="address"
            placeholder="Hostel or campus location"
            required
          />
        </div>
        <div class="col-md-6">
          <label for="hostel" class="form-label">Hostel</label>
          <input
            id="hostel"
            type="text"
            class="form-control"
            name="hostel"
            maxlength="100"
            required
          />
        </div>
        <div class="col-md-6">
          <label for="course" class="form-label">Course</label>
          <input
            id="course"
            type="text"
            class="form-control"
            name="course"
            maxlength="100"
            required
          />
        </div>
        <div class="col-md-6">
          <label for="semester" class="form-label">Semester</label>
          <input
            id="semester"
            type="text"
            class="form-control"
            name="semester"
            maxlength="50"
            required
          />
        </div>
        <div class="col-md-6">
          <label for="inputPassword4" class="form-label">Password</label>
          <input 
            type="password"
            class="form-control"
            name="pwd"
            placeholder="Password"
            required="required"
          />
        </div>
        <div class="col-md-6">
          <label for="inputPassword4" class="form-label">Confirm password</label>
          <input 
            type="password" 
            class="form-control" 
            name="rpwd"
            placeholder="Confirm password"
            required="required"
          />
        </div>
        <div class="col-12">
          <button 
            type="submit" 
            class="btn btn-primary"
            name="submit"
          >Sign up</button>
        </div>
      </form>
      <p class="text-center mt-3">Already have an account? <a href="login.php">Log in</a></p>
    </div>
    <script src="./js/jquery.js" type="text/javascript"></script>
    <script src="./js/bootstrap.min.js" type="text/javascript"></script>
  </body>
</html>