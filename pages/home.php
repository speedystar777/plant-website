<?php include("includes/init.php");

include_once("includes/db.php");
$db = open_sqlite_db("db/catalog.sqlite");

// SEARCH, FILTER, & SORT VARIABLES --------------------------------------
$query = 'SELECT * FROM houseplants';
$select_params = array();
$has_where = false;
$has_search = false;


// SEARCH ----------------------------------------------------------------
// search inputs
$search_terms = NULL;
$sticky_search = '';
// if search is submitted
if (isset($_GET['query'])) {
  $search_terms = $_GET['query']; // untrusted
  $search_terms = trim($search_terms); //untrusted
  if (empty($search_terms)) {
    $search_terms = NULL;
  }
  $sticky_search = $search_terms; // tainted
}
// add search to sql query
if ($search_terms) {
  $query = $query . " WHERE scientific_name LIKE '%' || :search || '%' OR common_name LIKE '%' || :search || '%'";
  $select_params[':search'] = $search_terms;
  $has_where = true;
  $has_search = true;
}


// FILTERS ---------------------------------------------------------------
// 1 = true & NULL = false
$lowsun = (bool)$_GET['lowsun']; // untrusted
$mediumsun = (bool)$_GET['mediumsun']; // untrusted
$highsun = (bool)$_GET['highsun']; // untrusted
$directsun = (bool)$_GET['directsun']; // untrusted
$lowhum = (bool)$_GET['lowhum']; // untrusted
$normalhum = (bool)$_GET['normalhum']; // untrusted
$highhum = (bool)$_GET['highhum']; // untrusted
$yes_safety = (bool)$_GET['yes']; // untrusted
$no_safety = (bool)$_GET['no']; // untrusted
// sticky values
$sticky_lowsun = ($lowsun ? 'checked' : '');
$sticky_mediumsun = ($mediumsun ? 'checked' : '');
$sticky_highsun = ($highsun ? 'checked' : '');
$sticky_directsun = ($directsun ? 'checked' : '');
$sticky_lowhum = ($lowhum ? 'checked' : '');
$sticky_normalhum = ($normalhum ? 'checked' : '');
$sticky_highhum = ($highhum ? 'checked' : '');
$sticky_yes_safety = ($yes_safety ? 'checked' : '');
$sticky_no_safety = ($no_safety ? 'checked' : '');
// sql filtering
if ($lowsun || $mediumsun || $highsun || $directsun || $lowhum || $normalhum || $highhum || $yes_safety || $no_safety) {
  $sql_sun_filter = '';
  $has_sun = false;
  if ($lowsun) {
    $sql_sun_filter = $sql_sun_filter . ($has_sun ? ' OR ' : '') . "(sunlight = 'Low Indirect')";
    $has_sun = true;
  }
  if ($mediumsun) {
    $sql_sun_filter = $sql_sun_filter . ($has_sun ? ' OR ' : '') . "(sunlight = 'Medium Indirect')";
    $has_sun = true;
  }
  if ($highsun) {
    $sql_sun_filter = $sql_sun_filter . ($has_sun ? ' OR ' : '') . "(sunlight = 'High Indirect')";
    $has_sun = true;
  }
  if ($directsun) {
    $sql_sun_filter = $sql_sun_filter . ($has_sun ? ' OR ' : '') . "(sunlight = 'Direct')";
    $has_sun = true;
  }

  $sql_hum_filter = '';
  $has_hum = false;
  if ($lowhum) {
    $sql_hum_filter = $sql_hum_filter . ($has_hum ? ' OR ' : '') . "(humidity = 'Low')";
    $has_hum = true;
  }
  if ($normalhum) {
    $sql_hum_filter = $sql_hum_filter . ($has_hum ? ' OR ' : '') . "(humidity = 'Normal')";
    $has_hum = true;
  }
  if ($highhum) {
    $sql_hum_filter = $sql_hum_filter . ($has_hum ? ' OR ' : '') . "(humidity = 'High')";
    $has_hum = true;
  }

  $sql_safety_filter = '';
  $has_safety = false;
  if ($yes_safety) {
    $sql_safety_filter = $sql_safety_filter . ($has_safety ? ' OR ' : '') . "(pet_baby_safety = 'Yes')";
    $has_safety = true;
  }
  if ($no_safety) {
    $sql_safety_filter = $sql_safety_filter . ($has_safety ? ' OR ' : '') . "(pet_baby_safety = 'No')";
    $has_safety = true;
  }

  if (!$has_where) {
    $query = $query . ' WHERE ';
    $sql_has_where = true;
  }
  if ($has_search) {
    $query = $query . ' AND ';
  }
  $query = $query . '(' . ($has_sun ? '(' : '') . $sql_sun_filter . ($has_sun ? ')' : '') .
    ($has_hum && $has_sun ? ' AND ' : '') . ($has_hum ? '(' : '') . $sql_hum_filter . ($has_hum ? ')' : '') .
    ($has_safety && ($has_hum || $has_sun) ? ' AND ' : '') . ($has_safety ? '(' : '') . $sql_safety_filter . ($has_safety ? ')' : '') . ')';
}


// SORT ------------------------------------------------------------------
$sort = $_GET['sort']; // untrusted
$order = $_GET['order']; // untrusted
//css classes
$css_sciname_both = 'hidden';
$css_sciname_up = '';
$css_sciname_down = 'hidden';
$css_name_both = 'hidden';
$css_name_up = '';
$css_name_down = 'hidden';
$css_water_both = 'hidden';
$css_water_up = '';
$css_water_down = 'hidden';
$css_mintemp_both = 'hidden';
$css_mintemp_up = '';
$css_mintemp_down = 'hidden';
$css_maxtemp_both = 'hidden';
$css_maxtemp_up = '';
$css_maxtemp_down = 'hidden';
// next sort array
$order_next_url = array(
  'sciname' => 'asc',
  'name' => 'asc',
  'water' => 'asc',
  'mintemp' => 'asc',
  'maxtemp' => 'asc'
);
// type of sort
if ($order == 'asc') {
  $order_sql = 'ASC';
  $order_next = 'desc';
} else if ($order == 'desc') {
  $order_sql = 'DESC';
  $order_next = NULL;
} else {
  $order = NULL;
  $sort = NULL;
}
// sql sorting
if (in_array($sort, array('sciname', 'name', 'water', 'mintemp', 'maxtemp'))) {
  if ($sort == 'sciname') {
    $query = $query . ' ORDER BY scientific_name ' . $order_sql;
    if ($order_next == 'asc') {
      $css_sciname_both = 'hidden';
      $css_sciname_up = '';
      $css_sciname_down = 'hidden';
    } else if ($order_next == 'desc') {
      $css_sciname_both = 'hidden';
      $css_sciname_up = 'hidden';
      $css_sciname_down = '';
    } else {
      $css_sciname_both = '';
      $css_sciname_up = 'hidden';
      $css_sciname_down = 'hidden';
    }
  } elseif ($sort == 'name') {
    $query = $query . ' ORDER BY common_name ' . $order_sql;
    if ($order_next == 'asc') {
      $css_name_both = 'hidden';
      $css_name_up = '';
      $css_name_down == 'hidden';
    } else if ($order_next == 'desc') {
      $css_name_both = 'hidden';
      $css_name_up = 'hidden';
      $css_name_down = '';
    } else {
      $css_name_both = '';
      $css_name_up = 'hidden';
      $css_name_down = 'hidden';
    }
  } elseif ($sort == 'water') {
    $query = $query . ' ORDER BY water_frequency ' . $order_sql;
    if ($order_next == 'asc') {
      $css_water_both = 'hidden';
      $css_water_up = '';
      $css_water_down = 'hidden';
    } else if ($order_next == 'desc') {
      $css_water_both = 'hidden';
      $css_water_up = 'hidden';
      $css_water_down = '';
    } else {
      $css_water_both = '';
      $css_water_up = 'hidden';
      $css_water_down = 'hidden';
    }
  } elseif ($sort == 'mintemp') {
    $query = $query . ' ORDER BY min_temp ' . $order_sql;
    if ($order_next == 'asc') {
      $css_mintemp_both = 'hidden';
      $css_mintemp_up = '';
      $css_mintemp_down = 'hidden';
    } else if ($order_next == 'desc') {
      $css_mintemp_both = 'hidden';
      $css_mintemp_up = 'hidden';
      $css_mintemp_down = '';
    } else {
      $css_mintemp_both = '';
      $css_mintemp_up = 'hidden';
      $css_mintemp_down = 'hidden';
    }
  } elseif ($sort == 'maxtemp') {
    $query = $query . ' ORDER BY max_temp ' . $order_sql;
    if ($order_next == 'asc') {
      $css_maxtemp_both = 'hidden';
      $css_maxtemp_up = '';
      $css_sciname_down = 'hidden';
    } else if ($order_next == 'desc') {
      $css_maxtemp_both = 'hidden';
      $css_maxtemp_up = 'hidden';
      $css_maxtemp_down = '';
    } else {
      $css_maxtemp_both = '';
      $css_maxtemp_up = 'hidden';
      $css_maxtemp_down = 'hidden';
    }
  }
  $order_next_url[$sort] = $order_next;
} else {
  $sort = NULL;
}
$sort_url = '/?';
// build query string
$sort_query = http_build_query(
  array(
    'query' => $search_terms,
    'lowsun' => $lowsun,
    'mediumsun' => $mediumsun,
    'highsun' => $highsun,
    'directsun' => $directsun,
    'lowhum' => $lowhum,
    'normalhum' => $normalhum,
    'highhum' => $highhum,
    'yes' => $yes_safety,
    'no' => $no_safety
  )
);
$sort_url = $sort_url . $sort_query;


// FORM VALIDATION -------------------------------------------------------
// additional validation constraints
$sci_name_not_unique = False;
$max_less_than_min = False;
$record_inserted = False;
$record_insert_failed = False;
// form inputs
$sci_name = '';
$name = '';
$sun = '';
$water = '';
$hum = '';
$min = '';
$max = '';
$safe = '';
$extra = '';
// form sticky values
$sticky_sci_name = '';
$sticky_name = '';
$sticky_low_indirect = '';
$sticky_med_indirect = '';
$sticky_high_indirect = '';
$sticky_direct = '';
$sticky_water = '';
$sticky_low_hum = '';
$sticky_nor_hum = '';
$sticky_high_hum = '';
$sticky_min = '';
$sticky_max = '';
$sticky_safe_yes = '';
$sticky_safe_no = '';
$sticky_extra = '';
// feedback
$sci_name_feedback = 'hidden';
$name_feedback = 'hidden';
$sun_feedback = 'hidden';
$water_feedback = 'hidden';
$hum_feedback = 'hidden';
$min_feedback = 'hidden';
$max_feedback = 'hidden';
$safe_feedback = 'hidden';
//form display
$form_valid = TRUE;
$show_form = True;
$show_confirmation = False;
// if form is submitted
if (isset($_POST["submit"])) {

  $sci_name = trim($_POST["sci-name"]); //untrusted
  $name = trim($_POST["name"]); //untrusted
  $sun = $_POST["sun"]; // untrusted
  $water = trim($_POST["water"]); // untrusted
  $hum = $_POST["hum"]; // untrusted
  $min = trim($_POST["min"]); // untrusted
  $max = trim($_POST["max"]); // untrusted
  $safe = $_POST["safe"]; // untrusted
  $extra = trim($_POST["extra"]); // untrusted

  if (empty($sci_name)) {
    $form_valid = FALSE;
    $sci_name_feedback = '';
  } else {
    $sci_name = ucfirst(strtolower($sci_name)); // tainted

    $records = exec_sql_query(
      $db,
      "SELECT * FROM houseplants WHERE (scientific_name = :sci_name);",
      array(
        ':sci_name' => $sci_name
      )
    )->fetchAll();
    if (count($records) > 0) {
      $form_valid = False;
      $sci_name_not_unique = True;
    }
  }

  if (empty($name)) {
    $form_valid = FALSE;
    $name_feedback = '';
  } else {
    $name = ucwords(strtolower($name)); // tainted
  }

  if (!in_array($sun, array('Low Indirect', 'Medium Indirect', 'High Indirect', 'Direct'))) {
    $form_valid = False;
    $sun_feedback = '';
  }

  if (empty($water) || !is_numeric($water)) {
    $form_valid = FALSE;
    $water_feedback = '';
  }

  if (!in_array($hum, array('Low', 'Normal', 'High'))) {
    $form_valid = False;
    $hum_feedback = '';
  }

  if (empty($min) || !is_numeric($min)) {
    $form_valid = FALSE;
    $min_feedback = '';
  }

  if (empty($max) || !is_numeric($max)) {
    $form_valid = FALSE;
    $max_feedback = '';
  } elseif (!empty($min) and $max < $min) {
    $form_valid = False;
    $max_less_than_min = True;
  }

  if (!in_array($safe, array('Yes', 'No'))) {
    $form_valid = False;
    $safe_feedback = '';
  }

  if (empty($extra)) {
    $extra = NULL;
  }

  if ($form_valid) {
    $result = exec_sql_query(
      $db,
      "INSERT INTO houseplants (scientific_name, common_name, sunlight, water_frequency, humidity, min_temp, max_temp, pet_baby_safety, extra) VALUES (:sci_name, :c_name, :sun, :water, :hum, :mint, :maxt, :pbsafe, :extra);",
      array(
        ':sci_name' => $sci_name, // tainted
        ':c_name' => $name, // tainted
        ':sun' => $sun,
        ':water' => $water, //tainted
        ':hum' => $hum,
        ':mint' => $min, //tainted
        ':maxt' => $max, //tainted
        ':pbsafe' => $safe,
        ':extra' => $extra, //tainted
      )
    );

    // insert record to database
    if ($result) {
      $record_inserted = True;
    } else {
      $record_insert_failed = True;
    }
  } else {
    // form is invalid, set sticky values
    $sticky_sci_name = $sci_name;
    $sticky_name = $name;
    $sticky_low_indirect = ($sun == 'Low Indirect' ? 'selected' : '');
    $sticky_med_indirect = ($sun == 'Medium Indirect' ? 'selected' : '');
    $sticky_high_indirect = ($sun == 'High Indirect' ? 'selected' : '');
    $sticky_direct = ($sun == 'Direct' ? 'selected' : '');
    $sticky_water = $water;
    $sticky_low_hum = ($hum == 'Low' ? 'selected' : '');
    $sticky_nor_hum = ($hum == 'Normal' ? 'selected' : '');
    $sticky_high_hum = ($hum == 'High' ? 'selected' : '');
    $sticky_min = $min;
    $sticky_max = $max;
    $sticky_safe_yes = ($safe == 'Yes' ? 'selected' : '');
    $sticky_safe_no = ($safe == 'No' ? 'selected' : '');
    $sticky_extra = $extra;
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>Plants & Paradise</title>

  <link rel="stylesheet" type="text/css" href="public/styles/site.css" media="all" />
</head>

<body>

  <?php include("includes/header.php"); ?>

  <div class="main">
    <div class="summary">
      Welcome
    </div>
    <div class="intro">
      Plants can create a beautiful and lively atmosphere in your home, but picking the right plants and nurturing them can be difficult. Here at Plants & Paradise, we have created a comprehensive database with basic growing information on many plants to help you get started on this journey!
    </div>

    <hr class="divider" />

    <div class="filters-title">
      Filters
    </div>

    <div class="filter-section">
      <div class="filter-card">
        <div class="filter-name">Sunlight</div>
        <form class="filter" action="/" method="get" novalidate>
          <label>
            <input type="checkbox" name="lowsun" value="1" required <?php echo $sticky_lowsun; ?> /> Low Indirect
          </label>
          <label>
            <input type="checkbox" name="mediumsun" value="1" required <?php echo $sticky_mediumsun; ?> /> Medium Indirect
          </label>
          <label>
            <input type="checkbox" name="highsun" value="1" required <?php echo $sticky_highsun; ?> /> High Indirect
          </label>
          <label>
            <input type="checkbox" name="directsun" value="1" required <?php echo $sticky_directsun; ?> /> Direct
          </label>
          <button class="filter-button" type="submit">Apply Filter</button>

          <input type="hidden" name="query" value="<?php echo htmlspecialchars($search_terms); ?>" />
          <input type="hidden" name="lowhum" value="<?php echo $lowhum; ?>" />
          <input type="hidden" name="normalhum" value="<?php echo $normalhum; ?>" />
          <input type="hidden" name="highhum" value="<?php echo $highhum; ?>" />
          <input type="hidden" name="yes" value="<?php echo $yes_safety; ?>" />
          <input type="hidden" name="no" value="<?php echo $no_safety; ?>" />
        </form>
      </div>
      <div class="filter-card">
        <div class="filter-name">Humidity</div>
        <form class="filter" action="/" method="get" novalidate>
          <label>
            <input type="checkbox" name="lowhum" value="1" required <?php echo $sticky_lowhum; ?> /> Low
          </label>
          <label>
            <input type="checkbox" name="normalhum" value="1" required <?php echo $sticky_normalhum; ?> /> Normal
          </label>
          <label>
            <input type="checkbox" name="highhum" value="1" required <?php echo $sticky_highhum; ?> /> High
          </label>
          <button class="filter-button" type="submit">Apply Filter</button>

          <input type="hidden" name="query" value="<?php echo htmlspecialchars($search_terms); ?>" />
          <input type="hidden" name="lowsun" value="<?php echo $lowsun; ?>" />
          <input type="hidden" name="mediumsun" value="<?php echo $mediumsun; ?>" />
          <input type="hidden" name="highsun" value="<?php echo $highsun; ?>" />
          <input type="hidden" name="directsun" value="<?php echo $directsun; ?>" />
          <input type="hidden" name="yes" value="<?php echo $yes_safety; ?>" />
          <input type="hidden" name="no" value="<?php echo $no_safety; ?>" />
        </form>
      </div>
      <div class="filter-card">
        <div class="filter-name">Safety</div>
        <form class="filter" action="/" method="get" novalidate>
          <label>
            <input type="checkbox" name="yes" value="1" required <?php echo $sticky_yes_safety; ?> /> Yes
          </label>
          <label>
            <input type="checkbox" name="no" value="1" required <?php echo $sticky_no_safety; ?> /> No
          </label>
          <button class="filter-button" type="submit">Apply Filter</button>

          <input type="hidden" name="query" value="<?php echo htmlspecialchars($search_terms); ?>" />
          <input type="hidden" name="lowsun" value="<?php echo $lowsun; ?>" />
          <input type="hidden" name="mediumsun" value="<?php echo $mediumsun; ?>" />
          <input type="hidden" name="highsun" value="<?php echo $highsun; ?>" />
          <input type="hidden" name="directsun" value="<?php echo $directsun; ?>" />
          <input type="hidden" name="lowhum" value="<?php echo $lowhum; ?>" />
          <input type="hidden" name="normalhum" value="<?php echo $normalhum; ?>" />
          <input type="hidden" name="highhum" value="<?php echo $highhum; ?>" />
        </form>
      </div>
    </div>

    <hr class="divider" />

    <div class="search-section">
      <form class="search-bar" action="/" method="get" novalidate>
        <label for="search">Search: </label>
        <input id="search" type="text" name="query" required value="<?php echo htmlspecialchars($sticky_search); ?>" placeholder="Scientific or Common Name" />
        <button type="submit">Enter</button>

        <input type="hidden" name="lowsun" value="<?php echo $lowsun; ?>" />
        <input type="hidden" name="mediumsun" value="<?php echo $mediumsun; ?>" />
        <input type="hidden" name="highsun" value="<?php echo $highsun; ?>" />
        <input type="hidden" name="directsun" value="<?php echo $directsun; ?>" />
        <input type="hidden" name="lowhum" value="<?php echo $lowhum; ?>" />
        <input type="hidden" name="normalhum" value="<?php echo $normalhum; ?>" />
        <input type="hidden" name="highhum" value="<?php echo $highhum; ?>" />
        <input type="hidden" name="yes" value="<?php echo $yes_safety; ?>" />
        <input type="hidden" name="no" value="<?php echo $no_safety; ?>" />
      </form>
    </div>

    <div class="database">
      <table class="center">
        <tr>
          <th class="sci-name">
            <a class="table-header" href="<?php echo $sort_url . '&sort=sciname&order=' . $order_next_url['sciname']; ?>" aria-label="Sort by Scientific Name">
              Scientific Name
              <!-- Source: https://static.thenounproject.com/png/798752-200.png-->
              <img class="sort-both <?php echo $css_sciname_both; ?>" src="/public/images/sort_icon_both.png" alt="Up and down arrows for sorting" />
              <img class="sort-both <?php echo $css_sciname_up; ?>" src="/public/images/sort_icon_up.png" alt="Up arrow for sorting" />
              <img class="sort-both <?php echo $css_sciname_down; ?>" src="/public/images/sort_icon_down.png" alt="Down arrow for sorting" />
            </a>
          </th>
          <th class="name">
            <a class="table-header" href="<?php echo $sort_url . '&sort=name&order=' . $order_next_url['name']; ?>" aria-label="Sort by Common Name">
              Common Name
              <!-- Source: https://static.thenounproject.com/png/798752-200.png-->
              <img class="sort-both <?php echo $css_name_both; ?>" src="/public/images/sort_icon_both.png" alt="Up and down arrows for sorting" />
              <img class="sort-both <?php echo $css_name_up; ?>" src="/public/images/sort_icon_up.png" alt="Up arrow for sorting" />
              <img class="sort-both <?php echo $css_name_down; ?>" src="/public/images/sort_icon_down.png" alt="Down arrow for sorting" />
            </a>
          </th>
          <th class="sun">Sunlight</th>
          <th class="water">
            <a class="table-header" href="<?php echo $sort_url . '&sort=water&order=' . $order_next_url['water']; ?>" aria-label="Sort by Water Frequency">
              Water Frequency
              <!-- Source: https://static.thenounproject.com/png/798752-200.png-->
              <img class="sort-both <?php echo $css_water_both; ?>" src="/public/images/sort_icon_both.png" alt="Up and down arrows for sorting" />
              <img class="sort-both <?php echo $css_water_up; ?>" src="/public/images/sort_icon_up.png" alt="Up arrow for sorting" />
              <img class="sort-both <?php echo $css_water_down; ?>" src="/public/images/sort_icon_down.png" alt="Down arrow for sorting" />
            </a>
          </th>
          <th class="hum">Humidity</th>
          <th class="min">
            <a class="table-header" href="<?php echo $sort_url . '&sort=mintemp&order=' . $order_next_url['mintemp']; ?>" aria-label="Sort by Minimum Temperature">
              Minimum Temperature
              <!-- Source: https://static.thenounproject.com/png/798752-200.png-->
              <img class="sort-both <?php echo $css_mintemp_both; ?>" src="/public/images/sort_icon_both.png" alt="Up and down arrows for sorting" />
              <img class="sort-both <?php echo $css_mintemp_up; ?>" src="/public/images/sort_icon_up.png" alt="Up arrow for sorting" />
              <img class="sort-both <?php echo $css_mintemp_down; ?>" src="/public/images/sort_icon_down.png" alt="Down arrow for sorting" />
            </a>
          </th>
          <th class="max">
            <a class="table-header" href="<?php echo $sort_url . '&sort=maxtemp&order=' . $order_next_url['maxtemp']; ?>" aria-label="Sort by Maximum Temperature">
              Maximum Temperature
              <!-- Source: https://static.thenounproject.com/png/798752-200.png-->
              <img class="sort-both <?php echo $css_maxtemp_both; ?>" src="/public/images/sort_icon_both.png" alt="Up and down arrows for sorting" />
              <img class="sort-both <?php echo $css_maxtemp_up; ?>" src="/public/images/sort_icon_up.png" alt="Up arrow for sorting" />
              <img class="sort-both <?php echo $css_maxtemp_down; ?>" src="/public/images/sort_icon_down.png" alt="Down arrow for sorting" />
            </a>
          </th>
          <th class="safe">Pet and Baby Safe?</th>
          <th class="extra">Extra Resources</th>
        </tr>

        <!-- display records -->
        <?php
        $records = exec_sql_query(
          $db,
          $query,
          $select_params
        )->fetchAll();

        if (count($records) > 0) {
          foreach ($records as $record) { ?>
            <tr>
              <td><?php echo htmlspecialchars($record['scientific_name']); ?></td>
              <td><?php echo htmlspecialchars($record['common_name']); ?></td>
              <td><?php echo htmlspecialchars($record['sunlight']); ?></td>
              <td class="water-col"><?php echo htmlspecialchars($record['water_frequency']) . " days"; ?></td>
              <td><?php echo htmlspecialchars($record['humidity']); ?></td>
              <td><?php echo htmlspecialchars($record['min_temp']) . " °F"; ?></td>
              <td><?php echo htmlspecialchars($record['max_temp']) . " °F"; ?></td>
              <td><?php echo htmlspecialchars($record['pet_baby_safety']); ?></td>
              <td><?php echo htmlspecialchars($record['extra']); ?></td>
            </tr>
          <?php } ?>
        <?php } else { ?>
          <tr>
            <td></td>
            <td></td>
            <td></td>
            <td>No records found</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
        <?php } ?>
      </table>
    </div>

    <div class="paragraph">
      If you are a veteran plant-owner and would like to contribute to our database, please fill out the form below with accurate information.
    </div>

    <!-- if record inserted -->
    <?php if ($record_inserted) { ?>
      <div class="confirmation-card">
        <div class="confirmation">
          Success! &quot;<?php echo htmlspecialchars($sci_name); ?>&quot; was added to the database above
        </div>
        <a class="success" href="/">Return to Form</a>
      </div>
    <?php } ?>

    <!-- if record not inserted -->
    <?php if (!$record_inserted) { ?>
      <div class="form-card">
        <div class="form-container">
          <div class="records">
            Add new records
          </div>

          <!-- if record insertion failed -->
          <?php if ($record_insert_failed) { ?>
            <p class="feedback">Failed to add &quot;<?php echo htmlspecialchars($sci_name); ?>&quot; to the database.</p>
          <?php } ?>

          <div class="form">
            <form method="post" action="/" novalidate>

              <div class="label-group">
                <label for="sci-name">Scientific Name</label>
                <input type="text" id="sci-name" name="sci-name" class="input" value="<?php echo htmlspecialchars($sticky_sci_name); ?>" required>
                <p class="feedback <?php echo $sci_name_feedback; ?>">Please enter a valid scientific name of the plant</p>
                <?php if ($sci_name_not_unique) { ?>
                  <p class="feedback">&quot;<?php echo htmlspecialchars($sci_name); ?>&quot; already exists in the database Please enter a different plant</p>
                <?php } ?>
              </div>

              <div class="label-group">
                <label for="name">Common Name</label>
                <input type="text" id="name" name="name" class="input" value="<?php echo htmlspecialchars($sticky_name); ?>" required>
                <p class="feedback <?php echo $name_feedback; ?>">Please enter valid common name of the plant</p>
              </div>

              <div class="label-group">
                <label for="sun">Sunlight</label>
                <select id="sun" name="sun" class="input clickable">
                  <option label=" "></option>
                  <option value='Low Indirect' <?php echo htmlspecialchars($sticky_low_indirect); ?>>Low Indirect</option>
                  <option value='Medium Indirect' <?php echo htmlspecialchars($sticky_med_indirect); ?>>Medium Indirect</option>
                  <option value='High Indirect' <?php echo htmlspecialchars($sticky_high_indirect); ?>>High Indirect</option>
                  <option value='Direct' <?php echo htmlspecialchars($sticky_direct); ?>>Direct</option>
                </select>
                <p class="feedback <?php echo $sun_feedback; ?>">Please choose the optimal sunlight level</p>
              </div>

              <div class="label-group">
                <label for="water">Water Frequency (days)</label>
                <input type="text" id="water" name="water" class="input" value="<?php echo htmlspecialchars($sticky_water); ?>" required>
                <p class="feedback <?php echo $water_feedback; ?>">Please a valid number for watering frequency in days</p>
              </div>

              <div class="label-group">
                <label for="hum">Humidity</label>
                <select id="hum" name="hum" class="input clickable">
                  <option label=" "></option>
                  <option value='Low' <?php echo htmlspecialchars($sticky_low_hum); ?>>Low</option>
                  <option value='Normal' <?php echo htmlspecialchars($sticky_nor_hum); ?>>Normal</option>
                  <option value='High' <?php echo htmlspecialchars($sticky_high_hum); ?>>High</option>
                </select>
                <p class="feedback <?php echo $hum_feedback; ?>">Please choose the optimal humidity level</p>
              </div>

              <div class="label-group">
                <label for="min">Minimum Temperature (°F)</label>
                <input type="text" id="min" name="min" class="input" value="<?php echo htmlspecialchars($sticky_min); ?>" required>
                <p class="feedback <?php echo $min_feedback; ?>">Please enter a valid number for minimum temperature</p>
              </div>

              <div class="label-group">
                <label for="max">Maximum Temperature (°F)</label>
                <input type="text" id="max" name="max" class="input" value="<?php echo htmlspecialchars($sticky_max); ?>" required>
                <p class="feedback <?php echo $max_feedback; ?>">Please enter a valid number for maximum temperature</p>
                <?php if ($max_less_than_min) { ?>
                  <p class="feedback">Maximum temperature must be less than the minimum temperature</p>
                <?php } ?>
              </div>

              <div class="label-group">
                <label for="safe">Pet and Baby Safe?</label>
                <select id="safe" name="safe" class="input clickable">
                  <option label=" "></option>
                  <option value='Yes' <?php echo htmlspecialchars($sticky_safe_yes); ?>>Yes</option>
                  <option value='No' <?php echo htmlspecialchars($sticky_safe_no); ?>>No</option>
                </select>
                <p class="feedback <?php echo $safe_feedback; ?>">Please choose the plant safety</p>
              </div>

              <div class="label-group">
                <label for="extra">Extra Resources (Optional)</label>
                <input type="text" id="extra" name="extra" class="input" value="<?php echo htmlspecialchars($sticky_extra); ?>">
              </div>

              <input class="submit-button clickable" type="submit" value="Submit" name="submit">
            </form>
          </div>
        </div>
        <div class="image-container">
          <!-- Source: https://i.pinimg.com/originals/72/dc/c8/72dcc8b8ee5c9cdcdf4b831522498399.png-->
          <img class="image" src="../public/images/plants_form.png" alt="Drawing of plants" />
        </div>
      </div>
    <?php } ?>

  </div>

  <?php include("includes/footer.php"); ?>

</body>

</html>
