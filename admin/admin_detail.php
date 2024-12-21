<?php
require '../config/db.php';

$collection = connectMongoDB();
$idParam = $_GET['id'] ?? null;

try {
    $id = new MongoDB\BSON\ObjectId($idParam);
} catch (Exception $e) {
    die('ID berita tidak valid.');
}

$news = $collection->findOne(['_id' => $id]);
if (!$news) {
    die('Berita tidak ditemukan.');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Berita Admin</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style/style.css">
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg bg-white">
            <div class="container">
                <a class="navbar-brand font-weight-bold" href="../public/index.php">BERITA NIH<i> ADMIN</i></a>
            </div>
        </nav>
    </header>

    <div class="container mt-4 mb-4">
        <h1 style="color: #b61318;"><?php echo htmlspecialchars($news['title']); ?></h1>
        <p><em>Ditulis oleh <?php echo htmlspecialchars($news['author']); ?> | <?php echo $news['created_at']->toDateTime()->format('Y-m-d'); ?></em></p>

        <?php if (!empty($news['image'])): ?>
            <?php
            $imageData = $news['image']->getData(); // Ambil data biner
            $base64 = base64_encode($imageData); // Encode ke base64
            ?>
            <img src="data:image/jpeg;base64,<?php echo $base64; ?>" class="img-fluid" alt="<?php echo htmlspecialchars($news['title']); ?>">
        <?php endif; ?>

        <div class="mt-4">
            <p style="text-align: justify;"><?php echo nl2br(htmlspecialchars($news['content'])); ?></p>
        </div>

        <a href="admin_dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>