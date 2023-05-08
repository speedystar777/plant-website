<?php
include("includes/init.php");
$catalog_page = 'here';

$query = 'SELECT DISTINCT items.* FROM items';

$has_continent = false;
$has_country = false;
$has_medium = false;
$has_museum = false;
$has_filtering = false;

$open = 'open';


$continents = exec_sql_query(
  $db,
  "SELECT * FROM tags WHERE category == 'Continent' ORDER BY tag ASC;"
)->fetchAll();
$countries = exec_sql_query(
  $db,
  "SELECT * FROM tags WHERE category == 'Country' ORDER BY tag ASC;"
)->fetchAll();
$mediums = exec_sql_query(
  $db,
  "SELECT * FROM tags WHERE category == 'Medium' ORDER BY tag ASC;"
)->fetchAll();
$museums = exec_sql_query(
  $db,
  "SELECT * FROM tags WHERE category == 'Museum' ORDER BY tag ASC;"
)->fetchAll();

$sticky_continent_checkboxes = array_fill(0, sizeof($continents), '');
$sticky_country_checkboxes = array_fill(0, sizeof($countries), '');
$sticky_medium_checkboxes = array_fill(0, sizeof($mediums), '');
$sticky_museum_checkboxes = array_fill(0, sizeof($museum), '');

$continent_filter = '';
$country_filter = '';
$medium_filter = '';
$museum_filter = '';

foreach ($continents as $index => $continent) {
  $tag_name = str_replace(' ', '-', strtolower($continent['tag']));
  $should_filter = (bool)$_GET[$tag_name]; // untrusted
  $sticky_continent_checkboxes[$index] = ($should_filter ? 'checked' : '');
  if ($should_filter) {
    $continent_filter = $continent_filter . ($has_continent ? ' OR ' : '') . "(tags.tag = '" . $continent['tag'] . "')";
    $has_continent = true;
    $has_filtering = true;
  }
}
foreach ($countries as $index => $country) {
  $tag_name = str_replace(' ', '-', strtolower($country['tag']));
  $should_filter = (bool)$_GET[$tag_name]; // untrusted
  $sticky_country_checkboxes[$index] = ($should_filter ? 'checked' : '');
  if ($should_filter) {
    $country_filter = $country_filter . ($has_country ? ' OR ' : '') . "(tags.tag = '" . $country['tag'] . "')";
    $has_country = true;
    $has_filtering = true;
  }
}
foreach ($mediums as $index => $medium) {
  $tag_name = str_replace(' ', '-', strtolower($medium['tag']));
  $should_filter = (bool)$_GET[$tag_name]; // untrusted
  $sticky_medium_checkboxes[$index] = ($should_filter ? 'checked' : '');
  if ($should_filter) {
    $medium_filter = $medium_filter . ($has_medium ? ' OR ' : '') . "(tags.tag = '" . $medium['tag'] . "')";
    $has_medium = true;
    $has_filtering = true;
  }
}
foreach ($museums as $index => $museum) {
  $tag_name = str_replace(' ', '-', strtolower($museum['tag']));
  $should_filter = (bool)$_GET[$tag_name]; // untrusted
  $sticky_museum_checkboxes[$index] = ($should_filter ? 'checked' : '');
  if ($should_filter) {
    $museum_filter = $museum_filter . ($has_museum ? ' OR ' : '') . "(tags.tag = '" . $museum['tag'] . "')";
    $has_museum = true;
    $has_filtering = true;
  }
}
if ($has_filtering) {
  $query = $query . ' WHERE ' .
    ($has_continent ? 'EXISTS ( SELECT * FROM item_tags LEFT OUTER JOIN tags ON item_tags.tag_id=tags.id WHERE item_tags.item_id = items.id AND (' : '') . $continent_filter . ($has_continent ? '))' : '') .
    ($has_continent && $has_country ? ' AND ' : '') . ($has_country ? 'EXISTS ( SELECT * FROM item_tags LEFT OUTER JOIN tags ON item_tags.tag_id=tags.id WHERE item_tags.item_id = items.id AND (' : '') . $country_filter . ($has_country ? '))' : '') .
    ($has_medium && ($has_continent || $has_country) ? ' AND ' : '') . ($has_medium ? 'EXISTS ( SELECT * FROM item_tags LEFT OUTER JOIN tags ON item_tags.tag_id=tags.id WHERE item_tags.item_id = items.id AND (' : '') . $medium_filter . ($has_medium ? '))' : '') . ($has_museum && ($has_continent || $has_country || $has_medium) ? ' AND ' : '') . ($has_museum ? 'EXISTS ( SELECT * FROM item_tags LEFT OUTER JOIN tags ON item_tags.tag_id=tags.id WHERE item_tags.item_id = items.id AND (' : '') . $museum_filter . ($has_museum ? '))' : '');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>ENIGMA</title>

  <link rel="stylesheet" type="text/css" href="/public/styles/site.css" media="all" />
  <link rel="stylesheet" type="text/css" href="/public/styles/catalog.css" media="all" />
  <script src="/public/scripts/jquery-3.5.1.js" type="text/javascript"></script>
  <script src="/public/scripts/filter-categories.js" type="text/javascript" async></script>
  <script src="/public/scripts/open-modal.js" type="text/javascript" async></script>
</head>

<body class="catalog-page">
  <?php include("includes/header.php"); ?>
  <div class="catalog-body">
    <div class="filter-bar">
      <form action="/catalog" method="get" novalidate>
        <div class="filter-title">
          FILTER BY TAGS
        </div>
        <div class="collapsible">Continent</div>
        <div class="content <?php if ($has_continent) echo $open; ?>">
          <?php
          $continents = exec_sql_query(
            $db,
            "SELECT * FROM tags WHERE category == 'Continent' ORDER BY tag ASC;"
          )->fetchAll();

          foreach ($continents as $index => $continent) {
            $tag_name = str_replace(' ', '-', strtolower($continent['tag'])); ?>
            <div class="tag-name"><label><?php echo $continent['tag']; ?><input type="checkbox" class="tag-button" name="<?php echo htmlspecialchars($tag_name); ?>" value="1" <?php echo $sticky_continent_checkboxes[$index]; ?> /></label></div>
          <?php
          }
          ?>
        </div>
        <div class="collapsible">Country</div>
        <div class="content <?php if ($has_country) echo $open; ?>">
          <?php
          $countries = exec_sql_query(
            $db,
            "SELECT * FROM tags WHERE category == 'Country' ORDER BY tag ASC;"
          )->fetchAll();

          foreach ($countries as $index => $country) {
            $tag_name = str_replace(' ', '-', strtolower($country['tag'])); ?>
            <div class="tag-name"><label><?php echo $country['tag']; ?><input type="checkbox" class="tag-button" name="<?php echo htmlspecialchars($tag_name); ?>" value="1" <?php echo $sticky_country_checkboxes[$index]; ?> /></label></div>
          <?php
          }
          ?>
        </div>
        <div class="collapsible">Medium</div>
        <div class="content <?php if ($has_medium) echo $open; ?>">
          <?php
          $mediums = exec_sql_query(
            $db,
            "SELECT * FROM tags WHERE category == 'Medium' ORDER BY tag ASC;"
          )->fetchAll();

          foreach ($mediums as $index => $medium) {
            $tag_name = str_replace(' ', '-', strtolower($medium['tag'])); ?>
            <div class="tag-name"><label><?php echo $medium['tag']; ?><input type="checkbox" class="tag-button" name="<?php echo htmlspecialchars($tag_name); ?>" value="1" <?php echo $sticky_medium_checkboxes[$index]; ?> /></label></div>
          <?php
          }
          ?>
        </div>
        <div class="collapsible">Museum</div>
        <div class="content <?php if ($has_museum) echo $open; ?>">
          <?php
          $museums = exec_sql_query(
            $db,
            "SELECT * FROM tags WHERE category == 'Museum' ORDER BY tag ASC;"
          )->fetchAll();

          foreach ($museums as $index => $museum) {
            $tag_name = str_replace(' ', '-', strtolower($museum['tag'])); ?>
            <div class="tag-name"><label><?php echo $museum['tag']; ?><input type="checkbox" class="tag-button" name="<?php echo htmlspecialchars($tag_name); ?>" value="1" <?php echo $sticky_museum_checkboxes[$index]; ?> /></label></div>
          <?php
          }
          ?>
        </div>
        <button name="filter" class="filter-button">
          Apply
        </button>
      </form>
    </div>
    <div>
      <div class="description">
        Catalog of Art
      </div>
      <?php
      $query = $query . ' ORDER BY items.artwork_title ASC';
      $records = exec_sql_query(
        $db,
        $query
      )->fetchAll();

      if (count($records) > 0) { ?>
        <div class="image-row">
          <?php
          foreach ($records as $record) {
          ?>
            <div class="image-section">
              <div class="image-card">
                <div class="image-container">

                  <img class="image" src="/public/uploads/items/<?php echo $record['id'] . '.' . $record['file_ext']; ?>" alt="<?php echo htmlspecialchars($record['artwork_title']); ?>" />
                </div>
                <div class="image-description"><a class="description-link" href="/catalog/art-page?<?php echo http_build_query(array('id' => $record['id'])); ?>"><?php echo htmlspecialchars($record['artwork_title']); ?></a></div>
              </div>
            </div>

          <?php
          } ?>
        </div>
      <?php
      } else {
      ?>
        <div class="border">
          <div class="border2">
            <div class="sub-text">No Artworks Found</div>
          </div>
        </div>
      <?php
      }
      ?>

    </div>
  </div>

</body>

</html>
