<?php
$pdo = new PDO('mysql:host=localhost;dbname=survey', 'root', '');
try {
    $pdo->exec('ALTER TABLE surveys ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 0 AFTER is_public');
    echo "Column added\n";
} catch (PDOException $e) {
    echo "Column may already exist: " . $e->getMessage() . "\n";
}
$pdo->exec('UPDATE surveys SET is_active = 1 WHERE is_public = 1');
echo "Updated existing rows\n";
