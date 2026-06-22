<?php
session_start();
include "db.php";

$response = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email    = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email == "" || $password == "") {
        $response = "missing_fields";
} else if (!preg_match('/^[a-zA-Z0-9._%+\-]+@(gmail|hotmail|outlook|yahoo|icloud)\.com$/', $email)) {
            $response = "invalid_email";
    } else {
        $check = $conn->prepare("SELECT id, first_name, last_name, password FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows == 0) {
            $response = "not_found";
        } else {
            $row = $res->fetch_assoc();
            if (password_verify($password, $row["password"])) {
                $_SESSION["user_id"]    = $row["id"];
                $_SESSION["first_name"] = $row["first_name"];
                $_SESSION["last_name"]  = $row["last_name"];
                $_SESSION["email"]      = $email;
                    setcookie('last_user_id', $row["id"], time() + (86400 * 30), '/');

                $response = "success";
            } else {
                $response = "wrong_password";
            }
        }
    }

    echo $response;
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | BISHTAH LINE</title>

<link rel="stylesheet" href="login.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600&display=swap" rel="stylesheet">

</head>
<body class="login-body">

<!-- ===== TOAST ===== -->
<div id="toast" class="login-toast"></div>

<!-- ===== MODAL ===== -->
<div id="successModal" class="login-modal-overlay">
    <div class="login-modal-box">
        <div class="login-modal-icon"></div>
        <h3 id="modalTitle">Login Successful</h3>
        <p id="modalMsg">You are being redirected to your home page.</p>
        <button class="login-modal-btn" onclick="goHome()">Go to Home</button>
    </div>
</div>

<!-- ===== HEADER ===== -->
<header class="login-header">
    <h1>BISHTAH LINE</h1>
    <p>Episodes of Your Life</p>
</header>

<!-- ===== MAIN ===== -->
<div class="login-container">
<div class="login-card">

    <a href="role.php" class="login-back-arrow" title="Back">
        <i class="fa-solid fa-arrow-left"></i>
    </a>

    <h2>Login</h2>
    <p>Welcome back to BISHTAH LINE</p>

    <form id="loginForm" class="login-form" novalidate>

        <div class="login-input-group">
    <i class="fa-regular fa-envelope login-field-icon"></i>
    <input type="email" name="email" id="email" placeholder="Email"
           oninput="this.value=this.value.replace(/[^\x00-\x7F]/g,'')">
</div>

<div class="login-input-group">
    <i class="fa-solid fa-lock login-field-icon"></i>
    <input type="password" name="password" id="password" placeholder="Password" autocomplete="current-password"
           oninput="this.value=this.value.replace(/[^\x00-\x7F]/g,'')">
    <button type="button" class="login-eye-btn" onclick="toggleEye('password','eyeIcon1')">
        <i id="eyeIcon1" class="fa-regular fa-eye"></i>
    </button>
</div>
        <button type="submit" id="submitBtn" class="login-submit-btn">
            <span id="btnText">Login</span>
        </button>

    </form>

    <div class="login-footer-link">
        Don't have an account? <a href="regp.php">Register</a>
    </div>

</div>
</div>

<script>
/* ===== TOAST ===== */
function toast(msg, type="success") {
    const t = document.getElementById("toast");
    t.className = "login-toast " + type + " show";
    t.innerHTML = msg;
    clearTimeout(t._t);
    t._t = setTimeout(() => t.classList.remove("show"), 3000);
}

/* ===== MODAL ===== */
function showModal(id) { document.getElementById(id).classList.add("show"); }
function closeModal(id) { document.getElementById(id).classList.remove("show"); }
function goHome() { window.location.href = "homep.php"; }

/* ===== EYE TOGGLE ===== */
function toggleEye(inputId, iconId) {
    const inp = document.getElementById(inputId);
    const ico = document.getElementById(iconId);
    if (inp.type === "password") {
        inp.type = "text";
        ico.className = "fa-regular fa-eye-slash";
    } else {
        inp.type = "password";
        ico.className = "fa-regular fa-eye";
    }
}

/* ===== SHAKE ===== */
function shake(el) {
    el.classList.remove("login-shake");
    void el.offsetWidth;
    el.classList.add("login-shake");
    setTimeout(() => el.classList.remove("login-shake"), 350);
}

/* ===== SUBMIT ===== */
document.getElementById("loginForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const emailEl = document.getElementById("email");
    const passEl  = document.getElementById("password");
    const email   = emailEl.value.trim();
    const pass    = passEl.value;

if (!email || !/^[a-zA-Z0-9._%+\-]+@(gmail|hotmail|outlook|yahoo|icloud)\.com$/.test(email)) {
            toast('<i class="fa-solid fa-circle-exclamation"></i> Enter a valid email', "error");
        shake(emailEl);
        return;
    }

    if (!pass) {
        toast('<i class="fa-solid fa-circle-exclamation"></i> Enter your password', "error");
        shake(passEl);
        return;
    }

    const btn = document.getElementById("submitBtn");
    const txt = document.getElementById("btnText");
    btn.disabled = true;
    txt.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Logging in...';

    fetch("logp.php", { method: "POST", body: new FormData(this) })
    .then(r => r.text())
    .then(data => {
        data = data.trim();

        if (data === "success") {
            document.getElementById("modalTitle").textContent = "Welcome Back! 🎉";
            document.getElementById("modalMsg").textContent = "Login successful. You're being redirected to your home page.";
            showModal("successModal");
            setTimeout(goHome, 2500);

        } else if (data === "missing_fields") {
            toast('<i class="fa-solid fa-circle-exclamation"></i> Please fill all fields', "error");
            btn.disabled = false; txt.textContent = "Login";

        } else if (data === "invalid_email") {
            toast('<i class="fa-solid fa-circle-exclamation"></i> Invalid email format', "error");
            shake(emailEl); btn.disabled = false; txt.textContent = "Login";

        } else if (data === "not_found") {
            toast('<i class="fa-solid fa-circle-exclamation"></i> Email not registered', "error");
            shake(emailEl); btn.disabled = false; txt.textContent = "Login";

        } else if (data === "wrong_password") {
            toast('<i class="fa-solid fa-circle-exclamation"></i> Wrong password', "error");
            shake(passEl); btn.disabled = false; txt.textContent = "Login";

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
