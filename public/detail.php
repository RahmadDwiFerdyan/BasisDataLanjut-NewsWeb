<?php
require '../config/db.php';

$collection = connectMongoDB();
$idParam = $_GET['id'] ?? null;
try {
    $id = new MongoDB\BSON\ObjectId($idParam);
} catch (Exception $e) {
    die('ID berita tidak valid. ');
}

$news = $collection->findOne(['_id' => $id]);
if (!$news) {
    die('Berita tidak ditemukan. ');
}

// Hitung rata-rata rating
$comments = $news['comments'] ?? [];
$totalRating = 0;
$ratingCount = 0;

foreach ($comments as $comment) {
    if (isset($comment['rating'])) {
        $totalRating += $comment['rating'];
        $ratingCount++;
    }
}

$averageRating = $ratingCount > 0 ? $totalRating / $ratingCount : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment']) && isset($_POST['rating'])) {
    $comment = htmlspecialchars($_POST['comment'], ENT_QUOTES, 'UTF-8');
    $rating = filter_var($_POST['rating'], FILTER_VALIDATE_INT);

    if (!$rating || $rating < 1 || $rating > 5) {
        die('Rating tidak valid. ');
    }
    date_default_timezone_set('Asia/Jakarta');

    $commentData = [
        'username' => 'Pengguna',
        'comment' => $comment,
        'rating' => $rating,
        'created_at' => new MongoDB\BSON\UTCDateTime()
    ];

    $collection->updateOne(
        ['_id' => $id],
        ['$push' => ['comments' => $commentData]]
    );

    header('Location: ' . $_SERVER['PHP_SELF'] . '?id=' . $idParam);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Berita</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../style/style.css">
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg bg-white">
            <div class="container">
                <a class="navbar-brand font-weight-bold" href="../public/index.php">BERITA NIH</a>
            </div>
        </nav>
    </header>

    <div class="container mt-4">
        <h1 style="color: #b61318;"><?php echo $news['title']; ?></h1>
        <p><em>Ditulis oleh <?php echo $news['author']; ?> | <?php echo $news['created_at']->toDateTime()->format('Y-m-d'); ?></em></p>

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

        <!-- Tampilkan Rata-rata Rating -->
        <section class="mt-4" style="border-top: solid 4px #b61318; padding-top: 20px;">
            <h3>Rating</h3>
            <h4>
                <?php echo number_format($averageRating, 1); ?> / 5
                <span class="rating">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <span class="bi bi-star-fill" style="color: <?php echo ($i <= $averageRating) ? 'gold' : 'lightgray'; ?>" title="<?php echo $i; ?> star"></span>
                    <?php endfor; ?>
                </span>
            </h4>
        </section>

        <!-- Form untuk menambahkan komentar dan rating -->
        <section class="mt-4" style="border-top: solid 4px #b61318; padding-top: 20px;">
            <h3 class="mt-4">Tambahkan Rating</h3>
            <form action="" method="post">
                <div class="form-group">
                    <label for="rating">Rating:</label>
                    <select class="form-control" id="rating" name="rating" required>
                        <option value="">Pilih</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="comment">Komentar (Opsional):</label>
                    <textarea class="form-control" id="comment" name="comment" rows="3" placeholder="Tuliskan komentar Anda..."></textarea>
                </div>
                <button type="submit" class="btn btn-outline-dark btn-custom">Kirim Rating</button>
            </form>
        </section>

        <!-- Tampilkan Komentar dan Rating -->
        <section class="mt-4" style="border-top: solid 4px #b61318;">
            <h3 class="mt-4">Komentar dan Rating</h3>
            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($comment['username']); ?></h5>
                            <p class="card-text">Rating: <?php echo $comment['rating']; ?> / 5</p>
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                            <p class="card-text"><small class="text-muted">Ditulis pada <?php echo $comment['created_at']->toDateTime()->format('Y-m-d H:i:s'); ?></small></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Belum ada komentar.</p>
            <?php endif; ?>
        </section>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>