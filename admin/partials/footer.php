<footer id="footer" class="footer">
    <div class="copyright">
        &copy; Copyright <strong><span>Ddevalacademy</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
        Designed by <a target="_blank" href="http://https://youtube.com/@devalentineomonya">DevalProjects</a>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" ></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootlint@1.1.0/dist/browser/bootlint.min.js"></script>
<script src="assets/js/main.js"></script>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
    function deleteImage(userId) {

        if (confirm("Are you sure you want to delete your profile image?")) {
            $.ajax({
                url: 'delete_image.php',
                type: 'POST',
                data: {
                    userId: userId
                },
                success: function(response) {
                 
                    window.location.reload();
                },
                error: function(error) {
            
                    console.log(error);
                }
            });
        }
    }
</script>


</body>

</html>