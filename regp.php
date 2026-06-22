<?php
session_start();
include "db.php";

$response = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $first_name = trim($_POST["first_name"] ?? "");
    $last_name  = trim($_POST["last_name"] ?? "");
    $email      = trim($_POST["email"] ?? "");
    $password   = $_POST["password"] ?? "";

    if ($first_name == "" || $last_name == "" || $email == "" || $password == "") {
        $response = "missing_fields";
} else if (!preg_match('/^[a-zA-Z0-9._%+\-]+@(gmail|hotmail|outlook|yahoo|icloud)\.com$/', $email)) {    
        $response = "invalid_email";
    } else if (
        strlen($password) < 8 ||
        !preg_match("/[A-Z]/", $password) ||
        !preg_match("/[a-z]/", $password) ||
        !preg_match("/[0-9]/", $password)
    ) {
        $response = "weak_password";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows > 0) {
            $response = "exists";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
            $insert->bind_param("ssss", $first_name, $last_name, $email, $hashedPassword);
            $response = $insert->execute() ? "success" : "db_error";
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
<title>Register | BISHTAH LINE</title>
<link rel="stylesheet" href="login.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600&display=swap" rel="stylesheet">
</head>
<body class="login-body">

<div id="toast" class="login-toast"></div>

<!-- WELCOME MODAL -->
<div id="welcomeModal" class="login-modal-overlay">
    <div class="login-modal-box">
        <div class="login-modal-icon"></div>
        <h3 id="modalTitle">Welcome!</h3>
        <p id="modalMsg">Your account has been created successfully.</p>
        <button class="login-modal-btn" onclick="goHome()">Go to Home</button>
    </div>
</div>

<!-- EXISTS MODAL -->
<div id="existsModal" class="login-modal-overlay">
    <div class="login-modal-box">
        <div class="login-modal-icon">⚠️</div>
        <h3>Account Already Exists</h3>
        <p>This email is already registered. Please login or use a different email.</p>
        <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
            <button class="login-modal-btn" onclick="closeModal('existsModal')">Try Again</button>
            <button class="login-modal-btn" style="background:#5a6f82;" onclick="window.location.href='logp.php'">Login</button>
        </div>
    </div>
</div>

<header class="login-header">
    <h1>BISHTAH LINE</h1>
    <p>Episodes of Your Life</p>
</header>

<div class="login-container">
<div class="login-card">

    <a href="role.php" class="login-back-arrow" title="Back">
        <i class="fa-solid fa-arrow-left"></i>
    </a>

    <h2>Create Account</h2>
    <p>Join BISHTAH LINE today</p>

    <form id="registerForm" class="login-form" novalidate>

        <div class="login-input-group">
            <i class="fa-regular fa-user login-field-icon"></i>
            <input type="text" name="first_name" id="first_name" placeholder="First Name"
                   oninput="this.value=this.value.replace(/[^a-zA-Z\s]/g,'')">
        </div>

        <div class="login-input-group">
            <i class="fa-regular fa-id-card login-field-icon"></i>
            <input type="text" name="last_name" id="last_name" placeholder="Last Name"
                   oninput="this.value=this.value.replace(/[^a-zA-Z\s]/g,'')">
        </div>

        <div class="login-input-group">
            <i class="fa-regular fa-envelope login-field-icon"></i>
            <input type="email" name="email" id="email" placeholder="Email"
                   oninput="this.value=this.value.replace(/[^\x00-\x7F]/g,'')">
        </div>

        <div class="login-input-group">
            <i class="fa-solid fa-lock login-field-icon"></i>
            <input type="password" name="password" id="password" placeholder="Password" autocomplete="new-password"
                   oninput="this.value=this.value.replace(/[^\x00-\x7F]/g,'')">
            <button type="button" class="login-eye-btn" onclick="toggleEye('password','eyeIcon1')">
                <i id="eyeIcon1" class="fa-regular fa-eye"></i>
            </button>
        </div>

        <!-- Strength bar -->
        <div class="login-strength-wrap">
            <div class="login-strength-bar">
                <div class="login-strength-fill" id="strengthFill"></div>
            </div>
            <div class="login-strength-label" id="strengthLabel"></div>
        </div>

        <!-- Rules -->
        <ul class="login-rules" id="rules">
            <li id="r-len"><i class="fa-solid fa-circle-xmark"></i> At least 8 characters</li>
            <li id="r-upper"><i class="fa-solid fa-circle-xmark"></i> One uppercase letter (A–Z)</li>
            <li id="r-lower"><i class="fa-solid fa-circle-xmark"></i> One lowercase letter (a–z)</li>
            <li id="r-num"><i class="fa-solid fa-circle-xmark"></i> One number (0–9)</li>
        </ul>

        <button type="submit" id="submitBtn" class="login-submit-btn">
            <span id="btnText">Register</span>
        </button>

    </form>

    <div class="login-footer-link">
        Already have an account? <a href="logp.php">Login</a>
    </div>

</div>
</div>

<script>
function toast(msg, type="success") {
    const t = document.getElementById("toast");
    t.className = "login-toast " + type + " show";
    t.innerHTML = msg;
    clearTimeout(t._t);
    t._t = setTimeout(() => t.classList.remove("show"), 3000);
}

function showModal(id) { document.getElementById(id).classList.add("show"); }
function closeModal(id) { document.getElementById(id).classList.remove("show"); }
function goHome() { window.location.href = "homep.php"; }

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

function shake(el) {
    el.classList.remove("login-shake");
    void el.offsetWidth;
    el.classList.add("login-shake");
    setTimeout(() => el.classList.remove("login-shake"), 350);
}

function checkRule(id, pass) {
    const tests = {
        "r-len":   pass.length >= 8,
        "r-upper": /[A-Z]/.test(pass),
        "r-lower": /[a-z]/.test(pass),
        "r-num":   /[0-9]/.test(pass)
    };
    const li = document.getElementById(id);
    const ok = tests[id];
    li.className = ok ? "ok" : "fail";
    li.querySelector("i").className = ok
        ? "fa-solid fa-circle-check"
        : "fa-solid fa-circle-xmark";
    return ok;
}

function validatePass(pass) {
    return (
        checkRule("r-len",   pass) &&
        checkRule("r-upper", pass) &&
        checkRule("r-lower", pass) &&
        checkRule("r-num",   pass)
    );
}

document.getElementById("password").addEventListener("input", function () {
    const val   = this.value;
    const fill  = document.getElementById("strengthFill");
    const label = document.getElementById("strengthLabel");

    checkRule("r-len",   val);
    checkRule("r-upper", val);
    checkRule("r-lower", val);
    checkRule("r-num",   val);

    let score = 0;
    if (val.length >= 8)          score++;
    if (val.length >= 12)         score++;
    if (/[A-Z]/.test(val))        score++;
    if (/[a-z]/.test(val))        score++;
    if (/[0-9]/.test(val))        score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    if (!val.length) { fill.style.width = "0%"; label.textContent = ""; return; }

    const levels = [
        { w:"16%",  bg:"#e74c3c", txt:"Very Weak" },
        { w:"32%",  bg:"#e67e22", txt:"Weak" },
        { w:"50%",  bg:"#f1c40f", txt:"Fair" },
        { w:"70%",  bg:"#2ecc71", txt:"Good" },
        { w:"85%",  bg:"#27ae60", txt:"Strong" },
        { w:"100%", bg:"#1f8a4c", txt:"Very Strong" },
    ];
    const lv = levels[Math.min(score, 5)];
    fill.style.width      = lv.w;
    fill.style.background = lv.bg;
    label.textContent     = lv.txt;
    label.style.color     = lv.bg;
});

document.getElementById("registerForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const firstName = document.getElementById("first_name");
    const lastName  = document.getElementById("last_name");
    const emailEl   = document.getElementById("email");
    const passEl    = document.getElementById("password");

    const first = firstName.value.trim();
    const last  = lastName.value.trim();
    const email = emailEl.value.trim();
    const pass  = passEl.value;

    if (!first) { toast('<i class="fa-solid fa-circle-exclamation"></i> Enter your first name', "error"); shake(firstName); return; }
    if (!last)  { toast('<i class="fa-solid fa-circle-exclamation"></i> Enter your last name', "error");  shake(lastName);  return; }
    if (!email || !/^[a-zA-Z0-9._%+\-]+@(gmail|hotmail|outlook|yahoo|icloud)\.com$/.test(email))  { toast('<i class="fa-solid fa-circle-exclamation"></i> Enter a valid email', "error"); shake(emailEl); return; }
    if (!validatePass(pass)) { toast('<i class="fa-solid fa-circle-exclamation"></i> Password doesn\'t meet requirements', "error"); shake(passEl); return; }

    const btn = document.getElementById("submitBtn");
    const txt = document.getElementById("btnText");
    btn.disabled = true;
    txt.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Creating...';

    fetch("regp.php", { method: "POST", body: new FormData(this) })
    .then(r => r.text())
    .then(data => {
        data = data.trim();

        if (data === "success") {
            document.getElementById("modalTitle").textContent = "Welcome, " + first + "! 🎉";
            document.getElementById("modalMsg").textContent   = "Your account has been created. You're being taken to your home page.";
            showModal("welcomeModal");
            setTimeout(goHome, 2800);

        } else if (data === "exists") {
            showModal("existsModal"); shake(emailEl);
            btn.disabled = false; txt.textContent = "Register";

        } else if (data === "missing_fields") {
            toast('<i class="fa-solid fa-circle-exclamation"></i> Please fill all fields', "error");
            btn.disabled = false; txt.textContent = "Register";

        } else if (data === "invalid_email") {
            toast('<i class="fa-solid fa-circle-exclamation"></i> Invalid email format', "error");
            shake(emailEl); btn.disabled = false; txt.textContent = "Register";

        } else if (data === "weak_password") {
            toast('<i class="fa-solid fa-circle-exclamation"></i> Weak password', "error");
            shake(passEl); btn.disabled = false; txt.textContent = "Register";

        } else {
            toast('<i class="fa-solid fa-triangle-exclamation"></i> Error: ' + data, "error");
            btn.disabled = false; txt.textContent = "Register";
        }
    })
    .catch(() => {
        toast('<i class="fa-solid fa-wifi"></i> Network error. Try again.', "error");
        btn.disabled = false; txt.textContent = "Register";
    });
});
</script>
</body>
</html>