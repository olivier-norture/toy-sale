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

$page = new \classes\pages\RestitutionEnvelope();


// Array of sentences
$sentences = $page->envelopeGetAllParticipants();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Bourse aux Jouets Chambly</title>
    <link rel="stylesheet" type="text/css" href="stylesheets/reset.css" />
    <link rel="stylesheet" type="text/css" href="stylesheets/main.css" />
    <link rel="stylesheet" type="text/css" href="stylesheets/style.css" />

    <style>
        @media print {
            .page {
                page-break-after: always;
            }
        }
        .page {
            margin-bottom: 50px; /* Optional: adds some space before the page break */
        }
    </style>

    <script type="text/javascript" src="js/script.js"></script>
    <script type="text/javascript">
        window.onload = function(){
        }

        async print(){
            imprimer_page();
            cancel_print();
        }
    </script>
</head>
<body>

<?php
$page->renderTemplateHeader();

// TODO: print only if user is authenticated

echo '<button name="action" type="submit" value="depot" onclick="imprimer_page()" class="notPrintable">Imprimer</button>
        <div id="main_content"> <!-- main_content -->';
// Loop through the sentences and display each in a div
foreach ($sentences as $sentence) {
    echo "<div class='page'><p style='font-size: xxx-large'>$sentence</p></div>";
}

echo '</div> <!-- main_content -->';

$page->renderTemplateFooter();
?>

</body>
</html>