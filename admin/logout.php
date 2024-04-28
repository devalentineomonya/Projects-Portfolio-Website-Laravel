<?php
session_start();
unset($_SESSION['current_user']);
session_destroy();
echo '<script type="text/javascript">window.location.href="login.php";</script>';
exit();
