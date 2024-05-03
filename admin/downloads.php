<?php
include_once 'partials/header.php';
?>

<main id="main" class="main">

  <div class="pagetitle">
    <h1>Downloads</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item active">Downloads</li>
      </ol>
    </nav>
  </div>
  <?php
  if(isset($_SESSION['delete_error'])){
      echo $_SESSION['delete_error'];
      unset($_SESSION['delete_error']);
  }
  ?>
  <section class="section dashboard">
    <!--  -->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body pb-3">
            <h5 class="card-title">Downloads <span id="filterLabel">| All</span></h5>
            <div id="newsContainer" class="news">
              <?php
              $stmt = $conn->prepare("SELECT * FROM downloads LIMIT 10");
              $stmt->execute();

              if ($stmt) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                  $downloadDate = new DateTime($row['created_at']);
              ?>
                  <div class="post-item clearfix pb-3 pt-3">
                    <div class="download-grid">
                      <h4 class="ml-0">User IP: <span><span><?php echo $row['userIp']; ?></span></span></h4>
                      <h4 class="ml-0">Browser: <span><?php echo $row['browser']; ?></span></h4>
                      <h4 class="ml-0">Device: <span><?php echo $row['device']; ?></span></h4>
                      <h4 class="ml-0">City: <span><?php echo $row['city']; ?></span></h4>
                      <h4 class="ml-0">Region: <span><?php echo $row['region']; ?></span></h4>
                      <h4 class="ml-0">Country: <span><?php echo $row['country']; ?></span></h4>
                      <h4 class="ml-0">Latitude: <span><?php echo $row['latitude']; ?></span></h4>
                      <h4 class="ml-0">Longitude: <span><?php echo $row['longitude']; ?></span></h4>
                      <h4 class="ml-0">Organization: <span><?php echo $row['organization']; ?></span></h4>
                      <h4 class="ml-0">Downloads: <span><?php echo $row['downloads']; ?></span></h4>
                      <h4 class="ml-0">Date: <span><?php echo $downloadDate->format('F j, Y, g:i a'); ?></span></h4>
                    </div>

                    <div class="mt-2">
                      <button type="button" class="btn btn-danger btn-delete" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-download-id="<?php echo $row['id']; ?>">
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
    </div>


  </section>

</main>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
  $(document).ready(function() {
    // Handle delete button click
    $('.btn-delete').click(function() {
      var downloadId = $(this).data('download-id');
      var confirmDelete = confirm('Are you sure you want to delete this record?');

      if (confirmDelete) {
        // Perform AJAX request to delete the record
        $.ajax({
          url: 'delete-download.php', // Replace with your delete script
          method: 'POST',
          data: {
            downloadId: downloadId
          },
          success: function(response) {
            // Reload the page or update the table as needed
            location.reload();
          },
          error: function(xhr, status, error) {
            console.error(xhr.responseText);
          }
        });
      }
    });
  });
</script>

<?php
include_once 'partials/footer.php';
?>