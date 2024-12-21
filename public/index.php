<?php
require '../config/db.php';

$collection = connectMongoDB();

$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter = [];

if (!empty($category)) {
    $filter['category'] = $category;
}

if (!empty($search)) {
    $filter['title'] = new MongoDB\BSON\Regex($search, 'i');
}

$newsList = $collection->find($filter, ['sort' => ['created_at' => -1]]);
$categories = $collection->distinct('category');

// Ambil berita terbaru
$latestNews = $collection->find([], ['sort' => ['created_at' => -1], 'limit' => 5]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Berita</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style/style.css">
</head>

<body>
    <header class="sticky-top">
        <nav class="navbar navbar-expand-lg bg-white">
            <div class="container">
                <a class="navbar-brand font-weight-bold" href="../config/index.php">BERITA NIH</a>
                <div class="d-flex align-items-center">
                    <form class="form-inline mr-3" method="GET" action="index.php">
                        <div class="input-group">
                            <input type="search" name="search" class="form-control" placeholder="Cari Berita"
                                aria-label="Search">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">Cari</button>
                            </div>
                        </div>
                    </form>
                    <?php if (isset($_COOKIE['admin_logged_in']) && $_COOKIE['admin_logged_in'] == 'true') {
                        echo '<a class="btn btn-outline-dark" href="../admin/admin_dashboard.php">Dashboard Admin</a>';
                    } else {
                        echo '<a class="btn btn-outline-dark" href="../admin/login.php">Login Admin</a>';
                    } ?>
                </div>
            </div>
        </nav>
    </header>

    <main class="container mt-4">
        <section class="banner-welcome mb-4 text-center py-4"
            style="background: linear-gradient(to right, #ff7e5f, #feb47b); border-radius: 12px;">
            <h2 style="color: white; font-size: 1.8rem; font-weight: bold;">Temukan Berita Terkini dan Terpercaya di <i
                    style="color: #b61318;">BERITA NIH</i>!</h2>
            <p style="color: white; font-size: 1rem;">Dapatkan informasi terbaru, analisis mendalam, dan berita yang
                Anda butuhkan, semua dalam satu tempat!</p>
        </section>

        <!-- Tambahkan Disini -->
         
        <!-- Slider Berita -->
        <div id="newsCarousel" class="carousel slide mb-4" data-ride="carousel">
            <div class="carousel-inner">
                <?php $isActive = true; ?>
                <?php foreach ($latestNews as $news): ?>
                    <div class="carousel-item <?php echo $isActive ? 'active' : ''; ?>"
                        onclick="window.location.href='detail.php?id=<?php echo $news['_id']; ?>';"
                        style="cursor: pointer;">
                        <?php if (!empty($news['image'])): ?>
                            <?php
                            $imageData = $news['image']->getData(); // Ambil data biner
                            $base64 = base64_encode($imageData); // Encode ke base64
                            ?>
                            <div style="position: relative;">
                                <img src="data:image/jpeg;base64,<?php echo $base64; ?>" class="d-block w-100"
                                    style="height: 450px; object-fit: cover;">
                                <div style="position: absolute; bottom: 0; width: 100%; background: linear-gradient(to top, rgba(182, 19, 24, 0.8), rgba(255,0,0,0)); padding: 20px 40px;">
                                    <h3 class="text-left text-white"><?php echo htmlspecialchars($news['title']); ?></h3>
                                </div>
                            </div>

                        <?php else: ?>
                            <img src="../uploads/default-image.jpg" class="d-block w-100"
                                style="height: 200px; object-fit: cover;" alt="Gambar Berita Default">
                            <!-- Gambar default jika tidak ada -->
                        <?php endif; ?>
                        <div class="carousel-caption d-none d-md-block">
                            
                        </div>
                    </div>
                    <?php $isActive = false; ?>
                <?php endforeach; ?>
            </div>
            <a class="carousel-control-prev" href="#newsCarousel" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#newsCarousel" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>


        <section class="category-badges mb-4">
            <a href="index.php" class="badge <?php echo empty($category) ? 'active' : ''; ?>">Semua</a>
            <?php foreach ($categories as $categoryItem): ?>
                <a href="index.php?category=<?php echo urlencode($categoryItem); ?>"
                    class="badge <?php echo $category === $categoryItem ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($categoryItem); ?>
                </a>
            <?php endforeach; ?>
        </section>

        <section class="news-list row">
            <?php foreach ($newsList as $news): ?>
                <article class="col-md-4 mb-4">
                    <div class="card h-100 clickable-card"
                        onclick="window.location.href='detail.php?id=<?php echo $news['_id']; ?>';">
                        <?php if (!empty($news['image'])): ?>
                            <?php
                            $imageData = $news['image']->getData(); // Ambil data biner
                            $base64 = base64_encode($imageData); // Encode ke base64
                            ?>
                            <img src="data:image/jpeg;base64,<?php echo $base64; ?>" class="card-img-top"
                                alt="<?php echo htmlspecialchars($news['title']); ?>">
                        <?php else: ?>
                            <img src="../uploads/default-image.jpg" class="card-img-top" alt="Gambar Berita Default">
                            <!-- Gambar default jika tidak ada -->
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo htmlspecialchars($news['title']); ?></h5>
                            <p class="card-text flex-grow-1">
                                <?php echo htmlspecialchars(substr($news['summary'], 0, 100)); ?>...
                            </p>
                            <div class="d-flex justify-content-start">
                                <span class="badge"><?php echo htmlspecialchars($news['category']); ?></span>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <small class="text-muted"><?php echo htmlspecialchars($news['author']); ?></small>
                            <small
                                class="text-muted"><?php echo $news['created_at']->toDateTime()->format('Y-m-d'); ?></small>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>
    </main>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>