<?php
require '../config/db.php';


if (isset($_GET['id'])) {
    $id = new MongoDB\BSON\ObjectId($_GET['id']);
    $collection = connectMongoDB();

    $result = $collection->deleteOne(['_id' => $id]);
    
    if ($result->getDeletedCount() > 0) {
        header('Location: admin_dashboard.php?message=Berita berhasil dihapus');
    } else {
        header('Location: admin_dashboard.php?message=Gagal menghapus berita');
    }
    exit;
} else {
    header('Location: admin_dashboard.php?message=ID berita tidak valid');
    exit;
}
?>