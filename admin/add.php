<?php 
require '../config/db.php'; 

$newsCollection = connectMongoDB(); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
    date_default_timezone_set('Asia/Jakarta');
    $newNews = [ 
        'title' => $_POST['title'], 
        'content' => $_POST['content'], 
        'summary' => $_POST['summary'], 
        'author' => $_POST['author'], 
        'category' => $_POST['category'], 
        'created_at' => new MongoDB\BSON\UTCDateTime(), 
        'updated_at' => new MongoDB\BSON\UTCDateTime(), 
        'image' => null // Inisialisasi dengan null
    ]; 

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) { 
        // Baca file gambar
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
        // Simpan gambar sebagai binary data
        $newNews['image'] = new MongoDB\BSON\Binary($imageData, MongoDB\BSON\Binary::TYPE_GENERIC);
    } else { 
        echo "Error: " . $_FILES['image']['error']; 
    } 

    $newsCollection->insertOne($newNews); 
    header('Location: admin_dashboard.php'); 
    exit; 
} 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Berita</title>
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
        <h2 class="text-center mb-4">Tambah Berita</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Judul</label>
                <input type="text" name="title" id="title" class="form-control" placeholder="Masukkan judul berita" required>
            </div>
            <div class="form-group">
                <label for="content">Konten</label>
                <textarea name="content" id="content" class="form-control" rows="5" placeholder="Masukkan konten berita" required></textarea>
            </div>
            <div class="form-group">
                <label for="summary">Ringkasan</label>
                <input type="text" name="summary" id="summary" class="form-control" placeholder="Masukkan ringkasan berita" required>
            </div>
            <div class="form-group">
                <label for="author">Penulis</label>
                <input type="text" name="author" id="author" class="form-control" placeholder="Masukkan nama penulis" required>
            </div>
            <div class="form-group">
                <label for="category">Kategori</label>
                <input type="text" name="category" id="category" class="form-control" placeholder="Masukkan kategori berita" required>
            </div>
            <div class="form-group">
                <label for="image">Gambar</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Tambah Berita</button>
        </form>
    </main>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>

</body>

</html>