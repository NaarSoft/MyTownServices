-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 08, 2018 at 12:36 PM
-- Server version: 10.1.25-MariaDB
-- PHP Version: 7.0.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mts2`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `usp_getavailableslotsforbooking` (IN `p_serviceIds` VARCHAR(100), IN `p_booking_date` DATE)  BEGIN
    DECLARE v_current_datetime DATETIME DEFAULT NULL;
	SET v_current_datetime = (CONVERT_TZ(current_timestamp(),'UTC','America/New_York') + INTERVAL 1 DAY);
    
   
    SET p_serviceIds = (SELECT GROUP_CONCAT(DISTINCT(id) ORDER BY id ASC) AS services FROM `services` AS s
						WHERE FIND_IN_SET(s.id, p_serviceIds));

	CREATE TEMPORARY TABLE slots (
		appointment_date DATE NOT NULL,
		appointment_datetime DATETIME NOT NULL,
        total_waiting_time INT,
        service_details VARCHAR(5000),
		schedule_id VARCHAR(1000) NOT NULL
	);
    
    BLOCK1:BEGIN
        DECLARE v_finished INT DEFAULT 0;
        DECLARE v_finished1 INT DEFAULT 0;
		DECLARE v_date DATE DEFAULT "";
        DECLARE v_service_id INT DEFAULT 0;
        DECLARE v_old_service_id INT DEFAULT 0;
        DECLARE v_schedule_id INT DEFAULT 0;
        DECLARE v_old_schedule_id INT DEFAULT 0;
        DECLARE v_end_datetime DATETIME;
        DECLARE v_old_end_datetime DATETIME DEFAULT "";
        DECLARE v_start_datetime DATETIME;
        DECLARE v_old_start_datetime DATETIME DEFAULT "";
        DECLARE v_schedule_ids_temp VARCHAR(100) DEFAULT "";
        DECLARE v_schedule_ids VARCHAR(100) DEFAULT "";
        DECLARE v_dates_temp VARCHAR(100) DEFAULT "";
        DECLARE v_dates VARCHAR(1000) DEFAULT "";
        DECLARE v_index INT DEFAULT 0;
        DECLARE v_appointment_datetime DATETIME;
        DECLARE v_waiting_time INT;
        DECLARE v_total_waiting_time INT;
        DECLARE v_service_detail VARCHAR(500);
        DECLARE v_all_service_details VARCHAR(5000);
        
		
        
		DEClARE date_cursor CURSOR FOR 
		SELECT date FROM
		 (
			 SELECT date, GROUP_CONCAT(DISTINCT(service_id) ORDER BY service_id ASC) AS services FROM `schedules` AS s
			 INNER JOIN users AS u ON s.user_id = u.id
			 INNER JOIN agency AS a ON u.agency_id = a.id
			 WHERE FIND_IN_SET(a.service_id, p_serviceIds) AND booked_by = 0 AND u.active = 1 AND u.deleted = 0 AND
             (
				(p_booking_date IS NOT NULL AND date = p_booking_date AND CONCAT(date, " ", start_time) >= v_current_datetime) OR 
                (p_booking_date IS NULL AND CONCAT(date, " ", start_time) >= v_current_datetime)
			)
			 GROUP BY date
			 ORDER BY date
		 ) AS tbl WHERE tbl.services = p_serviceIds; 
     
		DECLARE CONTINUE HANDLER 
		FOR NOT FOUND SET v_finished = 1;
	 
		OPEN date_cursor;
	 
		get_date: LOOP
	 
			FETCH date_cursor INTO v_date;
	 
			IF v_finished = 1 THEN 
				LEAVE get_date;
			END IF;
            
            SET v_dates_temp = v_date;
            SET v_finished1 = 0;
            
            BLOCK2:BEGIN                      
         
			DEClARE service_cursor CURSOR FOR 
            SELECT a.service_id
                FROM `schedules` AS s
				INNER JOIN users AS u ON s.user_id = u.id
				INNER JOIN agency AS a ON u.agency_id = a.id
                WHERE date = v_date AND booked_by = 0 AND FIND_IN_SET(a.service_id, p_serviceIds) AND u.active = 1 AND u.deleted = 0
                GROUP BY a.service_id
                ORDER BY CONCAT(date, " ", start_time), a.service_id ASC;
			 
			
			DECLARE CONTINUE HANDLER 
			FOR NOT FOUND SET v_finished1 = 1;            

            SET v_index = 0;
			SET v_old_service_id = 0;
            SET v_schedule_ids_temp = '';
            SET v_appointment_datetime = NULL;
            SET v_old_start_datetime = NULL;
            SET v_old_end_datetime = NULL;
            SET v_total_waiting_time = 0;
            SET v_all_service_details = '';
            
            OPEN service_cursor;
            get_service: LOOP
				FETCH service_cursor INTO v_service_id;
                
                SET v_index = v_index + 1;
                SET v_schedule_id = 0;
                SET v_start_datetime = NULL;
                SET v_end_datetime = NULL;                
                SET v_waiting_time = 0;
	 
                IF v_finished1 = 1 AND v_service_id <> v_old_service_id THEN 
					SET v_schedule_ids_temp = "";
                    SET v_dates_temp = "";
					LEAVE get_service;
				ELSEIF v_finished1 = 1 THEN
					LEAVE get_service;
				END IF;
                
				IF v_service_id = v_old_service_id THEN 
					LEAVE get_service;
				END IF;
				                
                SELECT s.id, CONCAT(date, " ", start_time), CONCAT(date, " ", end_time), 
                CASE WHEN v_index > 1 THEN
						CASE WHEN CONCAT(date, " ", start_time) > v_old_start_datetime THEN ROUND(time_to_sec((TIMEDIFF(CONCAT(date, " ", start_time), v_old_end_datetime))) / 60)
							ELSE 0 END
				ELSE 0 END AS difference,
                CONCAT(a.name, ': ', DATE_FORMAT(start_time, '%h:%i %p'), ' - ', DATE_FORMAT(end_time, '%h:%i %p')) AS service_details
                INTO v_schedule_id, v_start_datetime, v_end_datetime, v_waiting_time, v_service_detail
                FROM `schedules` AS s
				INNER JOIN users AS u ON s.user_id = u.id
				INNER JOIN agency AS a ON u.agency_id = a.id
                WHERE date = v_date AND a.service_id = v_service_id AND booked_by = 0 AND u.active = 1 AND u.deleted = 0
                AND (v_index = 1 OR 
						(
						CONCAT(date, " ", start_time) >= v_old_end_datetime
						)
                    )
				AND (
						(p_booking_date IS NOT NULL AND CONCAT(date, " ", start_time) >= v_current_datetime) OR 
						(p_booking_date IS NULL AND CONCAT(date, " ", start_time) >= v_current_datetime)
                    )
                ORDER BY CONCAT(date, " ", start_time) ASC
                LIMIT 0, 1;
                                             
                IF v_schedule_id > 0 THEN
					IF v_index = 1 OR v_start_datetime < v_old_start_datetime THEN
                        SET v_appointment_datetime = v_start_datetime;
                    END IF;
                    
					SET v_old_schedule_id = v_schedule_id;
					SET v_old_end_datetime = v_end_datetime;
                    SET v_old_start_datetime = v_start_datetime;
                    SET v_total_waiting_time = v_total_waiting_time + v_waiting_time;
                    SET v_service_detail = CONCAT("<li>", v_service_detail, "</li>");
                    SET v_all_service_details = CONCAT(v_all_service_details, v_service_detail);
                    
                    IF v_schedule_ids_temp <> '' THEN
						SET v_schedule_ids_temp = CONCAT(v_schedule_ids_temp, ",", v_schedule_id);
					ELSE
                    	SET v_schedule_ids_temp = v_schedule_id;
                    END IF;
                END IF;
                
                SET v_old_service_id = v_service_id;
			END LOOP get_service;
	 
			CLOSE service_cursor;	
            
            IF v_dates_temp <> '' THEN
				SET v_all_service_details = CONCAT("<ul>", v_all_service_details, "</ul>");
            
				INSERT INTO slots(appointment_date, appointment_datetime, total_waiting_time, service_details, schedule_id)
                VALUES(v_dates_temp, v_appointment_datetime, v_total_waiting_time, v_all_service_details, v_schedule_ids_temp);
                
				IF v_dates <> '' THEN
					SET v_dates = CONCAT(v_dates, ",", v_dates_temp);
				ELSE
					SET v_dates = v_dates_temp;
                END IF;
            END IF;
            
            END BLOCK2;
        
			IF v_schedule_ids <> '' THEN
				SET v_schedule_ids = CONCAT(v_schedule_ids, ",", v_schedule_ids_temp);
			ELSE
				SET v_schedule_ids = v_schedule_ids_temp;
            END IF;            
		END LOOP get_date;
	 
		CLOSE date_cursor;	 
        
        SELECT appointment_date, DATE_FORMAT(appointment_datetime, '%b, %d, %Y') AS formatted_appointment_date, 
        DATE_FORMAT(appointment_datetime, '%b, %d, %Y %h:%i %p') AS appointment_datetime, total_waiting_time, 
        service_details, schedule_id FROM slots 
        WHERE total_waiting_time <= 60;
	END BLOCK1;
    
    DROP TABLE slots;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `agency`
--

CREATE TABLE `agency` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` varchar(500) DEFAULT NULL,
  `contact_info` varchar(100) DEFAULT NULL,
  `website` varchar(100) DEFAULT NULL,
  `service_id` int(11) NOT NULL,
  `htmlcontent` longblob,
  `image_path` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `agency`
--

INSERT INTO `agency` (`id`, `name`, `address`, `contact_info`, `website`, `service_id`, `htmlcontent`, `image_path`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'West Michigan Works', 'A-50', '616-754-3611, www.westmiworks.org', '', 4, 0x57657374204d6963686967616e20576f726b73206f666665727320436172656572204275696c64696e6720417373697374616e63652c20547261696e696e6720666f722061204e6577204361726565722c2043757272656e7420536b696c6c73204173736573736d656e74732c20596f75746820536572766963657320616e6420486972696e67204576656e74732e205468726f7567682065787065727420617373697374616e63652c204d6963686967616e20576f726b732063616e2068656c7020796f752066696e6420796f7572206e657874206361726565722e, '1.png', '2017-04-27 13:23:09', '2017-10-04 04:59:49', 41, 41),
(2, 'EightCAP, Inc.', 'Street Name', '616-754-9315 x3321, TTY 711, www.8cap.org', 'www.8cap.org', 1, 0x45696768744341502c20496e632e2c207761732065737461626c6973686564206173206120436f6d6d756e69747920416374696f6e204167656e637920696e20313936362c2073657276696e672047726174696f742c20496f6e69612c2049736162656c6c612c20616e64204d6f6e7463616c6d20436f756e746965732e204f757220736572766963657320696e636c7564652048656164205374617274207072657363686f6f6c2c204561726c79204865616420537461727420287072656e6174616c2d332079656172206f6c64206368696c6420656475636174696f6e2f66616d696c79207365727669636573292c2057656174686572697a6174696f6e20417373697374616e63652c20436f6d6d756e69747920536572766963657320287574696c69747920617373697374616e63652077697468206d657465726564206675656c2c2064656c6976657261626c65206675656c292c20486f6d656c6573736e6573732073657276696365732c20466f73746572204772616e64706172656e742f53656e696f7220436f6d70616e696f6e2050726f6772616d732c20616e642074686520477265656e76696c6c65204f7074696d6973742043616d7020666f7220496e646976696475616c732077697468204469736162696c69746965732e, '2.png', '2017-04-27 13:23:09', '2017-10-04 04:59:49', 41, 41),
(3, 'Montcalm County Commission on Aging', 'A-65', '989-831-7476, www.montcalm.org', '', 5, 0x4d6f6e7463616c6d20436f756e747920436f6d6d697373696f6e206f6e204167696e6720737570706f7274732073656e696f7220636974697a656e7320616e642074686569722066616d696c6965732062792070726f766964696e6720617373697374616e636520616e6420696e666f726d6174696f6e20746f206d61696e7461696e206865616c74682c206469676e6974792c20696e646570656e64656e636520616e642077656c6c2d6265696e6720666f7220616c6c207265736964656e7473206f66204d6f6e7463616c6d20436f756e74792077686f20617265203630207965617273206f662061676520616e64206f6c6465722e20576520776f726b206174206163636f6d706c697368696e672074686973206d697373696f6e207468726f756768206f7572205472616e73706f72746174696f6e2050726f6772616d2c204a6f75726e65792050726f6772616d2c20526573706974652026205370656369616c697a656420526573706974652050726f6772616d2c20547269702050726f6772616d2c20566f6c756e746565722050726f6772616d2c20496e666f726d6174696f6e20616e6420526566657272616c2c205374616e746f6e2043656e746572204163746976697469657320616e64206f7572204361736520436f6f7264696e6174696f6e20616e6420537570706f72742050726f6772616d2e20576520616c736f2070726f7669646520617373697374616e6365207468726f75676820746865204d656469636169642f4d6564696361726520417373697374616e63652050726f6772616d20616e64206f7572206d6f6e74686c79206e65777370617065722e, '3.png', '2017-04-27 13:23:09', '2017-10-04 04:59:49', 41, 41),
(4, 'Montcalm Care Network', 'A-98', '989-831-7520, www.montcalmcare.net', '', 3, 0x4d6f6e7463616c6d2043617265204e6574776f726b206973206120636f6d70726568656e7369766520636172652070726f766964657220616e64207468652064657369676e6174656420636f6d6d756e697479206d656e74616c206865616c74682073657276696365732070726f6772616d20666f72204d6f6e7463616c6d20436f756e74792e202046726f6d2074686520486f776172642043697479206f66666963652c207765206f66666572206f757470617469656e7420636f756e73656c696e672c2061636365737320746f207073796368696174726963207265736f757263657320616e6420696e74616b65206173736573736d656e747320746f20636f6e6e65637420796f7520746f206f75722066756c6c206172726179206f662073657276696365732c206d6f7374206f662077686963682063616e2062652070726f766964656420696e20796f757220686f6d65206f7220696d6d65646961746520617265612e2020576861746576657220796f7572206e6565647320e280942066726f6d206d656e74616c206865616c74682c207072696d61727920636172652c20616e64207375627374616e636520616275736520e28094204d6f6e7463616c6d2043617265204e6574776f726b2068617320796f7520636f76657265642e, '4.png', '2017-04-27 13:23:09', '2017-10-04 04:59:49', 41, 41),
(5, 'Department of Health and Human Services', '609 N. State Street, Stanton, MI 48888', '989-831-8400, www.michigan.gov', 'michigan.gov', 2, 0x4d6963686967616e204465706172746d656e74206f66204865616c746820616e642048756d616e2053657276696365732068617320456c69676962696c697479205370656369616c6973747320617661696c61626c6520746f20616e737765722067656e6572616c20617373697374616e63652072656c61746564207175657374696f6e7320616e6420617373697374207769746820636f6e7461637420696e666f726d6174696f6e20666f7220616363657373696e67207265736f7572636573206c6f636174656420696e204d6f6e7463616c6d20436f756e74792e20205765e280996c6c2068656c7020796f7520636f6d706c65746520796f7572204d492042726964676573206170706c69636174696f6e20746f206170706c7920666f722062656e6566697473206f666665726564207468726f756768204d444848532e2020596f752063616e20616c736f207375626d697420766572696669636174696f6e7320746f2074686973206c6f636174696f6e2e2020496e74657276696577732063616e20626520636f6d706c6574656420666f7220466f6f6420417373697374616e63652062656e65666974732c204361736820417373697374616e63652062656e65666974732c204368696c6420446576656c6f706d656e7420616e6420436172652062656e65666974732c20616e6420537461746520456d657267656e63792052656c696566204170706c69636174696f6e732e, '5.png', '2017-04-27 13:23:09', '2017-10-16 05:34:31', 41, 41),
(6, 'Mid-Michigan District Health Department', 'A-95', '989-831-5237, www.mmdhd.org', '', 7, 0x4d69642d4d6963686967616e204469737472696374204865616c7468204465706172746d656e742073657276657320436c696e746f6e2c2047726174696f7420616e64204d6f6e7463616c6d20636f756e746965732e2020416c6c206f66206974732070726f6772616d20617265617320696e636c7564652061206865616c746820656475636174696f6e20636f6d706f6e656e7420776869636820686f706566756c6c79206d616b6573207573206d6f726520656666656374697665206173206564756361746f727320696e20746865206669656c64206f662070726576656e7461746976652073657276696365732062792070726f6d6f74696e6720676f6f642073616e69746174696f6e2c20706572736f6e616c206865616c7468207072616374696365732c20616e6420636f6d6d756e6974792073637265656e696e6720616e6420656475636174696f6e2e202020436f6d6d756e697479206865616c746820776f726b6572732c2074686f7365207468617420756e6465727374616e64207468652073797374656d20626563617573652074686579e280997665206265656e207468726f756768207468652073797374656d2c2061726520617661696c61626c6520746f2068656c7020696e20636f6e6e656374696e6720796f752077697468207265736f757263657320666f7220626574746572206865616c74682e, '6.png', '2017-04-27 13:23:09', '2017-10-04 04:59:49', 41, 41),
(7, 'Montcalm County Military & Veteran Services', 'A-65', '989-831-7437, www.montcalm.org', '', 6, 0x4d6f6e7463616c6d20436f756e7479204d696c697461727920616e64205665746572616e20536572766963657320617265206865726520746f2068656c702074686f73652074686174206861766520736572766564206f757220636f756e74727920746f20636f6e6e6563742077697468207265736f7572636573206865726520696e204d6f6e7463616c6d20436f756e74792e20436f6d6520737065616b207769746820616e2061636372656469746564205665746572616e732053657276696365204f66666963657220746f206578706c6f7265204665646572616c2c2053746174652c20616e64204c6f63616c2042656e656669747320796f7520616e6420796f75722066616d696c79206d617920626520656e7469746c65642e205765e280996c6c2068656c70207769746820746865206170706c69636174696f6e2070726f6365737320616e64207365652074686520636c61696d207468726f75676820746f207468652066696e616c206465636973696f6e206f7220666f726d756c61746520616e2061707065616c206966206e65636573736172792e, '7.png', '2017-04-27 13:23:09', '2017-10-16 05:40:23', 41, 41),
(8, 'Great Start Collaborative', 'A-98', '616-225-6146, www.GreatStartMontcalm.org', '', 8, 0x477265617420537461727420436f6c6c61626f726174697665206973206120706172746e657273686970206f6620636f6d6d756e697479206c6561646572732c20627573696e657373206f776e6572732c2063686172697461626c6520616e642066616974682d6261736564206f7267616e697a6174696f6e732c206865616c746820616e642068756d616e2073657276696365206167656e636965732c206564756361746f72732c20616e6420706172656e7473207468617420636f6d6520746f67657468657220666f722074686520707572706f7365206f663a200950726f6d6f74696e672061776172656e65737320616e64206164766f636174696e6720666f72206561726c79206368696c64686f6f64206973737565733b2041646472657373696e6720666163746f7273207468617420696e666c75656e6365207363686f6f6c2072656164696e6573733b20616e642c20506c616e6e696e6720616e6420636f6f7264696e6174696e6720636f6d6d756e6974792073657276696365732e, '8.png', '2017-04-27 13:23:09', '2017-10-04 04:59:49', 41, 41);

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cancelled_appointments`
--

CREATE TABLE `cancelled_appointments` (
  `schedule_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `booked_by` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `cancelled_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `holidays`
--

CREATE TABLE `holidays` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `day` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2017_04_25_120455_entrust_setup_tables', 1),
(4, '2016_06_01_000001_create_oauth_auth_codes_table', 2),
(5, '2016_06_01_000002_create_oauth_access_tokens_table', 2),
(6, '2016_06_01_000003_create_oauth_refresh_tokens_table', 2),
(7, '2016_06_01_000004_create_oauth_clients_table', 2),
(8, '2016_06_01_000005_create_oauth_personal_access_clients_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `oauth_access_tokens`
--

CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `client_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `scopes` text COLLATE utf8_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_auth_codes`
--

CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `scopes` text COLLATE utf8_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_clients`
--

CREATE TABLE `oauth_clients` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `secret` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `redirect` text COLLATE utf8_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `oauth_clients`
--

INSERT INTO `oauth_clients` (`id`, `user_id`, `name`, `secret`, `redirect`, `personal_access_client`, `password_client`, `revoked`, `created_at`, `updated_at`) VALUES
(1, NULL, 'My Town Services Personal Access Client', 'jdsWZnfgOWJU1uPbPGzSqHuDzY0fW2UGMHzgQ1I9', 'http://localhost', 1, 0, 0, '2018-06-04 09:14:07', '2018-06-04 09:14:07'),
(2, NULL, 'My Town Services Password Grant Client', 'eQdoD3W7YqxE3Z8ORgulLu3VHpp7NlfN8vgToutD', 'http://localhost', 0, 1, 0, '2018-06-04 09:14:07', '2018-06-04 09:14:07');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_personal_access_clients`
--

CREATE TABLE `oauth_personal_access_clients` (
  `id` int(10) UNSIGNED NOT NULL,
  `client_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `oauth_personal_access_clients`
--

INSERT INTO `oauth_personal_access_clients` (`id`, `client_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2018-06-04 09:14:07', '2018-06-04 09:14:07');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_refresh_tokens`
--

CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `access_token_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`email`, `token`, `created_at`) VALUES
('test@gmail.com', 'de9e24e53397286584798b4fa331be3810336306707c757a9d1c0eb2f8e5948e', '2018-05-16 04:34:39'),
('nag@gmail.com', '85b557b2e7506eaaeacb292bcb71963134432c89b7bf4e4a2fefc0ff49120da2', '2018-05-16 04:37:23'),
('test5@gmail.com', '62d982180c304372bcf39a7e11666780ad9e50932705e4e45dc9e1e443148bac', '2018-05-16 05:11:06'),
('test51@gmail.com', '415d431e24cef82c4bd2f5bb2d46af9d54f20cfdc2d939fd966ddfbbd808b22e', '2018-05-16 05:25:12'),
('test6@gmail.com', '213ff8386edfdf6888ffd38955e7570f990a59673c9be5c70b383b2d83684ee2', '2018-05-16 05:30:57'),
('young.veeru@gmail.com', '0892fafd0a9f9f30bd5644b1792c8619100710ea41d1dfe880cd5370cf2c3143', '2018-05-31 06:26:37'),
('ranga@gmail.com', '9011d22055e9e401f9639fc6870d4e11909f7c840956066863c87697c6dad933', '2018-06-05 03:54:23'),
('asdf@gmail.com', 'fc040ec452abc02b2213427a29b18eee274d14ae6186fe3a927777d372958c71', '2018-06-06 01:30:43'),
('cvbvb@gmail.com', '18b39077cb1521a1f037e57f25f6cccee10a682054883bcb8424d7fdb44c3cd3', '2018-06-06 06:40:16'),
('rana@gmail.com', 'd7e0f11f9987b83c26f0098da929b44d8ec2495a421d54c78593a69bd59e11ad', '2018-06-07 01:45:10'),
('safdsgfh@gmail.com', '8999257a126b9e36a2c3f3344a9f20a98f879b9c2ff35b23c1cc484a114e606e', '2018-06-07 06:33:56'),
('adfgdfh@gmail.com', 'ab33fa66da756b19e3a75f0e8844d8d2b3790d5025ea24b7853182457e4218e2', '2018-06-07 06:48:48'),
('asdffhfg@gmail.com', 'b6a1bb6a753552ee4891db492ecb501551111961e6bff5050fdaab7f11b504df', '2018-06-07 07:40:04'),
('asfdfhj@gmail.com', 'b4e98f1543219639f8298eab0240ea45c4945e2a6f04573d3dc49058f354ded6', '2018-06-07 09:45:12');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `display_name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'admin-permission', 'Admin Rights', 'Admin Rights', '2017-04-25 13:07:47', '2017-04-25 13:07:47'),
(2, 'manage-responses', 'Manage Responses', 'Manages Responses', '2017-04-25 13:07:47', '2017-04-25 13:07:47');

-- --------------------------------------------------------

--
-- Table structure for table `permission_role`
--

CREATE TABLE `permission_role` (
  `permission_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permission_role`
--

INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

CREATE TABLE `question` (
  `id` int(10) UNSIGNED NOT NULL,
  `q1` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `q2` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `q3` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `q4` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `q5` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `q6` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `q7` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `q8` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `q9` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `q10` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `question1`
--

CREATE TABLE `question1` (
  `id` int(10) NOT NULL,
  `question1` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `service_id` int(11) DEFAULT NULL,
  `text` varchar(1000) NOT NULL,
  `parent_question_id` int(11) NOT NULL,
  `visible` tinyint(1) NOT NULL,
  `show_if` varchar(1000) NOT NULL,
  `position` int(11) NOT NULL,
  `service_identifier` tinyint(1) NOT NULL,
  `condition1` varchar(50) DEFAULT NULL,
  `is_text` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `service_id`, `text`, `parent_question_id`, `visible`, `show_if`, `position`, `service_identifier`, `condition1`, `is_text`) VALUES
(1000000001, NULL, 'Are you a resident of Montcalm County?', 0, 1, '', 0, 0, '', 0),
(1000000002, NULL, 'Are you currently employed?', 0, 1, '', 0, 0, '', 0),
(1000000003, NULL, 'Do you have Medicaid, Healthy Michigan Plan, Medicare or a Medicaid Health Plan coverage?', 0, 1, '', 0, 0, '', 0),
(1000000004, NULL, 'Do you have commercial health insurance (Blue Cross, Priority, United Healthcare)?', 0, 1, '', 0, 0, '', 0),
(1000000005, NULL, 'Do you know which agencies you want to meet with?', 0, 1, '', 0, 0, '', 0),
(1000000006, 1, 'Do you need emergency help with basic needs such as housing, food, or clothing?', 0, 1, '', 1, 0, '', 0),
(1000000007, 1, 'Do you need help with getting food?', 1000000006, 0, '{\"1\": \"true\", \"6\": \"true\"}', 1, 1, '', 0),
(1000000008, 1, 'Do you need help with your heating or electrical bills?', 1000000006, 0, '{\"1\": \"true\", \"6\": \"true\"}', 2, 1, '', 0),
(1000000009, 1, 'Do you need help with finding housing?', 1000000006, 0, '{\"1\": \"true\", \"6\": \"true\"}', 3, 1, '', 0),
(1000000010, 1, 'Do you need help with clothing? ', 1000000006, 0, '{\"1\": \"true\", \"6\": \"true\"}', 4, 1, '', 0),
(1000000011, 2, 'Do you have human service needs such as food, housing or child care?', 0, 1, '', 2, 0, '', 0),
(1000000012, 2, 'Do you need help buying food for your family?', 1000000011, 0, '', 1, 1, '', 0),
(1000000013, 2, 'Is your energy bill past due or shut off?', 1000000011, 0, '', 2, 1, '', 0),
(1000000014, 2, 'Are you in need of Cash Assistance?  Note: You must be disabled or have dependent children living with you.', 1000000011, 0, '', 3, 1, '', 0),
(1000000015, 2, 'Do you need help paying for Child Care?', 1000000011, 0, '', 4, 1, '', 0),
(1000000016, 2, 'Do you have questions or changes regarding your DHHS case or current benefits?', 1000000011, 0, '', 5, 1, '', 0),
(1000000017, 3, 'Do you have mental health or substance use concerns?', 0, 1, '', 3, 0, '', 0),
(1000000018, 3, 'Have you been recommended for mental health services?', 1000000017, 0, '', 1, 1, 'county_minor', 0),
(1000000019, 3, 'Have you received mental health services in the past?', 1000000017, 0, '', 2, 1, 'county_minor', 0),
(1000000020, 3, 'Do you feel hopeless or have thoughts of hurting yourself or others?', 1000000017, 0, '', 3, 1, 'county_minor', 0),
(1000000021, 3, 'Do you struggle to cope with daily life?', 1000000017, 0, '', 4, 1, 'county_minor', 0),
(1000000022, 3, 'Do you have a developmental disability?', 1000000017, 0, '', 5, 1, 'county_minor', 0),
(1000000023, 3, 'Would you like to cut down on drinking or drug use?', 1000000017, 0, '', 6, 1, 'county_minor', 0),
(1000000024, 3, 'Have you (or your child) been recommended for mental health services?', 1000000017, 0, '', 7, 1, 'county_adult', 0),
(1000000025, 3, 'Have you (or your child) received mental health services in the past?', 1000000017, 0, '', 8, 1, 'county_adult', 0),
(1000000026, 3, 'Do you (or your child) feel hopeless or have thoughts of hurting yourself or others?', 1000000017, 0, '', 9, 1, 'county_adult', 0),
(1000000027, 3, 'Do you (or your child) struggle to cope with daily life?', 1000000017, 0, '', 10, 1, 'county_adult', 0),
(1000000028, 3, 'Do you (or your child) have a developmental disability?', 1000000017, 0, '', 11, 1, 'county_adult', 0),
(1000000029, 3, 'Would you (or your child) like to cut down on drinking or drug use?', 1000000017, 0, '', 12, 1, 'county_adult', 0),
(1000000030, 4, 'Do you have employment needs?', 0, 1, '', 4, 0, '', 0),
(1000000031, 4, 'Applying for unemployment benefits?', 1000000030, 0, '', 1, 1, '', 0),
(1000000032, 4, 'Applying for jobs?', 1000000030, 0, '', 2, 1, '', 0),
(1000000033, 4, 'Getting job training?', 1000000030, 0, '', 3, 1, '', 0),
(1000000034, 4, 'Looking for a new career?', 1000000030, 0, '', 4, 1, '', 0),
(1000000035, 5, 'Are you interested in senior services? ', 0, 1, '', 5, 0, '', 0),
(1000000036, 5, 'Home Delivered Meals?', 1000000035, 0, '', 1, 1, 'county_senior', 0),
(1000000037, 5, 'Adult Day activities for individuals with dementia?', 1000000035, 0, '', 2, 1, 'county_senior', 0),
(1000000038, 5, 'In-Home services such as homemaking, personal care or respite?', 1000000035, 0, '', 3, 1, 'county_senior', 0),
(1000000039, 5, 'Medical transportation?', 1000000035, 0, '', 4, 1, 'county_senior', 0),
(1000000040, 5, 'Supplemental insurance through the Medicare/Medicaid Assistance program?', 1000000035, 0, '', 5, 1, 'county_senior', 0),
(1000000041, 5, 'Do you live alone?', 1000000035, 0, '', 6, 1, 'county_senior_service_required', 0),
(1000000042, 5, 'Do you have a caregiver?', 1000000035, 0, '', 7, 1, 'county_senior_service_required', 0),
(1000000043, 5, 'Do you have family that can help you?', 1000000035, 0, '', 8, 1, 'county_senior_service_required', 0),
(1000000044, 5, 'Do you cook?', 1000000035, 0, '', 9, 1, 'county_senior_service_required', 0),
(1000000045, 5, 'Do you use a walker or wheelchair?', 1000000035, 0, '', 10, 1, 'county_senior_service_required', 0),
(1000000046, 5, 'Do you have dementia?', 1000000035, 0, '', 11, 1, 'county_senior_service_required', 0),
(1000000047, 6, 'Are you a Veteran (or the spouse of a Veteran) and interested in services?', 0, 1, '', 6, 0, '', 0),
(1000000048, 6, 'Do you have an original copy of your DD 214?', 1000000047, 0, '', 1, 1, '', 0),
(1000000049, 6, 'Were you honorably discharged from the armed forces?', 1000000047, 0, '', 2, 1, '', 0),
(1000000050, 6, 'Did you sustain or acquire any injury or illness while serving on active duty, are have a pre-existing injury or illness that was aggravated by military service?', 1000000047, 0, '', 3, 1, '', 0),
(1000000051, 6, 'Have you been treated for any service related injury by a Veterans Affairs Hospital or primary care physician?', 1000000047, 0, '', 4, 1, '', 0),
(1000000052, 6, 'Do you have minimal health care coverage according to the Affordable Care Act such as Medicare/ Medicaid or insurance from the Healthcare Marketplace, if no, would you like to apply for VA Health Care?', 1000000047, 0, '', 5, 1, '', 0),
(1000000053, 6, 'Are you over 65 and have less than 1,200.00 in total monthly income?', 1000000047, 0, '', 6, 1, '', 0),
(1000000054, 6, 'Do you or your spouse require the aid and attendance of another person for activities of daily living?', 1000000047, 0, '', 7, 1, '', 0),
(1000000055, 6, 'Have you incurred funeral expenses for the burial of a veteran?', 1000000047, 0, '', 8, 1, '', 0),
(1000000056, 6, 'As the surviving spouse or dependent child of a veteran, have you applied for survivor benefits.', 1000000047, 0, '', 9, 1, '', 0),
(1000000057, 7, 'Do you need help accessing health care related services?', 0, 1, '', 7, 0, '', 0),
(1000000058, 7, 'Someone helping you to get health insurance?', 1000000057, 0, '', 1, 1, 'county_male', 0),
(1000000059, 7, 'Someone helping you to find a primary care provider?', 1000000057, 0, '', 2, 1, 'county_male', 0),
(1000000060, 7, 'Someone helping you to get needed medications?', 1000000057, 0, '', 3, 1, 'county_male', 0),
(1000000061, 7, 'Someone helping you to access other services such as food assistance, housing, etc.?', 1000000057, 0, '', 4, 1, 'county_male', 0),
(1000000062, 7, 'Help with getting health insurance?', 1000000057, 0, '', 5, 1, 'county_female', 0),
(1000000063, 7, 'Help with finding a doctor in your area?', 1000000057, 0, '', 6, 1, 'county_female', 0),
(1000000064, 7, 'Help with getting the medications you need? ', 1000000057, 0, '', 7, 1, 'county_female', 0),
(1000000065, 7, 'Help with getting other services such as food assistance, housing, etc.?', 1000000057, 0, '', 8, 1, 'county_female', 0),
(1000000066, 7, 'Help with getting your yearly examinations, birth control and education?', 1000000057, 0, '', 9, 1, 'county_female', 0),
(1000000067, 7, 'If pregnant and on Medicaid, help with getting the food supplemental program called WIC?', 1000000057, 0, '', 10, 1, 'county_female', 0),
(1000000068, 8, 'Do you have children under the age of 5?  ', 0, 1, '', 8, 0, '', 0),
(1000000069, 8, 'Do you live in a Montcalm Intermediate School District (Carson City-Crystal, Central Montcalm, Greenville, Lakeview, Montabella, Tri-County, Vestaburg)?', 1000000068, 0, '', 1, 1, '', 0),
(1000000070, 8, 'Preschool for a 3 or 4-year old child? ', 1000000069, 0, '', 2, 1, '', 0),
(1000000071, 8, 'Help with finding child care?', 1000000069, 0, '', 3, 1, '', 0),
(1000000072, 8, 'Help with accessing other resources or supports for young children?', 1000000069, 0, '', 4, 1, '', 0),
(1000000073, 3, 'Minor children, age 14 to 17, may request and receive mental health services and mental professionals may provide services on an outpatient basis without the consent or knowledge of the minor\'s parent or guardian.', 1000000017, 0, '', 0, 1, 'county_minor', 1),
(1000000074, 3, 'Note:  The following questions can be answered for yourself or can be answered for a child if you are the parent or legal guardian.', 1000000017, 0, '', 0, 1, 'county_adult', 1),
(1000000075, 4, 'Are you interested in:  (check all that apply)', 1000000030, 0, '', 0, 1, '', 1),
(1000000076, 5, 'Are you interested in:  (check all that apply)', 1000000035, 0, '', 0, 1, 'county_senior', 1),
(1000000077, 7, 'Are you interested in:  (check all that apply)', 1000000057, 0, '', 0, 1, 'county_male', 1),
(1000000078, 7, 'Are you interested in:  (check all that apply)', 1000000057, 0, '', 0, 1, 'county_female', 1),
(1000000079, 8, 'Are you interested in:  (check all that apply)', 1000000069, 0, '', 1, 1, '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `question_detail`
--

CREATE TABLE `question_detail` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `detail` text NOT NULL,
  `condition1` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `question_detail`
--

INSERT INTO `question_detail` (`id`, `question_id`, `detail`, `condition1`) VALUES
(1, 1000000006, 'My Town Services are available for people that live in Montcalm County.   Please visit the link below to get connected with services in your home county.\n<br /><br />\n<ul>\n	<li>\n		<a target=\"_blank\" href=\"http://www.michigan.gov/mdhhs/0,5885,7-339-73970_5461---,00.html\">http://www.michigan.gov/mdhhs/0,5885,7-339-73970_5461---,00.html</a>\n	</li>\n</ul>', ''),
(2, 1000000011, 'My Town Services are available for people that live in Montcalm County.   Please visit the link below to get connected with services in your home county.\n<br /><br />\n<ul>\n	<li>\n		<a target=\"_blank\" href=\"http://www.michigan.gov/mdhhs/0,5885,7-339-73970_5461---,00.html\">http://www.michigan.gov/mdhhs/0,5885,7-339-73970_5461---,00.html</a>\n	</li>\n</ul>', ''),
(3, 1000000017, 'My Town Services are available for people that live in Montcalm County.   Below is contact information for getting connected with mental health services in neighboring counties.\n<ul>\n	<li>\n		Kent County, (616) 336-3909, <a target=\"_blank\" href=\"https://www.network180.org/en/\">https://www.network180.org/en/</a>\n	</li>\n	<li>\n		Mecosta County, (989) 772-5938, <a target=\"_blank\" href=\"http://www.cmhcm.org/\">http://www.cmhcm.org/</a>\n	</li>\n	<li>\n		Newaygo County, (231) 689-7580, <a target=\"_blank\" href=\"http://www.newaygocmh.org/\">http://www.newaygocmh.org/</a>\n	</li>\n</ul>', ''),
(7, 1000000035, 'My Town Services are available for people that live in Montcalm County.   Please visit the link below to get connected with services in Western Michigan.\n<br /><br />\n<ul>\n	<li>\n		<a target=\"_blank\" href=\"http://www.aaawm.org/services_by_county\">http://www.aaawm.org/services_by_county</a>\n	</li>\n</ul>', ''),
(8, 1000000035, 'Senior Services are only available to individuals 60 years of age or older.  If you made an error, please go back and correct this information on page 1. \r\n', 'county'),
(10, 1000000047, 'My Town Services are available for people that live in Montcalm County.   Below is contact information for getting connected with Veterans services in neighboring counties.  \n<br /><br />\n<ul>\n	<li>Kent County, (616) 632-5722, <a target=\"_blank\" href=\"https://www.accesskent.com/Departments/VeteransServices\">https://www.accesskent.com/Departments/VeteransServices</a></li>\n	<li>Mecosta County, (231) 592-0124, <a target=\"_blank\" href=\"http://www.co.mecosta.mi.us/vetaffairs.html\">http://www.co.mecosta.mi.us/vetaffairs.html</a></li>\n	<li>Newaygo County, (231) 689-7030, <a target=\"_blank\" href=\"http://www.countyofnewaygo.com/VeteranAffairs.aspx\">http://www.countyofnewaygo.com/VeteranAffairs.aspx</a></li>\n</ul>', ''),
(11, 1000000057, 'My Town Services are available for people that live in Montcalm County.  Below is contact information for getting connected with health services in neighboring counties.\n<br /><br />\n<ul>\n	<li>Kent County, (616) 632-7100, <a target=\"_blank\" href=\"https://www.accesskent.com/Health/\">https://www.accesskent.com/Health/</a></li>\n	<li>Mecosta County, (231) 592-0130, <a target=\"_blank\" href=\"https://www.dhd10.org/clinic-locations/mecosta/\">https://www.dhd10.org/clinic-locations/mecosta/</a></li>\n	<li>Newaygo County, (231) 689-7300, <a target=\"_blank\" href=\"https://www.dhd10.org/clinic-locations/newaygo/\">https://www.dhd10.org/clinic-locations/newaygo/</a></li>\n</ul>', ''),
(15, 1000000069, 'My Town Services are available for children that live in the Montcalm Intermediate School District.  Below is contact information for getting connected with Great Start services in neighboring counties.\n<br /><br />\n<ul>\n	<li>Kent County, (616) 364-1333, <a target=\"_blank\" href=\"http://www.kentisd.org/instructional-services/early-childhood/great-start-readiness-preschool/\">http://www.kentisd.org/instructional-services/early-childhood/great-start-readiness-preschool/</a></li>\n	<li>Mecosta County, (231) 592-9605, <a target=\"_blank\" href=\"http://www.mogreatstart.org/\">http://www.mogreatstart.org/</a></li>\n	<li>Newaygo County, (231) 652-3604, <a target=\"_blank\" href=\"http://www.newaygocountycc.org/index.php?option=com_content&view=article&id=23&Itemid=128\">http://www.newaygocountycc.org/index.php?option=com_content&view=article&id=23&Itemid=128</a></li>\n</ul>', '');

-- --------------------------------------------------------

--
-- Table structure for table `responses`
--

CREATE TABLE `responses` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email_address` varchar(100) NOT NULL,
  `cell_phone` varchar(20) NOT NULL,
  `mode_of_contact` enum('Email','Text') NOT NULL,
  `gender` enum('M','F') NOT NULL,
  `age` varchar(10) NOT NULL,
  `parent_name` varchar(100) DEFAULT NULL,
  `parent_contact_info` varchar(1000) DEFAULT NULL,
  `cancellation_reason` varchar(1000) DEFAULT NULL,
  `request_id` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `responses`
--

INSERT INTO `responses` (`id`, `name`, `email_address`, `cell_phone`, `mode_of_contact`, `gender`, `age`, `parent_name`, `parent_contact_info`, `cancellation_reason`, `request_id`, `created_at`, `updated_at`) VALUES
(1, 'test', 'test@gmail.com', '(456)123-789_', 'Email', 'M', '25-44', '', '', NULL, NULL, '2018-05-16 06:53:06', '2018-05-16 06:53:06'),
(2, 'veera', 'veera@gmail.com', '(789)456-123_', 'Email', 'M', '25-44', '', '', NULL, NULL, '2018-05-16 07:06:43', '2018-05-16 07:11:26'),
(3, 'test1', 'test1@gmail.com', '(593)895-623_', 'Email', 'M', '25-44', '', '', NULL, NULL, '2018-05-16 07:34:50', '2018-05-16 07:34:50'),
(4, 'test3', 'test3@gmail.com', '(789)456-123_', 'Email', 'M', '25-44', '', '', NULL, NULL, '2018-05-16 10:20:51', '2018-05-16 10:20:51'),
(5, 'test4', 'tes44@gmail.com', '(898)592-3232', 'Email', 'M', '25-44', '', '', NULL, NULL, '2018-05-16 10:28:06', '2018-05-16 10:28:06'),
(6, 'abc', 'abc@gmail.com', '(789)456-123_', 'Email', 'M', '25-44', '', '', NULL, NULL, '2018-05-16 11:32:21', '2018-05-16 11:32:21'),
(7, 'asd', 'asd@gmail.com', '(789)456-123_', 'Email', 'M', '25-44', '', '', NULL, NULL, '2018-05-16 11:36:31', '2018-05-16 11:37:33'),
(8, 'test45', '54@gmail.com', '(789)456-123_', 'Email', 'M', '25-44', '', '', NULL, NULL, '2018-05-18 10:06:55', '2018-05-18 10:06:55'),
(9, 'test', 'test@gmail.com', '(789)456-1231', 'Email', 'M', '25-44', '', '', NULL, NULL, '2018-05-18 10:45:10', '2018-05-18 10:45:10'),
(10, 'zasas', 'asasasas@sdsdsd.fdf', '(786)534-5678', 'Email', 'M', '25-44', '', '', NULL, NULL, '2018-05-18 10:48:59', '2018-05-18 10:48:59'),
(11, 'asdfgdf', 'sfedfrfgbv@serfsdg.ftgh', '(123)456-7834', 'Email', 'M', '18-24', '', '', NULL, NULL, '2018-05-18 10:55:26', '2018-05-18 10:55:26'),
(12, 'zsdsdzsdf', 'sdazxsz@sdfssdf.xcf', '(546)546-5465', 'Email', 'M', '18-24', '', '', NULL, NULL, '2018-05-18 11:04:40', '2018-05-18 11:04:40'),
(13, 'test', 'test@gmail.com', '(789)456-123_', 'Email', 'M', '25-44', '', '', NULL, NULL, '2018-05-18 11:09:37', '2018-05-18 11:09:37'),
(14, 'abc', 'abc@gmail.com', '(850)023-2201', 'Email', 'M', '25-44', '', '', NULL, NULL, '2018-05-21 09:24:43', '2018-05-21 09:26:42'),
(15, 'abc', 'abc@gmail.com', '(789)456-321_', 'Email', 'M', '25-44', '', '', NULL, NULL, '2018-05-21 09:29:28', '2018-05-21 09:29:28'),
(16, 'asd', 'asd@gmail.com', '(789)456-123_', 'Email', 'M', '25-44', '', '', 'test test', NULL, '2018-05-21 11:04:03', '2018-05-21 05:35:07'),
(17, 'test0', 'test0@gmail.com', '(789)456-123_', 'Email', 'M', '25-44', '', '', NULL, NULL, '2018-05-22 05:59:20', '2018-05-22 05:59:21'),
(18, 'test01', 'test01@gmail.com', '(789)456-123_', 'Email', 'M', '25-44', '', '', NULL, NULL, '2018-05-22 06:42:05', '2018-05-22 06:42:05'),
(19, 'a', 'aaaaa@aqq.ghhh', '(456)766-7777', 'Email', 'M', '25-44', '', '', NULL, NULL, '2018-05-22 07:59:34', '2018-05-22 07:59:34'),
(20, 'zcxzvczx', 'xzcxz@gmail.com', '(874)654-313_', 'Email', 'M', '25-44', '', '', NULL, NULL, '2018-05-22 09:52:46', '2018-05-22 09:52:46'),
(21, 'qwe', 'qwe@gmail.com', '(789)456-123_', 'Email', 'M', '25-44', '', '', NULL, NULL, '2018-05-22 11:09:20', '2018-05-22 11:09:20'),
(22, 'qew', 'qew@gmail.com', '(123)456-789_', 'Email', 'M', '25-44', '', '', NULL, NULL, '2018-05-22 11:18:02', '2018-05-22 11:18:02'),
(23, 'test10', 'test10@gmail.com', '(789)545-6123', 'Email', 'M', '25-44', '', '', NULL, NULL, '2018-05-31 06:05:23', '2018-05-31 06:05:23'),
(24, 'test12', 'test12@gmail.com', '(789)456-1234', 'Email', 'M', '25-44', '', '', NULL, NULL, '2018-06-01 12:40:18', '2018-06-01 12:40:18'),
(25, 'veeranji', 'veeranj@gmail.com', '(789)456-123_', 'Email', 'M', '25-44', '', '', NULL, NULL, '2018-06-06 10:43:49', '2018-06-06 10:47:44'),
(26, 'veeranjjjjj', 'veeraaaaaaaa@gmail.com', '(789)456-1230', 'Email', 'M', '25-44', '', '', NULL, NULL, '2018-06-06 10:59:30', '2018-06-06 10:59:30');

-- --------------------------------------------------------

--
-- Table structure for table `response_details`
--

CREATE TABLE `response_details` (
  `response_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `service_id` int(11) DEFAULT NULL,
  `answer` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `response_details`
--

INSERT INTO `response_details` (`response_id`, `question_id`, `service_id`, `answer`) VALUES
(1, 1000000001, 0, 1),
(1, 1000000002, 0, 1),
(1, 1000000003, 0, 1),
(1, 1000000004, 0, 1),
(1, 1000000005, 0, 1),
(2, 1000000001, 0, 1),
(2, 1000000002, 0, 1),
(2, 1000000003, 0, 0),
(2, 1000000004, 0, 0),
(2, 1000000005, 0, 0),
(2, 1000000006, 1, 0),
(2, 1000000011, 2, 0),
(2, 1000000017, 3, 0),
(2, 1000000030, 4, 0),
(2, 1000000035, 5, 1),
(2, 1000000047, 6, 0),
(2, 1000000057, 7, 0),
(2, 1000000068, 8, 0),
(3, 1000000001, 0, 1),
(3, 1000000002, 0, 1),
(3, 1000000003, 0, 0),
(3, 1000000004, 0, 0),
(3, 1000000005, 0, 1),
(4, 1000000001, 0, 1),
(4, 1000000002, 0, 1),
(4, 1000000003, 0, 1),
(4, 1000000004, 0, 1),
(4, 1000000005, 0, 1),
(5, 1000000001, 0, 1),
(5, 1000000002, 0, 1),
(5, 1000000003, 0, 0),
(5, 1000000004, 0, 0),
(5, 1000000005, 0, 1),
(6, 1000000001, 0, 1),
(6, 1000000002, 0, 1),
(6, 1000000003, 0, 0),
(6, 1000000004, 0, 0),
(6, 1000000005, 0, 1),
(7, 1000000001, 0, 1),
(7, 1000000002, 0, 1),
(7, 1000000003, 0, 0),
(7, 1000000004, 0, 0),
(7, 1000000005, 0, 0),
(7, 1000000006, 1, 0),
(7, 1000000011, 2, 0),
(7, 1000000017, 3, 0),
(7, 1000000030, 4, 0),
(7, 1000000035, 5, 1),
(7, 1000000047, 6, 0),
(7, 1000000057, 7, 0),
(7, 1000000068, 8, 0),
(8, 1000000001, 0, 1),
(8, 1000000002, 0, 1),
(8, 1000000003, 0, 1),
(8, 1000000004, 0, 1),
(8, 1000000005, 0, 1),
(9, 1000000001, 0, 1),
(9, 1000000002, 0, 1),
(9, 1000000003, 0, 1),
(9, 1000000004, 0, 1),
(9, 1000000005, 0, 1),
(10, 1000000001, 0, 1),
(10, 1000000002, 0, 1),
(10, 1000000003, 0, 1),
(10, 1000000004, 0, 1),
(10, 1000000005, 0, 1),
(11, 1000000001, 0, 1),
(11, 1000000002, 0, 1),
(11, 1000000003, 0, 1),
(11, 1000000004, 0, 1),
(11, 1000000005, 0, 1),
(12, 1000000001, 0, 1),
(12, 1000000002, 0, 1),
(12, 1000000003, 0, 1),
(12, 1000000004, 0, 1),
(12, 1000000005, 0, 1),
(13, 1000000001, 0, 1),
(13, 1000000002, 0, 1),
(13, 1000000003, 0, 1),
(13, 1000000004, 0, 1),
(13, 1000000005, 0, 1),
(14, 1000000001, 0, 1),
(14, 1000000002, 0, 1),
(14, 1000000003, 0, 1),
(14, 1000000004, 0, 1),
(14, 1000000005, 0, 1),
(15, 1000000001, 0, 1),
(15, 1000000002, 0, 1),
(15, 1000000003, 0, 1),
(15, 1000000004, 0, 1),
(15, 1000000005, 0, 1),
(16, 1000000001, 0, 1),
(16, 1000000002, 0, 1),
(16, 1000000003, 0, 1),
(16, 1000000004, 0, 1),
(16, 1000000005, 0, 1),
(17, 1000000001, 0, 1),
(17, 1000000002, 0, 1),
(17, 1000000003, 0, 1),
(17, 1000000004, 0, 1),
(17, 1000000005, 0, 1),
(18, 1000000001, 0, 1),
(18, 1000000002, 0, 1),
(18, 1000000003, 0, 0),
(18, 1000000004, 0, 0),
(18, 1000000005, 0, 1),
(19, 1000000001, 0, 1),
(19, 1000000002, 0, 1),
(19, 1000000003, 0, 1),
(19, 1000000004, 0, 1),
(19, 1000000005, 0, 1),
(20, 1000000001, 0, 1),
(20, 1000000002, 0, 1),
(20, 1000000003, 0, 1),
(20, 1000000004, 0, 1),
(20, 1000000005, 0, 1),
(21, 1000000001, 0, 1),
(21, 1000000002, 0, 1),
(21, 1000000003, 0, 1),
(21, 1000000004, 0, 1),
(21, 1000000005, 0, 1),
(22, 1000000001, 0, 1),
(22, 1000000002, 0, 1),
(22, 1000000003, 0, 1),
(22, 1000000004, 0, 1),
(22, 1000000005, 0, 1),
(23, 1000000001, 0, 1),
(23, 1000000002, 0, 1),
(23, 1000000003, 0, 1),
(23, 1000000004, 0, 1),
(23, 1000000005, 0, 1),
(24, 1000000001, 0, 1),
(24, 1000000002, 0, 1),
(24, 1000000003, 0, 1),
(24, 1000000004, 0, 1),
(24, 1000000005, 0, 1),
(25, 1000000001, 0, 1),
(25, 1000000002, 0, 1),
(25, 1000000003, 0, 0),
(25, 1000000004, 0, 0),
(25, 1000000005, 0, 0),
(25, 1000000006, 1, 1),
(25, 1000000007, 1, 1),
(25, 1000000008, 1, 1),
(25, 1000000009, 1, 1),
(25, 1000000010, 1, 1),
(25, 1000000011, 2, 1),
(25, 1000000012, 2, 1),
(25, 1000000013, 2, 1),
(25, 1000000014, 2, 1),
(25, 1000000015, 2, 1),
(25, 1000000016, 2, 1),
(25, 1000000017, 3, 1),
(25, 1000000024, 3, 1),
(25, 1000000025, 3, 1),
(25, 1000000026, 3, 1),
(25, 1000000027, 3, 1),
(25, 1000000028, 3, 1),
(25, 1000000029, 3, 1),
(25, 1000000030, 4, 1),
(25, 1000000031, 4, 1),
(25, 1000000032, 4, 1),
(25, 1000000033, 4, 1),
(25, 1000000034, 4, 1),
(25, 1000000035, 5, 1),
(25, 1000000047, 6, 0),
(25, 1000000057, 7, 1),
(25, 1000000058, 7, 1),
(25, 1000000059, 7, 1),
(25, 1000000060, 7, 1),
(25, 1000000061, 7, 1),
(25, 1000000068, 8, 0),
(26, 1000000001, 0, 1),
(26, 1000000002, 0, 1),
(26, 1000000003, 0, 1),
(26, 1000000004, 0, 1),
(26, 1000000005, 0, 1);

--
-- Triggers `response_details`
--
DELIMITER $$
CREATE TRIGGER `response_detail_on_insert_trigger` BEFORE INSERT ON `response_details` FOR EACH ROW BEGIN     
	UPDATE responses SET updated_at = CURRENT_TIMESTAMP WHERE id = NEW.response_id;             
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `display_name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'Admin', 'Admin User', '2017-04-25 07:23:41', '2017-04-25 07:23:41'),
(2, 'agency', 'Agency', 'Agency User', '2017-04-25 07:23:41', '2017-04-25 07:23:41');

-- --------------------------------------------------------

--
-- Table structure for table `role_user`
--

CREATE TABLE `role_user` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_user`
--

INSERT INTO `role_user` (`user_id`, `role_id`) VALUES
(41, 1),
(70, 2),
(71, 2),
(72, 1),
(73, 1),
(74, 1),
(75, 2),
(76, 1),
(77, 2),
(78, 1),
(79, 2),
(80, 1),
(81, 2),
(82, 2),
(83, 2),
(84, 2);

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `booked_by` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `user_id`, `date`, `start_time`, `end_time`, `booked_by`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(83, 72, '2018-05-16', '09:00:00', '09:30:00', 0, '2018-05-16 10:30:08', NULL, 41, NULL),
(84, 72, '2018-05-16', '09:30:00', '10:00:00', 0, '2018-05-16 10:30:08', NULL, 41, NULL),
(85, 72, '2018-05-16', '10:00:00', '10:30:00', 0, '2018-05-16 10:30:08', NULL, 41, NULL),
(86, 72, '2018-05-16', '10:30:00', '11:00:00', 0, '2018-05-16 10:30:08', NULL, 41, NULL),
(87, 72, '2018-05-16', '11:00:00', '11:30:00', 0, '2018-05-16 10:30:08', NULL, 41, NULL),
(88, 72, '2018-05-16', '11:30:00', '12:00:00', 0, '2018-05-16 10:30:08', NULL, 41, NULL),
(89, 72, '2018-05-16', '12:00:00', '12:30:00', 0, '2018-05-16 10:30:08', NULL, 41, NULL),
(90, 72, '2018-05-16', '12:30:00', '13:00:00', 0, '2018-05-16 10:30:08', NULL, 41, NULL),
(91, 72, '2018-05-16', '14:00:00', '14:30:00', 0, '2018-05-16 10:30:08', NULL, 41, NULL),
(92, 72, '2018-05-16', '14:30:00', '15:00:00', 0, '2018-05-16 10:30:08', NULL, 41, NULL),
(93, 72, '2018-05-16', '15:00:00', '15:30:00', 0, '2018-05-16 10:30:08', NULL, 41, NULL),
(94, 72, '2018-05-16', '15:30:00', '16:00:00', 0, '2018-05-16 10:30:08', NULL, 41, NULL),
(101, 41, '2018-05-16', '09:00:00', '10:00:00', 0, '2018-05-16 11:34:13', NULL, 41, NULL),
(102, 41, '2018-05-16', '10:00:00', '11:00:00', 0, '2018-05-16 11:34:13', NULL, 41, NULL),
(103, 41, '2018-05-16', '11:00:00', '12:00:00', 0, '2018-05-16 11:34:13', NULL, 41, NULL),
(104, 41, '2018-05-16', '12:00:00', '13:00:00', 0, '2018-05-16 11:34:13', NULL, 41, NULL),
(105, 41, '2018-05-16', '14:00:00', '15:00:00', 0, '2018-05-16 11:34:13', NULL, 41, NULL),
(106, 41, '2018-05-16', '15:00:00', '16:00:00', 0, '2018-05-16 11:34:13', NULL, 41, NULL),
(119, 72, '2018-05-21', '09:00:00', '09:30:00', 0, '2018-05-21 09:26:22', NULL, 41, NULL),
(120, 72, '2018-05-21', '09:30:00', '10:00:00', 0, '2018-05-21 09:26:22', NULL, 41, NULL),
(121, 72, '2018-05-21', '10:00:00', '10:30:00', 0, '2018-05-21 09:26:22', NULL, 41, NULL),
(122, 72, '2018-05-21', '10:30:00', '11:00:00', 0, '2018-05-21 09:26:22', NULL, 41, NULL),
(123, 72, '2018-05-21', '11:00:00', '11:30:00', 0, '2018-05-21 09:26:22', NULL, 41, NULL),
(124, 72, '2018-05-21', '11:30:00', '12:00:00', 0, '2018-05-21 09:26:22', NULL, 41, NULL),
(125, 72, '2018-05-21', '12:00:00', '12:30:00', 0, '2018-05-21 09:26:22', NULL, 41, NULL),
(126, 72, '2018-05-21', '12:30:00', '13:00:00', 0, '2018-05-21 09:26:22', NULL, 41, NULL),
(127, 72, '2018-05-21', '13:00:00', '13:30:00', 0, '2018-05-21 09:26:22', NULL, 41, NULL),
(128, 72, '2018-05-21', '13:30:00', '14:00:00', 0, '2018-05-21 09:26:22', NULL, 41, NULL),
(129, 72, '2018-05-21', '14:00:00', '14:30:00', 0, '2018-05-21 09:26:22', NULL, 41, NULL),
(130, 72, '2018-05-21', '14:30:00', '15:00:00', 0, '2018-05-21 09:26:22', NULL, 41, NULL),
(131, 72, '2018-05-21', '15:00:00', '15:30:00', 0, '2018-05-21 09:26:22', NULL, 41, NULL),
(132, 72, '2018-05-21', '15:30:00', '16:00:00', 0, '2018-05-21 09:26:22', NULL, 41, NULL),
(133, 41, '2018-05-22', '09:00:00', '09:30:00', 0, '2018-05-22 05:57:48', NULL, 41, NULL),
(134, 41, '2018-05-22', '09:30:00', '10:00:00', 0, '2018-05-22 05:57:48', NULL, 41, NULL),
(135, 41, '2018-05-22', '10:00:00', '10:30:00', 0, '2018-05-22 05:57:48', NULL, 41, NULL),
(136, 41, '2018-05-22', '10:30:00', '11:00:00', 0, '2018-05-22 05:57:48', NULL, 41, NULL),
(137, 41, '2018-05-22', '11:00:00', '11:30:00', 0, '2018-05-22 05:57:48', NULL, 41, NULL),
(138, 41, '2018-05-22', '11:30:00', '12:00:00', 0, '2018-05-22 05:57:48', NULL, 41, NULL),
(139, 41, '2018-05-22', '12:00:00', '12:30:00', 0, '2018-05-22 05:57:48', NULL, 41, NULL),
(140, 41, '2018-05-22', '12:30:00', '13:00:00', 0, '2018-05-22 05:57:48', NULL, 41, NULL),
(141, 41, '2018-05-22', '14:00:00', '14:30:00', 0, '2018-05-22 05:57:48', NULL, 41, NULL),
(142, 41, '2018-05-22', '14:30:00', '15:00:00', 0, '2018-05-22 05:57:48', NULL, 41, NULL),
(143, 41, '2018-05-22', '15:00:00', '15:30:00', 0, '2018-05-22 05:57:48', NULL, 41, NULL),
(144, 41, '2018-05-22', '15:30:00', '16:00:00', 0, '2018-05-22 05:57:48', NULL, 41, NULL),
(181, 41, '2018-06-06', '10:00:00', '10:30:00', 0, '2018-06-06 10:58:13', NULL, 41, NULL),
(182, 41, '2018-06-06', '10:30:00', '11:00:00', 0, '2018-06-06 10:58:13', NULL, 41, NULL),
(183, 41, '2018-06-06', '11:00:00', '11:30:00', 0, '2018-06-06 10:58:13', NULL, 41, NULL),
(184, 41, '2018-06-06', '11:30:00', '12:00:00', 0, '2018-06-06 10:58:13', NULL, 41, NULL),
(185, 41, '2018-06-06', '12:00:00', '12:30:00', 0, '2018-06-06 10:58:13', NULL, 41, NULL),
(186, 41, '2018-06-06', '12:30:00', '13:00:00', 0, '2018-06-06 10:58:13', NULL, 41, NULL),
(187, 41, '2018-06-06', '14:00:00', '14:30:00', 0, '2018-06-06 10:58:13', NULL, 41, NULL),
(188, 41, '2018-06-06', '14:30:00', '15:00:00', 0, '2018-06-06 10:58:13', NULL, 41, NULL),
(189, 41, '2018-06-06', '15:00:00', '15:30:00', 0, '2018-06-06 10:58:13', NULL, 41, NULL),
(190, 41, '2018-06-06', '15:30:00', '16:00:00', 0, '2018-06-06 10:58:13', NULL, 41, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `detail` varchar(500) DEFAULT NULL,
  `position` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `detail`, `position`, `created_at`, `updated_at`) VALUES
(1, 'Basic Needs', 'EightCAP, (616) 754-9315, <a href=\"http://www.8cap.org/\">http://www.8cap.org/</a>', 1, '2017-05-10 04:56:35', '2017-05-26 12:29:22'),
(2, 'Human Services', 'Montcalm Department of Health and Human Services, (989) 831-8400, <a href=\"http://www.michigan.gov/mdhhs/0,5885,7-339-73970_5461_66819-287862--,00.html\"> http://www.michigan.gov/mdhhs/0,5885,7-339-73970_5461_66819-287862--,00.html</a>', 2, '2017-05-10 04:56:35', '2017-06-13 13:22:13'),
(3, 'Mental Health', 'Montcalm Care Network, (989) 831-7520, <a href=\"http://montcalmcare.net/\">http://montcalmcare.net/</a>', 3, '2017-05-10 04:56:35', '2017-05-26 12:29:22'),
(4, 'Employment Needs', 'West Michigan Works, (616) 754-3611, <a href=\"http://www.westmiworks.org/\">http://www.westmiworks.org/</a>', 4, '2017-05-10 04:56:35', '2017-05-26 12:29:22'),
(5, 'Senior Services', 'Montcalm Commission on Aging, (989) 831-7476, <a href=\"http://www.montcalm.us/departments_services/health_and_human_services/commission_on_aging.php\">http://www.montcalm.us/departments_services/health_and_human_services/commission_on_aging.php</a>', 5, '2017-05-10 04:56:35', '2017-06-13 13:23:41'),
(6, 'Veterans Services', 'Montcalm County Department of Veterans Affairs, (989) 831-7437, <a href=\"http://www.michigan.gov/dmva/\">http://www.michigan.gov/dmva/</a>', 6, '2017-05-10 04:56:35', '2017-05-26 12:29:22'),
(7, 'Health Care', 'Mid-Michigan District Health Department, (989) 831-5237, <a href=\"http://www.mmdhd.org/\">http://www.mmdhd.org/</a>', 7, '2017-05-10 04:56:35', '2017-05-26 12:29:22'),
(8, 'Education', 'Great Start Collaborative Montcalm County, (616) 225-6146, <a href=\"http://www.greatstartmontcalm.org/wp/\">http://www.greatstartmontcalm.org/wp/</a>', 8, '2017-05-10 04:56:35', '2017-10-02 16:39:42'),
(9, 'Final Step', '', 9, '2017-05-10 04:56:35', '2017-05-11 10:30:22');

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE `setting` (
  `id` int(11) NOT NULL,
  `office_start_time` time NOT NULL,
  `office_end_time` time NOT NULL,
  `lunch_start_time` time NOT NULL,
  `lunch_end_time` time NOT NULL,
  `office_days` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_by` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`id`, `office_start_time`, `office_end_time`, `lunch_start_time`, `lunch_end_time`, `office_days`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, '09:00:00', '16:00:00', '13:00:00', '14:00:00', '1,2,3,4,5', '2017-05-25 11:09:34', '2018-06-06 06:34:06', 41, 41);

-- --------------------------------------------------------

--
-- Table structure for table `student_details`
--

CREATE TABLE `student_details` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `city_name` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_info` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agency_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `updated_by` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `schedule_color` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `remember_token`, `contact_info`, `agency_id`, `created_at`, `updated_at`, `created_by`, `updated_by`, `schedule_color`, `active`, `deleted`) VALUES
(41, 'Veera', 'Appaiahgari', 'veera@naarsys.com', '$2y$10$IHcQHqawj8qw6T1OoNtjXOLZZaRMZyyaeyK2IHoHNUeZjSTLVByWe', 'GLyqEIeOO3Fb1oZpnHE9XcGtQdZuvBR06pY8QHcljQxALiLoI9F3us4uCvcg', '719-581-2221', 4, '2017-07-19 20:13:18', '2018-06-07 07:01:12', 41, 41, '#1b17e6', 1, 0),
(70, 'test', '', 'test@gmail.com', NULL, NULL, '', 5, '2018-05-16 06:57:25', '2018-05-16 06:57:25', 41, 41, '#522626', 0, 0),
(71, 'nag', 'lak', 'nag@gmail.com', NULL, NULL, '', 2, '2018-05-16 10:06:54', '2018-05-16 10:06:54', 41, 41, '#752121', 0, 0),
(72, 'veeranji', 'anji', 'young.veeru@gmail.com', NULL, NULL, 'hyderabad', 1, '2018-05-16 10:15:31', '2018-05-16 10:18:37', 41, 41, '#140303', 1, 0),
(73, 'test5', 'test', 'test5@gmail.com', NULL, NULL, '', 5, '2018-05-16 10:41:06', '2018-05-16 10:41:06', 41, 41, '#330c0c', 0, 0),
(74, 'test5', 'test', 'test51@gmail.com', NULL, NULL, '', 5, '2018-05-16 10:55:12', '2018-05-16 10:55:12', 41, 41, '#330c0c', 0, 0),
(75, 'tes8', '', 'test6@gmail.com', NULL, NULL, '', 5, '2018-05-16 11:00:57', '2018-06-05 13:19:56', 41, 41, '#360c0c', 0, 0),
(76, 'nanda', 'nandaaa', 'nanda@gmail.com', NULL, NULL, '', 5, '2018-06-04 14:53:15', '2018-06-05 13:17:09', 41, 41, '#962323', 0, 1),
(77, 'ranga', 'rao', 'ranga@gmail.com', NULL, NULL, '', 5, '2018-06-05 09:24:23', '2018-06-05 09:24:23', 41, 41, '#f0b6b6', 0, 0),
(78, 'asdfasde', 'asdfgh', 'asdf@gmail.com', NULL, NULL, '', 5, '2018-06-06 07:00:43', '2018-06-07 11:48:17', 41, 41, '#bf2e2e', 0, 0),
(79, 'yhgjgjm', 'cvbvbnvbnm', 'cvbvb@gmail.com', NULL, NULL, '', 5, '2018-06-06 12:10:16', '2018-06-06 12:10:16', 41, 41, '#c22121', 0, 0),
(80, 'rana', 'ranaaa', 'rana@gmail.com', NULL, NULL, '', 5, '2018-06-07 07:15:10', '2018-06-07 07:15:10', 41, 41, '#b51818', 0, 0),
(81, 'dsfdfgdg', 'sdggg', 'safdsgfh@gmail.com', NULL, NULL, '', 5, '2018-06-07 12:03:56', '2018-06-07 12:03:56', 41, 41, '#b87f7f', 0, 0),
(82, 'fdtgdfhfg', 'sdfasdfdfsdg', 'adfgdfh@gmail.com', NULL, NULL, '', 5, '2018-06-07 12:18:48', '2018-06-07 12:18:48', 41, 41, '#ab6565', 0, 0),
(83, 'dsfdgxcvvcn', 'ffbbgsadf', 'asdffhfg@gmail.com', NULL, NULL, '', 5, '2018-06-07 13:10:04', '2018-06-07 13:10:04', 41, 41, '#d9a9a9', 0, 0),
(84, 'adfsgfhjgkjjh,kl', 'xdfcvhbnmjhm', 'asfdfhj@gmail.com', NULL, NULL, '', 5, '2018-06-07 15:15:12', '2018-06-07 15:15:12', 41, 41, '#c78b8b', 0, 0);

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `user_on_insert` BEFORE INSERT ON `users` FOR EACH ROW BEGIN
    SET NEW.created_at = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agency`
--
ALTER TABLE `agency`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `FK_services_agency` (`service_id`),
  ADD KEY `FK_agency_created_by_user` (`created_by`),
  ADD KEY `FK_agency_updated_by_user` (`updated_by`);

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cancelled_appointments`
--
ALTER TABLE `cancelled_appointments`
  ADD PRIMARY KEY (`schedule_id`);

--
-- Indexes for table `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_holidays_created_by_user` (`created_by`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_access_tokens`
--
ALTER TABLE `oauth_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_access_tokens_user_id_index` (`user_id`),
  ADD KEY `oauth_access_tokens_client_id_index` (`client_id`);

--
-- Indexes for table `oauth_auth_codes`
--
ALTER TABLE `oauth_auth_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_clients_user_id_index` (`user_id`);

--
-- Indexes for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_personal_access_clients_client_id_index` (`client_id`);

--
-- Indexes for table `oauth_refresh_tokens`
--
ALTER TABLE `oauth_refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_unique` (`name`);

--
-- Indexes for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `permission_role_role_id_foreign` (`role_id`);

--
-- Indexes for table `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `question1`
--
ALTER TABLE `question1`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_services_questions` (`service_id`);

--
-- Indexes for table `question_detail`
--
ALTER TABLE `question_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `responses`
--
ALTER TABLE `responses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `response_details`
--
ALTER TABLE `response_details`
  ADD KEY `FK_responses_response_details_response_id` (`response_id`),
  ADD KEY `FK_questions_response_details_question_id` (`question_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Indexes for table `role_user`
--
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_user_role_id_foreign` (`role_id`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_users_schedules` (`user_id`),
  ADD KEY `FK_schedules_created_by_user` (`created_by`),
  ADD KEY `FK_schedules_updated_by_user` (`updated_by`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_setting_created_by_user` (`created_by`),
  ADD KEY `FK_setting_updated_by_user` (`updated_by`);

--
-- Indexes for table `student_details`
--
ALTER TABLE `student_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `FK_agency_users` (`agency_id`),
  ADD KEY `FK_users_created_by_user` (`created_by`),
  ADD KEY `FK_users_updated_by_user` (`updated_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `agency`
--
ALTER TABLE `agency`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `holidays`
--
ALTER TABLE `holidays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `question`
--
ALTER TABLE `question`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `question1`
--
ALTER TABLE `question1`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1000000080;
--
-- AUTO_INCREMENT for table `question_detail`
--
ALTER TABLE `question_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `responses`
--
ALTER TABLE `responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=191;
--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `setting`
--
ALTER TABLE `setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `student_details`
--
ALTER TABLE `student_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `agency`
--
ALTER TABLE `agency`
  ADD CONSTRAINT `FK_services_agency` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`);

--
-- Constraints for table `holidays`
--
ALTER TABLE `holidays`
  ADD CONSTRAINT `FK_holidays_created_by_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`email`) REFERENCES `users` (`email`);

--
-- Constraints for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `FK_services_questions` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`);

--
-- Constraints for table `role_user`
--
ALTER TABLE `role_user`
  ADD CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `FK_schedules_created_by_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `FK_schedules_updated_by_user` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `FK_users_schedules` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `setting`
--
ALTER TABLE `setting`
  ADD CONSTRAINT `FK_setting_created_by_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `FK_setting_updated_by_user` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `FK_agency_users` FOREIGN KEY (`agency_id`) REFERENCES `agency` (`id`),
  ADD CONSTRAINT `FK_users_created_by_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `FK_users_updated_by_user` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
