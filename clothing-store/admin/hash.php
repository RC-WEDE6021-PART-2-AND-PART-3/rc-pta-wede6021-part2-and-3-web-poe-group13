<?php

// Simple password hashing tool (run once in browser)

if(isset($_POST['password'])) {
    $password = $_POST['password'];

    $hash = password_hash($password, PASSWORD_DEFAULT);
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Password Hash Generator</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="auth-container">

    <div class="auth-hero">
        <div>
            <h1>Password Hash Tool</h1>
            <p>Generate secure bcrypt hashes for your database</p>
        </div>
    </div>

    <div class="auth-card">

        <h2>Generate Hash</h2>

        <form method="POST">

            <div class="form-row">
                <label>Enter Password</label>
                <input type="text" name="password" required>
            </div>

            <div class="form-actions">
                <button class="btn">Generate</button>
            </div>

        </form>

        <?php if(isset($hash)): ?>
            <div class="success" style="margin-top:15px;">
                <strong>Generated Hash:</strong><br><br>
                <code style="word-break:break-all;">
                    <?php echo $hash; ?>
                </code>
            </div>
        <?php endif; ?>

    </div>

</div>

</body>
</html>