<?php
session_start();
require_once '../config/database.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$db = getDB();
$message = '';

// Toggle availability
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $db->prepare("UPDATE rooms SET is_available = NOT is_available WHERE id = ?")->execute([$id]);
    header('Location: rooms.php');
    exit;
}

// Delete room
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $db->prepare("DELETE FROM rooms WHERE id = ?")->execute([$id]);
        $message = 'Room deleted.';
    } catch (PDOException $e) {
        $message = 'Cannot delete room with existing bookings.';
    }
}

// Add / Edit room
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $room_number = clean($_POST['room_number'] ?? '');
    $name = clean($_POST['name'] ?? '');
    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
    $description = clean($_POST['description'] ?? '');
    $room_type = clean($_POST['room_type'] ?? 'twin');
    $capacity = (int)($_POST['capacity'] ?? 2);
    $price = (float)($_POST['price_per_month'] ?? 0);
    $size_sqm = (int)($_POST['size_sqm'] ?? 20);
    $bed_type = clean($_POST['bed_type'] ?? 'Single Bed');
    $facilities = clean($_POST['facilities'] ?? '');
    $image_url = clean($_POST['image_url'] ?? '');
    $is_available = isset($_POST['is_available']) ? 1 : 0;

    if ($id > 0) {
        $stmt = $db->prepare("
            UPDATE rooms SET room_number=?, name=?, slug=?, description=?, room_type=?, capacity=?, 
            price_per_month=?, size_sqm=?, bed_type=?, facilities=?, image_url=?, is_available=?
            WHERE id=?
        ");
        $stmt->execute([$room_number, $name, $slug, $description, $room_type, $capacity, $price, $size_sqm, $bed_type, $facilities, $image_url, $is_available, $id]);
        $message = 'Room updated.';
    } else {
        $stmt = $db->prepare("
            INSERT INTO rooms (room_number, name, slug, description, room_type, capacity, price_per_month, size_sqm, bed_type, facilities, image_url, is_available)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$room_number, $name, $slug, $description, $room_type, $capacity, $price, $size_sqm, $bed_type, $facilities, $image_url, $is_available]);
        $message = 'Room added.';
    }
}

$rooms = $db->query("SELECT * FROM rooms ORDER BY room_number ASC")->fetchAll();
$edit = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM rooms WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $edit = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms | Grand TAR ABC Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 admin-sidebar px-0">
                <div class="text-center py-4">
                    <i class="fas fa-building fa-2x text-warning"></i>
                    <h5 class="text-white mt-2 mb-0" style="font-family:'Playfair Display',serif;">Grand TAR ABC</h5>
                    <small class="text-white-50">Admin Panel</small>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link" href="index.php"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
                    <a class="nav-link" href="bookings.php"><i class="fas fa-calendar-check me-2"></i> Bookings</a>
                    <a class="nav-link active" href="rooms.php"><i class="fas fa-bed me-2"></i> Rooms</a>
                    <a class="nav-link" href="students.php"><i class="fas fa-user-graduate me-2"></i> Students</a>
                    <a class="nav-link" href="../index.php" target="_blank"><i class="fas fa-external-link-alt me-2"></i> View Site</a>
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
                </nav>
            </div>

            <div class="col-md-9 col-lg-10 bg-light min-vh-100">
                <div class="p-4">
                    <h3 class="mb-4" style="font-family:'Playfair Display',serif;">Manage Rooms</h3>

                    <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
                    <?php endif; ?>

                    <!-- Form -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0 font-serif"><?php echo $edit ? 'Edit Room' : 'Add Room'; ?></h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <?php if ($edit): ?>
                                <input type="hidden" name="id" value="<?php echo $edit['id']; ?>">
                                <?php endif; ?>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Room Number *</label>
                                        <input type="text" name="room_number" class="form-control" required value="<?php echo htmlspecialchars($edit['room_number'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label">Name *</label>
                                        <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($edit['name'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Type *</label>
                                        <select name="room_type" class="form-select">
                                            <?php foreach (['single','twin','4-bed','6-bed'] as $t): ?>
                                            <option value="<?php echo $t; ?>" <?php echo (($edit['room_type'] ?? '') === $t) ? 'selected' : ''; ?>><?php echo ucfirst(str_replace('-',' ',$t)); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Capacity</label>
                                        <input type="number" name="capacity" class="form-control" min="1" value="<?php echo (int)($edit['capacity'] ?? 2); ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Price/Month</label>
                                        <input type="number" name="price_per_month" class="form-control" step="0.01" value="<?php echo htmlspecialchars($edit['price_per_month'] ?? '300'); ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Size (m²)</label>
                                        <input type="number" name="size_sqm" class="form-control" value="<?php echo (int)($edit['size_sqm'] ?? 20); ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Bed Type</label>
                                        <input type="text" name="bed_type" class="form-control" value="<?php echo htmlspecialchars($edit['bed_type'] ?? 'Single Bed'); ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Available</label>
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" name="is_available" <?php echo (!isset($edit) || ($edit['is_available'] ?? 1)) ? 'checked' : ''; ?>>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Description</label>
                                        <textarea name="description" class="form-control" rows="2"><?php echo htmlspecialchars($edit['description'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Facilities (comma-separated)</label>
                                        <input type="text" name="facilities" class="form-control" value="<?php echo htmlspecialchars($edit['facilities'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Image URL</label>
                                        <input type="url" name="image_url" class="form-control" value="<?php echo htmlspecialchars($edit['image_url'] ?? ''); ?>">
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-gold"><?php echo $edit ? 'Update Room' : 'Add Room'; ?></button>
                                        <?php if ($edit): ?>
                                        <a href="rooms.php" class="btn btn-outline-secondary">Cancel</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- List -->
                    <div class="card border-0 shadow-sm">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>No.</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Capacity</th>
                                        <th>Price/mo</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rooms as $r): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($r['room_number']); ?></td>
                                        <td><?php echo htmlspecialchars($r['name']); ?></td>
                                        <td><?php echo ucfirst(str_replace('-', ' ', $r['room_type'])); ?></td>
                                        <td><?php echo (int)$r['capacity']; ?></td>
                                        <td>RM <?php echo number_format($r['price_per_month'], 0); ?></td>
                                        <td>
                                            <?php if ($r['is_available']): ?>
                                            <span class="badge-available">Available</span>
                                            <?php else: ?>
                                            <span class="badge-full">Unavailable</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="rooms.php?edit=<?php echo $r['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="rooms.php?toggle=<?php echo $r['id']; ?>" class="btn btn-sm btn-outline-secondary">Toggle</a>
                                            <a href="rooms.php?delete=<?php echo $r['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this room?');">Delete</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
