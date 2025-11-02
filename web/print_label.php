<?php
set_include_path(get_include_path() . PATH_SEPARATOR . realpath(__DIR__ . '/../'));
spl_autoload_register(function ($class) {
    $file_as_is = str_replace('\\', '/', $class) . '.php';
    $file_lowercase = strtolower($file_as_is);

    if (stream_resolve_include_path($file_as_is)) {
        require_once $file_as_is;
    } else if (stream_resolve_include_path($file_lowercase)) {
        require_once $file_lowercase;
    }
});

use classes\db\object\Objet;

$objet = null;
$description = '';
$price = '';
$ref = '';

if (isset($_GET['objet_id'])) {
    $objet_id = $_GET['objet_id'];
    $objet = Objet::searchPk($objet_id);
    if ($objet) {
        $description = $objet->getDescription();
        $price = $objet->getPrix();
        $ref = $objet->getRef();
    }
} elseif (isset($_GET['description']) && isset($_GET['price']) && isset($_GET['ref'])) {
    $description = $_GET['description'];
    $price = $_GET['price'];
    $ref = $_GET['ref'];
} else {
    die('No data provided.');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Label Print</title>
    <link rel="stylesheet" type="text/css" href="stylesheets/label.css" />
    <script type="text/javascript">
        window.onload = function() {
            window.print();
            setTimeout(function() { window.close(); }, 100);
        }
    </script>
</head>
<body>
    <div class="label-container">
        <div class="label-ref"><?php echo htmlspecialchars($ref); ?></div>
        <div class="label-description"><?php echo htmlspecialchars($description); ?></div>
        <div class="label-price"><?php echo htmlspecialchars(number_format($price, 2, ',', '') . '€'); ?></div>
    </div>
</body>
</html>
