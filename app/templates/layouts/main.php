<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogy</title>

    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    <?php include __DIR__ . '/../partials/header.php' ?>


    <main class="site-main">

        <div class="container">
            <?= $content ?>
        </div>

    </main>


    <?php include __DIR__ . '/../partials/footer.php' ?>
</body>
</html>