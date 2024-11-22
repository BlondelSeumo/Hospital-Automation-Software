-- Smart Hospital DB
-- version 5.0
-- https://smart-hospital.in
-- https://qdocs.in
-- Total tables: 190

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Table structure for table `ambulance_call`
--

CREATE TABLE `ambulance_call` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `case_reference_id` int DEFAULT NULL,
  `vehicle_id` int DEFAULT NULL,
  `contact_no` varchar(20) DEFAULT NULL,
  `address` text,
  `vehicle_model` varchar(20) DEFAULT NULL,
  `driver` varchar(100) NOT NULL,
  `date` datetime DEFAULT NULL,
  `call_from` varchar(200) NOT NULL,
  `call_to` varchar(200) NOT NULL,
  `charge_category_id` int DEFAULT NULL,
  `charge_id` int DEFAULT NULL,
  `standard_charge` int DEFAULT NULL,
  `discount_percentage` float(10,2) DEFAULT '0.00',
  `discount` float(10,2) DEFAULT '0.00',
  `tax_percentage` float(10,2) DEFAULT NULL,
  `amount` float(10,2) DEFAULT '0.00',
  `net_amount` float(10,2) DEFAULT NULL,
  `transaction_id` int DEFAULT NULL,
  `note` text,
  `generated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `annual_calendar`
--

CREATE TABLE `annual_calendar` (
  `id` int NOT NULL,
  `holiday_type` int NOT NULL COMMENT '1 for holiday , 2 for activity , 3 for vacation',
  `from_date` datetime DEFAULT NULL,
  `to_date` datetime DEFAULT NULL,
  `description` text NOT NULL COMMENT 'Holiday Description',
  `is_active` int NOT NULL DEFAULT '1' COMMENT '1 for active 0 for inactive',
  `holiday_color` varchar(200) NOT NULL,
  `front_site` int NOT NULL DEFAULT '0',
  `created_by` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `antenatal_examine`
--

CREATE TABLE `antenatal_examine` (
  `id` int NOT NULL,
  `primary_examine_id` int NOT NULL,
  `visit_details_id` int DEFAULT NULL,
  `ipdid` int DEFAULT NULL,
  `uter_size` varchar(250) DEFAULT NULL,
  `uterus_size` varchar(250) DEFAULT NULL,
  `presentation_position` varchar(250) DEFAULT NULL,
  `brim_presentation` varchar(250) DEFAULT NULL,
  `foeta_heart` varchar(250) DEFAULT NULL,
  `blood_pressure` varchar(250) DEFAULT NULL,
  `antenatal_oedema` varchar(250) DEFAULT NULL,
  `antenatal_weight` varchar(250) DEFAULT NULL,
  `urine_sugar` varchar(250) DEFAULT NULL,
  `urine` varchar(250) DEFAULT NULL,
  `remark` text,
  `next_visit` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `id` int NOT NULL,
  `patient_id` int DEFAULT NULL,
  `case_reference_id` int DEFAULT NULL,
  `visit_details_id` int DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `priority` varchar(100) NOT NULL,
  `specialist` varchar(100) NOT NULL,
  `doctor` int DEFAULT NULL,
  `amount` varchar(200) NOT NULL,
  `message` text,
  `appointment_status` varchar(11) DEFAULT NULL,
  `source` varchar(100) NOT NULL,
  `is_opd` varchar(10) NOT NULL,
  `is_ipd` varchar(10) NOT NULL,
  `doctor_shift_time_id` int DEFAULT NULL,
  `doctor_global_shift_id` int DEFAULT NULL,
  `is_queue` int DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `rejected_time` datetime DEFAULT NULL,
  `live_consult` varchar(50) DEFAULT NULL,
  `live_consult_link` int NOT NULL DEFAULT '1' COMMENT '1 (link created) 0 (not created)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `appointment_payment`
--

CREATE TABLE `appointment_payment` (
  `id` int NOT NULL,
  `appointment_id` int DEFAULT NULL,
  `charge_id` int DEFAULT NULL,
  `standard_amount` float(10,2) NOT NULL DEFAULT '0.00',
  `tax` float(10,2) NOT NULL DEFAULT '0.00',
  `discount_percentage` float(10,2) NOT NULL DEFAULT '0.00',
  `paid_amount` float(10,2) NOT NULL,
  `payment_mode` varchar(50) DEFAULT NULL,
  `payment_type` varchar(100) NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `note` varchar(100) DEFAULT NULL,
  `date` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `appointment_queue`
--

CREATE TABLE `appointment_queue` (
  `id` int NOT NULL,
  `appointment_id` int DEFAULT NULL,
  `staff_id` int DEFAULT NULL,
  `position` int DEFAULT NULL,
  `shift_id` int DEFAULT NULL,
  `date` date NOT NULL DEFAULT '2021-01-11',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `appoint_priority`
--

CREATE TABLE `appoint_priority` (
  `id` int NOT NULL,
  `appoint_priority` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `appoint_priority`
--

INSERT INTO `appoint_priority` (`id`, `appoint_priority`, `created_at`) VALUES
(1, 'Normal', '0000-00-00 00:00:00'),
(2, 'Urgent', '0000-00-00 00:00:00'),
(3, 'Very Urgent', '0000-00-00 00:00:00'),
(5, 'Low', '2021-09-24 13:28:40');

-- --------------------------------------------------------

--
-- Table structure for table `bed`
--

CREATE TABLE `bed` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `bed_type_id` int DEFAULT NULL,
  `bed_group_id` int DEFAULT NULL,
  `is_active` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `bed_group`
--

CREATE TABLE `bed_group` (
  `id` int NOT NULL,
  `name` varchar(200) NOT NULL,
  `color` varchar(50) NOT NULL DEFAULT '#f4f4f4',
  `description` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `floor` int NOT NULL,
  `is_active` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `bed_type`
--

CREATE TABLE `bed_type` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `bill`
--

CREATE TABLE `bill` (
  `id` int NOT NULL,
  `case_id` int NOT NULL,
  `attachment` mediumtext CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `attachment_name` mediumtext,
  `amount` float(10,2) DEFAULT NULL,
  `payment_mode` varchar(100) DEFAULT NULL,
  `cheque_no` varchar(100) DEFAULT NULL,
  `cheque_date` date DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `note` mediumtext,
  `received_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `birth_report`
--

CREATE TABLE `birth_report` (
  `id` int NOT NULL,
  `child_name` varchar(200) NOT NULL,
  `child_pic` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `gender` varchar(200) NOT NULL,
  `birth_date` datetime DEFAULT NULL,
  `weight` varchar(200) NOT NULL,
  `patient_id` int DEFAULT NULL,
  `case_reference_id` int DEFAULT NULL,
  `contact` varchar(20) NOT NULL,
  `mother_pic` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `father_name` varchar(200) NOT NULL,
  `father_pic` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `birth_report` mediumtext,
  `document` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `address` text,
  `is_active` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `blood_bank_products`
--

CREATE TABLE `blood_bank_products` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_blood_group` int DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `blood_donor`
--

CREATE TABLE `blood_donor` (
  `id` int NOT NULL,
  `donor_name` varchar(100) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `blood_bank_product_id` int DEFAULT NULL,
  `gender` varchar(11) DEFAULT NULL,
  `father_name` varchar(100) DEFAULT NULL,
  `address` text,
  `contact_no` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `blood_donor_cycle`
--

CREATE TABLE `blood_donor_cycle` (
  `id` int NOT NULL,
  `blood_donor_cycle_id` int NOT NULL,
  `blood_bank_product_id` int DEFAULT NULL,
  `blood_donor_id` int DEFAULT NULL,
  `charge_id` int DEFAULT NULL,
  `donate_date` date DEFAULT NULL,
  `bag_no` varchar(11) DEFAULT NULL,
  `lot` varchar(11) DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `standard_charge` float(10,2) DEFAULT NULL,
  `apply_charge` float(10,2) DEFAULT NULL,
  `amount` float(10,2) DEFAULT NULL,
  `institution` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `note` text,
  `discount_percentage` float(10,2) DEFAULT '0.00',
  `tax_percentage` float(10,2) DEFAULT '0.00',
  `volume` varchar(100) DEFAULT NULL,
  `unit` int DEFAULT NULL,
  `available` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `blood_issue`
--

CREATE TABLE `blood_issue` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `case_reference_id` int DEFAULT NULL,
  `blood_donor_cycle_id` int DEFAULT NULL,
  `date_of_issue` datetime DEFAULT NULL,
  `hospital_doctor` int DEFAULT NULL,
  `reference` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `charge_id` int DEFAULT NULL,
  `standard_charge` int NOT NULL,
  `tax_percentage` float(10,2) NOT NULL,
  `discount_percentage` float(10,2) DEFAULT '0.00',
  `amount` float(10,2) DEFAULT NULL,
  `net_amount` float(10,2) NOT NULL,
  `institution` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `technician` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `remark` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `organisation_id` int DEFAULT NULL,
  `insurance_validity` date DEFAULT NULL,
  `insurance_id` varchar(250) DEFAULT NULL,
  `generated_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `captcha`
--

CREATE TABLE `captcha` (
  `id` int NOT NULL,
  `name` varchar(250) NOT NULL,
  `status` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `captcha`
--

INSERT INTO `captcha` (`id`, `name`, `status`, `created_at`) VALUES
(1, 'userlogin', 0, '2021-10-22 05:21:32'),
(2, 'login', 0, '2021-10-22 05:21:38'),
(3, 'appointment', 0, '2021-10-22 05:21:40');

-- --------------------------------------------------------

--
-- Table structure for table `case_references`
--

CREATE TABLE `case_references` (
  `id` int NOT NULL,
  `bill_id` int DEFAULT NULL,
  `discount_percentage` float(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `id` int NOT NULL,
  `certificate_name` varchar(100) NOT NULL,
  `certificate_text` text,
  `left_header` varchar(100) NOT NULL,
  `center_header` varchar(100) NOT NULL,
  `right_header` varchar(100) NOT NULL,
  `left_footer` varchar(100) NOT NULL,
  `right_footer` varchar(100) NOT NULL,
  `center_footer` varchar(100) NOT NULL,
  `background_image` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `created_for` tinyint(1) NOT NULL COMMENT '1 = staff, 2 = patients',
  `status` tinyint(1) NOT NULL,
  `header_height` int NOT NULL,
  `content_height` int NOT NULL,
  `footer_height` int NOT NULL,
  `content_width` int NOT NULL,
  `enable_patient_image` tinyint(1) NOT NULL COMMENT '0=no,1=yes',
  `enable_image_height` int NOT NULL,
  `updated_at` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `certificates`
--

INSERT INTO `certificates` (`id`, `certificate_name`, `certificate_text`, `left_header`, `center_header`, `right_header`, `left_footer`, `right_footer`, `center_footer`, `background_image`, `created_for`, `status`, `header_height`, `content_height`, `footer_height`, `content_width`, `enable_patient_image`, `enable_image_height`, `updated_at`, `created_at`) VALUES
(12, 'Sample Patient File Cover', '<table class=\"table table-bordered\" width=\"100%\">\r\n <tr>\r\n  <td width=\"50%\">Patient Name  </td>\r\n  <td width=\"50%\">[name] ([patient_id]) </td>\r\n </tr>\r\n <tr>\r\n  <td>Date of birth</td>\r\n  <td valign=\"top\">[dob]</td>\r\n </tr>\r\n <tr>\r\n  <td>Age</td>\r\n  <td valign=\"top\">[age]</td>\r\n </tr>\r\n <tr>\r\n  <td>Gender</td>\r\n  <td valign=\"top\">[gender]</td>\r\n </tr>\r\n \r\n <tr>\r\n  <td>Phone</td>\r\n  <td valign=\"top\">[phone]</td>\r\n </tr>\r\n <tr>\r\n  <td>Guardian Name</td>\r\n  <td valign=\"top\">[guardian_name]</td>\r\n </tr>\r\n <tr>\r\n  <td>Address</td>\r\n  <td valign=\"top\">[address]</td>\r\n </tr>\r\n <tr>\r\n  <td>Email</td>\r\n  <td valign=\"top\">[email]</td>\r\n </tr>\r\n <tr>\r\n  <td>OPD/IPD NO</td>\r\n  <td valign=\"top\">[opd_ipd_no]</td>\r\n </tr>\r\n  <tr>\r\n  <td>OPD Checkup Id</td>\r\n  <td valign=\"top\">[opd_checkup_id]</td>\r\n </tr>\r\n <tr>\r\n  <td>Consultant Doctor</td>\r\n  <td valign=\"top\">[consultant_doctor]</td>\r\n </tr>\r\n</table>', '<h2>Patient Detail</h2>', '', '', '', '', '', 'certificate.jpg', 2, 1, 140, 300, 700, 600, 1, 200, NULL, '2021-10-28 22:58:45');

-- --------------------------------------------------------

--
-- Table structure for table `charges`
--

CREATE TABLE `charges` (
  `id` int NOT NULL,
  `charge_category_id` int DEFAULT NULL,
  `tax_category_id` int DEFAULT NULL,
  `charge_unit_id` int DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `standard_charge` float(10,2) DEFAULT '0.00',
  `date` date DEFAULT NULL,
  `description` mediumtext,
  `status` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `charge_categories`
--

CREATE TABLE `charge_categories` (
  `id` int NOT NULL,
  `charge_type_id` int DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `description` mediumtext,
  `short_code` varchar(30) DEFAULT NULL,
  `is_default` varchar(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `charge_type_master`
--

CREATE TABLE `charge_type_master` (
  `id` int NOT NULL,
  `charge_type` varchar(200) NOT NULL,
  `is_default` varchar(10) NOT NULL,
  `is_active` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `charge_type_master`
--

INSERT INTO `charge_type_master` (`id`, `charge_type`, `is_default`, `is_active`, `created_at`) VALUES
(1, 'Appointment', 'yes', 'yes', '2021-09-24 14:10:32'),
(2, 'OPD', 'yes', 'yes', '2021-09-24 14:10:02'),
(3, 'IPD', 'yes', 'yes', '2021-09-24 14:10:47'),
(4, 'Pathology', 'yes', 'yes', '2021-10-22 21:40:03'),
(5, 'Radiology', 'yes', 'yes', '2021-10-22 22:10:21'),
(6, 'Blood Bank', 'yes', 'yes', '2021-10-22 22:10:33'),
(7, 'Ambulance', 'yes', 'yes', '2021-10-22 22:10:44'),
(8, 'Procedures', 'yes', 'yes', '2018-08-17 13:40:07'),
(9, 'Investigations', 'yes', 'yes', '2018-08-17 13:40:07'),
(10, 'Supplier', 'yes', 'yes', '2018-08-17 13:40:07'),
(11, 'Operations', 'yes', 'yes', '2018-08-17 13:40:07'),
(12, 'Others', 'yes', 'yes', '2018-08-17 13:40:07');

-- --------------------------------------------------------

--
-- Table structure for table `charge_type_module`
--

CREATE TABLE `charge_type_module` (
  `id` int NOT NULL,
  `charge_type_master_id` int DEFAULT NULL,
  `module_shortcode` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `charge_type_module`
--

INSERT INTO `charge_type_module` (`id`, `charge_type_master_id`, `module_shortcode`, `created_at`) VALUES
(2, 1, 'appointment', '2021-10-23 03:52:42'),
(4, 2, 'opd', '2021-10-23 03:52:45'),
(5, 3, 'ipd', '2021-10-23 03:52:49'),
(6, 4, 'pathology', '2021-10-23 03:52:52'),
(7, 5, 'radiology', '2021-10-23 03:52:54'),
(8, 6, 'blood_bank', '2021-10-23 03:52:56'),
(9, 7, 'ambulance', '2021-10-23 03:52:59'),
(10, 8, 'opd', '2021-10-23 03:53:03'),
(11, 8, 'ipd', '2021-10-23 03:53:04'),
(13, 9, 'pathology', '2021-10-23 03:53:09'),
(14, 9, 'radiology', '2021-10-23 03:53:11'),
(15, 10, 'opd', '2021-10-23 03:53:14'),
(16, 10, 'ipd', '2021-10-23 03:53:16'),
(17, 11, 'opd', '2021-10-23 03:53:18'),
(18, 11, 'ipd', '2021-10-23 03:53:18'),
(19, 12, 'appointment', '2021-10-23 03:53:20'),
(20, 12, 'opd', '2021-10-23 03:53:21'),
(21, 12, 'ipd', '2021-10-23 03:53:21'),
(24, 12, 'pathology', '2021-10-23 03:53:25'),
(25, 12, 'radiology', '2021-10-23 03:53:27'),
(26, 12, 'blood_bank', '2021-10-23 03:53:30'),
(27, 12, 'ambulance', '2021-10-23 03:53:31');

-- --------------------------------------------------------

--
-- Table structure for table `charge_units`
--

CREATE TABLE `charge_units` (
  `id` int NOT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `is_active` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `chat_connections`
--

CREATE TABLE `chat_connections` (
  `id` int NOT NULL,
  `chat_user_one` int NOT NULL,
  `chat_user_two` int NOT NULL,
  `ip` varchar(30) DEFAULT NULL,
  `time` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int NOT NULL,
  `message` text,
  `chat_user_id` int NOT NULL,
  `ip` varchar(30) NOT NULL,
  `time` int NOT NULL,
  `is_first` int DEFAULT '0',
  `is_read` int NOT NULL DEFAULT '0',
  `chat_connection_id` int NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `chat_users`
--

CREATE TABLE `chat_users` (
  `id` int NOT NULL,
  `user_type` varchar(20) DEFAULT NULL,
  `staff_id` int DEFAULT NULL,
  `patient_id` int DEFAULT NULL,
  `create_staff_id` int DEFAULT NULL,
  `create_patient_id` int DEFAULT NULL,
  `is_active` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `complaint`
--

CREATE TABLE `complaint` (
  `id` int NOT NULL,
  `complaint_type_id` int DEFAULT NULL,
  `source` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `email` varchar(200) NOT NULL,
  `date` date DEFAULT NULL,
  `description` text,
  `action_taken` varchar(200) NOT NULL,
  `assigned` varchar(50) NOT NULL,
  `note` text,
  `image` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `complaint_type`
--

CREATE TABLE `complaint_type` (
  `id` int NOT NULL,
  `complaint_type` varchar(100) NOT NULL,
  `description` mediumtext,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `conferences`
--

CREATE TABLE `conferences` (
  `id` int NOT NULL,
  `purpose` varchar(200) NOT NULL,
  `staff_id` int DEFAULT NULL,
  `patient_id` int DEFAULT NULL,
  `visit_details_id` int DEFAULT NULL,
  `ipd_id` int DEFAULT NULL,
  `created_id` int DEFAULT NULL,
  `title` text,
  `date` datetime NOT NULL,
  `duration` int NOT NULL,
  `password` varchar(100) NOT NULL,
  `host_video` int NOT NULL,
  `client_video` int NOT NULL,
  `description` mediumtext,
  `timezone` text,
  `return_response` text,
  `api_type` varchar(50) NOT NULL,
  `status` int NOT NULL,
  `live_consult_link` int NOT NULL DEFAULT '1' COMMENT 'appointment zoom link for front user status',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `conferences_history`
--

CREATE TABLE `conferences_history` (
  `id` int NOT NULL,
  `conference_id` int DEFAULT NULL,
  `staff_id` int DEFAULT NULL,
  `patient_id` int DEFAULT NULL,
  `total_hit` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `conference_staff`
--

CREATE TABLE `conference_staff` (
  `id` int NOT NULL,
  `conference_id` int DEFAULT NULL,
  `staff_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `consultant_register`
--

CREATE TABLE `consultant_register` (
  `id` int NOT NULL,
  `ipd_id` int DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `ins_date` date DEFAULT NULL,
  `instruction` text,
  `cons_doctor` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `consult_charges`
--

CREATE TABLE `consult_charges` (
  `id` int NOT NULL,
  `doctor` int DEFAULT NULL,
  `standard_charge` float(10,2) NOT NULL,
  `date` date NOT NULL,
  `status` varchar(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `contents`
--

CREATE TABLE `contents` (
  `id` int NOT NULL,
  `title` varchar(100) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `is_public` varchar(10) DEFAULT 'No',
  `file` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `note` text,
  `date` date DEFAULT NULL,
  `is_active` varchar(10) DEFAULT 'no',
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `content_for`
--

CREATE TABLE `content_for` (
  `id` int NOT NULL,
  `role` varchar(50) DEFAULT NULL,
  `content_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `content_types`
--

CREATE TABLE `content_types` (
  `id` int NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `description` text,
  `is_active` int DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `custom_fields`
--

CREATE TABLE `custom_fields` (
  `id` int NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `belong_to` varchar(100) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `bs_column` int DEFAULT NULL,
  `validation` int DEFAULT '0',
  `field_values` mediumtext,
  `visible_on_print` int DEFAULT NULL,
  `visible_on_report` int DEFAULT NULL,
  `visible_on_table` int DEFAULT NULL,
  `visible_on_patient_panel` int DEFAULT NULL,
  `weight` int DEFAULT NULL,
  `is_active` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `custom_field_values`
--

CREATE TABLE `custom_field_values` (
  `id` int NOT NULL,
  `belong_table_id` int DEFAULT NULL,
  `custom_field_id` int DEFAULT NULL,
  `field_value` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `death_report`
--

CREATE TABLE `death_report` (
  `id` int NOT NULL,
  `patient_id` int DEFAULT NULL,
  `case_reference_id` int DEFAULT NULL,
  `attachment` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `attachment_name` text,
  `death_date` datetime NOT NULL,
  `guardian_name` varchar(200) NOT NULL,
  `death_report` text,
  `is_active` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `id` int NOT NULL,
  `department_name` varchar(200) NOT NULL,
  `is_active` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `discharge_card`
--

CREATE TABLE `discharge_card` (
  `id` int NOT NULL,
  `case_reference_id` int DEFAULT NULL,
  `opd_details_id` int DEFAULT NULL,
  `ipd_details_id` int DEFAULT NULL,
  `discharge_by` int DEFAULT NULL,
  `discharge_date` datetime DEFAULT NULL,
  `discharge_status` int NOT NULL,
  `death_date` datetime DEFAULT NULL,
  `refer_date` datetime DEFAULT NULL,
  `refer_to_hospital` varchar(255) DEFAULT NULL,
  `reason_for_referral` varchar(255) DEFAULT NULL,
  `operation` varchar(225) NOT NULL,
  `diagnosis` varchar(255) NOT NULL,
  `investigations` text,
  `treatment_home` varchar(255) NOT NULL,
  `note` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `dispatch_receive`
--

CREATE TABLE `dispatch_receive` (
  `id` int NOT NULL,
  `reference_no` varchar(50) NOT NULL,
  `to_title` varchar(100) NOT NULL,
  `address` text,
  `note` text,
  `from_title` varchar(200) NOT NULL,
  `date` date DEFAULT NULL,
  `image` varchar(100) NOT NULL,
  `type` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `doctor_absent`
--

CREATE TABLE `doctor_absent` (
  `id` int NOT NULL,
  `staff_id` int DEFAULT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `doctor_global_shift`
--

CREATE TABLE `doctor_global_shift` (
  `id` int NOT NULL,
  `staff_id` int DEFAULT NULL,
  `global_shift_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `doctor_shift_time`
--

CREATE TABLE `doctor_shift_time` (
  `id` int NOT NULL,
  `day` varchar(20) DEFAULT NULL,
  `staff_id` int DEFAULT NULL,
  `doctor_global_shift_id` int DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `dose_duration`
--

CREATE TABLE `dose_duration` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `dose_interval`
--

CREATE TABLE `dose_interval` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `duty_roster_assign`
--

CREATE TABLE `duty_roster_assign` (
  `id` int NOT NULL,
  `code` int NOT NULL,
  `roster_duty_date` date DEFAULT NULL,
  `floor_id` int DEFAULT NULL,
  `department_id` int DEFAULT NULL,
  `staff_id` int NOT NULL,
  `duty_roster_list_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `duty_roster_list`
--

CREATE TABLE `duty_roster_list` (
  `id` int NOT NULL,
  `duty_roster_shift_id` int NOT NULL,
  `duty_roster_start_date` date NOT NULL,
  `duty_roster_end_date` date NOT NULL,
  `duty_roster_total_day` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `duty_roster_shift`
--

CREATE TABLE `duty_roster_shift` (
  `id` int NOT NULL,
  `shift_name` varchar(255) NOT NULL,
  `shift_start` time NOT NULL,
  `shift_end` time NOT NULL,
  `shift_hour` time NOT NULL,
  `is_active` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `email_config`
--

CREATE TABLE `email_config` (
  `id` int UNSIGNED NOT NULL,
  `email_type` varchar(100) DEFAULT NULL,
  `smtp_server` varchar(100) DEFAULT NULL,
  `smtp_port` varchar(100) DEFAULT NULL,
  `smtp_username` varchar(100) DEFAULT NULL,
  `smtp_password` varchar(100) DEFAULT NULL,
  `ssl_tls` varchar(100) DEFAULT NULL,
  `smtp_auth` varchar(10) NOT NULL,
  `is_active` varchar(10) NOT NULL DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `email_config`
--

INSERT INTO `email_config` (`id`, `email_type`, `smtp_server`, `smtp_port`, `smtp_username`, `smtp_password`, `ssl_tls`, `smtp_auth`, `is_active`, `created_at`) VALUES
(1, 'sendmail', '', '', '', '', '', 'true', 'yes', '2021-09-24 12:44:21');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int NOT NULL,
  `event_title` varchar(200) NOT NULL,
  `event_description` text,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `event_type` varchar(100) NOT NULL,
  `event_color` varchar(200) NOT NULL,
  `event_for` varchar(100) NOT NULL,
  `role_id` int DEFAULT NULL,
  `is_active` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int NOT NULL,
  `exp_head_id` int DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `invoice_no` varchar(200) NOT NULL,
  `date` date DEFAULT NULL,
  `amount` float(10,2) DEFAULT NULL,
  `documents` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `note` text,
  `is_active` varchar(10) DEFAULT 'yes',
  `is_deleted` varchar(10) DEFAULT 'no',
  `generated_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `expense_head`
--

CREATE TABLE `expense_head` (
  `id` int NOT NULL,
  `exp_category` varchar(50) DEFAULT NULL,
  `description` text,
  `is_active` varchar(10) DEFAULT 'yes',
  `is_deleted` varchar(10) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `filetypes`
--

CREATE TABLE `filetypes` (
  `id` int NOT NULL,
  `file_extension` text,
  `file_mime` text,
  `file_size` int NOT NULL,
  `image_extension` text,
  `image_mime` text,
  `image_size` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `finding`
--

CREATE TABLE `finding` (
  `id` int NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text,
  `finding_category_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `finding_category`
--

CREATE TABLE `finding_category` (
  `id` int NOT NULL,
  `category` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `floor`
--

CREATE TABLE `floor` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `front_cms_media_gallery`
--

CREATE TABLE `front_cms_media_gallery` (
  `id` int NOT NULL,
  `image` varchar(300) DEFAULT NULL,
  `thumb_path` varchar(300) DEFAULT NULL,
  `dir_path` varchar(300) DEFAULT NULL,
  `img_name` varchar(300) DEFAULT NULL,
  `thumb_name` varchar(300) DEFAULT NULL,
  `file_type` varchar(100) NOT NULL,
  `file_size` varchar(100) NOT NULL,
  `vid_url` mediumtext,
  `vid_title` varchar(250) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `front_cms_menus`
--

CREATE TABLE `front_cms_menus` (
  `id` int NOT NULL,
  `menu` varchar(100) DEFAULT NULL,
  `slug` varchar(200) DEFAULT NULL,
  `description` mediumtext,
  `open_new_tab` int NOT NULL DEFAULT '0',
  `ext_url` mediumtext,
  `ext_url_link` mediumtext,
  `publish` int NOT NULL DEFAULT '0',
  `content_type` varchar(10) NOT NULL DEFAULT 'manual',
  `is_active` varchar(10) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `front_cms_menus`
--

INSERT INTO `front_cms_menus` (`id`, `menu`, `slug`, `description`, `open_new_tab`, `ext_url`, `ext_url_link`, `publish`, `content_type`, `is_active`, `created_at`) VALUES
(1, 'Main Menu', 'main-menu', 'Main menu', 0, '', '', 0, 'default', 'no', '2018-04-20 03:54:49'),
(2, 'Bottom Menu', 'bottom-menu', 'Bottom Menu', 0, '', '', 0, 'default', 'no', '2018-04-20 03:54:55');

-- --------------------------------------------------------

--
-- Table structure for table `front_cms_menu_items`
--

CREATE TABLE `front_cms_menu_items` (
  `id` int NOT NULL,
  `menu_id` int DEFAULT NULL,
  `menu` varchar(100) DEFAULT NULL,
  `page_id` int NOT NULL,
  `parent_id` int NOT NULL,
  `ext_url` mediumtext,
  `open_new_tab` int DEFAULT '0',
  `ext_url_link` mediumtext,
  `slug` varchar(200) DEFAULT NULL,
  `weight` int DEFAULT NULL,
  `publish` int NOT NULL DEFAULT '0',
  `description` mediumtext,
  `is_active` varchar(10) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `front_cms_menu_items`
--

INSERT INTO `front_cms_menu_items` (`id`, `menu_id`, `menu`, `page_id`, `parent_id`, `ext_url`, `open_new_tab`, `ext_url_link`, `slug`, `weight`, `publish`, `description`, `is_active`, `created_at`) VALUES
(1, 2, 'Home', 1, 0, NULL, NULL, NULL, 'home-1', NULL, 0, NULL, 'no', '2018-07-14 03:14:12'),
(2, 1, 'Appointment', 0, 0, '1', NULL, 'http://yourdomainname.com/form/appointment', 'appointment', 2, 0, NULL, 'no', '2021-09-27 12:04:57'),
(3, 1, 'Home', 1, 0, NULL, NULL, NULL, 'home', NULL, 0, NULL, 'no', '2019-01-24 03:18:17'),
(4, 2, 'Appointment', 0, 0, '1', NULL, 'http://yourdomainname.com/form/appointment', 'appointment-1', NULL, 0, NULL, 'no', '2019-11-02 10:54:41');

-- --------------------------------------------------------

--
-- Table structure for table `front_cms_pages`
--

CREATE TABLE `front_cms_pages` (
  `id` int NOT NULL,
  `page_type` varchar(10) NOT NULL DEFAULT 'manual',
  `is_homepage` int DEFAULT '0',
  `title` varchar(250) DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `slug` varchar(200) DEFAULT NULL,
  `meta_title` mediumtext,
  `meta_description` mediumtext,
  `meta_keyword` mediumtext,
  `feature_image` varchar(200) NOT NULL,
  `description` longtext,
  `publish_date` date DEFAULT NULL,
  `publish` int DEFAULT '0',
  `sidebar` int DEFAULT '0',
  `is_active` varchar(10) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `front_cms_pages`
--

INSERT INTO `front_cms_pages` (`id`, `page_type`, `is_homepage`, `title`, `url`, `type`, `slug`, `meta_title`, `meta_description`, `meta_keyword`, `feature_image`, `description`, `publish_date`, `publish`, `sidebar`, `is_active`, `created_at`) VALUES
(1, 'default', 1, 'Home page first new', 'page/home-page-first-new', 'page', 'home-page-first-new', '', '', '', '', '<p>Home page first</p>', '0000-00-00', 1, 1, 'no', '2021-09-28 15:49:10'),
(2, 'default', 0, 'Complain', 'page/complain', 'page', 'complain', 'Complain form', '                                                                                                                                                                                    complain form                                                                                                                                                                                                                                ', 'complain form', '', '<div class=\"col-md-12 col-sm-12\">\r\n<h2 class=\"text-center\">&nbsp;</h2>\r\n\r\n<p class=\"text-center\">[form-builder:complain]</p>\r\n</div>', '0000-00-00', 1, 1, 'no', '2019-01-24 03:00:12'),
(3, 'default', 0, '404 page', 'page/404-page', 'page', '404-page', '', '                                ', '', '', '<title></title>\r\n<p>404 page found</p>', '0000-00-00', 0, NULL, 'no', '2021-09-24 11:35:15'),
(4, 'default', 0, 'Contact us', 'page/contact-us', 'page', 'contact-us', '', '', '', '', '<p>[form-builder:contact_us]</p>', '0000-00-00', 0, NULL, 'no', '2021-09-24 06:27:54'),
(5, 'manual', 0, 'our-appointment', 'page/our-appointment', 'page', 'our-appointment', '', '', '', '', '<form action=\"welcome/appointment\" method=\"get\">First name: <input name=\"fname\" type=\"text\" /><br />\r\nLast name: <input name=\"lname\" type=\"text\" /><br />\r\n<input type=\"submit\" value=\"Submit\" />&nbsp;</form>', '0000-00-00', 0, 1, 'no', '2021-09-24 11:35:25');

-- --------------------------------------------------------

--
-- Table structure for table `front_cms_page_contents`
--

CREATE TABLE `front_cms_page_contents` (
  `id` int NOT NULL,
  `page_id` int DEFAULT NULL,
  `content_type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `front_cms_programs`
--

CREATE TABLE `front_cms_programs` (
  `id` int NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `url` mediumtext,
  `title` varchar(200) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `event_start` date DEFAULT NULL,
  `event_end` date DEFAULT NULL,
  `event_venue` mediumtext,
  `description` mediumtext,
  `is_active` varchar(10) DEFAULT 'no',
  `meta_title` mediumtext,
  `meta_description` mediumtext,
  `meta_keyword` mediumtext,
  `feature_image` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `publish_date` date DEFAULT NULL,
  `publish` varchar(10) NOT NULL DEFAULT '0',
  `sidebar` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `front_cms_program_photos`
--

CREATE TABLE `front_cms_program_photos` (
  `id` int NOT NULL,
  `program_id` int DEFAULT NULL,
  `media_gallery_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `front_cms_settings`
--

CREATE TABLE `front_cms_settings` (
  `id` int NOT NULL,
  `theme` varchar(50) DEFAULT NULL,
  `is_active_rtl` int DEFAULT '0',
  `is_active_front_cms` int DEFAULT '0',
  `is_active_online_appointment` int DEFAULT NULL,
  `is_active_sidebar` int DEFAULT '0',
  `logo` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `contact_us_email` varchar(100) DEFAULT NULL,
  `complain_form_email` varchar(100) DEFAULT NULL,
  `sidebar_options` mediumtext,
  `fb_url` varchar(200) NOT NULL,
  `twitter_url` varchar(200) NOT NULL,
  `youtube_url` varchar(200) NOT NULL,
  `google_plus` varchar(200) NOT NULL,
  `instagram_url` varchar(200) NOT NULL,
  `pinterest_url` varchar(200) NOT NULL,
  `linkedin_url` varchar(200) NOT NULL,
  `google_analytics` mediumtext,
  `footer_text` varchar(500) DEFAULT NULL,
  `fav_icon` varchar(250) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `front_cms_settings`
--

INSERT INTO `front_cms_settings` (`id`, `theme`, `is_active_rtl`, `is_active_front_cms`, `is_active_online_appointment`, `is_active_sidebar`, `logo`, `contact_us_email`, `complain_form_email`, `sidebar_options`, `fb_url`, `twitter_url`, `youtube_url`, `google_plus`, `instagram_url`, `pinterest_url`, `linkedin_url`, `google_analytics`, `footer_text`, `fav_icon`, `created_at`) VALUES
(1, 'turquoise_blue', NULL, NULL, NULL, NULL, '', '', '', '[\"news\",\"complain\"]', '', '', '', '', '', '', '', '', '', '', '2024-08-21 09:29:55');

-- --------------------------------------------------------

--
-- Table structure for table `gateway_ins`
--

CREATE TABLE `gateway_ins` (
  `id` int NOT NULL,
  `online_appointment_id` int DEFAULT NULL,
  `type` varchar(30) NOT NULL COMMENT 'patient_bill,appointment	',
  `gateway_name` varchar(50) NOT NULL,
  `module_type` varchar(255) NOT NULL,
  `unique_id` varchar(255) NOT NULL,
  `parameter_details` mediumtext NOT NULL,
  `payment_status` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `gateway_ins_response`
--

CREATE TABLE `gateway_ins_response` (
  `id` int NOT NULL,
  `gateway_ins_id` int DEFAULT NULL,
  `posted_data` text,
  `response` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `general_calls`
--

CREATE TABLE `general_calls` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact` varchar(12) NOT NULL,
  `date` date NOT NULL,
  `description` text,
  `follow_up_date` date DEFAULT NULL,
  `call_duration` varchar(50) NOT NULL,
  `note` mediumtext,
  `call_type` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `global_shift`
--

CREATE TABLE `global_shift` (
  `id` int NOT NULL,
  `name` varchar(250) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `income`
--

CREATE TABLE `income` (
  `id` int NOT NULL,
  `inc_head_id` int DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `invoice_no` varchar(200) NOT NULL,
  `date` date DEFAULT NULL,
  `amount` float(10,2) DEFAULT '0.00',
  `note` text,
  `is_deleted` varchar(10) DEFAULT 'no',
  `documents` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `generated_by` int DEFAULT NULL,
  `is_active` varchar(10) DEFAULT 'yes',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `income_head`
--

CREATE TABLE `income_head` (
  `id` int NOT NULL,
  `income_category` varchar(255) DEFAULT NULL,
  `description` text,
  `is_active` varchar(10) NOT NULL DEFAULT 'yes',
  `is_deleted` varchar(10) NOT NULL DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `ipd_details`
--

CREATE TABLE `ipd_details` (
  `id` int NOT NULL,
  `patient_id` int DEFAULT NULL,
  `case_reference_id` int DEFAULT NULL,
  `height` varchar(5) DEFAULT NULL,
  `weight` varchar(5) DEFAULT NULL,
  `pulse` varchar(200) NOT NULL,
  `temperature` varchar(200) NOT NULL,
  `respiration` varchar(200) NOT NULL,
  `bp` varchar(20) DEFAULT NULL,
  `bed` int DEFAULT NULL,
  `bed_group_id` int DEFAULT NULL,
  `case_type` varchar(100) NOT NULL,
  `casualty` varchar(100) NOT NULL,
  `symptoms` longtext NOT NULL,
  `known_allergies` varchar(200) DEFAULT NULL,
  `patient_old` varchar(50) NOT NULL,
  `note` text,
  `refference` varchar(200) NOT NULL,
  `cons_doctor` int DEFAULT NULL,
  `organisation_id` int DEFAULT NULL,
  `credit_limit` varchar(100) NOT NULL,
  `payment_mode` varchar(100) NOT NULL,
  `date` datetime DEFAULT NULL,
  `discharged` varchar(200) NOT NULL,
  `live_consult` varchar(50) DEFAULT NULL,
  `generated_by` int DEFAULT NULL,
  `is_antenatal` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `ipd_doctors`
--

CREATE TABLE `ipd_doctors` (
  `id` int NOT NULL,
  `ipd_id` int NOT NULL,
  `consult_doctor` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `ipd_prescription_basic`
--

CREATE TABLE `ipd_prescription_basic` (
  `id` int NOT NULL,
  `ipd_id` int DEFAULT NULL,
  `visit_details_id` int DEFAULT NULL,
  `attachment` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `attachment_name` text NOT NULL,
  `header_note` text,
  `footer_note` text,
  `finding_description` text,
  `is_finding_print` varchar(100) DEFAULT NULL,
  `date` date NOT NULL,
  `generated_by` int DEFAULT NULL,
  `prescribe_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `ipd_prescription_details`
--

CREATE TABLE `ipd_prescription_details` (
  `id` int NOT NULL,
  `basic_id` int DEFAULT NULL,
  `pharmacy_id` int DEFAULT NULL,
  `dosage` int DEFAULT NULL,
  `dose_interval_id` int DEFAULT NULL,
  `dose_duration_id` int DEFAULT NULL,
  `instruction` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `ipd_prescription_test`
--

CREATE TABLE `ipd_prescription_test` (
  `id` int NOT NULL,
  `ipd_prescription_basic_id` int DEFAULT NULL,
  `pathology_id` int DEFAULT NULL,
  `radiology_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `id` int NOT NULL,
  `item_category_id` int DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `unit` varchar(200) NOT NULL,
  `item_photo` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `description` text,
  `quantity` int NOT NULL,
  `date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `item_category`
--

CREATE TABLE `item_category` (
  `id` int NOT NULL,
  `item_category` varchar(255) DEFAULT NULL,
  `is_active` varchar(10) NOT NULL DEFAULT 'yes',
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `item_issue`
--

CREATE TABLE `item_issue` (
  `id` int NOT NULL,
  `issue_type` int DEFAULT NULL,
  `issue_to` int DEFAULT NULL,
  `issue_by` varchar(100) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `return_date` date DEFAULT NULL,
  `item_category_id` int DEFAULT NULL,
  `item_id` int DEFAULT NULL,
  `quantity` int NOT NULL,
  `note` text,
  `is_returned` int NOT NULL DEFAULT '1',
  `is_active` varchar(10) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `item_stock`
--

CREATE TABLE `item_stock` (
  `id` int NOT NULL,
  `item_id` int DEFAULT NULL,
  `supplier_id` int DEFAULT NULL,
  `symbol` varchar(10) NOT NULL DEFAULT '+',
  `store_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `purchase_price` float(10,2) DEFAULT '0.00',
  `date` date DEFAULT NULL,
  `attachment` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `description` text,
  `is_active` varchar(10) DEFAULT 'yes',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `item_store`
--

CREATE TABLE `item_store` (
  `id` int NOT NULL,
  `item_store` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `item_supplier`
--

CREATE TABLE `item_supplier` (
  `id` int NOT NULL,
  `item_supplier` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `contact_person_name` varchar(255) NOT NULL,
  `contact_person_phone` varchar(255) NOT NULL,
  `contact_person_email` varchar(255) NOT NULL,
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `lab`
--

CREATE TABLE `lab` (
  `id` int NOT NULL,
  `lab_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` int NOT NULL,
  `language` varchar(50) DEFAULT NULL,
  `short_code` varchar(255) NOT NULL,
  `country_code` varchar(255) NOT NULL,
  `is_deleted` varchar(10) NOT NULL DEFAULT 'yes',
  `is_rtl` varchar(10) NOT NULL DEFAULT 'no',
  `is_active` varchar(255) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `language`, `short_code`, `country_code`, `is_deleted`, `is_rtl`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Azerbaijan', 'az', 'az', 'no', 'no', 'no', '2021-09-28 09:51:22', NULL),
(2, 'Albanian', 'sq', 'al', 'no', 'no', 'no', '2021-09-28 10:08:10', NULL),
(3, 'Amharic', 'am', 'am', 'no', 'no', 'no', '2021-09-28 09:50:47', NULL),
(4, 'English', 'en', 'us', 'no', 'no', 'no', '2021-09-16 05:20:47', NULL),
(5, 'Arabic', 'ar', 'sa', 'no', 'no', 'no', '2021-09-28 09:50:48', NULL),
(6, 'Afrikaans', 'af', 'af', 'no', 'no', 'no', '2021-09-28 10:51:19', NULL),
(7, 'Basque', 'eu', 'es', 'no', 'no', 'no', '2021-09-24 06:58:21', NULL),
(8, 'Bengali', 'bn', 'in', 'no', 'no', 'no', '2021-09-24 06:58:25', NULL),
(9, 'Bosnian', 'bs', 'bs', 'no', 'no', 'no', '2021-09-24 06:58:28', NULL),
(10, 'Welsh', 'cy', 'cy', 'no', 'no', 'no', '2021-09-24 06:58:31', NULL),
(11, 'Hungarian', 'hu', 'hu', 'no', 'no', 'no', '2021-09-24 06:58:35', NULL),
(12, 'Vietnamese', 'vi', 'vi', 'no', 'no', 'no', '2021-09-24 06:58:39', NULL),
(13, 'Haitian', 'ht', 'ht', 'no', 'no', 'no', '2021-09-24 06:58:43', NULL),
(14, 'Galician', 'gl', 'gl', 'no', 'no', 'no', '2021-09-24 06:58:47', NULL),
(15, 'Dutch', 'nl', 'nl', 'no', 'no', 'no', '2021-09-24 06:58:51', NULL),
(16, 'Greek', 'el', 'gr', 'no', 'no', 'no', '2021-09-24 06:58:53', NULL),
(17, 'Georgian', 'ka', 'ge', 'no', 'no', 'no', '2021-09-24 06:58:56', NULL),
(18, 'Gujarati', 'gu', 'in', 'no', 'no', 'no', '2021-09-24 06:58:59', NULL),
(19, 'Danish', 'da', 'dk', 'no', 'no', 'no', '2021-09-24 06:59:01', NULL),
(20, 'Hebrew', 'he', 'il', 'no', 'no', 'no', '2021-09-24 06:59:04', NULL),
(21, 'Yiddish', 'yi', 'il', 'no', 'no', 'no', '2021-09-24 06:59:07', NULL),
(22, 'Indonesian', 'id', 'id', 'no', 'no', 'no', '2021-09-24 06:59:10', NULL),
(23, 'Irish', 'ga', 'ga', 'no', 'no', 'no', '2021-09-24 06:59:14', NULL),
(24, 'Italian', 'it', 'it', 'no', 'no', 'no', '2021-09-24 06:59:17', NULL),
(25, 'Icelandic', 'is', 'is', 'no', 'no', 'no', '2021-09-24 06:59:20', NULL),
(26, 'Spanish', 'es', 'es', 'no', 'no', 'no', '2021-09-24 06:59:29', NULL),
(27, 'Kannada', 'kn', 'kn', 'no', 'no', 'no', '2021-09-24 06:59:32', NULL),
(28, 'Catalan', 'ca', 'ca', 'no', 'no', 'no', '2021-09-24 06:59:34', NULL),
(29, 'Chinese', 'zh', 'cn', 'no', 'no', 'no', '2021-09-24 06:59:36', NULL),
(30, 'Korean', 'ko', 'kr', 'no', 'no', 'no', '2021-09-24 06:59:39', NULL),
(31, 'Xhosa', 'xh', 'ls', 'no', 'no', 'no', '2021-09-24 06:59:42', NULL),
(32, 'Latin', 'la', 'la', 'no', 'no', 'no', '2021-09-24 06:59:45', NULL),
(33, 'Latvian', 'lv', 'lv', 'no', 'no', 'no', '2021-09-24 06:59:47', NULL),
(34, 'Lithuanian', 'lt', 'lt', 'no', 'no', 'no', '2021-09-24 06:59:50', NULL),
(35, 'Malagasy', 'mg', 'mg', 'no', 'no', 'no', '2021-09-24 06:59:52', NULL),
(36, 'Malay', 'ms', 'ms', 'no', 'no', 'no', '2021-09-24 07:00:01', NULL),
(37, 'Malayalam', 'ml', 'ml', 'no', 'no', 'no', '2021-09-24 07:00:05', NULL),
(38, 'Maltese', 'mt', 'mt', 'no', 'no', 'no', '2021-09-24 07:00:26', NULL),
(39, 'Macedonian', 'mk', 'mk', 'no', 'no', 'no', '2021-09-24 07:00:41', NULL),
(40, 'Maori', 'mi', 'nz', 'no', 'no', 'no', '2021-09-24 07:00:44', NULL),
(41, 'Marathi', 'mr', 'in', 'no', 'no', 'no', '2021-09-24 07:00:51', NULL),
(42, 'Mongolian', 'mn', 'mn', 'no', 'no', 'no', '2021-09-24 07:01:15', NULL),
(43, 'German', 'de', 'de', 'no', 'no', 'no', '2021-09-24 07:01:18', NULL),
(44, 'Nepali', 'ne', 'ne', 'no', 'no', 'no', '2021-09-24 07:01:21', NULL),
(45, 'Norwegian', 'no', 'no', 'no', 'no', 'no', '2021-09-24 07:01:41', NULL),
(46, 'Punjabi', 'pa', 'in', 'no', 'no', 'no', '2021-09-24 07:01:43', NULL),
(47, 'Persian', 'fa', 'ir', 'no', 'no', 'no', '2021-09-24 07:01:49', NULL),
(48, 'Portuguese', 'pt', 'pt', 'no', 'no', 'no', '2021-09-24 07:01:52', NULL),
(49, 'Romanian', 'ro', 'ro', 'no', 'no', 'no', '2021-09-24 07:01:56', NULL),
(50, 'Russian', 'ru', 'ru', 'no', 'no', 'no', '2021-09-24 07:01:59', NULL),
(51, 'Cebuano', 'ceb', 'ph', 'no', 'no', 'no', '2021-09-24 07:02:02', NULL),
(52, 'Sinhala', 'si', 'si', 'no', 'no', 'no', '2021-09-24 07:02:04', NULL),
(53, 'Slovakian', 'sk', 'sk', 'no', 'no', 'no', '2021-09-24 07:02:07', NULL),
(54, 'Slovenian', 'sl', 'sl', 'no', 'no', 'no', '2021-09-24 07:02:10', NULL),
(55, 'Swahili', 'sw', 'ke', 'no', 'no', 'no', '2021-09-24 07:02:12', NULL),
(56, 'Sundanese', 'su', 'sd', 'no', 'no', 'no', '2021-09-24 07:02:15', NULL),
(57, 'Thai', 'th', 'th', 'no', 'no', 'no', '2021-09-24 07:02:18', NULL),
(58, 'Tagalog', 'tl', 'tl', 'no', 'no', 'no', '2021-09-24 07:02:21', NULL),
(59, 'Tamil', 'ta', 'in', 'no', 'no', 'no', '2021-09-24 07:02:23', NULL),
(60, 'Telugu', 'te', 'in', 'no', 'no', 'no', '2021-09-24 07:02:26', NULL),
(61, 'Turkish', 'tr', 'tr', 'no', 'no', 'no', '2021-09-24 07:02:29', NULL),
(62, 'Uzbek', 'uz', 'uz', 'no', 'no', 'no', '2021-09-24 07:02:31', NULL),
(63, 'Urdu', 'ur', 'pk', 'no', 'no', 'no', '2021-09-24 07:02:34', NULL),
(64, 'Finnish', 'fi', 'fi', 'no', 'no', 'no', '2021-09-24 07:02:37', NULL),
(65, 'French', 'fr', 'fr', 'no', 'no', 'no', '2021-09-24 07:02:39', NULL),
(66, 'Hindi', 'hi', 'in', 'no', 'no', 'no', '2021-09-24 07:02:41', NULL),
(67, 'Czech', 'cs', 'cz', 'no', 'no', 'no', '2021-09-24 07:02:44', NULL),
(68, 'Swedish', 'sv', 'sv', 'no', 'no', 'no', '2021-09-24 07:02:46', NULL),
(69, 'Scottish', 'gd', 'gd', 'no', 'no', 'no', '2021-09-24 07:02:49', NULL),
(70, 'Estonian', 'et', 'et', 'no', 'no', 'no', '2021-09-24 07:02:52', NULL),
(71, 'Esperanto', 'eo', 'br', 'no', 'no', 'no', '2021-09-24 07:02:55', NULL),
(72, 'Javanese', 'jv', 'id', 'no', 'no', 'no', '2021-09-24 07:02:58', NULL),
(73, 'Japanese', 'ja', 'jp', 'no', 'no', 'no', '2021-09-24 07:03:01', NULL),
(74, 'Polish', 'pl', 'pl', 'no', 'no', 'no', '2021-09-28 06:39:06', NULL),
(75, 'Croatia', 'hr', 'hr', 'no', 'no', 'no', '2021-10-25 07:56:41', NULL),
(76, 'Kurdish', 'ku', 'iq', 'no', 'no', 'no', '2021-10-25 07:56:44', NULL),
(77, 'Lao', 'lo', 'la', 'no', 'no', 'no', '2021-10-25 07:56:47', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `leave_types`
--

CREATE TABLE `leave_types` (
  `id` int NOT NULL,
  `type` varchar(200) NOT NULL,
  `is_active` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int NOT NULL,
  `message` text,
  `record_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `ip_address` varchar(50) NOT NULL,
  `platform` varchar(50) NOT NULL,
  `agent` varchar(50) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `medication_report`
--

CREATE TABLE `medication_report` (
  `id` int NOT NULL,
  `medicine_dosage_id` int DEFAULT NULL,
  `pharmacy_id` int DEFAULT NULL,
  `opd_details_id` int DEFAULT NULL,
  `ipd_id` int DEFAULT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `remark` text,
  `generated_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `medicine_bad_stock`
--

CREATE TABLE `medicine_bad_stock` (
  `id` int NOT NULL,
  `medicine_batch_details_id` int DEFAULT NULL,
  `pharmacy_id` int DEFAULT NULL,
  `outward_date` date NOT NULL,
  `expiry_date` date NOT NULL,
  `batch_no` varchar(100) NOT NULL,
  `quantity` varchar(20) NOT NULL,
  `note` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `medicine_batch_details`
--

CREATE TABLE `medicine_batch_details` (
  `id` int NOT NULL,
  `supplier_bill_basic_id` int DEFAULT NULL,
  `pharmacy_id` int DEFAULT NULL,
  `inward_date` datetime NOT NULL,
  `expiry` date NOT NULL,
  `batch_no` varchar(100) NOT NULL,
  `packing_qty` varchar(100) NOT NULL,
  `purchase_rate_packing` varchar(100) NOT NULL,
  `quantity` varchar(200) NOT NULL,
  `mrp` float(10,2) DEFAULT '0.00',
  `purchase_price` float(10,2) DEFAULT '0.00',
  `tax` float(10,2) DEFAULT '0.00',
  `sale_rate` float(10,2) DEFAULT '0.00',
  `batch_amount` float(10,2) DEFAULT '0.00',
  `amount` float(10,2) DEFAULT '0.00',
  `available_quantity` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `medicine_category`
--

CREATE TABLE `medicine_category` (
  `id` int NOT NULL,
  `medicine_category` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `medicine_dosage`
--

CREATE TABLE `medicine_dosage` (
  `id` int NOT NULL,
  `medicine_category_id` int DEFAULT NULL,
  `dosage` varchar(100) NOT NULL,
  `units_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `medicine_group`
--

CREATE TABLE `medicine_group` (
  `id` int NOT NULL,
  `group_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `medicine_supplier`
--

CREATE TABLE `medicine_supplier` (
  `id` int NOT NULL,
  `supplier` varchar(200) NOT NULL,
  `contact` varchar(200) NOT NULL,
  `supplier_person` varchar(200) NOT NULL,
  `supplier_person_contact` varchar(200) NOT NULL,
  `supplier_drug_licence` varchar(200) NOT NULL,
  `address` varchar(200) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `template_id` varchar(100) NOT NULL,
  `message` text,
  `send_mail` varchar(10) DEFAULT '0',
  `send_sms` varchar(10) DEFAULT '0',
  `is_group` varchar(10) DEFAULT '0',
  `is_individual` varchar(10) DEFAULT '0',
  `file` varchar(200) NOT NULL,
  `group_list` text,
  `user_list` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `version` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `notification_roles`
--

CREATE TABLE `notification_roles` (
  `id` int NOT NULL,
  `send_notification_id` int DEFAULT NULL,
  `role_id` int DEFAULT NULL,
  `is_active` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `notification_setting`
--

CREATE TABLE `notification_setting` (
  `id` int NOT NULL,
  `type` varchar(100) DEFAULT NULL,
  `is_mail` int DEFAULT '0',
  `is_sms` int DEFAULT '0',
  `is_mobileapp` int NOT NULL,
  `is_notification` int NOT NULL,
  `display_notification` int NOT NULL,
  `display_sms` int NOT NULL,
  `template` longtext,
  `template_id` varchar(100) NOT NULL,
  `subject` text,
  `variables` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `notification_setting`
--

INSERT INTO `notification_setting` (`id`, `type`, `is_mail`, `is_sms`, `is_mobileapp`, `is_notification`, `display_notification`, `display_sms`, `template`, `template_id`, `subject`, `variables`, `created_at`) VALUES
(1, 'opd_patient_registration', 1, 0, 1, 1, 1, 1, 'Dear {{patient_name}} your OPD Registration at Smart Hospital is successful on date {{appointment_date}} with Patient Id {{patient_id}} and OPD No {{opdno}}', '', 'OPD Patient', '{{patient_name}} {{appointment_date}} {{patient_id}} {{opdno}}', '2021-10-22 05:57:53'),
(2, 'ipd_patient_registration', 1, 0, 0, 0, 1, 1, 'Dear {{patient_name}} your IPD Registration at Smart Hospital is successful  with Patient Id {{patient_id}} and IPD No {{ipd_no}}', '', 'IPD Patient Registration', '{{patient_name}} {{patient_id}} {{ipd_no}}', '2021-10-22 05:59:33'),
(3, 'ipd_patient_discharged', 1, 0, 0, 0, 1, 1, 'IPD Patient {{patient_name}} is now discharged from Smart Hospital\nTotal bill amount is {{currency_symbol}}{{total_amount}}\nTotal paid amount is {{currency_symbol}}{{paid_amount}}\nTotal balance bill amount is {{currency_symbol}}{{balance_amount}}', '', 'IPD Patient Discharge', '{{patient_name}} {{currency_symbol}} {{total_amount}} {{paid_amount}} {balance_amount}}', '2021-10-25 02:32:35'),
(5, 'login_credential', 1, 0, 0, 0, 0, 1, 'Hello {{display_name}} your Smart Hospital login details are Url: {{url}} Username: {{username}} Password: {{password}}', '', 'Smart Hospital Login Credential', '{{display_name}} {{url}} {{username}} {{password}}', '2021-10-22 06:18:21'),
(6, 'appointment_approved', 1, 0, 0, 0, 1, 1, 'Dear {{patient_name}} your appointment with {{staff_name}} {{staff_surname}} is confirmed on {{date}} with appointment no: {{appointment_no}}', '', 'Appointment Approved', '{{patient_name}} {{staff_name}}\n{{staff_surname}}  {{date}} {{appointment_no}}', '2021-10-22 23:56:24'),
(7, 'live_meeting', 1, 0, 0, 0, 0, 1, 'Dear staff, your live meeting {{title}} has been scheduled on {{date}} for the duration of {{duration}} minute.', '', 'Live Meeting', '{{title}} {{date}} {{duration}}', '2021-10-22 23:54:58'),
(8, 'live_consult', 1, 0, 0, 0, 1, 1, 'Dear patient, your live consultation {{title}} has been scheduled on {{date}} for the duration of {{duration}} minute.', '', 'Live Consultation', '{{title}} {{date}} {{duration}}', '2021-10-22 06:28:26'),
(9, 'opd_patient_discharged', 1, 0, 0, 0, 1, 1, 'OPD No {{opd_no}}  {{patient_name}} is now discharged from Smart Hospital.\r\n\r\nTotal bill amount was {{currency_symbol}}  {{total_amount}} \r\nTotal paid amount was {{currency_symbol}}{{paid_amount}}  \r\nTotal balance amount is {{currency_symbol}}{{balance_amount}}', '', 'OPD Patient Discharged', '{{patient_name}} {{mobileno}} {{email}} {{dob}} {{gender}} {{patient_unique_id}} {{opd_no}}{{currency_symbol}} {{billing_amount}}', '2021-10-22 06:25:06'),
(10, 'forgot_password', 1, 0, 0, 0, 0, 0, 'Dear  {{display_name}}, recently a request was submitted to reset password for your account with email: {{email}}. If you didn\'t make the request, just ignore this email, otherwise you can reset your password using this link <a href=\'{{resetpasslink}}\'>click here to reset your password</a>, if you\'re having trouble clicking the password reset link, copy and paste below URL  into your web browser. {{resetpasslink}} <br> Regards,  <br>\r\nSmart Hospital', '', 'Reset Password Request', '{{display_name}}  {{email}}  {{resetpasslink}', '2021-10-22 06:34:34');

-- --------------------------------------------------------

--
-- Table structure for table `nurse_note`
--

CREATE TABLE `nurse_note` (
  `id` int NOT NULL,
  `date` datetime NOT NULL,
  `ipd_id` int DEFAULT NULL,
  `staff_id` int DEFAULT NULL,
  `note` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `comment` text,
  `created_by` int NOT NULL,
  `updated_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `nurse_notes_comment`
--

CREATE TABLE `nurse_notes_comment` (
  `id` int NOT NULL,
  `nurse_note_id` int DEFAULT NULL,
  `comment_staffid` int DEFAULT NULL,
  `comment_staff` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `obstetric_history`
--

CREATE TABLE `obstetric_history` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `place_of_delivery` varchar(250) NOT NULL,
  `pregnancy_duration` varchar(250) NOT NULL,
  `pregnancy_complications` varchar(250) NOT NULL,
  `birth_weight` varchar(250) NOT NULL,
  `gender` varchar(100) NOT NULL,
  `infant_feeding` varchar(250) NOT NULL,
  `alive_dead` varchar(50) NOT NULL,
  `date` date DEFAULT NULL,
  `death_cause` varchar(250) NOT NULL,
  `previous_medical_history` text NOT NULL,
  `special_instruction` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `opd_details`
--

CREATE TABLE `opd_details` (
  `id` int NOT NULL,
  `case_reference_id` int DEFAULT NULL,
  `patient_id` int DEFAULT NULL,
  `generated_by` int DEFAULT NULL,
  `is_ipd_moved` int NOT NULL DEFAULT '0',
  `discharged` varchar(10) NOT NULL DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `operation`
--

CREATE TABLE `operation` (
  `id` int NOT NULL,
  `operation` varchar(250) NOT NULL,
  `category_id` int DEFAULT NULL,
  `is_active` varchar(10) NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `operation_category`
--

CREATE TABLE `operation_category` (
  `id` int NOT NULL,
  `category` varchar(250) NOT NULL,
  `is_active` varchar(10) NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `operation_theatre`
--

CREATE TABLE `operation_theatre` (
  `id` int NOT NULL,
  `opd_details_id` int DEFAULT NULL,
  `ipd_details_id` int DEFAULT NULL,
  `customer_type` varchar(50) DEFAULT NULL,
  `operation_id` int NOT NULL,
  `date` datetime DEFAULT NULL,
  `operation_type` varchar(100) DEFAULT NULL,
  `consultant_doctor` int DEFAULT NULL,
  `ass_consultant_1` varchar(50) DEFAULT NULL,
  `ass_consultant_2` varchar(50) DEFAULT NULL,
  `anesthetist` varchar(50) DEFAULT NULL,
  `anaethesia_type` varchar(50) DEFAULT NULL,
  `ot_technician` varchar(100) DEFAULT NULL,
  `ot_assistant` varchar(100) DEFAULT NULL,
  `result` text,
  `remark` text,
  `generated_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `organisation`
--

CREATE TABLE `organisation` (
  `id` int NOT NULL,
  `organisation_name` varchar(200) NOT NULL,
  `code` varchar(50) NOT NULL,
  `contact_no` varchar(20) NOT NULL,
  `address` varchar(300) NOT NULL,
  `contact_person_name` varchar(200) NOT NULL,
  `contact_person_phone` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `organisations_charges`
--

CREATE TABLE `organisations_charges` (
  `id` int NOT NULL,
  `org_id` int DEFAULT NULL,
  `charge_id` int DEFAULT NULL,
  `org_charge` float(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `pathology`
--

CREATE TABLE `pathology` (
  `id` int NOT NULL,
  `test_name` varchar(100) DEFAULT NULL,
  `short_name` varchar(100) DEFAULT NULL,
  `test_type` varchar(100) DEFAULT NULL,
  `pathology_category_id` int DEFAULT NULL,
  `unit` varchar(50) NOT NULL,
  `sub_category` varchar(50) NOT NULL,
  `report_days` varchar(50) NOT NULL,
  `method` varchar(50) NOT NULL,
  `charge_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `pathology_billing`
--

CREATE TABLE `pathology_billing` (
  `id` int NOT NULL,
  `case_reference_id` int DEFAULT NULL,
  `ipd_prescription_basic_id` int DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `patient_id` int DEFAULT NULL,
  `doctor_id` int DEFAULT NULL,
  `doctor_name` varchar(100) NOT NULL,
  `total` float(10,2) DEFAULT '0.00',
  `discount_percentage` float(10,2) DEFAULT '0.00',
  `discount` float(10,2) DEFAULT '0.00',
  `tax_percentage` float(10,2) DEFAULT '0.00',
  `tax` float(10,2) DEFAULT '0.00',
  `net_amount` float(10,2) DEFAULT '0.00',
  `transaction_id` int DEFAULT NULL,
  `note` text,
  `organisation_id` int DEFAULT NULL,
  `insurance_validity` date DEFAULT NULL,
  `generated_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `insurance_id` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `pathology_category`
--

CREATE TABLE `pathology_category` (
  `id` int NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `pathology_parameter`
--

CREATE TABLE `pathology_parameter` (
  `id` int NOT NULL,
  `parameter_name` varchar(100) NOT NULL,
  `test_value` varchar(100) NOT NULL,
  `reference_range` varchar(100) NOT NULL,
  `range_from` varchar(500) DEFAULT NULL,
  `range_to` varchar(500) DEFAULT NULL,
  `gender` varchar(100) NOT NULL,
  `unit` int DEFAULT NULL,
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `pathology_parameterdetails`
--

CREATE TABLE `pathology_parameterdetails` (
  `id` int NOT NULL,
  `pathology_id` int DEFAULT NULL,
  `pathology_parameter_id` int DEFAULT NULL,
  `created_id` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `pathology_report`
--

CREATE TABLE `pathology_report` (
  `id` int NOT NULL,
  `pathology_bill_id` int DEFAULT NULL,
  `pathology_id` int DEFAULT NULL,
  `customer_type` varchar(50) DEFAULT NULL,
  `patient_id` int DEFAULT NULL,
  `reporting_date` date DEFAULT NULL,
  `parameter_update` date DEFAULT NULL,
  `tax_percentage` float(10,2) NOT NULL DEFAULT '0.00',
  `apply_charge` float(10,2) NOT NULL,
  `collection_date` date DEFAULT NULL,
  `collection_specialist` int DEFAULT NULL,
  `pathology_center` varchar(250) DEFAULT NULL,
  `approved_by` int DEFAULT NULL,
  `patient_name` varchar(100) DEFAULT NULL,
  `description` text,
  `pathology_report` varchar(255) DEFAULT NULL,
  `report_name` text,
  `pathology_result` text COMMENT 'test result',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `pathology_report_parameterdetails`
--

CREATE TABLE `pathology_report_parameterdetails` (
  `id` int NOT NULL,
  `pathology_report_id` int DEFAULT NULL,
  `pathology_parameterdetail_id` int DEFAULT NULL,
  `pathology_report_value` varchar(200) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int NOT NULL,
  `lang_id` int DEFAULT NULL,
  `patient_name` varchar(100) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `age` int NOT NULL,
  `month` int NOT NULL,
  `day` int NOT NULL,
  `as_of_date` date DEFAULT NULL,
  `image` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `mobileno` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `gender` varchar(100) DEFAULT NULL,
  `marital_status` varchar(100) NOT NULL,
  `blood_group` varchar(200) NOT NULL,
  `blood_bank_product_id` int DEFAULT NULL,
  `address` text,
  `guardian_name` varchar(100) DEFAULT NULL,
  `patient_type` varchar(200) NOT NULL,
  `identification_number` varchar(60) NOT NULL,
  `known_allergies` varchar(200) NOT NULL,
  `note` varchar(200) NOT NULL,
  `is_ipd` varchar(200) NOT NULL,
  `app_key` varchar(200) NOT NULL,
  `organisation_id` int DEFAULT NULL,
  `insurance_id` varchar(250) DEFAULT NULL,
  `insurance_validity` date DEFAULT NULL,
  `is_dead` varchar(255) NOT NULL DEFAULT 'no',
  `is_antenatal` int NOT NULL,
  `is_active` varchar(255) DEFAULT 'no',
  `disable_at` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `patients_vitals`
--

CREATE TABLE `patients_vitals` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `vital_id` int NOT NULL,
  `reference_range` varchar(100) NOT NULL,
  `messure_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `patient_bed_history`
--

CREATE TABLE `patient_bed_history` (
  `id` int NOT NULL,
  `case_reference_id` int DEFAULT NULL,
  `bed_group_id` int DEFAULT NULL,
  `bed_id` int DEFAULT NULL,
  `revert_reason` text,
  `from_date` datetime DEFAULT NULL,
  `to_date` datetime DEFAULT NULL,
  `is_active` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `patient_charges`
--

CREATE TABLE `patient_charges` (
  `id` int NOT NULL,
  `date` datetime DEFAULT NULL,
  `ipd_id` int DEFAULT NULL,
  `opd_id` int DEFAULT NULL,
  `qty` int DEFAULT NULL,
  `charge_id` int DEFAULT NULL,
  `standard_charge` float(10,2) DEFAULT '0.00',
  `tpa_charge` float(10,2) DEFAULT '0.00',
  `discount_percentage` float(10,2) DEFAULT '0.00',
  `tax` float(10,2) DEFAULT '0.00',
  `apply_charge` float(10,2) DEFAULT '0.00',
  `amount` float(10,2) DEFAULT '0.00',
  `note` text,
  `organisation_id` int DEFAULT NULL,
  `insurance_validity` date DEFAULT NULL,
  `insurance_id` varchar(250) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `patient_id_card`
--

CREATE TABLE `patient_id_card` (
  `id` int NOT NULL,
  `title` varchar(100) NOT NULL,
  `hospital_name` varchar(100) NOT NULL,
  `hospital_address` varchar(500) NOT NULL,
  `background` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `logo` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `sign_image` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `header_color` varchar(100) NOT NULL,
  `enable_patient_name` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_guardian_name` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_patient_unique_id` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_address` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_phone` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_dob` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_blood_group` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `status` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_barcode` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `patient_id_card`
--

INSERT INTO `patient_id_card` (`id`, `title`, `hospital_name`, `hospital_address`, `background`, `logo`, `sign_image`, `header_color`, `enable_patient_name`, `enable_guardian_name`, `enable_patient_unique_id`, `enable_address`, `enable_phone`, `enable_dob`, `enable_blood_group`, `status`, `enable_barcode`, `created_at`) VALUES
(1, 'Sample Patient Id Card', 'Royal Hospital', 'Nr Loyala Ashram, A 69, Shahpura Rd, Manisha Market, Sector  Bhopal', 'background.jpg', 'logo.jpg', 'signature.png', '#0796f5', 1, 1, 1, 1, 1, 1, 1, 1, 0, '2021-10-19 07:06:02');

-- --------------------------------------------------------

--
-- Table structure for table `patient_timeline`
--

CREATE TABLE `patient_timeline` (
  `id` int NOT NULL,
  `patient_id` int DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `timeline_date` datetime DEFAULT NULL,
  `description` text,
  `document` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `status` varchar(100) NOT NULL,
  `date` datetime DEFAULT NULL,
  `generated_users_type` varchar(100) NOT NULL,
  `generated_users_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `payment_settings`
--

CREATE TABLE `payment_settings` (
  `id` int NOT NULL,
  `payment_type` varchar(200) NOT NULL,
  `api_username` varchar(200) DEFAULT NULL,
  `api_secret_key` varchar(200) NOT NULL,
  `salt` varchar(200) NOT NULL,
  `api_publishable_key` varchar(200) NOT NULL,
  `paytm_website` varchar(255) NOT NULL,
  `paytm_industrytype` varchar(255) NOT NULL,
  `api_password` varchar(200) DEFAULT NULL,
  `api_signature` varchar(200) DEFAULT NULL,
  `api_email` varchar(200) DEFAULT NULL,
  `paypal_demo` varchar(100) NOT NULL,
  `account_no` varchar(200) NOT NULL,
  `is_active` varchar(255) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `payslip_allowance`
--

CREATE TABLE `payslip_allowance` (
  `id` int NOT NULL,
  `staff_payslip_id` int DEFAULT NULL,
  `staff_id` int DEFAULT NULL,
  `allowance_type` varchar(200) NOT NULL,
  `amount` float NOT NULL,
  `cal_type` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `permission_category`
--

CREATE TABLE `permission_category` (
  `id` int NOT NULL,
  `perm_group_id` int DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `short_code` varchar(100) DEFAULT NULL,
  `enable_view` int DEFAULT '0',
  `enable_add` int DEFAULT '0',
  `enable_edit` int DEFAULT '0',
  `enable_delete` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `permission_category`
--

INSERT INTO `permission_category` (`id`, `perm_group_id`, `name`, `short_code`, `enable_view`, `enable_add`, `enable_edit`, `enable_delete`, `created_at`) VALUES
(9, 3, 'Income', 'income', 1, 1, 1, 1, '2018-06-21 23:23:21'),
(10, 3, 'Income Head', 'income_head', 1, 1, 1, 1, '2018-06-21 23:22:44'),
(12, 4, 'Expense', 'expense', 1, 1, 1, 1, '2018-06-21 23:24:06'),
(13, 4, 'Expense Head', 'expense_head', 1, 1, 1, 1, '2018-06-21 23:23:47'),
(27, 8, 'Content Type', 'content_type', 1, 1, 1, 1, '2024-08-21 09:29:55'),
(31, 10, 'Issue Item', 'issue_item', 1, 1, 0, 1, '2018-12-16 22:55:14'),
(32, 10, 'Item Stock', 'item_stock', 1, 1, 1, 1, '2018-06-21 23:35:17'),
(33, 10, 'Item', 'item', 1, 1, 1, 1, '2018-06-21 23:35:40'),
(34, 10, 'Store', 'store', 1, 1, 1, 1, '2018-06-21 23:36:02'),
(35, 10, 'Supplier', 'supplier', 1, 1, 1, 1, '2018-06-21 23:36:25'),
(43, 13, 'Notice Board', 'notice_board', 1, 1, 1, 1, '2018-06-21 23:41:17'),
(44, 13, 'Email / SMS', 'email_sms', 1, 0, 0, 0, '2018-06-21 23:40:54'),
(48, 14, 'OPD Report', 'opd_report', 1, 0, 0, 0, '2018-12-17 21:59:18'),
(53, 15, 'Languages', 'languages', 1, 1, 0, 0, '2021-09-12 22:56:36'),
(54, 15, 'General Setting', 'general_setting', 1, 0, 1, 0, '2018-07-04 22:08:35'),
(56, 15, 'Notification Setting', 'notification_setting', 1, 0, 1, 0, '2018-07-04 22:08:41'),
(57, 15, 'SMS Setting', 'sms_setting', 1, 0, 1, 0, '2018-07-04 22:08:47'),
(58, 15, 'Email Setting', 'email_setting', 1, 0, 1, 0, '2018-07-04 22:08:51'),
(59, 15, 'Front CMS Setting', 'front_cms_setting', 1, 0, 1, 0, '2018-07-04 22:08:55'),
(60, 15, 'Payment Methods', 'payment_methods', 1, 0, 1, 0, '2018-07-04 22:08:59'),
(61, 16, 'Menus', 'menus', 1, 1, 0, 1, '2018-07-08 16:50:06'),
(62, 16, 'Media Manager', 'media_manager', 1, 1, 0, 1, '2018-07-08 16:50:26'),
(63, 16, 'Banner Images', 'banner_images', 1, 1, 0, 1, '2018-06-21 23:46:02'),
(64, 16, 'Pages', 'pages', 1, 1, 1, 1, '2018-06-21 23:46:21'),
(65, 16, 'Gallery', 'gallery', 1, 1, 1, 1, '2018-06-21 23:47:02'),
(66, 16, 'Event', 'event', 1, 1, 1, 1, '2018-06-21 23:47:20'),
(67, 16, 'News', 'notice', 1, 1, 1, 1, '2018-07-02 21:39:34'),
(80, 17, 'Visitor Book', 'visitor_book', 1, 1, 1, 1, '2018-06-21 23:48:58'),
(81, 17, 'Phone Call Log', 'phone_call_log', 1, 1, 1, 1, '2018-06-21 23:50:57'),
(82, 17, 'Postal Dispatch', 'postal_dispatch', 1, 1, 1, 1, '2018-06-21 23:50:21'),
(83, 17, 'Postal Receive', 'postal_receive', 1, 1, 1, 1, '2018-06-21 23:50:04'),
(84, 17, 'Complain', 'complain', 1, 1, 1, 1, '2018-12-18 22:11:37'),
(85, 17, 'Setup Front Office', 'setup_front_office', 1, 1, 1, 1, '2018-11-14 13:49:58'),
(86, 18, 'Staff', 'staff', 1, 1, 1, 1, '2018-06-21 23:53:31'),
(87, 18, 'Disable Staff', 'disable_staff', 1, 0, 0, 0, '2018-06-21 23:53:12'),
(88, 18, 'Staff Attendance', 'staff_attendance', 1, 1, 1, 0, '2018-06-21 23:53:10'),
(89, 14, 'Staff Attendance Report', 'staff_attendance_report', 1, 0, 0, 0, '2021-09-13 02:12:50'),
(90, 18, 'Staff Payroll', 'staff_payroll', 1, 1, 1, 1, '2024-08-21 09:29:55'),
(91, 14, 'Payroll Report', 'payroll_report', 1, 0, 0, 0, '2021-09-13 02:13:00'),
(102, 21, 'Calendar To Do List', 'calendar_to_do_list', 1, 1, 1, 1, '2018-06-21 23:54:41'),
(104, 10, 'Item Category', 'item_category', 1, 1, 1, 1, '2018-06-21 23:34:33'),
(108, 18, ' Approve Leave Request', 'approve_leave_request', 1, 1, 1, 1, '2018-07-01 23:17:41'),
(109, 18, 'Apply Leave', 'apply_leave', 1, 1, 0, 1, '2020-08-24 14:48:58'),
(110, 18, 'LeaveTypes', 'leave_types', 1, 1, 1, 1, '2021-10-26 11:54:30'),
(111, 18, 'Department', 'department', 1, 1, 1, 1, '2018-06-25 16:57:07'),
(112, 18, 'Designation', 'designation', 1, 1, 1, 1, '2018-06-25 16:57:07'),
(118, 22, 'Staff Role Count Widget', 'staff_role_count_widget', 1, 0, 0, 0, '2018-07-02 20:13:35'),
(126, 15, 'Users', 'users', 1, 0, 0, 0, '2021-09-21 19:43:59'),
(127, 18, 'Can See Other Users Profile', 'can_see_other_users_profile', 1, 0, 0, 0, '2018-07-02 21:42:29'),
(129, 18, 'Staff Timeline', 'staff_timeline', 0, 1, 0, 1, '2018-07-04 21:08:52'),
(130, 15, 'Backup', 'backup', 1, 1, 0, 1, '2018-07-08 17:17:17'),
(131, 15, 'Restore', 'restore', 1, 0, 0, 0, '2018-07-08 17:17:17'),
(132, 23, 'OPD Patient', 'opd_patient', 1, 1, 1, 1, '2018-12-19 22:37:26'),
(134, 23, 'Prescription', 'prescription', 1, 1, 1, 1, '2018-10-10 14:28:26'),
(135, 23, 'Visit', 'visit', 1, 1, 1, 1, '2021-09-16 20:39:58'),
(137, 23, 'OPD Timeline', 'opd_timeline', 1, 1, 1, 1, '2021-02-24 01:02:04'),
(138, 24, 'IPD Patients', 'ipd_patient', 1, 1, 1, 1, '2018-10-10 20:14:55'),
(139, 24, 'Discharged Patients', 'discharged_patients', 1, 1, 1, 1, '2021-02-24 01:27:17'),
(140, 24, 'Consultant Register', 'consultant_register', 1, 1, 1, 1, '2021-02-24 01:37:07'),
(142, 24, 'IPD Timeline', 'ipd_timeline', 1, 1, 1, 1, '2021-02-25 01:30:00'),
(143, 24, 'Charges', 'charges', 1, 1, 1, 1, '2018-10-10 14:28:26'),
(144, 24, 'Payment', 'payment', 1, 1, 1, 1, '2024-08-21 09:29:55'),
(146, 25, 'Medicine', 'medicine', 1, 1, 1, 1, '2018-10-10 14:28:26'),
(148, 25, 'Pharmacy Bill', 'pharmacy_bill', 1, 1, 1, 1, '2021-02-25 01:33:40'),
(149, 26, 'Pathology Test', 'pathology_test', 1, 1, 1, 1, '2021-02-25 01:36:32'),
(152, 27, 'Radiology Test', 'radiology_test', 1, 1, 1, 1, '2021-02-25 01:45:31'),
(153, 27, 'Radiology  Bill', 'radiology_bill', 1, 1, 1, 1, '2021-09-16 18:16:48'),
(155, 22, 'IPD Income Widget', 'ipd_income_widget', 1, 0, 0, 0, '2018-12-19 22:08:05'),
(156, 22, 'OPD Income Widget', 'opd_income_widget', 1, 0, 0, 0, '2018-12-19 22:08:15'),
(157, 22, 'Pharmacy Income Widget', 'pharmacy_income_widget', 1, 0, 0, 0, '2018-12-19 22:08:25'),
(158, 22, 'Pathology Income Widget', 'pathology_income_widget', 1, 0, 0, 0, '2018-12-19 22:08:37'),
(159, 22, 'Radiology Income Widget', 'radiology_income_widget', 1, 0, 0, 0, '2018-12-19 22:08:49'),
(161, 22, 'Blood Bank Income Widget', 'blood_bank_income_widget', 1, 0, 0, 0, '2018-12-19 22:09:13'),
(162, 22, 'Ambulance Income Widget', 'ambulance_income_widget', 1, 0, 0, 0, '2018-12-19 22:09:25'),
(165, 29, 'Ambulance Call', 'ambulance_call', 1, 1, 1, 1, '2018-10-26 16:37:51'),
(166, 29, 'Ambulance', 'ambulance', 1, 1, 1, 1, '2018-10-26 16:37:59'),
(168, 30, 'Blood Issue', 'blood_issue', 1, 1, 1, 1, '2018-10-26 17:20:15'),
(169, 30, 'Blood Donor', 'blood_donor', 1, 1, 1, 1, '2018-10-26 17:20:19'),
(170, 25, 'Medicine Category', 'medicine_category', 1, 1, 1, 1, '2018-10-24 19:10:24'),
(171, 27, 'Radiology Category', 'radiology_category', 1, 1, 1, 1, '2021-02-25 01:52:34'),
(173, 31, 'Organisation', 'organisation', 1, 1, 1, 1, '2018-10-24 19:10:24'),
(175, 26, 'Pathology Category', 'pathology_category', 1, 1, 1, 1, '2018-10-24 19:10:24'),
(176, 32, 'Hospital Charges', 'hospital_charges', 1, 1, 1, 1, '2021-09-12 20:29:30'),
(178, 14, 'IPD Report', 'ipd_report', 1, 0, 0, 0, '2018-12-11 23:09:24'),
(179, 14, 'Pharmacy Bill Report', 'pharmacy_bill_report', 1, 0, 0, 0, '2018-12-11 23:09:24'),
(180, 14, 'Pathology Patient Report', 'pathology_patient_report', 1, 0, 0, 0, '2018-12-11 23:09:24'),
(181, 14, 'Radiology Patient Report', 'radiology_patient_report', 1, 0, 0, 0, '2018-12-11 23:09:24'),
(182, 14, 'OT Report', 'ot_report', 1, 0, 0, 0, '2019-03-07 19:56:54'),
(183, 14, 'Blood Donor Report', 'blood_donor_report', 1, 0, 0, 0, '2019-03-07 19:56:54'),
(184, 14, 'Payroll Month Report', 'payroll_month_report', 1, 0, 0, 0, '2019-03-07 19:57:25'),
(185, 14, 'Payroll Report', 'payroll_report', 1, 0, 0, 0, '2019-03-07 19:57:35'),
(187, 14, 'User Log', 'user_log', 1, 0, 0, 0, '2018-12-11 23:09:24'),
(188, 14, 'Patient Login Credential', 'patient_login_credential', 1, 0, 0, 0, '2018-12-11 23:09:24'),
(189, 14, 'Email / SMS Log', 'email_sms_log', 1, 0, 0, 0, '2018-12-11 23:09:24'),
(190, 22, 'Yearly Income & Expense Chart', 'yearly_income_expense_chart', 1, 0, 0, 0, '2018-12-11 23:22:05'),
(191, 22, 'Monthly Income & Expense Chart', 'monthly_income_expense_chart', 1, 0, 0, 0, '2018-12-11 23:25:14'),
(192, 23, 'OPD Prescription Print Header Footer ', 'opd_prescription_print_header_footer', 1, 0, 0, 0, '2024-08-21 09:29:55'),
(196, 24, 'Bed', 'bed', 1, 1, 1, 1, '2018-12-11 23:46:01'),
(197, 24, 'IPD Prescription Print Header Footer', 'ipd_prescription_print_header_footer', 1, 0, 0, 0, '2024-08-21 09:29:55'),
(198, 24, 'Bed Status', 'bed_status', 1, 0, 0, 0, '2018-12-11 23:39:42'),
(200, 25, 'Medicine Bad Stock', 'medicine_bad_stock', 1, 1, 0, 1, '2018-12-17 14:12:46'),
(201, 25, 'Pharmacy Bill print Header Footer', 'pharmacy_bill_print_header_footer', 1, 0, 0, 0, '2024-08-21 09:29:55'),
(202, 30, 'Blood Stock', 'blood_stock', 1, 1, 0, 1, '2021-09-10 22:49:52'),
(203, 32, 'Charge Category', 'charge_category', 1, 1, 1, 1, '2018-12-12 00:19:38'),
(206, 14, 'TPA Report', 'tpa_report', 1, 0, 0, 0, '2019-03-07 19:49:25'),
(207, 14, 'Ambulance Report', 'ambulance_report', 1, 0, 0, 0, '2019-03-07 19:49:41'),
(208, 14, 'Discharge Patient Report', 'discharge_patient_report', 1, 0, 0, 0, '2019-03-07 19:49:55'),
(209, 14, 'Appointment Report', 'appointment_report', 1, 0, 0, 0, '2019-03-07 19:50:10'),
(210, 14, 'Transaction Report', 'transaction_report', 1, 0, 0, 0, '2019-03-07 19:57:35'),
(211, 14, 'Blood Issue Report', 'blood_issue_report', 1, 0, 0, 0, '2019-03-07 19:57:35'),
(212, 14, 'Income Report', 'income_report', 1, 0, 0, 0, '2019-03-07 19:57:35'),
(213, 14, 'Expense Report', 'expense_report', 1, 0, 0, 0, '2019-03-07 19:57:35'),
(214, 34, 'Birth Record', 'birth_record', 1, 1, 1, 1, '2018-06-21 23:36:02'),
(215, 34, 'Death Record', 'death_record', 1, 1, 1, 1, '2018-06-21 23:36:02'),
(218, 23, 'Move Patient in IPD', 'opd_move_patient_in_ipd', 1, 0, 0, 0, '2021-09-16 21:00:06'),
(219, 23, 'Manual Prescription', 'manual_prescription', 1, 0, 0, 0, '2019-09-22 17:52:06'),
(220, 24, 'Prescription ', 'ipd_prescription', 1, 1, 1, 1, '2019-09-23 13:59:27'),
(221, 23, 'Charges', 'opd_charges', 1, 1, 1, 1, '2019-09-22 17:58:03'),
(222, 23, 'Payment', 'opd_payment', 1, 1, 1, 1, '2024-08-21 09:29:55'),
(224, 25, 'Import Medicine', 'import_medicine', 1, 0, 0, 0, '2019-09-22 18:03:31'),
(225, 25, 'Medicine Purchase', 'medicine_purchase', 1, 1, 0, 1, '2021-10-02 04:59:02'),
(226, 25, 'Medicine Supplier', 'medicine_supplier', 1, 1, 1, 1, '2019-09-22 18:09:36'),
(227, 25, 'Medicine Dosage', 'medicine_dosage', 1, 1, 1, 1, '2019-09-22 18:17:16'),
(236, 36, 'Patient', 'patient', 1, 1, 1, 1, '2021-09-21 21:29:37'),
(237, 36, 'Enabled/Disabled', 'enabled_disabled', 1, 0, 0, 0, '2019-09-22 19:25:35'),
(238, 22, 'Notification Center', 'notification_center', 1, 0, 0, 0, '2019-09-23 16:48:33'),
(239, 36, 'Import', 'patient_import', 1, 0, 0, 0, '2019-10-03 14:20:26'),
(240, 34, 'Birth Print Header Footer', 'birth_print_header_footer', 1, 0, 0, 0, '2024-08-21 09:29:55'),
(242, 34, 'Death Print Header Footer', 'death_print_header_footer', 1, 0, 0, 0, '2024-08-21 09:29:55'),
(243, 26, 'Print Header Footer', 'pathology_print_header_footer', 1, 0, 0, 0, '2024-08-21 09:29:55'),
(244, 27, 'Print Header Footer', 'radiology_print_header_footer', 1, 0, 0, 0, '2024-08-21 09:29:55'),
(246, 30, 'Print Header Footer', 'bloodbank_print_header_footer', 1, 0, 0, 0, '2021-10-07 04:06:58'),
(247, 29, 'Print Header Footer', 'ambulance_print_header_footer', 1, 0, 0, 0, '2024-08-21 09:29:55'),
(248, 24, 'IPD Bill Print Header Footer', 'ipd_bill_print_header_footer', 1, 0, 0, 0, '2024-08-21 09:29:55'),
(249, 18, 'Print Payslip Header Footer', 'print_payslip_header_footer', 1, 0, 0, 0, '2024-08-21 09:29:55'),
(250, 14, 'Income Group Report', 'income_group_report', 1, 0, 0, 0, '2020-08-11 18:52:52'),
(251, 14, 'Expense Group Report', 'expense_group_report', 1, 0, 0, 0, '2019-10-03 17:15:56'),
(253, 14, 'Inventory Stock Report', 'inventory_stock_report', 1, 0, 0, 0, '2019-10-03 18:20:31'),
(254, 14, 'Inventory Item Report', 'add_item_report', 1, 0, 0, 0, '2019-10-03 18:23:22'),
(255, 14, 'Inventory Issue Report', 'issue_inventory_report', 1, 0, 0, 0, '2019-10-03 18:24:40'),
(256, 14, 'Expiry Medicine Report', 'expiry_medicine_report', 1, 0, 0, 0, '2019-10-03 19:00:11'),
(257, 26, 'Pathology Bill', 'pathology_bill', 1, 1, 1, 1, '2021-02-25 01:58:10'),
(258, 14, 'Birth Report', 'birth_report', 1, 0, 0, 0, '2019-10-13 16:12:35'),
(259, 14, 'Death Report', 'death_report', 1, 0, 0, 0, '2019-10-13 16:13:56'),
(260, 26, 'Pathology Unit', 'pathology_unit', 1, 1, 1, 1, '2020-07-21 14:13:49'),
(261, 27, 'Radiology Unit', 'radiology_unit', 1, 1, 1, 1, '2020-07-21 14:14:47'),
(262, 27, 'Radiology Parameter', 'radiology_parameter', 1, 1, 1, 1, '2020-07-21 14:20:28'),
(263, 26, 'Pathology Parameter', 'pathology_parameter', 1, 1, 1, 1, '2020-07-21 14:20:28'),
(264, 32, 'Charge Type', 'charge_type', 1, 1, 1, 1, '2024-08-21 09:29:55'),
(265, 14, 'OPD Balance Report', 'opd_balance_report', 1, 0, 0, 0, '2020-07-27 15:03:34'),
(266, 14, 'IPD Balance Report', 'ipd_balance_report', 1, 0, 0, 0, '2020-07-27 15:03:34'),
(267, 15, 'Symptoms Type', 'symptoms_type', 1, 1, 1, 1, '2021-09-13 21:36:22'),
(269, 37, 'Live Consultation', 'live_consultation', 1, 1, 0, 1, '2020-08-12 19:19:27'),
(270, 37, 'Live Meeting', 'live_meeting', 1, 1, 0, 1, '2020-08-12 19:19:27'),
(271, 14, 'Live Consultation Report', 'live_consultation_report', 1, 0, 0, 0, '2021-09-13 02:11:19'),
(272, 14, 'Live Meeting Report', 'live_meeting_report', 1, 0, 0, 0, '2021-09-13 02:11:14'),
(273, 37, 'Setting', 'setting', 1, 0, 1, 0, '2020-08-12 20:03:28'),
(274, 15, 'Language Switcher', 'language_switcher', 1, 0, 0, 0, '2020-08-20 17:48:53'),
(279, 15, 'Symptoms Head', 'symptoms_head', 1, 1, 1, 1, '2021-09-13 21:36:27'),
(280, 18, 'Specialist', 'specialist', 1, 1, 1, 1, '2019-10-03 10:01:33'),
(281, 22, 'General Income Widget', 'general_income_widget', 1, 0, 0, 0, '2018-12-19 16:38:05'),
(282, 22, 'Expenses Widget', 'expenses_widget', 1, 0, 0, 0, '2018-12-19 16:38:05'),
(283, 38, 'Referral Category', 'referral_category', 1, 1, 1, 1, '2021-06-11 02:54:41'),
(284, 38, 'Referral Commission', 'referral_commission', 1, 1, 1, 1, '2021-06-11 02:54:41'),
(285, 38, 'Referral Person', 'referral_person', 1, 1, 1, 1, '2021-06-11 02:55:21'),
(286, 38, 'Referral Payment', 'referral_payment', 1, 1, 1, 1, '2021-06-11 02:55:21'),
(287, 15, 'Prefix Setting', 'prefix_setting', 1, 0, 1, 0, '2021-06-11 20:46:10'),
(288, 15, 'Captcha Setting', 'captcha_setting', 1, 0, 1, 0, '2021-06-11 21:43:53'),
(289, 32, 'Tax Category', 'tax_category', 1, 1, 1, 1, '2021-06-11 22:16:39'),
(290, 32, 'Unit Type', 'unit_type', 1, 1, 1, 1, '2021-06-11 22:16:39'),
(291, 25, 'Dosage Interval', 'dosage_interval', 1, 1, 1, 1, '2021-06-12 00:15:37'),
(292, 25, 'Dosage Duration', 'dosage_duration', 1, 1, 1, 1, '2021-06-12 00:15:37'),
(293, 30, 'Blood Bank Product', 'blood_bank_product', 1, 1, 1, 1, '2021-06-12 00:51:23'),
(294, 39, 'Slot', 'online_appointment_slot', 1, 1, 1, 1, '2021-09-14 01:04:31'),
(295, 39, 'Doctor Shift', 'online_appointment_doctor_shift', 1, 0, 1, 0, '2021-06-12 01:43:48'),
(296, 39, 'Shift', 'online_appointment_shift', 1, 1, 1, 1, '2021-06-12 01:24:25'),
(297, 39, 'Doctor Wise Appointment', 'doctor_wise_appointment', 1, 0, 0, 0, '2021-10-07 01:45:39'),
(298, 39, 'Patient Queue', 'patient_queue', 1, 0, 0, 0, '2021-10-07 01:45:42'),
(299, 23, 'OPD Medication', 'opd_medication', 1, 1, 1, 1, '2021-06-14 20:00:12'),
(300, 24, 'IPD Medication', 'ipd_medication', 1, 1, 1, 1, '2021-06-14 20:00:12'),
(301, 24, 'Bed History', 'bed_history', 1, 0, 0, 0, '2021-06-14 20:00:12'),
(302, 30, 'Blood Bank Components', 'blood_bank_components', 1, 1, 0, 1, '2021-06-15 00:46:48'),
(303, 23, 'Operation Theatre', 'opd_operation_theatre', 1, 1, 1, 1, '2021-09-07 22:49:13'),
(304, 23, 'Lab Investigation', 'opd_lab_investigation', 1, 0, 0, 0, '2021-09-06 19:36:10'),
(305, 23, 'Patient Discharge', 'opd_patient_discharge', 1, 0, 1, 0, '2021-09-06 19:39:16'),
(306, 23, 'Patient Discharge Revert', 'opd_patient_discharge_revert', 1, 0, 0, 0, '2021-09-06 19:39:38'),
(307, 23, 'Treatment History', 'opd_treatment_history', 1, 0, 0, 0, '2021-09-06 19:49:05'),
(308, 24, 'Lab Investigation', 'ipd_lab_investigation', 1, 0, 0, 0, '2021-09-06 20:45:59'),
(309, 24, 'Patient Discharge', 'ipd_patient_discharge', 1, 0, 1, 0, '2021-09-06 22:08:20'),
(310, 24, 'Patient Discharge Revert', 'ipd_patient_discharge_revert', 1, 0, 0, 0, '2021-09-06 22:14:54'),
(311, 30, 'Issue Component', 'issue_component', 1, 1, 1, 1, '2021-09-06 22:21:53'),
(312, 26, '	Add/Edit Collection Person', 'pathology_add_edit_collection_person', 1, 0, 1, 0, '2021-09-16 20:06:13'),
(313, 25, 'Partial Payment', 'pharmacy_partial_payment', 1, 1, 0, 1, '2021-09-07 01:10:15'),
(314, 26, 'Partial Payment', 'pathology_partial_payment', 1, 1, 0, 1, '2021-09-07 02:34:33'),
(315, 27, 'Partial Payment', 'radiology_partial_payment', 1, 1, 0, 1, '2021-09-07 02:38:15'),
(316, 28, 'Partial Payment', 'radiology_partial_payment', 1, 1, 0, 1, '2021-09-07 02:39:02'),
(317, 30, 'Partial Payment', 'blood_bank_partial_payment', 1, 1, 0, 1, '2021-09-07 02:47:22'),
(318, 29, 'Partial Payment', 'ambulance_partial_payment', 1, 1, 0, 1, '2021-09-07 02:48:10'),
(319, 23, 'Checkup', 'checkup', 1, 1, 1, 1, '2021-09-16 20:40:33'),
(320, 23, 'Print Bill', 'opd_print_bill', 1, 0, 0, 0, '2021-09-07 23:09:27'),
(321, 23, 'Live Consult', 'opd_live_consult', 1, 0, 0, 0, '2021-09-08 00:53:31'),
(322, 24, 'Nurse Note', 'nurse_note', 1, 1, 1, 1, '2021-09-08 01:20:07'),
(323, 24, 'Bed Type', 'bed_type', 1, 1, 1, 1, '2021-09-08 20:06:39'),
(324, 24, 'Bed Group', 'bed_group', 1, 1, 1, 1, '2021-09-08 20:07:08'),
(325, 24, 'Floor', 'floor', 1, 1, 1, 1, '2021-09-08 20:08:35'),
(326, 24, 'Operation Theatre', 'ipd_operation_theatre', 1, 1, 1, 1, '2021-09-08 22:38:14'),
(327, 24, 'Live Consult', 'ipd_live_consultation', 1, 0, 0, 0, '2021-09-08 23:05:26'),
(329, 24, 'Treatment History', 'ipd_treatment_history', 1, 0, 0, 0, '2021-09-06 20:45:59'),
(330, 41, 'OPD Billing', 'opd_billing', 1, 0, 0, 0, '2021-09-09 00:33:14'),
(331, 41, 'OPD Billing Payment', 'opd_billing_payment', 1, 1, 0, 0, '2021-09-09 01:10:36'),
(332, 41, 'IPD Billing', 'ipd_billing', 1, 0, 0, 0, '2021-09-09 00:52:26'),
(333, 41, 'IPD Billing Payment', 'ipd_billing_payment', 1, 1, 0, 0, '2021-09-09 00:53:03'),
(334, 41, 'Pharmacy Billing', 'pharmacy_billing', 1, 0, 0, 0, '2021-09-09 00:53:03'),
(335, 41, 'Pharmacy Billing Payment', 'pharmacy_billing_payment', 1, 1, 0, 0, '2021-09-09 00:53:03'),
(336, 41, 'Pathology Billing', 'pathology_billing', 1, 0, 0, 0, '2021-09-09 00:53:03'),
(337, 41, 'Pathology Billing Payment', 'pathology_billing_payment', 1, 1, 0, 0, '2021-09-09 00:53:03'),
(338, 41, 'Radiology Billing', 'radiology_billing', 1, 0, 0, 0, '2021-09-09 00:53:03'),
(339, 41, 'Radiology Billing Payment', 'radiology_billing_payment', 1, 1, 0, 0, '2021-09-09 00:53:03'),
(340, 41, 'Blood Bank Billing', 'blood_bank_billing', 1, 0, 0, 0, '2021-09-09 00:53:03'),
(341, 41, 'Blood Bank Billing Payment', 'blood_bank_billing_payment', 1, 1, 0, 0, '2021-09-09 00:53:03'),
(342, 41, 'Ambulance Billing', 'ambulance_billing', 1, 0, 0, 0, '2021-09-09 00:53:03'),
(343, 41, 'Ambulance Billing Payment', 'ambulance_billing_payment', 1, 1, 0, 0, '2021-09-09 00:53:03'),
(344, 41, 'Generate Bill', 'generate_bill', 1, 0, 0, 0, '2021-09-09 20:36:09'),
(345, 41, 'Generate Discharge Card', 'generate_discharge_card', 1, 0, 0, 0, '2021-09-09 00:53:03'),
(346, 40, 'Online Appointment', 'online_appointment', 1, 0, 0, 0, '2021-09-09 02:15:17'),
(347, 31, 'TPA Charges ', 'tpa_charges', 1, 0, 1, 1, '2018-10-24 19:10:24'),
(348, 15, 'System Notification Setting', 'system_notification_setting', 1, 0, 1, 0, '2018-07-04 22:08:41'),
(349, 14, 'All Transaction Report', 'all_transaction_report', 1, 0, 0, 0, '2021-09-13 02:29:20'),
(350, 14, 'Patient Visit Report', 'patient_visit_report', 1, 0, 0, 0, '2019-10-03 18:23:22'),
(351, 14, 'Patient Bill Report', 'patient_bill_report', 1, 0, 0, 0, '2019-10-03 17:15:56'),
(352, 14, 'Referral Report', 'referral_report', 1, 0, 0, 0, '2019-10-03 17:15:56'),
(353, 27, 'Add/Edit Collection Person', 'radiology_add_edit_collection_person', 1, 0, 1, 0, '2021-09-16 20:06:41'),
(354, 27, 'Add/Edit  Report', 'radiology_add_edit_report', 1, 0, 1, 0, '2021-09-16 20:06:50'),
(355, 26, 'Add/Edit Report', 'pathology_add_edit_report', 1, 0, 1, 0, '2021-09-16 20:06:24'),
(362, 42, 'Generate Certificate', 'generate_certificate', 1, 0, 0, 0, '2021-09-20 16:48:25'),
(363, 42, 'Certificate', 'certificate', 1, 1, 1, 1, '2021-09-20 16:48:25'),
(364, 42, 'Generate Staff ID Card', 'generate_staff_id_card', 1, 0, 0, 0, '2021-09-20 16:56:38'),
(365, 42, 'Staff ID Card', 'staff_id_card', 1, 1, 1, 1, '2021-09-20 16:56:09'),
(366, 42, 'Generate Patient ID Card', 'generate_patient_id_card', 1, 0, 0, 0, '2021-09-20 23:13:54'),
(367, 42, 'Patient ID Card', 'patient_id_card', 1, 1, 1, 1, '2021-09-20 16:54:38'),
(369, 14, 'Component Issue Report', 'component_issue_report', 1, 0, 0, 0, '2019-03-07 19:57:35'),
(370, 14, 'Audit Trail Report', 'audit_trail_report', 1, 0, 0, 0, '2021-09-28 01:08:22'),
(371, 43, 'Chat', 'chat', 1, 0, 0, 0, '2021-10-07 05:05:15'),
(372, 15, 'Custom Fields', 'custom_fields', 1, 0, 0, 0, '2021-10-29 07:41:26'),
(373, 14, 'Daily Transaction Report', 'daily_transaction_report', 1, 0, 0, 0, '2021-10-29 07:42:08'),
(374, 15, 'Operation', 'operation', 1, 1, 1, 1, '2021-10-29 07:45:14'),
(375, 15, 'Operation Category', 'operation_category', 1, 1, 1, 1, '2021-10-29 07:45:14'),
(386, 39, 'Appointment', 'appointment', 1, 1, 0, 1, '2021-12-24 09:36:15'),
(387, 39, 'Reschedule', 'reschedule', 1, 0, 0, 0, '2021-12-24 09:36:15'),
(388, 15, 'Finding', 'finding', 1, 1, 1, 1, '2021-10-29 07:45:14'),
(389, 15, 'Finding Category', 'finding_category', 1, 1, 1, 1, '2021-10-29 07:45:14'),
(390, 41, 'Appointment Billing', 'appointment_billing', 1, 0, 0, 0, '2021-09-09 00:53:03'),
(391, 15, 'Vital', 'vital', 1, 1, 1, 1, '2021-10-29 07:45:14'),
(392, 23, 'OPD Vitals', 'opd_vitals', 1, 1, 1, 1, '2018-12-19 22:37:26'),
(393, 24, 'IPD Vitals', 'ipd_vitals', 1, 1, 1, 1, '2021-02-25 01:30:00'),
(394, 24, 'Previous Obstetric History', 'ipd_previous_obstetric_history', 1, 1, 1, 1, '2021-02-25 01:30:00'),
(395, 24, 'Postnatal History', 'ipd_postnatal_history', 1, 1, 1, 1, '2021-02-25 01:30:00'),
(396, 24, 'Antenatal', 'ipd_antenatal', 1, 1, 1, 1, '2021-02-25 01:30:00'),
(397, 39, 'Print Appointment Header Footer', 'print_appointment_header_footer', 1, 0, 0, 0, '2024-02-29 09:05:48'),
(398, 23, 'Antenatal', 'opd_antenatal', 1, 1, 1, 1, '2024-03-11 11:24:19'),
(399, 41, 'Payment Receipt Header Footer', 'payment_receipt_header_footer', 1, 0, 0, 0, '2024-02-29 10:44:39'),
(400, 13, 'Send Credential', 'send_credential', 1, 0, 0, 0, '2024-02-29 10:44:43'),
(401, 24, 'IPD Antenatal Finding Print Header Footer', 'ipd_antenatal_finding_print_header_footer', 1, 0, 0, 0, '2024-02-29 10:44:47'),
(402, 23, 'OPD Antenatal Finding Print Header Footer ', 'opd_antenatal_finding_print_header_footer', 1, 0, 0, 0, '2024-02-29 10:44:52'),
(403, 24, 'Discharge Summary Print Header Footer', 'discharge_summary_print_header_footer', 1, 0, 0, 0, '2024-02-29 10:44:57'),
(404, 24, 'IPD Obstetric History Print Header Footer', 'ipd_obstetric_history_print_header_footer', 1, 0, 0, 0, '2024-02-29 10:44:47'),
(405, 39, 'Appointment Priority', 'appointment_priority', 1, 1, 1, 1, '2021-06-12 01:24:25'),
(406, 25, 'Unit', 'medicine_unit', 1, 1, 1, 1, '2018-10-24 19:10:24'),
(407, 25, 'Company', 'medicine_company', 1, 1, 1, 1, '2018-10-24 19:10:24'),
(408, 25, 'Medicine Group', 'medicine_group', 1, 1, 1, 1, '2018-10-24 19:10:24'),
(409, 8, 'Content Share List', 'content_share_list', 1, 0, 0, 1, '2024-04-20 06:05:46'),
(410, 8, 'Upload/Share Content', 'upload_share_content', 1, 1, 0, 1, '2024-04-20 06:41:55'),
(411, 8, 'Generate URL', 'generate_url', 1, 0, 0, 0, '2024-04-20 06:41:55'),
(412, 8, 'Share', 'share_content', 1, 0, 0, 0, '2024-04-20 06:41:55'),
(413, 23, 'OPD Bill Print Header Footer', 'opd_bill_print_header_footer', 1, 0, 1, 0, '2024-05-01 12:39:20'),
(414, 15, 'Attendance Setting', 'attendance_setting', 1, 0, 1, 0, '2018-07-04 22:08:35'),
(415, 44, 'Duty Roster', 'duty_roster', 1, 0, 0, 0, '2018-07-04 22:08:35'),
(416, 44, 'Shift', 'roster_shift', 1, 1, 1, 1, '2024-08-08 09:03:23'),
(417, 44, 'Roster List', 'roster_list', 1, 1, 1, 1, '2018-07-04 22:08:35'),
(418, 44, 'Roster Assign', 'roster_assign', 1, 1, 1, 1, '2018-07-04 22:08:35'),
(419, 45, 'Annual Calendar', 'annual_calendar', 1, 1, 1, 1, '2018-07-04 22:08:35');

-- --------------------------------------------------------

--
-- Table structure for table `permission_group`
--

CREATE TABLE `permission_group` (
  `id` int NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `short_code` varchar(100) NOT NULL,
  `is_active` int DEFAULT '0',
  `system` int NOT NULL,
  `sort_order` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `permission_group`
--

INSERT INTO `permission_group` (`id`, `name`, `short_code`, `is_active`, `system`, `sort_order`, `created_at`) VALUES
(3, 'Income', 'income', 1, 0, 15.00, '2021-10-22 00:07:50'),
(4, 'Expense', 'expense', 1, 0, 16.00, '2021-10-22 00:07:55'),
(8, 'Download Center', 'download_center', 1, 0, 19.00, '2021-10-22 00:13:38'),
(10, 'Inventory', 'inventory', 1, 0, 18.00, '2021-10-22 00:13:22'),
(13, 'Messaging', 'communicate', 1, 0, 17.00, '2021-10-22 00:13:08'),
(14, 'Reports', 'reports', 1, 1, 23.00, '2021-10-22 00:14:35'),
(15, 'System Settings', 'system_settings', 1, 1, 24.00, '2021-10-22 00:16:02'),
(16, 'Front CMS', 'front_cms', 1, 0, 21.00, '2021-10-22 00:14:07'),
(17, 'Front Office', 'front_office', 1, 0, 10.00, '2021-10-22 00:05:56'),
(18, 'Human Resource', 'human_resource', 1, 1, 12.00, '2021-10-22 00:06:27'),
(21, 'Calendar To Do List', 'calendar_to_do_list', 1, 0, 28.00, '2021-10-22 00:22:27'),
(22, 'Dashboard and Widgets', 'dashboard_and_widgets', 1, 1, 0.01, '2021-10-22 00:18:00'),
(23, 'OPD', 'opd', 1, 0, 3.00, '2021-10-22 00:04:29'),
(24, 'IPD', 'ipd', 1, 0, 4.00, '2021-10-22 00:04:38'),
(25, 'Pharmacy', 'pharmacy', 1, 0, 5.00, '2021-10-22 00:04:47'),
(26, 'Pathology', 'pathology', 1, 0, 6.00, '2021-10-22 00:04:59'),
(27, 'Radiology', 'radiology', 1, 0, 7.00, '2021-10-22 00:05:09'),
(29, 'Ambulance', 'ambulance', 1, 0, 9.00, '2021-10-22 00:05:31'),
(30, 'Blood Bank', 'blood_bank', 1, 0, 8.00, '2021-10-22 00:05:21'),
(31, 'TPA Management', 'tpa_management', 1, 0, 14.00, '2021-10-22 00:06:58'),
(32, 'Hospital Charges', 'hospital_charges', 1, 1, 26.00, '2021-10-22 00:19:04'),
(34, 'Birth Death Record', 'birth_death_report', 1, 0, 11.00, '2021-10-22 00:06:10'),
(36, 'Patient', 'patient', 1, 0, 25.00, '2021-10-22 00:18:46'),
(37, 'Live Consultation', 'live_consultation', 1, 0, 22.00, '2021-10-22 00:14:21'),
(38, 'Referral', 'referral', 1, 0, 13.00, '2021-10-22 00:06:48'),
(39, 'Appointment', 'appointment', 1, 0, 2.00, '2021-10-22 00:04:15'),
(41, 'Billing', 'bill', 1, 0, 1.00, '2024-08-21 09:29:55'),
(42, 'Certificate', 'certificate', 1, 0, 20.00, '2021-10-04 03:36:58'),
(43, 'Chat', 'chat', 1, 0, 27.00, '2021-10-22 00:22:19'),
(44, 'Duty Roster', 'duty_roster', 1, 0, 12.10, '2024-08-09 10:37:56'),
(45, 'Annual Calendar', 'annual_calendar', 1, 0, 12.20, '2024-08-17 06:32:13');

-- --------------------------------------------------------

--
-- Table structure for table `permission_patient`
--

CREATE TABLE `permission_patient` (
  `id` int NOT NULL,
  `permission_group_short_code` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `short_code` varchar(100) NOT NULL,
  `is_active` int DEFAULT NULL,
  `system` int NOT NULL,
  `sort_order` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `permission_patient`
--

INSERT INTO `permission_patient` (`id`, `permission_group_short_code`, `name`, `short_code`, `is_active`, `system`, `sort_order`, `created_at`) VALUES
(1, 'appointment', 'My Appointments', 'my_appointments', 1, 0, 1.00, '2021-09-27 13:17:05'),
(2, 'opd', 'OPD', 'opd', 1, 0, 2.00, '2021-09-27 13:17:21'),
(3, 'ipd', 'IPD', 'ipd', 1, 0, 3.00, '2021-09-25 09:33:07'),
(4, 'pharmacy', 'Pharmacy', 'pharmacy', 1, 0, 4.00, '2021-09-25 06:03:29'),
(5, 'pathology', 'Pathology', 'pathology', 1, 0, 5.00, '2021-09-27 13:15:45'),
(6, 'radiology', 'Radiology', 'radiology', 1, 0, 6.00, '2021-09-27 13:15:47'),
(7, 'ambulance', 'Ambulance', 'ambulance', 1, 0, 7.00, '2021-09-27 13:15:50'),
(8, 'blood_bank', 'Blood Bank', 'blood_bank', 1, 0, 8.00, '2021-09-24 07:40:59'),
(9, 'live_consultation', 'Live Consultation', 'live_consultation', 1, 0, 9.00, '2021-09-27 13:16:49'),
(10, 'calendar_to_do_list', 'Calendar To Do List', 'calendar_to_do_list', 1, 0, 11.00, '2021-10-04 09:07:25'),
(11, 'chat', 'Chat', 'chat', 1, 0, 11.00, '2021-10-04 07:34:59'),
(12, 'download_center', 'Download Center', 'download_center', 1, 0, 12.00, '2024-06-26 12:29:50');

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy`
--

CREATE TABLE `pharmacy` (
  `id` int NOT NULL,
  `medicine_name` varchar(200) DEFAULT NULL,
  `medicine_category_id` int DEFAULT NULL,
  `medicine_image` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `medicine_company` varchar(100) DEFAULT NULL,
  `medicine_composition` varchar(100) DEFAULT NULL,
  `medicine_group` varchar(100) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `min_level` varchar(50) DEFAULT NULL,
  `reorder_level` varchar(50) DEFAULT NULL,
  `vat` float DEFAULT NULL,
  `unit_packing` varchar(50) DEFAULT NULL,
  `vat_ac` varchar(50) DEFAULT NULL,
  `rack_number` varchar(255) NOT NULL,
  `note` text,
  `is_active` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_bill_basic`
--

CREATE TABLE `pharmacy_bill_basic` (
  `id` int NOT NULL,
  `date` datetime DEFAULT NULL,
  `patient_id` int DEFAULT NULL,
  `ipd_prescription_basic_id` int DEFAULT NULL,
  `case_reference_id` int DEFAULT NULL,
  `customer_name` varchar(50) DEFAULT NULL,
  `customer_type` varchar(50) DEFAULT NULL,
  `doctor_name` varchar(50) DEFAULT NULL,
  `file` varchar(200) NOT NULL,
  `total` float(10,2) DEFAULT '0.00',
  `discount_percentage` float(10,2) DEFAULT '0.00',
  `discount` float(10,2) DEFAULT '0.00',
  `tax_percentage` float(10,2) DEFAULT '0.00',
  `tax` float(10,2) DEFAULT '0.00',
  `net_amount` float(10,2) DEFAULT '0.00',
  `note` text,
  `generated_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_bill_detail`
--

CREATE TABLE `pharmacy_bill_detail` (
  `id` int NOT NULL,
  `pharmacy_bill_basic_id` int DEFAULT NULL,
  `medicine_batch_detail_id` int DEFAULT NULL,
  `quantity` varchar(100) NOT NULL,
  `sale_price` float(10,2) NOT NULL,
  `amount` float(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `pharmacy_company`
--

CREATE TABLE `pharmacy_company` (
  `id` int NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `postnatal_examine`
--

CREATE TABLE `postnatal_examine` (
  `id` int NOT NULL,
  `patient_id` int NOT NULL,
  `labor_time` datetime NOT NULL,
  `delivery_time` datetime NOT NULL,
  `routine_question` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `general_remark` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `prefixes`
--

CREATE TABLE `prefixes` (
  `id` int NOT NULL,
  `type` varchar(100) DEFAULT NULL,
  `prefix` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `prefixes`
--

INSERT INTO `prefixes` (`id`, `type`, `prefix`, `created_at`) VALUES
(1, 'ipd_no', 'IPDN', '2021-06-30 17:40:23'),
(2, 'opd_no', 'OPDN', '2021-02-22 13:38:01'),
(3, 'ipd_prescription', 'IPDP', '2021-02-12 18:42:07'),
(4, 'opd_prescription', 'OPDP', '2021-02-12 18:42:17'),
(5, 'appointment', 'APPN', '2021-10-22 05:37:43'),
(6, 'pharmacy_billing', 'PHAB', '2021-10-22 05:37:43'),
(7, 'operation_theater_reference_no', 'OTRN', '2021-10-22 05:37:43'),
(8, 'blood_bank_billing', 'BLBB', '2021-10-22 05:40:38'),
(9, 'ambulance_call_billing', 'AMCB', '2021-10-22 05:40:38'),
(10, 'radiology_billing', 'RADB', '2021-10-22 05:40:38'),
(11, 'pathology_billing', 'PATB', '2021-10-22 05:40:38'),
(12, 'checkup_id', 'OCID', '2021-10-22 05:44:25'),
(13, 'purchase_no', 'PHPN', '2021-10-22 05:44:25'),
(14, 'transaction_id', 'TRID', '2021-10-22 05:44:25'),
(15, 'birth_record_reference_no', 'BRRN', '2021-10-22 05:44:25'),
(16, 'death_record_reference_no', 'DRRN', '2021-10-22 05:44:25');

-- --------------------------------------------------------

--
-- Table structure for table `primary_examine`
--

CREATE TABLE `primary_examine` (
  `id` int NOT NULL,
  `ipdid` int DEFAULT NULL,
  `visit_details_id` int DEFAULT NULL,
  `bleeding` varchar(250) DEFAULT NULL,
  `headache` varchar(250) DEFAULT NULL,
  `pain` varchar(250) DEFAULT NULL,
  `constipation` varchar(250) DEFAULT NULL,
  `urinary_symptoms` varchar(250) NOT NULL,
  `vomiting` varchar(250) DEFAULT NULL,
  `cough` varchar(250) DEFAULT NULL,
  `vaginal` varchar(250) DEFAULT NULL,
  `discharge` varchar(250) DEFAULT NULL,
  `oedema` varchar(250) DEFAULT NULL,
  `haemoroids` varchar(250) DEFAULT NULL,
  `weight` varchar(250) NOT NULL,
  `height` varchar(11) NOT NULL,
  `date` datetime DEFAULT NULL,
  `general_condition` text NOT NULL,
  `finding_remark` varchar(250) NOT NULL,
  `pelvic_examination` text NOT NULL,
  `sp` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `print_setting`
--

CREATE TABLE `print_setting` (
  `id` int NOT NULL,
  `print_header` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `print_footer` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `setting_for` varchar(200) NOT NULL,
  `is_active` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `print_setting`
--

INSERT INTO `print_setting` (`id`, `print_header`, `print_footer`, `setting_for`, `is_active`, `created_at`) VALUES
(1, 'uploads/printing/1.jpg', '', 'opdpre', 'yes', '2021-09-25 06:44:20'),
(2, 'uploads/printing/2.jpg', '', 'opd', 'yes', '2021-09-25 06:44:20'),
(3, 'uploads/printing/3.jpg', '', 'ipdpres', 'yes', '2021-09-25 06:44:20'),
(4, 'uploads/printing/4.jpg', '', 'ipd', 'yes', '2021-09-25 06:44:20'),
(5, 'uploads/printing/5.jpg', '', 'bill', 'yes', '2021-09-25 06:44:20'),
(6, 'uploads/printing/6.jpg', '', 'pharmacy', 'yes', '2021-09-25 06:44:20'),
(7, 'uploads/printing/7.jpg', '', 'payslip', 'yes', '2021-09-25 06:44:20'),
(8, 'uploads/printing/8.jpg', '', 'paymentreceipt', 'yes', '2021-09-25 06:44:20'),
(9, 'uploads/printing/9.jpg', '', 'birth', 'yes', '2021-09-25 06:44:20'),
(10, 'uploads/printing/10.jpg', '', 'death', 'yes', '2021-09-25 06:44:20'),
(11, 'uploads/printing/11.jpg', '', 'pathology', 'yes', '2021-09-25 06:44:20'),
(12, 'uploads/printing/12.jpg', '', 'radiology', 'yes', '2021-09-25 06:44:20'),
(13, 'uploads/printing/13.jpg', '', 'ot', 'yes', '2021-09-25 06:44:20'),
(14, 'uploads/printing/14.jpg', '', 'bloodbank', 'yes', '2021-09-25 06:44:20'),
(15, 'uploads/printing/15.jpg', '', 'ambulance', 'yes', '2021-09-25 06:44:20'),
(16, 'uploads/printing/16.jpg', '', 'discharge_card', 'yes', '2021-09-25 06:44:20'),
(17, 'uploads/printing/17.jpg', '', 'obstetric_history', 'yes', '2021-09-25 06:44:20'),
(18, 'uploads/printing/18.jpg', '', 'opd_antenatal_finding', 'yes', '2021-09-25 06:44:20'),
(19, 'uploads/printing/19.jpg', '', 'ipd_antenatal_finding', 'yes', '2021-09-25 06:44:20'),
(20, 'uploads/printing/20.jpg', '', 'appointment', 'yes', '2021-09-25 06:44:20');

-- --------------------------------------------------------

--
-- Table structure for table `radio`
--

CREATE TABLE `radio` (
  `id` int NOT NULL,
  `test_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `short_name` varchar(100) DEFAULT NULL,
  `test_type` varchar(100) DEFAULT NULL,
  `radiology_category_id` int DEFAULT NULL,
  `sub_category` varchar(50) NOT NULL,
  `report_days` varchar(50) NOT NULL,
  `charge_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `radiology_billing`
--

CREATE TABLE `radiology_billing` (
  `id` int NOT NULL,
  `patient_id` int DEFAULT NULL,
  `case_reference_id` int DEFAULT NULL,
  `ipd_prescription_basic_id` int DEFAULT NULL,
  `doctor_id` int DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `doctor_name` varchar(100) NOT NULL,
  `total` float(10,2) NOT NULL,
  `discount_percentage` float(10,2) NOT NULL,
  `discount` float(10,2) NOT NULL,
  `tax_percentage` float(10,2) NOT NULL,
  `tax` float(10,2) NOT NULL,
  `net_amount` float(10,2) NOT NULL,
  `transaction_id` int DEFAULT NULL,
  `note` text,
  `organisation_id` int DEFAULT NULL,
  `insurance_validity` date DEFAULT NULL,
  `insurance_id` varchar(250) DEFAULT NULL,
  `generated_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `radiology_parameter`
--

CREATE TABLE `radiology_parameter` (
  `id` int NOT NULL,
  `parameter_name` varchar(100) NOT NULL,
  `test_value` varchar(100) NOT NULL,
  `reference_range` varchar(100) NOT NULL,
  `range_from` varchar(500) DEFAULT NULL,
  `range_to` varchar(500) DEFAULT NULL,
  `gender` varchar(100) NOT NULL,
  `unit` varchar(100) NOT NULL,
  `description` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `radiology_parameterdetails`
--

CREATE TABLE `radiology_parameterdetails` (
  `id` int NOT NULL,
  `radiology_id` int DEFAULT NULL,
  `radiology_parameter_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `radiology_report`
--

CREATE TABLE `radiology_report` (
  `id` int NOT NULL,
  `radiology_bill_id` int DEFAULT NULL,
  `radiology_id` int DEFAULT NULL,
  `patient_id` int DEFAULT NULL,
  `customer_type` varchar(50) DEFAULT NULL,
  `patient_name` varchar(100) DEFAULT NULL,
  `consultant_doctor` varchar(10) NOT NULL,
  `reporting_date` date DEFAULT NULL,
  `parameter_update` date DEFAULT NULL,
  `description` text,
  `radiology_report` text,
  `report_name` text,
  `radiology_result` text COMMENT 'test result',
  `tax_percentage` float(10,2) NOT NULL DEFAULT '0.00',
  `apply_charge` float(10,2) NOT NULL DEFAULT '0.00',
  `radiology_center` varchar(250) NOT NULL,
  `generated_by` int DEFAULT NULL,
  `collection_specialist` int DEFAULT NULL,
  `collection_date` date DEFAULT NULL,
  `approved_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `radiology_report_parameterdetails`
--

CREATE TABLE `radiology_report_parameterdetails` (
  `id` int NOT NULL,
  `radiology_report_id` int DEFAULT NULL,
  `radiology_parameterdetail_id` int DEFAULT NULL,
  `radiology_report_value` varchar(200) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `read_notification`
--

CREATE TABLE `read_notification` (
  `id` int NOT NULL,
  `staff_id` int DEFAULT NULL,
  `notification_id` int DEFAULT NULL,
  `is_active` varchar(10) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `read_systemnotification`
--

CREATE TABLE `read_systemnotification` (
  `id` int NOT NULL,
  `notification_id` int DEFAULT NULL,
  `receiver_id` int DEFAULT NULL,
  `is_active` varchar(10) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `referral_category`
--

CREATE TABLE `referral_category` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_active` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `referral_commission`
--

CREATE TABLE `referral_commission` (
  `id` int NOT NULL,
  `referral_category_id` int DEFAULT NULL,
  `referral_type_id` int DEFAULT NULL,
  `commission` float DEFAULT NULL,
  `is_active` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `referral_payment`
--

CREATE TABLE `referral_payment` (
  `id` int NOT NULL,
  `referral_person_id` int DEFAULT NULL,
  `patient_id` int DEFAULT NULL,
  `referral_type` int DEFAULT NULL,
  `billing_id` int NOT NULL,
  `bill_amount` float(10,2) DEFAULT '0.00',
  `percentage` float(10,2) DEFAULT '0.00',
  `amount` float(10,2) DEFAULT '0.00',
  `date` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `referral_person`
--

CREATE TABLE `referral_person` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `category_id` int DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `person_name` varchar(100) DEFAULT NULL,
  `person_phone` varchar(50) DEFAULT NULL,
  `standard_commission` float(10,2) NOT NULL DEFAULT '0.00',
  `address` varchar(100) DEFAULT NULL,
  `is_active` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `referral_person_commission`
--

CREATE TABLE `referral_person_commission` (
  `id` int NOT NULL,
  `referral_person_id` int DEFAULT NULL,
  `referral_type_id` int DEFAULT NULL,
  `commission` float(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `referral_type`
--

CREATE TABLE `referral_type` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `prefixes_type` varchar(100) NOT NULL,
  `is_active` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `referral_type`
--

INSERT INTO `referral_type` (`id`, `name`, `prefixes_type`, `is_active`, `created_at`) VALUES
(1, 'opd', 'opd_no', 1, '2021-09-17 02:07:51'),
(2, 'ipd', 'ipd_no', 1, '2021-09-17 02:07:51'),
(3, 'pharmacy', 'pharmacy_billing', 1, '2021-09-17 02:07:51'),
(4, 'pathology', 'pathology_billing', 1, '2021-09-17 02:07:51'),
(5, 'radiology', 'radiology_billing', 1, '2021-09-17 02:07:51'),
(6, 'blood_bank', 'blood_bank_billing', 1, '2021-09-17 02:07:51'),
(7, 'ambulance', 'ambulance_call_billing', 1, '2021-09-17 02:07:51');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `slug` varchar(150) DEFAULT NULL,
  `is_active` int DEFAULT '0',
  `is_system` int NOT NULL DEFAULT '0',
  `is_superadmin` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `is_active`, `is_system`, `is_superadmin`, `created_at`) VALUES
(1, 'Admin', NULL, 0, 1, 0, '2018-12-25 06:19:43'),
(2, 'Accountant', NULL, 0, 1, 0, '2018-12-25 06:19:38'),
(3, 'Doctor', NULL, 0, 1, 0, '2018-07-21 05:07:36'),
(4, 'Pharmacist', NULL, 0, 1, 0, '2018-07-21 05:08:26'),
(5, 'Pathologist', NULL, 0, 1, 0, '2018-12-25 06:19:59'),
(6, 'Radiologist', NULL, 0, 1, 0, '2018-12-25 06:20:27'),
(7, 'Super Admin', NULL, 0, 1, 1, '2018-12-25 06:22:24'),
(8, 'Receptionist', NULL, 0, 1, 0, '2018-12-25 06:20:22'),
(9, 'Nurse', NULL, 0, 1, 0, '2020-12-23 01:58:58');

-- --------------------------------------------------------

--
-- Table structure for table `roles_permissions`
--

CREATE TABLE `roles_permissions` (
  `id` int NOT NULL,
  `role_id` int DEFAULT NULL,
  `perm_cat_id` int DEFAULT NULL,
  `can_view` int DEFAULT NULL,
  `can_add` int DEFAULT NULL,
  `can_edit` int DEFAULT NULL,
  `can_delete` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `roles_permissions`
--

INSERT INTO `roles_permissions` (`id`, `role_id`, `perm_cat_id`, `can_view`, `can_add`, `can_edit`, `can_delete`, `created_at`) VALUES
(1, 1, 346, 1, 0, 0, 0, '2021-09-15 02:19:21'),
(2, 1, 80, 1, 1, 1, 1, '2021-09-15 02:31:34'),
(3, 1, 81, 1, 1, 1, 1, '2021-09-15 02:31:34'),
(4, 1, 82, 1, 1, 1, 1, '2021-09-15 02:31:34'),
(5, 1, 83, 1, 1, 1, 1, '2021-09-15 02:31:34'),
(6, 1, 84, 1, 1, 1, 1, '2021-09-15 02:31:34'),
(7, 1, 85, 1, 1, 1, 1, '2021-09-15 02:31:34'),
(8, 1, 204, 1, 1, 1, 1, '2021-09-15 02:22:47'),
(9, 1, 205, 1, 0, 0, 0, '2021-09-15 02:20:15'),
(10, 1, 216, 1, 0, 0, 0, '2021-09-15 02:20:15'),
(11, 1, 217, 1, 0, 0, 0, '2021-09-15 02:20:15'),
(14, 1, 237, 1, 0, 0, 0, '2021-09-15 02:25:31'),
(15, 1, 239, 1, 0, 0, 0, '2021-09-15 02:25:31'),
(16, 1, 214, 1, 1, 1, 1, '2021-09-15 02:35:14'),
(17, 1, 215, 1, 1, 1, 1, '2021-09-15 02:35:14'),
(18, 1, 240, 1, 0, 1, 0, '2021-09-15 02:35:14'),
(19, 1, 242, 1, 0, 1, 0, '2021-09-15 02:35:14'),
(36, 1, 48, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(37, 1, 89, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(38, 1, 91, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(39, 1, 178, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(40, 1, 179, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(41, 1, 180, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(42, 1, 181, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(43, 1, 182, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(44, 1, 183, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(45, 1, 184, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(46, 1, 185, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(47, 1, 187, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(48, 1, 188, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(49, 1, 189, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(50, 1, 206, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(51, 1, 207, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(52, 1, 208, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(53, 1, 209, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(54, 1, 210, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(55, 1, 211, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(56, 1, 212, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(57, 1, 213, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(58, 1, 250, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(59, 1, 251, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(60, 1, 253, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(61, 1, 254, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(62, 1, 255, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(63, 1, 256, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(64, 1, 258, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(65, 1, 259, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(66, 1, 265, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(67, 1, 266, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(68, 1, 271, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(69, 1, 272, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(70, 1, 349, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(71, 1, 350, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(72, 1, 351, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(73, 1, 352, 1, 0, 0, 0, '2021-09-15 18:37:59'),
(78, 1, 12, 1, 1, 1, 1, '2021-09-17 21:55:07'),
(79, 1, 13, 1, 1, 1, 1, '2021-09-17 21:55:07'),
(81, 1, 134, 1, 1, 1, 1, '2021-10-07 04:54:53'),
(84, 1, 192, 1, 0, 1, 0, '2021-10-07 04:54:53'),
(105, 1, 140, 1, 1, 1, 1, '2021-10-07 04:55:23'),
(106, 1, 142, 1, 1, 1, 1, '2021-10-07 04:55:23'),
(114, 1, 300, 1, 1, 1, 1, '2021-09-16 22:16:20'),
(117, 1, 309, 1, 0, 1, 0, '2021-09-16 22:16:20'),
(119, 1, 322, 1, 1, 1, 1, '2021-09-16 22:16:20'),
(125, 1, 170, 1, 1, 1, 1, '2021-09-17 19:38:24'),
(127, 1, 201, 1, 0, 1, 0, '2021-09-15 23:45:28'),
(131, 1, 227, 1, 1, 1, 1, '2021-09-17 19:10:09'),
(132, 1, 291, 1, 1, 1, 1, '2021-09-17 19:10:09'),
(133, 1, 292, 1, 1, 1, 1, '2021-09-17 19:10:09'),
(142, 1, 317, 1, 1, 0, 1, '2021-09-15 20:15:33'),
(143, 1, 269, 1, 1, 0, 1, '2021-09-15 20:16:52'),
(144, 1, 270, 1, 1, 0, 1, '2021-09-15 20:16:52'),
(149, 1, 54, 1, 0, 1, 0, '2021-10-07 00:37:31'),
(150, 1, 56, 1, 0, 1, 0, '2021-10-07 00:37:31'),
(151, 1, 57, 1, 0, 1, 0, '2021-09-15 20:53:19'),
(152, 1, 58, 1, 0, 1, 0, '2021-09-15 20:53:19'),
(153, 1, 59, 1, 0, 1, 0, '2021-09-15 20:53:19'),
(154, 1, 60, 1, 0, 1, 0, '2021-09-15 20:53:19'),
(155, 1, 126, 1, 0, 0, 0, '2021-09-15 20:53:19'),
(156, 1, 130, 1, 1, 0, 1, '2021-09-15 20:53:19'),
(157, 1, 131, 1, 0, 0, 0, '2021-09-15 20:54:53'),
(158, 1, 267, 1, 1, 1, 1, '2021-09-15 20:54:53'),
(159, 1, 274, 1, 0, 0, 0, '2021-09-15 20:54:53'),
(160, 1, 279, 1, 1, 1, 1, '2021-09-15 20:54:53'),
(161, 1, 287, 1, 0, 1, 0, '2021-09-15 20:54:53'),
(162, 1, 288, 1, 0, 1, 0, '2021-09-15 20:54:53'),
(163, 1, 348, 1, 0, 1, 0, '2021-09-15 20:54:53'),
(164, 1, 61, 1, 1, 0, 1, '2021-10-07 04:57:12'),
(165, 1, 62, 1, 1, 0, 1, '2021-10-07 04:57:12'),
(166, 1, 63, 1, 1, 0, 1, '2021-10-07 04:57:12'),
(167, 1, 64, 1, 1, 1, 1, '2021-10-07 04:57:12'),
(168, 1, 65, 1, 1, 1, 1, '2021-10-07 04:57:12'),
(169, 1, 66, 1, 1, 1, 1, '2021-10-07 04:57:12'),
(170, 1, 67, 1, 1, 1, 1, '2021-10-07 04:57:12'),
(171, 1, 43, 1, 1, 1, 1, '2021-09-15 21:54:11'),
(172, 1, 44, 1, 0, 0, 0, '2021-09-15 21:53:24'),
(175, 1, 283, 1, 1, 1, 1, '2021-09-17 22:22:19'),
(176, 1, 284, 1, 1, 1, 1, '2021-09-17 22:22:19'),
(177, 1, 285, 1, 1, 1, 1, '2021-09-17 22:22:19'),
(178, 1, 286, 1, 1, 1, 1, '2021-09-17 22:22:19'),
(181, 1, 146, 1, 1, 1, 1, '2021-09-17 02:03:26'),
(182, 1, 148, 1, 1, 1, 1, '2021-09-17 02:03:26'),
(184, 1, 86, 1, 1, 1, 1, '2021-09-17 23:02:51'),
(192, 1, 127, 1, 0, 0, 0, '2021-09-16 00:46:49'),
(193, 1, 118, 1, 0, 0, 0, '2021-09-16 00:59:08'),
(194, 1, 152, 1, 1, 1, 1, '2021-09-16 23:30:15'),
(195, 1, 153, 1, 1, 1, 1, '2021-09-16 19:14:09'),
(197, 1, 354, 1, 0, 1, 0, '2021-09-17 19:42:16'),
(199, 1, 261, 1, 1, 1, 1, '2021-09-17 19:42:16'),
(200, 1, 262, 1, 1, 1, 1, '2021-09-17 19:42:16'),
(201, 1, 315, 1, 1, 0, 1, '2021-09-17 19:42:16'),
(202, 1, 244, 1, 0, 1, 0, '2021-09-16 20:29:17'),
(221, 1, 138, 1, 1, 1, 1, '2021-10-07 04:55:23'),
(222, 1, 139, 1, 1, 1, 1, '2021-10-07 04:55:23'),
(223, 1, 143, 1, 1, 1, 1, '2021-10-07 04:55:23'),
(224, 1, 144, 1, 1, 0, 1, '2021-10-07 04:55:23'),
(226, 1, 197, 1, 0, 1, 0, '2021-09-17 02:01:37'),
(228, 1, 248, 1, 0, 1, 0, '2021-10-07 04:55:23'),
(229, 1, 301, 1, 0, 0, 0, '2021-09-16 22:16:20'),
(230, 1, 308, 1, 0, 0, 0, '2021-09-16 22:16:20'),
(236, 1, 149, 1, 1, 1, 1, '2021-10-22 00:28:29'),
(237, 1, 175, 1, 1, 1, 1, '2021-10-22 00:28:29'),
(238, 1, 243, 1, 0, 1, 0, '2021-10-22 00:28:29'),
(239, 1, 257, 1, 1, 1, 1, '2021-10-22 00:28:29'),
(243, 1, 314, 1, 1, 0, 1, '2021-10-22 00:28:29'),
(245, 1, 310, 1, 0, 0, 0, '2021-09-16 22:29:09'),
(247, 1, 355, 1, 0, 1, 0, '2021-10-22 00:28:29'),
(248, 1, 260, 1, 1, 1, 1, '2021-10-22 00:28:29'),
(249, 1, 263, 1, 1, 1, 1, '2021-10-22 00:28:29'),
(250, 1, 312, 1, 0, 1, 0, '2021-10-22 00:28:29'),
(254, 1, 135, 1, 1, 1, 1, '2021-10-07 04:54:53'),
(255, 1, 137, 1, 1, 1, 1, '2021-10-07 04:54:53'),
(257, 1, 219, 1, 0, 0, 0, '2021-09-17 01:09:11'),
(258, 1, 221, 1, 1, 1, 1, '2021-09-18 00:55:57'),
(259, 1, 222, 1, 1, 0, 1, '2021-09-17 01:13:33'),
(260, 1, 299, 1, 1, 1, 1, '2021-09-17 01:14:24'),
(261, 1, 303, 1, 1, 1, 1, '2021-09-17 01:17:48'),
(262, 1, 304, 1, 0, 0, 0, '2021-09-17 01:21:20'),
(263, 1, 305, 1, 0, 1, 0, '2021-09-17 01:22:28'),
(264, 1, 306, 1, 0, 0, 0, '2021-09-17 01:22:43'),
(265, 1, 307, 1, 0, 0, 0, '2021-09-17 01:23:28'),
(266, 1, 319, 1, 1, 1, 1, '2021-10-07 05:01:26'),
(274, 1, 220, 1, 1, 1, 1, '2021-09-17 02:02:20'),
(275, 1, 326, 1, 1, 1, 1, '2021-09-17 18:09:51'),
(276, 1, 200, 1, 1, 0, 1, '2021-09-17 18:59:44'),
(277, 1, 225, 1, 1, 1, 1, '2021-09-17 19:10:09'),
(278, 1, 226, 1, 1, 1, 1, '2021-09-17 19:10:09'),
(279, 1, 224, 1, 0, 0, 0, '2021-09-17 19:38:24'),
(280, 1, 313, 1, 1, 0, 1, '2021-09-17 19:39:06'),
(281, 1, 171, 1, 1, 1, 1, '2021-09-17 19:46:07'),
(282, 1, 353, 1, 0, 1, 0, '2021-09-17 19:46:38'),
(283, 1, 168, 1, 1, 1, 1, '2021-09-17 20:14:24'),
(284, 1, 169, 1, 1, 1, 1, '2021-09-17 20:16:16'),
(285, 1, 311, 1, 1, 1, 1, '2021-09-17 20:24:00'),
(286, 1, 246, 1, 1, 1, 1, '2021-09-17 20:26:44'),
(287, 1, 202, 1, 1, 0, 1, '2021-09-17 20:30:46'),
(288, 1, 293, 1, 1, 1, 1, '2021-09-17 20:30:46'),
(289, 1, 302, 1, 1, 0, 1, '2021-09-17 20:30:46'),
(290, 1, 173, 1, 1, 1, 1, '2021-09-17 20:36:23'),
(291, 1, 347, 1, 0, 1, 1, '2021-09-17 20:36:23'),
(292, 1, 273, 1, 0, 1, 0, '2021-09-17 21:43:50'),
(293, 1, 9, 1, 1, 1, 1, '2021-09-17 21:47:50'),
(294, 1, 10, 1, 1, 1, 1, '2021-09-17 21:47:50'),
(295, 1, 176, 1, 1, 1, 1, '2021-09-20 23:45:51'),
(296, 1, 102, 1, 1, 1, 1, '2021-10-07 05:04:58'),
(297, 1, 31, 1, 1, 0, 1, '2021-10-07 00:40:13'),
(298, 1, 32, 1, 1, 1, 1, '2021-09-17 22:47:43'),
(299, 1, 33, 1, 1, 1, 1, '2021-09-17 22:47:43'),
(300, 1, 34, 1, 1, 1, 1, '2021-09-17 22:47:43'),
(301, 1, 35, 1, 1, 1, 1, '2021-09-17 22:47:43'),
(302, 1, 104, 1, 1, 1, 1, '2021-09-17 22:47:43'),
(303, 1, 87, 1, 0, 0, 0, '2021-09-17 23:01:48'),
(304, 1, 88, 1, 1, 1, 0, '2021-09-17 23:33:38'),
(305, 1, 90, 1, 1, 0, 1, '2021-09-17 23:34:45'),
(306, 1, 108, 1, 1, 1, 1, '2021-09-17 23:37:46'),
(307, 1, 109, 1, 1, 0, 1, '2021-09-17 23:39:39'),
(308, 1, 110, 1, 1, 1, 1, '2021-10-07 04:56:43'),
(309, 1, 111, 1, 1, 1, 1, '2021-10-07 04:56:43'),
(310, 1, 112, 1, 1, 1, 1, '2021-10-07 04:56:43'),
(311, 1, 249, 1, 1, 1, 1, '2021-10-07 04:56:43'),
(313, 1, 203, 1, 1, 1, 1, '2021-09-17 23:08:50'),
(314, 1, 264, 1, 1, 0, 1, '2021-09-17 23:08:50'),
(315, 1, 289, 1, 1, 1, 1, '2021-09-17 23:08:50'),
(316, 1, 290, 1, 1, 1, 1, '2021-09-17 23:08:50'),
(317, 1, 165, 1, 1, 1, 1, '2021-09-17 23:23:51'),
(318, 1, 166, 1, 1, 1, 1, '2021-09-17 23:23:51'),
(319, 1, 247, 1, 1, 1, 1, '2021-09-17 23:23:51'),
(320, 1, 318, 1, 1, 0, 1, '2021-09-17 23:23:51'),
(324, 2, 237, 1, 0, 0, 0, '2021-09-18 01:01:56'),
(327, 2, 135, 1, 1, 1, 0, '2021-10-07 01:01:51'),
(334, 2, 221, 1, 1, 1, 1, '2021-09-18 01:25:50'),
(335, 2, 222, 1, 1, 0, 1, '2021-09-18 01:26:52'),
(336, 2, 299, 1, 0, 0, 0, '2021-10-07 01:01:51'),
(337, 2, 303, 1, 0, 0, 0, '2021-10-07 01:01:51'),
(339, 2, 305, 1, 0, 1, 0, '2021-09-18 01:38:56'),
(341, 2, 307, 1, 0, 0, 0, '2021-09-18 01:43:41'),
(343, 2, 320, 1, 0, 0, 0, '2021-09-18 01:44:37'),
(346, 2, 138, 1, 1, 1, 0, '2021-10-07 01:02:47'),
(350, 2, 143, 1, 1, 1, 1, '2021-09-19 23:54:42'),
(351, 2, 144, 1, 1, 0, 1, '2021-09-19 23:54:42'),
(354, 2, 329, 1, 0, 0, 0, '2021-09-18 02:23:47'),
(356, 2, 326, 1, 0, 0, 0, '2021-10-07 05:33:02'),
(357, 3, 132, 1, 1, 1, 1, '2021-09-21 20:39:45'),
(358, 3, 134, 1, 1, 1, 1, '2021-09-19 19:30:16'),
(362, 3, 135, 1, 1, 1, 1, '2021-09-19 19:45:00'),
(363, 3, 137, 1, 1, 1, 1, '2021-09-19 19:45:00'),
(364, 3, 192, 1, 0, 1, 0, '2021-09-19 19:46:00'),
(372, 1, 295, 1, 0, 1, 0, '2021-10-07 04:56:29'),
(373, 3, 218, 1, 0, 0, 0, '2021-09-19 21:47:53'),
(374, 3, 219, 1, 0, 0, 0, '2021-09-19 21:48:21'),
(375, 3, 221, 1, 1, 1, 1, '2021-09-19 21:48:54'),
(376, 3, 222, 1, 1, 0, 1, '2021-09-19 21:51:36'),
(377, 3, 299, 1, 1, 1, 1, '2021-09-19 21:53:11'),
(378, 3, 303, 1, 1, 1, 1, '2021-09-19 22:05:35'),
(379, 2, 139, 1, 1, 1, 0, '2021-10-07 01:02:47'),
(380, 3, 304, 1, 0, 0, 0, '2021-09-19 22:21:44'),
(382, 3, 305, 1, 0, 1, 0, '2021-09-19 22:23:53'),
(384, 2, 198, 1, 0, 0, 0, '2021-09-19 22:24:26'),
(386, 2, 300, 1, 1, 1, 1, '2021-09-19 23:54:42'),
(387, 2, 301, 1, 0, 0, 0, '2021-09-19 22:24:26'),
(388, 2, 308, 1, 0, 0, 0, '2021-09-19 22:24:26'),
(389, 2, 309, 1, 0, 0, 0, '2021-09-19 22:24:26'),
(391, 2, 323, 1, 1, 1, 1, '2021-09-19 23:54:42'),
(392, 2, 324, 1, 1, 1, 1, '2021-09-19 23:54:42'),
(393, 2, 325, 1, 1, 1, 1, '2021-09-19 23:54:42'),
(394, 3, 306, 1, 0, 0, 0, '2021-09-19 22:24:51'),
(395, 3, 307, 1, 0, 0, 0, '2021-09-19 22:26:27'),
(396, 3, 319, 1, 1, 1, 1, '2021-09-19 22:27:25'),
(397, 3, 320, 1, 0, 0, 0, '2021-09-19 22:38:28'),
(398, 3, 321, 1, 0, 0, 0, '2021-09-19 22:46:51'),
(399, 3, 138, 1, 1, 1, 1, '2021-09-19 22:47:05'),
(400, 3, 139, 1, 1, 1, 1, '2021-09-19 22:50:25'),
(401, 3, 140, 1, 1, 1, 1, '2021-09-19 22:51:13'),
(402, 3, 142, 1, 1, 1, 1, '2021-09-19 22:51:13'),
(403, 3, 143, 1, 1, 1, 1, '2021-09-19 22:51:13'),
(404, 3, 144, 1, 1, 0, 1, '2021-09-19 22:52:59'),
(405, 3, 196, 1, 1, 1, 1, '2021-09-19 22:56:19'),
(406, 3, 197, 1, 0, 1, 0, '2021-09-19 22:57:00'),
(407, 3, 198, 1, 0, 0, 0, '2021-09-19 22:57:21'),
(408, 3, 220, 1, 1, 1, 1, '2021-09-19 22:57:21'),
(409, 3, 248, 1, 0, 1, 0, '2021-09-19 22:58:10'),
(410, 3, 300, 1, 1, 1, 1, '2021-09-19 22:58:10'),
(411, 3, 301, 1, 0, 0, 0, '2021-09-19 22:59:15'),
(412, 3, 308, 1, 0, 0, 0, '2021-09-19 22:59:50'),
(413, 3, 309, 1, 0, 1, 0, '2021-09-19 23:00:17'),
(414, 3, 310, 1, 0, 0, 0, '2021-09-19 23:00:37'),
(415, 3, 322, 1, 1, 1, 1, '2021-09-19 23:01:22'),
(416, 3, 323, 1, 1, 1, 1, '2021-09-19 23:02:41'),
(417, 3, 324, 1, 1, 1, 1, '2021-09-19 23:02:41'),
(418, 3, 325, 1, 1, 1, 1, '2021-09-19 23:02:41'),
(419, 3, 326, 1, 1, 1, 1, '2021-09-19 23:03:57'),
(420, 3, 327, 1, 0, 0, 0, '2021-09-19 23:10:25'),
(421, 3, 329, 1, 0, 0, 0, '2021-09-19 23:10:25'),
(422, 3, 146, 1, 0, 0, 0, '2021-09-21 21:58:29'),
(424, 2, 327, 1, 0, 0, 0, '2021-09-19 23:14:27'),
(425, 3, 236, 1, 1, 1, 1, '2021-10-07 02:00:54'),
(433, 3, 226, 1, 0, 0, 0, '2021-09-20 19:02:48'),
(435, 3, 291, 1, 0, 0, 0, '2021-09-20 19:02:48'),
(436, 3, 292, 1, 0, 0, 0, '2021-09-20 19:02:48'),
(438, 3, 149, 1, 0, 0, 0, '2021-10-07 01:50:27'),
(444, 3, 312, 1, 0, 0, 0, '2021-10-07 01:50:27'),
(447, 2, 149, 1, 0, 0, 0, '2021-10-07 01:17:28'),
(453, 2, 312, 1, 0, 1, 0, '2021-09-20 00:04:18'),
(454, 2, 314, 1, 1, 0, 1, '2021-09-22 19:32:53'),
(455, 2, 355, 1, 0, 1, 0, '2021-09-20 00:04:18'),
(456, 3, 152, 1, 0, 0, 0, '2021-10-07 01:50:59'),
(463, 3, 353, 1, 0, 0, 0, '2021-10-07 01:50:59'),
(465, 2, 152, 1, 0, 0, 0, '2021-10-07 01:21:53'),
(466, 2, 153, 1, 0, 0, 0, '2021-10-07 01:22:48'),
(472, 2, 353, 1, 0, 1, 0, '2021-09-20 00:34:19'),
(474, 3, 168, 1, 0, 0, 0, '2021-10-07 01:56:13'),
(475, 2, 146, 1, 0, 0, 0, '2021-10-07 01:12:21'),
(476, 2, 148, 1, 0, 0, 0, '2021-10-07 01:12:21'),
(478, 2, 200, 1, 0, 0, 0, '2021-10-07 01:14:19'),
(481, 2, 225, 1, 0, 0, 0, '2021-10-07 01:14:19'),
(484, 2, 291, 1, 1, 0, 0, '2021-10-07 05:33:32'),
(485, 2, 292, 1, 1, 0, 0, '2021-10-07 05:33:32'),
(486, 2, 313, 1, 1, 0, 0, '2021-10-07 05:33:32'),
(495, 3, 270, 1, 1, 0, 1, '2021-09-20 01:25:18'),
(496, 2, 168, 1, 0, 0, 0, '2021-10-07 01:24:37'),
(498, 2, 202, 1, 0, 0, 0, '2021-10-07 01:24:42'),
(500, 2, 293, 1, 0, 0, 0, '2021-10-07 01:25:10'),
(501, 2, 302, 1, 0, 0, 0, '2021-10-07 01:25:10'),
(502, 2, 311, 1, 0, 0, 0, '2021-10-07 01:25:10'),
(503, 2, 317, 1, 1, 0, 1, '2021-09-21 02:02:20'),
(504, 3, 102, 1, 1, 1, 1, '2021-09-20 01:26:19'),
(506, 3, 118, 1, 0, 0, 0, '2021-09-20 01:29:24'),
(519, 3, 173, 1, 0, 0, 0, '2021-10-07 01:56:37'),
(520, 3, 347, 1, 0, 0, 0, '2021-10-07 01:56:37'),
(527, 3, 176, 1, 0, 0, 0, '2021-10-07 02:01:51'),
(530, 3, 289, 1, 0, 0, 0, '2021-10-07 02:01:51'),
(531, 3, 290, 1, 0, 0, 0, '2021-10-07 02:01:51'),
(533, 3, 330, 1, 0, 0, 0, '2021-09-20 01:46:52'),
(534, 3, 331, 1, 0, 0, 0, '2021-10-07 05:42:53'),
(535, 3, 332, 1, 0, 0, 0, '2021-09-20 01:46:52'),
(536, 3, 333, 1, 0, 0, 0, '2021-09-20 01:46:52'),
(537, 3, 334, 1, 0, 0, 0, '2021-09-20 01:46:52'),
(538, 3, 335, 1, 0, 0, 0, '2021-10-07 02:00:10'),
(539, 3, 336, 1, 0, 0, 0, '2021-09-20 01:46:52'),
(540, 3, 337, 1, 0, 0, 0, '2021-09-20 01:46:52'),
(541, 3, 338, 1, 0, 0, 0, '2021-09-20 01:46:52'),
(542, 3, 339, 1, 0, 0, 0, '2021-10-07 02:00:10'),
(543, 3, 340, 1, 0, 0, 0, '2021-09-20 01:46:52'),
(544, 3, 341, 1, 0, 0, 0, '2021-09-21 02:32:35'),
(545, 3, 342, 1, 0, 0, 0, '2021-09-20 01:46:52'),
(546, 3, 343, 1, 0, 0, 0, '2021-09-20 01:46:52'),
(547, 3, 344, 1, 0, 0, 0, '2021-09-20 01:46:52'),
(548, 3, 345, 1, 0, 0, 0, '2021-09-20 01:46:52'),
(550, 3, 166, 1, 0, 0, 0, '2021-09-21 01:50:56'),
(565, 3, 204, 1, 1, 1, 1, '2021-09-20 18:43:10'),
(566, 3, 205, 1, 0, 0, 0, '2021-09-20 02:08:03'),
(567, 3, 216, 1, 0, 0, 0, '2021-09-20 02:08:03'),
(568, 3, 217, 1, 0, 0, 0, '2021-09-20 02:08:03'),
(573, 3, 214, 1, 1, 1, 1, '2021-09-20 02:18:50'),
(574, 3, 215, 1, 1, 1, 1, '2021-09-20 02:18:50'),
(577, 3, 294, 1, 1, 1, 1, '2021-10-07 05:43:36'),
(578, 3, 295, 1, 0, 1, 0, '2021-09-20 23:39:37'),
(579, 3, 296, 1, 1, 1, 1, '2021-09-20 23:39:37'),
(580, 3, 297, 1, 0, 0, 0, '2021-10-07 05:43:36'),
(581, 3, 298, 1, 0, 0, 0, '2021-10-07 05:43:36'),
(584, 2, 165, 1, 0, 0, 0, '2021-10-07 05:35:28'),
(585, 2, 166, 1, 0, 0, 0, '2021-10-07 01:35:03'),
(594, 2, 204, 1, 1, 1, 1, '2021-09-20 18:35:02'),
(596, 2, 216, 1, 0, 0, 0, '2021-09-20 18:14:53'),
(597, 2, 217, 1, 0, 0, 0, '2021-09-20 18:14:53'),
(607, 2, 294, 1, 1, 1, 1, '2021-09-20 20:46:45'),
(608, 2, 295, 1, 0, 1, 0, '2021-09-20 19:50:38'),
(609, 2, 296, 1, 1, 1, 1, '2021-09-20 20:46:45'),
(610, 2, 297, 1, 0, 1, 0, '2021-09-20 19:50:38'),
(611, 2, 298, 1, 0, 1, 0, '2021-09-20 19:50:38'),
(612, 2, 102, 1, 1, 1, 1, '2021-10-07 01:46:19'),
(614, 2, 304, 1, 0, 0, 0, '2021-09-20 20:11:46'),
(619, 3, 302, 1, 0, 0, 0, '2021-10-07 01:56:13'),
(620, 3, 311, 1, 0, 0, 0, '2021-10-07 01:56:13'),
(624, 3, 269, 1, 1, 0, 1, '2021-09-20 20:50:56'),
(626, 2, 176, 1, 1, 1, 1, '2021-09-20 20:55:32'),
(627, 2, 203, 1, 1, 1, 1, '2021-09-20 20:55:32'),
(629, 2, 289, 1, 1, 1, 1, '2021-09-20 20:55:32'),
(630, 2, 290, 1, 1, 1, 1, '2021-09-20 20:55:32'),
(631, 2, 9, 1, 1, 1, 1, '2021-10-07 01:27:11'),
(632, 2, 10, 1, 1, 1, 1, '2021-10-07 01:27:11'),
(633, 2, 12, 1, 1, 1, 1, '2021-09-20 21:09:31'),
(634, 2, 13, 1, 1, 1, 1, '2021-09-22 19:19:22'),
(639, 2, 330, 1, 0, 0, 0, '2021-09-20 22:27:44'),
(640, 2, 331, 1, 1, 0, 0, '2021-09-20 22:53:32'),
(641, 2, 332, 1, 0, 0, 0, '2021-09-20 22:27:44'),
(642, 2, 333, 1, 1, 0, 0, '2021-09-20 22:53:32'),
(643, 2, 334, 1, 0, 0, 0, '2021-09-20 22:27:44'),
(644, 2, 335, 1, 1, 0, 0, '2021-09-21 01:52:27'),
(646, 2, 337, 1, 1, 0, 0, '2021-09-21 19:15:44'),
(647, 2, 338, 1, 0, 0, 0, '2021-09-20 22:27:44'),
(648, 2, 339, 1, 1, 0, 0, '2021-09-21 00:38:37'),
(649, 2, 340, 1, 0, 0, 0, '2021-09-20 22:27:44'),
(650, 2, 341, 1, 1, 0, 0, '2021-09-22 19:19:22'),
(651, 2, 342, 1, 0, 0, 0, '2021-09-20 22:27:44'),
(652, 2, 343, 1, 1, 0, 0, '2021-09-22 19:19:22'),
(653, 2, 344, 1, 0, 0, 0, '2021-09-20 22:27:44'),
(654, 2, 345, 1, 0, 0, 0, '2021-09-20 22:27:44'),
(655, 2, 48, 1, 0, 0, 0, '2021-09-20 23:01:51'),
(658, 2, 178, 1, 0, 0, 0, '2021-09-20 23:01:51'),
(659, 2, 179, 1, 0, 0, 0, '2021-09-20 23:01:51'),
(660, 2, 180, 1, 0, 0, 0, '2021-09-20 23:01:51'),
(661, 2, 181, 1, 0, 0, 0, '2021-09-20 23:01:51'),
(662, 2, 182, 1, 0, 0, 0, '2021-09-20 23:01:51'),
(667, 2, 188, 1, 0, 0, 0, '2021-09-20 23:01:51'),
(668, 2, 189, 1, 0, 0, 0, '2021-09-20 23:01:51'),
(669, 2, 206, 1, 0, 0, 0, '2021-09-20 23:01:51'),
(670, 2, 207, 1, 0, 0, 0, '2021-09-20 23:01:51'),
(671, 2, 208, 1, 0, 0, 0, '2021-09-20 23:01:51'),
(672, 2, 209, 1, 0, 0, 0, '2021-09-20 23:01:51'),
(673, 2, 210, 1, 0, 0, 0, '2021-09-20 23:01:51'),
(675, 2, 212, 1, 0, 0, 0, '2021-09-20 23:01:51'),
(676, 2, 213, 1, 0, 0, 0, '2021-09-20 23:01:51'),
(677, 2, 250, 1, 0, 0, 0, '2021-09-20 23:01:51'),
(678, 2, 251, 1, 0, 0, 0, '2021-09-20 23:01:51'),
(679, 2, 253, 1, 0, 0, 0, '2021-09-20 23:01:51'),
(680, 2, 254, 1, 0, 0, 0, '2021-09-20 23:01:51'),
(681, 2, 255, 1, 0, 0, 0, '2021-09-20 23:01:51'),
(687, 2, 271, 1, 0, 0, 0, '2021-09-20 23:01:51'),
(688, 2, 272, 1, 0, 0, 0, '2021-09-20 23:01:51'),
(689, 2, 349, 1, 0, 0, 0, '2021-09-20 23:01:51'),
(690, 2, 350, 1, 0, 0, 0, '2021-09-20 23:01:51'),
(691, 2, 351, 1, 0, 0, 0, '2021-09-20 23:01:51'),
(692, 2, 352, 1, 0, 0, 0, '2021-09-20 23:01:51'),
(693, 3, 86, 1, 0, 0, 0, '2021-10-07 02:07:51'),
(695, 2, 43, 1, 1, 1, 1, '2021-09-21 00:07:38'),
(696, 2, 44, 1, 0, 0, 0, '2021-09-20 23:59:29'),
(700, 3, 109, 1, 1, 0, 1, '2021-10-07 02:07:51'),
(703, 2, 27, 1, 1, 0, 1, '2021-09-21 00:22:26'),
(706, 2, 31, 1, 1, 0, 1, '2021-09-21 00:38:37'),
(707, 2, 32, 1, 1, 1, 1, '2021-09-21 00:38:37'),
(708, 2, 33, 1, 1, 1, 1, '2021-09-21 00:38:37'),
(709, 2, 34, 1, 1, 1, 1, '2021-09-21 00:38:37'),
(710, 2, 35, 1, 1, 1, 1, '2021-09-21 00:38:37'),
(711, 2, 104, 1, 1, 1, 1, '2021-09-21 00:38:37'),
(712, 2, 315, 1, 1, 0, 1, '2021-09-22 19:34:31'),
(726, 3, 43, 1, 1, 1, 1, '2021-09-21 01:03:20'),
(727, 3, 44, 1, 0, 0, 0, '2021-09-21 01:03:20'),
(728, 3, 27, 1, 1, 0, 1, '2021-09-21 01:12:55'),
(735, 3, 165, 1, 0, 0, 0, '2021-10-07 02:02:19'),
(750, 3, 267, 1, 1, 1, 1, '2021-09-21 01:47:43'),
(751, 3, 274, 1, 0, 0, 0, '2021-09-21 01:45:32'),
(752, 3, 279, 1, 1, 1, 1, '2021-09-21 01:47:43'),
(757, 2, 86, 1, 1, 1, 1, '2021-09-21 20:06:20'),
(764, 2, 283, 1, 1, 1, 1, '2021-09-22 01:07:28'),
(765, 2, 284, 1, 1, 1, 1, '2021-09-22 01:07:28'),
(766, 2, 285, 1, 1, 1, 1, '2021-09-22 01:07:28'),
(767, 2, 286, 1, 1, 1, 1, '2021-09-22 01:07:28'),
(768, 3, 48, 1, 0, 0, 0, '2021-09-21 02:12:03'),
(771, 3, 178, 1, 0, 0, 0, '2021-09-21 02:12:03'),
(775, 3, 182, 1, 0, 0, 0, '2021-09-21 02:12:03'),
(801, 3, 272, 1, 0, 0, 0, '2021-09-21 02:12:03'),
(806, 2, 88, 1, 0, 0, 0, '2021-10-07 05:36:29'),
(807, 2, 90, 1, 0, 0, 0, '2021-10-07 05:36:29'),
(809, 2, 109, 1, 1, 0, 1, '2021-09-27 06:57:53'),
(814, 2, 249, 1, 1, 1, 1, '2021-09-22 01:43:04'),
(816, 2, 310, 1, 0, 0, 0, '2021-09-21 18:00:40'),
(817, 2, 129, 0, 1, 0, 1, '2021-09-22 01:43:04'),
(819, 1, 362, 1, 0, 0, 0, '2021-09-21 18:50:19'),
(820, 1, 363, 1, 1, 1, 1, '2021-09-21 19:07:31'),
(821, 1, 364, 1, 0, 0, 0, '2021-09-21 18:59:42'),
(822, 1, 365, 1, 1, 1, 1, '2021-09-21 19:03:37'),
(823, 1, 366, 1, 0, 0, 0, '2021-09-21 18:59:42'),
(826, 2, 132, 1, 1, 1, 0, '2021-10-07 01:01:51'),
(849, 2, 319, 1, 0, 0, 0, '2021-10-07 05:31:49'),
(870, 3, 368, 1, 0, 0, 0, '2021-09-21 20:33:40'),
(924, 8, 152, 1, 0, 0, 0, '2021-10-07 04:04:37'),
(927, 4, 270, 1, 0, 0, 0, '2021-10-07 02:27:36'),
(948, 4, 334, 1, 0, 0, 0, '2021-09-21 23:56:07'),
(949, 4, 335, 1, 1, 0, 0, '2021-09-30 06:47:45'),
(962, 4, 176, 1, 1, 1, 1, '2021-10-07 05:52:38'),
(963, 4, 203, 1, 1, 1, 1, '2021-10-07 05:52:38'),
(965, 4, 289, 1, 1, 1, 1, '2021-09-30 07:32:30'),
(966, 4, 290, 1, 1, 1, 1, '2021-09-30 07:32:30'),
(983, 4, 216, 1, 0, 0, 0, '2021-09-22 00:25:36'),
(984, 4, 217, 1, 0, 0, 0, '2021-09-22 00:34:54'),
(990, 4, 86, 1, 0, 0, 0, '2021-10-07 02:37:32'),
(995, 4, 109, 1, 1, 0, 1, '2021-10-01 00:27:47'),
(1001, 2, 118, 1, 0, 0, 0, '2021-09-22 00:55:25'),
(1016, 4, 43, 1, 1, 1, 1, '2021-09-22 01:04:08'),
(1017, 4, 44, 1, 0, 0, 0, '2021-09-22 01:04:08'),
(1018, 4, 27, 1, 1, 0, 1, '2021-09-22 01:12:34'),
(1060, 4, 179, 1, 0, 0, 0, '2021-09-22 01:51:01'),
(1099, 2, 336, 1, 0, 0, 0, '2021-09-22 19:19:22'),
(1119, 1, 367, 1, 1, 1, 1, '2021-10-07 05:04:40'),
(1134, 9, 102, 1, 1, 1, 1, '2021-09-22 20:44:30'),
(1136, 9, 132, 1, 0, 0, 0, '2021-10-07 04:18:18'),
(1137, 9, 134, 1, 0, 0, 0, '2021-10-07 04:18:34'),
(1138, 9, 135, 1, 0, 0, 0, '2021-10-07 04:18:50'),
(1141, 9, 218, 1, 0, 0, 0, '2021-09-22 20:46:41'),
(1142, 9, 219, 1, 0, 0, 0, '2021-09-22 20:46:41'),
(1145, 9, 299, 1, 1, 1, 1, '2021-09-22 22:31:25'),
(1146, 9, 303, 1, 0, 0, 0, '2021-10-07 04:24:16'),
(1147, 9, 304, 1, 0, 0, 0, '2021-09-22 20:46:41'),
(1148, 9, 305, 1, 0, 0, 0, '2021-10-07 04:25:46'),
(1150, 9, 307, 1, 0, 0, 0, '2021-09-22 20:46:41'),
(1151, 9, 319, 1, 0, 0, 0, '2021-10-07 04:25:46'),
(1165, 5, 308, 1, 0, 0, 0, '2021-09-22 20:57:37'),
(1175, 5, 329, 1, 0, 0, 0, '2021-09-22 22:14:39'),
(1188, 1, 53, 1, 1, 0, 0, '2021-09-22 23:24:10'),
(1189, 5, 149, 1, 1, 1, 1, '2021-09-22 22:40:24'),
(1190, 5, 175, 1, 1, 1, 1, '2021-09-22 22:59:48'),
(1191, 5, 243, 1, 0, 1, 0, '2021-09-22 22:59:48'),
(1193, 5, 260, 1, 1, 1, 1, '2021-09-22 22:59:48'),
(1194, 5, 263, 1, 1, 1, 1, '2021-09-22 22:59:48'),
(1195, 5, 312, 1, 0, 1, 0, '2021-09-22 22:59:48'),
(1196, 5, 314, 1, 1, 0, 1, '2021-09-22 22:59:48'),
(1197, 5, 355, 1, 0, 1, 0, '2021-09-22 22:59:48'),
(1204, 9, 138, 1, 0, 0, 0, '2021-10-07 04:51:18'),
(1205, 9, 139, 1, 0, 0, 0, '2021-10-07 04:51:18'),
(1206, 9, 140, 1, 1, 1, 1, '2021-10-23 04:56:32'),
(1207, 9, 142, 1, 1, 1, 1, '2021-10-23 04:56:32'),
(1210, 9, 196, 1, 0, 0, 0, '2021-10-07 04:27:45'),
(1212, 9, 198, 1, 0, 0, 0, '2021-09-22 23:19:30'),
(1213, 9, 220, 1, 0, 0, 0, '2021-10-07 04:27:45'),
(1215, 9, 300, 1, 1, 1, 1, '2021-09-23 01:24:52'),
(1216, 9, 301, 1, 0, 0, 0, '2021-09-22 23:19:30'),
(1217, 9, 308, 1, 0, 0, 0, '2021-09-22 23:19:30'),
(1218, 9, 309, 1, 0, 0, 0, '2021-10-07 04:27:45'),
(1220, 9, 322, 1, 1, 1, 1, '2021-09-23 01:24:52'),
(1224, 9, 326, 1, 0, 0, 0, '2021-10-07 04:27:45'),
(1225, 9, 327, 1, 0, 0, 0, '2021-09-22 23:19:30'),
(1226, 9, 329, 1, 0, 0, 0, '2021-09-22 23:19:30'),
(1235, 1, 191, 1, 0, 0, 0, '2021-09-22 23:49:17'),
(1246, 5, 317, 1, 1, 0, 1, '2021-09-23 00:16:36'),
(1248, 5, 270, 1, 0, 0, 0, '2021-10-07 03:23:02'),
(1250, 5, 102, 1, 1, 1, 1, '2021-09-23 00:32:09'),
(1251, 5, 346, 1, 0, 0, 0, '2021-09-23 00:32:09'),
(1269, 5, 337, 1, 1, 0, 0, '2021-09-23 01:31:48'),
(1278, 5, 236, 1, 0, 0, 0, '2021-10-07 03:26:02'),
(1306, 5, 216, 1, 0, 0, 0, '2021-09-23 01:40:28'),
(1307, 5, 217, 1, 0, 0, 0, '2021-09-23 01:40:28'),
(1317, 5, 86, 1, 0, 0, 0, '2021-10-07 03:29:26'),
(1322, 5, 109, 1, 1, 0, 1, '2021-09-23 19:36:32'),
(1329, 5, 43, 1, 1, 1, 1, '2021-09-30 00:45:54'),
(1330, 5, 44, 1, 0, 0, 0, '2021-09-23 02:12:30'),
(1331, 5, 27, 1, 1, 0, 1, '2021-09-23 02:15:51'),
(1355, 9, 270, 1, 0, 0, 0, '2021-10-07 06:17:12'),
(1358, 9, 236, 1, 0, 0, 0, '2021-10-07 04:42:28'),
(1370, 5, 158, 1, 0, 0, 0, '2021-09-23 19:44:20'),
(1416, 5, 369, 1, 0, 0, 0, '2021-09-23 20:16:25'),
(1426, 5, 180, 1, 0, 0, 0, '2021-09-23 20:23:15'),
(1460, 6, 270, 1, 0, 0, 0, '2021-10-07 03:38:49'),
(1503, 6, 304, 1, 0, 0, 0, '2021-09-27 00:19:54'),
(1509, 6, 319, 1, 0, 0, 0, '2021-10-07 06:03:09'),
(1518, 9, 216, 1, 0, 0, 0, '2021-09-27 00:45:44'),
(1519, 9, 217, 1, 0, 0, 0, '2021-09-27 00:45:44'),
(1579, 2, 155, 1, 0, 0, 0, '2021-09-27 05:41:19'),
(1580, 2, 156, 1, 0, 0, 0, '2021-09-27 05:41:19'),
(1581, 2, 157, 1, 0, 0, 0, '2021-09-27 05:41:19'),
(1582, 2, 158, 1, 0, 0, 0, '2021-09-27 05:41:19'),
(1583, 2, 159, 1, 0, 0, 0, '2021-09-27 05:41:19'),
(1584, 2, 161, 1, 0, 0, 0, '2021-09-27 05:41:19'),
(1585, 2, 162, 1, 0, 0, 0, '2021-09-27 05:41:19'),
(1586, 2, 190, 1, 0, 0, 0, '2021-09-27 05:41:19'),
(1587, 2, 191, 1, 0, 0, 0, '2021-09-27 05:41:19'),
(1588, 2, 238, 1, 0, 0, 0, '2021-09-27 05:41:19'),
(1596, 9, 48, 1, 0, 0, 0, '2021-09-27 06:56:58'),
(1599, 9, 178, 1, 0, 0, 0, '2021-09-27 06:56:58'),
(1603, 9, 182, 1, 0, 0, 0, '2021-09-27 06:56:58'),
(1631, 9, 350, 1, 0, 0, 0, '2021-09-27 06:56:58'),
(1663, 9, 27, 1, 0, 0, 0, '2021-10-23 04:59:59'),
(1664, 9, 43, 1, 1, 1, 1, '2021-09-28 01:25:13'),
(1665, 9, 44, 1, 0, 0, 0, '2021-09-28 01:20:42'),
(1666, 9, 86, 1, 0, 0, 0, '2021-10-07 04:46:07'),
(1671, 9, 109, 1, 1, 0, 1, '2021-09-28 01:58:50'),
(1679, 9, 204, 1, 1, 1, 1, '2021-09-28 03:31:04'),
(1680, 9, 205, 1, 0, 0, 0, '2021-09-28 03:31:04'),
(1697, 6, 152, 1, 1, 1, 1, '2021-09-28 05:21:22'),
(1698, 6, 153, 1, 1, 1, 1, '2021-09-28 05:21:22'),
(1699, 6, 171, 1, 1, 1, 1, '2021-09-28 05:21:22'),
(1700, 6, 244, 1, 0, 1, 0, '2021-09-28 05:21:22'),
(1701, 6, 261, 1, 1, 1, 1, '2021-09-28 05:21:22'),
(1702, 6, 262, 1, 1, 1, 1, '2021-09-28 05:21:22'),
(1703, 6, 315, 1, 1, 0, 1, '2021-09-28 05:21:22'),
(1704, 6, 353, 1, 0, 1, 0, '2021-09-28 05:21:22'),
(1705, 6, 354, 1, 0, 1, 0, '2021-09-28 05:21:22'),
(1713, 6, 236, 1, 0, 0, 0, '2021-10-07 03:46:01'),
(1716, 6, 176, 1, 1, 1, 1, '2021-10-07 06:04:58'),
(1719, 6, 289, 1, 1, 1, 1, '2021-09-28 06:47:58'),
(1720, 6, 290, 1, 1, 1, 1, '2021-09-28 06:47:58'),
(1739, 6, 43, 1, 1, 1, 1, '2021-09-28 07:53:01'),
(1740, 6, 44, 1, 0, 0, 0, '2021-09-28 07:51:03'),
(1741, 6, 27, 1, 1, 0, 1, '2021-09-28 07:57:40'),
(1742, 6, 118, 1, 0, 0, 0, '2021-09-28 07:59:22'),
(1747, 6, 159, 1, 0, 0, 0, '2021-09-28 07:59:22'),
(1782, 2, 205, 1, 0, 0, 0, '2021-09-29 03:01:52'),
(1823, 6, 338, 1, 0, 0, 0, '2021-09-29 05:47:03'),
(1824, 6, 339, 1, 1, 0, 0, '2021-09-29 06:07:31'),
(1831, 2, 318, 1, 1, 0, 1, '2021-09-29 06:26:26'),
(1838, 6, 181, 1, 0, 0, 0, '2021-09-29 06:27:14'),
(1872, 6, 102, 1, 1, 1, 1, '2021-09-29 06:31:52'),
(1873, 6, 86, 1, 0, 0, 0, '2021-10-07 03:48:51'),
(1878, 6, 109, 1, 1, 0, 1, '2021-09-29 07:05:03'),
(1900, 6, 329, 1, 0, 0, 0, '2021-09-29 07:59:38'),
(1906, 5, 370, 1, 0, 0, 0, '2021-09-30 02:11:43'),
(1908, 4, 146, 1, 1, 1, 1, '2021-10-07 02:24:11'),
(1909, 4, 148, 1, 1, 1, 1, '2021-10-07 02:24:11'),
(1910, 4, 170, 1, 1, 1, 1, '2021-10-07 02:24:11'),
(1911, 4, 200, 1, 1, 0, 1, '2021-10-07 02:24:11'),
(1912, 4, 201, 1, 0, 1, 0, '2021-09-30 05:42:41'),
(1913, 4, 224, 1, 0, 0, 0, '2021-09-30 05:36:57'),
(1914, 4, 225, 1, 1, 0, 1, '2021-10-07 02:24:11'),
(1915, 4, 226, 1, 1, 1, 1, '2021-10-07 02:24:11'),
(1916, 4, 227, 1, 1, 1, 1, '2021-10-07 02:24:11'),
(1917, 4, 291, 1, 1, 1, 1, '2021-10-07 02:24:11'),
(1918, 4, 292, 1, 1, 1, 1, '2021-10-07 02:24:11'),
(1919, 4, 313, 1, 1, 0, 1, '2021-10-07 02:24:11'),
(1931, 4, 236, 1, 0, 0, 0, '2021-10-07 02:30:35'),
(1934, 4, 118, 1, 0, 0, 0, '2021-10-01 00:51:45'),
(1937, 4, 157, 1, 0, 0, 0, '2021-10-01 00:51:45'),
(1944, 4, 238, 1, 0, 0, 0, '2021-10-01 00:51:45'),
(1957, 4, 256, 1, 0, 0, 0, '2021-10-01 00:54:01'),
(1976, 4, 102, 1, 1, 1, 1, '2021-10-01 01:33:49'),
(1977, 4, 274, 1, 0, 0, 0, '2021-10-01 01:36:40'),
(1980, 9, 137, 1, 1, 1, 1, '2021-10-23 04:54:52'),
(1981, 2, 173, 1, 1, 1, 1, '2021-10-07 05:34:24'),
(1982, 2, 347, 1, 0, 1, 1, '2021-10-07 05:34:24'),
(1995, 1, 196, 1, 1, 1, 1, '2021-10-07 04:55:23'),
(1996, 1, 323, 1, 1, 1, 1, '2021-10-07 04:55:23'),
(1997, 1, 324, 1, 1, 1, 1, '2021-10-07 04:55:23'),
(1998, 1, 325, 1, 1, 1, 1, '2021-10-07 04:55:23'),
(1999, 1, 236, 1, 1, 1, 1, '2021-10-07 00:36:12'),
(2001, 2, 270, 1, 0, 0, 0, '2021-10-07 01:25:46'),
(2002, 2, 236, 1, 1, 1, 0, '2021-10-07 01:28:54'),
(2003, 2, 266, 1, 0, 0, 0, '2021-10-07 01:43:54'),
(2019, 8, 132, 1, 1, 1, 1, '2021-10-07 04:00:10'),
(2020, 8, 135, 1, 1, 1, 1, '2021-10-07 04:00:10'),
(2021, 8, 218, 1, 0, 0, 0, '2021-10-07 04:00:10'),
(2022, 8, 219, 1, 0, 0, 0, '2021-10-07 04:00:10'),
(2023, 8, 221, 1, 1, 1, 1, '2021-10-07 06:09:52'),
(2024, 8, 222, 1, 1, 0, 1, '2021-10-07 06:09:52'),
(2025, 8, 138, 1, 1, 1, 1, '2021-10-07 04:00:35'),
(2026, 8, 139, 1, 1, 1, 1, '2021-10-07 04:00:35'),
(2027, 8, 143, 1, 1, 1, 1, '2021-10-07 06:10:36'),
(2028, 8, 144, 1, 1, 0, 1, '2021-10-07 06:10:36'),
(2029, 8, 326, 1, 0, 0, 0, '2021-10-07 04:01:17'),
(2031, 8, 196, 1, 1, 1, 1, '2021-10-07 06:10:36'),
(2032, 8, 198, 1, 0, 0, 0, '2021-10-07 04:03:01'),
(2034, 4, 202, 1, 0, 0, 0, '2021-10-07 04:03:15'),
(2035, 8, 146, 1, 0, 0, 0, '2021-10-07 04:03:54'),
(2036, 8, 148, 1, 0, 0, 0, '2021-10-07 04:03:54'),
(2037, 8, 200, 1, 0, 0, 0, '2021-10-07 04:03:54'),
(2038, 8, 225, 1, 0, 0, 0, '2021-10-07 04:03:54'),
(2039, 8, 149, 1, 0, 0, 0, '2021-10-07 04:04:27'),
(2042, 8, 168, 1, 0, 0, 0, '2021-10-07 04:04:59'),
(2043, 8, 270, 1, 0, 0, 0, '2021-10-07 04:05:25'),
(2044, 8, 173, 1, 0, 0, 0, '2021-10-07 04:06:20'),
(2045, 8, 347, 1, 0, 0, 0, '2021-10-07 04:06:20'),
(2046, 8, 330, 1, 0, 0, 0, '2021-10-07 04:08:02'),
(2047, 8, 332, 1, 0, 0, 0, '2021-10-07 04:08:02'),
(2048, 8, 334, 1, 0, 0, 0, '2021-10-07 04:08:02'),
(2049, 8, 336, 1, 0, 0, 0, '2021-10-07 04:08:02'),
(2050, 8, 338, 1, 0, 0, 0, '2021-10-07 04:08:02'),
(2051, 8, 340, 1, 0, 0, 0, '2021-10-07 04:08:02'),
(2052, 8, 342, 1, 0, 0, 0, '2021-10-07 04:08:02'),
(2053, 8, 236, 1, 1, 1, 0, '2021-10-07 04:08:58'),
(2055, 8, 165, 1, 1, 1, 1, '2021-10-07 06:12:31'),
(2056, 8, 166, 1, 1, 1, 1, '2021-10-07 06:12:31'),
(2057, 8, 80, 1, 1, 1, 1, '2021-10-07 04:11:10'),
(2058, 8, 81, 1, 1, 1, 1, '2021-10-07 04:11:10'),
(2059, 8, 82, 1, 1, 1, 1, '2021-10-07 04:11:10'),
(2060, 8, 83, 1, 1, 1, 1, '2021-10-07 04:11:10'),
(2061, 8, 84, 1, 1, 1, 1, '2021-10-07 04:11:10'),
(2062, 8, 85, 1, 1, 1, 1, '2021-10-07 04:11:10'),
(2063, 8, 204, 1, 1, 1, 1, '2021-10-07 04:11:10'),
(2064, 8, 205, 1, 0, 0, 0, '2021-10-07 04:11:10'),
(2065, 8, 214, 1, 0, 0, 0, '2021-10-07 04:11:28'),
(2066, 8, 215, 1, 0, 0, 0, '2021-10-07 04:11:28'),
(2067, 8, 86, 1, 0, 0, 0, '2021-10-07 04:11:47'),
(2068, 8, 109, 1, 1, 0, 1, '2021-10-07 04:11:47'),
(2069, 8, 31, 1, 0, 0, 0, '2021-10-07 04:12:14'),
(2070, 8, 32, 1, 0, 0, 0, '2021-10-07 04:12:14'),
(2071, 8, 48, 1, 0, 0, 0, '2021-10-07 04:13:52'),
(2072, 8, 89, 1, 0, 0, 0, '2021-10-07 04:13:52'),
(2073, 8, 178, 1, 0, 0, 0, '2021-10-07 04:13:52'),
(2074, 8, 180, 1, 0, 0, 0, '2021-10-07 04:13:52'),
(2075, 8, 181, 1, 0, 0, 0, '2021-10-07 04:13:52'),
(2076, 8, 182, 1, 0, 0, 0, '2021-10-07 04:13:52'),
(2077, 8, 207, 1, 0, 0, 0, '2021-10-07 04:13:52'),
(2078, 8, 208, 1, 0, 0, 0, '2021-10-07 04:13:52'),
(2079, 8, 209, 1, 0, 0, 0, '2021-10-07 04:13:52'),
(2080, 8, 253, 1, 0, 0, 0, '2021-10-07 04:13:52'),
(2081, 8, 254, 1, 0, 0, 0, '2021-10-07 04:13:52'),
(2082, 8, 255, 1, 0, 0, 0, '2021-10-07 04:13:52'),
(2083, 8, 118, 1, 0, 0, 0, '2021-10-07 04:14:18'),
(2084, 8, 238, 1, 0, 0, 0, '2021-10-07 04:14:18'),
(2085, 8, 102, 1, 1, 1, 1, '2021-10-07 04:14:38'),
(2086, 9, 321, 1, 0, 0, 0, '2021-10-07 04:25:46'),
(2093, 1, 132, 1, 1, 1, 1, '2021-10-07 04:54:53'),
(2094, 1, 198, 1, 0, 0, 0, '2021-10-07 04:55:23'),
(2095, 1, 327, 1, 0, 0, 0, '2021-10-07 04:55:23'),
(2096, 1, 329, 1, 0, 0, 0, '2021-10-07 04:55:23'),
(2108, 1, 294, 1, 1, 1, 1, '2021-10-07 04:56:29'),
(2109, 1, 296, 1, 1, 1, 1, '2021-10-07 04:56:29'),
(2110, 1, 297, 1, 0, 0, 0, '2021-10-07 04:56:29'),
(2111, 1, 298, 1, 0, 0, 0, '2021-10-07 04:56:29'),
(2112, 1, 129, 0, 1, 0, 1, '2021-10-07 04:56:43'),
(2113, 1, 27, 1, 1, 0, 1, '2021-10-07 04:56:54'),
(2114, 1, 155, 1, 0, 0, 0, '2021-10-07 04:57:41'),
(2115, 1, 156, 1, 0, 0, 0, '2021-10-07 04:57:41'),
(2116, 1, 157, 1, 0, 0, 0, '2021-10-07 04:57:41'),
(2117, 1, 158, 1, 0, 0, 0, '2021-10-07 04:57:41'),
(2118, 1, 159, 1, 0, 0, 0, '2021-10-07 04:57:41'),
(2119, 1, 161, 1, 0, 0, 0, '2021-10-07 04:57:41'),
(2120, 1, 162, 1, 0, 0, 0, '2021-10-07 04:57:41'),
(2121, 1, 190, 1, 0, 0, 0, '2021-10-07 04:57:41'),
(2122, 1, 238, 1, 0, 0, 0, '2021-10-07 04:57:41'),
(2127, 1, 320, 1, 0, 0, 0, '2021-10-07 05:01:34'),
(2128, 1, 321, 1, 0, 0, 0, '2021-10-07 05:01:34'),
(2135, 1, 371, 1, 0, 0, 0, '2021-10-07 05:05:30'),
(2150, 1, 280, 1, 1, 1, 1, '2021-10-07 05:29:08'),
(2151, 1, 369, 1, 0, 0, 0, '2021-10-07 05:30:23'),
(2152, 1, 370, 1, 0, 0, 0, '2021-10-07 05:30:23'),
(2153, 1, 281, 1, 0, 0, 0, '2021-10-07 05:30:36'),
(2154, 1, 282, 1, 0, 0, 0, '2021-10-07 05:30:36'),
(2155, 2, 321, 1, 0, 0, 0, '2021-10-07 05:31:49'),
(2156, 2, 197, 1, 0, 0, 0, '2021-10-07 05:33:02'),
(2157, 2, 248, 1, 0, 0, 0, '2021-10-07 05:33:02'),
(2158, 2, 264, 1, 1, 0, 1, '2021-10-07 05:34:52'),
(2159, 2, 247, 1, 0, 0, 0, '2021-10-07 05:35:28'),
(2160, 2, 281, 1, 0, 0, 0, '2021-10-07 05:40:09'),
(2161, 2, 282, 1, 0, 0, 0, '2021-10-07 05:40:09'),
(2162, 2, 371, 1, 0, 0, 0, '2021-10-07 05:40:26'),
(2163, 3, 257, 1, 0, 0, 0, '2021-10-07 05:41:20'),
(2164, 3, 355, 1, 0, 0, 0, '2021-10-07 05:41:20'),
(2165, 3, 153, 1, 0, 0, 0, '2021-10-07 05:41:40'),
(2166, 3, 244, 1, 0, 0, 0, '2021-10-07 05:41:40'),
(2167, 3, 354, 1, 0, 0, 0, '2021-10-07 05:41:40'),
(2168, 3, 202, 1, 0, 0, 0, '2021-10-07 05:42:06'),
(2169, 3, 246, 1, 0, 0, 0, '2021-10-07 05:42:06'),
(2170, 3, 237, 1, 0, 0, 0, '2021-10-07 05:43:04'),
(2171, 3, 240, 1, 0, 1, 0, '2021-10-07 05:43:28'),
(2172, 3, 242, 1, 0, 1, 0, '2021-10-07 05:43:28'),
(2173, 3, 129, 0, 1, 0, 1, '2021-10-07 05:43:59'),
(2174, 3, 183, 1, 0, 0, 0, '2021-10-07 05:46:03'),
(2175, 3, 188, 1, 0, 0, 0, '2021-10-07 05:46:03'),
(2176, 3, 206, 1, 0, 0, 0, '2021-10-07 05:46:03'),
(2177, 3, 207, 1, 0, 0, 0, '2021-10-07 05:46:03'),
(2178, 3, 208, 1, 0, 0, 0, '2021-10-07 05:46:03'),
(2179, 3, 209, 1, 0, 0, 0, '2021-10-07 05:46:03'),
(2180, 3, 211, 1, 0, 0, 0, '2021-10-07 05:46:03'),
(2181, 3, 258, 1, 0, 0, 0, '2021-10-07 05:46:03'),
(2182, 3, 271, 1, 0, 0, 0, '2021-10-07 05:46:03'),
(2183, 3, 350, 1, 0, 0, 0, '2021-10-07 05:46:03'),
(2184, 3, 369, 1, 0, 0, 0, '2021-10-07 05:46:03'),
(2185, 3, 238, 1, 0, 0, 0, '2021-10-07 05:46:28'),
(2186, 3, 362, 1, 0, 0, 0, '2021-10-07 05:46:52'),
(2187, 3, 363, 1, 1, 1, 1, '2021-10-07 05:47:07'),
(2188, 3, 366, 1, 0, 0, 0, '2021-10-07 05:46:52'),
(2189, 3, 367, 1, 1, 1, 1, '2021-10-07 05:47:07'),
(2190, 3, 371, 1, 0, 0, 0, '2021-10-07 05:47:23'),
(2194, 4, 132, 1, 0, 0, 0, '2021-10-07 05:50:59'),
(2195, 4, 138, 1, 0, 0, 0, '2021-10-07 05:51:05'),
(2196, 4, 264, 1, 1, 0, 1, '2021-10-07 05:52:38'),
(2198, 4, 371, 1, 0, 0, 0, '2021-10-07 05:56:00'),
(2201, 5, 132, 1, 0, 0, 0, '2021-10-07 05:56:53'),
(2202, 5, 304, 1, 0, 0, 0, '2021-10-07 05:56:53'),
(2204, 5, 138, 1, 0, 0, 0, '2021-10-07 05:57:32'),
(2205, 5, 139, 1, 0, 0, 0, '2021-10-07 05:57:32'),
(2206, 5, 307, 1, 0, 0, 0, '2021-10-07 05:57:47'),
(2207, 5, 257, 1, 1, 1, 1, '2021-10-07 05:58:05'),
(2209, 5, 168, 1, 1, 1, 1, '2021-10-07 05:58:48'),
(2210, 5, 169, 1, 1, 1, 1, '2021-10-07 05:58:48'),
(2211, 5, 202, 1, 1, 0, 1, '2021-10-07 05:58:48'),
(2212, 5, 246, 1, 0, 0, 0, '2021-10-07 05:58:48'),
(2213, 5, 293, 1, 1, 1, 1, '2021-10-07 05:58:48'),
(2214, 5, 302, 1, 1, 0, 1, '2021-10-07 05:58:48'),
(2215, 5, 311, 1, 1, 1, 1, '2021-10-07 05:58:48'),
(2216, 5, 336, 1, 0, 0, 0, '2021-10-07 05:59:04'),
(2217, 5, 274, 1, 0, 0, 0, '2021-10-07 06:00:38'),
(2219, 5, 183, 1, 0, 0, 0, '2021-10-07 06:01:42'),
(2220, 5, 211, 1, 0, 0, 0, '2021-10-07 06:01:42'),
(2221, 5, 371, 1, 0, 0, 0, '2021-10-07 06:02:10'),
(2222, 6, 132, 1, 0, 0, 0, '2021-10-07 06:03:09'),
(2223, 6, 135, 1, 0, 0, 0, '2021-10-07 06:03:09'),
(2224, 6, 138, 1, 0, 0, 0, '2021-10-07 06:03:45'),
(2225, 6, 139, 1, 0, 0, 0, '2021-10-07 06:03:45'),
(2226, 6, 308, 1, 0, 0, 0, '2021-10-07 06:03:45'),
(2227, 6, 309, 1, 0, 0, 0, '2021-10-07 06:03:45'),
(2228, 6, 203, 1, 1, 1, 1, '2021-10-07 06:04:58'),
(2229, 6, 264, 1, 1, 0, 1, '2021-10-07 06:04:58'),
(2231, 6, 274, 1, 0, 0, 0, '2021-10-07 06:06:19'),
(2232, 6, 371, 1, 0, 0, 0, '2021-10-07 06:07:27'),
(2233, 8, 304, 1, 0, 0, 0, '2021-10-07 06:09:52'),
(2234, 8, 305, 1, 0, 1, 0, '2021-10-07 06:09:52'),
(2235, 8, 306, 1, 0, 0, 0, '2021-10-07 06:09:52'),
(2236, 8, 307, 1, 0, 0, 0, '2021-10-07 06:09:52'),
(2237, 8, 319, 1, 1, 1, 1, '2021-10-07 06:09:52'),
(2238, 8, 320, 1, 0, 0, 0, '2021-10-07 06:09:52'),
(2239, 8, 301, 1, 0, 0, 0, '2021-10-07 06:10:36'),
(2240, 8, 309, 1, 0, 1, 0, '2021-10-07 06:10:36'),
(2241, 8, 310, 1, 0, 0, 0, '2021-10-07 06:10:36'),
(2242, 8, 329, 1, 0, 0, 0, '2021-10-07 06:10:36'),
(2243, 8, 257, 1, 0, 0, 0, '2021-10-07 06:10:57'),
(2244, 8, 153, 1, 0, 0, 0, '2021-10-07 06:11:07'),
(2245, 8, 169, 1, 0, 0, 0, '2021-10-07 06:11:37'),
(2246, 8, 202, 1, 0, 0, 0, '2021-10-07 06:11:37'),
(2247, 8, 302, 1, 0, 0, 0, '2021-10-07 06:11:37'),
(2248, 8, 311, 1, 0, 0, 0, '2021-10-07 06:11:37'),
(2249, 2, 348, 1, 0, 1, 0, '2021-10-07 06:17:44'),
(2250, 8, 247, 1, 1, 1, 1, '2021-10-07 06:12:31'),
(2251, 8, 318, 1, 1, 0, 1, '2021-10-07 06:12:31'),
(2252, 8, 294, 1, 1, 1, 1, '2021-10-07 06:13:02'),
(2253, 8, 295, 1, 0, 1, 0, '2021-10-07 06:13:02'),
(2254, 8, 296, 1, 1, 1, 1, '2021-10-07 06:13:02'),
(2255, 8, 297, 1, 0, 0, 0, '2021-10-07 06:13:02'),
(2256, 8, 298, 1, 0, 0, 0, '2021-10-07 06:13:02'),
(2257, 2, 56, 1, 0, 0, 0, '2021-10-07 06:13:19'),
(2258, 8, 43, 1, 1, 1, 1, '2021-10-07 06:13:22'),
(2259, 8, 44, 1, 0, 0, 0, '2021-10-07 06:13:22'),
(2260, 8, 27, 1, 1, 0, 1, '2021-10-07 06:13:28'),
(2261, 8, 274, 1, 0, 0, 0, '2021-10-07 06:13:45'),
(2262, 2, 54, 1, 0, 0, 0, '2021-10-07 06:13:47'),
(2263, 8, 183, 1, 0, 0, 0, '2021-10-07 06:15:00'),
(2264, 8, 256, 1, 0, 0, 0, '2021-10-07 06:15:00'),
(2265, 8, 258, 1, 0, 0, 0, '2021-10-07 06:15:00'),
(2266, 8, 259, 1, 0, 0, 0, '2021-10-07 06:15:00'),
(2267, 8, 350, 1, 0, 0, 0, '2021-10-07 06:15:00'),
(2268, 8, 162, 1, 0, 0, 0, '2021-10-07 06:15:18'),
(2269, 8, 371, 1, 0, 0, 0, '2021-10-07 06:15:32'),
(2271, 9, 274, 1, 0, 0, 0, '2021-10-07 06:19:59'),
(2272, 9, 118, 1, 0, 0, 0, '2021-10-07 06:20:29'),
(2273, 9, 238, 1, 0, 0, 0, '2021-10-07 06:20:29'),
(2274, 9, 371, 1, 0, 0, 0, '2021-10-07 06:20:40'),
(2275, 1, 218, 1, 0, 0, 0, '2021-10-07 06:20:53'),
(2276, 1, 330, 1, 0, 0, 0, '2021-10-22 00:27:48'),
(2277, 1, 331, 1, 1, 0, 0, '2021-10-22 00:27:48'),
(2278, 1, 332, 1, 0, 0, 0, '2021-10-22 00:27:48'),
(2279, 1, 333, 1, 1, 0, 0, '2021-10-22 00:27:48'),
(2280, 1, 334, 1, 0, 0, 0, '2021-10-22 00:27:48'),
(2281, 1, 335, 1, 1, 0, 0, '2021-10-22 00:27:48'),
(2282, 1, 336, 1, 0, 0, 0, '2021-10-22 00:27:48'),
(2283, 1, 337, 1, 1, 0, 0, '2021-10-22 00:27:48'),
(2284, 1, 338, 1, 0, 0, 0, '2021-10-22 00:27:48'),
(2285, 1, 339, 1, 1, 0, 0, '2021-10-22 00:27:48'),
(2286, 1, 340, 1, 0, 0, 0, '2021-10-22 00:27:48'),
(2287, 1, 341, 1, 1, 0, 0, '2021-10-22 00:27:48'),
(2288, 1, 342, 1, 0, 0, 0, '2021-10-22 00:27:48'),
(2289, 1, 343, 1, 1, 0, 0, '2021-10-22 00:27:48'),
(2290, 1, 344, 1, 0, 0, 0, '2021-10-22 00:27:48'),
(2291, 1, 345, 1, 0, 0, 0, '2021-10-22 00:27:48'),
(2292, 1, 372, 1, NULL, NULL, NULL, '2021-10-29 07:41:42'),
(2293, 1, 373, 1, NULL, NULL, NULL, '2021-10-29 07:42:20'),
(2294, 1, 374, 1, 1, 1, 1, '2021-10-29 07:45:25'),
(2295, 1, 375, 1, 1, 1, 1, '2021-10-29 07:45:25'),
(2296, 1, 386, 1, 1, 0, 1, '2021-11-10 06:22:22'),
(2297, 3, 386, 1, 1, 0, 1, '2021-11-10 06:22:22'),
(2298, 1, 387, 1, 0, 0, 0, '2021-11-10 06:24:10'),
(2299, 3, 387, 1, 0, 0, 0, '2021-11-10 06:24:10'),
(2300, 1, 419, 1, 1, 1, 1, '2024-08-20 10:19:15'),
(2301, 1, 415, 1, 0, 0, 0, '2024-08-20 10:19:36'),
(2302, 1, 416, 1, 1, 1, 1, '2024-08-20 10:19:36'),
(2303, 1, 417, 1, 1, 1, 1, '2024-08-20 10:19:36'),
(2304, 1, 418, 1, 1, 1, 1, '2024-08-20 10:19:36'),
(2305, 1, 414, 1, 0, 1, 0, '2024-08-20 10:20:08'),
(2306, 1, 398, 1, 1, 1, 1, '2024-08-20 10:21:04'),
(2307, 1, 402, 1, 0, 0, 0, '2024-08-20 10:21:04'),
(2308, 1, 413, 1, 0, 1, 0, '2024-08-20 10:21:04'),
(2309, 1, 409, 1, 0, 0, 1, '2024-08-20 10:21:50'),
(2310, 1, 410, 1, 1, 0, 1, '2024-08-20 10:21:50'),
(2311, 1, 411, 1, 0, 0, 0, '2024-08-20 10:21:50'),
(2312, 1, 412, 1, 0, 0, 0, '2024-08-20 10:21:50'),
(2313, 1, 406, 1, 1, 1, 1, '2024-08-20 10:22:23'),
(2314, 1, 407, 1, 1, 1, 1, '2024-08-20 10:22:23'),
(2315, 1, 408, 1, 1, 1, 1, '2024-08-20 10:22:23'),
(2316, 1, 405, 1, 1, 1, 1, '2024-08-20 10:22:33'),
(2317, 1, 393, 1, 1, 1, 1, '2024-08-20 10:23:11'),
(2318, 1, 394, 1, 1, 1, 1, '2024-08-20 10:23:11'),
(2319, 1, 395, 1, 1, 1, 1, '2024-08-20 10:23:11'),
(2320, 1, 396, 1, 1, 1, 1, '2024-08-20 10:23:11'),
(2321, 1, 401, 1, 0, 0, 0, '2024-08-20 10:23:11'),
(2322, 1, 403, 1, 0, 0, 0, '2024-08-20 10:23:11'),
(2323, 1, 404, 1, 0, 0, 0, '2024-08-20 10:23:11'),
(2324, 1, 392, 1, 1, 1, 1, '2024-08-20 10:25:03'),
(2325, 1, 400, 1, 0, 0, 0, '2024-08-20 10:25:23'),
(2326, 1, 390, 1, 0, 0, 0, '2024-08-20 10:25:53'),
(2327, 1, 399, 1, 0, 0, 0, '2024-08-20 10:25:53'),
(2328, 1, 388, 1, 1, 1, 1, '2024-08-20 10:27:59'),
(2329, 1, 389, 1, 1, 1, 1, '2024-08-20 10:27:59'),
(2330, 1, 391, 1, 1, 1, 1, '2024-08-20 10:27:59'),
(2331, 1, 397, 1, 0, 0, 0, '2024-08-20 10:31:13');

-- --------------------------------------------------------

--
-- Table structure for table `sch_settings`
--

CREATE TABLE `sch_settings` (
  `id` int NOT NULL,
  `base_url` varchar(500) DEFAULT NULL,
  `folder_path` text,
  `name` varchar(100) DEFAULT NULL,
  `biometric` int DEFAULT '0',
  `biometric_device` text,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text,
  `start_month` varchar(100) NOT NULL,
  `session_id` int DEFAULT NULL,
  `lang_id` int DEFAULT NULL,
  `languages` varchar(255) NOT NULL DEFAULT '["4"]',
  `dise_code` varchar(50) DEFAULT NULL,
  `date_format` varchar(50) NOT NULL,
  `time_format` varchar(20) DEFAULT '24-hour',
  `currency` varchar(50) NOT NULL,
  `currency_symbol` varchar(50) NOT NULL,
  `is_rtl` varchar(10) DEFAULT 'disabled',
  `timezone` varchar(30) DEFAULT 'UTC',
  `image` varchar(100) DEFAULT NULL,
  `mini_logo` varchar(200) NOT NULL,
  `theme` varchar(200) NOT NULL DEFAULT 'default.jpg',
  `credit_limit` varchar(255) DEFAULT NULL,
  `opd_record_month` varchar(50) DEFAULT NULL,
  `is_active` varchar(255) DEFAULT 'no',
  `cron_secret_key` varchar(100) NOT NULL,
  `doctor_restriction` varchar(100) NOT NULL,
  `superadmin_restriction` varchar(200) NOT NULL,
  `patient_panel` varchar(50) NOT NULL,
  `scan_code_type` varchar(50) NOT NULL DEFAULT 'barcode',
  `mobile_api_url` varchar(200) NOT NULL,
  `app_primary_color_code` varchar(50) NOT NULL,
  `app_secondary_color_code` varchar(50) NOT NULL,
  `app_logo` varchar(200) NOT NULL,
  `zoom_api_key` varchar(200) NOT NULL,
  `zoom_api_secret` varchar(200) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `sch_settings`
--

INSERT INTO `sch_settings` (`id`, `base_url`, `folder_path`, `name`, `biometric`, `biometric_device`, `email`, `phone`, `address`, `start_month`, `session_id`, `lang_id`, `languages`, `dise_code`, `date_format`, `time_format`, `currency`, `currency_symbol`, `is_rtl`, `timezone`, `image`, `mini_logo`, `theme`, `credit_limit`, `opd_record_month`, `is_active`, `cron_secret_key`, `doctor_restriction`, `superadmin_restriction`, `patient_panel`, `scan_code_type`, `mobile_api_url`, `app_primary_color_code`, `app_secondary_color_code`, `app_logo`, `zoom_api_key`, `zoom_api_secret`, `created_at`) VALUES
(1, NULL, NULL, 'Your Hospital Name', 0, NULL, 'Your Hospital Email', 'Your Hospital Phone', 'Your Hospital Address', '', NULL, 4, '[\"4\"]', 'Your Hospital Code', 'm/d/Y', '12-hour', 'USD', '$', 'disabled', 'UTC', '0.png', '0mini_logo.png', 'default.jpg', '20000', '1', 'no', '', 'disabled', 'enabled', 'enabled', 'barcode', '', '#424242', '#eeeeee', '0app_logo.png', '', '', '2021-10-21 23:58:24');

-- --------------------------------------------------------

--
-- Table structure for table `send_notification`
--

CREATE TABLE `send_notification` (
  `id` int NOT NULL,
  `title` varchar(50) DEFAULT NULL,
  `publish_date` date DEFAULT NULL,
  `date` date DEFAULT NULL,
  `message` text,
  `visible_staff` varchar(10) NOT NULL DEFAULT 'no',
  `visible_patient` varchar(10) NOT NULL DEFAULT 'no',
  `created_by` varchar(60) DEFAULT NULL,
  `created_id` int DEFAULT NULL,
  `is_active` varchar(10) DEFAULT 'no',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `share_contents`
--

CREATE TABLE `share_contents` (
  `id` int NOT NULL,
  `send_to` varchar(50) DEFAULT NULL,
  `title` text,
  `share_date` date DEFAULT NULL,
  `valid_upto` date DEFAULT NULL,
  `description` text,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `share_content_for`
--

CREATE TABLE `share_content_for` (
  `id` int NOT NULL,
  `group_id` varchar(20) DEFAULT NULL,
  `patient_id` int DEFAULT NULL,
  `staff_id` int DEFAULT NULL,
  `share_content_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `share_upload_contents`
--

CREATE TABLE `share_upload_contents` (
  `id` int NOT NULL,
  `upload_content_id` int DEFAULT NULL,
  `share_content_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `shift_details`
--

CREATE TABLE `shift_details` (
  `id` int NOT NULL,
  `staff_id` int DEFAULT NULL,
  `consult_duration` int DEFAULT NULL,
  `charge_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `sms_config`
--

CREATE TABLE `sms_config` (
  `id` int NOT NULL,
  `type` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `api_id` varchar(100) NOT NULL,
  `authkey` varchar(100) NOT NULL,
  `senderid` varchar(100) NOT NULL,
  `contact` text,
  `username` varchar(150) DEFAULT NULL,
  `url` varchar(150) DEFAULT NULL,
  `password` varchar(150) DEFAULT NULL,
  `is_active` varchar(10) DEFAULT 'disabled',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `source`
--

CREATE TABLE `source` (
  `id` int NOT NULL,
  `source` varchar(100) NOT NULL,
  `description` mediumtext,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `specialist`
--

CREATE TABLE `specialist` (
  `id` int NOT NULL,
  `specialist_name` varchar(200) NOT NULL,
  `is_active` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int NOT NULL,
  `employee_id` varchar(200) DEFAULT NULL,
  `lang_id` int NOT NULL,
  `department_id` int DEFAULT NULL,
  `staff_designation_id` int DEFAULT NULL,
  `specialist` varchar(200) NOT NULL,
  `qualification` varchar(200) NOT NULL,
  `work_exp` varchar(200) NOT NULL,
  `specialization` varchar(200) NOT NULL,
  `name` varchar(200) NOT NULL,
  `surname` varchar(200) NOT NULL,
  `father_name` varchar(200) NOT NULL,
  `mother_name` varchar(200) NOT NULL,
  `contact_no` varchar(200) NOT NULL,
  `emergency_contact_no` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `dob` date DEFAULT NULL,
  `marital_status` varchar(100) NOT NULL,
  `date_of_joining` date DEFAULT NULL,
  `date_of_leaving` date DEFAULT NULL,
  `local_address` varchar(300) NOT NULL,
  `permanent_address` varchar(200) NOT NULL,
  `note` varchar(200) NOT NULL,
  `image` varchar(200) NOT NULL,
  `password` varchar(250) NOT NULL,
  `gender` varchar(50) NOT NULL,
  `blood_group` varchar(100) NOT NULL,
  `account_title` varchar(200) NOT NULL,
  `bank_account_no` varchar(200) NOT NULL,
  `bank_name` varchar(200) NOT NULL,
  `ifsc_code` varchar(200) NOT NULL,
  `bank_branch` varchar(100) NOT NULL,
  `payscale` varchar(200) NOT NULL,
  `basic_salary` varchar(200) NOT NULL,
  `epf_no` varchar(200) NOT NULL,
  `contract_type` varchar(100) NOT NULL,
  `shift` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL,
  `facebook` varchar(200) NOT NULL,
  `twitter` varchar(200) NOT NULL,
  `linkedin` varchar(200) NOT NULL,
  `instagram` varchar(200) NOT NULL,
  `resume` varchar(200) NOT NULL,
  `joining_letter` varchar(200) NOT NULL,
  `resignation_letter` varchar(200) NOT NULL,
  `other_document_name` varchar(200) NOT NULL,
  `other_document_file` varchar(200) NOT NULL,
  `user_id` int NOT NULL,
  `is_active` int NOT NULL,
  `verification_code` varchar(100) NOT NULL,
  `zoom_api_key` varchar(100) NOT NULL,
  `zoom_api_secret` varchar(100) NOT NULL,
  `pan_number` varchar(30) NOT NULL,
  `identification_number` varchar(30) NOT NULL,
  `local_identification_number` varchar(30) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `staff_attendance`
--

CREATE TABLE `staff_attendance` (
  `id` int NOT NULL,
  `date` date NOT NULL,
  `staff_id` int DEFAULT NULL,
  `staff_attendance_type_id` int DEFAULT NULL,
  `biometric_attendence` int DEFAULT '0',
  `biometric_device_data` text,
  `user_agent` varchar(255) DEFAULT NULL,
  `remark` varchar(200) NOT NULL,
  `is_active` int NOT NULL,
  `in_time` time DEFAULT NULL,
  `out_time` time DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `staff_attendance_type`
--

CREATE TABLE `staff_attendance_type` (
  `id` int NOT NULL,
  `type` varchar(200) NOT NULL,
  `key_value` varchar(200) NOT NULL,
  `is_active` varchar(50) NOT NULL,
  `long_lang_name` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `long_name_style` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `for_schedule` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `staff_attendance_type`
--

INSERT INTO `staff_attendance_type` (`id`, `type`, `key_value`, `is_active`, `long_lang_name`, `long_name_style`, `for_schedule`, `created_at`, `updated_at`) VALUES
(1, 'Present', '<b class=\"text text-success\">P</b>', 'yes', 'present', NULL, 1, '2024-08-21 09:29:55', NULL),
(2, 'Late', '<b class=\"text text-warning\">L</b>', 'yes', 'late', NULL, 1, '2024-08-21 09:29:55', NULL),
(3, 'Absent', '<b class=\"text text-danger\">A</b>', 'yes', 'absent', NULL, 0, '2024-08-21 09:29:55', NULL),
(4, 'Half Day', '<b class=\"text text-warning\">F</b>', 'yes', 'half_day', NULL, 1, '2024-08-21 09:29:55', NULL),
(5, 'Holiday', 'H', 'yes', 'holiday', NULL, 0, '2024-08-21 09:29:55', NULL),
(6, 'Half Day Second Shift', '<b class=\"text text-warning\">SH</b>', 'yes', 'half_day_second_shift', 'label label-info', 1, '2024-06-08 16:35:55', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `staff_attendence_schedules`
--

CREATE TABLE `staff_attendence_schedules` (
  `id` int NOT NULL,
  `staff_attendence_type_id` int DEFAULT NULL,
  `role_id` int DEFAULT NULL,
  `entry_time_from` time DEFAULT NULL,
  `entry_time_to` time DEFAULT NULL,
  `total_institute_hour` time DEFAULT NULL,
  `is_active` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `staff_designation`
--

CREATE TABLE `staff_designation` (
  `id` int NOT NULL,
  `designation` varchar(200) NOT NULL,
  `is_active` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `staff_id_card`
--

CREATE TABLE `staff_id_card` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `hospital_name` varchar(255) NOT NULL,
  `hospital_address` varchar(255) NOT NULL,
  `background` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `logo` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `sign_image` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `header_color` varchar(100) NOT NULL,
  `enable_staff_role` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_staff_id` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_staff_department` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_designation` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_name` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_fathers_name` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_mothers_name` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_date_of_joining` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_permanent_address` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_staff_dob` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_staff_phone` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `enable_staff_barcode` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `status` tinyint(1) NOT NULL COMMENT '0=disable,1=enable',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `staff_id_card`
--

INSERT INTO `staff_id_card` (`id`, `title`, `hospital_name`, `hospital_address`, `background`, `logo`, `sign_image`, `header_color`, `enable_staff_role`, `enable_staff_id`, `enable_staff_department`, `enable_designation`, `enable_name`, `enable_fathers_name`, `enable_mothers_name`, `enable_date_of_joining`, `enable_permanent_address`, `enable_staff_dob`, `enable_staff_phone`, `enable_staff_barcode`, `status`, `created_at`) VALUES
(1, 'Sample Staff Id Card', 'National Hospital', 'Habibganj Rd, Opp Cricket Club, E-3, Arera Colony, Bhopal', 'background.jpg', 'logo.jpg', 'signature.png', '#0e5c9f', 0, 1, 0, 1, 1, 0, 0, 0, 1, 1, 1, 0, 1, '2021-10-19 06:58:50');

-- --------------------------------------------------------

--
-- Table structure for table `staff_leave_details`
--

CREATE TABLE `staff_leave_details` (
  `id` int NOT NULL,
  `staff_id` int DEFAULT NULL,
  `leave_type_id` int DEFAULT NULL,
  `alloted_leave` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `staff_leave_request`
--

CREATE TABLE `staff_leave_request` (
  `id` int NOT NULL,
  `staff_id` int DEFAULT NULL,
  `leave_type_id` int DEFAULT NULL,
  `leave_from` date NOT NULL,
  `leave_to` date NOT NULL,
  `leave_days` int NOT NULL,
  `employee_remark` varchar(200) NOT NULL,
  `admin_remark` varchar(200) NOT NULL,
  `status` varchar(100) NOT NULL,
  `approved_date` date DEFAULT NULL,
  `applied_by` int DEFAULT NULL,
  `status_updated_by` int DEFAULT NULL,
  `document_file` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `staff_payroll`
--

CREATE TABLE `staff_payroll` (
  `id` int NOT NULL,
  `basic_salary` float(10,2) NOT NULL,
  `pay_scale` int NOT NULL,
  `grade` varchar(50) NOT NULL,
  `is_active` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `staff_payslip`
--

CREATE TABLE `staff_payslip` (
  `id` int NOT NULL,
  `staff_id` int DEFAULT NULL,
  `basic` float(10,2) NOT NULL,
  `total_allowance` float(10,2) NOT NULL,
  `total_deduction` float(10,2) NOT NULL,
  `leave_deduction` int NOT NULL,
  `tax` float(10,2) NOT NULL DEFAULT '0.00',
  `net_salary` float(10,2) NOT NULL,
  `status` varchar(100) NOT NULL,
  `month` varchar(200) NOT NULL,
  `year` varchar(200) NOT NULL,
  `cheque_no` varchar(250) DEFAULT NULL,
  `cheque_date` date DEFAULT NULL,
  `attachment` varchar(250) DEFAULT NULL,
  `attachment_name` text,
  `payment_mode` varchar(200) NOT NULL,
  `payment_date` date NOT NULL,
  `remark` text,
  `generated_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `staff_roles`
--

CREATE TABLE `staff_roles` (
  `id` int NOT NULL,
  `role_id` int DEFAULT NULL,
  `staff_id` int DEFAULT NULL,
  `is_active` int DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `staff_timeline`
--

CREATE TABLE `staff_timeline` (
  `id` int NOT NULL,
  `staff_id` int DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `timeline_date` date NOT NULL,
  `description` text,
  `document` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `status` varchar(10) NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `supplier_bill_basic`
--

CREATE TABLE `supplier_bill_basic` (
  `id` int NOT NULL,
  `invoice_no` varchar(100) NOT NULL,
  `date` datetime NOT NULL,
  `supplier_id` int DEFAULT NULL,
  `file` varchar(200) NOT NULL,
  `total` float(10,2) NOT NULL,
  `tax` float(10,2) NOT NULL,
  `discount` float(10,2) NOT NULL,
  `net_amount` float(10,2) NOT NULL,
  `note` text,
  `payment_mode` varchar(30) DEFAULT NULL,
  `cheque_no` varchar(255) DEFAULT NULL,
  `cheque_date` date DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `received_by` int DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `attachment_name` varchar(255) DEFAULT NULL,
  `payment_note` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `symptoms`
--

CREATE TABLE `symptoms` (
  `id` int NOT NULL,
  `symptoms_title` varchar(200) NOT NULL,
  `description` text,
  `type` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `symptoms_classification`
--

CREATE TABLE `symptoms_classification` (
  `id` int NOT NULL,
  `symptoms_type` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `system_notification`
--

CREATE TABLE `system_notification` (
  `id` int NOT NULL,
  `notification_title` varchar(200) NOT NULL,
  `notification_type` varchar(50) NOT NULL,
  `notification_desc` text,
  `notification_for` varchar(50) NOT NULL,
  `role_id` int DEFAULT NULL,
  `receiver_id` int DEFAULT NULL,
  `date` datetime NOT NULL,
  `is_active` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `system_notification_setting`
--

CREATE TABLE `system_notification_setting` (
  `id` int NOT NULL,
  `event` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `staff_message` text,
  `is_staff` int NOT NULL DEFAULT '1',
  `patient_message` text,
  `is_patient` int NOT NULL DEFAULT '0',
  `variables` text,
  `url` varchar(255) NOT NULL,
  `patient_url` varchar(255) NOT NULL,
  `notification_type` varchar(255) NOT NULL,
  `is_active` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `system_notification_setting`
--

INSERT INTO `system_notification_setting` (`id`, `event`, `subject`, `staff_message`, `is_staff`, `patient_message`, `is_patient`, `variables`, `url`, `patient_url`, `notification_type`, `is_active`, `created_at`) VALUES
(1, 'notification_appointment_created', 'New Appointment Created', 'Appointment has been created for Patient: {{patient_name}} ({{patient_id}}). Appointment Date: {{appointment_date}}  With Doctor Name: {{doctor_name}}.', 1, 'Dear {{patient_name}} ({{patient_id}}) your appointment has been created with Doctor: {{doctor_name}}.', 1, '{{appointment_date}} {{patient_name}} {{patient_id}} {{doctor_name}} {{message}}', '', '', 'appointment', 1, '2021-09-17 02:54:13'),
(2, 'appointment_approved', 'Appointment Status', 'Patient: {{patient_name}} ({{patient_id}}) appointment status is {{appointment_status}} with Doctor:  {{doctor_name}} Date: {{appointment_date}}.', 1, 'Dear {{patient_name}} ({{patient_id}}) your appointment status is {{appointment_status}} Date: {{appointment_date}} with Doctor {{doctor_name}}.', 1, '{{appointment_date}} {{patient_name}} {{patient_id}} {{doctor_name}} {{message}} {{appointment_status}}', '', '', 'appointment', 1, '2021-09-17 02:54:13'),
(3, 'opd_visit_created', 'New OPD Visit Created', 'OPD Visit has been created for patient: {{patient_name}} ({{patient_id}}) with doctor: {{doctor_name}}. Patient Symptoms Details are {{symptoms_description}} and any known allergies: {{any_known_allergies}} .', 1, 'Dear: {{patient_name}} ({{patient_id}}) your OPD visit has been created.  Your Symptoms Details are {{symptoms_description}} and any known allergies: {{any_known_allergies}}. ', 1, '{{patient_name}} {{patient_id}} {{symptoms_description}} {{any_known_allergies}} {{appointment_date}} {{doctor_name}}', '', '', 'opd', 1, '2021-09-17 02:54:13'),
(4, 'notification_opd_prescription_created', 'New OPD Prescription Created', 'New OPD prescription has been created for Patient: {{patient_name}} ({{patient_id}}) Checkup ID: ({{checkup_id}}). Prescription {{prescription_no}} prescribe by {{prescribe_by}}.  \r\n\r\n Prescription Details.\r\n(1) Finding Description: {{finding_description}}\r\n(2) Medicine Details: {{medicine}}\r\n(3) Radiology Test: {{radilogy_test}}\r\n(4) Pathology Test: {{pathology_test}}', 1, 'Dear {{patient_name}} ({{patient_id}}) Checkup ID: ({{checkup_id}}) your OPD ({{opd_no}}) prescription has been created . Please Check your finding details {{finding_description}} prescribe by {{prescribe_by}}.\r\n\r\nPlease Check prescription details. \r\n(1) Medicines Details: {{medicine}}\r\n(2) Radiology Test: {{radilogy_test}}\r\n(3) Pathology Test: {{pathology_test}}', 1, '{{prescription_no}} {{opd_no}} {{checkup_id}} {{finding_description}} {{medicine}} {{radilogy_test}} {{pathology_test}} {{prescribe_by}} {{generated_by}} {{patient_name}} {{patient_id}}', '', '', 'opd', 1, '2021-09-17 02:54:13'),
(5, 'add_opd_patient_charge', 'Add OPD Patient Charge', 'New OPD charges added in OPD Number: ({{opd_no}}) For Patient: {{patient_name}} ({{patient_id}}). In OPD applied charges is {{charge_type}}, charge category {{charge_category}} and charge Name {{charge_name}} quantity {{qty}}. Total net payable bill amount is {{net_amount}} date {{date}}', 1, 'Dear {{patient_name}}({{patient_id}}) OPD Number ({{opd_no}}) . In OPD applied charge name {{charge_type}} , category {{charge_category}},  charge name {{charge_name}} quantity {{qty}} and your net payable bill amount is {{net_amount}} Date {{date}}.', 1, '{{patient_name}} {{patient_id}}  {{opd_no}} {{charge_type}} {{charge_category}} {{charge_name}} {{qty}} {{net_amount}} {{date}} {{doctor_name}}', '', '', 'opd', 1, '2021-09-17 02:54:13'),
(6, 'add_opd_payment', 'Add OPD Payment', 'New OPD payment has been received from Patient: {{patient_name}}({{patient_id}}) OPD Number: ({{opd_no}}) transaction id: {{transaction_id}} payment date: {{date}} payment amount: {{amount}} payment mode: {{payment_mode}}.', 1, 'Dear {{patient_name}} ({{patient_id}}) your payment successfully received. OPD Number: {{opd_no}} transaction id: {{transaction_id}} payment date: {{date}} payment amount: ${{amount}} payment mode: {{payment_mode}}. ', 1, '{{patient_name}} {{patient_id}} {{opd_no}} {{date}} {{amount}} {{payment_mode}} {{transaction_id}}', '', '', 'opd', 1, '2021-09-17 02:54:13'),
(7, 'add_opd_medication_dose', 'New OPD Medication Dose', 'Consultant Doctor {{doctor_name}} has given medicine {{medicine_name}} Category is {{medicine_category}} Dosage {{dosage}} for OPD patient number is  {{opd_no}} patient name is {{patient_name}} medicine time  {{date}} {{time}}.', 1, 'Dear {{patient_name}} ({{patient_id}}) OPD Number: {{opd_no}} you have been given the Medicine is {{medicine_name}} Dose ({{dosage}}) medicine time {{date}} {{time}}.', 1, '{{patient_name}} {{patient_id}}  {{opd_no}} {{case_id}} \r\n{{date}} {{time}}  {{medicine_name}} {{dosage}} {{medicine_category}}  {{doctor_name}}', '', '', 'opd', 1, '2021-09-17 02:54:13'),
(8, 'add_nurse_note', 'New IPD Nurse Note', 'Add New Nurse Note for IPD Number: ({{ipd_no}}) Patient: {{patient_name}} ({{patient_id}}) Case ID: {{case_id}} with consultant doctor  {{doctor_name}}. \r\n\r\nNurse Note Details:\r\n(1) Nurse Name: {{nurse_name}} ({{nurse_id}})\r\n(2) Note: {{note}}\r\n(3) Comment: {{comment}}', 1, 'Dear {{patient_name}} ({{patient_id}}) IPD Number: ({{ipd_no}}) and Case ID: {{case_id}} your consultant doctor is {{doctor_name}}. \r\n\r\nNurse Note Details:\r\n(1) Nurse Name: {{nurse_name}} ({{nurse_id}})\r\n(2) Note: {{note}}\r\n(3) Comment: {{comment}}', 1, '{{patient_name}} {{patient_id}} {{ipd_no}} {{case_id}} {{doctor_name}} {{date}} {{nurse_name}} {{nurse_id}} {{note}} {{comment}}', '', '', 'ipd', 1, '2021-09-17 02:54:13'),
(9, 'move_in_ipd_from_opd', 'Patient Move in IPD From OPD', 'Patient {{patient_name}} ({{patient_id}}) move in IPD From OPD. Symptoms Details: {{symptoms_description}} and known allergies is  {{any_known_allergies}}. The patient is being shifted from opd to ipd whose consultant doctor is {{doctor_name}}.', 1, 'Dear {{patient_name}} ({{patient_id}}) you have been shifted from OPD to IPD consultant doctor is {{doctor_name}}. Check your symptoms details {{symptoms_description}} and known allergies {{any_known_allergies}}.\r\n\r\n', 1, '{{patient_name}} {{patient_id}} {{symptoms_description}} {{any_known_allergies}} {{appointment_date}} {{doctor_name}}', '', '', 'opd', 1, '2021-09-17 02:54:13'),
(10, 'add_opd_operation', 'New OPD Operation', 'OPD Number: ({{opd_no}}) Patient: {{patient_name}} ({{patient_id}}) Case id: {{case_id}} has been shifted to the operation ward. Consultant Doctor is {{doctor_name}} .\r\n\r\nOperation Details.\r\nOperation Name: {{operation_name}}\r\nOperation Date: {{operation_date}}', 1, 'Dear {{patient_name}} {{patient_id}} your operation {{operation_name}} date is on {{operation_date}} and your consultant doctor is {{doctor_name}}.', 1, '{{patient_name}} {{patient_id}} {{opd_no}} {{case_id}} {{operation_name}} {{operation_date}} {{doctor_name}}', '', '', 'opd', 1, '2021-09-17 02:54:13'),
(11, 'ipd_visit_created', 'New IPD Visit Created', 'IPD Visit has been created for {{patient_name}} ({{patient_id}}) with Doctor: {{doctor_name}}. Patient Symptoms Details are {{symptoms_description}}.', 1, 'Dear {{patient_name}} ({{patient_id}}) your IPD visit has been created .', 1, '{{patient_name}} {{patient_id}} {{symptoms_description}} {{admission_date}} {{doctor_name}} {{bed_location}}', '', '', 'ipd', 1, '2021-09-17 02:54:13'),
(12, 'notification_ipd_prescription_created', 'Notification IPD Prescription Created', 'Prescription({{prescription_no}}) for IPD ({{ipd_no}}) prescribe by: {{priscribe_by}}. \r\n\r\nPrescription  Details-\r\nFinding Description: {{finding_description}}\r\nMedicine Name: {{medicine}}\r\nRadiology Test: {{radilogy_test}}\r\nPathology Test: {{pathology_test}}\r\n{{priscribe_by}}', 1, 'Dear {{patient_name}} {{patient_id}} your IPD prescription number {{prescription_no}} is prescribe by: {{priscribe_by}}. \r\n\r\nPrescription  Details-\r\n Finding Description: {{finding_description}}\r\n Medicine Name : {{medicine}}\r\n Radiology Test: {{radilogy_test}}\r\n Pathology Test: {{pathology_test}}', 1, '{{prescription_no}} {{ipd_no}} {{finding_description}} {{medicine}} {{radilogy_test}} {{pathology_test}} {{priscribe_by}} {{generated_by}} {{patient_name}} {{patient_id}}', '', '', 'ipd', 1, '2021-09-17 02:54:13'),
(14, 'add_ipd_operation', 'Add IPD Operation', 'Patient Name : {{patient_name}} ({{patient_id}}) IPD Number : {{ipd_no}} Case Id : {{case_id}} has been shifted to the operation ward. Whose doctor is {{doctor_name}}.\r\n\r\nOperation Details-\r\n(1) Operation Name: {{operation_name}}\r\n(2) Operation  Date:  {{operation_date}}', 1, 'Dear {{patient_name}} ({{patient_id}}) your operation {{operation_name}} date is on {{operation_date}} with {{doctor_name}}.', 1, '{{patient_name}} {{patient_id}} {{ipd_no}} {{case_id}} {{operation_name}} {{operation_date}} {{doctor_name}}', '', '', 'ipd', 1, '2021-09-17 02:54:13'),
(15, 'add_ipd_generate_bill', 'Add IPD Generate Bill', 'Generated bill for IPD Number {{ipd_no}}  Patient Name {{patient_name}} {{patient_id}} .\r\n\r\nBill Details\r\nTotal Amount {{total}}\r\nNet Amount {{net_amount}}\r\nTax  {{tax}}\r\nPaid Amount   {{paid}}\r\nDue Amount   {{due}}', 1, 'Dear {{patient_name}} {{patient_id}}  your IPD bill is generated for Case Id {{case_id}} .\r\n\r\nBill Details\r\nTotal Amount {{total}}\r\nNet Amount {{net_amount}}\r\nTax  {{tax}}\r\nPaid Amount   {{paid}}\r\nDue Amount   {{due}}', 1, '{{patient_name}} {{patient_id}} {{ipd_no}} {{case_id}} {{net_amount}} {{total}} {{tax}} {{paid}} {{due}}', '', '', 'ipd', 1, '2021-09-17 02:54:13'),
(16, 'add_ipd_patient_charge', 'Add IPD Patient Charge', 'Add Charge for IPD Patient Name : {{patient_name}} ({{patient_id}}) IPD Number ({{ipd_no}}) has applied charge {{charge_type}}, category  {{charge_category}}, and Name {{charge_name}} total quantity {{qty}} . Now total net amount {{net_amount}} date {{date}}.', 1, 'Dear {{patient_name}} ({{patient_id}}) IPD Number {{ipd_no}} you have applied charge name is {{charge_type}}, category {{charge_category}} ,charge name {{charge_name}}  and total quantity {{qty}} now your net amount {{net_amount}} and date {{date}}.', 1, '{{patient_name}} {{patient_id}} {{ipd_no}} {{charge_type}} {{charge_category}} {{charge_name}} {{qty}} {{net_amount}} {{date}}', '', '', 'ipd', 1, '2021-09-17 02:54:13'),
(17, 'add_ipd_payment', 'Add IPD Payment', 'Payment has been received from Patient Name: {{patient_name}} ({{patient_id}}) IPD NO: {{ipd_no}} transaction id: {{transaction_id}} payment date: {{date}} payment amount: {{amount}} payment mode: {{payment_mode}}.', 1, 'Dear {{patient_name}} ({{patient_id}}) IPD: {{ipd_no}} we have received your payment amount ({{amount}}) transaction id: {{transaction_id}} payment date: {{date}} payment mode: {{payment_mode}} .', 1, '{{patient_name}} {{patient_id}} {{ipd_no}} {{date}} {{amount}} {{payment_mode}} {{transaction_id}}', '', '', 'ipd', 1, '2021-09-17 02:54:13'),
(18, 'add_ipd_medication_dose', 'Add IPD Medication Dose', 'Doctor {{doctor_name}}  has given medicine {{medicine_name}} Category is {{medicine_category}} Dosage {{dosage}} to Patient:  {{patient_name}} {{patient_id}} at {{date}} {{time}}.', 1, 'Dear {{patient_name}} ({{patient_id}}) IPD Number {{ipd_no}} you have been given the {{medicine_name}} dose {{dosage}} of medicine at {{date}} {{time}}.', 1, '{{patient_name}} {{patient_id}} {{ipd_no}} {{case_id}} {{date}} {{time}} {{medicine_name}} {{dosage}} {{medicine_category}} {{doctor_name}}', '', '', 'ipd', 1, '2021-09-17 02:54:13'),
(20, 'add_consultant_register', 'Add Consultant Register', 'New Consultant Register: {doctor_name}} has been added  some instructions: {{instruction}} on date {{applied_date}} for the patients {{patient_name}} ({{patient_id}}) of IPD {{ipd_no}}.', 1, 'Dear {{patient_name}} ({{patient_id}}) IPD Number: ({{ipd_no}}). Consultant: {{doctor_name}} has added some instructions: {{instruction}} on applied date {{applied_date}}.', 1, '{{patient_name}} {{patient_id}} {{ipd_no}} {{case_id}} {{applied_date}} {{instruction_date}} {{doctor_name}} {{instruction}}', '', '', 'ipd', 1, '2021-09-17 02:54:13'),
(22, 'pharmacy_generate_bill', 'Pharmacy Generate Bill', 'Pharmacy Bill Generated for Patient: {{patient_name}} ({{patient_id}}) Case ID: {{case_id}}.\r\n\r\nPharmacy Bill Details-\r\nTotal Amount: {{total}}\r\nNet Amount: {{net_amount}}\r\nDiscount: {discount}} \r\nTax: {{tax}}\r\nPaid Amount  $ {{paid}}\r\nDue Amount  $ {{due_amount}}', 1, 'Dear {{patient_name}} {{patient_id}} your pharmacy bill is generated. \r\n\r\nBill Details-\r\nTotal Amount: {{total}}\r\nNet Amount: {{net_amount}}\r\nDiscount: {{discount}}\r\nTax: {{tax}}\r\nPaid Amount: {{paid}}\r\nDue Amount: {{due_amount}}', 1, '{{patient_name}} {{patient_id}} {{case_id}} {{bill_no}} {{medicine_details}} {{doctor_name}} {{total}} {{discount}} {{tax}} {{net_amount}} {{date}} {{paid}} {{due_amount}}', '', '', 'pharmacy', 1, '2021-09-17 02:54:13'),
(23, 'add_medicine', 'Add Medicine', 'New Add Medicine Details: \r\n\r\nMedicine Name  {{medicine_name}} , \r\nMedicine Category  {{medicine_category}} ,\r\nMedicine Company  {{medicine_company}} ,\r\nMedicine Composition  {{medicine_composition}} ,\r\nMedicine Group {{medicine_group}} , \r\nUnit {{unit}} ,\r\nPacking  {{unit_packing}} ,', 1, '', 0, '{{medicine_name}} {{medicine_category}} {{medicine_company}} {{medicine_composition}} {{medicine_group}} {{unit}} {{unit_packing}}', '', '', 'pharmacy', 1, '2021-09-17 02:54:13'),
(24, 'add_bad_stock', 'Add Bad Stock', 'Add Bad Stock Details :\r\n\r\nBatch No {{batch_no}}\r\nExpiry Date  {{expiry_date}}\r\nOutward Date   {{outward_date}}  \r\n Total Qty  {{qty}}', 1, '', 0, '{{batch_no}} {{expiry_date}} {{outward_date}} {{qty}}', '', '', 'pharmacy', 1, '2021-09-17 02:54:13'),
(25, 'purchase_medicine', 'Purchase Medicine', 'Purchase Medicine Details :\r\nSupplier Name: {{supplier_name}} \r\nMedicine Details: {{medicine_details}}\r\nPurchase Date: {{purchase_date}}\r\nInvoice Number:  {{invoice_number}}\r\nTotal: {{total}}\r\nDiscount: {{discount}} \r\nTax: {{tax}}\r\nNet Amount: {{net_amount}}', 1, '', 0, '{{supplier_name}} {{medicine_details}} {{purchase_date}} {{invoice_number}} {{total}} {{discount}} {{tax}} {{net_amount}}', '', '', 'pharmacy', 1, '2021-09-17 02:54:13'),
(26, 'pathology_investigation', 'Pathology Investigation', 'Pathology Test Report for Patient: {{patient_name}} ({{patient_id}}) case id: {{case_id}}. Pathology test assign by {{doctor_name}}. pathology charge- total amount {{total}}, discount {{discount}} ,tax {{tax}}  net amount is {{net_amount}} and total paid amount {{paid_amount}}.', 1, 'Dear {{patient_name}} ({{patient_id}}) case id: {{case_id}}. Your pathology test bill number is {{bill_no}} and total amount {{total}}, tax {{tax}}, discount {{discount}} so now your net amount is {{net_amount}}.  You have paid your total amount {{paid_amount}}.', 1, '{{patient_name}} {{patient_id}} {{case_id}} {{bill_no}} {{date}} {{doctor_name}}  {{total}} {{discount}} {{tax}} {{net_amount}} {{paid_amount}}', '', '', 'pathology', 1, '2021-09-17 02:54:13'),
(27, 'pathology_sample_collection', 'Pathology Sample Collection', 'Pathology Bill Number {{bill_no}} Patient: {{patient_name}} ({{patient_id}}) Case id: {{case_id}}. Sample Collected  by  {{sample_collected_person_name}} on {{collected_date}} from {{pathology_center}} and report expected date is {{expected_date}}.', 1, 'Dear {{patient_name}} {{patient_id}} Case id: {{case_id}}  your pathology test sample collected by {{sample_collected_person_name}} on {{collected_date}} from {{pathology_center}} . Pathology Test report expected date {{expected_date}}. ', 1, '{{patient_name}} {{patient_id}} {{case_id}} {{bill_no}} {{test_name}} {{sample_collected_person_name}} {{collected_date}} {{pathology_center}} {{expected_date}} {{doctor_name}}', '', '', 'pathology', 1, '2021-09-17 02:54:13'),
(28, 'pathology_test_report', 'Pathology Test Report', 'Pathology Test Report Bill Number {{bill_no}} for Patient Name is {{patient_name}} {{patient_id}} Case id {{case_id}} and test approved by {{approved_by}} on {{approve_date}} . Pathology Test {{test_name}} sample collected by {{sample_collected_person_name}} on {{collected_date}} from {{pathology_center}} and Expected date {{expected_date}} . {{doctor_name}}', 1, 'Dear {{patient_name}} {{patient_id}} Case id  {{case_id}}. Your Pathology Test {{test_name}} sample collected by {{sample_collected_person_name}} on  {{collected_date}} from {{pathology_center}} .', 1, '{{patient_name}} {{patient_id}} {{case_id}} {{bill_no}} {{test_name}} {{sample_collected_person_name}} {{collected_date}} {{pathology_center}} {{expected_date}} {{approved_by}} {{approve_date}} {{doctor_name}}', '', '', 'pathology', 1, '2021-09-17 02:54:13'),
(29, 'radiology_investigation', 'Radiology Investigation', 'Radiology Test Report for Patient: {{patient_name}} ({{patient_id}}) case id: {{case_id}}. Radiology test assign by {{doctor_name}}. Test Charge total amount {{total}}, total discount {{discount}}, tax {{tax}}.', 1, 'Dear {{patient_name}} ({{patient_id}}) case id: {{case_id}}. Your Radiology test bill number is {{bill_no}},  total bill amount {{total}} tax {{tax}}, discount {{discount}} so now your net amount {{net_amount}} and total paid amount is {{paid}}. ', 1, '{{patient_name}} {{patient_id}} {{case_id}} {{bill_no}} {{date}} {{doctor_name}}  {{total}} {{net_amount}} {{paid}} {{discount}} {{tax}}', '', '', 'radiology', 1, '2021-09-17 02:54:13'),
(30, 'radiology_sample_collection', 'Radiology Sample Collection', 'Radiology Bill Number: {{bill_no}} for Patient: {{patient_name}} ({{patient_id}}) Case id: {{case_id}}. Radiology test name is {{test_name}} and sample collected by {{sample_collected_person_name}} on {{collected_date}} from {{radiology_center}} and report expected date is {{expected_date}}.', 1, 'Dear {{patient_name}} {{patient_id}} Case id: {{case_id}}  your radiology test is {{test_name}} and  sample collected by {{sample_collected_person_name}} on {{collected_date}} from {{radiology_center}}. Test report expected date {{expected_date}}. ', 1, '{{patient_name}} {{patient_id}} {{case_id}} {{bill_no}} {{test_name}} {{sample_collected_person_name}} {{collected_date}} {{radiology_center}} {{expected_date}} {{doctor_name}}', '', '', 'radiology', 1, '2021-09-17 02:54:13'),
(31, 'radiology_test_report', 'Radiology Test Report', 'Radiology Bill Number {{bill_no}} Patient Name {{patient_name}} ({{patient_id}}) Case id ( {{case_id}}). Sample Collected  by  {{sample_collected_person_name}} on {{collected_date}} from {{radiology_center}} and Expected date is {{expected_date}}.', 1, 'Dear {{patient_name}} {{patient_id}} Case id ({{case_id}}) your radiology test sample collected by {{sample_collected_person_name}} on {{collected_date}} from  {{radiology_center}}. radiology test report expected date {{expected_date}} .', 1, '{{patient_name}} {{patient_id}} {{case_id}} {{bill_no}} {{test_name}} {{sample_collected_person_name}} {{collected_date}} {{radiology_center}} {{expected_date}} {{approved_by}} {{approved_date}} {{doctor_name}}', '', '', 'radiology', 1, '2021-09-17 02:54:13'),
(32, 'add_bag_stock', 'Add Bag Stock', 'New Add Bag Stock Details- Donor Name: {{donor_name}}, Blood Group: ({{blood_group}}) and contact number {{contact_no}} . Donate bag details blood bag number ({{bag}}) and charge {{charge_name}} donated date {{donate_date}}. Total amount {{total}} discount {{discount}} tax {{tax}} so total net amount is {{net_amount}}.', 1, '', 0, '{{donor_name}} {{blood_group}} {{contact_no}} {{donate_date}} {{bag}} {{charge_name}} {{total}} {{discount}} {{tax}} {{net_amount}}', '', '', 'blood_bank', 1, '2021-09-17 02:54:13'),
(33, 'blood_issue', 'Blood Issue', 'Blood issue for Bill Number {{bill_no}} Patient: {{patient_name}} ({{patient_id}}) Case Id {{case_id}} . Patient blood group is {{blood_group}} and bag number ({{bag}}) issue on {{issue_date}}, reference by {{reference_name}}. Applied charge name is {{charge_name}} and total amount {{total}}, discount {{discount}}, tax {{tax}}, now total net amount{{net_amount}}.', 1, 'Dear {{patient_name}} ({{patient_id}}) case id: {{case_id}} your bill number {{bill_no}} blood group {{blood_group}} bag number is {{bag}} charge name  {{charge_name}} issue on {{issue_date}} reference by {{reference_name}} .Total amount {{total}}, discount {{discount}}, tax {{tax}} now your total net amount {{net_amount}}.', 1, '{{patient_name}} {{patient_id}} {{case_id}} {{bill_no}} {{issue_date}} {{reference_name}} {{blood_group}} {{bag}} {{charge_name}} {{total}} {{discount}} {{tax}} {{net_amount}} ', '', '', 'blood_bank', 1, '2021-09-17 02:54:13'),
(34, 'add_component_of_blood', 'Add Component of Blood', '{{component_name}} component has been added on the bag number {{bag}} of Blood Group {{blood_group}} .', 1, '', 0, '{{blood_group}} {{bag}} {{ component_name}} {{component_bag}}', '', '', 'blood_bank', 1, '2021-09-17 02:54:13'),
(35, 'component_issue', 'Component Issue', 'Component Issue for  Bill Number {{bill_no}} Patient Name is {{patient_name}} ({{patient_id}}) Case Id: {{case_id}}.  Blood group {{blood_group}} Component: {{component}}, bag number {{bag}} issue on {{issue_date}}  reference by {{reference_name}}. Applied charge name {{charge_name}} total amount {{total}}  discount {{discount}} tax {{tax}} now total net amount {{net_amount}}.', 1, 'Dear {{patient_name}} ({{patient_id}}) {{case_id}} you have issued a component {{component}} Bag number is {{bag}}  blood group is {{blood_group}} issue on  {{issue_date}} reference by {{reference_name}} . Total amount {{total}} Discount {{discount}} Tax {{tax}} now your total net amount  is {{net_amount}}.', 1, '{{patient_name}} {{patient_id}} {{case_id}} {{bill_no}} {{issue_date}} {{reference_name}} {{blood_group}} {{component}} {{bag}} {{charge_name}} {{total}} {{discount}} {{tax}} {{net_amount}} ', '', '', 'blood_bank', 1, '2021-09-17 02:54:13'),
(36, 'live_opd_consultation_add', 'Live OPD Consultation Add', 'Live Consultation for  OPD {{opd_no}} Patient  Name {{patient_name}} {{patient_id}}  with Consultant Doctor {{doctor_name}} {{doctor_id}} . Live consulatent Title  {{consultation_title}} Date {{consultation_date}} minutes {{consultation_date}} {{consultation_duration_minutes}}.', 1, 'Dear {{patient_name}} {{patient_id}} your live consultation subject {{consultation_title}} date {{consultation_date}} minute {{consultation_duration_minutes}}  with Consultant Doctor {{doctor_name}} ({{doctor_id}}).', 1, '{{patient_name}} {{patient_id}} {{consultation_title}} {{consultation_date}} {{consultation_duration_minutes}}  {{opd_no}} {{checkup_id}} {{doctor_id}} {{doctor_name}}', '', '', 'live_consultation', 1, '2021-09-17 02:54:13'),
(37, 'live_opd_consultation_start', 'Live Opd Consultation Start', 'patient_name: {{patient_name}} patient_id: {{patient_id}} consultation_title: {{consultation_title}} consultation_date: {{consultation_date}}  consultation_duration_minutes: {{consultation_duration_minutes}} opd_no: {{opd_no}} checkup_id: {{checkup_id}} doctor_name: {{doctor_name}}', 1, 'patient_name: {{patient_name}} patient_id: {{patient_id}} consultation_title: {{consultation_title}} consultation_date: {{consultation_date}}  consultation_duration_minutes: {{consultation_duration_minutes}} opd_no: {{opd_no}} checkup_id: {{checkup_id}} doctor_name: {{doctor_name}}', 1, '{{patient_name}} {{patient_id}} {{consultation_title}} {{consultation_date}} {{consultation_duration_minutes}}  {{opd_no}} {{checkup_id}} {{doctor_name}}', '', '', 'live_consultation', 1, '2021-09-17 02:54:13'),
(38, 'live_meeting_start', 'Live Meeting Start', 'Live Meeting has been created for Staff: {{staff_list}}  Meeting Title is {{meeting_title}}  and Meeting Date {{meeting_date}} Meeting Duration Minutes: {{meeting_duration_minutes}}.', 1, '', 0, '{{meeting_title}} {{meeting_date}} {{meeting_duration_minutes}} {{staff_list}}', '', '', 'live_consultation', 1, '2021-09-17 02:54:13'),
(39, 'live_meeting_add', 'Live Meeting Add', 'Live Meeting Created for Staff {{staff_list}} and  Meeting Title is {{meeting_title}} on Meeting Date {{meeting_date}} Meeting Duration Minutes{{meeting_duration_minutes}} .', 1, '', 0, '{{meeting_title}} {{meeting_date}} {{meeting_duration_minutes}} {{staff_list}}', '', '', 'live_consultation', 1, '2021-09-17 02:54:13'),
(40, 'add_referral_payment', 'Add Referral Payment', 'Patient Name {{patient_name}} ({{patient_id}}) in {{patient_type}} Bill number {{bill_no}} and patient bill amount is {{patient_bill_amount}}. Commission percentage of total bill {{commission_percentage}}. Commission amount {{commission_amount}} has been given to the payee {{payee}}.', 1, '', 0, '{{patient_name}} {{patient_id}} {{patient_type}} {{bill_no}} {{patient_bill_amount}} {{payee}} {{commission_percentage}} {{commission_amount}}', '', '', 'referral', 1, '2021-09-17 02:54:13'),
(41, 'patient_certificate_generate', 'Patient Certificate Generate', 'Patient Name {{patient_name}} {{patient_id}} certificate {{certificate_name}} has been generated. OPD/ IPD number {{opd_ipd_no}}.', 1, 'Dear Patient {{patient_name}} {{patient_id}} OPD / IPD number is {{opd_ipd_no}}  your certificate {{certificate_name}} has been generated.', 1, '{{patient_name}} {{patient_id}} {{opd_ipd_no}} {{certificate_name}}', '', '', 'certificate', 1, '2021-09-17 02:54:13'),
(42, 'patient_id_card_generate', 'remaining', 'ID Card is generated for Patient Name {{patient_name}} {{patient_id}} .', 1, 'Dear {{patient_name}} {{patient_id}} your id card is generated .', 1, '{{patient_name}} {{patient_id}}  {{id_card_template}}', '', '', 'certificate', 1, '2021-09-17 02:54:13'),
(43, 'generate_staff_id_card', 'Generate Staff ID Card', 'Staff ID card is generated for Role: {{role}}, staff name {{staff_name}} suename {{staff_surname}} employee id: {{employee_id}}.', 1, '', 0, '{{role}} {{staff_name}} {{staff_surname}} {{employee_id}} {{id_card_template}}', '', '', 'certificate', 1, '2021-09-17 02:54:13'),
(44, 'create_ambulance_call', 'Create Ambulance Call', '{{patient_name}} {{patient_id}} has booked an ambulance on {{date}} his charge name {{charge_name}} tax {{tax}}  net amount {{net_amount}} and total paid  amount {{paid_amount}}.\r\n\r\nAmbulance Details \r\n\r\nVehicle Model  {{vehicle_model}}\r\nDriver Name  {{driver_name}}', 1, 'Dear {{patient_name}} {{patient_id}} your ambulance is booked on {{date}} . Charge applied {{charge_name}}, tax {{tax}} net amount is {{net_amount}} and your paid amount is {{paid_amount}} .\r\n\r\nAmbulance Details-\r\nVehicle Model: {{vehicle_model}}\r\nDriver Name: {{driver_name}}', 1, '{{patient_name}} {{patient_id}} {{vehicle_model}} {{driver_name}} {{date}} {{charge_name}} {{tax}} {{net_amount}} {{paid_amount}}', '', '', 'ambulance', 1, '2021-09-17 02:54:13'),
(45, 'add_birth_record', 'Add Birth Record', 'Patient {{mother_name}} ({{mother_id}}) has given birth to a new baby {{child_name}} on {{birth_date}}.', 1, 'Dear {{mother_name}} {{mother_id}} case id : {{case_id}} your baby {{child_name}} is born on {{birth_date}}.', 1, '{{mother_name}} {{mother_id}} {{child_name}} {{birth_date}} {{case_id}}', '', '', 'birth_death_record', 1, '2021-09-17 02:54:13'),
(46, 'add_death_record', 'Add Death Record', 'Patient {{patient_name}} ({{patient_id}}) Case id :{{case_id}} has died on {{death_date}}.', 1, '', 0, '{{case_id}} {{patient_name}} {{patient_id}} {{death_date}}', '', '', 'birth_death_record', 1, '2021-09-17 02:54:13'),
(47, 'staff_enabale_disable', 'Staff Enabale/Disable', 'Staff Name: {{staff_name}} surname: {{staff_surname}} Employment ID: ({{employee_id}}) has been {{status}}.', 1, '', 0, '{{staff_name}} {{staff_surname}} {{employee_id}} {{status}}', '', '', 'human_resource', 1, '2021-09-17 02:54:13'),
(48, 'staff_generate_payroll', 'Staff Generate Payroll', 'Payroll Generated for  Month {{month}} year {{year}}  Role {{role}} . Basic Salary is {{basic_salary}} Earning  {{earning}} Deduction {{deduction}} Gross salary  {{gross_salary}}.  Now Total Net Salary {{net_salary}}.', 1, '', 0, '{{role}} {{month}} {{year}} {{basic_salary}} {{earning}} {{deduction}} {{gross_salary}} {{tax_amount}} {{net_salary}}', '', '', 'human_resource', 1, '2021-09-17 02:54:13'),
(49, 'staff_leave', 'Staff Leave', 'Staff {{staff_name}} {{staff_surname}} ({{employee_id}}) has applied leave on Date {{apply_date}} for leave {{days}} days. date {{leave_date}} . Currently Leave Status is {{leave_status}} .', 1, '', 0, '{{apply_date}} {{leave_type}} {{leave_date}} {{days}} {{staff_name}} {{staff_surname}} {{employee_id}}\r\n{{leave_status}}', '', '', 'human_resource', 1, '2021-09-17 02:54:13'),
(50, 'staff_leave_status', 'Staff Leave Status', 'Staff Name {{staff_name}} {{staff_surname}} {{employee_id}} has applied leave for {{days}} days. leave date: {{leave_date}}, Leave Status:  {{leave_status}}.', 1, '', 0, '{{apply_date}} {{leave_type}} {{leave_date}} {{days}} {{staff_name}} {{staff_surname}} {{employee_id}}\r\n{{leave_status}}', '', '', 'human_resource', 1, '2021-09-17 02:54:13'),
(51, 'live_ipd_consultation_add', 'Live IPD Consultation Add', 'Live Consultation for IPD {{ipd_no}} Patient  Name {{patient_name}} {{patient_id}} with Consultant Doctor {{doctor_name}} {{doctor_id}} . Live consulatent Title  {{consultation_title}} Date {{consultation_date}} minutes {{consultation_date}} {{consultation_duration_minutes}}.', 1, 'Dear {{patient_name}} {{patient_id}} your live consultation subject {{consultation_title}} date {{consultation_date}} minute {{consultation_duration_minutes}}  with Consultant Doctor {{doctor_name}} ({{doctor_id}}).', 1, '{{patient_name}} {{patient_id}} {{consultation_title}} {{consultation_date}} {{consultation_duration_minutes}} \r\n{{ipd_no}} {{doctor_id}} {{doctor_name}}', '', '', 'live_consultation', 1, '2021-09-17 02:54:13'),
(52, 'live_ipd_consultation_start', 'Live IPD Consultation Start', 'IPD No {{ipd_no}} Patient Name {{patient_name}} {{patient_id}}. Live Consultation Doctor {{doctor_name}}. \r\n\r\nLive Consultation Details.\r\nConsultation Title {{consultation_title}}\r\nConsultation Date  {{consultation_date}}\r\nConsultation Duration Minutes  {{consultation_duration_minutes}}', 1, 'Dear patient patient_name: {{patient_name}} patient_id: {{patient_id}} , your live consultation consultation_title: {{consultation_title}} has been scheduled on Consultation Date: {{consultation_date}} for the duration of consultation_duration_minutes: {{consultation_duration_minutes}} minute, ipd_no: {{ipd_no}} and your consultant doctor doctor_name: {{doctor_name}}  please do not share the link to any body.', 1, '{{patient_name}} {{patient_id}} {{consultation_title}} {{consultation_date}} {{consultation_duration_minutes}}  {{ipd_no}} {{doctor_name}}', '', '', 'live_consultation', 1, '2021-09-17 02:54:13'),
(53, 'add_ipd_discharge_patient', 'Add IPD Discharge Patient', 'IPD Patient: {{patient_name}}({{patient_id}}) status: ({{discharge_status}}) on {{discharge_date}}.', 1, 'Dear {{patient_name}} {{patient_id}} you have been {{discharge_status}} on {{discharge_date}}.', 1, '{{patient_name}} {{patient_id}} {{discharge_status}} {{discharge_date}} {{ipd_no}} {{case_id}}', '', '', 'ipd', 1, '2021-09-17 02:54:13'),
(54, 'add_opd_discharge_patient', 'Add OPD Discharge Patient', 'OPD Patient {{patient_name}} {{patient_id}} discharge status: {discharge_status}} on {{discharge_date}}.', 1, '\r\nDear {{patient_name}} {{patient_id}} you have been {{discharge_status}} on {{discharge_date}}.', 1, '{{patient_name}} {{patient_id}} {{discharge_status}} {{discharge_date}} {{opd_no}} {{case_id}}', '', '', 'opd', 1, '2021-09-17 02:54:13'),
(55, 'add_payroll_payment', 'Add Payroll Payment', 'Month {{month}} salary amount {{payment_amount}} has been given to staff name {{staff}} on date {{payment_date}}.', 1, 'staff: {{staff}} payment_amount: {{payment_amount}} month: {{month}} year: {{year}} payment_mode: {{payment_mode}} payment_date: {{payment_date}}\r\n', 0, '{{staff}} {{payment_amount}} {{month}} {{year}} {{payment_mode}} {{payment_date}}', '', '', 'human_resource', 1, '2021-09-17 02:54:13'),
(56, 'add_opd_generate_bill', 'Add OPD Generate Bill', 'Generated bill for OPD Number {{opd_id}}  Patient Name {{patient_name}} {{patient_id}} .\r\n\r\nBill Details\r\nTotal Amount {{total}}\r\nNet Amount {{net_amount}}\r\nTax  {{tax}}\r\nPaid Amount   {{paid}}\r\nDue Amount   {{due}}', 1, 'Dear {{patient_name}} {{patient_id}}  your OPD bill is generated for Case Id {{case_id}} .\r\n\r\nBill Details\r\nTotal Amount {{total}}\r\nNet Amount {{net_amount}}\r\nTax  {{tax}}\r\nPaid Amount   {{paid}}\r\nDue Amount   {{due}}', 1, '{{patient_name}} {{patient_id}} {{opd_id}} {{case_id}} {{net_amount}} {{total}} {{tax}} {{paid}} {{due}}', '', '', 'opd', 1, '2021-09-17 02:54:13'),
(57, 'patient_consultation_add', 'Patient Consultation Add', 'Live Consultation for Patient  Name {{patient_name}} {{patient_id}}  with Consultant Doctor {{doctor_name}} . Live consulatent Title  {{consultation_title}} Date {{consultation_date}} minutes {{consultation_date}} {{consultation_duration_minutes}}.', 1, 'Dear {{patient_name}} {{patient_id}} your live consultation subject {{consultation_title}} date {{consultation_date}} minute {{consultation_duration_minutes}}  with Consultant Doctor {{doctor_name}}.', 1, '{{patient_name}} {{patient_id}} {{consultation_title}} {{consultation_date}} {{consultation_duration_minutes}}  {{checkup_id}} {{doctor_name}}', '', '', 'live_consultation', 1, '2021-09-17 02:54:13'),
(58, 'opd_patient_discharge_revert', 'opd_patient_discharge_revert', 'patient_name: {{patient_name}} patient_id: {{patient_id}} discharge_status: {{discharge_status}} discharge_date: {{discharge_date}} opd_no: {{opd_no}} case_id: {{case_id}}', 1, 'patient_name: {{patient_name}} patient_id: {{patient_id}} discharge_status: {{discharge_status}} discharge_date: {{discharge_date}} opd_no: {{opd_no}} case_id: {{case_id}}', 1, '{{patient_name}} {{patient_id}} {{discharge_status}} {{discharge_date}} {{opd_no}} {{case_id}}', '', '', 'opd', 1, '2021-09-17 02:54:13'),
(59, 'ipd_patient_discharge_revert', 'ipd_patient_discharge_revert', 'patient_name: {{patient_name}} patient_id: {{patient_id}} discharge_status: {{discharge_status}} discharge_date: {{discharge_date}} ipd_no: {{ipd_no}} case_id: {{case_id}}', 1, 'patient_name: {{patient_name}} patient_id: {{patient_id}} discharge_status: {{discharge_status}} discharge_date: {{discharge_date}} ipd_no: {{ipd_no}} case_id: {{case_id}}', 1, '{{patient_name}} {{patient_id}} {{discharge_status}} {{discharge_date}} {{ipd_no}} {{case_id}}', '', '', 'opd', 1, '2021-09-17 02:54:13'),
(60, 'add_ipd_previous_obstetric_history', 'Add IPD  Previous Obstetric History', 'IPD  Previous Obstetric History been created for Patient: {{patient_name}} ({{patient_id}}).', 1, 'IPD  Previous Obstetric History been created for Patient: {{patient_name}} ({{patient_id}}). ', 1, '{{patient_name}} {{patient_id}} {{ipd_no}} {{case_id}} {{place_of_delivery}}', '', '', 'ipd', 1, '2021-09-17 02:54:13'),
(61, 'add_ipd_postnatal_history', 'Add IPD Postnatal History', 'IPD Postnatal History\r\n has been created for Patient: {{patient_name}} ({{patient_id}}).', 1, 'IPD Postnatal History\r\n has been created for Patient: {{patient_name}} ({{patient_id}}).\r\n', 1, '{{patient_name}} {{patient_id}} {{ipd_no}} {{case_id}} {{labor_time}} {{delivery_time}}', '', '', 'ipd', 1, '2021-09-17 02:54:13'),
(62, 'add_ipd_antenatal', 'Add IPD Antenatal', 'IPD Antenatal has been created for Patient: {{patient_name}} ({{patient_id}}).', 1, 'IPD Antenatal has been created for Patient: {{patient_name}} ({{patient_id}}). \r\n\r\n', 1, '{{patient_name}} {{patient_id}} {{ipd_no}} {{case_id}} {{date}}', '', '', 'ipd', 1, '2021-09-17 02:54:13'),
(63, 'add_opd_antenatal', 'Add OPD Antenatal', 'OPD Antenatal has been created for Patient: {{patient_name}} ({{patient_id}}).', 1, '\r\n OPD Antenatal has been created for Patient: {{patient_name}} ({{patient_id}}).', 1, '{{patient_name}} {{patient_id}} {{opd_no}} {{case_id}} {{date}}', '', '', 'opd', 1, '2021-09-17 02:54:13'),
(64, 'opd_new_checkup_created', 'OPD New Checkup Created', 'OPD New Checkup Created {{patient_name}} doctor and admin msg', 1, 'OPD New Checkup Created {{patient_name}} patient mesg', 1, '{{patient_name}} {{patient_id}} {{ipd_no}} {{case_id}} {{place_of_delivery}}', '', '', 'opd', 1, '2021-09-17 02:54:13');

-- --------------------------------------------------------

--
-- Table structure for table `tax_category`
--

CREATE TABLE `tax_category` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `percentage` float(10,2) DEFAULT '0.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int NOT NULL,
  `type` varchar(100) DEFAULT NULL,
  `section` varchar(50) NOT NULL,
  `patient_id` int DEFAULT NULL,
  `case_reference_id` int DEFAULT NULL,
  `opd_id` int DEFAULT NULL,
  `ipd_id` int DEFAULT NULL,
  `pharmacy_bill_basic_id` int DEFAULT NULL,
  `pathology_billing_id` int DEFAULT NULL,
  `radiology_billing_id` int DEFAULT NULL,
  `blood_donor_cycle_id` int DEFAULT NULL,
  `blood_issue_id` int DEFAULT NULL,
  `ambulance_call_id` int DEFAULT NULL,
  `appointment_id` int DEFAULT NULL,
  `attachment` text,
  `attachment_name` text,
  `amount_type` varchar(10) DEFAULT NULL,
  `amount` float(10,2) DEFAULT NULL,
  `payment_mode` varchar(100) DEFAULT NULL,
  `cheque_no` varchar(100) DEFAULT NULL,
  `cheque_date` date DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `note` text,
  `received_by` int DEFAULT NULL,
  `bill_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `transactions_processing`
--

CREATE TABLE `transactions_processing` (
  `id` int NOT NULL,
  `gateway_ins_id` int NOT NULL,
  `type` varchar(100) DEFAULT NULL,
  `section` varchar(50) NOT NULL,
  `patient_id` int DEFAULT NULL,
  `case_reference_id` int DEFAULT NULL,
  `opd_id` int DEFAULT NULL,
  `ipd_id` int DEFAULT NULL,
  `pharmacy_bill_basic_id` int DEFAULT NULL,
  `pathology_billing_id` int DEFAULT NULL,
  `radiology_billing_id` int DEFAULT NULL,
  `blood_donor_cycle_id` int DEFAULT NULL,
  `blood_issue_id` int DEFAULT NULL,
  `ambulance_call_id` int DEFAULT NULL,
  `appointment_id` int DEFAULT NULL,
  `attachment` varchar(250) DEFAULT NULL,
  `attachment_name` text,
  `amount_type` varchar(10) DEFAULT NULL,
  `amount` float(10,2) DEFAULT NULL,
  `payment_mode` varchar(100) DEFAULT NULL,
  `cheque_no` varchar(100) DEFAULT NULL,
  `cheque_date` date DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `note` text,
  `received_by` int DEFAULT NULL,
  `bill_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `unit`
--

CREATE TABLE `unit` (
  `id` int NOT NULL,
  `unit_name` varchar(100) NOT NULL,
  `unit_type` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `upload_contents`
--

CREATE TABLE `upload_contents` (
  `id` int NOT NULL,
  `content_type_id` int NOT NULL,
  `image` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `thumb_path` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `dir_path` varchar(300) DEFAULT NULL,
  `real_name` text NOT NULL,
  `img_name` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci,
  `thumb_name` varchar(300) DEFAULT NULL,
  `file_type` varchar(100) NOT NULL,
  `mime_type` text NOT NULL,
  `file_size` varchar(100) NOT NULL,
  `vid_url` text NOT NULL,
  `vid_title` varchar(250) NOT NULL,
  `upload_by` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `userlog`
--

CREATE TABLE `userlog` (
  `id` int NOT NULL,
  `user` varchar(100) DEFAULT NULL,
  `role` varchar(100) DEFAULT NULL,
  `ipaddress` varchar(100) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `login_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `childs` text,
  `role` varchar(30) NOT NULL,
  `verification_code` varchar(200) NOT NULL,
  `is_active` varchar(10) DEFAULT 'yes',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `users_authentication`
--

CREATE TABLE `users_authentication` (
  `id` int NOT NULL,
  `users_id` int DEFAULT NULL,
  `token` varchar(200) NOT NULL,
  `expired_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int NOT NULL,
  `vehicle_no` varchar(20) DEFAULT NULL,
  `vehicle_model` varchar(100) NOT NULL DEFAULT 'None',
  `manufacture_year` varchar(4) DEFAULT NULL,
  `vehicle_type` varchar(100) NOT NULL,
  `driver_name` varchar(50) DEFAULT NULL,
  `driver_licence` varchar(50) NOT NULL DEFAULT 'None',
  `driver_contact` varchar(20) DEFAULT NULL,
  `note` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `visitors_book`
--

CREATE TABLE `visitors_book` (
  `id` int NOT NULL,
  `source` varchar(100) DEFAULT NULL,
  `purpose` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact` varchar(12) NOT NULL,
  `id_proof` varchar(50) NOT NULL,
  `visit_to` varchar(20) NOT NULL,
  `ipd_opd_staff_id` int DEFAULT NULL,
  `related_to` varchar(60) NOT NULL,
  `no_of_pepple` int NOT NULL,
  `date` date NOT NULL,
  `in_time` varchar(20) NOT NULL,
  `out_time` varchar(20) NOT NULL,
  `note` mediumtext,
  `image` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `visitors_purpose`
--

CREATE TABLE `visitors_purpose` (
  `id` int NOT NULL,
  `visitors_purpose` varchar(100) NOT NULL,
  `description` mediumtext,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `visit_details`
--

CREATE TABLE `visit_details` (
  `id` int NOT NULL,
  `opd_details_id` int DEFAULT NULL,
  `organisation_id` int DEFAULT NULL,
  `patient_charge_id` int DEFAULT NULL,
  `transaction_id` int DEFAULT NULL,
  `cons_doctor` int DEFAULT NULL,
  `case_type` varchar(200) NOT NULL,
  `appointment_date` datetime DEFAULT NULL,
  `symptoms_type` int DEFAULT NULL,
  `symptoms` text,
  `bp` varchar(100) DEFAULT NULL,
  `height` varchar(100) DEFAULT NULL,
  `weight` varchar(100) DEFAULT NULL,
  `pulse` varchar(200) DEFAULT NULL,
  `temperature` varchar(200) DEFAULT NULL,
  `respiration` varchar(200) DEFAULT NULL,
  `known_allergies` varchar(100) DEFAULT NULL,
  `patient_old` varchar(50) DEFAULT NULL,
  `casualty` varchar(200) DEFAULT NULL,
  `refference` varchar(200) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `note` text,
  `note_remark` mediumtext,
  `payment_mode` varchar(100) NOT NULL,
  `generated_by` int DEFAULT NULL,
  `live_consult` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `is_antenatal` int NOT NULL,
  `can_delete` varchar(11) NOT NULL DEFAULT 'yes',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `vitals`
--

CREATE TABLE `vitals` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `reference_range` varchar(100) NOT NULL,
  `unit` varchar(11) DEFAULT NULL,
  `is_system` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `vitals`
--

INSERT INTO `vitals` (`id`, `name`, `reference_range`, `unit`, `is_system`, `created_at`) VALUES
(1, 'Height', '1  -  200', 'Centimeters', 1, '2024-03-14 06:03:18'),
(2, 'Weight', '0  -  150', 'Kilograms', 1, '2024-05-20 09:06:24'),
(3, 'Pulse ', '70 -   100 ', 'Beats per', 1, '2024-03-07 11:27:43'),
(4, 'Temperature', '95.8  -  99.3', 'Fahrenheit ', 1, '2024-05-16 10:59:30'),
(5, 'BP', '90/60  -  140/90', 'mmHg', 1, '2024-03-07 11:27:48');

-- --------------------------------------------------------

--
-- Table structure for table `zoom_settings`
--

CREATE TABLE `zoom_settings` (
  `id` int NOT NULL,
  `zoom_api_key` varchar(200) DEFAULT NULL,
  `zoom_api_secret` varchar(200) DEFAULT NULL,
  `use_doctor_api` int DEFAULT '1',
  `use_zoom_app` int DEFAULT '1',
  `opd_duration` int DEFAULT NULL,
  `ipd_duration` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `zoom_settings`
--

INSERT INTO `zoom_settings` (`id`, `zoom_api_key`, `zoom_api_secret`, `use_doctor_api`, `use_zoom_app`, `opd_duration`, `ipd_duration`, `created_at`) VALUES
(1, '', '', 0, 0, 0, 0, '2021-10-29 09:58:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ambulance_call`
--
ALTER TABLE `ambulance_call`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `generated_by` (`generated_by`),
  ADD KEY `case_reference_id` (`case_reference_id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `charge_id` (`charge_id`),
  ADD KEY `idx_contact_no` (`contact_no`),
  ADD KEY `idx_vehicle_model` (`vehicle_model`),
  ADD KEY `idx_driver` (`driver`),
  ADD KEY `index_date` (`date`),
  ADD KEY `index_call_from` (`call_from`),
  ADD KEY `index_call_to` (`call_to`),
  ADD KEY `index_charge_category_id` (`charge_category_id`),
  ADD KEY `index_standard_charge` (`standard_charge`),
  ADD KEY `index_discount_percentage` (`discount_percentage`),
  ADD KEY `index_discount` (`discount`),
  ADD KEY `index_tax_percentage` (`tax_percentage`),
  ADD KEY `index_amount` (`amount`),
  ADD KEY `index_net_amount` (`net_amount`);

--
-- Indexes for table `annual_calendar`
--
ALTER TABLE `annual_calendar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_holiday_type` (`holiday_type`),
  ADD KEY `idx_created_by` (`created_by`),
  ADD KEY `index_from_date` (`from_date`) USING BTREE,
  ADD KEY `index_to_date` (`to_date`) USING BTREE;

--
-- Indexes for table `antenatal_examine`
--
ALTER TABLE `antenatal_examine`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visit_details_id` (`visit_details_id`),
  ADD KEY `ipdid` (`ipdid`),
  ADD KEY `index_uter_size` (`uter_size`),
  ADD KEY `index_uterus_size` (`uterus_size`),
  ADD KEY `index_presentation_position` (`presentation_position`),
  ADD KEY `index_brim_presentation` (`brim_presentation`),
  ADD KEY `index_foeta_heart` (`foeta_heart`),
  ADD KEY `index_blood_pressure` (`blood_pressure`),
  ADD KEY `index_antenatal_oedema` (`antenatal_oedema`),
  ADD KEY `index_antenatal_weight` (`antenatal_weight`),
  ADD KEY `index_urine_sugar` (`urine_sugar`),
  ADD KEY `index_urine` (`urine`),
  ADD KEY `primary_examine_id` (`primary_examine_id`);

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor` (`doctor`),
  ADD KEY `case_reference_id` (`case_reference_id`),
  ADD KEY `visit_details_id` (`visit_details_id`),
  ADD KEY `doctor_shift_time_id` (`doctor_shift_time_id`);

--
-- Indexes for table `appointment_payment`
--
ALTER TABLE `appointment_payment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `charge_id` (`charge_id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `index_standard_amount` (`standard_amount`),
  ADD KEY `index_tax` (`tax`),
  ADD KEY `index_discount_percentage` (`discount_percentage`),
  ADD KEY `index_paid_amount` (`paid_amount`),
  ADD KEY `index_payment_mode` (`payment_mode`),
  ADD KEY `index_payment_type` (`payment_type`);

--
-- Indexes for table `appointment_queue`
--
ALTER TABLE `appointment_queue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `global_shift_id` (`shift_id`);

--
-- Indexes for table `appoint_priority`
--
ALTER TABLE `appoint_priority`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_appoint_priority` (`appoint_priority`);

--
-- Indexes for table `bed`
--
ALTER TABLE `bed`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bed_type_id` (`bed_type_id`),
  ADD KEY `bed_group_id` (`bed_group_id`),
  ADD KEY `index_name` (`name`);

--
-- Indexes for table `bed_group`
--
ALTER TABLE `bed_group`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_name` (`name`),
  ADD KEY `index_color` (`color`);

--
-- Indexes for table `bed_type`
--
ALTER TABLE `bed_type`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_name` (`name`);

--
-- Indexes for table `bill`
--
ALTER TABLE `bill`
  ADD PRIMARY KEY (`id`),
  ADD KEY `case_id` (`case_id`);

--
-- Indexes for table `birth_report`
--
ALTER TABLE `birth_report`
  ADD PRIMARY KEY (`id`),
  ADD KEY `case_reference_id` (`case_reference_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `index_child_name` (`child_name`),
  ADD KEY `index_gender` (`gender`),
  ADD KEY `index_weight` (`weight`),
  ADD KEY `index_contact` (`contact`);

--
-- Indexes for table `blood_bank_products`
--
ALTER TABLE `blood_bank_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_name` (`name`);

--
-- Indexes for table `blood_donor`
--
ALTER TABLE `blood_donor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `blood_bank_product_id` (`blood_bank_product_id`),
  ADD KEY `index_donor_name` (`donor_name`),
  ADD KEY `index_gender` (`gender`),
  ADD KEY `index_father_name` (`father_name`),
  ADD KEY `index_contact_no` (`contact_no`);

--
-- Indexes for table `blood_donor_cycle`
--
ALTER TABLE `blood_donor_cycle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `blood_bank_product_id` (`blood_bank_product_id`),
  ADD KEY `blood_donor_id` (`blood_donor_id`),
  ADD KEY `charge_id` (`charge_id`),
  ADD KEY `index_bag_no` (`bag_no`),
  ADD KEY `index_lot` (`lot`),
  ADD KEY `index_amount` (`amount`),
  ADD KEY `index_unit` (`unit`),
  ADD KEY `index_volume` (`volume`),
  ADD KEY `index_quantity` (`quantity`),
  ADD KEY `index_standard_charge` (`standard_charge`),
  ADD KEY `index_apply_charge` (`apply_charge`);

--
-- Indexes for table `blood_issue`
--
ALTER TABLE `blood_issue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `case_reference_id` (`case_reference_id`),
  ADD KEY `blood_donor_cycle_id` (`blood_donor_cycle_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `charge_id` (`charge_id`),
  ADD KEY `generated_by` (`generated_by`),
  ADD KEY `organisation_id` (`organisation_id`),
  ADD KEY `index_standard_charge` (`standard_charge`),
  ADD KEY `index_tax_percentage` (`tax_percentage`),
  ADD KEY `index_discount_percentage` (`discount_percentage`),
  ADD KEY `index_amount` (`amount`),
  ADD KEY `index_net_amount` (`net_amount`),
  ADD KEY `hospital_doctor` (`hospital_doctor`);

--
-- Indexes for table `captcha`
--
ALTER TABLE `captcha`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `case_references`
--
ALTER TABLE `case_references`
  ADD PRIMARY KEY (`id`),
  ADD KEY `case_references_ibfk_1` (`bill_id`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `charges`
--
ALTER TABLE `charges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `charge_category_id` (`charge_category_id`),
  ADD KEY `tax_category_id` (`tax_category_id`),
  ADD KEY `charge_unit_id` (`charge_unit_id`),
  ADD KEY `index_name` (`name`),
  ADD KEY `index_standard_charge` (`standard_charge`),
  ADD KEY `index_date` (`date`);

--
-- Indexes for table `charge_categories`
--
ALTER TABLE `charge_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `charge_type_id` (`charge_type_id`),
  ADD KEY `index_name` (`name`);

--
-- Indexes for table `charge_type_master`
--
ALTER TABLE `charge_type_master`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_charge_type` (`charge_type`);

--
-- Indexes for table `charge_type_module`
--
ALTER TABLE `charge_type_module`
  ADD PRIMARY KEY (`id`),
  ADD KEY `charge_type_master_id` (`charge_type_master_id`);

--
-- Indexes for table `charge_units`
--
ALTER TABLE `charge_units`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_unit` (`unit`);

--
-- Indexes for table `chat_connections`
--
ALTER TABLE `chat_connections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_user_one` (`chat_user_one`),
  ADD KEY `chat_user_two` (`chat_user_two`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_user_id` (`chat_user_id`),
  ADD KEY `chat_connection_id` (`chat_connection_id`);

--
-- Indexes for table `chat_users`
--
ALTER TABLE `chat_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `create_staff_id` (`create_staff_id`),
  ADD KEY `create_patient_id` (`create_patient_id`);

--
-- Indexes for table `complaint`
--
ALTER TABLE `complaint`
  ADD PRIMARY KEY (`id`),
  ADD KEY `complaint_type_id` (`complaint_type_id`);

--
-- Indexes for table `complaint_type`
--
ALTER TABLE `complaint_type`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_complaint_type` (`complaint_type`);

--
-- Indexes for table `conferences`
--
ALTER TABLE `conferences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `visit_details_id` (`visit_details_id`),
  ADD KEY `ipd_id` (`ipd_id`),
  ADD KEY `created_id` (`created_id`);

--
-- Indexes for table `conferences_history`
--
ALTER TABLE `conferences_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conference_id` (`conference_id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `conference_staff`
--
ALTER TABLE `conference_staff`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conference_id` (`conference_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `consultant_register`
--
ALTER TABLE `consultant_register`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ipd_id` (`ipd_id`),
  ADD KEY `cons_doctor` (`cons_doctor`);

--
-- Indexes for table `consult_charges`
--
ALTER TABLE `consult_charges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor` (`doctor`);

--
-- Indexes for table `contents`
--
ALTER TABLE `contents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_title` (`title`);

--
-- Indexes for table `content_for`
--
ALTER TABLE `content_for`
  ADD PRIMARY KEY (`id`),
  ADD KEY `content_id` (`content_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `content_types`
--
ALTER TABLE `content_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_name` (`name`);

--
-- Indexes for table `custom_fields`
--
ALTER TABLE `custom_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_name` (`name`),
  ADD KEY `index_belong_to` (`belong_to`),
  ADD KEY `index_type` (`type`),
  ADD KEY `index_visible_on_table` (`visible_on_table`),
  ADD KEY `index_visible_on_print` (`visible_on_print`),
  ADD KEY `index_visible_on_report` (`visible_on_report`);

--
-- Indexes for table `custom_field_values`
--
ALTER TABLE `custom_field_values`
  ADD PRIMARY KEY (`id`),
  ADD KEY `custom_field_id` (`custom_field_id`),
  ADD KEY `index_field_value` (`field_value`),
  ADD KEY `index_belong_table_id` (`belong_table_id`),
  ADD KEY `index_custom_field_id` (`custom_field_id`);

--
-- Indexes for table `death_report`
--
ALTER TABLE `death_report`
  ADD PRIMARY KEY (`id`),
  ADD KEY `case_reference_id` (`case_reference_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_department_name` (`department_name`);

--
-- Indexes for table `discharge_card`
--
ALTER TABLE `discharge_card`
  ADD PRIMARY KEY (`id`),
  ADD KEY `case_reference_id` (`case_reference_id`),
  ADD KEY `opd_details_id` (`opd_details_id`),
  ADD KEY `ipd_details_id` (`ipd_details_id`),
  ADD KEY `discharge_by` (`discharge_by`);

--
-- Indexes for table `dispatch_receive`
--
ALTER TABLE `dispatch_receive`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_reference_no` (`reference_no`),
  ADD KEY `index_to_title` (`to_title`),
  ADD KEY `index_from_title` (`from_title`),
  ADD KEY `index_date` (`date`);

--
-- Indexes for table `doctor_absent`
--
ALTER TABLE `doctor_absent`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `index_date` (`date`);

--
-- Indexes for table `doctor_global_shift`
--
ALTER TABLE `doctor_global_shift`
  ADD PRIMARY KEY (`id`),
  ADD KEY `global_shift_id` (`global_shift_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `doctor_shift_time`
--
ALTER TABLE `doctor_shift_time`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `doctor_global_shift_id` (`doctor_global_shift_id`),
  ADD KEY `index_day` (`day`);

--
-- Indexes for table `dose_duration`
--
ALTER TABLE `dose_duration`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_name` (`name`);

--
-- Indexes for table `dose_interval`
--
ALTER TABLE `dose_interval`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_name` (`name`);

--
-- Indexes for table `duty_roster_assign`
--
ALTER TABLE `duty_roster_assign`
  ADD PRIMARY KEY (`id`),
  ADD KEY `duty_roster_list_id` (`duty_roster_list_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `floor_id` (`floor_id`),
  ADD KEY `index_roster_duty_date` (`roster_duty_date`);

--
-- Indexes for table `duty_roster_list`
--
ALTER TABLE `duty_roster_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `duty_roster_shift_id` (`duty_roster_shift_id`),
  ADD KEY `index_duty_roster_start_date` (`duty_roster_start_date`),
  ADD KEY `index_duty_roster_end_date` (`duty_roster_end_date`);

--
-- Indexes for table `duty_roster_shift`
--
ALTER TABLE `duty_roster_shift`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_shift_name` (`shift_name`),
  ADD KEY `index_shift_start` (`shift_start`),
  ADD KEY `index_shift_end` (`shift_end`),
  ADD KEY `index_shift_hour` (`shift_hour`);

--
-- Indexes for table `email_config`
--
ALTER TABLE `email_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `start_date` (`start_date`),
  ADD KEY `index_end_date` (`end_date`),
  ADD KEY `index_event_type` (`event_type`) USING BTREE,
  ADD KEY `index_event_color` (`event_color`) USING BTREE,
  ADD KEY `index_event_title` (`event_title`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exp_head_id` (`exp_head_id`),
  ADD KEY `generated_by` (`generated_by`),
  ADD KEY `index_invoice_no` (`invoice_no`),
  ADD KEY `index_name` (`name`),
  ADD KEY `index_date` (`date`) USING BTREE,
  ADD KEY `index_amount` (`amount`) USING BTREE;

--
-- Indexes for table `expense_head`
--
ALTER TABLE `expense_head`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_exp_category` (`exp_category`);

--
-- Indexes for table `filetypes`
--
ALTER TABLE `filetypes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `finding`
--
ALTER TABLE `finding`
  ADD PRIMARY KEY (`id`),
  ADD KEY `finding_category_id` (`finding_category_id`),
  ADD KEY `index_name` (`name`);

--
-- Indexes for table `finding_category`
--
ALTER TABLE `finding_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_category` (`category`);

--
-- Indexes for table `floor`
--
ALTER TABLE `floor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_name` (`name`);

--
-- Indexes for table `front_cms_media_gallery`
--
ALTER TABLE `front_cms_media_gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `front_cms_menus`
--
ALTER TABLE `front_cms_menus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `front_cms_menu_items`
--
ALTER TABLE `front_cms_menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `front_cms_pages`
--
ALTER TABLE `front_cms_pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `front_cms_page_contents`
--
ALTER TABLE `front_cms_page_contents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `page_id` (`page_id`);

--
-- Indexes for table `front_cms_programs`
--
ALTER TABLE `front_cms_programs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `front_cms_program_photos`
--
ALTER TABLE `front_cms_program_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `front_cms_settings`
--
ALTER TABLE `front_cms_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gateway_ins`
--
ALTER TABLE `gateway_ins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_type` (`type`),
  ADD KEY `index_gateway_name` (`gateway_name`),
  ADD KEY `index_module_type` (`module_type`),
  ADD KEY `index_unique_id` (`unique_id`),
  ADD KEY `online_appointment_id` (`online_appointment_id`);

--
-- Indexes for table `gateway_ins_response`
--
ALTER TABLE `gateway_ins_response`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gateway_ins_id` (`gateway_ins_id`);

--
-- Indexes for table `general_calls`
--
ALTER TABLE `general_calls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_name` (`name`),
  ADD KEY `index_contact` (`contact`),
  ADD KEY `index_date` (`date`),
  ADD KEY `index_call_duration` (`call_duration`),
  ADD KEY `index_follow_up_date` (`follow_up_date`);

--
-- Indexes for table `global_shift`
--
ALTER TABLE `global_shift`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_name` (`name`),
  ADD KEY `index_start_time` (`start_time`),
  ADD KEY `index_end_time` (`end_time`);

--
-- Indexes for table `income`
--
ALTER TABLE `income`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inc_head_id` (`inc_head_id`),
  ADD KEY `generated_by` (`generated_by`),
  ADD KEY `index_name` (`name`),
  ADD KEY `index_invoice_no` (`invoice_no`),
  ADD KEY `index_date` (`date`),
  ADD KEY `index_amount` (`amount`);

--
-- Indexes for table `income_head`
--
ALTER TABLE `income_head`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_income_category` (`income_category`);

--
-- Indexes for table `ipd_details`
--
ALTER TABLE `ipd_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `case_reference_id` (`case_reference_id`),
  ADD KEY `cons_doctor` (`cons_doctor`),
  ADD KEY `bed_group_id` (`bed_group_id`),
  ADD KEY `bed` (`bed`);

--
-- Indexes for table `ipd_doctors`
--
ALTER TABLE `ipd_doctors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ipd_id` (`ipd_id`),
  ADD KEY `consult_doctor` (`consult_doctor`);

--
-- Indexes for table `ipd_prescription_basic`
--
ALTER TABLE `ipd_prescription_basic`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ipd_id` (`ipd_id`),
  ADD KEY `visit_details_id` (`visit_details_id`),
  ADD KEY `generated_by` (`generated_by`),
  ADD KEY `index_date` (`date`),
  ADD KEY `prescribe_by` (`prescribe_by`);

--
-- Indexes for table `ipd_prescription_details`
--
ALTER TABLE `ipd_prescription_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `basic_id` (`basic_id`),
  ADD KEY `pharmacy_id` (`pharmacy_id`),
  ADD KEY `index_dosage` (`dosage`),
  ADD KEY `index_dose_interval_id` (`dose_interval_id`),
  ADD KEY `index_dose_duration_id` (`dose_duration_id`);

--
-- Indexes for table `ipd_prescription_test`
--
ALTER TABLE `ipd_prescription_test`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ipd_prescription_basic_id` (`ipd_prescription_basic_id`),
  ADD KEY `pathology_id` (`pathology_id`),
  ADD KEY `radiology_id` (`radiology_id`);

--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_category_id` (`item_category_id`),
  ADD KEY `index_name` (`name`),
  ADD KEY `index_unit` (`unit`),
  ADD KEY `index_quantity` (`quantity`),
  ADD KEY `index_date` (`date`);

--
-- Indexes for table `item_category`
--
ALTER TABLE `item_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_item_category` (`item_category`);

--
-- Indexes for table `item_issue`
--
ALTER TABLE `item_issue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `item_category_id` (`item_category_id`),
  ADD KEY `issue_to` (`issue_to`),
  ADD KEY `index_issue_date` (`issue_date`),
  ADD KEY `index_return_date` (`return_date`),
  ADD KEY `index_quantity` (`quantity`),
  ADD KEY `index_is_returned` (`is_returned`);

--
-- Indexes for table `item_stock`
--
ALTER TABLE `item_stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `store_id` (`store_id`),
  ADD KEY `index_quantity` (`quantity`),
  ADD KEY `index_purchase_price` (`purchase_price`),
  ADD KEY `index_date` (`date`);

--
-- Indexes for table `item_store`
--
ALTER TABLE `item_store`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_item_store` (`item_store`),
  ADD KEY `index_code` (`code`);

--
-- Indexes for table `item_supplier`
--
ALTER TABLE `item_supplier`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_item_supplier` (`item_supplier`),
  ADD KEY `index_phone` (`phone`),
  ADD KEY `index_email` (`email`),
  ADD KEY `index_address` (`address`),
  ADD KEY `index_contact_person_name` (`contact_person_name`),
  ADD KEY `index_contact_person_phone` (`contact_person_phone`),
  ADD KEY `index_contact_person_email` (`contact_person_email`);

--
-- Indexes for table `lab`
--
ALTER TABLE `lab`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_language` (`language`),
  ADD KEY `index_short_code` (`short_code`),
  ADD KEY `index_country_code` (`country_code`),
  ADD KEY `index_is_deleted` (`is_deleted`),
  ADD KEY `index_is_rtl` (`is_rtl`);

--
-- Indexes for table `leave_types`
--
ALTER TABLE `leave_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type` (`type`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `medication_report`
--
ALTER TABLE `medication_report`
  ADD PRIMARY KEY (`id`),
  ADD KEY `generated_by` (`generated_by`),
  ADD KEY `pharmacy_id` (`pharmacy_id`),
  ADD KEY `ipd_id` (`ipd_id`),
  ADD KEY `medicine_dosage_id` (`medicine_dosage_id`),
  ADD KEY `opd_details_id` (`opd_details_id`),
  ADD KEY `index_date` (`date`),
  ADD KEY `index_time` (`time`);

--
-- Indexes for table `medicine_bad_stock`
--
ALTER TABLE `medicine_bad_stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pharmacy_id` (`pharmacy_id`),
  ADD KEY `medicine_batch_details_id` (`medicine_batch_details_id`),
  ADD KEY `index_outward_date` (`outward_date`),
  ADD KEY `index_expiry_date` (`expiry_date`),
  ADD KEY `index_batch_no` (`batch_no`),
  ADD KEY `index_quantity` (`quantity`);

--
-- Indexes for table `medicine_batch_details`
--
ALTER TABLE `medicine_batch_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_bill_basic_id` (`supplier_bill_basic_id`),
  ADD KEY `pharmacy_id` (`pharmacy_id`),
  ADD KEY `index_inward_date` (`inward_date`),
  ADD KEY `index_expiry` (`expiry`),
  ADD KEY `index_batch_no` (`batch_no`),
  ADD KEY `index_packing_qty` (`packing_qty`),
  ADD KEY `index_purchase_rate_packing` (`purchase_rate_packing`),
  ADD KEY `index_quantity` (`quantity`),
  ADD KEY `index_mrp` (`mrp`),
  ADD KEY `index_purchase_price` (`purchase_price`),
  ADD KEY `index_tax` (`tax`),
  ADD KEY `index_sale_rate` (`sale_rate`),
  ADD KEY `index_batch_amount` (`batch_amount`),
  ADD KEY `index_amount` (`amount`),
  ADD KEY `index_available_quantity` (`available_quantity`);

--
-- Indexes for table `medicine_category`
--
ALTER TABLE `medicine_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_medicine_category` (`medicine_category`);

--
-- Indexes for table `medicine_dosage`
--
ALTER TABLE `medicine_dosage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `medicine_category_id` (`medicine_category_id`),
  ADD KEY `charge_units_id` (`units_id`),
  ADD KEY `index_dosage` (`dosage`);

--
-- Indexes for table `medicine_group`
--
ALTER TABLE `medicine_group`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_group_name` (`group_name`);

--
-- Indexes for table `medicine_supplier`
--
ALTER TABLE `medicine_supplier`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_supplier` (`supplier`),
  ADD KEY `index_contact` (`contact`),
  ADD KEY `index_supplier_person` (`supplier_person`),
  ADD KEY `index_supplier_person_contact` (`supplier_person_contact`),
  ADD KEY `index_supplier_drug_licence` (`supplier_drug_licence`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_title` (`title`),
  ADD KEY `index_send_mail` (`send_mail`),
  ADD KEY `index_send_sms` (`send_sms`),
  ADD KEY `index_is_group` (`is_group`),
  ADD KEY `index_is_individual` (`is_individual`);

--
-- Indexes for table `notification_roles`
--
ALTER TABLE `notification_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `send_notification_id` (`send_notification_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `notification_setting`
--
ALTER TABLE `notification_setting`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_type` (`type`),
  ADD KEY `index_is_mail` (`is_mail`),
  ADD KEY `index_is_sms` (`is_sms`),
  ADD KEY `index_is_mobileapp` (`is_mobileapp`),
  ADD KEY `index_is_notification` (`is_notification`),
  ADD KEY `index_display_notification` (`display_notification`),
  ADD KEY `index_display_sms` (`display_sms`);

--
-- Indexes for table `nurse_note`
--
ALTER TABLE `nurse_note`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ipd_id` (`ipd_id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `index_date` (`date`);

--
-- Indexes for table `nurse_notes_comment`
--
ALTER TABLE `nurse_notes_comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nurse_note_id` (`nurse_note_id`),
  ADD KEY `comment_staffid` (`comment_staffid`);

--
-- Indexes for table `obstetric_history`
--
ALTER TABLE `obstetric_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_place_of_delivery` (`place_of_delivery`),
  ADD KEY `index_pregnancy_duration` (`pregnancy_duration`),
  ADD KEY `index_pregnancy_complications` (`pregnancy_complications`),
  ADD KEY `index_birth_weight` (`birth_weight`),
  ADD KEY `index_gender` (`gender`),
  ADD KEY `index_infant_feeding` (`infant_feeding`),
  ADD KEY `index_alive_dead` (`alive_dead`),
  ADD KEY `index_date` (`date`),
  ADD KEY `index_death_cause` (`death_cause`) USING BTREE;

--
-- Indexes for table `opd_details`
--
ALTER TABLE `opd_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `case_reference_id` (`case_reference_id`),
  ADD KEY `generated_by` (`generated_by`);

--
-- Indexes for table `operation`
--
ALTER TABLE `operation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `index_operation` (`operation`);

--
-- Indexes for table `operation_category`
--
ALTER TABLE `operation_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_category` (`category`);

--
-- Indexes for table `operation_theatre`
--
ALTER TABLE `operation_theatre`
  ADD PRIMARY KEY (`id`),
  ADD KEY `opd_details_id` (`opd_details_id`),
  ADD KEY `ipd_details_id` (`ipd_details_id`),
  ADD KEY `consultant_doctor` (`consultant_doctor`),
  ADD KEY `generated_by` (`generated_by`),
  ADD KEY `operation_id` (`operation_id`),
  ADD KEY `index_date` (`date`);

--
-- Indexes for table `organisation`
--
ALTER TABLE `organisation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_organisation_name` (`organisation_name`),
  ADD KEY `index_code` (`code`),
  ADD KEY `index_contact_no` (`contact_no`),
  ADD KEY `index_address` (`address`),
  ADD KEY `index_contact_person_name` (`contact_person_name`),
  ADD KEY `index_contact_person_phone` (`contact_person_phone`);

--
-- Indexes for table `organisations_charges`
--
ALTER TABLE `organisations_charges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `org_id` (`org_id`),
  ADD KEY `charge_id` (`charge_id`);

--
-- Indexes for table `pathology`
--
ALTER TABLE `pathology`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pathology_category_id` (`pathology_category_id`),
  ADD KEY `charge_id` (`charge_id`),
  ADD KEY `index_test_name` (`test_name`),
  ADD KEY `index_short_name` (`short_name`),
  ADD KEY `index_test_type` (`test_type`),
  ADD KEY `index_unit` (`unit`),
  ADD KEY `index_sub_category` (`sub_category`),
  ADD KEY `index_report_days` (`report_days`),
  ADD KEY `index_method` (`method`);

--
-- Indexes for table `pathology_billing`
--
ALTER TABLE `pathology_billing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `case_reference_id` (`case_reference_id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `generated_by` (`generated_by`),
  ADD KEY `ipd_prescription_basic_id` (`ipd_prescription_basic_id`),
  ADD KEY `index_date` (`date`),
  ADD KEY `index_doctor_name` (`doctor_name`),
  ADD KEY `index_total` (`total`),
  ADD KEY `index_discount_percentage` (`discount_percentage`),
  ADD KEY `index_discount` (`discount`),
  ADD KEY `index_tax_percentage` (`tax_percentage`),
  ADD KEY `index_tax` (`tax`),
  ADD KEY `index_net_amount` (`net_amount`),
  ADD KEY `organisation_id` (`organisation_id`);

--
-- Indexes for table `pathology_category`
--
ALTER TABLE `pathology_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_category_name` (`category_name`);

--
-- Indexes for table `pathology_parameter`
--
ALTER TABLE `pathology_parameter`
  ADD PRIMARY KEY (`id`),
  ADD KEY `unit` (`unit`),
  ADD KEY `index_parameter_name` (`parameter_name`),
  ADD KEY `index_test_value` (`test_value`),
  ADD KEY `index_reference_range` (`reference_range`),
  ADD KEY `index_range_from` (`range_from`),
  ADD KEY `index_range_to` (`range_to`),
  ADD KEY `index_gender` (`gender`),
  ADD KEY `index_unit` (`unit`);

--
-- Indexes for table `pathology_parameterdetails`
--
ALTER TABLE `pathology_parameterdetails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pathology_id` (`pathology_id`),
  ADD KEY `pathology_parameter_id` (`pathology_parameter_id`);

--
-- Indexes for table `pathology_report`
--
ALTER TABLE `pathology_report`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `pathology_bill_id` (`pathology_bill_id`),
  ADD KEY `pathology_id` (`pathology_id`),
  ADD KEY `collection_specialist` (`collection_specialist`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `index_reporting_date` (`reporting_date`),
  ADD KEY `index_parameter_update` (`parameter_update`),
  ADD KEY `index_tax_percentage` (`tax_percentage`),
  ADD KEY `index_apply_charge` (`apply_charge`),
  ADD KEY `index_collection_date` (`collection_date`);

--
-- Indexes for table `pathology_report_parameterdetails`
--
ALTER TABLE `pathology_report_parameterdetails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pathology_report_id` (`pathology_report_id`),
  ADD KEY `pathology_parameterdetail_id` (`pathology_parameterdetail_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `blood_bank_product_id` (`blood_bank_product_id`),
  ADD KEY `idx_patient_name` (`patient_name`),
  ADD KEY `idx_dob` (`dob`),
  ADD KEY `idx_age` (`age`),
  ADD KEY `idx_month` (`month`),
  ADD KEY `idx_mobileno` (`mobileno`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_gender` (`gender`),
  ADD KEY `idx_marital_status` (`marital_status`),
  ADD KEY `idx_address` (`address`(500)),
  ADD KEY `idx_guardian_name` (`guardian_name`),
  ADD KEY `organisation_id` (`organisation_id`);

--
-- Indexes for table `patients_vitals`
--
ALTER TABLE `patients_vitals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `index_reference_range` (`reference_range`),
  ADD KEY `index_messure_date` (`messure_date`),
  ADD KEY `vital_id` (`vital_id`);

--
-- Indexes for table `patient_bed_history`
--
ALTER TABLE `patient_bed_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `case_reference_id` (`case_reference_id`),
  ADD KEY `bed_group_id` (`bed_group_id`),
  ADD KEY `bed_id` (`bed_id`),
  ADD KEY `index_from_date` (`from_date`),
  ADD KEY `index_to_date` (`to_date`);

--
-- Indexes for table `patient_charges`
--
ALTER TABLE `patient_charges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `opd_id` (`opd_id`),
  ADD KEY `ipd_id` (`ipd_id`),
  ADD KEY `charge_id` (`charge_id`),
  ADD KEY `organisation_id` (`organisation_id`),
  ADD KEY `index_qty` (`qty`),
  ADD KEY `index_standard_charge` (`standard_charge`),
  ADD KEY `index_tpa_charge` (`tpa_charge`),
  ADD KEY `index_discount_percentage` (`discount_percentage`),
  ADD KEY `index_tax` (`tax`),
  ADD KEY `index_apply_charge` (`apply_charge`),
  ADD KEY `index_amount` (`amount`);

--
-- Indexes for table `patient_id_card`
--
ALTER TABLE `patient_id_card`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_title` (`title`),
  ADD KEY `index_hospital_name` (`hospital_name`),
  ADD KEY `index_hospital_address` (`hospital_address`),
  ADD KEY `index_header_color` (`header_color`),
  ADD KEY `index_enable_patient_name` (`enable_patient_name`),
  ADD KEY `index_enable_guardian_name` (`enable_guardian_name`),
  ADD KEY `index_enable_patient_unique_id` (`enable_patient_unique_id`),
  ADD KEY `index_enable_address` (`enable_address`),
  ADD KEY `index_enable_phone` (`enable_phone`),
  ADD KEY `index_enable_dob` (`enable_dob`),
  ADD KEY `index_enable_blood_group` (`enable_blood_group`),
  ADD KEY `index_status` (`status`),
  ADD KEY `index_enable_barcode` (`enable_barcode`);

--
-- Indexes for table `patient_timeline`
--
ALTER TABLE `patient_timeline`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `generated_users_id` (`generated_users_id`),
  ADD KEY `index_title` (`title`),
  ADD KEY `index_date` (`date`),
  ADD KEY `index_timeline_date` (`timeline_date`);

--
-- Indexes for table `payment_settings`
--
ALTER TABLE `payment_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_payment_type` (`payment_type`),
  ADD KEY `index_api_username` (`api_username`),
  ADD KEY `index_api_secret_key` (`api_secret_key`),
  ADD KEY `index_salt` (`salt`),
  ADD KEY `index_api_publishable_key` (`api_publishable_key`),
  ADD KEY `index_paytm_website` (`paytm_website`),
  ADD KEY `index_paytm_industrytype` (`paytm_industrytype`),
  ADD KEY `index_api_password` (`api_password`),
  ADD KEY `index_api_signature` (`api_signature`),
  ADD KEY `index_api_email` (`api_email`),
  ADD KEY `index_paypal_demo` (`paypal_demo`),
  ADD KEY `index_account_no` (`account_no`),
  ADD KEY `index_is_active` (`is_active`);

--
-- Indexes for table `payslip_allowance`
--
ALTER TABLE `payslip_allowance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `staff_payslip_id` (`staff_payslip_id`),
  ADD KEY `index_allowance_type` (`allowance_type`),
  ADD KEY `index_amount` (`amount`),
  ADD KEY `index_cal_type` (`cal_type`);

--
-- Indexes for table `permission_category`
--
ALTER TABLE `permission_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_name` (`name`);

--
-- Indexes for table `permission_group`
--
ALTER TABLE `permission_group`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_name` (`name`),
  ADD KEY `index_short_code` (`short_code`);

--
-- Indexes for table `permission_patient`
--
ALTER TABLE `permission_patient`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_permission_group_short_code` (`permission_group_short_code`),
  ADD KEY `index_name` (`name`),
  ADD KEY `index_short_code` (`short_code`),
  ADD KEY `index_is_active` (`is_active`);

--
-- Indexes for table `pharmacy`
--
ALTER TABLE `pharmacy`
  ADD PRIMARY KEY (`id`),
  ADD KEY `medicine_category_id` (`medicine_category_id`),
  ADD KEY `idx_medicine_name` (`medicine_name`),
  ADD KEY `index_medicine_name` (`medicine_name`),
  ADD KEY `index_medicine_company` (`medicine_company`),
  ADD KEY `index_medicine_composition` (`medicine_composition`),
  ADD KEY `index_medicine_group` (`medicine_group`),
  ADD KEY `index_unit` (`unit`),
  ADD KEY `index_min_level` (`min_level`),
  ADD KEY `index_reorder_level` (`reorder_level`),
  ADD KEY `index_vat` (`vat`),
  ADD KEY `index_unit_packing` (`unit_packing`),
  ADD KEY `index_vat_ac` (`vat_ac`),
  ADD KEY `index_rack_number` (`rack_number`);

--
-- Indexes for table `pharmacy_bill_basic`
--
ALTER TABLE `pharmacy_bill_basic`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `case_reference_id` (`case_reference_id`),
  ADD KEY `generated_by` (`generated_by`),
  ADD KEY `ipd_prescription_basic_id` (`ipd_prescription_basic_id`),
  ADD KEY `index_customer_name` (`customer_name`),
  ADD KEY `index_customer_type` (`customer_type`),
  ADD KEY `index_doctor_name` (`doctor_name`),
  ADD KEY `index_total` (`total`),
  ADD KEY `index_discount_percentage` (`discount_percentage`),
  ADD KEY `index_discount` (`discount`),
  ADD KEY `index_tax_percentage` (`tax_percentage`),
  ADD KEY `index_tax` (`tax`),
  ADD KEY `index_net_amount` (`net_amount`);

--
-- Indexes for table `pharmacy_bill_detail`
--
ALTER TABLE `pharmacy_bill_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pharmacy_bill_basic_id` (`pharmacy_bill_basic_id`),
  ADD KEY `medicine_batch_detail_id` (`medicine_batch_detail_id`),
  ADD KEY `index_quantity` (`quantity`),
  ADD KEY `index_sale_price` (`sale_price`),
  ADD KEY `index_amount` (`amount`);

--
-- Indexes for table `pharmacy_company`
--
ALTER TABLE `pharmacy_company`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_company_name` (`company_name`);

--
-- Indexes for table `postnatal_examine`
--
ALTER TABLE `postnatal_examine`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `index_labor_time` (`labor_time`),
  ADD KEY `index_delivery_time` (`delivery_time`);

--
-- Indexes for table `prefixes`
--
ALTER TABLE `prefixes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_type` (`type`),
  ADD KEY `index_prefix` (`prefix`);

--
-- Indexes for table `primary_examine`
--
ALTER TABLE `primary_examine`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visit_details_id` (`visit_details_id`),
  ADD KEY `ipdid` (`ipdid`),
  ADD KEY `index_bleeding` (`bleeding`),
  ADD KEY `index_headache` (`headache`),
  ADD KEY `index_pain` (`pain`),
  ADD KEY `index_constipation` (`constipation`),
  ADD KEY `index_urinary_symptoms` (`urinary_symptoms`),
  ADD KEY `index_vomiting` (`vomiting`),
  ADD KEY `index_cough` (`cough`),
  ADD KEY `index_vaginal` (`vaginal`),
  ADD KEY `index_discharge` (`discharge`),
  ADD KEY `index_oedema` (`oedema`),
  ADD KEY `index_haemoroids` (`haemoroids`),
  ADD KEY `index_weight` (`weight`),
  ADD KEY `index_height` (`height`),
  ADD KEY `index_date` (`date`);

--
-- Indexes for table `print_setting`
--
ALTER TABLE `print_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `radio`
--
ALTER TABLE `radio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `charge_id` (`charge_id`),
  ADD KEY `index_test_name` (`test_name`),
  ADD KEY `index_short_name` (`short_name`),
  ADD KEY `index_test_type` (`test_type`);

--
-- Indexes for table `radiology_billing`
--
ALTER TABLE `radiology_billing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `case_reference_id` (`case_reference_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `generated_by` (`generated_by`),
  ADD KEY `ipd_prescription_basic_id` (`ipd_prescription_basic_id`),
  ADD KEY `index_date` (`date`),
  ADD KEY `index_doctor_name` (`doctor_name`),
  ADD KEY `index_total` (`total`),
  ADD KEY `index_discount_percentage` (`discount_percentage`),
  ADD KEY `index_discount` (`discount`),
  ADD KEY `index_tax_percentage` (`tax_percentage`),
  ADD KEY `index_tax` (`tax`),
  ADD KEY `index_net_amount` (`net_amount`),
  ADD KEY `index_transaction_id` (`transaction_id`),
  ADD KEY `index_organisation_id` (`organisation_id`),
  ADD KEY `index_insurance_validity` (`insurance_validity`),
  ADD KEY `index_insurance_id` (`insurance_id`);

--
-- Indexes for table `radiology_parameter`
--
ALTER TABLE `radiology_parameter`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_parameter_name` (`parameter_name`),
  ADD KEY `index_test_value` (`test_value`),
  ADD KEY `index_reference_range` (`reference_range`),
  ADD KEY `index_range_from` (`range_from`),
  ADD KEY `index_range_to` (`range_to`),
  ADD KEY `index_gender` (`gender`),
  ADD KEY `index_unit` (`unit`);

--
-- Indexes for table `radiology_parameterdetails`
--
ALTER TABLE `radiology_parameterdetails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `radiology_id` (`radiology_id`),
  ADD KEY `radiology_parameter_id` (`radiology_parameter_id`);

--
-- Indexes for table `radiology_report`
--
ALTER TABLE `radiology_report`
  ADD PRIMARY KEY (`id`),
  ADD KEY `radiology_id` (`radiology_id`),
  ADD KEY `radiology_bill_id` (`radiology_bill_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `generated_by` (`generated_by`),
  ADD KEY `collection_specialist` (`collection_specialist`),
  ADD KEY `approved_by` (`approved_by`),
  ADD KEY `index_customer_type` (`customer_type`),
  ADD KEY `index_patient_name` (`patient_name`),
  ADD KEY `index_consultant_doctor` (`consultant_doctor`),
  ADD KEY `index_reporting_date` (`reporting_date`),
  ADD KEY `index_parameter_update` (`parameter_update`),
  ADD KEY `index_tax_percentage` (`tax_percentage`),
  ADD KEY `index_apply_charge` (`apply_charge`),
  ADD KEY `index_radiology_center` (`radiology_center`);

--
-- Indexes for table `radiology_report_parameterdetails`
--
ALTER TABLE `radiology_report_parameterdetails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `radiology_report_id` (`radiology_report_id`),
  ADD KEY `radiology_parameterdetail_id` (`radiology_parameterdetail_id`);

--
-- Indexes for table `read_notification`
--
ALTER TABLE `read_notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `read_systemnotification`
--
ALTER TABLE `read_systemnotification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notification_id` (`notification_id`);

--
-- Indexes for table `referral_category`
--
ALTER TABLE `referral_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_name` (`name`);

--
-- Indexes for table `referral_commission`
--
ALTER TABLE `referral_commission`
  ADD PRIMARY KEY (`id`),
  ADD KEY `referral_category_id` (`referral_category_id`),
  ADD KEY `referral_type_id` (`referral_type_id`);

--
-- Indexes for table `referral_payment`
--
ALTER TABLE `referral_payment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `referral_person_id` (`referral_person_id`),
  ADD KEY `referral_type` (`referral_type`),
  ADD KEY `index_bill_amount` (`bill_amount`),
  ADD KEY `index_percentage` (`percentage`),
  ADD KEY `index_amount` (`amount`),
  ADD KEY `index_date` (`date`);

--
-- Indexes for table `referral_person`
--
ALTER TABLE `referral_person`
  ADD PRIMARY KEY (`id`),
  ADD KEY `referral_category` (`category_id`),
  ADD KEY `index_name` (`name`),
  ADD KEY `index_contact` (`contact`),
  ADD KEY `index_person_name` (`person_name`),
  ADD KEY `index_person_phone` (`person_phone`),
  ADD KEY `index_standard_commission` (`standard_commission`);

--
-- Indexes for table `referral_person_commission`
--
ALTER TABLE `referral_person_commission`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `referral_person_id` (`referral_person_id`),
  ADD KEY `referral_type_id` (`referral_type_id`);

--
-- Indexes for table `referral_type`
--
ALTER TABLE `referral_type`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `index_name` (`name`),
  ADD KEY `index_prefixes_type` (`prefixes_type`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_name` (`name`);

--
-- Indexes for table `roles_permissions`
--
ALTER TABLE `roles_permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_can_view` (`can_view`),
  ADD KEY `index_can_add` (`can_add`),
  ADD KEY `index_can_edit` (`can_edit`),
  ADD KEY `index_can_delete` (`can_delete`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `perm_cat_id` (`perm_cat_id`);

--
-- Indexes for table `sch_settings`
--
ALTER TABLE `sch_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lang_id` (`lang_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `send_notification`
--
ALTER TABLE `send_notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_id` (`created_id`),
  ADD KEY `index_title` (`title`),
  ADD KEY `index_publish_date` (`publish_date`),
  ADD KEY `index_date` (`date`),
  ADD KEY `index_visible_staff` (`visible_staff`),
  ADD KEY `index_visible_patient` (`visible_patient`);

--
-- Indexes for table `share_contents`
--
ALTER TABLE `share_contents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `index_send_to` (`send_to`),
  ADD KEY `index_share_date` (`share_date`),
  ADD KEY `index_valid_upto` (`valid_upto`),
  ADD KEY `index_created_by` (`created_by`);

--
-- Indexes for table `share_content_for`
--
ALTER TABLE `share_content_for`
  ADD PRIMARY KEY (`id`),
  ADD KEY `upload_content_id` (`share_content_id`),
  ADD KEY `student_id` (`patient_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `share_upload_contents`
--
ALTER TABLE `share_upload_contents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `upload_content_id` (`upload_content_id`),
  ADD KEY `share_content_id` (`share_content_id`);

--
-- Indexes for table `shift_details`
--
ALTER TABLE `shift_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `charge_id` (`charge_id`),
  ADD KEY `index_consult_duration` (`consult_duration`);

--
-- Indexes for table `sms_config`
--
ALTER TABLE `sms_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `source`
--
ALTER TABLE `source`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_source` (`source`);

--
-- Indexes for table `specialist`
--
ALTER TABLE `specialist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_specialist_name` (`specialist_name`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`),
  ADD KEY `staff_designation_id` (`staff_designation_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `index_name` (`name`),
  ADD KEY `index_surname` (`surname`),
  ADD KEY `index_father_name` (`father_name`),
  ADD KEY `index_mother_name` (`mother_name`),
  ADD KEY `index_contact_no` (`contact_no`),
  ADD KEY `index_emergency_contact_no` (`emergency_contact_no`),
  ADD KEY `index_email` (`email`),
  ADD KEY `index_dob` (`dob`),
  ADD KEY `index_marital_status` (`marital_status`),
  ADD KEY `index_date_of_joining` (`date_of_joining`),
  ADD KEY `index_date_of_leaving` (`date_of_leaving`),
  ADD KEY `index_gender` (`gender`),
  ADD KEY `index_blood_group` (`blood_group`);

--
-- Indexes for table `staff_attendance`
--
ALTER TABLE `staff_attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `staff_attendance_type_id` (`staff_attendance_type_id`);

--
-- Indexes for table `staff_attendance_type`
--
ALTER TABLE `staff_attendance_type`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_type` (`type`),
  ADD KEY `index_key_value` (`key_value`),
  ADD KEY `index_is_active` (`is_active`),
  ADD KEY `index_long_lang_name` (`long_lang_name`),
  ADD KEY `index_long_name_style` (`long_name_style`),
  ADD KEY `index_for_schedule` (`for_schedule`);

--
-- Indexes for table `staff_attendence_schedules`
--
ALTER TABLE `staff_attendence_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `staff_attendence_type_id` (`staff_attendence_type_id`),
  ADD KEY `index_entry_time_from` (`entry_time_from`),
  ADD KEY `index_entry_time_to` (`entry_time_to`),
  ADD KEY `index_total_institute_hour` (`total_institute_hour`);

--
-- Indexes for table `staff_designation`
--
ALTER TABLE `staff_designation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_designation` (`designation`);

--
-- Indexes for table `staff_id_card`
--
ALTER TABLE `staff_id_card`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_title` (`title`),
  ADD KEY `index_hospital_name` (`hospital_name`),
  ADD KEY `index_hospital_address` (`hospital_address`),
  ADD KEY `index_header_color` (`header_color`),
  ADD KEY `index_enable_staff_role` (`enable_staff_role`),
  ADD KEY `index_enable_staff_id` (`enable_staff_id`),
  ADD KEY `index_enable_staff_department` (`enable_staff_department`),
  ADD KEY `index_enable_designation` (`enable_designation`),
  ADD KEY `index_enable_name` (`enable_name`),
  ADD KEY `index_enable_fathers_name` (`enable_fathers_name`),
  ADD KEY `index_enable_mothers_name` (`enable_mothers_name`),
  ADD KEY `index_enable_date_of_joining` (`enable_date_of_joining`),
  ADD KEY `index_enable_permanent_address` (`enable_permanent_address`),
  ADD KEY `index_enable_staff_dob` (`enable_staff_dob`),
  ADD KEY `index_enable_staff_phone` (`enable_staff_phone`),
  ADD KEY `index_enable_staff_barcode` (`enable_staff_barcode`),
  ADD KEY `index_status` (`status`);

--
-- Indexes for table `staff_leave_details`
--
ALTER TABLE `staff_leave_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `leave_type_id` (`leave_type_id`);

--
-- Indexes for table `staff_leave_request`
--
ALTER TABLE `staff_leave_request`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `leave_type_id` (`leave_type_id`),
  ADD KEY `applied_by` (`applied_by`),
  ADD KEY `index_leave_from` (`leave_from`),
  ADD KEY `index_leave_to` (`leave_to`),
  ADD KEY `index_leave_days` (`leave_days`),
  ADD KEY `index_employee_remark` (`employee_remark`),
  ADD KEY `index_admin_remark` (`admin_remark`),
  ADD KEY `index_status` (`status`),
  ADD KEY `index_approved_date` (`approved_date`),
  ADD KEY `index_status_updated_by` (`status_updated_by`),
  ADD KEY `index_date` (`date`);

--
-- Indexes for table `staff_payroll`
--
ALTER TABLE `staff_payroll`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff_payslip`
--
ALTER TABLE `staff_payslip`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `generated_by` (`generated_by`),
  ADD KEY `index_basic` (`basic`),
  ADD KEY `index_total_allowance` (`total_allowance`),
  ADD KEY `index_total_deduction` (`total_deduction`),
  ADD KEY `index_leave_deduction` (`leave_deduction`),
  ADD KEY `index_tax` (`tax`),
  ADD KEY `index_net_salary` (`net_salary`),
  ADD KEY `index_status` (`status`),
  ADD KEY `index_month` (`month`),
  ADD KEY `index_year` (`year`),
  ADD KEY `index_cheque_no` (`cheque_no`),
  ADD KEY `index_cheque_date` (`cheque_date`),
  ADD KEY `index_attachment` (`attachment`),
  ADD KEY `index_payment_mode` (`payment_mode`),
  ADD KEY `index_payment_date` (`payment_date`);

--
-- Indexes for table `staff_roles`
--
ALTER TABLE `staff_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `staff_timeline`
--
ALTER TABLE `staff_timeline`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `index_title` (`title`),
  ADD KEY `index_timeline_date` (`timeline_date`),
  ADD KEY `index_status` (`status`),
  ADD KEY `index_date` (`date`);

--
-- Indexes for table `supplier_bill_basic`
--
ALTER TABLE `supplier_bill_basic`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `received_by` (`received_by`),
  ADD KEY `index_total` (`total`),
  ADD KEY `index_tax` (`tax`),
  ADD KEY `index_discount` (`discount`),
  ADD KEY `index_net_amount` (`net_amount`),
  ADD KEY `index_payment_mode` (`payment_mode`),
  ADD KEY `index_cheque_no` (`cheque_no`),
  ADD KEY `index_cheque_date` (`cheque_date`),
  ADD KEY `index_payment_date` (`payment_date`);

--
-- Indexes for table `symptoms`
--
ALTER TABLE `symptoms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_symptoms_title` (`symptoms_title`);

--
-- Indexes for table `symptoms_classification`
--
ALTER TABLE `symptoms_classification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_symptoms_type` (`symptoms_type`);

--
-- Indexes for table `system_notification`
--
ALTER TABLE `system_notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notification_title` (`notification_title`);

--
-- Indexes for table `system_notification_setting`
--
ALTER TABLE `system_notification_setting`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_event` (`event`),
  ADD KEY `index_is_staff` (`is_staff`),
  ADD KEY `index_is_patient` (`is_patient`);

--
-- Indexes for table `tax_category`
--
ALTER TABLE `tax_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_name` (`name`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `case_reference_id` (`case_reference_id`),
  ADD KEY `opd_id` (`opd_id`),
  ADD KEY `ipd_id` (`ipd_id`),
  ADD KEY `pharmacy_bill_basic_id` (`pharmacy_bill_basic_id`),
  ADD KEY `pathology_billing_id` (`pathology_billing_id`),
  ADD KEY `radiology_billing_id` (`radiology_billing_id`),
  ADD KEY `blood_donor_cycle_id` (`blood_donor_cycle_id`),
  ADD KEY `blood_issue_id` (`blood_issue_id`),
  ADD KEY `ambulance_call_id` (`ambulance_call_id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `bill_id` (`bill_id`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_patient_id` (`patient_id`),
  ADD KEY `index_type` (`type`),
  ADD KEY `index_section` (`section`);

--
-- Indexes for table `transactions_processing`
--
ALTER TABLE `transactions_processing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `case_reference_id` (`case_reference_id`),
  ADD KEY `opd_id` (`opd_id`),
  ADD KEY `ipd_id` (`ipd_id`),
  ADD KEY `pharmacy_bill_basic_id` (`pharmacy_bill_basic_id`),
  ADD KEY `pathology_billing_id` (`pathology_billing_id`),
  ADD KEY `radiology_billing_id` (`radiology_billing_id`),
  ADD KEY `blood_donor_cycle_id` (`blood_donor_cycle_id`),
  ADD KEY `blood_issue_id` (`blood_issue_id`),
  ADD KEY `ambulance_call_id` (`ambulance_call_id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `bill_id` (`bill_id`),
  ADD KEY `index_attachment` (`attachment`),
  ADD KEY `index_amount_type` (`amount_type`),
  ADD KEY `index_amount` (`amount`),
  ADD KEY `index_payment_mode` (`payment_mode`),
  ADD KEY `index_cheque_no` (`cheque_no`),
  ADD KEY `index_cheque_date` (`cheque_date`),
  ADD KEY `index_payment_date` (`payment_date`);

--
-- Indexes for table `unit`
--
ALTER TABLE `unit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_unit_name` (`unit_name`),
  ADD KEY `index_unit_type` (`unit_type`);

--
-- Indexes for table `upload_contents`
--
ALTER TABLE `upload_contents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `upload_by` (`upload_by`),
  ADD KEY `upload_contents_ibfk_2` (`content_type_id`);

--
-- Indexes for table `userlog`
--
ALTER TABLE `userlog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_authentication`
--
ALTER TABLE `users_authentication`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_vehicle_no` (`vehicle_no`),
  ADD KEY `index_vehicle_model` (`vehicle_model`),
  ADD KEY `index_manufacture_year` (`manufacture_year`),
  ADD KEY `index_vehicle_type` (`vehicle_type`),
  ADD KEY `index_driver_name` (`driver_name`),
  ADD KEY `index_driver_licence` (`driver_licence`),
  ADD KEY `index_driver_contact` (`driver_contact`);

--
-- Indexes for table `visitors_book`
--
ALTER TABLE `visitors_book`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_source` (`source`),
  ADD KEY `index_purpose` (`purpose`),
  ADD KEY `index_name` (`name`),
  ADD KEY `index_email` (`email`),
  ADD KEY `index_contact` (`contact`),
  ADD KEY `index_id_proof` (`id_proof`),
  ADD KEY `index_visit_to` (`visit_to`),
  ADD KEY `index_related_to` (`related_to`),
  ADD KEY `index_no_of_pepple` (`no_of_pepple`),
  ADD KEY `index_date` (`date`),
  ADD KEY `index_in_time` (`in_time`),
  ADD KEY `index_out_time` (`out_time`);

--
-- Indexes for table `visitors_purpose`
--
ALTER TABLE `visitors_purpose`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_visitors_purpose` (`visitors_purpose`);

--
-- Indexes for table `visit_details`
--
ALTER TABLE `visit_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `generated_by` (`generated_by`),
  ADD KEY `opd_details_id` (`opd_details_id`),
  ADD KEY `organisation_id` (`organisation_id`),
  ADD KEY `cons_doctor` (`cons_doctor`),
  ADD KEY `patient_charge_id` (`patient_charge_id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `index_case_type` (`case_type`),
  ADD KEY `index_appointment_date` (`appointment_date`),
  ADD KEY `index_symptoms_type` (`symptoms_type`),
  ADD KEY `index_bp` (`bp`),
  ADD KEY `index_height` (`height`),
  ADD KEY `index_weight` (`weight`),
  ADD KEY `index_pulse` (`pulse`),
  ADD KEY `index_temperature` (`temperature`),
  ADD KEY `index_respiration` (`respiration`),
  ADD KEY `index_known_allergies` (`known_allergies`),
  ADD KEY `index_patient_old` (`patient_old`),
  ADD KEY `index_casualty` (`casualty`),
  ADD KEY `index_refference` (`refference`),
  ADD KEY `index_date` (`date`),
  ADD KEY `index_payment_mode` (`payment_mode`),
  ADD KEY `index_generated_by` (`generated_by`),
  ADD KEY `index_live_consult` (`live_consult`),
  ADD KEY `index_is_antenatal` (`is_antenatal`);

--
-- Indexes for table `vitals`
--
ALTER TABLE `vitals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `unit` (`unit`),
  ADD KEY `index_name` (`name`),
  ADD KEY `index_reference_range` (`reference_range`),
  ADD KEY `index_unit` (`unit`),
  ADD KEY `index_is_system` (`is_system`);

--
-- Indexes for table `zoom_settings`
--
ALTER TABLE `zoom_settings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ambulance_call`
--
ALTER TABLE `ambulance_call`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `annual_calendar`
--
ALTER TABLE `annual_calendar`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `antenatal_examine`
--
ALTER TABLE `antenatal_examine`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `appointment_payment`
--
ALTER TABLE `appointment_payment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `appointment_queue`
--
ALTER TABLE `appointment_queue`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `appoint_priority`
--
ALTER TABLE `appoint_priority`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `bed`
--
ALTER TABLE `bed`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bed_group`
--
ALTER TABLE `bed_group`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bed_type`
--
ALTER TABLE `bed_type`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bill`
--
ALTER TABLE `bill`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `birth_report`
--
ALTER TABLE `birth_report`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blood_bank_products`
--
ALTER TABLE `blood_bank_products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blood_donor`
--
ALTER TABLE `blood_donor`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blood_donor_cycle`
--
ALTER TABLE `blood_donor_cycle`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blood_issue`
--
ALTER TABLE `blood_issue`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `captcha`
--
ALTER TABLE `captcha`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `case_references`
--
ALTER TABLE `case_references`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `charges`
--
ALTER TABLE `charges`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `charge_categories`
--
ALTER TABLE `charge_categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `charge_type_master`
--
ALTER TABLE `charge_type_master`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `charge_type_module`
--
ALTER TABLE `charge_type_module`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `charge_units`
--
ALTER TABLE `charge_units`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_connections`
--
ALTER TABLE `chat_connections`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_users`
--
ALTER TABLE `chat_users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `complaint`
--
ALTER TABLE `complaint`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `complaint_type`
--
ALTER TABLE `complaint_type`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `conferences`
--
ALTER TABLE `conferences`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `conferences_history`
--
ALTER TABLE `conferences_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `conference_staff`
--
ALTER TABLE `conference_staff`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `consultant_register`
--
ALTER TABLE `consultant_register`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `consult_charges`
--
ALTER TABLE `consult_charges`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contents`
--
ALTER TABLE `contents`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `content_for`
--
ALTER TABLE `content_for`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `content_types`
--
ALTER TABLE `content_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `custom_fields`
--
ALTER TABLE `custom_fields`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `custom_field_values`
--
ALTER TABLE `custom_field_values`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `death_report`
--
ALTER TABLE `death_report`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `discharge_card`
--
ALTER TABLE `discharge_card`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dispatch_receive`
--
ALTER TABLE `dispatch_receive`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `doctor_absent`
--
ALTER TABLE `doctor_absent`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `doctor_global_shift`
--
ALTER TABLE `doctor_global_shift`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `doctor_shift_time`
--
ALTER TABLE `doctor_shift_time`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dose_duration`
--
ALTER TABLE `dose_duration`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dose_interval`
--
ALTER TABLE `dose_interval`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `duty_roster_assign`
--
ALTER TABLE `duty_roster_assign`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `duty_roster_list`
--
ALTER TABLE `duty_roster_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `duty_roster_shift`
--
ALTER TABLE `duty_roster_shift`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_config`
--
ALTER TABLE `email_config`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_head`
--
ALTER TABLE `expense_head`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `filetypes`
--
ALTER TABLE `filetypes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `finding`
--
ALTER TABLE `finding`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `finding_category`
--
ALTER TABLE `finding_category`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `floor`
--
ALTER TABLE `floor`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `front_cms_media_gallery`
--
ALTER TABLE `front_cms_media_gallery`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `front_cms_menus`
--
ALTER TABLE `front_cms_menus`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `front_cms_menu_items`
--
ALTER TABLE `front_cms_menu_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `front_cms_pages`
--
ALTER TABLE `front_cms_pages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `front_cms_page_contents`
--
ALTER TABLE `front_cms_page_contents`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `front_cms_programs`
--
ALTER TABLE `front_cms_programs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `front_cms_program_photos`
--
ALTER TABLE `front_cms_program_photos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `front_cms_settings`
--
ALTER TABLE `front_cms_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `gateway_ins`
--
ALTER TABLE `gateway_ins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gateway_ins_response`
--
ALTER TABLE `gateway_ins_response`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `general_calls`
--
ALTER TABLE `general_calls`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `global_shift`
--
ALTER TABLE `global_shift`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `income`
--
ALTER TABLE `income`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `income_head`
--
ALTER TABLE `income_head`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ipd_details`
--
ALTER TABLE `ipd_details`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ipd_doctors`
--
ALTER TABLE `ipd_doctors`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ipd_prescription_basic`
--
ALTER TABLE `ipd_prescription_basic`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ipd_prescription_details`
--
ALTER TABLE `ipd_prescription_details`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ipd_prescription_test`
--
ALTER TABLE `ipd_prescription_test`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item`
--
ALTER TABLE `item`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_category`
--
ALTER TABLE `item_category`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_issue`
--
ALTER TABLE `item_issue`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_stock`
--
ALTER TABLE `item_stock`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_store`
--
ALTER TABLE `item_store`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item_supplier`
--
ALTER TABLE `item_supplier`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lab`
--
ALTER TABLE `lab`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `leave_types`
--
ALTER TABLE `leave_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medication_report`
--
ALTER TABLE `medication_report`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medicine_bad_stock`
--
ALTER TABLE `medicine_bad_stock`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medicine_batch_details`
--
ALTER TABLE `medicine_batch_details`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medicine_category`
--
ALTER TABLE `medicine_category`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medicine_dosage`
--
ALTER TABLE `medicine_dosage`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medicine_group`
--
ALTER TABLE `medicine_group`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medicine_supplier`
--
ALTER TABLE `medicine_supplier`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_roles`
--
ALTER TABLE `notification_roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification_setting`
--
ALTER TABLE `notification_setting`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `nurse_note`
--
ALTER TABLE `nurse_note`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nurse_notes_comment`
--
ALTER TABLE `nurse_notes_comment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `obstetric_history`
--
ALTER TABLE `obstetric_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `opd_details`
--
ALTER TABLE `opd_details`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `operation`
--
ALTER TABLE `operation`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `operation_category`
--
ALTER TABLE `operation_category`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `operation_theatre`
--
ALTER TABLE `operation_theatre`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `organisation`
--
ALTER TABLE `organisation`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `organisations_charges`
--
ALTER TABLE `organisations_charges`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pathology`
--
ALTER TABLE `pathology`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pathology_billing`
--
ALTER TABLE `pathology_billing`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pathology_category`
--
ALTER TABLE `pathology_category`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pathology_parameter`
--
ALTER TABLE `pathology_parameter`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pathology_parameterdetails`
--
ALTER TABLE `pathology_parameterdetails`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pathology_report`
--
ALTER TABLE `pathology_report`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pathology_report_parameterdetails`
--
ALTER TABLE `pathology_report_parameterdetails`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patients_vitals`
--
ALTER TABLE `patients_vitals`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_bed_history`
--
ALTER TABLE `patient_bed_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_charges`
--
ALTER TABLE `patient_charges`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_id_card`
--
ALTER TABLE `patient_id_card`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `patient_timeline`
--
ALTER TABLE `patient_timeline`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_settings`
--
ALTER TABLE `payment_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payslip_allowance`
--
ALTER TABLE `payslip_allowance`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permission_category`
--
ALTER TABLE `permission_category`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=420;

--
-- AUTO_INCREMENT for table `permission_group`
--
ALTER TABLE `permission_group`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `permission_patient`
--
ALTER TABLE `permission_patient`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `pharmacy`
--
ALTER TABLE `pharmacy`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pharmacy_bill_basic`
--
ALTER TABLE `pharmacy_bill_basic`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pharmacy_bill_detail`
--
ALTER TABLE `pharmacy_bill_detail`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pharmacy_company`
--
ALTER TABLE `pharmacy_company`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `postnatal_examine`
--
ALTER TABLE `postnatal_examine`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prefixes`
--
ALTER TABLE `prefixes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `primary_examine`
--
ALTER TABLE `primary_examine`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `print_setting`
--
ALTER TABLE `print_setting`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `radio`
--
ALTER TABLE `radio`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `radiology_billing`
--
ALTER TABLE `radiology_billing`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `radiology_parameter`
--
ALTER TABLE `radiology_parameter`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `radiology_parameterdetails`
--
ALTER TABLE `radiology_parameterdetails`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `radiology_report`
--
ALTER TABLE `radiology_report`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `radiology_report_parameterdetails`
--
ALTER TABLE `radiology_report_parameterdetails`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `read_notification`
--
ALTER TABLE `read_notification`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `read_systemnotification`
--
ALTER TABLE `read_systemnotification`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `referral_category`
--
ALTER TABLE `referral_category`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `referral_commission`
--
ALTER TABLE `referral_commission`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `referral_payment`
--
ALTER TABLE `referral_payment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `referral_person`
--
ALTER TABLE `referral_person`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `referral_person_commission`
--
ALTER TABLE `referral_person_commission`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `referral_type`
--
ALTER TABLE `referral_type`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `roles_permissions`
--
ALTER TABLE `roles_permissions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2332;

--
-- AUTO_INCREMENT for table `sch_settings`
--
ALTER TABLE `sch_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `send_notification`
--
ALTER TABLE `send_notification`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `share_contents`
--
ALTER TABLE `share_contents`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `share_content_for`
--
ALTER TABLE `share_content_for`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `share_upload_contents`
--
ALTER TABLE `share_upload_contents`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shift_details`
--
ALTER TABLE `shift_details`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sms_config`
--
ALTER TABLE `sms_config`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `source`
--
ALTER TABLE `source`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `specialist`
--
ALTER TABLE `specialist`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_attendance`
--
ALTER TABLE `staff_attendance`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_attendance_type`
--
ALTER TABLE `staff_attendance_type`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `staff_attendence_schedules`
--
ALTER TABLE `staff_attendence_schedules`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_designation`
--
ALTER TABLE `staff_designation`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_id_card`
--
ALTER TABLE `staff_id_card`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `staff_leave_details`
--
ALTER TABLE `staff_leave_details`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_leave_request`
--
ALTER TABLE `staff_leave_request`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_payroll`
--
ALTER TABLE `staff_payroll`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_payslip`
--
ALTER TABLE `staff_payslip`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_roles`
--
ALTER TABLE `staff_roles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_timeline`
--
ALTER TABLE `staff_timeline`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplier_bill_basic`
--
ALTER TABLE `supplier_bill_basic`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `symptoms`
--
ALTER TABLE `symptoms`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `symptoms_classification`
--
ALTER TABLE `symptoms_classification`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_notification`
--
ALTER TABLE `system_notification`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_notification_setting`
--
ALTER TABLE `system_notification_setting`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `tax_category`
--
ALTER TABLE `tax_category`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions_processing`
--
ALTER TABLE `transactions_processing`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `unit`
--
ALTER TABLE `unit`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `upload_contents`
--
ALTER TABLE `upload_contents`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `userlog`
--
ALTER TABLE `userlog`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_authentication`
--
ALTER TABLE `users_authentication`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `visitors_book`
--
ALTER TABLE `visitors_book`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `visitors_purpose`
--
ALTER TABLE `visitors_purpose`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `visit_details`
--
ALTER TABLE `visit_details`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vitals`
--
ALTER TABLE `vitals`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `zoom_settings`
--
ALTER TABLE `zoom_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ambulance_call`
--
ALTER TABLE `ambulance_call`
  ADD CONSTRAINT `ambulance_call_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ambulance_call_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ambulance_call_ibfk_3` FOREIGN KEY (`generated_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ambulance_call_ibfk_4` FOREIGN KEY (`case_reference_id`) REFERENCES `case_references` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ambulance_call_ibfk_5` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `ambulance_call_ibfk_6` FOREIGN KEY (`charge_id`) REFERENCES `charges` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `annual_calendar`
--
ALTER TABLE `annual_calendar`
  ADD CONSTRAINT `annual_calendar_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `antenatal_examine`
--
ALTER TABLE `antenatal_examine`
  ADD CONSTRAINT `antenatal_examine_ibfk_1` FOREIGN KEY (`visit_details_id`) REFERENCES `visit_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `antenatal_examine_ibfk_2` FOREIGN KEY (`ipdid`) REFERENCES `ipd_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `antenatal_examine_ibfk_3` FOREIGN KEY (`primary_examine_id`) REFERENCES `primary_examine` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `appointment_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointment_ibfk_2` FOREIGN KEY (`doctor`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointment_ibfk_3` FOREIGN KEY (`case_reference_id`) REFERENCES `case_references` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointment_ibfk_4` FOREIGN KEY (`visit_details_id`) REFERENCES `visit_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointment_ibfk_5` FOREIGN KEY (`doctor_shift_time_id`) REFERENCES `doctor_shift_time` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `appointment_payment`
--
ALTER TABLE `appointment_payment`
  ADD CONSTRAINT `appointment_payment_ibfk_2` FOREIGN KEY (`charge_id`) REFERENCES `charges` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointment_payment_ibfk_3` FOREIGN KEY (`appointment_id`) REFERENCES `appointment` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `appointment_queue`
--
ALTER TABLE `appointment_queue`
  ADD CONSTRAINT `appointment_queue_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointment` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointment_queue_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointment_queue_ibfk_3` FOREIGN KEY (`shift_id`) REFERENCES `doctor_shift_time` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bed`
--
ALTER TABLE `bed`
  ADD CONSTRAINT `bed_ibfk_1` FOREIGN KEY (`bed_type_id`) REFERENCES `bed_type` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bed_ibfk_2` FOREIGN KEY (`bed_group_id`) REFERENCES `bed_group` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bed_ibfk_3` FOREIGN KEY (`bed_group_id`) REFERENCES `bed_group` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bill`
--
ALTER TABLE `bill`
  ADD CONSTRAINT `bill_ibfk_1` FOREIGN KEY (`case_id`) REFERENCES `case_references` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `birth_report`
--
ALTER TABLE `birth_report`
  ADD CONSTRAINT `birth_report_ibfk_1` FOREIGN KEY (`case_reference_id`) REFERENCES `case_references` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `birth_report_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `blood_donor`
--
ALTER TABLE `blood_donor`
  ADD CONSTRAINT `blood_donor_ibfk_1` FOREIGN KEY (`blood_bank_product_id`) REFERENCES `blood_bank_products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `blood_donor_cycle`
--
ALTER TABLE `blood_donor_cycle`
  ADD CONSTRAINT `blood_donor_cycle_ibfk_1` FOREIGN KEY (`blood_bank_product_id`) REFERENCES `blood_bank_products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `blood_donor_cycle_ibfk_2` FOREIGN KEY (`blood_donor_id`) REFERENCES `blood_donor` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `blood_donor_cycle_ibfk_3` FOREIGN KEY (`charge_id`) REFERENCES `charges` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `blood_issue`
--
ALTER TABLE `blood_issue`
  ADD CONSTRAINT `blood_issue_ibfk_1` FOREIGN KEY (`case_reference_id`) REFERENCES `case_references` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `blood_issue_ibfk_2` FOREIGN KEY (`blood_donor_cycle_id`) REFERENCES `blood_donor_cycle` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `blood_issue_ibfk_3` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `blood_issue_ibfk_4` FOREIGN KEY (`charge_id`) REFERENCES `charges` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `blood_issue_ibfk_5` FOREIGN KEY (`generated_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `blood_issue_ibfk_6` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `blood_issue_ibfk_7` FOREIGN KEY (`hospital_doctor`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `case_references`
--
ALTER TABLE `case_references`
  ADD CONSTRAINT `case_references_ibfk_1` FOREIGN KEY (`bill_id`) REFERENCES `bill` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `charges`
--
ALTER TABLE `charges`
  ADD CONSTRAINT `charges_ibfk_1` FOREIGN KEY (`charge_category_id`) REFERENCES `charge_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `charges_ibfk_2` FOREIGN KEY (`tax_category_id`) REFERENCES `tax_category` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `charges_ibfk_3` FOREIGN KEY (`charge_unit_id`) REFERENCES `charge_units` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `charge_categories`
--
ALTER TABLE `charge_categories`
  ADD CONSTRAINT `charge_categories_ibfk_1` FOREIGN KEY (`charge_type_id`) REFERENCES `charge_type_master` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `charge_type_module`
--
ALTER TABLE `charge_type_module`
  ADD CONSTRAINT `charge_type_module_ibfk_1` FOREIGN KEY (`charge_type_master_id`) REFERENCES `charge_type_master` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_connections`
--
ALTER TABLE `chat_connections`
  ADD CONSTRAINT `chat_connections_ibfk_1` FOREIGN KEY (`chat_user_one`) REFERENCES `chat_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_connections_ibfk_2` FOREIGN KEY (`chat_user_two`) REFERENCES `chat_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`chat_user_id`) REFERENCES `chat_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_messages_ibfk_2` FOREIGN KEY (`chat_connection_id`) REFERENCES `chat_connections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_users`
--
ALTER TABLE `chat_users`
  ADD CONSTRAINT `chat_users_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_users_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_users_ibfk_3` FOREIGN KEY (`create_staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_users_ibfk_4` FOREIGN KEY (`create_patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `complaint`
--
ALTER TABLE `complaint`
  ADD CONSTRAINT `complaint_ibfk_1` FOREIGN KEY (`complaint_type_id`) REFERENCES `complaint_type` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `conferences`
--
ALTER TABLE `conferences`
  ADD CONSTRAINT `conferences_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conferences_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conferences_ibfk_3` FOREIGN KEY (`visit_details_id`) REFERENCES `visit_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conferences_ibfk_4` FOREIGN KEY (`ipd_id`) REFERENCES `ipd_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conferences_ibfk_5` FOREIGN KEY (`created_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `conferences_history`
--
ALTER TABLE `conferences_history`
  ADD CONSTRAINT `conferences_history_ibfk_1` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conferences_history_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conferences_history_ibfk_3` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `conference_staff`
--
ALTER TABLE `conference_staff`
  ADD CONSTRAINT `conference_staff_ibfk_1` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conference_staff_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `consultant_register`
--
ALTER TABLE `consultant_register`
  ADD CONSTRAINT `consultant_register_ibfk_1` FOREIGN KEY (`ipd_id`) REFERENCES `ipd_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `consultant_register_ibfk_2` FOREIGN KEY (`cons_doctor`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `consult_charges`
--
ALTER TABLE `consult_charges`
  ADD CONSTRAINT `consult_charges_ibfk_1` FOREIGN KEY (`doctor`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `content_for`
--
ALTER TABLE `content_for`
  ADD CONSTRAINT `content_for_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `contents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `content_for_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `custom_field_values`
--
ALTER TABLE `custom_field_values`
  ADD CONSTRAINT `custom_field_values_ibfk_1` FOREIGN KEY (`custom_field_id`) REFERENCES `custom_fields` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `death_report`
--
ALTER TABLE `death_report`
  ADD CONSTRAINT `death_report_ibfk_1` FOREIGN KEY (`case_reference_id`) REFERENCES `case_references` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `death_report_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `discharge_card`
--
ALTER TABLE `discharge_card`
  ADD CONSTRAINT `discharge_card_ibfk_1` FOREIGN KEY (`ipd_details_id`) REFERENCES `ipd_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discharge_card_ibfk_2` FOREIGN KEY (`discharge_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discharge_card_ibfk_3` FOREIGN KEY (`case_reference_id`) REFERENCES `case_references` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discharge_card_ibfk_4` FOREIGN KEY (`opd_details_id`) REFERENCES `opd_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discharge_card_ibfk_5` FOREIGN KEY (`ipd_details_id`) REFERENCES `ipd_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `discharge_card_ibfk_6` FOREIGN KEY (`discharge_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `doctor_absent`
--
ALTER TABLE `doctor_absent`
  ADD CONSTRAINT `doctor_absent_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `doctor_global_shift`
--
ALTER TABLE `doctor_global_shift`
  ADD CONSTRAINT `doctor_global_shift_ibfk_1` FOREIGN KEY (`global_shift_id`) REFERENCES `global_shift` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `doctor_global_shift_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `doctor_shift_time`
--
ALTER TABLE `doctor_shift_time`
  ADD CONSTRAINT `doctor_shift_time_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `doctor_shift_time_ibfk_3` FOREIGN KEY (`doctor_global_shift_id`) REFERENCES `doctor_global_shift` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `duty_roster_assign`
--
ALTER TABLE `duty_roster_assign`
  ADD CONSTRAINT `duty_roster_assign_ibfk_1` FOREIGN KEY (`duty_roster_list_id`) REFERENCES `duty_roster_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `duty_roster_assign_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `duty_roster_assign_ibfk_3` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `duty_roster_assign_ibfk_4` FOREIGN KEY (`floor_id`) REFERENCES `floor` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `duty_roster_list`
--
ALTER TABLE `duty_roster_list`
  ADD CONSTRAINT `duty_roster_list_ibfk_1` FOREIGN KEY (`duty_roster_shift_id`) REFERENCES `duty_roster_shift` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`exp_head_id`) REFERENCES `expense_head` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `expenses_ibfk_2` FOREIGN KEY (`generated_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `finding`
--
ALTER TABLE `finding`
  ADD CONSTRAINT `finding_ibfk_1` FOREIGN KEY (`finding_category_id`) REFERENCES `finding_category` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `front_cms_page_contents`
--
ALTER TABLE `front_cms_page_contents`
  ADD CONSTRAINT `front_cms_page_contents_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `front_cms_pages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `front_cms_program_photos`
--
ALTER TABLE `front_cms_program_photos`
  ADD CONSTRAINT `front_cms_program_photos_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `front_cms_programs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gateway_ins`
--
ALTER TABLE `gateway_ins`
  ADD CONSTRAINT `gateway_ins_ibfk_1` FOREIGN KEY (`online_appointment_id`) REFERENCES `appointment` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gateway_ins_response`
--
ALTER TABLE `gateway_ins_response`
  ADD CONSTRAINT `gateway_ins_response_ibfk_1` FOREIGN KEY (`gateway_ins_id`) REFERENCES `gateway_ins` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `income`
--
ALTER TABLE `income`
  ADD CONSTRAINT `income_ibfk_1` FOREIGN KEY (`inc_head_id`) REFERENCES `income_head` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `income_ibfk_2` FOREIGN KEY (`generated_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ipd_details`
--
ALTER TABLE `ipd_details`
  ADD CONSTRAINT `ipd_details_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_details_ibfk_2` FOREIGN KEY (`case_reference_id`) REFERENCES `case_references` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_details_ibfk_3` FOREIGN KEY (`case_reference_id`) REFERENCES `case_references` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_details_ibfk_4` FOREIGN KEY (`cons_doctor`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_details_ibfk_5` FOREIGN KEY (`bed_group_id`) REFERENCES `bed_group` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_details_ibfk_6` FOREIGN KEY (`bed`) REFERENCES `bed` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ipd_doctors`
--
ALTER TABLE `ipd_doctors`
  ADD CONSTRAINT `ipd_doctors_ibfk_1` FOREIGN KEY (`ipd_id`) REFERENCES `ipd_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_doctors_ibfk_2` FOREIGN KEY (`consult_doctor`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ipd_prescription_basic`
--
ALTER TABLE `ipd_prescription_basic`
  ADD CONSTRAINT `ipd_prescription_basic_ibfk_1` FOREIGN KEY (`ipd_id`) REFERENCES `ipd_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_prescription_basic_ibfk_2` FOREIGN KEY (`visit_details_id`) REFERENCES `visit_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_prescription_basic_ibfk_3` FOREIGN KEY (`generated_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_prescription_basic_ibfk_4` FOREIGN KEY (`prescribe_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ipd_prescription_details`
--
ALTER TABLE `ipd_prescription_details`
  ADD CONSTRAINT `ipd_prescription_details_ibfk_1` FOREIGN KEY (`basic_id`) REFERENCES `ipd_prescription_basic` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_prescription_details_ibfk_2` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacy` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_prescription_details_ibfk_3` FOREIGN KEY (`dose_interval_id`) REFERENCES `dose_interval` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_prescription_details_ibfk_4` FOREIGN KEY (`dose_duration_id`) REFERENCES `dose_interval` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ipd_prescription_test`
--
ALTER TABLE `ipd_prescription_test`
  ADD CONSTRAINT `ipd_prescription_test_ibfk_1` FOREIGN KEY (`ipd_prescription_basic_id`) REFERENCES `ipd_prescription_basic` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_prescription_test_ibfk_2` FOREIGN KEY (`pathology_id`) REFERENCES `pathology` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ipd_prescription_test_ibfk_3` FOREIGN KEY (`radiology_id`) REFERENCES `radio` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `item_ibfk_1` FOREIGN KEY (`item_category_id`) REFERENCES `item_category` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `item_issue`
--
ALTER TABLE `item_issue`
  ADD CONSTRAINT `item_issue_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_issue_ibfk_2` FOREIGN KEY (`item_category_id`) REFERENCES `item_category` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_issue_ibfk_3` FOREIGN KEY (`issue_to`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `item_stock`
--
ALTER TABLE `item_stock`
  ADD CONSTRAINT `item_stock_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `item` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_stock_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `item_supplier` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_stock_ibfk_3` FOREIGN KEY (`store_id`) REFERENCES `item_store` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `medication_report`
--
ALTER TABLE `medication_report`
  ADD CONSTRAINT `medication_report_ibfk_1` FOREIGN KEY (`generated_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medication_report_ibfk_2` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacy` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medication_report_ibfk_3` FOREIGN KEY (`ipd_id`) REFERENCES `ipd_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medication_report_ibfk_4` FOREIGN KEY (`medicine_dosage_id`) REFERENCES `medicine_dosage` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medication_report_ibfk_5` FOREIGN KEY (`opd_details_id`) REFERENCES `opd_details` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `medicine_bad_stock`
--
ALTER TABLE `medicine_bad_stock`
  ADD CONSTRAINT `medicine_bad_stock_ibfk_1` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacy` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medicine_bad_stock_ibfk_2` FOREIGN KEY (`medicine_batch_details_id`) REFERENCES `medicine_batch_details` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `medicine_batch_details`
--
ALTER TABLE `medicine_batch_details`
  ADD CONSTRAINT `medicine_batch_details_ibfk_1` FOREIGN KEY (`supplier_bill_basic_id`) REFERENCES `supplier_bill_basic` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medicine_batch_details_ibfk_2` FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacy` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `medicine_dosage`
--
ALTER TABLE `medicine_dosage`
  ADD CONSTRAINT `medicine_dosage_ibfk_1` FOREIGN KEY (`medicine_category_id`) REFERENCES `medicine_category` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `medicine_dosage_ibfk_2` FOREIGN KEY (`units_id`) REFERENCES `unit` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notification_roles`
--
ALTER TABLE `notification_roles`
  ADD CONSTRAINT `notification_roles_ibfk_1` FOREIGN KEY (`send_notification_id`) REFERENCES `send_notification` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notification_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `nurse_note`
--
ALTER TABLE `nurse_note`
  ADD CONSTRAINT `nurse_note_ibfk_1` FOREIGN KEY (`ipd_id`) REFERENCES `ipd_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `nurse_note_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `nurse_notes_comment`
--
ALTER TABLE `nurse_notes_comment`
  ADD CONSTRAINT `nurse_notes_comment_ibfk_1` FOREIGN KEY (`nurse_note_id`) REFERENCES `nurse_note` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `nurse_notes_comment_ibfk_2` FOREIGN KEY (`comment_staffid`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `opd_details`
--
ALTER TABLE `opd_details`
  ADD CONSTRAINT `opd_details_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `opd_details_ibfk_2` FOREIGN KEY (`case_reference_id`) REFERENCES `case_references` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `opd_details_ibfk_3` FOREIGN KEY (`generated_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `operation`
--
ALTER TABLE `operation`
  ADD CONSTRAINT `operation_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `operation_category` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `operation_theatre`
--
ALTER TABLE `operation_theatre`
  ADD CONSTRAINT `operation_theatre_ibfk_1` FOREIGN KEY (`opd_details_id`) REFERENCES `opd_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `operation_theatre_ibfk_2` FOREIGN KEY (`ipd_details_id`) REFERENCES `ipd_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `operation_theatre_ibfk_3` FOREIGN KEY (`consultant_doctor`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `operation_theatre_ibfk_4` FOREIGN KEY (`generated_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `operation_theatre_ibfk_5` FOREIGN KEY (`operation_id`) REFERENCES `operation` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `organisations_charges`
--
ALTER TABLE `organisations_charges`
  ADD CONSTRAINT `organisations_charges_ibfk_1` FOREIGN KEY (`org_id`) REFERENCES `organisation` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `organisations_charges_ibfk_2` FOREIGN KEY (`charge_id`) REFERENCES `charges` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pathology`
--
ALTER TABLE `pathology`
  ADD CONSTRAINT `pathology_ibfk_1` FOREIGN KEY (`pathology_category_id`) REFERENCES `pathology_category` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pathology_ibfk_2` FOREIGN KEY (`charge_id`) REFERENCES `charges` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pathology_billing`
--
ALTER TABLE `pathology_billing`
  ADD CONSTRAINT `pathology_billing_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pathology_billing_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pathology_billing_ibfk_3` FOREIGN KEY (`case_reference_id`) REFERENCES `case_references` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pathology_billing_ibfk_4` FOREIGN KEY (`generated_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pathology_billing_ibfk_5` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pathology_billing_ibfk_6` FOREIGN KEY (`ipd_prescription_basic_id`) REFERENCES `ipd_prescription_basic` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pathology_billing_ibfk_7` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pathology_parameter`
--
ALTER TABLE `pathology_parameter`
  ADD CONSTRAINT `pathology_parameter_ibfk_1` FOREIGN KEY (`unit`) REFERENCES `unit` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pathology_parameterdetails`
--
ALTER TABLE `pathology_parameterdetails`
  ADD CONSTRAINT `pathology_parameterdetails_ibfk_1` FOREIGN KEY (`pathology_id`) REFERENCES `pathology` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pathology_parameterdetails_ibfk_2` FOREIGN KEY (`pathology_parameter_id`) REFERENCES `pathology_parameter` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pathology_report`
--
ALTER TABLE `pathology_report`
  ADD CONSTRAINT `pathology_report_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pathology_report_ibfk_2` FOREIGN KEY (`pathology_bill_id`) REFERENCES `pathology_billing` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pathology_report_ibfk_3` FOREIGN KEY (`pathology_id`) REFERENCES `pathology` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pathology_report_ibfk_4` FOREIGN KEY (`collection_specialist`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pathology_report_ibfk_5` FOREIGN KEY (`approved_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pathology_report_parameterdetails`
--
ALTER TABLE `pathology_report_parameterdetails`
  ADD CONSTRAINT `pathology_report_parameterdetails_ibfk_1` FOREIGN KEY (`pathology_report_id`) REFERENCES `pathology_report` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pathology_report_parameterdetails_ibfk_2` FOREIGN KEY (`pathology_parameterdetail_id`) REFERENCES `pathology_parameterdetails` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `patients_ibfk_1` FOREIGN KEY (`blood_bank_product_id`) REFERENCES `blood_bank_products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patients_ibfk_2` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patients_vitals`
--
ALTER TABLE `patients_vitals`
  ADD CONSTRAINT `patients_vitals_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patients_vitals_ibfk_2` FOREIGN KEY (`vital_id`) REFERENCES `vitals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patients_vitals_ibfk_3` FOREIGN KEY (`vital_id`) REFERENCES `vitals` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patient_bed_history`
--
ALTER TABLE `patient_bed_history`
  ADD CONSTRAINT `patient_bed_history_ibfk_1` FOREIGN KEY (`case_reference_id`) REFERENCES `case_references` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_bed_history_ibfk_2` FOREIGN KEY (`bed_group_id`) REFERENCES `bed_group` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_bed_history_ibfk_3` FOREIGN KEY (`bed_id`) REFERENCES `bed` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patient_charges`
--
ALTER TABLE `patient_charges`
  ADD CONSTRAINT `patient_charges_ibfk_1` FOREIGN KEY (`opd_id`) REFERENCES `opd_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_charges_ibfk_2` FOREIGN KEY (`ipd_id`) REFERENCES `ipd_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_charges_ibfk_3` FOREIGN KEY (`charge_id`) REFERENCES `charges` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_charges_ibfk_4` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patient_timeline`
--
ALTER TABLE `patient_timeline`
  ADD CONSTRAINT `patient_timeline_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_timeline_ibfk_2` FOREIGN KEY (`generated_users_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payslip_allowance`
--
ALTER TABLE `payslip_allowance`
  ADD CONSTRAINT `payslip_allowance_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payslip_allowance_ibfk_2` FOREIGN KEY (`staff_payslip_id`) REFERENCES `staff_payslip` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pharmacy`
--
ALTER TABLE `pharmacy`
  ADD CONSTRAINT `pharmacy_ibfk_1` FOREIGN KEY (`medicine_category_id`) REFERENCES `medicine_category` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pharmacy_bill_basic`
--
ALTER TABLE `pharmacy_bill_basic`
  ADD CONSTRAINT `pharmacy_bill_basic_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pharmacy_bill_basic_ibfk_2` FOREIGN KEY (`case_reference_id`) REFERENCES `case_references` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pharmacy_bill_basic_ibfk_3` FOREIGN KEY (`generated_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pharmacy_bill_basic_ibfk_4` FOREIGN KEY (`ipd_prescription_basic_id`) REFERENCES `ipd_prescription_basic` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pharmacy_bill_detail`
--
ALTER TABLE `pharmacy_bill_detail`
  ADD CONSTRAINT `pharmacy_bill_detail_ibfk_1` FOREIGN KEY (`pharmacy_bill_basic_id`) REFERENCES `pharmacy_bill_basic` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pharmacy_bill_detail_ibfk_2` FOREIGN KEY (`medicine_batch_detail_id`) REFERENCES `medicine_batch_details` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `postnatal_examine`
--
ALTER TABLE `postnatal_examine`
  ADD CONSTRAINT `postnatal_examine_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `primary_examine`
--
ALTER TABLE `primary_examine`
  ADD CONSTRAINT `primary_examine_ibfk_1` FOREIGN KEY (`visit_details_id`) REFERENCES `visit_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `primary_examine_ibfk_2` FOREIGN KEY (`ipdid`) REFERENCES `ipd_details` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `radio`
--
ALTER TABLE `radio`
  ADD CONSTRAINT `radio_ibfk_1` FOREIGN KEY (`charge_id`) REFERENCES `charges` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `radiology_billing`
--
ALTER TABLE `radiology_billing`
  ADD CONSTRAINT `radiology_billing_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `radiology_billing_ibfk_2` FOREIGN KEY (`case_reference_id`) REFERENCES `case_references` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `radiology_billing_ibfk_3` FOREIGN KEY (`doctor_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `radiology_billing_ibfk_4` FOREIGN KEY (`generated_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `radiology_billing_ibfk_5` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `radiology_billing_ibfk_6` FOREIGN KEY (`ipd_prescription_basic_id`) REFERENCES `ipd_prescription_basic` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `radiology_billing_ibfk_7` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `radiology_parameterdetails`
--
ALTER TABLE `radiology_parameterdetails`
  ADD CONSTRAINT `radiology_parameterdetails_ibfk_1` FOREIGN KEY (`radiology_id`) REFERENCES `radio` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `radiology_parameterdetails_ibfk_2` FOREIGN KEY (`radiology_parameter_id`) REFERENCES `radiology_parameter` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `radiology_report`
--
ALTER TABLE `radiology_report`
  ADD CONSTRAINT `radiology_report_ibfk_1` FOREIGN KEY (`radiology_id`) REFERENCES `radio` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `radiology_report_ibfk_2` FOREIGN KEY (`radiology_bill_id`) REFERENCES `radiology_billing` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `radiology_report_ibfk_3` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `radiology_report_ibfk_4` FOREIGN KEY (`generated_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `radiology_report_ibfk_5` FOREIGN KEY (`collection_specialist`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `radiology_report_ibfk_6` FOREIGN KEY (`approved_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `radiology_report_parameterdetails`
--
ALTER TABLE `radiology_report_parameterdetails`
  ADD CONSTRAINT `radiology_report_parameterdetails_ibfk_1` FOREIGN KEY (`radiology_report_id`) REFERENCES `radiology_report` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `radiology_report_parameterdetails_ibfk_2` FOREIGN KEY (`radiology_parameterdetail_id`) REFERENCES `radiology_parameterdetails` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `read_notification`
--
ALTER TABLE `read_notification`
  ADD CONSTRAINT `read_notification_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `read_systemnotification`
--
ALTER TABLE `read_systemnotification`
  ADD CONSTRAINT `read_systemnotification_ibfk_1` FOREIGN KEY (`notification_id`) REFERENCES `system_notification` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `referral_commission`
--
ALTER TABLE `referral_commission`
  ADD CONSTRAINT `referral_commission_ibfk_1` FOREIGN KEY (`referral_category_id`) REFERENCES `referral_category` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `referral_commission_ibfk_2` FOREIGN KEY (`referral_type_id`) REFERENCES `referral_type` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `referral_payment`
--
ALTER TABLE `referral_payment`
  ADD CONSTRAINT `referral_payment_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `referral_payment_ibfk_2` FOREIGN KEY (`referral_person_id`) REFERENCES `referral_person` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `referral_payment_ibfk_3` FOREIGN KEY (`referral_type`) REFERENCES `referral_type` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `referral_person`
--
ALTER TABLE `referral_person`
  ADD CONSTRAINT `referral_person_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `referral_category` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `referral_person_commission`
--
ALTER TABLE `referral_person_commission`
  ADD CONSTRAINT `referral_person_commission_ibfk_1` FOREIGN KEY (`referral_person_id`) REFERENCES `referral_person` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `referral_person_commission_ibfk_2` FOREIGN KEY (`referral_type_id`) REFERENCES `referral_type` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `roles_permissions`
--
ALTER TABLE `roles_permissions`
  ADD CONSTRAINT `roles_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `roles_permissions_ibfk_2` FOREIGN KEY (`perm_cat_id`) REFERENCES `permission_category` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `send_notification`
--
ALTER TABLE `send_notification`
  ADD CONSTRAINT `send_notification_ibfk_1` FOREIGN KEY (`created_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `share_contents`
--
ALTER TABLE `share_contents`
  ADD CONSTRAINT `share_contents_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `share_content_for`
--
ALTER TABLE `share_content_for`
  ADD CONSTRAINT `share_content_for_ibfk_1` FOREIGN KEY (`share_content_id`) REFERENCES `share_contents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `share_content_for_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patient_id` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `share_content_for_ibfk_3` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `share_upload_contents`
--
ALTER TABLE `share_upload_contents`
  ADD CONSTRAINT `share_upload_contents_ibfk_1` FOREIGN KEY (`upload_content_id`) REFERENCES `upload_contents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `share_upload_contents_ibfk_2` FOREIGN KEY (`share_content_id`) REFERENCES `share_contents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shift_details`
--
ALTER TABLE `shift_details`
  ADD CONSTRAINT `shift_details_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shift_details_ibfk_2` FOREIGN KEY (`charge_id`) REFERENCES `charges` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`staff_designation_id`) REFERENCES `staff_designation` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_attendance`
--
ALTER TABLE `staff_attendance`
  ADD CONSTRAINT `staff_attendance_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_attendance_ibfk_2` FOREIGN KEY (`staff_attendance_type_id`) REFERENCES `staff_attendance_type` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_attendence_schedules`
--
ALTER TABLE `staff_attendence_schedules`
  ADD CONSTRAINT `staff_attendence_schedules_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_attendence_schedules_ibfk_2` FOREIGN KEY (`staff_attendence_type_id`) REFERENCES `staff_attendance_type` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_leave_details`
--
ALTER TABLE `staff_leave_details`
  ADD CONSTRAINT `staff_leave_details_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_leave_details_ibfk_2` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_leave_request`
--
ALTER TABLE `staff_leave_request`
  ADD CONSTRAINT `staff_leave_request_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_leave_request_ibfk_2` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_leave_request_ibfk_3` FOREIGN KEY (`applied_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_payslip`
--
ALTER TABLE `staff_payslip`
  ADD CONSTRAINT `staff_payslip_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_payslip_ibfk_2` FOREIGN KEY (`generated_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_roles`
--
ALTER TABLE `staff_roles`
  ADD CONSTRAINT `staff_roles_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_roles_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_timeline`
--
ALTER TABLE `staff_timeline`
  ADD CONSTRAINT `staff_timeline_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `supplier_bill_basic`
--
ALTER TABLE `supplier_bill_basic`
  ADD CONSTRAINT `supplier_bill_basic_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `medicine_supplier` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `supplier_bill_basic_ibfk_2` FOREIGN KEY (`received_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_10` FOREIGN KEY (`ambulance_call_id`) REFERENCES `ambulance_call` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_11` FOREIGN KEY (`appointment_id`) REFERENCES `appointment` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_12` FOREIGN KEY (`bill_id`) REFERENCES `bill` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`case_reference_id`) REFERENCES `case_references` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_3` FOREIGN KEY (`opd_id`) REFERENCES `opd_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_4` FOREIGN KEY (`ipd_id`) REFERENCES `ipd_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_5` FOREIGN KEY (`pharmacy_bill_basic_id`) REFERENCES `pharmacy_bill_basic` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_6` FOREIGN KEY (`pathology_billing_id`) REFERENCES `pathology_billing` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_7` FOREIGN KEY (`radiology_billing_id`) REFERENCES `radiology_billing` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_8` FOREIGN KEY (`blood_donor_cycle_id`) REFERENCES `blood_donor_cycle` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_9` FOREIGN KEY (`blood_issue_id`) REFERENCES `blood_issue` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions_processing`
--
ALTER TABLE `transactions_processing`
  ADD CONSTRAINT `transactions_processing_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_processing_ibfk_10` FOREIGN KEY (`ambulance_call_id`) REFERENCES `ambulance_call` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_processing_ibfk_11` FOREIGN KEY (`appointment_id`) REFERENCES `appointment` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_processing_ibfk_12` FOREIGN KEY (`bill_id`) REFERENCES `bill` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_processing_ibfk_2` FOREIGN KEY (`case_reference_id`) REFERENCES `case_references` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_processing_ibfk_3` FOREIGN KEY (`opd_id`) REFERENCES `opd_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_processing_ibfk_4` FOREIGN KEY (`ipd_id`) REFERENCES `ipd_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_processing_ibfk_5` FOREIGN KEY (`pharmacy_bill_basic_id`) REFERENCES `pharmacy_bill_basic` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_processing_ibfk_6` FOREIGN KEY (`pathology_billing_id`) REFERENCES `pathology_billing` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_processing_ibfk_7` FOREIGN KEY (`radiology_billing_id`) REFERENCES `radiology_billing` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_processing_ibfk_8` FOREIGN KEY (`blood_donor_cycle_id`) REFERENCES `blood_donor_cycle` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_processing_ibfk_9` FOREIGN KEY (`blood_issue_id`) REFERENCES `blood_issue` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `upload_contents`
--
ALTER TABLE `upload_contents`
  ADD CONSTRAINT `upload_contents_ibfk_1` FOREIGN KEY (`upload_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `upload_contents_ibfk_2` FOREIGN KEY (`content_type_id`) REFERENCES `content_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `visit_details`
--
ALTER TABLE `visit_details`
  ADD CONSTRAINT `visit_details_ibfk_1` FOREIGN KEY (`generated_by`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `visit_details_ibfk_2` FOREIGN KEY (`opd_details_id`) REFERENCES `opd_details` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `visit_details_ibfk_3` FOREIGN KEY (`organisation_id`) REFERENCES `organisation` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `visit_details_ibfk_4` FOREIGN KEY (`cons_doctor`) REFERENCES `staff` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `visit_details_ibfk_5` FOREIGN KEY (`patient_charge_id`) REFERENCES `patient_charges` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `visit_details_ibfk_6` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
