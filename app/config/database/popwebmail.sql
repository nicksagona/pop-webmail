--
-- Pop Webmail SQLite Database
--

-- --------------------------------------------------------

--
-- Set database encoding
--

PRAGMA encoding = "UTF-8";
PRAGMA foreign_keys = ON;

-- --------------------------------------------------------

-- --
-- Table structure for table "users"
--

DROP TABLE IF EXISTS "users";
CREATE TABLE IF NOT EXISTS "users" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "username" varchar NOT NULL,
  "password" varchar NOT NULL,
  "email" varchar,
  UNIQUE ("id")
) ;

INSERT INTO "sqlite_sequence" ("name", "seq") VALUES ('users', 0);

--
-- Dumping data for table "users"
--

INSERT INTO "users" ("username", "password") VALUES ('admin', '$2y$08$ckh6UXNYdjdSVzhlcWh2OOCrjBWHarr8Fxf3i2BYVlC29Ag/eoGkC');

-- --------------------------------------------------------

-- --
-- Table structure for table "accounts"
--

DROP TABLE IF EXISTS "accounts";
CREATE TABLE IF NOT EXISTS "accounts" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "imap_host" varchar,
  "imap_port" varchar,
  "imap_username" varchar,
  "imap_password" varchar,
  "smtp_host" varchar,
  "smtp_port" varchar,
  "smtp_username" varchar,
  "smtp_password" varchar,
  "smtp_security" varchar,
  UNIQUE ("id")
) ;

INSERT INTO "sqlite_sequence" ("name", "seq") VALUES ('accounts', 0);
