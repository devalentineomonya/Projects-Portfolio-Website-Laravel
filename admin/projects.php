<?php
include_once 'partials/header.php';
?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Projects</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item active">Projects</li>
      </ol>
    </nav>
  </div><!-- End Page Title -->

  <div class="card">
    <div class="card-body pt-3">
      <a href="add-project.php" type="button" class="btn btn-outline-success">Add new Project</a>
    </div>
  </div>
  <div class="card">
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
    <div class="row   d-none d-md-block">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body pt-3">
            <table class="table datatable">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>
                    <b>N</b>ame
                  </th>
                  <th>Languages</th>
                  <th>Category</th>
                  <th data-type="date" data-format="YYYY/DD/MM">Created At</th>
                  <th data-type="date" data-format="YYYY/DD/MM">Updated At</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $stmt = $conn->prepare("SELECT * FROM projects");
                $stmt->execute();
                $projectId = 1;
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  $dateCreatedArr = new DateTime($row['created_at']);
                  $dateCreated = $dateCreatedArr->format('F j, Y, g:i a');
                  $dateUpdatedArr = new DateTime($row['updated_at']);
                  $dateUpdated = $dateUpdatedArr->format('F j, Y, g:i a');
                ?>
                  <tr>
                    <td><?php echo $projectId ?></td>
                    <td><?php echo $row['title'] ?></td>
                    <td><?php echo $row['language'] ?></td>
                    <td><?php echo $row['category'] ?></td>
                    <td><?php echo $dateCreated ?></td>
                    <td><?php echo $dateUpdated ?></td>
                    <td>
                      <a href="add-project.php?id=<?php echo $row['project_id']; ?>" class="btn btn-primary">Edit</a>
                      <button type="button" class="btn btn-danger btn-delete" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-project-id="<?php echo $row['project_id']; ?>">
                        Delete
                      </button>
                    </td>
                  </tr>

                  <!-- Modal for each project -->

                <?php
                  $projectId++;
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
          <div class="filter">
            <a id="filterDropdown" class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
              <li class="dropdown-header text-start">
                <h6>Filter</h6>
              </li>
              <li><a class="dropdown-item" href="#" onclick="filterData('all')">All</a></li>
              <li><a class="dropdown-item" href="#" onclick="filterData('today')">Today</a></li>
              <li><a class="dropdown-item" href="#" onclick="filterData('this_month')">This Month</a></li>
              <li><a class="dropdown-item" href="#" onclick="filterData('this_year')">This Year</a></li>
            </ul>
          </div>

          <div class="card-body pb-3">
            <h5 class="card-title">Projects Updates <span id="filterLabel">| All</span></h5>
            <div id="newsContainer" class="news">
              <?php
              $stmt = $conn->prepare("SELECT * FROM projects LIMIT 10");
              $stmt->execute();

              if ($stmt) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  $dateCreatedArr = new DateTime($row['created_at']);
                  $dateCreated = $dateCreatedArr->format('F j, Y, g:i a');
                  $dateUpdatedArr = new DateTime($row['updated_at']);
                  $dateUpdated = $dateUpdatedArr->format('F j, Y, g:i a');
              ?>
                  <div class="post-item clearfix pt-3 p-3">
                    <img src="../images/<?php echo $row['image_name'] ?>" alt="">
                    <h4><a href="#" onclick="openDownload('<?php echo urlencode($row['title']) ?>')"><?php echo $row['title'] ?></a></h4>
                    <h4><?php echo $row['language'] ?></h4>
                    <h4><?php echo $row['category'] ?></h4>
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
                      <a href="add-project.php?id=<?php echo $row['project_id']; ?>" class="btn btn-primary">Edit</a>
                      <button type="button" class="btn btn-danger btn-delete" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-project-id="<?php echo $row['project_id']; ?>">
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
        Are you sure you want to delete this Project?
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
  function filterData(filter) {
    var filterLabel = $('#filterLabel');
    var filterDropdown = $('#filterDropdown');

    filterLabel.text('| ' + filter.replace('_', ' '));

    $.ajax({
      url: 'filter.php',
      type: 'GET',
      data: {
        filter: filter
      },
      success: function(data) {
        $('#newsContainer').html(data);
      }
    });
  }

  $(document).ready(function() {
    var confirmDeleteModal = $('.modal');
    var projectId;

    $('.btn-delete').click(function() {
      projectId = $(this).data('project-id');
      var modalId = '#confirmDeleteModal_' + projectId;
      $(modalId).modal('show');
    });

    $('.continueBtn').click(function() {
      $.ajax({
        url: 'delete-project.php',
        method: 'POST',
        data: {
          projectId: projectId
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