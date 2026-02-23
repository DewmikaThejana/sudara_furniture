<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sudara Furniture</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="nav-brand">Sudara <span>Furniture</span></a>
        </div>
    </nav>

    <div class="auth-section">
        <div class="auth-card">
            <div class="auth-header">
                <h2 class="auth-title">Welcome Back</h2>
                <p>Sign in to your Sudara account</p>
            </div>

            <form id="loginForm">
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required
                        placeholder="Enter your email">
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required
                        placeholder="Enter your password">
                </div>
                <button type="submit" class="btn btn-primary">Sign In</button>
            </form>

            <div class="auth-links">
                <p>Don't have an account? <a href="register.php">Create Account</a></p>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);

            try {
                const response = await fetch('api/auth.php?action=login', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showNotification('Login successful! Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1500);
                } else {
                    showNotification(result.message || 'Login failed', 'error');
                }
            } catch (error) {
                console.error('Error logging in:', error);
                showNotification('An error occurred during login', 'error');
            }
        });
    </script>
</body>

</html>