<header>
    <h1 class="title">E∙N∙I∙G∙M∙A</h1>
    <nav>
        <a class="nav-link <?php echo $home_page; ?>" href="/">Home</a>
        <a class="nav-link <?php echo $catalog_page; ?>" href="/catalog">Catalog</a>
        <a class="nav-link <?php echo $add_works_page; ?>" href="/add-works">Add Works</a>
        <a class="nav-link <?php echo $login_page; ?>" href="/login">Log <?php echo (is_user_logged_in()) ? 'Out' : 'In'; ?></a>
    </nav>
</header>
