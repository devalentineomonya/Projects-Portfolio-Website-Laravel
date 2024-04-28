<?php
include_once 'partials/header.php';
include_once 'connection.php';
include_once 'upload.php';

$getUserLevel = $conn->prepare("SELECT security_level FROM users WHERE user_id = :user_id");
$getUserLevel->bindParam(':user_id', $_SESSION['current_user']);
$getUserLevel->execute();
$level = $getUserLevel->fetch(PDO::FETCH_ASSOC);


if (isset($_GET['id'])) {
  if ($level['security_level'] == 1) {
    $userId = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = :userId");
    $stmt->bindParam(':userId', $userId);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      if (isset($_POST['password']) && isset($_POST['newpassword']) && isset($_POST['renewpassword'])) {
        $currentPassword = $_POST['password'];
        $newPassword = $_POST['newpassword'];
        $renewPassword = $_POST['renewpassword'];

        if (password_verify($currentPassword, $user['password']) && $newPassword == $renewPassword && $newPassword != $currentPassword) {
          $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
          $updatePasswordStmt = $conn->prepare("UPDATE users SET password = :password WHERE user_id = :userId");
          $updatePasswordStmt->bindParam(':password', $hashedPassword);
          $updatePasswordStmt->bindParam(':userId', $userId);

          if ($updatePasswordStmt->execute()) {
            $_SESSION['success'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">Password changed successfully!</div>';
          } else {
            $_SESSION['delete_error'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Failed to update password!</div>';
          }
        } else {
          $_SESSION['delete_error'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">Invalid input for password change!</div>';
        }
      } elseif (isset($_POST['updateuser'])) {
      
        $fullName = $_POST['fullName'];
        $userName = $_POST['username'];
        $job = $_POST['job'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];

        $imagename = $user['image_name'];
        if ($_FILES["image"]["size"] > 0) {
          $imageUploadResult = uploadImage($_FILES["image"]);
          if (!is_string($imageUploadResult)) {
            $imagename = $imageUploadResult['name'];
          }
        }

        $updateStmt = $conn->prepare("UPDATE users SET full_name = :fullName, username = :userName, occupation = :job, phone = :phone, email = :email, image_name = :imagename WHERE user_id = :userId");
        $updateStmt->bindParam(':fullName', $fullName);
        $updateStmt->bindParam(':userName', $userName);
        $updateStmt->bindParam(':job', $job);
        $updateStmt->bindParam(':phone', $phone);
        $updateStmt->bindParam(':email', $email);
        $updateStmt->bindParam(':imagename', $imagename);
        $updateStmt->bindParam(':userId', $userId);

        if ($updateStmt->execute()) {
          $_SESSION['success'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">
         
          <i class="bi bi-exclamation-octagon me-1"></i>
          User updated successfully!
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
        } else {
          $_SESSION['delete_error'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="bi bi-exclamation-octagon me-1"></i>
          Update Failed!.
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
        }
      }
    }

    echo '<script type="text/javascript">window.location.href="users.php"</script>';
    exit();
  }else{
    $_SESSION['error'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-octagon me-1"></i>
    You don\'t have permission to access this service.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>';
  }
}

echo '<script type="text/javascript">window.location.href="users.php"</script>';
?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Profile</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item active">Profile</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <section class="section profile">
    <div class="row">
      <div class="col-xl-4">

        <div class="card">
          <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">

            <img src="../images/<?php echo $user['image_name'] ?>" alt="Profile" class="rounded-circle">
            <h2><?php echo $user['full_name'] ?></h2>
            <h3><?php echo $user['occupation'] ?></h3>
            <div class="social-links mt-2">
              <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
              <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
              <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
              <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
            </div>
          </div>
        </div>

      </div>

      <div class="col-xl-8">

        <div class="card">
          <div class="card-body pt-3">
            <!-- Bordered Tabs -->
            <ul class="nav nav-tabs nav-tabs-bordered">

              <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Overview</button>
              </li>

              <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit Profile</button>
              </li>

              <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Change Password</button>
              </li>

            </ul>
            <div class="tab-content pt-2">

              <div class="tab-pane fade show active profile-overview" id="profile-overview">
                <h5 class="card-title">Profile Details</h5>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label ">Full Name</div>
                  <div class="col-lg-9 col-md-8"><?php echo $user['full_name'] ?></div>
                </div>
                <div class="row">
                  <div class="col-lg-3 col-md-4 label ">User Name</div>
                  <div class="col-lg-9 col-md-8"><?php echo $user['username']; ?></div>
                </div>



                <div class="row">
                  <div class="col-lg-3 col-md-4 label">Job</div>
                  <div class="col-lg-9 col-md-8"><?php echo $user['occupation'] ?></div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label">Phone</div>
                  <div class="col-lg-9 col-md-8"><?php echo $user['phone'] ?></div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-md-4 label">Email</div>
                  <div class="col-lg-9 col-md-8"><?php echo $user['email']; ?></div>
                </div>

              </div>

              <div class="tab-pane fade profile-edit pt-3" id="profile-edit">

                <!-- Profile Edit Form -->
                <form method="post" enctype="multipart/form-data">
                  <div class="row mb-3">
                    <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                    <div class="col-md-8 col-lg-9">
                      <img src="../images/<?php echo $user['image_name'] ?>" alt="Profile">
                      <div class="pt-2">
                        <label class="input-group-btn">
                          <span class="btn btn-primary btn-sm">
                            <i class="bi bi-upload"></i><input type="file" name="image" style="display: none;">
                          </span>
                        </label>
                        <a onclick="deleteImage(<?php echo $userId ?>)" href="#" class="btn btn-danger btn-sm" title="Remove my profile image">
                          <i class="bi bi-trash"></i>

                        </a>
                      </div>
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Full Name</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="fullName" type="text" class="form-control" id="fullName" value="<?php echo $user['full_name'] ?>">
                    </div>
                  </div>
                  <div class="row mb-3">
                    <label for="fullName" class="col-md-4 col-lg-3 col-form-label">User Name</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="username" type="text" class="form-control" id="fullName" value="<?php echo $user['username']; ?>">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="Job" class="col-md-4 col-lg-3 col-form-label">Job</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="job" type="text" class="form-control" id="Job" value="<?php echo $user['occupation'] ?>">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="Phone" class="col-md-4 col-lg-3 col-form-label">Phone</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="phone" type="text" class="form-control" id="Phone" value="<?php echo $user['phone'] ?>">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="Email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="email" type="email" class="form-control" id="Email" value="<?php echo $user['email']; ?>">
                    </div>
                  </div>

                  <div class="text-center">
                    <button name="updateuser" type="submit" class="btn btn-primary">Save Changes</button>
                  </div>
                </form><!-- End Profile Edit Form -->

              </div>


              <div class="tab-pane fade pt-3" id="profile-change-password">
                <!-- Change Password Form -->
                <form method="post">

                  <div class="row mb-3">
                    <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Current Password</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="password" type="password" class="form-control" id="currentPassword">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New Password</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="newpassword" type="password" class="form-control" id="newPassword">
                    </div>
                  </div>

                  <div class="row mb-3">
                    <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Re-enter New Password</label>
                    <div class="col-md-8 col-lg-9">
                      <input name="renewpassword" type="password" class="form-control" id="renewPassword">
                    </div>
                  </div>

                  <div class="text-center">
                    <button name="changepass" type="submit" class="btn btn-primary">Change Password</button>
                  </div>
                </form><!-- End Change Password Form -->

              </div>

            </div><!-- End Bordered Tabs -->

          </div>
        </div>

      </div>
    </div>
  </section>

</main>

<?php
include_once 'partials/footer.php'
?>