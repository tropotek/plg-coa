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
  background VARCHAR(255) NOT NULL DEFAULT '',            -- Background image for PDF
  html TEXT,                                              -- allow curlyBrackets {company.name} (Will be converted to the PDF html2pdf)
  email_html TEXT,                                        -- The email curlyTemplate

  del BOOL NOT NULL DEFAULT 0,
  modified DATETIME NOT NULL,
  created DATETIME NOT NULL,
  KEY (profile_id),
  KEY (del)
) ENGINE=InnoDB;


INSERT INTO `coa` VALUES (null,2,'company','Certificate Of Appreciation For {name}','/institution/1/profile/2/coa/1/dvm34PdfBg.png','<div>\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"text-align: center;\"><span style=\"font-size: 14pt;\">This is to certify that</span></p>\r\n<p style=\"text-align: center;\"><strong><span style=\"font-size: 14pt;\">{name}</span></strong></p>\r\n<p style=\"text-align: center;\"><span style=\"font-size: 14pt;\">has Attained</span></p>\r\n<p style=\"text-align: center;\"><span style=\"font-size: 14pt;\">{cpd} unstructured CPD Points*</span></p>\r\n<p style=\"text-align: center;\"><span style=\"font-size: 14pt;\">Between {dateStart} and {dateEnd}</span></p>\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"text-align: center;\"><span style=\"font-size: 8pt;\">*As described under the Veterinary Practitioners Registration Board of Victoria, </span><br /><span style=\"font-size: 8pt;\">Guideline 13 Continuing Professional Development, section 13.7.1 (a)</span></p>\r\n</div>','<p>Hi {name}</p>\r\n<p>Attached is your certificate.</p>\r\n<p>{sig}</p>',0,'2018-11-28 12:30:32','2018-11-27 07:24:43'),
                         (null,1,'company','Thank you from the Melbourne Veterinary School','/institution/1/profile/1/coa/2/dvm12PdfBg.png','<div>\r\n<p style=\"text-align: right;\">&nbsp;</p>\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"text-align: center;\"><span style=\"font-size: 14pt;\">Certificate of Appreciation</span></p>\r\n<p style=\"text-align: center;\"><span style=\"font-size: 14pt;\">The Faculty of Veterinary Science gratefully acknowledges the support of</span></p>\r\n<p style=\"text-align: center;\"><span style=\"font-size: 14pt;\">{name}</span></p>\r\n<p style=\"text-align: center;\"><span style=\"font-size: 14pt;\">for mentoring and training veterinary students from</span></p>\r\n<p style=\"text-align: center;\"><span style=\"font-size: 14pt;\">The University of Melbourne</span></p>\r\n<p style=\"text-align: center;\"><span style=\"font-size: 14pt;\">{dateFrom} - {dateTo}</span></p>\r\n</div>','<p>Dear {name}</p>\r\n<p>On behalf of the Melbourne Veterinary School and the students you mentored while they were on placement with you in 2016, thank you for your effort in providing such valuable experience to our students.</p>\r\n<p>The placement program is a vital component of our training of future veterinarians and it is only through the hard work and diligence of people, such as you, that the program is successful.</p>\r\n<p>{sig}</p>',0,'2018-11-28 14:03:53','2018-11-28 12:37:52');