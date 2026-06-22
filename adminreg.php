<?php
session_start();
include "db.php";

if(isset($_POST['action']) && $_POST['action'] == "register"){

    if(!isset($_POST['full_name'], $_POST['email'], $_POST['password'], $_POST['code'])){
        echo "missing"; exit;
    }

    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);
    $password  = $_POST['password'];
    $code      = trim($_POST['code']);

    if(empty($full_name) || empty($email) || empty($password) || empty($code)){
        echo "empty"; exit;
    }

    if(!preg_match('/^[a-zA-Z\s]+$/', $full_name)){
        echo "invalid_name"; exit;
    }

    if($code !== "1122"){
        echo "wrong_code"; exit;
    }

if(!preg_match('/^[a-zA-Z0-9._%+\-]+@(gmail|hotmail|outlook|yahoo|icloud)\.com$/', $email)){
            echo "invalid_email"; exit;
    }

    if(
        strlen($password) < 8 ||
        !preg_match("/[A-Z]/", $password) ||
        !preg_match("/[a-z]/", $password) ||
        !preg_match("/[0-9]/", $password)
    ){
        echo "weak_password"; exit;
    }

    $check = mysqli_query($conn, "SELECT id FROM admins WHERE email='$email'");
    if(mysqli_num_rows($check) > 0){
        echo "exists"; exit;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $insert = mysqli_query($conn, "INSERT INTO admins (full_name, email, password) VALUES ('$full_name', '$email', '$hashed')");

    echo $insert ? "success" : "error";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration | BISHTAH LINE</title>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600&display=swap" rel="stylesheet">
</head>
<body class="login-body">

<div id="toast" class="login-toast"></div>

<!-- SUCCESS MODAL -->
<div id="welcomeModal" class="login-modal-overlay">
    <div class="login-modal-box">
        <div class="login-modal-icon"></div>
        <h3 id="modalTitle">Welcome!</h3>
        <p id="modalMsg">Admin account created successfully.</p>
        <button class="login-modal-btn" onclick="window.location.href='admin_home.php'">Go to Dashboard</button>
    </div>
</div>

<!-- EXISTS MODAL -->
<div id="existsModal" class="login-modal-overlay">
    <div class="login-modal-box">
        <div class="login-modal-icon">⚠️</div>
        <h3>Email Already Registered</h3>
        <p>This email is already linked to an admin account. Please login or use a different email.</p>
        <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
            <button class="login-modal-btn" onclick="closeModal('existsModal')">Try Again</button>
            <button class="login-modal-btn" style="background:#5a6f82;" onclick="window.location.href='adminlog.php'">Login</button>
        </div>
    </div>
</div>

<header class="login-header">
    <h1>BISHTAH LINE</h1>
    <p>Admin Portal</p>
</header>

<div class="login-container">
<div class="login-card">

    <a href="role.php" class="login-back-arrow" title="Back">
        <i class="fa-solid fa-arrow-left"></i>
    </a>

    <h2>Admin Registration</h2>
    <p>Set up a new administrator account</p>

    <form id="adminForm" class="login-form" novalidate>

        <div class="login-input-group">
            <i class="fa-regular fa-user login-field-icon"></i>
            <input type="text" name="full_name" id="full_name"
                   placeholder="Full Name (English only)"
                   oninput="this.value=this.value.replace(/[^a-zA-Z\s]/g,'')">
        </div>

        <div class="login-input-group">
            <i class="fa-regular fa-envelope login-field-icon"></i>
            <input type="email" name="email" id="email" placeholder="Work Email"
                   oninput="this.value=this.value.replace(/[^\x00-\x7F]/g,'')">
        </div>

        <div class="login-input-group">
            <i class="fa-solid fa-lock login-field-icon"></i>
            <input type="password" name="password" id="password"
                   placeholder="Password" autocomplete="new-password"
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

        <div class="login-input-group">
            <i class="fa-solid fa-key login-field-icon"></i>
            <input type="text" name="code" id="code"
                   placeholder="Authorization Code"
                   oninput="this.value=this.value.replace(/[^0-9]/g,'')">
        </div>

        <button type="submit" id="submitBtn" class="login-submit-btn">
            <span id="btnText">Register as Admin</span>
        </button>

    </form>

    <div class="login-footer-link">
        Already have admin access? <a href="adminlog.php">Login here</a>
    </div>

</div>
</div>

<script>
function toast(msg, type="success"){
    const t = document.getElementById("toast");
    t.className = "login-toast " + type + " show";
    t.innerHTML = msg;
    clearTimeout(t._t);
    t._t = setTimeout(() => t.classList.remove("show"), 3000);
}

function showModal(id){ document.getElementById(id).classList.add("show"); }
function closeModal(id){ document.getElementById(id).classList.remove("show"); }

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

function shake(el){
    el.classList.remove("login-shake");
    void el.offsetWidth;
    el.classList.add("login-shake");
    setTimeout(() => el.classList.remove("login-shake"), 350);
}

function checkRule(id, pass){
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

function validatePass(pass){
    return (
        checkRule("r-len",   pass) &&
        checkRule("r-upper", pass) &&
        checkRule("r-lower", pass) &&
        checkRule("r-num",   pass)
    );
}

document.getElementById("password").addEventListener("input", function(){
    const val   = this.value;
    const fill  = document.getElementById("strengthFill");
    const label = document.getElementById("strengthLabel");

    checkRule("r-len",   val);
    checkRule("r-upper", val);
    checkRule("r-lower", val);
    checkRule("r-num",   val);

    let score = 0;
    if(val.length >= 8)          score++;
    if(val.length >= 12)         score++;
    if(/[A-Z]/.test(val))        score++;
    if(/[a-z]/.test(val))        score++;
    if(/[0-9]/.test(val))        score++;
    if(/[^A-Za-z0-9]/.test(val)) score++;

    if(!val.length){ fill.style.width="0%"; label.textContent=""; return; }

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

document.getElementById("adminForm").addEventListener("submit", function(e){
    e.preventDefault();

    const fullNameEl = document.getElementById("full_name");
    const emailEl    = document.getElementById("email");
    const passEl     = document.getElementById("password");
    const codeEl     = document.getElementById("code");

    const fullName = fullNameEl.value.trim();
    const email    = emailEl.value.trim();
    const pass     = passEl.value;
    const code     = codeEl.value.trim();

    if(!fullName){
        toast('<i class="fa-solid fa-circle-exclamation"></i> Enter your full name', "error");
        shake(fullNameEl); return;
    }
    if(!/^[a-zA-Z\s]+$/.test(fullName)){
        toast('<i class="fa-solid fa-circle-exclamation"></i> Name must be in English only', "error");
        shake(fullNameEl); return;
    }
if(!email || !/^[a-zA-Z0-9._%+\-]+@(gmail|hotmail|outlook|yahoo|icloud)\.com$/.test(email)){
            toast('<i class="fa-solid fa-circle-exclamation"></i> Enter a valid email', "error");
        shake(emailEl); return;
    }
    if(!validatePass(pass)){
        toast('<i class="fa-solid fa-circle-exclamation"></i> Password doesn\'t meet requirements', "error");
        shake(passEl); return;
    }
    if(!code){
        toast('<i class="fa-solid fa-circle-exclamation"></i> Enter authorization code', "error");
        shake(codeEl); return;
    }

    const btn = document.getElementById("submitBtn");
    const txt = document.getElementById("btnText");
    btn.disabled = true;
    txt.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Creating...';

    let formData = new FormData(this);
    formData.append("action", "register");

    fetch("adminreg.php", { method:"POST", body:formData })
    .then(r => r.text())
    .then(data => {
        data = data.trim();

        if(data === "success"){
            document.getElementById("modalTitle").textContent = "Welcome, " + fullName + "! 🎉";
            document.getElementById("modalMsg").textContent   = "Admin account created successfully.";
            showModal("welcomeModal");
            setTimeout(() => window.location.href = "admin_home.php", 2800);

        } else if(data === "exists"){
            showModal("existsModal");
            shake(emailEl);
            btn.disabled = false; txt.textContent = "Register as Admin";

        } else if(data === "wrong_code"){
            toast('<i class="fa-solid fa-circle-exclamation"></i> Wrong authorization code ❌', "error");
            shake(codeEl);
            btn.disabled = false; txt.textContent = "Register as Admin";

        } else if(data === "invalid_name"){
            toast('<i class="fa-solid fa-circle-exclamation"></i> Name must be in English only', "error");
            shake(fullNameEl);
            btn.disabled = false; txt.textContent = "Register as Admin";

        } else if(data === "weak_password"){
            toast('<i class="fa-solid fa-circle-exclamation"></i> Weak password', "error");
            shake(passEl);
            btn.disabled = false; txt.textContent = "Register as Admin";

        } else if(data === "missing" || data === "empty"){
            toast('<i class="fa-solid fa-circle-exclamation"></i> Please fill all fields', "error");
            btn.disabled = false; txt.textContent = "Register as Admin";

        } else {
            toast('<i class="fa-solid fa-triangle-exclamation"></i> Error: ' + data, "error");
            btn.disabled = false; txt.textContent = "Register as Admin";
        }
    })
    .catch(() => {
        toast('<i class="fa-solid fa-wifi"></i> Network error. Try again.', "error");
        btn.disabled = false; txt.textContent = "Register as Admin";
    });
});
</script>

</body>
</html>