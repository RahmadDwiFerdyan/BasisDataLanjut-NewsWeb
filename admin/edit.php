<?php
require '../config/db.php';

$newsCollection = connectMongoDB();

$id = new MongoDB\BSON\ObjectId($_GET['id']);
$news = $newsCollection->findOne(['_id' => $id]);

if (!isset($_GET['id'])) {
    header('Location: admin_dashboard.php?message=ID berita tidak valid');
    exit;
}

if (!$news) {
    header('Location: admin_dashboard.php?message=Berita tidak ditemukan');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $updateNews = [
        'title' => $_POST['title'],
        'content' => $_POST['content'],
        'summary' => $_POST['summary'],
        'author' => $_POST['author'],
        'category' => $_POST['category'],
        'updated_at' => new MongoDB\BSON\UTCDateTime()
    ];

    $currentImage = $news['image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // Baca file gambar 
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
        // Simpan gambar sebagai binary data 
        $updateNews['image'] = new MongoDB\BSON\Binary($imageData, MongoDB\BSON\Binary::TYPE_GENERIC);
    } else {
        // Jika tidak ada gambar baru, gunakan gambar saat ini
        $updateNews['image'] = $currentImage;
    }

    $newsCollection->updateOne(
        ['_id' => $id],
        ['$set' => $updateNews]
    );

    header('Location: admin_dashboard.php?message=Berita berhasil diperbarui');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Berita</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style/style.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins&display=swap');

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f8f9fa;
        }

        .form-container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            color: #303338;
        }

        .btn-primary {
            background-color: #b61318;
            border: none;
        }

        .btn-primary:hover {
            background-color: #9c1015;
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg bg-white">
            <div class="container">
                <a class="navbar-brand font-weight-bold" href="../public/index.php">BERITA NIH<i> ADMIN</i></a>
            </div>
        </nav>
    </header>

    <main class="form-container">
        <h2 class="text-center mb-4">Edit Berita</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Judul</label>
                <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($news['title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="content">Konten</label>
                <textarea name="content" id="content" class="form-control" rows="5" required><?php echo htmlspecialchars($news['content']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="summary">Ringkasan</label>
                <textarea name="summary" id="summary" class="form-control" rows="3" required><?php echo htmlspecialchars($news['summary']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="author">Penulis</label>
                <input type="text" name="author" id="author" class="form-control" value="<?php echo htmlspecialchars($news['author']); ?>" required>
            </div>
            <div class="form-group">
                <label for="category">Kategori</label>
                <input type="text" name="category" id="category" class="form-control" value="<?php echo htmlspecialchars($news['category']); ?>" required>
            </div>
            <div class="form-group">
                <label for="image">Gambar</label>
                <input type="file" name="image" id="image" class="form-control-file" accept="image/*">
                <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah gambar.</small>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </main>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>