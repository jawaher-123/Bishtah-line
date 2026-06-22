<?php
session_start();
include "db.php";

if(isset($_POST['action']) && $_POST['action'] == "login"){

    if(!isset($_POST['email'], $_POST['password'], $_POST['code'])){
        echo "missing"; exit;
    }

    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $code     = trim($_POST['code']);

    if(empty($email) || empty($password) || empty($code)){
        echo "empty"; exit;
    }

    if($code !== "1122"){
        echo "wrong_code"; exit;
    }

    $result = mysqli_query($conn, "SELECT * FROM admins WHERE email='$email'");

    if(mysqli_num_rows($result) == 0){
        echo "not_found"; exit;
    }

    $admin = mysqli_fetch_assoc($result);

    if(!password_verify($password, $admin['password'])){
        echo "wrong_password"; exit;
    }

    $_SESSION['admin_id']   = $admin['id'];
    $_SESSION['admin_name'] = $admin['full_name'];
    $_SESSION['is_admin']   = true;

    echo "success";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | BISHTAH LINE</title>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600&display=swap" rel="stylesheet">
</head>
<body class="login-body">

<div id="toast" class="login-toast"></div>

<!-- ===== MODAL ===== -->
<div id="successModal" class="login-modal-overlay">
    <div class="login-modal-box">
        <div class="login-modal-icon"></div>
        <h3 id="modalTitle">Login Successful</h3>
        <p id="modalMsg">You are being redirected to the dashboard.</p>
        <button class="login-modal-btn" onclick="goDashboard()">Go to Dashboard</button>
    </div>
</div>

<header class="login-header">
    <h1>BISHTAH LINE</h1>
    <p>Admin Portal</p>
</header>

<div class="login-container">
    <div class="login-card">

        <a href="role.php" class="login-back-arrow">
            <i class="fa-solid fa-arrow-left"></i>
        </a>

        <h2>Admin Login</h2>
        <p>Login to manage BISHTAH products.</p>

        <form id="loginForm" class="login-form" novalidate>

            <div class="login-input-group">
                <i class="fa-regular fa-envelope login-field-icon"></i>
                <input type="email" name="email" id="email" placeholder="Admin Email"
                       oninput="this.value=this.value.replace(/[^\x00-\x7F]/g,'')">
            </div>

            <div class="login-input-group">
                <i class="fa-solid fa-lock login-field-icon"></i>
                <input type="password" name="password" id="password" placeholder="Password"
                       oninput="this.value=this.value.replace(/[^\x00-\x7F]/g,'')">
                <button type="button" class="login-eye-btn" onclick="toggleEye('password','eyeIcon1')">
                    <i id="eyeIcon1" class="fa-regular fa-eye"></i>
                </button>
            </div>

            <div class="login-input-group">
                <i class="fa-solid fa-key login-field-icon"></i>
                <input type="text" name="code" id="code" placeholder="Authorization Code"
                       oninput="this.value=this.value.replace(/[^0-9]/g,'')">
            </div>

            <button type="submit" id="submitBtn" class="login-submit-btn">
                <span id="btnText">Login</span>
            </button>

        </form>

        <div class="login-footer-link">
            Don't have an admin account? <a href="adminreg.php">Create one</a>
        </div>

    </div>
</div>

<script>
/* ===== TOAST ===== */
function toast(msg, type="success"){
    const t = document.getElementById("toast");
    t.className = "login-toast " + type + " show";
    t.innerHTML = msg;
    clearTimeout(t._t);
    t._t = setTimeout(() => t.classList.remove("show"), 3000);
}

/* ===== MODAL ===== */
function showModal(id){ document.getElementById(id).classList.add("show"); }
function closeModal(id){ document.getElementById(id).classList.remove("show"); }
function goDashboard(){ window.location.href = "admin_home.php"; }

/* ===== EYE TOGGLE ===== */
function toggleEye(inputId, iconId){
    const inp = document.getElementById(inputId);
    const ico = document.getElementById(iconId);
    if(inp.type === "password"){
        inp.type = "text";
        ico.className = "fa-regular fa-eye-slash";
    } else {
        inp.type = "password";
        ico.className = "fa-regular fa-eye";
    }
}

/* ===== SHAKE ===== */
function shake(el){
    el.classList.remove("login-shake");
    void el.offsetWidth;
    el.classList.add("login-shake");
    setTimeout(() => el.classList.remove("login-shake"), 350);
}

/* ===== SUBMIT ===== */
document.getElementById("loginForm").addEventListener("submit", function(e){
    e.preventDefault();

    const emailEl = document.getElementById("email");
    const passEl  = document.getElementById("password");
    const codeEl  = document.getElementById("code");

    const email = emailEl.value.trim();
    const pass  = passEl.value;
    const code  = codeEl.value.trim();

    /* ---- Client-side validation ---- */
    if(!email || !/^[a-zA-Z0-9._%+\-]+@(gmail|hotmail|outlook|yahoo|icloud)\.com$/.test(email)){
        toast('<i class="fa-solid fa-circle-exclamation"></i> Enter a valid email', "error");
        shake(emailEl); return;
    }

    if(!pass){
        toast('<i class="fa-solid fa-circle-exclamation"></i> Enter your password', "error");
        shake(passEl); return;
    }

    if(!code){
        toast('<i class="fa-solid fa-circle-exclamation"></i> Enter authorization code', "error");
        shake(codeEl); return;
    }

    const btn = document.getElementById("submitBtn");
    const txt = document.getElementById("btnText");
    btn.disabled = true;
    txt.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Verifying...';

    let formData = new FormData(this);
    formData.append("action","login");

    fetch("adminlog.php", { method:"POST", body:formData })
    .then(res => res.text())
    .then(data => {
        data = data.trim();

        if(data === "success"){
            document.getElementById("modalTitle").textContent = "Welcome Admin ✅";
            document.getElementById("modalMsg").textContent   = "Login successful. You are being redirected to the dashboard.";
            showModal("successModal");
            setTimeout(goDashboard, 2500);

        } else if(data === "wrong_code"){
            toast('<i class="fa-solid fa-circle-exclamation"></i> Wrong authorization code', "error");
            shake(codeEl);
            btn.disabled = false; txt.textContent = "Login";

        } else if(data === "wrong_password"){
            toast('<i class="fa-solid fa-circle-exclamation"></i> Wrong password', "error");
            shake(passEl);
            btn.disabled = false; txt.textContent = "Login";

        } else if(data === "not_found"){
            toast('<i class="fa-solid fa-circle-exclamation"></i> Admin not found', "error");
            shake(emailEl);
            btn.disabled = false; txt.textContent = "Login";

        } else if(data === "empty" || data === "missing"){
            toast('<i class="fa-solid fa-circle-exclamation"></i> Please fill all fields', "error");
            btn.disabled = false; txt.textContent = "Login";

        } else {
            toast('<i class="fa-solid fa-triangle-exclamation"></i> Error: ' + data, "error");
            btn.disabled = false; txt.textContent = "Login";
        }
    })
    .catch(() => {
        toast('<i class="fa-solid fa-wifi"></i> Network error. Try again.', "error");
        btn.disabled = false; txt.textContent = "Login";
    });
});
</script>

</body>
</html>