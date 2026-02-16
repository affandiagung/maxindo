-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.33-0ubuntu0.20.04.4 - (Ubuntu)
-- Server OS:                    Linux
-- HeidiSQL Version:             11.2.0.6213
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for trigger maxindo.projectquotations_before_insert
DROP TRIGGER IF EXISTS `projectquotations_before_insert`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='';
DELIMITER //
CREATE TRIGGER `projectquotations_before_insert` BEFORE INSERT ON `projectquotations` FOR EACH ROW BEGIN
	DECLARE urut INT(10);
	IF(NEW.QTY < NEW.AVAILABLEQTY ) THEN
		SET NEW.STATUS = 1;
	ELSE
		SET NEW.STATUS = 0;
	END IF;
	
	SELECT MAX(ORDERSEQ) INTO urut FROM projectquotations WHERE PROJECT=NEW.PROJECT;
	IF (NOT ISNULL( urut )) THEN
		SET NEW.ORDERSEQ = urut + 1;
	ELSE
		SET NEW.ORDERSEQ = 0;
	END IF;
	
	#SET NEW.DURATION = (SELECT PROJECTDURATION FROM projects WHERE PROJECTID = NEW.PROJECT);
	SET NEW.TOTALCOST = NEW.QTY * NEW.DURATION * NEW.COST;
	SET NEW.FINALCOST = NEW.TOTALCOST - NEW.DISCOUNT;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

