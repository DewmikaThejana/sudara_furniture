<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Sudara Furniture</title>
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
                <h2 class="auth-title">Create Account</h2>
                <p>Join Sudara Furniture today</p>
            </div>

            <form id="registerForm">
                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required
                        placeholder="Choose a username">
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required
                        placeholder="Enter your email">
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required minlength="6"
                        placeholder="Create a password">
                </div>
                <button type="submit" class="btn btn-primary">Sign Up</button>
            </form>

            <div class="auth-links">
                <p>Already have an account? <a href="login.php">Sign In</a></p>
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script>
        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);

            try {
                const response = await fetch('api/auth.php?action=register', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    showNotification('Registration successful! Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1500);
                } else {
                    showNotification(result.message || 'Registration failed', 'error');
                }
            } catch (error) {
                console.error('Error during registration:', error);
                showNotification('An error occurred during registration', 'error');
            }
        });
    </script>
</body>

</html>