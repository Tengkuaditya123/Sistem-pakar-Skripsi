<div class="d-flex justify-content-center align-items-center vh-100"
     style="
     background-image: url('assets/img/p6.jpg');
     background-size: cover;
     background-position: center;">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <div class="text-center" style="width: 300px; color: white;">

        <!-- LOGO -->
        <img src="assets/img/sse.png" alt="logo" style="width: 150px; margin-bottom: 80px;">
        <div style="margin-top: -80px;">

        <!-- TITLE -->
        <h4 class="mb-4">Login</h4>

        <!-- FORM -->
        <form method="POST" action="loginproses.php">

            <div class="mb-3">
                <input type="text" name="username" class="form-control custom-input" placeholder="Username" required>
            </div>

            <div class="mb-3">
                <input type="password" name="password" class="form-control custom-input" placeholder="Password" required>
            </div>

            <button type="submit" class="btn btn-secondary w-100">LOGIN</button>


        </form>
        <div class="text-center mt-3">
    <a href="forgot.php" class="forgot-link">Don't have a account?</a>
</div>
    </div>
</div>

<style>
/* Hilangin background putih */
.custom-input {
    background: transparent;
    border: none;
    border-bottom: 1px solid white;
    color: white;
    border-radius: 0;
}

/* Placeholder jadi putih */
.custom-input::placeholder {
    color: rgba(255, 255, 255, 0.7);
}

/* Fokus input */
.custom-input:focus {
    background: transparent;
    box-shadow: none;
    border-bottom: 1px solid #00c6ff;
    color: white;
}
body {
    font-family: 'Poppins', sans-serif;
}   

.forgot-link {
    color: rgba(255,255,255,0.7);
    font-size: 14px;
    text-decoration: none;
}

.forgot-link:hover {
    color: white;
    text-decoration: underline;
}
</style>