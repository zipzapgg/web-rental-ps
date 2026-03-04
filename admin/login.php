<!DOCTYPE html>
<html>
<head>
    <title>Login Admin - Violet PS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #0f051d; }
        .login-card { background: #1a1a2e; border: 1px solid #8a2be2; border-radius: 15px; color: white; }
        .btn-violet { background: #8a2be2; color: white; }
    </style>
</head>
<body class="d-flex align-items-center vh-100">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card login-card shadow-lg">
                <div class="card-body p-4 text-center">
                    <img src="../assets/images/logo.png" width="80" class="mb-3">
                    <h4 class="mb-4">Admin Login</h4>
                    <form action="cek_login.php" method="POST">
                        <div class="mb-3 text-start">
                            <label>Username</label>
                            <input type="text" name="user" class="form-control" required>
                        </div>
                        <div class="mb-3 text-start">
                            <label>Password</label>
                            <input type="password" name="pass" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-violet w-100 mt-3">LOGIN</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>