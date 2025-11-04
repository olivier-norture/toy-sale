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
if (isset($_GET['objet_id'])) {
    $objet_id = $_GET['objet_id'];
    $objet = Objet::searchPk($objet_id);
}

if ($objet === null) {
    die('Objet not found.');
}

$description = $objet->getDescription();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Label Print</title>
    <link rel="stylesheet" type="text/css" href="stylesheets/label.css" />
    <script type="text/javascript">
        window.onload = function() {
            window.print();
            <?php if (isset($_GET['redirect_url']) && !empty($_GET['redirect_url'])):
            ?>
            setTimeout(function() { window.location.href = '<?php echo htmlspecialchars($_GET['redirect_url']); ?>'; }, 100);
            <?php else: ?>
            setTimeout(function() { window.close(); }, 100);
            <?php endif; ?>
        }
    </script>
</head>
<body>
    <div class="label-container">
        <div class="label-ref"><?php echo htmlspecialchars($objet->getRef()); ?></div>
        <div class="label-description"><?php echo htmlspecialchars($description); ?></div>
        <div class="label-price"><?php echo htmlspecialchars(number_format($objet->getPrix(), 2, ',', '') . '€'); ?></div>
    </div>
</body>
</html>
