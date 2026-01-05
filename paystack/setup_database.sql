-- Paystack Transactions Table
-- Run this SQL to create the required table for Paystack payments

CREATE TABLE IF NOT EXISTS `paystack_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reference` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `channel` varchar(50) DEFAULT NULL,
  `gateway_response` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference` (`reference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add admin_viewed column to mycheckout if it doesn't exist
-- This is used for tracking new orders in admin dashboard
ALTER TABLE `mycheckout` ADD COLUMN IF NOT EXISTS `admin_viewed` tinyint(1) DEFAULT 0;
