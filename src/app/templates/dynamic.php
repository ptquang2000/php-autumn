<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic URL</title>
</head>
<body>
    <?php if (!$isEven):?>
        <p><?=$number?> is an even number</p>
    <?php else:?>
        <p><?=$number?> is an odd number</p>
    <?php endif?>
    <br>
    <a href="/your-path">go back</a>
</body>
</html>