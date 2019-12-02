-- TODO: Put ALL SQL in between `BEGIN TRANSACTION` and `COMMIT`
BEGIN TRANSACTION;

-------------------------------- TODO: create tables -----------------------------------

CREATE TABLE 'home_imgs' (
    'id'            INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    'file_ext'      TEXT,
    'alt_txt'       TEXT NOT NULL
);

CREATE TABLE 'about' (
    'id'            INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    'ext'           TEXT NOT NULL,
    'name'          TEXT NOT NULL,
    'blurb'         TEXT NOT NULL,
    'opening'       TEXT NOT NULL
);

CREATE TABLE 'photos' (
    'id'                INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    'ext'               TEXT NOT NULL,
    'name'              TEXT NOT NULL UNIQUE,
    'price'             TEXT NOT NULL,
    'description'       TEXT,
    'product_type_id'   INTEGER
);

CREATE TABLE 'tags' (
    'id'            INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    'low'           INTEGER NOT NULL,
    'high'          INTEGER NOT NULL,
    'price_range'   TEXT NOT NULL UNIQUE
);

CREATE TABLE 'product_types' (
    'id'            INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    'type'          TEXT NOT NULL,
    'type_low'      TEXT NOT NULL,
    'ext'           TEXT NOT NULL
);

CREATE TABLE 'photos_tags_types' (
    'id'            INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    'photo_id'      INTEGER NOT NULL,
    'tag_id'        INTEGER NOT NULL,
    'prod_type_id'  INTEGER NOT NULL
);

CREATE TABLE 'messages' (
    'id'            INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    'date'          TEXT NOT NULL,
    'time'          TEXT NOT NULL,
    'sender_first'  TEXT NOT NULL,
    'sender_last'   TEXT NOT NULL,
    'sender_email'  TEXT NOT NULL,
    'sender_phone'  TEXT,
    'subject'       TEXT NOT NULL,
    'message'       TEXT NOT NULL
);

CREATE TABLE 'users' (
    'id'            INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
    'name'          TEXT,
    'username'      TEXT NOT NULL UNIQUE,
    'salt'          TEXT NOT NULL UNIQUE,
    'password'      TEXT NOT NULL,
    'session'       TEXT UNIQUE
);


-------------------------------- TODO: CREATE PHOTO / MESSAGE SEED DATA   -----------------------------------

-- home page images
INSERT INTO 'home_imgs' (id, file_ext, alt_txt) VALUES (1, "jpg", "jewlery image");
INSERT INTO 'home_imgs' (id, file_ext, alt_txt) VALUES (2, "jpg", "jewlery image");
INSERT INTO 'home_imgs' (id, file_ext, alt_txt) VALUES (3, "jpg", "jewlery image");

-- photos seed data
INSERT INTO 'photos' (id, ext, name, price, description, product_type_id) VALUES (1, "jpg", "White Gem Earrings", "$35", "Perfect condition. Never been worn. Great accent for your outfit.", 1);
INSERT INTO 'photos' (id, ext, name, price, description, product_type_id) VALUES (2, "jpg", "Dragon Red Gem Bracelet", "$35", "Perfect condition. Great way to add a little something to your outfit.", 2);
INSERT INTO 'photos' (id, ext, name, price, description, product_type_id) VALUES (3, "jpg", "Simple Bracelet", "$35", "Perfect condition. Matches well with everything.", 2);
INSERT INTO 'photos' (id, ext, name, price, description, product_type_id) VALUES (4, "jpg", "Ivory Elegance Necklace", "$40", "Perfect condition. Great for grabbing everybody's attention at a party.", 3);
INSERT INTO 'photos' (id, ext, name, price, description, product_type_id) VALUES (5, "jpg", "Blue Party Bracelet", "$40", "Perfect condition. Never been worn Great for grabbing everybody's attention at a party..", 2);
INSERT INTO 'photos' (id, ext, name, price, description, product_type_id) VALUES (6, "jpg", "High Royalty Necklace", "$40", "Perfect condition. Never been worn. Great accessory that shows off your classiness.", 3);
INSERT INTO 'photos' (id, ext, name, price, description, product_type_id) VALUES (7, "jpg", "Red Show Bracelet", "$40", "The best bracelet for anyone who is looking for a taste of luxury.", 2);
INSERT INTO 'photos' (id, ext, name, price, description, product_type_id) VALUES (8, "jpg", "White Gem Glam Bracelet", "$5", "This bracelet proves that taste does not need to cost a lot.", 2);
INSERT INTO 'photos' (id, ext, name, price, description, product_type_id) VALUES (9, "jpg", "Rose Pooja Earrings", "$5", "Cheap earrings that actually look nice.", 1);
INSERT INTO 'photos' (id, ext, name, price, description, product_type_id) VALUES (10, "jpg", "Pink Twin Bracelet", "$5", "If pink is your favorite color, then this bracelet is the best for you.", 2);
INSERT INTO 'photos' (id, ext, name, price, description, product_type_id) VALUES (11, "jpg", "Red Delicate Necklace", "$5", "A cheap necklace at a great price.", 3);
INSERT INTO 'photos' (id, ext, name, price, description, product_type_id) VALUES (12, "jpg", "Pink Lotus Earrings", "$5", "The best lotus earrings on the market.", 1);
INSERT INTO 'photos' (id, ext, name, price, description, product_type_id) VALUES (13, "jpg", "Flower Party Necklace", "$5", "If you like flowers, this is the necklace for you.", 3);
INSERT INTO 'photos' (id, ext, name, price, description, product_type_id) VALUES (14, "jpg", "Green Eyed Bracelet", "$15", "If your favorite color is green, this is the best bracelet for you.", 2);
INSERT INTO 'photos' (id, ext, name, price, description, product_type_id) VALUES (15, "jpg", "Pretty Spring Necklace", "$15", "Best necklace on the market at this price point.", 3);
INSERT INTO 'photos' (id, ext, name, price, description, product_type_id) VALUES (16, "jpg", "Fancy White Bracelet", "$15", "Very fancy bracelet for this price.", 2);
INSERT INTO 'photos' (id, ext, name, price, description, product_type_id) VALUES (17, "jpg", "Clasp Bracelet", "$15", "A bracelet in perfect condition and has never been worn.", 2);
INSERT INTO 'photos' (id, ext, name, price, description, product_type_id) VALUES (18, "jpg", "Flower Pot Earrings", "$15", "Great earrings at a low price.", 1);
INSERT INTO 'photos' (id, ext, name, price, description, product_type_id) VALUES (19, "jpg", "Dancing Stars Earrings", "$90", "Perfect condition and matches well with everything you wear.", 1);
INSERT INTO 'photos' (id, ext, name, price, description, product_type_id) VALUES (20, "jpg", "Ribbon River Earrings", "$90", "Luxury earrings that match well with any clothing.", 1);
INSERT INTO 'photos' (id, ext, name, price, description, product_type_id) VALUES (21, "jpg", "White Gem Flash Bracelet", "$90", "Good condition earrings that are sure to sell.", 2);

-- NOTE: I made the price values text instead of ints to be consistent with our table

INSERT INTO 'tags' (id, low, high, price_range) VALUES (1, 0, 20, "$0-$20");
INSERT INTO 'tags' (id, low, high, price_range) VALUES (2, 20, 40, "$20-$40");
INSERT INTO 'tags' (id, low, high, price_range) VALUES (3, 40, 2147483647, "$40+");

INSERT INTO 'product_types' (id, type, type_low, ext) VALUES (1, "Earrings", "earrings", "jpg");
INSERT INTO 'product_types' (id, type, type_low, ext) VALUES (2, "Bracelets", "bracelets", "jpg");
INSERT INTO 'product_types' (id, type, type_low, ext) VALUES (3, "Necklaces", "necklaces", "jpg");

INSERT INTO 'photos_tags_types' (id, photo_id, prod_type_id, tag_id) VALUES (1, 1, 1, 2);
INSERT INTO 'photos_tags_types' (id, photo_id, prod_type_id, tag_id) VALUES (2, 2, 2, 2);
INSERT INTO 'photos_tags_types' (id, photo_id, prod_type_id, tag_id) VALUES (3, 3, 2, 2);
INSERT INTO 'photos_tags_types' (id, photo_id, prod_type_id, tag_id) VALUES (4, 4, 3, 3);
INSERT INTO 'photos_tags_types' (id, photo_id, prod_type_id, tag_id) VALUES (5, 5, 2, 3);
INSERT INTO 'photos_tags_types' (id, photo_id, prod_type_id, tag_id) VALUES (6, 6, 3, 3);
INSERT INTO 'photos_tags_types' (id, photo_id, prod_type_id, tag_id) VALUES (7, 7, 2, 3);
INSERT INTO 'photos_tags_types' (id, photo_id, prod_type_id, tag_id) VALUES (8, 8, 2, 1);
INSERT INTO 'photos_tags_types' (id, photo_id, prod_type_id, tag_id) VALUES (9, 9, 1, 1);
INSERT INTO 'photos_tags_types' (id, photo_id, prod_type_id, tag_id) VALUES (10, 10, 2, 1);
INSERT INTO 'photos_tags_types' (id, photo_id, prod_type_id, tag_id) VALUES (11, 11, 3, 1);
INSERT INTO 'photos_tags_types' (id, photo_id, prod_type_id, tag_id) VALUES (12, 12, 1, 1);
INSERT INTO 'photos_tags_types' (id, photo_id, prod_type_id, tag_id) VALUES (13, 13, 3, 1);
INSERT INTO 'photos_tags_types' (id, photo_id, prod_type_id, tag_id) VALUES (14, 14, 2, 1);
INSERT INTO 'photos_tags_types' (id, photo_id, prod_type_id, tag_id) VALUES (15, 15, 3, 1);
INSERT INTO 'photos_tags_types' (id, photo_id, prod_type_id, tag_id) VALUES (16, 16, 2, 1);
INSERT INTO 'photos_tags_types' (id, photo_id, prod_type_id, tag_id) VALUES (17, 17, 2, 1);
INSERT INTO 'photos_tags_types' (id, photo_id, prod_type_id, tag_id) VALUES (18, 18, 1, 1);
INSERT INTO 'photos_tags_types' (id, photo_id, prod_type_id, tag_id) VALUES (19, 19, 1, 3);
INSERT INTO 'photos_tags_types' (id, photo_id, prod_type_id, tag_id) VALUES (20, 20, 1, 3);
INSERT INTO 'photos_tags_types' (id, photo_id, prod_type_id, tag_id) VALUES (21, 21, 2, 3);

-- home page images

-- messages seed data
INSERT INTO 'messages' (id, date, time, sender_first, sender_last, sender_email, sender_phone, subject, message) VALUES (1, "4/18/2019", "5:07 PM", "Zelda", "Atkins", "jea255@cornell.edu", "(555) 555-5555", "Message test 1", "Hi, this is a test of the messages system from our new website. Hopefully this is displaying okay for you!");
INSERT INTO 'messages' (id, date, time, sender_first, sender_last, sender_email, sender_phone, subject, message) VALUES (2, "4/19/2019", "5:14 PM", "The Dev", "Team", "abc123@cornell.edu", "(123) 456-7890", "Welcome to your messages!", "Welcome to the message system. Here, you can view any messages sent to you and see all other inputed contact information.");
INSERT INTO 'messages' (id, date, time, sender_first, sender_last, sender_email, subject, message) VALUES (3, "5/3/2019", "5:23 PM", "The Dev", "Team", "abc123@cornell.edu", "Phone Number test", "Hi, this is a test to check if the code for phone number is working. For this message, no phone number should be listed because it was not provided!.");


-- 'About' table seed data
INSERT INTO 'about' (id, ext, name, blurb, opening) VALUES (1, "jpg", "Mamta Harris", "Mamta is a real estate agent and mother of two kids. In her free time, she enjoys long walks with her two dogs and all things jewelry- specifically, shiny jewelry! Growing up she was always fascinated with the jewelry her sisters would wear and began collecting jewelry over the last 20 years. She recently learned how to make jewelry and harbored a passion for design. She enjoys showing and selling jewelry collection to her friends and family. Now she has opened up her market to you! Contact her for more information!", "Hello!");


--------------------------------------- TODO: CREATE USER SEED ----------------------------------------
-- NOTE: Passwords are encrypted using the SHA3-512 Algorithm (with the 15-character alphanumeric salt value prepended to password, then hashed).

INSERT INTO 'users' (id, name, username, salt, password) VALUES (1, 'Site Administrator', 'admin', 'E4B7O1unBUzDdaT', 'c4b9211c6ab33eaedb76f4978b6fc5db08ac81090bd4323a7cde33857b0f916d5193fc3deef402e972ff50afd5aced40ac6a30c2bca3fbe19010a767f93a6061');  -- Username: admin, Password: abc123

COMMIT;
