<?php
session_start();
?>
<?php

$name = $_SESSION['fullname'] ;
$useremail = $_SESSION['useremail'];
$subject = $_SESSION['subject'];
$message = $_SESSION['message'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sending Mail</title>
    <style>

        @font-face {
            font-family: 'Graphik';
            src: url('./fonts/GraphikLight.otf') format('opentype');
        }

        body {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Graphik', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'Noto Sans', 'sans-serif', 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji';
        }

        .overlay {
            position: fixed;
            width: 100%;
            height: 100vh;
            background-color: #0f172a;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .wait-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .loader-container {
            position: relative;
            width: 80px;
            height: 80px;
        }

        .loader {
            position: absolute;
            width: 100%;
            height: 100%;
            border: 6px solid rgb(134 239 172);
            border-top: 6px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .waiting-message {
            color: rgb(134 239 172);
            text-align: center;
            font-size: 1.1rem;
            font-weight: 400;
            margin-top: 20px; /* Adjust the margin if necessary */
        }
        
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <form hidden id="contactForm" method="post" action="https://api.web3forms.com/submit">
        <input type="hidden" name="access_key" value="46e71153-727d-43b2-853b-6525fc87bc7f">
        <input type="text" name="name" value="<?php echo $name; ?>"><br>
        <input type="email" name="email" value="<?php echo $useremail; ?>"><br>
        <input type="text" name="subject" value="<?php echo $subject; ?>"><br>
        <textarea name="message" rows="4"><?php echo $message; ?></textarea><br>
    </form>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
    let submission = 0;
    const contactForm = document.getElementById('contactForm');

    if (submission === 0) {
        contactForm.submit();
        submission++;

        contactForm.setAttribute('disabled', true);
    }
});

    </script>
    <div class="overlay">
        <div class="wait-container">
            <div class="loader-container">
                <div class="loader"></div>
            </div>
            <div class="waiting-message">
                <h3 class="waiting text">Please wait as we send your email</h3>
            </div>
        </div>
    </div>
</body>

</html>

