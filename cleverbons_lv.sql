-- phpMyAdmin SQL Dump
-- version 4.4.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Oct 09, 2015 at 04:59 AM
-- Server version: 5.6.25
-- PHP Version: 5.6.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cleverbons_lv`
--

-- --------------------------------------------------------

--
-- Table structure for table `auctions`
--

CREATE TABLE IF NOT EXISTS `auctions` (
  `id` int(11) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `properties_id` int(11) NOT NULL,
  `start_price` varchar(50) NOT NULL,
  `auction_price` varchar(50) NOT NULL,
  `bidding_price` varchar(50) NOT NULL,
  `reserve_price` varchar(50) NOT NULL,
  `asking_price` varchar(50) NOT NULL,
  `hourly_deduction` varchar(50) NOT NULL,
  `raise_bid_price` varchar(50) NOT NULL,
  `slow_auction_start_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `fast_auction_start_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `auction_duration` varchar(50) NOT NULL,
  `projected_end_time` varchar(50) NOT NULL,
  `bid_color` varchar(20) NOT NULL,
  `contract_filename` varchar(200) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '2',
  `paid` tinyint(1) NOT NULL DEFAULT '0',
  `winner_users_id` int(11) NOT NULL DEFAULT '0',
  `started` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Live aution has started or not'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bidders`
--

CREATE TABLE IF NOT EXISTS `bidders` (
  `id` int(11) NOT NULL,
  `auctions_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `starting_bid` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '2',
  `color` varchar(30) NOT NULL,
  `signed_contract_filename` varchar(200) NOT NULL,
  `bid_price` varchar(50) NOT NULL,
  `qualified` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `device_tokens`
--

CREATE TABLE IF NOT EXISTS `device_tokens` (
  `id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `os` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE IF NOT EXISTS `invoices` (
  `id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `json_data` mediumtext NOT NULL,
  `amount` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL,
  `invoice_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `type` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `has_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `observers`
--

CREATE TABLE IF NOT EXISTS `observers` (
  `id` int(11) NOT NULL,
  `auctions_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '2',
  `paid` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE IF NOT EXISTS `properties` (
  `id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL COMMENT 'id of agent user',
  `name` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `street` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `postcode` varchar(30) NOT NULL,
  `lat` varchar(50) NOT NULL,
  `lng` varchar(50) NOT NULL,
  `num_bedrooms` int(11) NOT NULL,
  `num_bathrooms` int(11) NOT NULL,
  `num_garage` int(11) NOT NULL,
  `landarea` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `property_files`
--

CREATE TABLE IF NOT EXISTS `property_files` (
  `id` int(11) NOT NULL,
  `properties_id` int(11) NOT NULL,
  `type` varchar(100) NOT NULL DEFAULT 'image',
  `codedname` varchar(200) NOT NULL,
  `filename` varchar(200) NOT NULL,
  `extension` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `password` varchar(200) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `is_loggedin` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `remember_token` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_company_details`
--

CREATE TABLE IF NOT EXISTS `user_company_details` (
  `users_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `street` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `postcode` varchar(50) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `logo` varchar(200) NOT NULL,
  `primary_color` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user_details`
--

CREATE TABLE IF NOT EXISTS `user_details` (
  `users_id` int(11) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `cellphone` varchar(50) NOT NULL,
  `city` varchar(50) NOT NULL,
  `suburb` varchar(50) NOT NULL,
  `proof_income` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `xplogs`
--

CREATE TABLE IF NOT EXISTS `xplogs` (
  `id` int(11) NOT NULL,
  `backup_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `logs` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auctions`
--
ALTER TABLE `auctions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bidders`
--
ALTER TABLE `bidders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `device_tokens`
--
ALTER TABLE `device_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `observers`
--
ALTER TABLE `observers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `property_files`
--
ALTER TABLE `property_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `xplogs`
--
ALTER TABLE `xplogs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auctions`
--
ALTER TABLE `auctions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `bidders`
--
ALTER TABLE `bidders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `device_tokens`
--
ALTER TABLE `device_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `observers`
--
ALTER TABLE `observers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `property_files`
--
ALTER TABLE `property_files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `xplogs`
--
ALTER TABLE `xplogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
