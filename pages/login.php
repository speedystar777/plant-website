<?php
include("includes/init.php");
$login_page = 'here';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>ENIGMA</title>

  <link rel="stylesheet" type="text/css" href="/public/styles/site.css" media="all" />
  <link rel="stylesheet" type="text/css" href="/public/styles/loginpage.css" media="all" />
</head>

<body class="login-page">
  <?php include("includes/header.php"); ?>

  <?php if (!is_user_logged_in()) {
    echo_login_form("/login", $session_messages);
  } else { ?>
    <div class="border">
      <div class="border2">
        <div class="description">WELCOME <?php echo strtoupper($current_user['first_name'])?>!</div>
        <div class="sub-text">You are currently logged in</div>
        <a class="gbutton" href="<?php echo logout_url(); ?>">LOG OUT →</a>
      </div>
    </div>
  <?php } ?>

  <?php include("includes/footer.php"); ?>
</body>

</html>
