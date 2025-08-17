<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <!-- Css Style -->
    <link rel="stylesheet" href="/css/forgot-password_style.css">

</head>

<body>

    <div class="forgot-container">
        <div class="forgot-content">
            <div class="forgot-heading">
                <h1>Forgot Password</h1>
                <p>Please enter your email address to receive a password reset link.</p>
            </div>
            <form method="post" action="send-password-reset.php" class="forgot-form">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" placeholder="Enter your email" required>
                </div>
                <button type="submit">Send Reset Link</button>
            </form>
        </div>
    </div>

</body>

</html>