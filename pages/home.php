<?php
include("includes/init.php");
$home_page = 'here';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>ENIGMA</title>

  <link rel="stylesheet" type="text/css" href="/public/styles/site.css" media="all" />
  <link rel="stylesheet" type="text/css" href="/public/styles/homepage.css" media="all" />
</head>

<!-- Source: https://i.pinimg.com/originals/7f/99/ec/7f99ec68714a2271fcc78fbc46466132.jpg-->

<body class="home-page">
  <?php include("includes/header.php"); ?>
  <div class="description">
    A Modern Art Catalog
  </div>
  <div class="gallery-button">
    <a class="gbutton" href="/catalog">VIEW WORKS →</a>
  </div>
</body>

</html>
