<?php
$newHash = password_hash('admin123', PASSWORD_BCRYPT);
echo "Hash baru: " . $newHash;

?>