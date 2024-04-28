
<?php
include_once 'partials/header.php';
include_once 'connection.php';
include_once 'upload.php';
?>
<main id="main" class="main">

    <div class="pagetitle">
        <h1>Form Elements</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item">Users</li>
                <li class="breadcrumb-item active">add-user</li>
            </ol>
        </nav>
    </div>
    <?php

     if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $fullname = $_POST['fullname'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $occupation = $_POST['occupation'];
        $phone = $_POST['number'];
        $password = $_POST['password'];
        $uploaded = $_FILES['image']['name'];

        $fullname = htmlspecialchars($fullname);
        $username = htmlspecialchars($username);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $phone = filter_var($phone, FILTER_SANITIZE_NUMBER_INT);
      
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        if (empty($fullname) || empty($username) || empty($email) || empty($phone) || empty($password) || empty($uploaded) || empty($occupation)) {
            $_SESSION['error'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-octagon me-1"></i>
                            All fields must be filled in.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
        } else {
            $num_length = strlen((string)$phone);
            if ($num_length < 10 || $num_length > 13) {
                $_SESSION['error'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-octagon me-1"></i>
                            Phone number should be of length 10 to 13 characters.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
            } else {
              
                $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
                $stmt->execute([$username, $email]);
                $count = $stmt->fetchColumn();

                if ($count > 0) {
                    $_SESSION['error'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-octagon me-1"></i>
                                User with the same username or email already exists.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>';
                } else {
                    
                    $imageUploadResult = uploadImage($_FILES["image"]);

                    if (!is_string($imageUploadResult)) {
                        
                        try {
                       
                            $stmt = $conn->prepare("INSERT INTO users (full_name, username, occupation, email, phone, password, image_name) VALUES (?, ?, ?, ?, ?, ?, ?)");
                            $stmt->execute([$fullname, $username,$occupation,$email, $phone, $hashedPassword, $imageUploadResult['name']]);

                            $_SESSION['success'] = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Registration successful!
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>';
                                        echo '<script type="text/javascript">window.location.href="users.php";</script>';
                                        
                        } catch (PDOException $e) {
                            
                            unlink("../images/" . $imageUploadResult['name']);
    
                            $_SESSION['error'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <i class="bi bi-exclamation-octagon me-1"></i>
                                            Error adding user to the database.
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>';
                                echo $occupation;
                        }
                    } else {
                      
                        $_SESSION['error'] = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="bi bi-exclamation-octagon me-1"></i>
                                    ' . $imageUploadResult . '
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>';
                                
                    }
                }
            }
        }
    }
    ?>

   
    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Fill in the users Details</h5>

                        <?php
                        if (isset($_SESSION['error'])) {
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                        };
                        ?>

                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="row mb-3">
                                <label for="inputText" class="col-sm-2 col-form-label">Full Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="fullname">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="inputText" class="col-sm-2 col-form-label">User Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="username">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="inputText" class="col-sm-2 col-form-label">Occupation</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="occupation">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" name="email">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="inputNumber" class="col-sm-2 col-form-label">Number</label>
                                <div class="col-sm-10">
                                    <input type="number" class="form-control" name="number">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="inputPassword" class="col-sm-2 col-form-label">Password</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" name="password">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="inputNumber" class="col-sm-2 col-form-label">File Upload</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="file" id="formFile" name="image">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-10">
                                    <button name="submit" type="submit" class="btn btn-success">Register</button>
                                    <a href="users.php" type="cancel" class="btn btn-danger">Cancel </a>
                                </div>
                            </div>

                        </form>



                    </div>
                </div>

            </div>


        </div>
    </section>

</main>
<?php
include_once 'partials/footer.php'
?>