<?php
include_once 'partials/header.php';
include_once 'connection.php';
?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Users</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item active">Users</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->


  <div class="card">
    <div class="card-body pt-3">
      <a href="add-user.php" type="button" class="btn btn-outline-success">Add new User</a>
    </div>
  </div>
  <div class="card">
    <?php
    if (isset($_SESSION['success'])) {
      echo $_SESSION['success'];
      unset($_SESSION['success']);
    }
    if (isset($_SESSION['delete_error'])) {
      echo $_SESSION['delete_error'];
      unset($_SESSION['delete_error']);
    }
    ?>
  </div>
  <section class="section dashboard">
    <div class="row d-none d-md-block">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body pt-3">

            <table class="table datatable">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Full Name</th>
                  <th>Username</th>
                  <th>Occupation</th>
                  <th>Email</th>
                  <th>Phone</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $sql = "SELECT * FROM users";
                $stmt = $conn->prepare($sql);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $userid = 1;
                foreach ($result as $row) {
                ?>
                  <tr>
                    <td><?php echo $userid ?></td>
                    <td><?php echo $row['full_name'] ?></td>
                    <td><?php echo $row['username'] ?></td>
                    <td><?php echo $row['occupation'] ?></td>
                    <td><?php echo $row['email'] ?></td>
                    <td><?php echo $row['phone'] ?></td>
                    <td>

                      <!-- Edit button -->
                      <a href="profile.php?id=<?php echo $row['user_id']; ?>" class="btn btn-primary">Edit</a>
                      <button type="button" class="btn btn-danger btn-delete" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-user-id="<?php echo $row['user_id']; ?>">
                        Delete
                      </button>

                    </td>
                  </tr>
                <?php
                  $userid++;
                }
                ?>
              </tbody>
            </table>

          </div>
        </div>

      </div>
    </div>

    <div class="row  d-block d-md-none">
      <div class="col-lg-4">
        <div class="card">
          <div class="card-body pb-3">
            <h5 class="card-title">Users <span id="filterLabel">| All</span></h5>
            <div id="newsContainer" class="news">
              <?php
              $stmt = $conn->prepare("SELECT * FROM users");
              $stmt->execute();

              if ($stmt) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
              ?>
                  <div class="post-item clearfix pt-3 p-3">
                    <img src="../images/<?php echo $row['image_name'] ?>" alt="">
                    <h4><?php echo $row['full_name'] ?></h4>
                    <h4><?php echo $row['username'] ?></h4>
                    <h4><?php echo $row['occupation'] ?></h4>
                    <h4><?php echo $row['email'] ?></h4>
                    <h4><?php echo $row['phone'] ?></h4>

                    <div class="mt-2">
                      <a href="profile.php?id=<?php echo $row['user_id']; ?>" class="btn btn-primary">Edit</a>
                      <button type="button" class="btn btn-danger btn-delete" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-user-id="<?php echo $row['user_id']; ?>">
                        Delete
                      </button>
                    </div>
                  </div>



              <?php
                }
              }
              ?>
            </div>

          </div>
        </div>
      </div>


      </script>
    </div>

  </section>
</main>

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Confirm Delete</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this user?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" id="continueBtn" class="btn btn-danger">Continue</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
  $(document).ready(function() {
    var confirmDeleteModal = $('#confirmDeleteModal');
    var userId;
    $('.btn-delete').click(function() {
      userId = $(this).data('user-id');
      confirmDeleteModal.modal('show');
    });

    $('#continueBtn').click(function() {
      $.ajax({
        url: 'delete-user.php',
        method: 'POST',
        data: {
          userId: userId
        },
        success: function(response) {
          location.reload();
        },
        error: function(xhr, status, error) {
          console.error(xhr.responseText);
        }
      });
    });
  });
</script>


<?php
include_once 'partials/footer.php'
?>