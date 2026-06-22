<?php
/* --- 1. Debugging & Database Connection --- */
// These lines ensure all PHP errors are displayed during your development/demo phase
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// include 'db.php' establishes the link between this script and your MySQL database
if (file_exists('db.php')) {
    include 'db.php';
} else {
    die("Error: db.php not found.");
}

$success_action = false;

/* --- 2. Delete Logic (Storage Management) --- */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $get_img = mysqli_query($conn, "SELECT image FROM abayas WHERE id = $id");
    
    if ($get_img && mysqli_num_rows($get_img) > 0) {
        $img_data = mysqli_fetch_assoc($get_img);
        // unlink() physically deletes the image file from the 'uploads' folder to save server space
        if (!empty($img_data['image']) && file_exists("uploads/" . $img_data['image'])) {
            unlink("uploads/" . $img_data['image']);
        }
        // This query removes the product record from the database table
        mysqli_query($conn, "DELETE FROM abayas WHERE id = $id");
    }
    header("Location: mproductm.php");
    exit();
}

/* --- 3. Edit Mode Logic --- */
$edit_mode  = false;
$edit_id    = 0;
$edit_name = ''; $edit_price = ''; $edit_desc = ''; $edit_tag = '';

// When 'edit' is clicked, we fetch the existing data to fill the form for modification
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $edit_id   = (int)$_GET['edit'];
    $edit_res  = mysqli_query($conn, "SELECT * FROM abayas WHERE id = $edit_id");
    if ($edit_res && mysqli_num_rows($edit_res) > 0) {
        $edit_data = mysqli_fetch_assoc($edit_res);
        $edit_name  = $edit_data['name'];
        $edit_price = $edit_data['price'];
        $edit_desc  = $edit_data['description'] ?? '';
        $edit_tag   = $edit_data['tag'] ?? '';
    }
}

/* --- 4. Add / Update Logic (Security) --- */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // mysqli_real_escape_string protects your database from SQL Injection attacks
    $name  = mysqli_real_escape_string($conn, $_POST['abaya_name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $desc  = mysqli_real_escape_string($conn, $_POST['description'] ?? '');
    $tag   = mysqli_real_escape_string($conn, $_POST['tag'] ?? '');

    if (isset($_POST['update_product'])) {
        /* --- Update Existing Product --- */
        $id = (int)$_POST['product_id'];
        mysqli_query($conn, "UPDATE abayas SET name='$name', price='$price', description='$desc', tag='$tag' WHERE id=$id");
        if (!empty($_FILES['abaya_photo']['name'])) {
            $img1 = basename($_FILES['abaya_photo']['name']);
            move_uploaded_file($_FILES['abaya_photo']['tmp_name'], "uploads/" . $img1);
            mysqli_query($conn, "UPDATE abayas SET image='$img1' WHERE id=$id");
        }
        header("Location: mproductm.php");
        exit();
    } elseif (isset($_POST['add_product'])) {
        /* --- Add New Product --- */
        $img1 = basename($_FILES['abaya_photo']['name']);
        // move_uploaded_file transfers the image from temporary storage to your project's folder
        if (move_uploaded_file($_FILES['abaya_photo']['tmp_name'], "uploads/" . $img1)) {
            $img1_e = mysqli_real_escape_string($conn, $img1);
            mysqli_query($conn, "INSERT INTO abayas (name, price, image, description, tag) VALUES ('$name', '$price', '$img1_e', '$desc', '$tag')");
            $success_action = true; // This activates the success popup modal
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products | BISHTAH LINE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* --- Brand Identity & Fonts --- */
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Poppins:wght@300;400;600&display=swap');

        :root {
            --brand-blue: #7c90a5;
            --brand-magenta: #70799f; 
            --brand-dark: #1a1a1a;
            --brand-accent: #ffffff;
            --text-muted: #555555;
            --bg-light: #fbfbfb;
            --card-shadow: 0 10px 20px rgba(0,0,0,0.05);
            --brand-love: #e74c3c;
        }

        body { font-family: 'Poppins', sans-serif; margin: 0; background-color: var(--bg-light); color: var(--brand-dark); }

        /* Titles use Playfair Display for a luxury feel */
        h1, h2, .modal-box h2 { font-family: 'Playfair Display', serif !important; }

        .store-header { background-color: var(--brand-blue); padding: 25px 4%; text-align: center; color: white; }
        .store-header h1 { font-size: 2.2rem; letter-spacing: 4px; text-transform: uppercase; margin: 0; }
        .store-header p { font-size: 0.8rem; letter-spacing: 3px; text-transform: uppercase; opacity: 0.9; margin-top: 5px; font-family: 'Poppins', sans-serif; }

        /* --- Modal / Popup Styles --- */
        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.4); display: none; align-items: center; justify-content: center; z-index: 2000; }
        .modal-box { background: #fff; padding: 40px; border-radius: 20px; text-align: center; max-width: 400px; width: 90%; box-shadow: var(--card-shadow); }
        .modal-box.delete-style { border-top: 5px solid var(--brand-love); }
        .modal-icon-success { color: #2ecc71; font-size: 60px; margin-bottom: 20px; }
        .modal-icon-delete { color: var(--brand-love); font-size: 60px; margin-bottom: 20px; }
        .btn-modal { padding: 12px 30px; border-radius: 8px; cursor: pointer; border: none; font-weight: 600; font-family: 'Poppins'; }
        .btn-continue { background: var(--brand-blue); color: white; margin-top: 20px; }
        .btn-confirm-delete { background: var(--brand-love); color: white; text-decoration: none; display: inline-block; padding: 12px 25px; border-radius: 8px; }
        .btn-cancel { background: #f0f0f0; color: #333; margin-right: 10px; }

        .manage-container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }
        .card { background: white; padding: 35px; border-radius: 15px; box-shadow: var(--card-shadow); margin-bottom: 30px; }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }
        .full-width { grid-column: span 2; }
        .field-wrap { display: flex; flex-direction: column; gap: 8px; }
        .field-wrap label { font-size: 13px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; }
        
        input, select, textarea { padding: 12px; border: 1px solid #ddd; border-radius: 10px; font-family: 'Poppins'; outline: none; }
        input:focus { border-color: var(--brand-blue); }

        .btn-submit { background: var(--brand-blue); color: white; border: none; padding: 15px; border-radius: 10px; cursor: pointer; width: 100%; font-size: 15px; font-weight: 600; margin-top: 25px; font-family: 'Poppins'; }

        /* --- Search --- */
        .search-row { display: flex; gap: 15px; margin-bottom: 25px; align-items: center; }
        .search-input { flex: 1; }
        .btn-search-action { background: var(--brand-blue); color: white; border: none; padding: 12px 25px; border-radius: 8px; cursor: pointer; font-family: 'Poppins'; }
        .btn-reset { background: #eee; color: #555; text-decoration: none; padding: 12px 20px; border-radius: 8px; font-size: 13px; font-family: 'Poppins'; }

        /* --- Table --- */
        table { width: 100%; border-collapse: collapse; }
        th { text-align: center; padding: 15px; background: #f9f9f9; border-bottom: 2px solid #eee; font-size: 14px; font-family: 'Poppins'; }
        td { padding: 15px; border-bottom: 1px solid #f2f2f2; text-align: center; font-size: 14px; font-family: 'Poppins'; }
        .img-preview { width: 50px; height: 65px; object-fit: cover; border-radius: 8px; }
        .no-results { padding: 50px 0; color: var(--text-muted); font-style: italic; font-family: 'Poppins'; font-size: 1.1rem; }
    </style>
</head>
<body>

<!-- Success Modal -->
<div id="successModal" class="modal-overlay" style="<?php echo $success_action ? 'display:flex;' : ''; ?>">
    <div class="modal-box">
        <div class="modal-icon-success"><i class="fas fa-check-circle"></i></div>
        <h2>Success! ✨</h2>
        <p>Operation completed successfully.</p>
        <button class="btn-modal btn-continue" onclick="window.location='mproductm.php'">Continue</button>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal-overlay">
    <div class="modal-box delete-style">
        <div class="modal-icon-delete"><i class="fas fa-exclamation-circle"></i></div>
        <h2>Wait a second! ✋</h2>
        <p>Are you sure you want to remove this piece from your collection?</p>
        <div style="margin-top:25px;">
            <button class="btn-modal btn-cancel" onclick="closeDeleteModal()">No, Keep it</button>
            <a id="confirmDeleteBtn" href="#" class="btn-modal btn-confirm-delete">Yes, Delete</a>
        </div>
    </div>
</div>

<header class="store-header">
    <h1>BISHTAH LINE</h1>
    <p>Admin Control Panel</p>
</header>

<div class="manage-container">
    <!-- Form Section -->
    <div class="card">
        <h2><?php echo $edit_mode ? 'Edit Abaya' : 'Add New Abaya'; ?></h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="<?php echo $edit_id; ?>">
            <div class="form-grid">
                <div class="field-wrap">
                    <label>Abaya Name</label>
                    <input type="text" name="abaya_name" placeholder="Name" value="<?php echo htmlspecialchars($edit_name); ?>" required>
                </div>
                <div class="field-wrap">
                    <label>Price (SAR)</label>
                    <!-- step="0.01" allows decimal values like 350.05 -->
                    <input type="number" name="price" step="0.01" value="<?php echo htmlspecialchars($edit_price); ?>" required>
                </div>
                <div class="field-wrap">
                    <label>Tag</label>
                    <select name="tag" required>
                        <option value="">Select Status</option>
                        <option value="New" <?php if($edit_tag == 'New') echo 'selected'; ?>>New Collection</option>
                        <option value="Old" <?php if($edit_tag == 'Old') echo 'selected'; ?>>Old Collection</option>
                    </select>
                </div>
                <div class="field-wrap">
                    <label>Product Photo</label>
                    <input type="file" name="abaya_photo">
                </div>
                <div class="field-wrap full-width">
                    <label>Description</label>
                    <textarea name="description" placeholder="Describe the abaya details..." rows="3"><?php echo htmlspecialchars($edit_desc); ?></textarea>
                </div>
            </div>
            <button type="submit" name="<?php echo $edit_mode ? 'update_product' : 'add_product'; ?>" class="btn-submit">
                + <?php echo $edit_mode ? 'Save Changes' : 'Add to Collection'; ?>
            </button>
        </form>
    </div>

    <!-- Inventory Table Section -->
    <div class="card">
        <div class="search-row">
            <input type="text" id="q" placeholder="Search by name..." class="search-input">
            <select id="f" style="padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: white;">
                <option value="">All Tags</option>
                <option value="New">New</option>
                <option value="Old">Old</option>
            </select>
            <button class="btn-search-action" onclick="filterTable()">Search</button>
            <a href="mproductm.php" class="btn-reset">Reset</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Preview</th>
                    <th>Product Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="inventoryBody">
                <?php
                $res = mysqli_query($conn, "SELECT * FROM abayas ORDER BY id DESC");
                if ($res && mysqli_num_rows($res) > 0) {
                    while ($row = mysqli_fetch_assoc($res)) {
                        echo "<tr class='item-row' data-name='".strtolower($row['name'])."' data-tag='{$row['tag']}'>
                            <td><img src='uploads/{$row['image']}' class='img-preview' onerror=\"this.src='https://via.placeholder.com/50x65'\"></td>
                            <td style='font-weight:600;'>".htmlspecialchars($row['name'])."</td>
                            <td style='color:var(--text-muted); max-width:250px;'>".htmlspecialchars($row['description'])."</td>
                            <!-- Price shown with SAR on the right for better readability -->
                            <td style='font-weight:600;'>{$row['price']} SAR</td>
                            <td><span style='background:rgba(124,144,165,0.1); color:var(--brand-blue); padding:4px 10px; border-radius:20px; font-size:11px; font-weight:600;'>{$row['tag']}</span></td>
                            <td>
                                <a href='?edit={$row['id']}' style='color:var(--brand-magenta); margin-right:15px;'><i class='fas fa-edit'></i></a>
                                <a href='javascript:void(0)' onclick='openDeleteModal({$row['id']})' style='color:var(--brand-love);'><i class='fas fa-trash-alt'></i></a>
                            </td>
                        </tr>";
                    }
                } else {
                    // Friendly message if database is empty
                    echo "<tr><td colspan='6' class='no-results'>No items found! Why not add a beautiful new piece to your collection?</td></tr>";
                }
                ?>
                <tr id="searchEmptyMsg" style="display:none;">
                    <td colspan='6' class='no-results'>No items found! Why not add a beautiful new piece to your collection?</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
/* --- Client-Side Filter Function (UX Optimization) --- */
function filterTable() {
    let q = document.getElementById('q').value.toLowerCase();
    let f = document.getElementById('f').value;
    let rows = document.querySelectorAll('.item-row');
    let visibleCount = 0;

    rows.forEach(row => {
        let name = row.getAttribute('data-name');
        let tag = row.getAttribute('data-tag');
        if (name.includes(q) && (f === "" || tag === f)) {
            row.style.display = "";
            visibleCount++;
        } else {
            row.style.display = "none";
        }
    });

    // Displays the friendly "No results" message if search criteria match zero items
    document.getElementById('searchEmptyMsg').style.display = (visibleCount === 0 && rows.length > 0) ? "" : "none";
}

function openDeleteModal(id) {
    document.getElementById('confirmDeleteBtn').href = '?delete=' + id;
    document.getElementById('deleteModal').style.display = 'flex';
}
function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}
</script>
</body>
</html>