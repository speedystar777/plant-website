<?php
include("includes/init.php");
$add_works_page = 'here';

define("MAX_FILE_SIZE", 1000000);

if (is_user_logged_in()) {
  // FORM VALIDATION -------------------------------------------------------
  // additional validation constraints
  $artwork_not_unique = false;
  $record_inserted = false;
  $record_insert_failed = false;
  // form inputs
  $title = '';
  $name = '';
  $year = '';
  $about = '';
  $source = '';
  $continent = '';
  $country = '';
  $medium = '';
  $museum = '';
  $filename = NULL;
  $file_ext = NULL;
  // form sticky values
  $sticky_title = '';
  $sticky_name = '';
  $sticky_year = '';
  $sticky_about = '';
  $sticky_source = '';
  $sticky_continent = '';
  $sticky_country = '';
  $sticky_medium = '';
  $sticky_museum = '';
  // feedback
  $title_feedback = 'hidden';
  $name_feedback = 'hidden';
  $year_feedback = 'hidden';
  $source_feedback = 'hidden';
  $file_feedback = 'hidden';
  //form display
  $form_valid = true;

  // if form is submitted
  if (isset($_POST["submit"])) {

    $title = trim($_POST["title"]); //untrusted
    $name = trim($_POST["name"]); //untrusted
    $year = $_POST["year"]; // untrusted
    $about = trim($_POST["about"]); // untrusted
    $source = trim($_POST["source"]); // untrusted
    $continent = trim($_POST["continent"]); // untrusted
    $country = trim($_POST["country"]); // untrusted
    $medium = trim($_POST["medium"]); // untrusted
    $museum = trim($_POST["museum"]); // untrusted
    $upload = $_FILES['image-file'];

    if (empty($title)) {
      $form_valid = FALSE;
      $title_feedback = '';
    }

    if (empty($name)) {
      $form_valid = FALSE;
      $name_feedback = '';
    } else {
      $name = ucwords(strtolower($name)); // tainted

      $records = exec_sql_query(
        $db,
        "SELECT * FROM items WHERE (artwork_title = :title) AND (artist_name = :art_name);",
        array(
          ':title' => $title,
          ':art_name' => $name
        )
      )->fetchAll();
      if (count($records) > 0) {
        $form_valid = False;
        $artwork_not_unique = True;
      }
    }

    if (empty($year) || !is_numeric($year)) {
      $form_valid = FALSE;
      $year_feedback = '';
    }

    if (empty($source)) {
      $form_valid = FALSE;
      $source_feedback = '';
    }

    if (empty($about)) {
      $about = NULL;
    }

    if ($upload['error'] == UPLOAD_ERR_OK) {
      $filename = basename($upload['name']);
      $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
      if (!in_array($file_ext, array('png', 'jpg'))) {
        $form_valid = False;
        $file_feedback = '';
      }
    } else {
      $form_valid = False;
      $file_feedback = '';
    }

    if (empty($continent)) {
      $continent = NULL;
    } else {
      $continents_array = explode(",", $continent); // tainted
      $index = 0;
      foreach ($continents_array as $continent_value) {
        $continent_value = ucwords(strtolower(trim($continent_value)));
        $continents_array[$index] = $continent_value;
        $index++;
      }
      $continents_array = array_unique($continents_array);
    }

    if (empty($country)) {
      $country = NULL;
    } else {
      $countries_array = explode(",", $country); // tainted
      $index = 0;
      foreach ($countries_array as $country_value) {
        $country_value = ucwords(strtolower(trim($country_value)));
        $countries_array[$index] = $country_value;
        $index++;
      }
      $countries_array = array_unique($countries_array);
    }

    if (empty($medium)) {
      $medium = NULL;
    } else {
      $mediums_array = explode(",", $medium); // tainted
      $index = 0;
      foreach ($mediums_array as $medium_value) {
        $medium_value = ucwords(strtolower(trim($medium_value)));
        $mediums_array[$index] = $medium_value;
        $index++;
      }
      $mediums_array = array_unique($mediums_array);
    }

    if (empty($museum)) {
      $museum = NULL;
    } else {
      $museums_array = explode(",", $museum); // tainted
      $index = 0;
      foreach ($museums_array as $museum_value) {
        $museum_value = ucwords(strtolower(trim($museum_value)));
        $museums_array[$index] = $museum_value;
        $index++;
      }
      $museums_array = array_unique($museums_array);
    }


    if ($form_valid) {
      $db->beginTransaction();
      $result = exec_sql_query(
        $db,
        "INSERT INTO items (artwork_title, artist_name, creation_year, about, filename, file_ext, source) VALUES (:title, :art_name, :year, :about, :filename, :ext, :source);",
        array(
          ':title' => $title, // tainted
          ':art_name' => $name, // tainted
          ':year' => $year,
          ':about' => $about, //tainted
          ':filename' => $filename,
          ':ext' => $file_ext,
          ':source' => $source
        )
      );

      if ($result) {
        $record_id = $db->lastInsertId('id');
        $records = exec_sql_query(
          $db,
          "SELECT * FROM items WHERE id = :id;",
          array(
            ':id' => $record_id,
          )
        )->fetchAll();
        $artwork = $records[0];

        if (!is_null($continent)) {
          foreach ($continents_array as $continent_value) {
            $continents = exec_sql_query(
              $db,
              "SELECT * FROM tags WHERE tag = :continent;",
              array(
                ':continent' => $continent_value
              )
            )->fetchAll();
            if (count($continents) > 0) {
              exec_sql_query(
                $db,
                "INSERT INTO item_tags (item_id, tag_id) VALUES (:item_id, :tag_id);",
                array(
                  ':item_id' => $artwork['id'],
                  ':tag_id' => $continents[0]['id']
                )
              );
            } else {
              exec_sql_query(
                $db,
                "INSERT INTO tags (tag, category) VALUES (:tag, 'Continent');",
                array(
                  ':tag' => $continent_value
                )
              );
              $continents = exec_sql_query(
                $db,
                "SELECT * FROM tags WHERE tag = :continent;",
                array(
                  ':continent' => $continent_value
                )
              )->fetchAll();
              exec_sql_query(
                $db,
                "INSERT INTO item_tags (item_id, tag_id) VALUES (:item_id, :tag_id);",
                array(
                  ':item_id' => $artwork['id'],
                  ':tag_id' => $continents[0]['id']
                )
              );
            }
          }
        }
        if (!is_null($country)) {
          foreach ($countries_array as $country_value) {
            $countries = exec_sql_query(
              $db,
              "SELECT * FROM tags WHERE tag = :country;",
              array(
                ':country' => $country_value
              )
            )->fetchAll();
            if (count($countries) > 0) {
              exec_sql_query(
                $db,
                "INSERT INTO item_tags (item_id, tag_id) VALUES (:item_id, :tag_id);",
                array(
                  ':item_id' => $artwork['id'],
                  ':tag_id' => $countries[0]['id']
                )
              );
            } else {
              exec_sql_query(
                $db,
                "INSERT INTO tags (tag, category) VALUES (:tag, 'Country');",
                array(
                  ':tag' => $country_value
                )
              );
              $countries = exec_sql_query(
                $db,
                "SELECT * FROM tags WHERE tag = :country;",
                array(
                  ':country' => $country_value
                )
              )->fetchAll();
              exec_sql_query(
                $db,
                "INSERT INTO item_tags (item_id, tag_id) VALUES (:item_id, :tag_id);",
                array(
                  ':item_id' => $artwork['id'],
                  ':tag_id' => $countries[0]['id']
                )
              );
            }
          }
        }
        if (!is_null($medium)) {
          foreach ($mediums_array as $medium_value) {
            $mediums = exec_sql_query(
              $db,
              "SELECT * FROM tags WHERE tag = :medium;",
              array(
                ':medium' => $medium_value
              )
            )->fetchAll();
            if (count($mediums) > 0) {
              exec_sql_query(
                $db,
                "INSERT INTO item_tags (item_id, tag_id) VALUES (:item_id, :tag_id);",
                array(
                  ':item_id' => $artwork['id'],
                  ':tag_id' => $mediums[0]['id']
                )
              );
            } else {
              exec_sql_query(
                $db,
                "INSERT INTO tags (tag, category) VALUES (:tag, 'Medium');",
                array(
                  ':tag' => $medium_value
                )
              );
              $mediums = exec_sql_query(
                $db,
                "SELECT * FROM tags WHERE tag = :medium;",
                array(
                  ':medium' => $medium_value
                )
              )->fetchAll();
              exec_sql_query(
                $db,
                "INSERT INTO item_tags (item_id, tag_id) VALUES (:item_id, :tag_id);",
                array(
                  ':item_id' => $artwork['id'],
                  ':tag_id' => $mediums[0]['id']
                )
              );
            }
          }
        }
        if (!is_null($museum)) {
          foreach ($museums_array as $museum_value) {
            $museums = exec_sql_query(
              $db,
              "SELECT * FROM tags WHERE tag = :museum;",
              array(
                ':museum' => $museum_value
              )
            )->fetchAll();
            if (count($museums) > 0) {
              exec_sql_query(
                $db,
                "INSERT INTO item_tags (item_id, tag_id) VALUES (:item_id, :tag_id);",
                array(
                  ':item_id' => $artwork['id'],
                  ':tag_id' => $museums[0]['id']
                )
              );
            } else {
              exec_sql_query(
                $db,
                "INSERT INTO tags (tag, category) VALUES (:tag, 'Museum');",
                array(
                  ':tag' => $museum_value
                )
              );
              $museums = exec_sql_query(
                $db,
                "SELECT * FROM tags WHERE tag = :museum;",
                array(
                  ':museum' => $museum_value
                )
              )->fetchAll();
              exec_sql_query(
                $db,
                "INSERT INTO item_tags (item_id, tag_id) VALUES (:item_id, :tag_id);",
                array(
                  ':item_id' => $artwork['id'],
                  ':tag_id' => $museums[0]['id']
                )
              );
            }
          }
        }

        $id_filename = 'public/uploads/items/' . $record_id . '.' . $file_ext;
        move_uploaded_file($upload["tmp_name"], $id_filename);

        $record_inserted = true;
      } else {
        $record_insert_failed = true;
      }
      $db->commit();
    } else {
      // form is invalid, set sticky values
      $sticky_title = $title;
      $sticky_name = $name;
      $sticky_year = $year;
      $sticky_about = $about;
      $sticky_continent = $continent;
      $sticky_country = $country;
      $sticky_medium = $medium;
      $sticky_museum = $museum;
      $sticky_source = $source;
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>Enigma</title>

  <link rel="stylesheet" type="text/css" href="public/styles/site.css" media="all" />
  <link rel="stylesheet" type="text/css" href="public/styles/formpage.css" media="all" />
</head>

<body class="form-page">
  <?php include("includes/header.php"); ?>

  <?php if (!is_user_logged_in()) { ?>

    <div class="border">
      <div class="border2">
        <div class="description">NOT AUTHORIZED</div>
        <div class="sub-text">You must be logged in to add art works to the website</div>
        <a class="gbutton" href="/login">LOG IN →</a>
      </div>
    </div>

  <?php } else { ?>

    <?php if ($record_inserted) { ?>
      <div class="border">
        <div class="border2">
          <div class="description">SUCCESS!</div>
          <div class="sub-text">&quot;<?php echo htmlspecialchars($title); ?>&quot; has been added to the records</div>
          <a class="gbutton" href="/add-works">Return to Form</a>
        </div>
      </div>
    <?php } ?>

    <?php if (!$record_inserted) { ?>
      <div class="form-card">
        <div class="form-container">
          <div class="records">
            Add Art Works
          </div>

          <?php if ($record_insert_failed) { ?>
            <p class="feedback">Failed to add &quot;<?php echo htmlspecialchars($title); ?>&quot; to the database.</p>
          <?php } ?>

          <?php if ($artwork_not_unique) { ?>
            <p class="feedback">&quot;<?php echo htmlspecialchars($title); ?>&quot; by &quot;<?php echo htmlspecialchars($name); ?>&quot; already exists in the database. Please enter a different art work</p>
          <?php } ?>

          <div class="form">
            <form method="post" action="/add-works" enctype="multipart/form-data" novalidate>

              <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_FILE_SIZE; ?>" />

              <div class="label-group">
                <label for="title">Art Work Title</label>
                <input type="text" id="title" name="title" class="input" value="<?php echo htmlspecialchars($sticky_title); ?>" required>
                <p class="feedback <?php echo $title_feedback; ?>">Please enter a valid title for the art work</p>
              </div>

              <div class="label-group">
                <label for="name">Artist Name</label>
                <input type="text" id="name" name="name" class="input" value="<?php echo htmlspecialchars($sticky_name); ?>" required>
                <p class="feedback <?php echo $name_feedback; ?>">Please enter valid name for the artist</p>
              </div>

              <div class="label-group">
                <label for="year">Creation Year</label>
                <input type="text" id="year" name="year" class="input" value="<?php echo htmlspecialchars($sticky_year); ?>" required>
                <p class="feedback <?php echo $year_feedback; ?>">Please a valid number for the year the work was created</p>
              </div>

              <div class="label-group">
                <label for="about">About (Optional)</label>
                <textarea id="about" name="about" class="input" required><?php echo htmlspecialchars($sticky_about); ?></textarea>
              </div>

              <div class="label-group">
                <label for="image-file">Image File</label>
                <input type="file" id="image-file" name="image-file" class="upload" accept=".jpg, .png" required>
                <p class="feedback <?php echo $file_feedback; ?>">Please upload a valid png or jpeg image under 1 MB</p>
              </div>

              <div class="label-group">
                <label for="source">Source</label>
                <input type="text" id="source" name="source" class="input" value="<?php echo htmlspecialchars($sticky_source); ?>" required>
                <p class="feedback <?php echo $source_feedback; ?>">Please enter a valid source for the image</p>
              </div>

              <div class="label">—Tags—</div>
              <div class="label">[please separate by a comma]</div>

              <div class="label-group">
                <label for="continent">Continent</label>
                <input type="text" id="continent" name="continent" class="input" value="<?php echo htmlspecialchars($sticky_continent); ?>">
              </div>
              <div class="label-group">
                <label for="country">Country</label>
                <input type="text" id="country" name="country" class="input" value="<?php echo htmlspecialchars($sticky_country); ?>">
              </div>
              <div class="label-group">
                <label for="medium">Medium</label>
                <input type="text" id="medium" name="medium" class="input" value="<?php echo htmlspecialchars($sticky_medium); ?>">
              </div>
              <div class="label-group">
                <label for="museum">Museum</label>
                <input type="text" id="museum" name="museum" class="input" value="<?php echo htmlspecialchars($sticky_museum); ?>">
              </div>

              <input class="submit-button clickable" type="submit" value="Submit" name="submit">
            </form>
          </div>
        </div>
      </div>
    <?php } ?>

  <?php } ?>

  <?php include("includes/footer.php"); ?>
</body>

</html>
