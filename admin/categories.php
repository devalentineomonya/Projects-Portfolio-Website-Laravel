<?php
include_once 'partials/header.php';
?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Categories</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item active">Categories</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->


    <div class="card">
      <div class="card-body pt-3">
        <a href="add-categories.php" type="button" class="btn btn-outline-success">Add new Category</a>
      </div>
    </div>
  <div class="card bg-transparent">
    <?php
    if (isset($_SESSION['success'])) {
      echo $_SESSION['success'];
      unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
      echo $_SESSION['error'];
      unset($_SESSION['error']);
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
                  <th><b>N</b>ame</th>
                  <th>Description</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $stmt = $conn->prepare("SELECT category_id, name, description FROM categories");
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $catid = 1;
                foreach ($result as $row) {

                ?>
                  <tr>
                    <td><?php echo $catid; ?></td>
                    <td><?php echo $row['name'] ?></td>
                    <td class="description-column">
                      <textarea class="form-control border-0" style="height: 30px; min-width:400px" readonly><?php echo $row['description'] ?></textarea>
                    </td>
                    <td>
                      <a href="add-categories.php?id=<?php echo $row['category_id']; ?>" class="btn btn-primary">Edit</a>
                      <button type="button" class="btn btn-danger btn-delete" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-cat-id="<?php echo $row['category_id']; ?>">
                        Delete
                      </button>
                    </td>
                  </tr>
                <?php
                  $catid += 1;
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
            <h5 class="card-title">Projects Categories <span id="filterLabel">| All</span></h5>
            <div id="newsContainer" class="news">
              <?php
              $stmt = $conn->prepare("SELECT * FROM categories LIMIT 10");
              $stmt->execute();

              if ($stmt) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  $dateCreatedArr = new DateTime($row['created_at']);
                  $dateUpdatedArr = new DateTime($row['updated_at']);
                  $dateUpdated = $dateUpdatedArr->format('F j, Y, g:i a');
                  $dateCreated = $dateCreatedArr->format('F j, Y, g:i a');

              ?>
                  <div class="post-item clearfix pb-3 pt-3">
                    <img src="../images/<?php echo $row['image_name'] ?>" alt="">
                    <h4><a href="#"><?php echo $row['name'] ?></a></h4>
                    <h4><?php echo $dateCreated ?></h4>
                    <h4><?php echo $dateUpdated ?></h4>

                    <script>
                      function openDownload(projectTitle) {
                        var baseUrl = window.location.origin;
                        var downloadUrl = baseUrl + '/download.php?project=' + projectTitle;

                        window.location.href = downloadUrl;
                      }
                    </script>
                    <div class="mt-2">
                      <a href="add-categories.php?id=<?php echo $row['category_id']; ?>" class="btn btn-primary">Edit</a>
                      <button type="button" class="btn btn-danger btn-delete" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-cat-id="<?php echo $row['category_id']; ?>">
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
        Are you sure you want to delete this Category?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-danger continueBtn">Continue</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
  $(document).ready(function() {
    var confirmDeleteModal = $('#confirmDeleteModal');
    var catId;

    $('.btn-delete').click(function() {
      catId = $(this).data('cat-id');
      confirmDeleteModal.modal('show');
    });

    $('.continueBtn').click(function() {
      $.ajax({
        url: 'delete-category.php',
        method: 'POST',
        data: {
          catId: catId
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
include_once 'partials/footer.php';
?>