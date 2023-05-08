-- Users
CREATE TABLE users (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
	username TEXT NOT NULL UNIQUE,
	password TEXT NOT NULL
);
-- Users Table Seed Data
INSERT INTO users (id, first_name, last_name, username, password) VALUES (1, 'Tim', 'Grant', 'tim', '$2y$10$QtCybkpkzh7x5VN11APHned4J8fu78.eFXlyAMmahuAaNcbwZ7FH.'); -- password: monkey


-- Sessions
CREATE TABLE sessions (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	user_id INTEGER NOT NULL,
	session TEXT NOT NULL UNIQUE,
    last_login TEXT NOT NULL,

  FOREIGN KEY(user_id) REFERENCES users(id)
);


-- Items Table
CREATE TABLE items (
    id	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    artwork_title TEXT NOT NULL,
    artist_name TEXT NOT NULL,
    creation_year INTEGER NOT NULL,
    about TEXT,
    filename TEXT NOT NULL,
    file_ext TEXT NOT NULL,
    source TEXT NOT NULL
);
-- Items Table Seed Data
INSERT INTO items (id, artwork_title, artist_name, creation_year, about, filename, file_ext, source) VALUES (1, 'Free and Leisure-10', 'Yue Minjun', 2003, NULL, "1", "jpg", "https://artsandculture.google.com/asset/free-and-leisure-10-yue-minjun/agHe2bazFRwvIA"); -- medium = Oil Painting; country = China; museum = Today Art Museum
INSERT INTO items (id, artwork_title, artist_name, creation_year, about, filename, file_ext, source) VALUES (2, 'A Subtlety', 'Kara Walker', 2014, 'About slavery and the sugar trade', "2", "jpg", "https://artsandculture.google.com/asset/a-subtlety/TAEOFCFww_WbDg?hl=en"); -- medium = Sugar; country = United States; museum = NULL
INSERT INTO items (id, artwork_title, artist_name, creation_year, about, filename, file_ext, source) VALUES (3, 'Exhaling Pearls', 'Joseph Havel', 1993, NULL, "3", "jpg", "https://artsandculture.google.com/asset/exhaling-pearls/cQGKURMuUtdZhg?hl=en"); -- medium = Patinated Bronze; country = United States; museum = The Museum of Fine Arts, Houston
INSERT INTO items (id, artwork_title, artist_name, creation_year, about, filename, file_ext, source) VALUES (4, 'Free South Africa', 'Keith Haring', 1993, NULL, "4", "jpg", "https://artsandculture.google.com/asset/free-south-africa/ygGNPqIX3l8fOQ?hl=en"); -- medium = Lithograph; country = United States; museum = The Museum of Fine Arts, Houston
INSERT INTO items (id, artwork_title, artist_name, creation_year, about, filename, file_ext, source) VALUES (5, 'Supper in Dresden', 'Georg Baselitz', 1983, NULL, "5", "jpg", "https://artsandculture.google.com/asset/supper-in-dresden/mwEiozAZ8GqcAg?hl=en"); -- medium = Oil Painting; country = Switzerland; museum = Kunsthaus Zürich
INSERT INTO items (id, artwork_title, artist_name, creation_year, about, filename, file_ext, source) VALUES (6, "Father's Whisper", 'Elmer Borlongan', 2006, NULL, "6", "jpg", "https://artsandculture.google.com/asset/father-s-whisper/0wGYtCII9jUAGw?hl=en"); -- medium = Oil Painting; country = Phillipines; museum = NULL
INSERT INTO items (id, artwork_title, artist_name, creation_year, about, filename, file_ext, source) VALUES (7, "Dos personajes atacados por perros", 'Rufino Tamayo', 1983, 'Title translation is "Two People Attacked by Dogs." The work is about humanity.', "7", "jpg", "https://artsandculture.google.com/asset/dos-personajes-atacados-por-perros/lgFpkU9F0eZ-pQ?hl=en"); -- medium = Mixograph and Paper; country = United States; museum = Museum of Latin American Art
INSERT INTO items (id, artwork_title, artist_name, creation_year, about, filename, file_ext, source) VALUES (8, "People at Mago Castle", 'Suh Yongsun', 2009, 'About the appearance of mythical Mago', "8", "jpg", "https://artsandculture.google.com/asset/people-at-mago-castle/6wH7boMj_VBRTg?hl=en"); -- medium = Acrylic, Steel and Wood; country = South Korea; museum = NULL
INSERT INTO items (id, artwork_title, artist_name, creation_year, about, filename, file_ext, source) VALUES (9, "Thinking of History at My Space", 'Chen YiFei', 1978, NULL, "9", "jpg", "https://artsandculture.google.com/asset/thinking-of-history-at-my-space/4wHJZ6r2X7NOFQ?hl=en"); -- medium = Oil Painting; country = China; museum = Long Museum West Bund
INSERT INTO items (id, artwork_title, artist_name, creation_year, about, filename, file_ext, source) VALUES (10, "Wirbelwerk", 'Olafur Eliasson', 2012, NULL, "10", "jpg", "https://artsandculture.google.com/asset/wirbelwerk/DgEDrFsO4kJoTA?hl=en"); -- medium = Steel and Glass; country = Germany; museum = Lenbachhaus
INSERT INTO items (id, artwork_title, artist_name, creation_year, about, filename, file_ext, source) VALUES (11, "The Mellow Pad", 'Stuart Davis', 1951, NULL, "11", "jpg", "https://artsandculture.google.com/asset/the-mellow-pad/iAH3dzm5LKGgow?hl=en"); -- medium = Oil Painting; country = United States; museum = Brooklyn Museum
INSERT INTO items (id, artwork_title, artist_name, creation_year, about, filename, file_ext, source) VALUES (12, "The Ceiling, China Collage series", 'Roberto Chabet', 1985, NULL, "12", "jpg", "https://artsandculture.google.com/asset/the-ceiling-china-collage-series/wAGg03T3ODR8nA?hl=en"); -- medium = Paper; country = Singapore; museum = National Gallery Singapore


-- Tags Table
-- Categories: Country, Contintent, Medium, Museum
CREATE TABLE tags (
    id	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    tag TEXT NOT NULL UNIQUE,
    category TEXT NOT NULL
);
-- Tags Table Seed Data
INSERT INTO tags (id, tag, category) VALUES (1, 'China', 'Country');
INSERT INTO tags (id, tag, category) VALUES (2, 'Oil Painting', 'Medium');
INSERT INTO tags (id, tag, category) VALUES (3, 'Today Art Museum', 'Museum');
INSERT INTO tags (id, tag, category) VALUES (4, 'United States', 'Country');
INSERT INTO tags (id, tag, category) VALUES (5, 'Sugar', 'Medium');
INSERT INTO tags (id, tag, category) VALUES (6, 'Patinated Bronze', 'Medium');
INSERT INTO tags (id, tag, category) VALUES (7, 'The Museum Of Fine Arts Houston', 'Museum');
INSERT INTO tags (id, tag, category) VALUES (8, 'Lithograph', 'Medium');
INSERT INTO tags (id, tag, category) VALUES (9, 'Switzerland', 'Country');
INSERT INTO tags (id, tag, category) VALUES (10, 'Kunsthaus Zurich', 'Museum');
INSERT INTO tags (id, tag, category) VALUES (11, 'Philippines', 'Country');
INSERT INTO tags (id, tag, category) VALUES (12, 'Mixograph', 'Medium');
INSERT INTO tags (id, tag, category) VALUES (13, 'Paper', 'Medium');
INSERT INTO tags (id, tag, category) VALUES (14, 'Museum Of Latin American Art', 'Museum');
INSERT INTO tags (id, tag, category) VALUES (15, 'Acrylic', 'Medium');
INSERT INTO tags (id, tag, category) VALUES (16, 'Steel', 'Medium');
INSERT INTO tags (id, tag, category) VALUES (17, 'Wood', 'Medium');
INSERT INTO tags (id, tag, category) VALUES (18, 'South Korea', 'Country');
INSERT INTO tags (id, tag, category) VALUES (19, 'Long Museum West Bund', 'Museum');
INSERT INTO tags (id, tag, category) VALUES (20, 'Lenbachhaus', 'Museum');
INSERT INTO tags (id, tag, category) VALUES (21, 'Germany', 'Country');
INSERT INTO tags (id, tag, category) VALUES (22, 'Glass', 'Medium');
INSERT INTO tags (id, tag, category) VALUES (23, 'Brooklyn Museum', 'Museum');
INSERT INTO tags (id, tag, category) VALUES (24, 'Singapore', 'Country');
INSERT INTO tags (id, tag, category) VALUES (25, 'National Gallery Singapore', 'Museum');
INSERT INTO tags (id, tag, category) VALUES (26, 'North America', 'Continent');
INSERT INTO tags (id, tag, category) VALUES (27, 'Europe', 'Continent');
INSERT INTO tags (id, tag, category) VALUES (28, 'Asia', 'Continent');


-- Item_Tags Table
CREATE TABLE item_tags (
    id	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    item_id INTEGER NOT NULL,
    tag_id INTEGER NOT NULL,

    FOREIGN KEY(item_id) REFERENCES items(id),
    FOREIGN KEY(tag_id) REFERENCES tags(id)
);
-- Item+Tags Table Seed Data
INSERT INTO item_tags (id, item_id, tag_id) VALUES (1, 1, 1);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (2, 1, 2);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (3, 1, 3);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (4, 1, 28);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (5, 2, 5);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (6, 2, 4);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (7, 2, 26);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (8, 3, 6);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (9, 3, 4);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (10, 3, 26);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (11, 3, 7);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (12, 4, 8);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (13, 4, 4);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (14, 4, 7);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (15, 4, 26);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (16, 5, 2);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (17, 5, 9);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (18, 5, 10);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (19, 5, 27);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (20, 6, 2);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (21, 6, 11);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (22, 6, 28);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (23, 7, 12);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (24, 7, 13);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (25, 7, 4);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (26, 7, 14);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (27, 7, 26);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (28, 8, 15);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (29, 8, 16);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (30, 8, 17);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (31, 8, 18);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (32, 8, 28);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (33, 9, 2);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (34, 9, 1);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (35, 9, 19);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (36, 9, 28);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (37, 10, 16);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (38, 10, 22);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (39, 10, 20);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (40, 10, 21);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (41, 10, 27);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (42, 11, 2);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (43, 11, 4);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (44, 11, 23);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (45, 11, 26);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (46, 12, 13);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (47, 12, 24);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (48, 12, 25);
INSERT INTO item_tags (id, item_id, tag_id) VALUES (49, 12, 28);
