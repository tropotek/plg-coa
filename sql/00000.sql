-- --------------------------------------------
-- Author: Michael Mifsud <info@tropotek.com>
-- --------------------------------------------
--
--
-- --------------------------------------------




-- ----------------------------
--  Skill and Entry Tables
-- ----------------------------
CREATE TABLE IF NOT EXISTS coa (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  profile_id INT UNSIGNED NOT NULL DEFAULT 0,

  type VARCHAR(32) NOT NULL DEFAULT 'company',            -- company (default), staff, student
  subject VARCHAR(255) NOT NULL DEFAULT '',               -- allow curlyBrackets {company.name}
  html TEXT,                                              -- allow curlyBrackets {company.name} (Will be converted to the PDF html2pdf)
  email_html TEXT,                                        -- The email curlyTemplate

  del BOOL NOT NULL DEFAULT 0,
  modified DATETIME NOT NULL,
  created DATETIME NOT NULL,
  KEY (profile_id),
  KEY (del)
) ENGINE=InnoDB;


