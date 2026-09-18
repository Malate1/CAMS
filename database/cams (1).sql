-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 01, 2022 at 02:42 AM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cams`
--
CREATE DATABASE IF NOT EXISTS `cams` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `cams`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(8) UNSIGNED ZEROFILL NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `fname` varchar(30) NOT NULL,
  `lname` varchar(30) NOT NULL,
  `mname` varchar(30) NOT NULL,
  `birthplace` varchar(100) NOT NULL,
  `birthdate` date NOT NULL,
  `gender` enum('Male','Female','','') NOT NULL,
  `contact` varchar(15) NOT NULL,
  `address` varchar(50) NOT NULL,
  `image` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `email`, `password`, `fname`, `lname`, `mname`, `birthplace`, `birthdate`, `gender`, `contact`, `address`, `image`, `role`) VALUES
(00000001, 'admin@admin.com', 'e10adc3949ba59abbe56e057f20f883e', 'Michael', 'Malate', 'Serondo', 'Quezon City, Metro Manila', '1999-07-26', 'Male', '09104194324', 'Cantiguib, Alburquerque, Bohol', '06728620.jpg', 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `answer`
--

CREATE TABLE `answer` (
  `id` bigint(5) NOT NULL,
  `answer` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `patient_id` bigint(5) NOT NULL,
  `q_id` bigint(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `answer`
--

INSERT INTO `answer` (`id`, `answer`, `email`, `patient_id`, `q_id`) VALUES
(1, '1999', '', 1, 4),
(2, 'blue', 'malatemichael01@gmail.com', 1, 3),
(3, 'Leo', 'malatemichael05@gmail.com', 5, 5),
(4, '1999', 'malatemichael03@gmail.com', 3, 4);

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `appointment_id` int(8) UNSIGNED ZEROFILL NOT NULL,
  `app_date` date NOT NULL,
  `purpose` varchar(100) NOT NULL,
  `app_status` enum('Done','Pending','Cancelled') NOT NULL,
  `date_created` datetime NOT NULL,
  `patient_id` bigint(5) NOT NULL,
  `physician_id` bigint(5) NOT NULL,
  `clinic_id` bigint(5) NOT NULL,
  `queueNum` bigint(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`appointment_id`, `app_date`, `purpose`, `app_status`, `date_created`, `patient_id`, `physician_id`, `clinic_id`, `queueNum`) VALUES
(00000001, '2019-07-03', 'Abdominal Pain', 'Cancelled', '2019-06-28 05:26:56', 1, 1, 1, 1),
(00000002, '2019-07-03', 'Chest pain', 'Done', '2019-06-28 05:31:57', 1, 2, 2, 1),
(00000004, '2019-07-03', 'Abdominal Pain', 'Done', '2019-06-28 05:38:03', 1, 2, 2, 2),
(00000005, '2019-07-03', 'Headache', 'Cancelled', '2019-06-28 23:14:09', 1, 10, 10, 1),
(00000006, '2019-07-01', 'Chest pain', 'Pending', '2019-06-28 23:30:44', 1, 9, 9, 1),
(00000007, '2019-07-10', 'Chest pain', 'Cancelled', '2019-06-28 23:37:08', 1, 1, 1, 1),
(00000008, '2019-07-03', 'Chest pain', 'Done', '2019-06-29 00:05:14', 1, 2, 2, 3),
(00000009, '2019-07-05', 'Chest pain', 'Cancelled', '2019-06-29 00:52:26', 1, 2, 2, 1),
(00000010, '2019-07-21', 'Abdominal Pain', 'Pending', '2019-07-05 06:12:16', 1, 4, 4, 1),
(00000011, '2019-07-10', 'High Blood and Pressure', 'Pending', '2019-07-07 07:35:41', 1, 1, 1, 2),
(00000012, '2019-07-17', 'Abdominal Pain', 'Done', '2019-07-07 07:38:55', 1, 1, 1, 1),
(00000013, '2019-07-23', 'High Blood and Pressure', 'Pending', '2019-07-07 07:39:31', 1, 1, 1, 1),
(00000014, '2019-07-29', 'Dizziness', 'Done', '2019-07-07 07:48:52', 1, 1, 1, 1),
(00000015, '2019-07-10', 'Chest pain', 'Done', '2019-07-08 23:13:39', 1, 1, 1, 3),
(00000016, '2019-07-17', 'Abdominal Pain', 'Cancelled', '2019-07-09 04:45:01', 1, 10, 10, 1),
(00000017, '2019-07-17', 'High Blood and Pressure', 'Cancelled', '2019-07-09 04:47:39', 1, 10, 10, 2),
(00000018, '2019-07-17', 'High Blood and Pressure', 'Pending', '2019-07-09 05:26:22', 1, 10, 10, 3),
(00000019, '2019-07-14', 'High Blood and Pressure', 'Pending', '2019-07-09 07:06:31', 1, 4, 4, 1),
(00000020, '2019-07-17', 'Chest pain', 'Cancelled', '2019-07-09 23:50:51', 1, 2, 2, 1),
(00000021, '2019-07-17', 'Chest pain', 'Cancelled', '2019-07-09 23:55:36', 1, 2, 2, 2),
(00000022, '2019-07-18', 'Abdominal Pain', 'Pending', '2019-07-10 00:24:52', 1, 2, 2, 1),
(00000023, '2019-07-18', 'Abdominal Pain', 'Pending', '2019-07-10 00:25:04', 1, 2, 2, 2),
(00000024, '2019-07-22', 'Abdominal Pain', 'Done', '2019-07-10 00:30:01', 1, 10, 10, 1),
(00000025, '2019-07-22', 'Dizziness', 'Done', '2019-07-10 00:30:14', 1, 10, 10, 2),
(00000026, '2019-07-24', 'Dizziness', 'Pending', '2019-07-12 07:57:02', 4, 1, 1, 1),
(00000027, '2019-07-30', 'High Blood and Pressure', 'Pending', '2019-07-21 04:32:29', 1, 1, 1, 1),
(00000028, '2019-08-05', 'Abdominal Pain', 'Pending', '2019-08-02 07:51:59', 1, 1, 1, 1),
(00000029, '2019-08-19', 'Dizziness', 'Pending', '2019-08-16 14:25:26', 2, 7, 7, 1),
(00000030, '2019-09-10', 'Back Pain and Low Back', 'Pending', '2019-09-02 14:43:59', 2, 2, 2, 1),
(00000031, '2019-09-11', 'Dizziness', 'Done', '2019-09-03 09:20:57', 2, 1, 1, 1),
(00000032, '2019-09-08', 'Dizziness', 'Pending', '2019-09-03 13:16:40', 3, 4, 4, 1),
(00000033, '2019-09-08', 'Dizziness', 'Pending', '2019-09-03 13:17:22', 3, 4, 4, 2),
(00000034, '2019-09-08', 'Abdominal Pain', 'Pending', '2019-09-03 13:18:13', 1, 4, 4, 3),
(00000035, '2019-09-15', 'Rashes, Allergy, Itchiness', 'Pending', '2019-09-03 16:18:11', 1, 4, 4, 1),
(00000036, '2019-09-11', 'Back Pain and Low Back', 'Pending', '2019-09-03 16:25:34', 3, 7, 7, 1),
(00000037, '2019-09-15', 'Fever', 'Pending', '2019-09-10 16:42:07', 3, 4, 4, 2),
(00000038, '2021-04-05', 'Fever', 'Cancelled', '2021-04-02 17:24:08', 3, 1, 1, 1),
(00000039, '2021-04-05', 'Fever', 'Done', '2021-04-02 18:03:26', 3, 1, 1, 2),
(00000040, '2021-10-03', 'Back Pain and Low Back', 'Pending', '2021-09-27 14:54:30', 1, 4, 4, 1),
(00000041, '2022-08-08', 'Back Pain and Low Back', 'Done', '2022-07-31 05:37:29', 1, 1, 1, 1),
(00000042, '2022-08-08', 'Abdominal Pain', 'Done', '2022-07-31 05:59:37', 1, 1, 1, 2),
(00000043, '2022-08-08', 'Abdominal Pain', 'Pending', '2022-07-31 06:02:36', 2, 1, 1, 3),
(00000044, '2022-08-02', 'Back Pain and Low Back', 'Pending', '2022-07-31 06:05:53', 8, 1, 1, 1),
(00000045, '2022-08-02', 'Rashes, Allergy, Itchiness', 'Cancelled', '2022-07-31 06:11:33', 1, 1, 1, 2),
(00000046, '2022-08-08', 'High Blood and Pressure', 'Pending', '2022-07-31 06:27:51', 13, 1, 1, 4),
(00000047, '2022-08-10', 'Back Pain and Low Back', 'Pending', '2022-08-08 15:28:29', 1, 1, 1, 1),
(00000048, '2022-08-14', 'Back Pain and Low Back', 'Pending', '2022-08-08 15:31:02', 1, 4, 4, 1),
(00000049, '2022-08-14', 'Headache', 'Pending', '2022-08-08 15:33:46', 1, 4, 4, 2),
(00000050, '2022-08-10', 'Abdominal Pain', 'Pending', '2022-08-08 15:36:06', 1, 1, 1, 2),
(00000051, '2022-08-10', 'Cough and Colds', 'Pending', '2022-08-08 16:23:17', 1, 10, 10, 1),
(00000052, '2022-08-17', 'Back Pain and Low Back', 'Pending', '2022-08-08 16:24:14', 1, 10, 10, 1),
(00000053, '2022-08-17', 'Headache', 'Pending', '2022-08-09 10:09:26', 1, 1, 1, 1),
(00000054, '2022-08-17', 'Abdominal Pain', 'Cancelled', '2022-08-09 10:11:39', 5, 1, 1, 2),
(00000055, '2022-08-15', 'Back Pain and Low Back', 'Cancelled', '2022-08-11 15:12:03', 5, 1, 1, 1),
(00000056, '2022-08-15', 'High Blood and Pressure', 'Pending', '2022-08-13 09:05:44', 1, 1, 1, 2),
(00000057, '2022-08-15', 'Chest pain', 'Pending', '2022-08-13 09:53:36', 1, 1, 1, 3),
(00000058, '2022-08-16', 'Accidents and Trauma', 'Pending', '2022-08-13 10:34:50', 1, 1, 1, 1),
(00000059, '2022-08-15', 'Cough and Colds', 'Pending', '2022-08-13 11:34:47', 1, 10, 10, 1),
(00000060, '2022-08-15', 'Rashes, Allergy, Itchiness', 'Pending', '2022-08-13 11:35:50', 1, 1, 1, 4),
(00000061, '2022-08-22', 'Cough and Colds', 'Pending', '2022-08-13 11:58:11', 1, 1, 1, 1),
(00000062, '2022-08-15', 'High Blood and Pressure', 'Cancelled', '2022-08-13 13:16:05', 1, 1, 1, 5),
(00000063, '2022-08-15', 'High Blood and Pressure', 'Pending', '2022-08-13 13:41:23', 1, 1, 1, 6),
(00000064, '2022-08-22', 'High Blood and Pressure', 'Pending', '2022-08-15 09:05:33', 1, 10, 10, 1),
(00000065, '2022-08-22', 'Fever', 'Pending', '2022-08-15 09:05:50', 1, 10, 10, 2),
(00000066, '2022-08-22', 'High Blood and Pressure', 'Pending', '2022-08-15 09:06:42', 1, 10, 10, 3),
(00000067, '2022-08-23', 'Dizziness', 'Pending', '2022-08-15 09:15:25', 2, 10, 10, 1),
(00000068, '2022-08-23', 'Cough and Colds', 'Pending', '2022-08-15 09:18:03', 1, 10, 10, 2),
(00000069, '2022-08-22', 'Back Pain and Low Back', 'Pending', '2022-08-15 10:25:56', 5, 1, 1, 2),
(00000070, '2022-08-22', 'Back Pain and Low Back', 'Pending', '2022-08-15 10:27:13', 5, 1, 1, 3),
(00000071, '2022-08-22', 'High Blood and Pressure', 'Pending', '2022-08-15 10:27:25', 5, 1, 1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `clinic`
--

CREATE TABLE `clinic` (
  `clinic_id` bigint(5) NOT NULL,
  `name` varchar(50) NOT NULL,
  `bir` varchar(15) NOT NULL,
  `businessPermit` varchar(15) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `location` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `clinic`
--

INSERT INTO `clinic` (`clinic_id`, `name`, `bir`, `businessPermit`, `contact`, `location`) VALUES
(1, 'Malate\'s Clinic', '1231231312', '123213213213', '09104018596', 'RM. 102'),
(2, 'Banda Clinic', '1231231312', '123213213213', '09104018596', 'RM. 103'),
(3, 'Guines Clinic', '1231231312', '123213213213', '09104018595', 'RM. 104'),
(4, 'Ygot Clinic', '1231231312', '123213213213', '09104018596', 'RM. 105'),
(5, 'Cantiberos Clinic', '1231231312', '123213213213', '09104018596', 'RM. 106'),
(6, 'Hajiron Clinic', '1231231312', '123213213213', '09104018596', 'RM. 107'),
(7, 'Talatagod Clinic', '1231231312', '123213213213', '09104018596', 'RM. 108'),
(8, 'Dolorican Clinic', '1231231312', '123213213213', '09104018596', 'RM. 109'),
(9, 'Gida Clinic', '1231231312', '123213213213', '09104018596', 'RM. 201'),
(10, 'Cinco Clinic', '1231231312', '123213213213', '09104018596', 'RM. 202'),
(11, 'Leo Clinics 2', '1231231312', '123213213213', '09104018596', 'RM. 103'),
(12, 'Leo Clinic', '1231231312', '123213213213', '09104018595', 'RM. 103'),
(13, 'Amara Brielle Clinic', '12313123123', '123213123', '09104194323', 'RM. 203');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(8) UNSIGNED ZEROFILL NOT NULL,
  `usertype` varchar(100) NOT NULL,
  `userid` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `action` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `usertype`, `userid`, `date`, `action`) VALUES
(00000001, 'Patient', 1, '2022-08-18 05:57:39', 'Logged in Michael Malate'),
(00000002, 'Patient', 1, '2022-08-18 05:57:43', 'Logged out Michael Malate'),
(00000003, 'Patient', 1, '2022-08-18 06:00:01', 'Logged in Michael Malate'),
(00000004, 'Patient', 1, '2022-08-18 06:00:23', 'Logged out Michael Malate'),
(00000005, 'Physician', 1, '2022-08-18 06:01:22', 'Logged in Michael Malate'),
(00000006, 'Admin', 1, '2022-08-18 06:04:10', 'Logged in Michael Malate'),
(00000007, 'Admin', 1, '2022-08-18 06:04:32', 'Logged out Michael Malate'),
(00000008, 'Patient', 5, '2022-08-18 06:08:26', 'Logged in Amara Brielle Malate'),
(00000009, 'Patient', 5, '2022-08-18 06:08:31', 'Logged out Amara Brielle Malate'),
(00000010, 'Patient', 1, '2022-08-18 06:15:38', 'Logged in Michael Malate'),
(00000011, 'Patient', 1, '2022-08-18 06:15:46', 'Logged out Michael Malate'),
(00000012, 'Patient', 5, '2022-08-18 06:18:33', 'Logged in Amara Brielle Malate'),
(00000013, 'Patient', 5, '2022-08-18 06:18:40', 'Logged out Amara Brielle Malate'),
(00000014, 'Admin', 1, '2022-08-18 07:03:58', 'Logged in Michael Malate'),
(00000015, 'Admin', 1, '2022-08-18 07:09:30', 'Logged in Michael Malate'),
(00000016, 'Admin', 1, '2022-08-18 07:10:58', 'Profile updated by Michael Malate'),
(00000017, 'Admin', 1, '2022-08-18 07:39:29', 'Logged in Michael Malate'),
(00000018, 'Admin', 1, '2022-08-18 07:39:34', 'Logged out Michael Malate'),
(00000019, 'Patient', 3, '2022-08-18 07:40:23', 'Logged in Michael Malate'),
(00000020, 'Patient', 3, '2022-08-18 07:40:29', 'Logged out Michael Malate'),
(00000021, 'Secretary', 1, '2022-08-18 07:47:44', 'Logged in Michael  Malate'),
(00000022, 'Patient', 5, '2022-08-19 00:18:03', 'Logged in Amara Brielle Malate'),
(00000023, 'Patient', 5, '2022-08-19 00:18:27', 'Logged out Amara Brielle Malate'),
(00000024, 'Patient', 5, '2022-08-19 00:19:57', 'Logged in Amara Brielle Malate'),
(00000025, 'Admin', 1, '2022-08-19 00:21:11', 'Logged in Michael Malate'),
(00000026, 'Admin', 1, '2022-08-19 00:21:52', 'Logged out Michael Malate'),
(00000027, 'Admin', 1, '2022-08-19 00:23:42', 'Logged out Michael Malate'),
(00000028, 'Admin', 1, '2022-08-19 00:25:16', 'Logged in Michael Malate'),
(00000029, 'Patient', 5, '2022-08-19 01:35:35', 'Logged in Amara Brielle Malate'),
(00000030, 'Patient', 5, '2022-08-19 01:35:39', 'Logged out Amara Brielle Malate'),
(00000031, 'Patient', 1, '2022-08-19 01:40:05', 'Logged in Michael Malate'),
(00000032, 'Patient', 1, '2022-08-19 01:40:10', 'Logged out Michael Malate'),
(00000033, 'Patient', 3, '2022-08-19 03:12:42', 'Logged in Michael Malate'),
(00000034, 'Patient', 3, '2022-08-19 03:13:02', 'Logged out Michael Malate'),
(00000035, 'Patient', 1, '2022-08-19 03:16:23', 'Logged in Michael Malate'),
(00000036, 'Patient', 1, '2022-08-19 03:16:30', 'Logged out Michael Malate'),
(00000037, 'Patient', 1, '2022-08-19 03:17:37', 'Logged in Michael Malate'),
(00000038, 'Patient', 1, '2022-08-19 03:18:53', 'Logged out Michael Malate'),
(00000039, 'Patient', 1, '2022-08-19 03:27:55', 'Logged in Michael Malate'),
(00000040, 'Patient', 1, '2022-08-19 03:30:38', 'Logged out Michael Malate'),
(00000041, 'Patient', 1, '2022-08-19 03:33:08', 'Logged in Michael Malate'),
(00000042, 'Admin', 1, '2022-08-19 03:48:44', 'Logged in Michael Malate'),
(00000043, 'Admin', 1, '2022-08-19 05:28:26', 'Logged in Michael Malate'),
(00000044, 'Admin', 1, '2022-08-19 05:29:08', 'Logged out Michael Malate'),
(00000045, 'Patient', 1, '2022-08-19 05:32:34', 'Logged in Michael Malate'),
(00000046, 'Secretary', 1, '2022-08-19 05:35:25', 'Logged in Michael  Malate'),
(00000047, 'Secretary', 1, '2022-08-19 05:36:49', 'Profile updated by Michael  Malate'),
(00000048, 'Secretary', 1, '2022-08-19 05:37:58', 'Logged out Michael  Malate'),
(00000049, 'Admin', 1, '2022-08-19 05:51:19', 'Logged in Michael Malate'),
(00000050, 'Admin', 1, '2022-08-19 05:51:28', 'Logged out Michael Malate'),
(00000051, 'Patient', 1, '2022-08-19 06:13:33', 'Logged in Michael Malate'),
(00000052, 'Patient', 1, '2022-08-19 06:13:58', 'Password changed by Michael Malate'),
(00000053, 'Patient', 1, '2022-08-19 06:14:09', 'Logged out Michael Malate'),
(00000054, 'Patient', 1, '2022-08-19 06:14:16', 'Logged in Michael Malate'),
(00000055, 'Patient', 1, '2022-08-19 06:17:20', 'Logged out Michael Malate'),
(00000056, 'Admin', 1, '2022-08-22 00:16:56', 'Logged in Michael Malate'),
(00000057, 'Admin', 1, '2022-08-22 00:17:51', 'Patient password updated by Michael Malate'),
(00000058, 'Admin', 1, '2022-08-22 00:19:14', 'Logged out Michael Malate'),
(00000059, 'Patient', 5, '2022-08-22 00:20:18', 'Logged in Amara Brielle Malate'),
(00000060, 'Patient', 5, '2022-08-22 00:26:23', 'Logged out Amara Brielle Malate'),
(00000061, 'Secretary', 1, '2022-08-22 00:26:47', 'Logged in Michael  Malate'),
(00000062, 'Secretary', 1, '2022-08-22 00:32:47', 'Logged out Michael  Malate'),
(00000063, 'Admin', 1, '2022-08-22 00:32:58', 'Logged in Michael Malate'),
(00000064, 'Secretary', 1, '2022-08-22 01:03:56', 'Logged in Michael  Malate'),
(00000065, 'Secretary', 1, '2022-08-22 02:27:22', 'Logged in Michael  Malate'),
(00000066, 'Admin', 1, '2022-08-22 06:19:57', 'Logged in Michael Malate'),
(00000067, 'Admin', 1, '2022-08-23 01:53:16', 'Logged in Michael Malate'),
(00000068, 'Admin', 1, '2022-08-23 01:53:58', 'Patient password updated by Michael Malate'),
(00000069, 'Admin', 1, '2022-08-23 01:54:09', 'Patient password updated by Michael Malate'),
(00000070, 'Admin', 1, '2022-08-23 01:55:10', 'Logged out Michael Malate'),
(00000071, 'Physician', 1, '2022-08-23 05:26:41', 'Logged in Michael Malate'),
(00000072, 'Admin', 1, '2022-08-23 08:22:18', 'Logged in Michael Malate'),
(00000073, 'Admin', 1, '2022-08-24 00:43:20', 'Logged in Michael Malate'),
(00000074, 'Admin', 1, '2022-08-24 00:43:45', 'Profile updated by Michael Malate'),
(00000075, 'Patient', 1, '2022-08-24 05:18:11', 'Logged in Michael Malate'),
(00000076, 'Patient', 1, '2022-08-24 05:18:51', 'Password changed by Michael Malate'),
(00000077, 'Patient', 1, '2022-08-24 05:19:00', 'Logged out Michael Malate'),
(00000078, 'Patient', 1, '2022-08-24 05:19:05', 'Logged in Michael Malate'),
(00000079, 'Admin', 1, '2022-08-24 05:19:43', 'Logged in Michael Malate'),
(00000080, 'Patient', 1, '2022-08-24 05:32:27', 'Logged out Michael Malate'),
(00000081, 'Admin', 1, '2022-08-25 01:10:43', 'Logged in Michael Malate'),
(00000082, 'Admin', 1, '2022-08-25 06:06:51', 'Logged in Michael Malate'),
(00000083, 'Admin', 1, '2022-08-25 07:32:39', 'Logged in Michael Malate'),
(00000084, 'Physician', 1, '2022-08-31 02:02:32', 'Logged in Michael Malate'),
(00000085, 'Physician', 1, '2022-08-31 02:03:06', 'Logged out Michael Malate');

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `patient_id` int(8) UNSIGNED ZEROFILL NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `fname` varchar(30) NOT NULL,
  `lname` varchar(30) NOT NULL,
  `mname` varchar(30) NOT NULL,
  `birthplace` varchar(100) NOT NULL,
  `birthdate` date NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `contact` varchar(15) NOT NULL,
  `address` varchar(50) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT 'default-pic.jpg',
  `role` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`patient_id`, `email`, `password`, `fname`, `lname`, `mname`, `birthplace`, `birthdate`, `gender`, `contact`, `address`, `status`, `image`, `role`) VALUES
(00000001, 'malatemichael01@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Michael', 'Malate', 'Serondo', 'Cantiguib', '2022-08-06', 'Male', '09104194324', 'Cantiguib', 'Active', '06728620.jpg', 'Patient'),
(00000002, 'malatemichael02@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Michael', 'Malate', 'Serondo', 'Cantiguib', '2022-08-06', 'Male', '09104194324', 'Cantiguib', 'Active', '06728620.jpg', 'Patient'),
(00000003, 'malatemichael03@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Michael', 'Malate', 'Serondo', 'Cantiguib', '2022-08-06', 'Male', '09104194324', 'Cantiguib', 'Active', '06728620.jpg', 'Patient'),
(00000004, 'malatemichael04@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Amara Brielle', 'Malate', 'Banda', 'Cantiguib', '2022-08-06', 'Female', '09104194324', 'Cantiguib', 'Active', 'sm-freeimg.jpg', 'Patient'),
(00000005, 'malatemichael05@gmail.com', 'fd4446424e45dcdf0cc322eb7aa277e3', 'Amara Brielle', 'Malate', 'Serondo', 'Cantiguib', '2022-08-06', 'Female', '09679030746', 'Cantiguib', 'Active', 'christen-freeimg.jpg', 'Patient'),
(00000006, 'malatemichael06@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Michael', 'Malate', 'Banda', 'Cantiguib', '2022-08-06', 'Male', '09104194324', 'Cantiguib', 'Active', 'download.jpg', 'Patient'),
(00000007, 'malatemichael07@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Michael', 'Malate', 'Banda', 'Cantiguib', '2022-08-06', 'Male', '09104194324', 'Cantiguib', 'Active', 'smportrait.jpg', 'Patient'),
(00000008, 'malatemichael08@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Michael', 'Malate', 'Banda', 'Cantiguib', '2022-08-06', 'Male', '09104194324', 'Cantiguib', 'Active', '02723-2022=2022-08-01=Profile=18-34-16-PM.jpg', 'Patient'),
(00000009, 'malatemichael09@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Michael', 'Malate', 'Banda', 'Cantiguib', '2022-08-06', 'Male', '09104194324', 'Cantiguib', 'Active', 'avatar_02.jpg', 'Patient'),
(00000010, 'malatemichael10@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Michael', 'Malate', 'Banda', 'Cantiguib', '2022-08-06', 'Male', '09104194324', 'Cantiguib', 'Active', '02723-2022=2022-08-01=Profile=18-34-16-PM.jpg', 'Patient'),
(00000011, 'malatemichael11@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Michael', 'Malate', 'Banda', 'Cantiguib', '2022-08-06', 'Male', '09104194324', 'Cantiguib', 'Active', '1748488740610ba69164fdc.jpg', 'Patient'),
(00000012, 'malatemichael12@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Michael', 'Malate', 'Banda', 'Cantiguib', '2022-08-06', 'Male', '09104194324', 'Cantiguib', 'Active', 'download.jpg', 'Patient'),
(00000013, 'malatemichael13@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Michael', 'Malate', 'Banda', 'Cantiguib', '2022-08-06', 'Male', '09104194324', 'Cantiguib', 'Active', 'download.jpg', 'Patient'),
(00000014, 'malatemichael14@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Michael', 'Malate', 'Banda', 'Cantiguib', '2022-08-06', 'Male', '09104194324', 'Cantiguib', 'Active', 'download.jpg', 'Patient'),
(00000015, 'malatemichael15@gmail.com', '06abd1b2bbdd6b5c4e11945eaca6e34c', 'Amara Brielle', 'Malate', 'Banda', 'Cantiguib', '2022-08-06', 'Female', '09104194324', 'Cantiguib', 'Active', 'avatar_02.jpg', 'Patient'),
(00000016, 'malatemichael16@gmail.com', '5719926390af9882527844c70197a861', 'Michael', 'Malate', 'Banda', 'Cantiguib', '2022-08-08', 'Male', '09104194324', 'Cantiguib', 'Active', 'download.jpg', 'Patient'),
(00000017, 'malatemichael17@gmail.com', '0c9399b2898063488e73c95b679d83f5', 'Michael', 'Malate', 'Serondo', 'Quezon City, Metro Manila', '2022-08-01', 'Male', '09104194324', 'Cantiguib', 'Active', 'default-pic.jpg', 'Patient');

-- --------------------------------------------------------

--
-- Table structure for table `physician`
--

CREATE TABLE `physician` (
  `physician_id` int(8) UNSIGNED ZEROFILL NOT NULL,
  `password` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `fname` varchar(30) NOT NULL,
  `lname` varchar(30) NOT NULL,
  `mname` varchar(30) NOT NULL,
  `birthplace` varchar(100) NOT NULL,
  `birthdate` date NOT NULL,
  `gender` enum('Male','Female','','') NOT NULL,
  `contact` varchar(15) NOT NULL,
  `address` varchar(50) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL,
  `image` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `physician`
--

INSERT INTO `physician` (`physician_id`, `password`, `email`, `fname`, `lname`, `mname`, `birthplace`, `birthdate`, `gender`, `contact`, `address`, `status`, `image`, `role`) VALUES
(00000001, 'e10adc3949ba59abbe56e057f20f883e', 'm.malate@hnu.edu.ph', 'Michael', 'Malate', 'Serondo', 'Quezon City, Metro Manila', '1999-04-26', 'Male', '09104194323', 'Cantiguib, Alburquerque, Bohol', 'Active', 'skport.jpg', 'Physician'),
(00000002, 'd71079fbb5683adf29310dd612598654', 'd.banda@hnu.edu.ph', 'Danna Mae', 'Banda', 'Autencio', 'Tagbilaran City', '2019-06-28', 'Female', '09104018596', 'Basacdacu, Alburquerque, Bohol', 'Active', 'default-pic.jpg', 'Physician'),
(00000003, 'e10adc3949ba59abbe56e057f20f883e', 'guineskurtkarl@gmail.com', 'Danna', 'Malate', 'Libot', 'Tagbilaran City', '2019-06-26', 'Male', '09104018596', 'Tagbilaran City', 'Active', 'default-pic.jpg', 'Physician'),
(00000004, 'da45f0d4186d43e3292367af72446e1b', 'v.ygot@hnu.edu.ph', 'Vanessa', 'Ygot', 'Cruz', 'Tagbilaran City', '2019-06-19', 'Female', '09104018596', 'Tagbilaran City', 'Active', 'default-pic.jpg', 'Physician'),
(00000005, 'e10adc3949ba59abbe56e057f20f883e', 'v.cantiberos@hnu.edu.ph', 'Vince', 'Cantiberos', 'Autencio', 'Tagbilaran City', '2019-06-12', 'Male', '09104018595', 'Tagbilaran City', 'Active', 'default-pic.jpg', 'Physician'),
(00000006, 'e10adc3949ba59abbe56e057f20f883e', 'v.hajiron@hnu.edu.ph', 'Vincent', 'Hajiron', 'Serondo', 'Tagbilaran City', '2019-06-25', 'Male', '09104018596', 'Tagbilaran City', 'Active', 'default-pic.jpg', 'Physician'),
(00000007, 'e10adc3949ba59abbe56e057f20f883e', 'h.talatagod@hnu.edu.ph', 'Harold', 'Talatagod', 'Serondo', 'Tagbilaran City', '2019-06-13', 'Male', '09104018595', 'Tagbilaran City', 'Active', 'default-pic.jpg', 'Physician'),
(00000008, 'e10adc3949ba59abbe56e057f20f883e', 'c.dolorican@hnu.edu.ph', 'Claire', 'Dolorican', 'Cruz', 'Tagbilaran City', '2019-06-27', 'Female', '09104018595', 'Tagbilaran City', 'Active', 'default-pic.jpg', 'Physician'),
(00000009, 'e10adc3949ba59abbe56e057f20f883e', 'k.gida@hnu.edu.ph', 'Karlo', 'Gida', 'Cruz', 'Tagbilaran City', '2019-06-25', 'Male', '09104018596', 'Tagbilaran City', 'Active', 'default-pic.jpg', 'Physician'),
(00000010, 'e10adc3949ba59abbe56e057f20f883e', 'f.cinco@hnu.edu.ph', 'Fidel Rey', 'Cinco', 'Banda', 'Tagbilaran City', '2019-06-28', 'Male', '09104018596', 'Tagbilaran City', 'Active', '7002489_preview.jpg', 'Physician');

-- --------------------------------------------------------

--
-- Table structure for table `physician_clinic`
--

CREATE TABLE `physician_clinic` (
  `id` bigint(5) NOT NULL,
  `physician_id` bigint(5) NOT NULL,
  `clinic_id` bigint(5) NOT NULL,
  `secretary_id` bigint(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `physician_clinic`
--

INSERT INTO `physician_clinic` (`id`, `physician_id`, `clinic_id`, `secretary_id`) VALUES
(1, 1, 1, 10),
(2, 2, 2, 9),
(3, 3, 3, 8),
(4, 4, 4, 7),
(5, 5, 5, 6),
(6, 6, 6, 5),
(7, 7, 7, 4),
(8, 8, 8, 3),
(9, 9, 9, 2),
(10, 10, 10, 1),
(11, 10, 11, 10),
(12, 9, 12, 9),
(13, 1, 13, 11);

-- --------------------------------------------------------

--
-- Table structure for table `physician_sched`
--

CREATE TABLE `physician_sched` (
  `id` bigint(5) NOT NULL,
  `physician_id` bigint(5) NOT NULL,
  `schedule_id` bigint(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `physician_sched`
--

INSERT INTO `physician_sched` (`id`, `physician_id`, `schedule_id`) VALUES
(1, 1, 7),
(2, 2, 8),
(3, 3, 9),
(4, 4, 10),
(5, 5, 11),
(6, 6, 12),
(7, 7, 13),
(8, 8, 14),
(9, 9, 15),
(10, 10, 16);

-- --------------------------------------------------------

--
-- Table structure for table `physician_special`
--

CREATE TABLE `physician_special` (
  `id` bigint(5) NOT NULL,
  `physician_id` bigint(5) NOT NULL,
  `special_id` bigint(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `physician_special`
--

INSERT INTO `physician_special` (`id`, `physician_id`, `special_id`) VALUES
(1, 2, 1),
(2, 1, 2),
(3, 9, 3),
(4, 10, 10),
(5, 1, 11),
(6, 2, 12),
(7, 3, 13),
(8, 4, 14),
(9, 5, 15),
(10, 6, 16),
(11, 7, 17),
(12, 8, 18),
(13, 9, 19),
(14, 10, 20);

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `q_id` int(2) NOT NULL,
  `question` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`q_id`, `question`) VALUES
(1, 'What is your mother\'s maiden name?'),
(2, 'In what school did you finished elementary?'),
(3, 'What is your favorite color?'),
(4, 'What is your birth year?'),
(5, 'What is your father\'s first name?');

-- --------------------------------------------------------

--
-- Table structure for table `queuecount`
--

CREATE TABLE `queuecount` (
  `id` bigint(5) NOT NULL,
  `dateLimit` date NOT NULL,
  `physician_id` bigint(5) NOT NULL,
  `queueLimit` bigint(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `queuecount`
--

INSERT INTO `queuecount` (`id`, `dateLimit`, `physician_id`, `queueLimit`) VALUES
(1, '2019-07-03', 1, 20),
(2, '2019-07-03', 2, 20),
(3, '0000-00-00', 2, 20),
(4, '2019-07-03', 10, 1),
(5, '2019-07-01', 9, 20),
(6, '2019-07-10', 1, 20),
(7, '2019-07-05', 2, 20),
(8, '2019-07-21', 4, 20),
(9, '2019-07-17', 1, 20),
(10, '2019-07-23', 1, 20),
(11, '2019-07-29', 1, 20),
(12, '2019-07-11', 1, 2),
(13, '2019-07-17', 10, 0),
(14, '2019-07-17', 10, 0),
(15, '2019-07-30', 10, 2),
(16, '2019-07-17', 10, 20),
(17, '2019-07-14', 4, 20),
(18, '2019-07-17', 2, 2),
(19, '2019-07-18', 2, 2),
(20, '2019-07-22', 10, 2),
(21, '2019-07-23', 2, 2),
(22, '2019-07-24', 1, 20),
(23, '2019-07-30', 1, 20),
(24, '2019-08-05', 1, 20),
(25, '2019-08-19', 7, 20),
(26, '2019-09-10', 2, 20),
(27, '2019-09-11', 1, 20),
(28, '2019-09-08', 4, 20),
(29, '2019-09-15', 4, 20),
(30, '2019-09-11', 7, 20),
(31, '2021-04-05', 1, 20),
(32, '2021-10-03', 4, 20),
(33, '2022-08-08', 1, 20),
(34, '2022-08-02', 1, 3),
(35, '2022-08-10', 1, 20),
(36, '2022-08-14', 4, 20),
(37, '2022-08-09', 1, 20),
(38, '2022-08-10', 10, 20),
(39, '2022-08-17', 10, 20),
(40, '2022-08-10', 10, 20),
(41, '2022-08-17', 1, 20),
(42, '2022-08-15', 1, 6),
(43, '2022-08-16', 1, 20),
(44, '2022-08-15', 10, 20),
(45, '2022-08-13', 1, 10),
(46, '2022-08-22', 1, 4),
(47, '2022-08-15', 1, 20),
(48, '2022-08-31', 1, 20),
(49, '2022-08-24', 1, 5),
(50, '2022-08-22', 10, 3),
(51, '2022-08-22', 10, 1),
(52, '2022-08-23', 10, 4);

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `schedule_id` bigint(5) NOT NULL,
  `day` varchar(10) NOT NULL,
  `time_in` time NOT NULL,
  `time_out` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`schedule_id`, `day`, `time_in`, `time_out`) VALUES
(1, 'MTW', '07:30:00', '12:30:00'),
(2, 'Sat', '12:00:00', '17:30:00'),
(3, 'MTW', '14:30:00', '20:30:00'),
(4, 'MTW', '12:00:00', '10:00:00'),
(5, 'MTW', '12:30:00', '20:30:00'),
(6, 'M-F', '12:00:00', '22:00:00'),
(7, 'MTW', '08:00:00', '20:30:00'),
(8, 'M-F', '12:00:00', '21:00:00'),
(9, 'MTW', '08:00:00', '17:00:00'),
(10, 'Sun', '08:00:00', '06:00:00'),
(11, 'ThF', '07:30:00', '00:00:00'),
(12, 'Sat', '06:30:00', '12:00:00'),
(13, 'MTW', '04:30:00', '00:30:00'),
(14, 'MTW', '13:30:00', '19:30:00'),
(15, 'MTW', '14:00:00', '17:30:00'),
(16, 'MTW', '08:00:00', '18:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `secretary`
--

CREATE TABLE `secretary` (
  `secretary_id` int(8) UNSIGNED ZEROFILL NOT NULL,
  `physician_id` bigint(5) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `fname` varchar(30) NOT NULL,
  `lname` varchar(30) NOT NULL,
  `mname` varchar(30) NOT NULL,
  `birthplace` varchar(30) NOT NULL,
  `birthdate` date NOT NULL,
  `gender` enum('Male','Female','','') NOT NULL,
  `contact` varchar(11) NOT NULL,
  `address` varchar(50) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL,
  `image` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `secretary`
--

INSERT INTO `secretary` (`secretary_id`, `physician_id`, `email`, `password`, `fname`, `lname`, `mname`, `birthplace`, `birthdate`, `gender`, `contact`, `address`, `status`, `image`, `role`) VALUES
(00000001, 10, 'm.malate@hnu.edu.ph', 'e10adc3949ba59abbe56e057f20f883e', 'Michael ', 'Malate', 'Serondo', 'Quezon City, Metro Manila', '1999-04-26', 'Male', '09104018596', 'Albur', 'Active', '06728620.jpg', 'Secretary'),
(00000002, 9, 'd.banda@hnu.edu.ph', 'e10adc3949ba59abbe56e057f20f883e', 'Danna Mae', 'Malate', 'Banda', 'Albur', '1998-11-05', 'Female', '09104018595', 'Albur', 'Active', 'default-pic.jpg', 'Secretary'),
(00000003, 8, 'guineskurtkarl@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'Kurt Karl', 'Guines', 'Autencio', 'Tagbilaran City', '2019-05-30', 'Male', '09104018596', 'Tagbilaran City', 'Active', 'default-pic.jpg', 'Secretary'),
(00000004, 7, 'v.ygot@hnu.edu.ph', 'e10adc3949ba59abbe56e057f20f883e', 'Vanessa', 'Ygot', 'Autencio', 'Tagbilaran City', '2019-05-24', 'Female', '09104018595', 'Tagbilaran City', 'Active', 'default-pic.jpg', 'Secretary'),
(00000005, 6, 'v.cantiberos@hnu.edu.ph', 'e10adc3949ba59abbe56e057f20f883e', 'Vince', 'Cantiberos', 'Libot', 'Tagbilaran City', '2019-06-26', 'Male', '09104018595', 'Tagbilaran City', 'Active', 'default-pic.jpg', 'Secretary'),
(00000006, 5, 'v.hajiron@hnu.edu.ph', 'e10adc3949ba59abbe56e057f20f883e', 'Vincent', 'Hajiron', 'Serondo', 'Tagbilaran City', '2019-07-03', 'Male', '09104018596', 'Tagbilaran City', 'Active', 'default-pic.jpg', 'Secretary'),
(00000007, 4, 'h.talatagod@hnu.edu.ph', 'e10adc3949ba59abbe56e057f20f883e', 'Harold', 'Talatagod', 'Cruz', 'Tagbilaran City', '2019-06-25', 'Male', '09104018596', 'Tagbilaran City', 'Active', 'default-pic.jpg', 'Secretary'),
(00000008, 3, 'c.dolorican@hnu.edu.ph', 'e10adc3949ba59abbe56e057f20f883e', 'Claire', 'Dolorican', 'Cruz', 'Tagbilaran City', '2019-06-26', 'Female', '09104018596', 'Tagbilaran City', 'Active', 'default-pic.jpg', 'Secretary'),
(00000009, 9, 'k.gida@hnu.edu.ph', 'e10adc3949ba59abbe56e057f20f883e', 'Karlo', 'Gida', 'Libot', 'Tagbilaran City', '2019-06-05', 'Male', '09104018595', 'Tagbilaran City', 'Active', 'default-pic.jpg', 'Secretary'),
(00000010, 10, 'f.cinco@hnu.edu.ph', 'dfb5b4307fbfe42fcdc056e7b93b0ff8', 'Fidel ', 'Cinco', 'Libot', 'Tagbilaran City', '2019-06-11', 'Male', '09104018596', 'Tagbilaran City', 'Active', 'default-pic.jpg', 'Secretary'),
(00000011, 1, 'secretary@secretary.com', '7944895b7bf75f0b5f4adcc38d7b02c1', 'Neka Althea', 'Suma-oy', 'Caballo', 'Tagbilaran City', '2019-09-19', 'Female', '09104018596', 'Tagbilaran City', 'Active', 'default-pic.jpg', 'Secretary');

-- --------------------------------------------------------

--
-- Table structure for table `specialization`
--

CREATE TABLE `specialization` (
  `special_id` bigint(5) NOT NULL,
  `special_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `specialization`
--

INSERT INTO `specialization` (`special_id`, `special_name`) VALUES
(1, 'Family Medicine'),
(2, 'Family Medicine'),
(3, 'OB - GYNE'),
(4, 'Cardiologist'),
(5, 'Neurologist'),
(6, 'Dermatologist'),
(9, 'Pediatrician'),
(10, 'Dermatologist'),
(11, 'Internal Medicine '),
(12, 'Rheumatologist '),
(13, 'Cardiologist'),
(14, 'Psychiatrist  '),
(15, 'Neurologist '),
(16, 'Ophthalmologist '),
(17, 'Neurologist '),
(18, 'Onconlogist '),
(19, 'Urologist  '),
(20, 'Cardiologist');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `answer`
--
ALTER TABLE `answer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`appointment_id`);

--
-- Indexes for table `clinic`
--
ALTER TABLE `clinic`
  ADD PRIMARY KEY (`clinic_id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`patient_id`);

--
-- Indexes for table `physician`
--
ALTER TABLE `physician`
  ADD PRIMARY KEY (`physician_id`);

--
-- Indexes for table `physician_clinic`
--
ALTER TABLE `physician_clinic`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `physician_sched`
--
ALTER TABLE `physician_sched`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `physician_special`
--
ALTER TABLE `physician_special`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`q_id`);

--
-- Indexes for table `queuecount`
--
ALTER TABLE `queuecount`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`schedule_id`);

--
-- Indexes for table `secretary`
--
ALTER TABLE `secretary`
  ADD PRIMARY KEY (`secretary_id`);

--
-- Indexes for table `specialization`
--
ALTER TABLE `specialization`
  ADD PRIMARY KEY (`special_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(8) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `answer`
--
ALTER TABLE `answer`
  MODIFY `id` bigint(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `appointment_id` int(8) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `clinic`
--
ALTER TABLE `clinic`
  MODIFY `clinic_id` bigint(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(8) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `patient_id` int(8) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `physician`
--
ALTER TABLE `physician`
  MODIFY `physician_id` int(8) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `physician_clinic`
--
ALTER TABLE `physician_clinic`
  MODIFY `id` bigint(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `physician_sched`
--
ALTER TABLE `physician_sched`
  MODIFY `id` bigint(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `physician_special`
--
ALTER TABLE `physician_special`
  MODIFY `id` bigint(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `q_id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `queuecount`
--
ALTER TABLE `queuecount`
  MODIFY `id` bigint(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `schedule_id` bigint(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `secretary`
--
ALTER TABLE `secretary`
  MODIFY `secretary_id` int(8) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `specialization`
--
ALTER TABLE `specialization`
  MODIFY `special_id` bigint(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
