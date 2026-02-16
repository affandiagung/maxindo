-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               10.6.12-MariaDB-0ubuntu0.22.04.1 - Ubuntu 22.04
-- Server OS:                    debian-linux-gnu
-- HeidiSQL Version:             12.3.0.6589
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for trigger maxindorental.employees_before_insert
DROP TRIGGER IF EXISTS `employees_before_insert`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
DELIMITER //
CREATE TRIGGER `employees_before_insert` BEFORE INSERT ON `employees` FOR EACH ROW BEGIN
	SET NEW.UNIT = (SELECT UNIT FROM jobpositions WHERE JOBPOSITIONID=NEW.JOBPOSITION);
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger maxindorental.employees_before_update
DROP TRIGGER IF EXISTS `employees_before_update`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
DELIMITER //
CREATE TRIGGER `employees_before_update` BEFORE UPDATE ON `employees` FOR EACH ROW BEGIN
	SET NEW.UNIT = (SELECT UNIT FROM jobpositions WHERE JOBPOSITIONID=NEW.JOBPOSITION);
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger maxindorental.inventorydetails_after_delete
DROP TRIGGER IF EXISTS `inventorydetails_after_delete`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
DELIMITER //
CREATE TRIGGER `inventorydetails_after_delete` AFTER DELETE ON `inventorydetails` FOR EACH ROW BEGIN
	UPDATE inventories SET TOTITEM=TOTITEM - OLD.QTY WHERE INVENTORYID = OLD.INVENTORY;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger maxindorental.inventorydetails_after_insert
DROP TRIGGER IF EXISTS `inventorydetails_after_insert`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
DELIMITER //
CREATE TRIGGER `inventorydetails_after_insert` AFTER INSERT ON `inventorydetails` FOR EACH ROW BEGIN
	UPDATE inventories SET TOTITEM=TOTITEM + NEW.QTY WHERE INVENTORYID = NEW.INVENTORY;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger maxindorental.inventorydetails_after_update
DROP TRIGGER IF EXISTS `inventorydetails_after_update`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
DELIMITER //
CREATE TRIGGER `inventorydetails_after_update` AFTER UPDATE ON `inventorydetails` FOR EACH ROW BEGIN
	UPDATE inventories SET TOTITEM=TOTITEM - OLD.QTY + NEW.QTY WHERE INVENTORYID = NEW.INVENTORY;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger maxindorental.inventorypackagedetails_after_delete
DROP TRIGGER IF EXISTS `inventorypackagedetails_after_delete`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
DELIMITER //
CREATE TRIGGER `inventorypackagedetails_after_delete` AFTER DELETE ON `inventorypackagedetails` FOR EACH ROW BEGIN
	UPDATE inventorypackages SET TOTALCOST = TOTALCOST - OLD.COST WHERE INVENTORYPACKAGEID = OLD.INVENTORYPACKAGE;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger maxindorental.inventorypackagedetails_after_insert
DROP TRIGGER IF EXISTS `inventorypackagedetails_after_insert`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
DELIMITER //
CREATE TRIGGER `inventorypackagedetails_after_insert` AFTER INSERT ON `inventorypackagedetails` FOR EACH ROW BEGIN
	UPDATE inventorypackages SET TOTALCOST = TOTALCOST + NEW.COST WHERE INVENTORYPACKAGEID = NEW.INVENTORYPACKAGE;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger maxindorental.inventorypackagedetails_after_update
DROP TRIGGER IF EXISTS `inventorypackagedetails_after_update`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
DELIMITER //
CREATE TRIGGER `inventorypackagedetails_after_update` AFTER UPDATE ON `inventorypackagedetails` FOR EACH ROW BEGIN
	UPDATE inventorypackages SET TOTALCOST = TOTALCOST - OLD.COST WHERE INVENTORYPACKAGEID = OLD.INVENTORYPACKAGE;
	UPDATE inventorypackages SET TOTALCOST = TOTALCOST + NEW.COST WHERE INVENTORYPACKAGEID = NEW.INVENTORYPACKAGE;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger maxindorental.projectinventories_before_insert
DROP TRIGGER IF EXISTS `projectinventories_before_insert`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
DELIMITER //
CREATE TRIGGER `projectinventories_before_insert` BEFORE INSERT ON `projectinventories` FOR EACH ROW BEGIN
	SET NEW.INVENTORY = (SELECT INVENTORY FROM inventorydetails WHERE INVENTORYDETAILID=NEW.INVENTORYDETAIL);
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger maxindorental.projectmembers_after_delete
DROP TRIGGER IF EXISTS `projectmembers_after_delete`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
DELIMITER //
CREATE TRIGGER `projectmembers_after_delete` AFTER DELETE ON `projectmembers` FOR EACH ROW BEGIN
	DELETE FROM employeecalendars WHERE EMPLOYEE=OLD.EMPLOYEE AND PROJECT=OLD.PROJECT;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger maxindorental.projectmembers_after_insert
DROP TRIGGER IF EXISTS `projectmembers_after_insert`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
DELIMITER //
CREATE TRIGGER `projectmembers_after_insert` AFTER INSERT ON `projectmembers` FOR EACH ROW BEGIN

	DECLARE projectStage INT(10);
	DECLARE istirahat INT(10);

	SET projectStage = (SELECT projects.PROJECTSTAGE FROM projects WHERE PROJECTID=NEW.PROJECT);
	SET istirahat = (SELECT configurations.PERSONILBREAKDAY FROM configurations LIMIT 1);
	IF( projectStage >= 4 ) THEN
		DELETE FROM employeecalendars WHERE EMPLOYEE=NEW.EMPLOYEE AND PROJECT=NEW.PROJECT;
		INSERT INTO employeecalendars (EMPLOYEE, PROJECT, STARTDATE, ENDDATE, `DESCRIPTION`) 
			SELECT NEW.EMPLOYEE, NEW.PROJECT, projects.SETUPDATE, projects.DISPLACEDATE, CONCAT('Alokasi ke Event/Project : ', projects.NAME)
			FROM projectmembers 
			LEFT JOIN projects ON PROJECT=PROJECTID
			WHERE PROJECTID=NEW.PROJECT;
		
		INSERT INTO employeecalendars (EMPLOYEE, PROJECT, STARTDATE, ENDDATE, `DESCRIPTION`) 
			SELECT NEW.EMPLOYEE, NEW.PROJECT, projects.DISPLACEDATE, DATE_ADD(projects.DISPLACEDATE, INTERVAL istirahat HOUR), CONCAT('Istirahat dari Event/Project : ', projects.NAME)
			FROM projectmembers 
			LEFT JOIN projects ON PROJECT=PROJECTID
			WHERE PROJECTID=NEW.PROJECT;
	END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger maxindorental.projectmembers_after_update
DROP TRIGGER IF EXISTS `projectmembers_after_update`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
DELIMITER //
CREATE TRIGGER `projectmembers_after_update` AFTER UPDATE ON `projectmembers` FOR EACH ROW BEGIN

	DECLARE projectStage INT(10);
	DECLARE istirahat INT(10);

	SET projectStage = (SELECT projects.PROJECTSTAGE FROM projects WHERE PROJECTID=NEW.PROJECT);
	SET istirahat = (SELECT configurations.PERSONILBREAKDAY FROM configurations LIMIT 1);
	IF( projectStage >= 4 ) THEN
		DELETE FROM employeecalendars WHERE (EMPLOYEE=NEW.EMPLOYEE OR EMPLOYEE=OLD.EMPLOYEE) AND PROJECT=NEW.PROJECT;
		INSERT INTO employeecalendars (EMPLOYEE, PROJECT, STARTDATE, ENDDATE, `DESCRIPTION`) 
			SELECT NEW.EMPLOYEE, NEW.PROJECT, projects.SETUPDATE, projects.DISPLACEDATE, CONCAT('Alokasi ke Event/Project : ', projects.NAME)
			FROM projectmembers 
			LEFT JOIN projects ON PROJECT=PROJECTID
			WHERE PROJECTID=NEW.PROJECT;
		
		INSERT INTO employeecalendars (EMPLOYEE, PROJECT, STARTDATE, ENDDATE, `DESCRIPTION`) 
			SELECT NEW.EMPLOYEE, NEW.PROJECT, projects.DISPLACEDATE, DATE_ADD(projects.DISPLACEDATE, INTERVAL istirahat HOUR), CONCAT('Istirahat dari Event/Project : ', projects.NAME)
			FROM projectmembers 
			LEFT JOIN projects ON PROJECT=PROJECTID
			WHERE PROJECTID=NEW.PROJECT;
	END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger maxindorental.projectquotations_after_delete
DROP TRIGGER IF EXISTS `projectquotations_after_delete`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `projectquotations_after_delete` AFTER DELETE ON `projectquotations` FOR EACH ROW BEGIN
	UPDATE projects SET TOTAMOUNT = TOTAMOUNT - OLD.TOTALCOST, FINALAMOUNT = FINALAMOUNT - OLD.FINALCOST WHERE PROJECTID =  OLD.PROJECT;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger maxindorental.projectquotations_after_insert
DROP TRIGGER IF EXISTS `projectquotations_after_insert`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `projectquotations_after_insert` AFTER INSERT ON `projectquotations` FOR EACH ROW BEGIN
	UPDATE projects SET TOTAMOUNT = TOTAMOUNT + NEW.TOTALCOST, FINALAMOUNT = FINALAMOUNT + NEW.FINALCOST WHERE PROJECTID =  NEW.PROJECT;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger maxindorental.projectquotations_after_update
DROP TRIGGER IF EXISTS `projectquotations_after_update`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `projectquotations_after_update` AFTER UPDATE ON `projectquotations` FOR EACH ROW BEGIN
	UPDATE projects SET TOTAMOUNT = TOTAMOUNT - OLD.TOTALCOST + NEW.TOTALCOST, FINALAMOUNT = FINALAMOUNT - OLD.FINALCOST + NEW.FINALCOST WHERE PROJECTID =  NEW.PROJECT;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger maxindorental.projectquotations_before_insert
DROP TRIGGER IF EXISTS `projectquotations_before_insert`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,,NO_ENGINE_SUBSTITUTION';
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
	
	SET NEW.DURATION = (SELECT PROJECTDURATION FROM projects WHERE PROJECTID = NEW.PROJECT);
	SET NEW.TOTALCOST = NEW.QTY * NEW.DURATION * NEW.COST;
	SET NEW.FINALCOST = NEW.TOTALCOST - NEW.DISCOUNT;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger maxindorental.projectquotations_before_update
DROP TRIGGER IF EXISTS `projectquotations_before_update`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `projectquotations_before_update` BEFORE UPDATE ON `projectquotations` FOR EACH ROW BEGIN
	IF(NEW.QTY < NEW.AVAILABLEQTY ) THEN
		SET NEW.STATUS = 1;
	ELSE
		SET NEW.STATUS = 0;
	END IF;
	
	SET NEW.TOTALCOST = NEW.QTY * NEW.DURATION * NEW.COST;
	SET NEW.FINALCOST = NEW.TOTALCOST - NEW.DISCOUNT;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger maxindorental.projects_after_delete
DROP TRIGGER IF EXISTS `projects_after_delete`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
DELIMITER //
CREATE TRIGGER `projects_after_delete` AFTER DELETE ON `projects` FOR EACH ROW BEGIN
	DELETE FROM inventorycalendars WHERE PROJECT=OLD.PROJECTID;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger maxindorental.projects_after_insert
DROP TRIGGER IF EXISTS `projects_after_insert`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
DELIMITER //
CREATE TRIGGER `projects_after_insert` AFTER INSERT ON `projects` FOR EACH ROW BEGIN
	DECLARE projectStage INT(10);
	DECLARE istirahat INT(10);

	SET projectStage = (SELECT projects.PROJECTSTAGE FROM projects WHERE PROJECTID=NEW.PROJECTID);
	SET istirahat = (SELECT configurations.INVENTORYBREAK FROM configurations LIMIT 1);
	
	IF(NEW.PROJECTSTAGE >= 4) THEN
		INSERT INTO inventorycalendars (INVENTORY, STARTDATE, ENDDATE, PROJECT, USEDCOUNT,DESCRIPTION) 
			SELECT INVENTORY, projects.SETUPDATE, DATE_ADD(projects.DISPLACEDATE, INTERVAL istirahat HOUR), PROJECT, QTY, 'Digunakan dan istirahat'
			FROM projectquotations
			LEFT JOIN projects ON PROJECT=PROJECTID
			WHERE projectquotations.PROJECT = NEW.PROJECTID;
	END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger maxindorental.projects_after_update
DROP TRIGGER IF EXISTS `projects_after_update`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `projects_after_update` AFTER UPDATE ON `projects` FOR EACH ROW BEGIN
	DECLARE projectStage INT(10);
	DECLARE istirahat INT(10);

	SET projectStage = (SELECT projects.PROJECTSTAGE FROM projects WHERE PROJECTID=NEW.PROJECTID);
	SET istirahat = (SELECT configurations.INVENTORYBREAK FROM configurations LIMIT 1);
	
	IF(OLD.PROJECTSTAGE >= 4 AND NEW.PROJECTSTAGE < 4) THEN
		DELETE FROM inventorycalendars WHERE PROJECT = NEW.PROJECTID;
	END IF;
	
	IF(OLD.PROJECTSTAGE < 4 AND NEW.PROJECTSTAGE >= 4) THEN
		INSERT INTO inventorycalendars (INVENTORY, STARTDATE, ENDDATE, PROJECT, USEDCOUNT,DESCRIPTION) 
			SELECT INVENTORY, projects.SETUPDATE, DATE_ADD(projects.DISPLACEDATE, INTERVAL istirahat HOUR), PROJECT, QTY, 'Digunakan dan istirahat'
			FROM projectquotations
			LEFT JOIN projects ON PROJECT=PROJECTID
			WHERE projectquotations.PROJECT = NEW.PROJECTID;
	END IF;
	
	#IF(NEW.PROJECTDURATION <> OLD.PROJECTDURATION) THEN
	#	UPDATE projectquotations SET DURATION = NEW.PROJECTDURATION WHERE PROJECT=NEW.PROJECTID;	
	#END IF;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger maxindorental.projects_before_insert
DROP TRIGGER IF EXISTS `projects_before_insert`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `projects_before_insert` BEFORE INSERT ON `projects` FOR EACH ROW BEGIN
	SET NEW.PROJECTDURATION = DATEDIFF(NEW.PROJECTEND, NEW.PROJECTSTART) +1;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger maxindorental.projects_before_update
DROP TRIGGER IF EXISTS `projects_before_update`;
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER `projects_before_update` BEFORE UPDATE ON `projects` FOR EACH ROW BEGIN
	SET NEW.PROJECTDURATION = DATEDIFF(NEW.PROJECTEND, NEW.PROJECTSTART) +1;
END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
