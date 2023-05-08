<?php
include("includes/init.php");
$catalog_page = 'here';

$art_id = (int)trim($_GET['id']);
$url = "/catalog/art-page?" . http_build_query(array('id' => $art_id));

$edit_mode = false;
$edit_authorization = false;

if (isset($_GET['edit'])) {
    $edit_mode = true;
    $art_id = (int)trim($_GET['edit']);
}

// find the document record
if ($art_id) {
    $records = exec_sql_query(
        $db,
        "SELECT * FROM items WHERE id = :id;",
        array(':id' => $art_id)
    )->fetchAll();
    if (count($records) > 0) {
        $artwork = $records[0];
    } else {
        $artwork = NULL;
    }
}

if ($artwork) {
    $tags = exec_sql_query(
        $db,
        "SELECT * FROM tags LEFT OUTER JOIN item_tags ON item_tags.tag_id=tags.id LEFT OUTER JOIN items ON item_tags.item_id=items.id WHERE items.id = :id;",
        array(':id' => $artwork['id'])
    )->fetchAll();

    if (is_user_logged_in()) {
        $edit_authorization = true;

        // FORM VALIDATION -------------------------------------------------------
        // additional validation constraints
        $continent_unique = true;
        $country_unique = true;
        $medium_unique = true;
        $museum_unique = true;
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
        // form sticky values
        $sticky_title = $artwork['artwork_title'];
        $sticky_name = $artwork['artist_name'];
        $sticky_year = $artwork['creation_year'];
        $sticky_about = $artwork['about'];
        $sticky_source = $artwork['source'];
        $sticky_continent = '';
        $sticky_country = '';
        $sticky_medium = '';
        $sticky_museum = '';
        $sticky_checkboxes = array_fill(0, sizeof($tags), '');
        // feedback
        $title_feedback = 'hidden';
        $name_feedback = 'hidden';
        $year_feedback = 'hidden';
        $source_feedback = 'hidden';
        $continent_unique = 'hidden';
        $country_unique = 'hidden';
        $medium_unique = 'hidden';
        $museum_unique = 'hidden';
        // form display
        $form_valid = true;
        // deletion
        $deletion = false;

        // when user presses save
        if (isset($_POST['save'])) {
            $title = trim($_POST['title']); // untrusted
            $name = ucwords(trim($_POST['name'])); // untrusted
            $year = trim($_POST['year']); // untrusted
            $about = trim($_POST['about']); // untrusted
            $continent = trim($_POST['continent']); // untrusted
            $country = trim($_POST['country']); // untrusted
            $medium = trim($_POST['medium']); // untrusted
            $museum = trim($_POST['museum']); // untrusted
            $source = trim($_POST['source']); // untrusted

            if (empty($title)) {
                $form_valid = FALSE;
                $title_feedback = '';
            }

            if (empty($name)) {
                $form_valid = FALSE;
                $name_feedback = '';
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

            if (empty($continent)) {
                $continent = NULL;
            } else {
                $continents_array = explode(",", $continent); // tainted
                $index = 0;
                foreach ($continents_array as $continent_value) {
                    $continent_value = ucwords(strtolower(trim($continent_value)));
                    $continents = exec_sql_query(
                        $db,
                        "SELECT * FROM tags LEFT OUTER JOIN item_tags ON item_tags.tag_id=tags.id LEFT OUTER JOIN items ON item_tags.item_id=items.id WHERE (items.id = :id) AND (tags.tag = :continent);",
                        array(
                            ':id' => $artwork['id'],
                            ':continent' => $continent_value
                        )
                    )->fetchAll();
                    if (count($continents) > 0) {
                        $continent_unique = '';
                        $form_valid = false;
                        break;
                    } else {
                        $continents_array[$index] = $continent_value;
                        $index++;
                    }
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
                    $countries = exec_sql_query(
                        $db,
                        "SELECT * FROM tags LEFT OUTER JOIN item_tags ON item_tags.tag_id=tags.id LEFT OUTER JOIN items ON item_tags.item_id=items.id WHERE (items.id = :id) AND (tags.tag = :country);",
                        array(
                            ':id' => $artwork['id'],
                            ':country' => $country_value
                        )
                    )->fetchAll();
                    if (count($countries) > 0) {
                        $country_unique = '';
                        $form_valid = false;
                        break;
                    } else {
                        $countries_array[$index] = $country_value;
                        $index++;
                    }
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
                    $mediums = exec_sql_query(
                        $db,
                        "SELECT * FROM tags LEFT OUTER JOIN item_tags ON item_tags.tag_id=tags.id LEFT OUTER JOIN items ON item_tags.item_id=items.id WHERE (items.id = :id) AND (tags.tag = :medium);",
                        array(
                            ':id' => $artwork['id'],
                            ':medium' => $medium_value
                        )
                    )->fetchAll();
                    if (count($mediums) > 0) {
                        $medium_unique = '';
                        $form_valid = false;
                        break;
                    } else {
                        $mediums_array[$index] = $medium_value;
                        $index++;
                    }
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
                    $museums = exec_sql_query(
                        $db,
                        "SELECT * FROM tags LEFT OUTER JOIN item_tags ON item_tags.tag_id=tags.id LEFT OUTER JOIN items ON item_tags.item_id=items.id WHERE (items.id = :id) AND (tags.tag = :museum);",
                        array(
                            ':id' => $artwork['id'],
                            ':museum' => $museum_value
                        )
                    )->fetchAll();
                    if (count($museums) > 0) {
                        $museum_unique = '';
                        $form_valid = false;
                        break;
                    } else {
                        $museums_array[$index] = $museum_value;
                        $index++;
                    }
                }
                $museums_array = array_unique($museums_array);
            }

            if ($form_valid) {
                exec_sql_query(
                    $db,
                    "UPDATE items SET artwork_title = :title, artist_name = :a_name, creation_year = :c_year, about = :about, source = :source WHERE (id = :id);",
                    array(
                        ':id' => $art_id,
                        ':title' => $title,
                        ':a_name' => $name,
                        ':c_year' => $year,
                        ':about' => $about,
                        ':source' => $source
                    )
                );

                // get updated document
                $records = exec_sql_query(
                    $db,
                    "SELECT * FROM items WHERE id = :id;",
                    array(':id' => $art_id)
                )->fetchAll();
                $artwork = $records[0];

                // Deleting tags
                foreach ($tags as $index => $tag) {
                    $tag_name = str_replace(' ', '-', strtolower($tag['tag']));
                    $should_delete = (bool)$_POST[$tag_name]; // untrusted
                    if ($should_delete) {
                        exec_sql_query(
                            $db,
                            "DELETE FROM item_tags WHERE (item_id=:item_id) AND (tag_id=:tag_id);",
                            array(
                                ':item_id' => $artwork['id'],
                                ':tag_id' => $tag['tag_id']
                            )
                        );
                    }
                }

                // Adding tags
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

                $tags = exec_sql_query(
                    $db,
                    "SELECT * FROM tags LEFT OUTER JOIN item_tags ON item_tags.tag_id=tags.id LEFT OUTER JOIN items ON item_tags.item_id=items.id WHERE items.id = :id;",
                    array(':id' => $artwork['id'])
                )->fetchAll();

                $edit_mode = false;
            } else {
                $edit_mode = true;
                $sticky_title = $title;
                $sticky_name = $name;
                $sticky_year = $year;
                $sticky_about = $about;
                $sticky_source = $source;
                $sticky_continent = $continent;
                $sticky_country = $country;
                $sticky_medium = $medium;
                $sticky_museum = $museum;
                foreach ($tags as $index => $tag) {
                    $tag_name = str_replace(' ', '-', strtolower($tag['tag']));
                    $should_check = (bool)$_POST[$tag_name]; // untrusted
                    $sticky_checkboxes[$index] = ($should_check ? 'checked' : '');
                }
            }
        }
        // when user presses delete
        if (isset($_POST['delete'])) {
            $edit_mode = true;
            $deletion = true;
        }
        if (isset($_POST['yes'])) {
            $edit_mode = false;
            unlink("public/uploads/items/" . $artwork['id'] . "." . $artwork['file_ext']);
            exec_sql_query(
                $db,
                "DELETE FROM item_tags WHERE (item_id=:item_id);",
                array(
                    ':item_id' => $artwork['id'],
                )
            );
            exec_sql_query(
                $db,
                "DELETE FROM items WHERE (id=:id);",
                array(
                    ':id' => $artwork['id'],
                )
            );
            $deletion_success = true;
        }
    }

    $url = "/catalog/art-page?" . http_build_query(array('id' => $artwork['id']));
    $edit_url = "/catalog/art-page?" . http_build_query(array('edit' => $artwork['id']));
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>ENIGMA</title>

    <link rel="stylesheet" type="text/css" href="/public/styles/site.css" media="all" />
    <link rel="stylesheet" type="text/css" href="/public/styles/art_page.css" media="all" />
</head>

<body class="art-page">
    <?php include("includes/header.php"); ?>

    <?php if ($artwork) { ?>
        <!-- when user is authorized and editing -->
        <?php if ($edit_mode && $edit_authorization) { ?>
            <!-- when delete is not pressed -->
            <?php if (!$deletion) { ?>
                <form class="edit" action="<?php echo $url; ?>" method="post" novalidate>

                    <div class="image-title">
                        <p class="feedback <?php echo $title_feedback; ?>">Invalid title</p>
                        <label class="title-label">
                            Title:
                            <input type="text" class="input thirty-two" name="title" value="<?php echo htmlspecialchars($sticky_title); ?>" required />
                        </label>
                    </div>
                    <div class="image-container">
                        <img class="image" src="/public/uploads/items/<?php echo $artwork['id'] . '.' . $artwork['file_ext']; ?>" alt="<?php echo htmlspecialchars($artwork['artwork_title']); ?>" />
                    </div>
                    <hr />
                    <div class="information-container">
                        <div>
                            <div class="information">
                                <p class="feedback <?php echo $name_feedback; ?>">Invalid artist name</p>
                                <label class="input-label">
                                    Artist:
                                </label>
                                <input type="text" class="input sixteen" name="name" value="<?php echo htmlspecialchars($sticky_name); ?>" required />
                            </div>
                            <div class="information">
                                <p class="feedback <?php echo $year_feedback; ?>">Invalid number for year</p>
                                <label class="input-label">
                                    Year:
                                </label>
                                <input type="text" class="input sixteen" name="year" value="<?php echo htmlspecialchars($sticky_year); ?>" required />
                            </div>
                            <div class="information">
                                <label class="input-label">
                                    About:
                                </label>
                                <textarea name="about" class="input sixteen" required><?php echo htmlspecialchars($sticky_about); ?></textarea>
                            </div>
                            <div class="information">
                                <p class="feedback <?php echo $source_feedback; ?>">Missing image source</p>
                                <label class="input-label">
                                    Source:
                                </label>
                                <input type="text" class="input sixteen" name="source" value="<?php echo htmlspecialchars($sticky_source); ?>" required />
                            </div>
                        </div>
                        <div>
                            <div class="information">
                                Tags—
                            </div>
                            <div class="information split">
                                <div>
                                    Remove
                                    <div>
                                        <?php foreach ($tags as $index => $tag) {
                                            $tag_name = str_replace(' ', '-', strtolower($tag['tag'])); ?>
                                            <label>
                                                <input type="checkbox" name="<?php echo htmlspecialchars($tag_name); ?>" value="1" <?php echo $sticky_checkboxes[$index]; ?> />
                                                <?php echo htmlspecialchars($tag['tag']); ?>
                                            </label>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div>
                                    Add
                                    <div class="label-group">
                                        <p class="feedback <?php echo $continent_unique; ?>">&quot;<?php echo htmlspecialchars($continent_value); ?>&quot; is already tagged</p>
                                        <label for="continent">Continent:</label>
                                        <input type="text" id="continent" name="continent" class="input" value="<?php echo htmlspecialchars($sticky_continent); ?>">
                                    </div>
                                    <div class="label-group">
                                        <p class="feedback <?php echo $country_unique; ?>">&quot;<?php echo htmlspecialchars($country_value); ?>&quot; is already tagged</p>
                                        <label for="country">Country:</label>
                                        <input type="text" id="country" name="country" class="input" value="<?php echo htmlspecialchars($sticky_country); ?>">
                                    </div>
                                    <div class="label-group">
                                        <p class="feedback <?php echo $medium_unique; ?>">&quot;<?php echo htmlspecialchars($medium_value); ?>&quot; is already tagged</p>
                                        <label for="medium">Medium:</label>
                                        <input type="text" id="medium" name="medium" class="input" value="<?php echo htmlspecialchars($sticky_medium); ?>">
                                    </div>
                                    <div class="label-group">
                                        <p class="feedback <?php echo $museum_unique; ?>">&quot;<?php echo htmlspecialchars($museum_value); ?>&quot; is already tagged</p>
                                        <label for="museum">Museum:</label>
                                        <input type="text" id="museum" name="museum" class="input" value="<?php echo htmlspecialchars($sticky_museum); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="save-button">
                        <button type="submit" name="save" class="button">Save</button>
                        <button type="submit" name="delete" class="button">Delete</button>
                    </div>
                </form>
                <!-- else when user presses delete -->
            <?php } else { ?>
                <div class="border">
                    <div class="border2">
                        <!-- confirmation for deletion -->
                        <div class="description">Are you sure you want to delete &quot;<?php echo htmlspecialchars($artwork['artwork_title']); ?>&quot; by <?php echo htmlspecialchars($artwork['artist_name']); ?>?</div>
                        <form action="<?php echo $url; ?>" method="post" novalidate>
                            <div class="save-button">
                                <button type="submit" id="yes" name="yes" class="button">Yes</button>
                                <a href="<?php echo $edit_url; ?>" name="no" class="button">No</a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <?php if ($deletion_success) { ?>
                <div class="border">
                    <div class="border2">
                        <div class="description">SUCCESSFULLY DELETED</div>
                        <a class="gbutton" href="/catalog">Catalog →</a>
                    </div>
                </div>
            <?php } else { ?>
                <div class="image-title">
                    <div><?php echo htmlspecialchars($artwork['artwork_title']); ?></div>
                    <div>
                        <a class="return-button left" href="/catalog">Return</a>
                        <?php if ($edit_authorization) { ?>
                            <a class="edit-button right" href="<?php echo $edit_url; ?>">Edit</a>
                        <?php } ?>
                    </div>
                </div>
                <div class="image-container">
                    <img class="image" src="/public/uploads/items/<?php echo $artwork['id'] . '.' . $artwork['file_ext']; ?>" alt="<?php echo htmlspecialchars($artwork['artwork_title']); ?>" />
                </div>
                <hr />
                <div class="information-container">
                    <div>
                        <div class="information">
                            <?php echo htmlspecialchars($artwork['artist_name']); ?>
                        </div>
                        <div class="information">
                            <?php echo htmlspecialchars($artwork['creation_year']); ?>
                        </div>
                        <div class="information">
                            <?php echo htmlspecialchars($artwork['about']); ?>
                        </div>
                    </div>
                    <?php

                    $tags = exec_sql_query(
                        $db,
                        "SELECT * FROM tags LEFT OUTER JOIN item_tags ON item_tags.tag_id=tags.id LEFT OUTER JOIN items ON item_tags.item_id=items.id WHERE items.id = :id;",
                        array(':id' => $artwork['id'])
                    )->fetchAll();
                    ?>
                    <div class="tag-section">
                        <div class="information">
                            —Tags
                        </div>
                        <div class="information">
                            <?php
                            if (count($tags) > 0) {
                                foreach ($tags as $tag) {
                                    $tag_name = str_replace(' ', '-', strtolower($tag['tag'])); ?>
                                    <a class="tags" href="/catalog?<?php echo htmlspecialchars($tag_name); ?>=1&filter="><?php echo htmlspecialchars($tag['tag']) . " "; ?></a>
                                <?php }
                            } else { ?>
                                <div class="tags">None</div>
                            <?php } ?>
                        </div>
                        <div class="information">
                            <?php
                            if (filter_var($artwork['source'], FILTER_VALIDATE_URL)) { ?>
                                <cite>
                                    <a href="<?php echo htmlspecialchars($artwork['source']); ?>" class="information">Source</a>
                                </cite>
                            <?php  } else { ?>
                                <cite>
                                    <div class="information"><?php echo htmlspecialchars($artwork['source']); ?></div>
                                </cite>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>

    <?php } else { ?>
        <div class="border">
            <div class="border2">
                <div class="description">ARTWORK NOT FOUND</div>
                <div class="sub-text">Please go back to the catalog to find an existing art work</div>
                <a class="gbutton" href="/catalog">Catalog →</a>
            </div>
        </div>
    <?php } ?>
    <?php include("includes/footer.php"); ?>
</body>

</html>
