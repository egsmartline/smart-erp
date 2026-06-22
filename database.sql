-- MySQL dump 10.13  Distrib 8.4.9, for Win64 (x86_64)
--
-- Host: ::1    Database: smart_erp
-- ------------------------------------------------------
-- Server version	8.4.9

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `analytical_accounts`
--

DROP TABLE IF EXISTS `analytical_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `analytical_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('cost_center','profit_center','project','department') COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `budget_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `current_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `analytical_accounts_code_unique` (`code`),
  KEY `analytical_accounts_tenant_id_foreign` (`tenant_id`),
  KEY `analytical_accounts_parent_id_foreign` (`parent_id`),
  CONSTRAINT `analytical_accounts_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `analytical_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `analytical_accounts_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `analytical_accounts`
--

LOCK TABLES `analytical_accounts` WRITE;
/*!40000 ALTER TABLE `analytical_accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `analytical_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attendance` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `employee_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `check_in` time NOT NULL,
  `check_out` time DEFAULT NULL,
  `work_hours` decimal(5,2) DEFAULT NULL,
  `overtime_hours` decimal(5,2) NOT NULL DEFAULT '0.00',
  `status` enum('present','absent','late','half_day','leave') COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendance_tenant_id_foreign` (`tenant_id`),
  KEY `attendance_employee_id_foreign` (`employee_id`),
  CONSTRAINT `attendance_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `attendance_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attendance`
--

LOCK TABLES `attendance` WRITE;
/*!40000 ALTER TABLE `attendance` DISABLE KEYS */;
/*!40000 ALTER TABLE `attendance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_foreign` (`user_id`),
  KEY `audit_logs_tenant_id_user_id_index` (`tenant_id`,`user_id`),
  KEY `audit_logs_tenant_id_action_index` (`tenant_id`,`action`),
  KEY `audit_logs_tenant_id_model_model_id_index` (`tenant_id`,`model`,`model_id`),
  KEY `audit_logs_tenant_id_created_at_index` (`tenant_id`,`created_at`),
  CONSTRAINT `audit_logs_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

LOCK TABLES `audit_logs` WRITE;
/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `backup_logs`
--

DROP TABLE IF EXISTS `backup_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `backup_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint NOT NULL DEFAULT '0',
  `type` enum('manual','auto') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `status` enum('pending','completed','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `backup_logs_user_id_foreign` (`user_id`),
  KEY `backup_logs_tenant_id_type_index` (`tenant_id`,`type`),
  KEY `backup_logs_tenant_id_status_index` (`tenant_id`,`status`),
  CONSTRAINT `backup_logs_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `backup_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `backup_logs`
--

LOCK TABLES `backup_logs` WRITE;
/*!40000 ALTER TABLE `backup_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `backup_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bank_accounts`
--

DROP TABLE IF EXISTS `bank_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bank_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `account_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `iban` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `swift_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `branch` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_id` bigint unsigned DEFAULT NULL,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `current_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `account_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bank_accounts_currency_id_foreign` (`currency_id`),
  KEY `bank_accounts_account_id_foreign` (`account_id`),
  KEY `bank_accounts_tenant_id_is_active_index` (`tenant_id`,`is_active`),
  CONSTRAINT `bank_accounts_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bank_accounts_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bank_accounts_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bank_accounts`
--

LOCK TABLES `bank_accounts` WRITE;
/*!40000 ALTER TABLE `bank_accounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `bank_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bank_statement_lines`
--

DROP TABLE IF EXISTS `bank_statement_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bank_statement_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `bank_statement_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_type` enum('customer_payment','supplier_payment','bank_charge','interest','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `partner_type` enum('customer','supplier','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `partner_id` bigint unsigned DEFAULT NULL,
  `currency_id` bigint unsigned DEFAULT NULL,
  `exchange_rate` decimal(15,6) NOT NULL DEFAULT '1.000000',
  `is_reconciled` tinyint(1) NOT NULL DEFAULT '0',
  `reconciled_date` date DEFAULT NULL,
  `journal_entry_line_id` bigint unsigned DEFAULT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bank_statement_lines_tenant_id_foreign` (`tenant_id`),
  KEY `bank_statement_lines_bank_statement_id_foreign` (`bank_statement_id`),
  KEY `bank_statement_lines_currency_id_foreign` (`currency_id`),
  KEY `bank_statement_lines_journal_entry_line_id_foreign` (`journal_entry_line_id`),
  CONSTRAINT `bank_statement_lines_bank_statement_id_foreign` FOREIGN KEY (`bank_statement_id`) REFERENCES `bank_statements` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bank_statement_lines_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bank_statement_lines_journal_entry_line_id_foreign` FOREIGN KEY (`journal_entry_line_id`) REFERENCES `journal_entry_lines` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bank_statement_lines_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bank_statement_lines`
--

LOCK TABLES `bank_statement_lines` WRITE;
/*!40000 ALTER TABLE `bank_statement_lines` DISABLE KEYS */;
/*!40000 ALTER TABLE `bank_statement_lines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bank_statements`
--

DROP TABLE IF EXISTS `bank_statements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bank_statements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `bank_account_id` bigint unsigned NOT NULL,
  `journal_id` bigint unsigned NOT NULL,
  `statement_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `start_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `end_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `balance_difference` decimal(15,2) NOT NULL DEFAULT '0.00',
  `state` enum('draft','posted','reconciled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `user_id` bigint unsigned NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bank_statements_tenant_id_foreign` (`tenant_id`),
  KEY `bank_statements_bank_account_id_foreign` (`bank_account_id`),
  KEY `bank_statements_journal_id_foreign` (`journal_id`),
  KEY `bank_statements_user_id_foreign` (`user_id`),
  CONSTRAINT `bank_statements_bank_account_id_foreign` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_accounts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bank_statements_journal_id_foreign` FOREIGN KEY (`journal_id`) REFERENCES `journals` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bank_statements_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bank_statements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bank_statements`
--

LOCK TABLES `bank_statements` WRITE;
/*!40000 ALTER TABLE `bank_statements` DISABLE KEYS */;
/*!40000 ALTER TABLE `bank_statements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bank_transactions`
--

DROP TABLE IF EXISTS `bank_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bank_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `bank_account_id` bigint unsigned NOT NULL,
  `type` enum('in','out','transfer','opening') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `reference_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `check_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_date` date DEFAULT NULL,
  `reference_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `target_bank_account_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bank_transactions_bank_account_id_foreign` (`bank_account_id`),
  KEY `bank_transactions_user_id_foreign` (`user_id`),
  KEY `bank_transactions_target_bank_account_id_foreign` (`target_bank_account_id`),
  KEY `bank_transactions_tenant_id_bank_account_id_index` (`tenant_id`,`bank_account_id`),
  KEY `bank_transactions_tenant_id_type_index` (`tenant_id`,`type`),
  KEY `bank_trans_ref_index` (`tenant_id`,`reference_type`,`reference_id`),
  CONSTRAINT `bank_transactions_bank_account_id_foreign` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_accounts` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `bank_transactions_target_bank_account_id_foreign` FOREIGN KEY (`target_bank_account_id`) REFERENCES `bank_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bank_transactions_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bank_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bank_transactions`
--

LOCK TABLES `bank_transactions` WRITE;
/*!40000 ALTER TABLE `bank_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `bank_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `budget_lines`
--

DROP TABLE IF EXISTS `budget_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `budget_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `budget_id` bigint unsigned NOT NULL,
  `account_id` bigint unsigned NOT NULL,
  `analytical_account_id` bigint unsigned DEFAULT NULL,
  `planned_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `actual_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `budget_lines_tenant_id_foreign` (`tenant_id`),
  KEY `budget_lines_budget_id_foreign` (`budget_id`),
  KEY `budget_lines_account_id_foreign` (`account_id`),
  KEY `budget_lines_analytical_account_id_foreign` (`analytical_account_id`),
  CONSTRAINT `budget_lines_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `budget_lines_analytical_account_id_foreign` FOREIGN KEY (`analytical_account_id`) REFERENCES `analytical_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `budget_lines_budget_id_foreign` FOREIGN KEY (`budget_id`) REFERENCES `budgets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `budget_lines_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `budget_lines`
--

LOCK TABLES `budget_lines` WRITE;
/*!40000 ALTER TABLE `budget_lines` DISABLE KEYS */;
/*!40000 ALTER TABLE `budget_lines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `budgets`
--

DROP TABLE IF EXISTS `budgets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `budgets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fiscal_year_id` bigint unsigned NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `state` enum('draft','confirmed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `total_planned_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_actual_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `user_id` bigint unsigned NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `budgets_tenant_id_foreign` (`tenant_id`),
  KEY `budgets_fiscal_year_id_foreign` (`fiscal_year_id`),
  KEY `budgets_user_id_foreign` (`user_id`),
  CONSTRAINT `budgets_fiscal_year_id_foreign` FOREIGN KEY (`fiscal_year_id`) REFERENCES `fiscal_years` (`id`) ON DELETE CASCADE,
  CONSTRAINT `budgets_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `budgets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `budgets`
--

LOCK TABLES `budgets` WRITE;
/*!40000 ALTER TABLE `budgets` DISABLE KEYS */;
/*!40000 ALTER TABLE `budgets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cash_treasuries`
--

DROP TABLE IF EXISTS `cash_treasuries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cash_treasuries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency_id` bigint unsigned DEFAULT NULL,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `current_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `account_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `whatsapp_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cash_treasuries_tenant_id_code_unique` (`tenant_id`,`code`),
  KEY `cash_treasuries_currency_id_foreign` (`currency_id`),
  KEY `cash_treasuries_account_id_foreign` (`account_id`),
  KEY `cash_treasuries_user_id_foreign` (`user_id`),
  KEY `cash_treasuries_tenant_id_is_active_index` (`tenant_id`,`is_active`),
  CONSTRAINT `cash_treasuries_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cash_treasuries_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cash_treasuries_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cash_treasuries_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cash_treasuries`
--

LOCK TABLES `cash_treasuries` WRITE;
/*!40000 ALTER TABLE `cash_treasuries` DISABLE KEYS */;
/*!40000 ALTER TABLE `cash_treasuries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chart_of_accounts`
--

DROP TABLE IF EXISTS `chart_of_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chart_of_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('asset','liability','equity','revenue','expense') COLLATE utf8mb4_unicode_ci NOT NULL,
  `sub_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `opening_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `current_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_header` tinyint(1) NOT NULL DEFAULT '1',
  `is_system` tinyint(1) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `chart_of_accounts_tenant_id_code_unique` (`tenant_id`,`code`),
  KEY `chart_of_accounts_parent_id_foreign` (`parent_id`),
  KEY `chart_of_accounts_tenant_id_type_index` (`tenant_id`,`type`),
  KEY `chart_of_accounts_tenant_id_parent_id_index` (`tenant_id`,`parent_id`),
  KEY `chart_of_accounts_tenant_id_is_active_index` (`tenant_id`,`is_active`),
  CONSTRAINT `chart_of_accounts_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `chart_of_accounts_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chart_of_accounts`
--

LOCK TABLES `chart_of_accounts` WRITE;
/*!40000 ALTER TABLE `chart_of_accounts` DISABLE KEYS */;
INSERT INTO `chart_of_accounts` VALUES (1,1,'1','أصول','Assets','asset','main',NULL,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(2,1,'11','أصول متداولة','Current Assets','asset','main',1,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(3,1,'1101','نقداً','Cash','asset','current_assets',2,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(4,1,'1102','بنوك','Bank','asset','current_assets',2,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(5,1,'1103','حسابات المدينة','Accounts Receivable','asset','current_assets',2,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(6,1,'1104','مخزون','Inventory','asset','current_assets',2,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(7,1,'12','أصول ثابتة','Fixed Assets','asset','main',1,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(8,1,'1201','مباني','Buildings','asset','fixed_assets',7,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(9,1,'1202','معدات','Equipment','asset','fixed_assets',7,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(10,1,'1203','سيارات','Vehicles','asset','fixed_assets',7,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(11,1,'2','خصوم','Liabilities','liability','main',NULL,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(12,1,'21','خصوم متداولة','Current Liabilities','liability','main',11,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(13,1,'2101','حسابات الدائن','Accounts Payable','liability','current_liabilities',12,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(14,1,'2102','ضريبة مستحقة','Tax Payable','liability','current_liabilities',12,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(15,1,'2103','قروض قصيرة','Short-term Loans','liability','current_liabilities',12,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(16,1,'22','خصوم طويلة','Long-term Liabilities','liability','main',11,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(17,1,'2201','قروض طويلة','Long-term Loans','liability','long_term_liabilities',16,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(18,1,'3','حقوق ملكية','Equity','equity','main',NULL,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(19,1,'31','رأس المال','Capital','equity','capital',18,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(20,1,'32','أرباح مبقاة','Retained Earnings','equity','retained_earnings',18,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(21,1,'33','ربح السنة','Current Year Profit','equity','current_year_profit',18,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(22,1,'4','إيرادات','Revenue','revenue','main',NULL,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(23,1,'41','إيرادات المبيعات','Sales Revenue','revenue','sales_revenue',22,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(24,1,'42','إيرادات الخدمات','Service Revenue','revenue','service_revenue',22,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(25,1,'43','إيرادات أخرى','Other Revenue','revenue','other_revenue',22,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(26,1,'5','مصروفات','Expenses','expense','main',NULL,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(27,1,'51','تكلفة البضاعة','Cost of Goods','expense','cost_of_goods',26,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(28,1,'52','رواتب','Salaries','expense','salaries',26,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(29,1,'53','إيجار','Rent','expense','rent',26,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(30,1,'54','مرافق','Utilities','expense','utilities',26,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(31,1,'55','إهلاك','Depreciation','expense','depreciation',26,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(32,1,'56','مصروفات أخرى','Other Expenses','expense','other_expenses',26,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 05:11:26','2026-06-21 06:21:22',NULL),(33,2,'1','أصول','Assets','asset','main',NULL,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(34,2,'11','أصول متداولة','Current Assets','asset','main',33,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(35,2,'1101','نقداً','Cash','asset','current_assets',34,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(36,2,'1102','بنوك','Bank','asset','current_assets',34,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(37,2,'1103','حسابات المدينة','Accounts Receivable','asset','current_assets',34,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(38,2,'1104','مخزون','Inventory','asset','current_assets',34,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(39,2,'12','أصول ثابتة','Fixed Assets','asset','main',33,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(40,2,'1201','مباني','Buildings','asset','fixed_assets',39,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(41,2,'1202','معدات','Equipment','asset','fixed_assets',39,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(42,2,'1203','سيارات','Vehicles','asset','fixed_assets',39,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(43,2,'2','خصوم','Liabilities','liability','main',NULL,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(44,2,'21','خصوم متداولة','Current Liabilities','liability','main',43,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(45,2,'2101','حسابات الدائن','Accounts Payable','liability','current_liabilities',44,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(46,2,'2102','ضريبة مستحقة','Tax Payable','liability','current_liabilities',44,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(47,2,'2103','قروض قصيرة','Short-term Loans','liability','current_liabilities',44,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(48,2,'22','خصوم طويلة','Long-term Liabilities','liability','main',43,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(49,2,'2201','قروض طويلة','Long-term Loans','liability','long_term_liabilities',48,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(50,2,'3','حقوق ملكية','Equity','equity','main',NULL,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(51,2,'31','رأس المال','Capital','equity','capital',50,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(52,2,'32','أرباح مبقاة','Retained Earnings','equity','retained_earnings',50,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(53,2,'33','ربح السنة','Current Year Profit','equity','current_year_profit',50,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(54,2,'4','إيرادات','Revenue','revenue','main',NULL,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(55,2,'41','إيرادات المبيعات','Sales Revenue','revenue','sales_revenue',54,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(56,2,'42','إيرادات الخدمات','Service Revenue','revenue','service_revenue',54,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(57,2,'43','إيرادات أخرى','Other Revenue','revenue','other_revenue',54,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(58,2,'5','مصروفات','Expenses','expense','main',NULL,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(59,2,'51','تكلفة البضاعة','Cost of Goods','expense','cost_of_goods',58,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(60,2,'52','رواتب','Salaries','expense','salaries',58,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(61,2,'53','إيجار','Rent','expense','rent',58,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(62,2,'54','مرافق','Utilities','expense','utilities',58,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(63,2,'55','إهلاك','Depreciation','expense','depreciation',58,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL),(64,2,'56','مصروفات أخرى','Other Expenses','expense','other_expenses',58,0.00,0.00,0.00,1,1,0,NULL,'2026-06-20 06:44:45','2026-06-21 06:48:17',NULL);
/*!40000 ALTER TABLE `chart_of_accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `companies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `address_en` text COLLATE utf8mb4_unicode_ci,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `commercial_registration` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'SAR',
  `secondary_currency_id` bigint unsigned DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signature_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `secondary_currency_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT 'USD',
  PRIMARY KEY (`id`),
  KEY `companies_tenant_id_is_active_index` (`tenant_id`,`is_active`),
  KEY `companies_secondary_currency_id_foreign` (`secondary_currency_id`),
  CONSTRAINT `companies_secondary_currency_id_foreign` FOREIGN KEY (`secondary_currency_id`) REFERENCES `currencies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `companies_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `companies`
--

LOCK TABLES `companies` WRITE;
/*!40000 ALTER TABLE `companies` DISABLE KEYS */;
/*!40000 ALTER TABLE `companies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contracts`
--

DROP TABLE IF EXISTS `contracts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contracts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `employee_id` bigint unsigned NOT NULL,
  `contract_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date DEFAULT NULL COMMENT 'null = open-ended',
  `contract_type` enum('permanent','temporary','part_time','internship') COLLATE utf8mb4_unicode_ci NOT NULL,
  `gross_salary` decimal(15,2) NOT NULL,
  `benefits` json DEFAULT NULL,
  `deductions` json DEFAULT NULL,
  `probation_period_days` int NOT NULL DEFAULT '90',
  `status` enum('draft','active','expired','terminated') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contracts_contract_number_unique` (`contract_number`),
  KEY `contracts_tenant_id_foreign` (`tenant_id`),
  KEY `contracts_employee_id_foreign` (`employee_id`),
  CONSTRAINT `contracts_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `contracts_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contracts`
--

LOCK TABLES `contracts` WRITE;
/*!40000 ALTER TABLE `contracts` DISABLE KEYS */;
/*!40000 ALTER TABLE `contracts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `currencies`
--

DROP TABLE IF EXISTS `currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `currencies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `symbol` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `exchange_rate` decimal(15,6) NOT NULL DEFAULT '1.000000',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `currencies_tenant_id_code_unique` (`tenant_id`,`code`),
  KEY `currencies_tenant_id_is_active_index` (`tenant_id`,`is_active`),
  CONSTRAINT `currencies_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `currencies`
--

LOCK TABLES `currencies` WRITE;
/*!40000 ALTER TABLE `currencies` DISABLE KEYS */;
/*!40000 ALTER TABLE `currencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `custodies`
--

DROP TABLE IF EXISTS `custodies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `custodies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `employee_id` bigint unsigned NOT NULL,
  `treasury_id` bigint unsigned DEFAULT NULL,
  `currency_id` bigint unsigned DEFAULT NULL,
  `account_id` bigint unsigned DEFAULT NULL,
  `custody_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `returned_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `date` date NOT NULL,
  `settlement_date` date DEFAULT NULL,
  `status` enum('active','settled','partial') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `description` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `custodies_custody_number_unique` (`custody_number`),
  KEY `custodies_tenant_id_foreign` (`tenant_id`),
  KEY `custodies_employee_id_foreign` (`employee_id`),
  KEY `custodies_treasury_id_foreign` (`treasury_id`),
  KEY `custodies_currency_id_foreign` (`currency_id`),
  KEY `custodies_user_id_foreign` (`user_id`),
  KEY `custodies_account_id_foreign` (`account_id`),
  CONSTRAINT `custodies_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `custodies_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `custodies_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `custodies_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `custodies_treasury_id_foreign` FOREIGN KEY (`treasury_id`) REFERENCES `cash_treasuries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `custodies_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `custodies`
--

LOCK TABLES `custodies` WRITE;
/*!40000 ALTER TABLE `custodies` DISABLE KEYS */;
/*!40000 ALTER TABLE `custodies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `account_id` bigint unsigned DEFAULT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `address_en` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `commercial_registration` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credit_limit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `classification` enum('a','b','c','d') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'a',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_tenant_id_code_unique` (`tenant_id`,`code`),
  KEY `customers_account_id_foreign` (`account_id`),
  KEY `customers_tenant_id_name_index` (`tenant_id`,`name`),
  KEY `customers_tenant_id_is_active_index` (`tenant_id`,`is_active`),
  CONSTRAINT `customers_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `customers_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `manager_id` bigint unsigned DEFAULT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `departments_code_unique` (`code`),
  KEY `departments_tenant_id_foreign` (`tenant_id`),
  KEY `departments_manager_id_foreign` (`manager_id`),
  KEY `departments_parent_id_foreign` (`parent_id`),
  CONSTRAINT `departments_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `departments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  CONSTRAINT `departments_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `employee_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'EMP-001',
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` enum('male','female') COLLATE utf8mb4_unicode_ci NOT NULL,
  `birth_date` date DEFAULT NULL,
  `national_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `job_position_id` bigint unsigned NOT NULL,
  `hire_date` date NOT NULL,
  `contract_end_date` date DEFAULT NULL,
  `employment_status` enum('active','inactive','terminated','on_leave') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `gross_salary` decimal(15,2) NOT NULL,
  `bank_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_account` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint unsigned DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_employee_id_unique` (`employee_id`),
  KEY `employees_tenant_id_foreign` (`tenant_id`),
  KEY `employees_job_position_id_foreign` (`job_position_id`),
  KEY `employees_user_id_foreign` (`user_id`),
  CONSTRAINT `employees_job_position_id_foreign` FOREIGN KEY (`job_position_id`) REFERENCES `job_positions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employees_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `employee_id` bigint unsigned NOT NULL,
  `expense_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `category` enum('travel','accommodation','meals','transport','office','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `receipt_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','submitted','approved','rejected','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `expenses_expense_number_unique` (`expense_number`),
  KEY `expenses_tenant_id_foreign` (`tenant_id`),
  KEY `expenses_employee_id_foreign` (`employee_id`),
  KEY `expenses_approved_by_foreign` (`approved_by`),
  CONSTRAINT `expenses_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `expenses_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `expenses_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fiscal_years`
--

DROP TABLE IF EXISTS `fiscal_years`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fiscal_years` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_closed` tinyint(1) NOT NULL DEFAULT '0',
  `closed_by` bigint unsigned DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fiscal_years_tenant_id_name_unique` (`tenant_id`,`name`),
  KEY `fiscal_years_closed_by_foreign` (`closed_by`),
  KEY `fiscal_years_tenant_id_is_active_index` (`tenant_id`,`is_active`),
  KEY `fiscal_years_tenant_id_is_closed_index` (`tenant_id`,`is_closed`),
  CONSTRAINT `fiscal_years_closed_by_foreign` FOREIGN KEY (`closed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fiscal_years_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fiscal_years`
--

LOCK TABLES `fiscal_years` WRITE;
/*!40000 ALTER TABLE `fiscal_years` DISABLE KEYS */;
/*!40000 ALTER TABLE `fiscal_years` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_adjustment_lines`
--

DROP TABLE IF EXISTS `inventory_adjustment_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_adjustment_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `inventory_adjustment_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `theoretical_qty` decimal(15,2) NOT NULL DEFAULT '0.00',
  `actual_qty` decimal(15,2) NOT NULL DEFAULT '0.00',
  `difference` decimal(15,2) NOT NULL DEFAULT '0.00',
  `unit_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventory_adjustment_lines_inventory_adjustment_id_foreign` (`inventory_adjustment_id`),
  KEY `inventory_adjustment_lines_item_id_foreign` (`item_id`),
  KEY `ial_tenant_adj_index` (`tenant_id`,`inventory_adjustment_id`),
  CONSTRAINT `inventory_adjustment_lines_inventory_adjustment_id_foreign` FOREIGN KEY (`inventory_adjustment_id`) REFERENCES `inventory_adjustments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_adjustment_lines_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_adjustment_lines_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_adjustment_lines`
--

LOCK TABLES `inventory_adjustment_lines` WRITE;
/*!40000 ALTER TABLE `inventory_adjustment_lines` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_adjustment_lines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_adjustments`
--

DROP TABLE IF EXISTS `inventory_adjustments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_adjustments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `state` enum('draft','done','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `user_id` bigint unsigned NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventory_adjustments_reference_unique` (`reference`),
  KEY `inventory_adjustments_warehouse_id_foreign` (`warehouse_id`),
  KEY `inventory_adjustments_user_id_foreign` (`user_id`),
  KEY `ia_tenant_state_index` (`tenant_id`,`state`),
  CONSTRAINT `inventory_adjustments_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_adjustments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_adjustments_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_adjustments`
--

LOCK TABLES `inventory_adjustments` WRITE;
/*!40000 ALTER TABLE `inventory_adjustments` DISABLE KEYS */;
/*!40000 ALTER TABLE `inventory_adjustments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_templates`
--

DROP TABLE IF EXISTS `invoice_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('sales','purchase','receipt','quotation','return') COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `paper_size` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A4',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_templates_tenant_id_type_index` (`tenant_id`,`type`),
  KEY `invoice_templates_tenant_id_is_active_index` (`tenant_id`,`is_active`),
  CONSTRAINT `invoice_templates_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_templates`
--

LOCK TABLES `invoice_templates` WRITE;
/*!40000 ALTER TABLE `invoice_templates` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice_templates` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_categories`
--

DROP TABLE IF EXISTS `item_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_categories_parent_id_foreign` (`parent_id`),
  KEY `item_categories_tenant_id_parent_id_index` (`tenant_id`,`parent_id`),
  KEY `item_categories_tenant_id_is_active_index` (`tenant_id`,`is_active`),
  CONSTRAINT `item_categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `item_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `item_categories_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_categories`
--

LOCK TABLES `item_categories` WRITE;
/*!40000 ALTER TABLE `item_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `item_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_units`
--

DROP TABLE IF EXISTS `item_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_units` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `symbol` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `conversion_factor` decimal(15,4) NOT NULL DEFAULT '1.0000',
  `base_unit_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_units_tenant_id_is_active_index` (`tenant_id`,`is_active`),
  KEY `item_units_base_unit_id_foreign` (`base_unit_id`),
  CONSTRAINT `item_units_base_unit_id_foreign` FOREIGN KEY (`base_unit_id`) REFERENCES `item_units` (`id`) ON DELETE SET NULL,
  CONSTRAINT `item_units_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_units`
--

LOCK TABLES `item_units` WRITE;
/*!40000 ALTER TABLE `item_units` DISABLE KEYS */;
/*!40000 ALTER TABLE `item_units` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_warehouses`
--

DROP TABLE IF EXISTS `item_warehouses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_warehouses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `quantity` decimal(15,2) NOT NULL DEFAULT '0.00',
  `reserved_quantity` decimal(15,2) NOT NULL DEFAULT '0.00',
  `average_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `item_warehouses_tenant_id_item_id_warehouse_id_unique` (`tenant_id`,`item_id`,`warehouse_id`),
  KEY `item_warehouses_item_id_foreign` (`item_id`),
  KEY `item_warehouses_warehouse_id_foreign` (`warehouse_id`),
  KEY `item_warehouses_tenant_id_item_id_index` (`tenant_id`,`item_id`),
  KEY `item_warehouses_tenant_id_warehouse_id_index` (`tenant_id`,`warehouse_id`),
  CONSTRAINT `item_warehouses_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `item_warehouses_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `item_warehouses_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_warehouses`
--

LOCK TABLES `item_warehouses` WRITE;
/*!40000 ALTER TABLE `item_warehouses` DISABLE KEYS */;
/*!40000 ALTER TABLE `item_warehouses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `unit_id` bigint unsigned DEFAULT NULL,
  `sales_account_id` bigint unsigned DEFAULT NULL,
  `purchase_account_id` bigint unsigned DEFAULT NULL,
  `inventory_account_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cost_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `purchase_currency_id` bigint unsigned DEFAULT NULL,
  `type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'product',
  `purchase_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `selling_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `sales_currency_id` bigint unsigned DEFAULT NULL,
  `min_stock` decimal(15,2) NOT NULL DEFAULT '0.00',
  `max_stock` decimal(15,2) NOT NULL DEFAULT '0.00',
  `reorder_level` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `minimum_stock` decimal(15,2) NOT NULL DEFAULT '0.00',
  `maximum_stock` decimal(15,2) NOT NULL DEFAULT '0.00',
  `has_serial` tinyint(1) NOT NULL DEFAULT '0',
  `has_batch` tinyint(1) NOT NULL DEFAULT '0',
  `has_expiry` tinyint(1) NOT NULL DEFAULT '0',
  `has_serial_numbers` tinyint(1) NOT NULL DEFAULT '0',
  `has_expiry_date` tinyint(1) NOT NULL DEFAULT '0',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `opening_stock` decimal(15,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `items_tenant_id_sku_unique` (`tenant_id`,`sku`),
  UNIQUE KEY `items_tenant_id_barcode_unique` (`tenant_id`,`barcode`),
  KEY `items_category_id_foreign` (`category_id`),
  KEY `items_unit_id_foreign` (`unit_id`),
  KEY `items_sales_account_id_foreign` (`sales_account_id`),
  KEY `items_purchase_account_id_foreign` (`purchase_account_id`),
  KEY `items_inventory_account_id_foreign` (`inventory_account_id`),
  KEY `items_tenant_id_category_id_index` (`tenant_id`,`category_id`),
  KEY `items_tenant_id_is_active_index` (`tenant_id`,`is_active`),
  KEY `items_purchase_currency_id_foreign` (`purchase_currency_id`),
  KEY `items_sales_currency_id_foreign` (`sales_currency_id`),
  CONSTRAINT `items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `item_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `items_inventory_account_id_foreign` FOREIGN KEY (`inventory_account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `items_purchase_account_id_foreign` FOREIGN KEY (`purchase_account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `items_purchase_currency_id_foreign` FOREIGN KEY (`purchase_currency_id`) REFERENCES `currencies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `items_sales_account_id_foreign` FOREIGN KEY (`sales_account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `items_sales_currency_id_foreign` FOREIGN KEY (`sales_currency_id`) REFERENCES `currencies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `items_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `items_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `item_units` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_positions`
--

DROP TABLE IF EXISTS `job_positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_positions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_salary` decimal(15,2) DEFAULT NULL,
  `max_salary` decimal(15,2) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `job_positions_code_unique` (`code`),
  KEY `job_positions_tenant_id_foreign` (`tenant_id`),
  CONSTRAINT `job_positions_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_positions`
--

LOCK TABLES `job_positions` WRITE;
/*!40000 ALTER TABLE `job_positions` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_positions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `journal_entries`
--

DROP TABLE IF EXISTS `journal_entries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `journal_entries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `entry_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `journal_id` bigint unsigned DEFAULT NULL,
  `fiscal_year_id` bigint unsigned NOT NULL,
  `is_posted` tinyint(1) NOT NULL DEFAULT '0',
  `posted_by` bigint unsigned DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `is_adjusting` tinyint(1) NOT NULL DEFAULT '0',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `total_debit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_credit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `journal_entries_tenant_id_entry_number_unique` (`tenant_id`,`entry_number`),
  KEY `journal_entries_fiscal_year_id_foreign` (`fiscal_year_id`),
  KEY `journal_entries_posted_by_foreign` (`posted_by`),
  KEY `journal_entries_tenant_id_date_index` (`tenant_id`,`date`),
  KEY `journal_entries_tenant_id_fiscal_year_id_index` (`tenant_id`,`fiscal_year_id`),
  KEY `journal_entries_tenant_id_is_posted_index` (`tenant_id`,`is_posted`),
  KEY `journal_entries_journal_id_foreign` (`journal_id`),
  KEY `journal_entries_created_by_foreign` (`created_by`),
  CONSTRAINT `journal_entries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `journal_entries_fiscal_year_id_foreign` FOREIGN KEY (`fiscal_year_id`) REFERENCES `fiscal_years` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `journal_entries_journal_id_foreign` FOREIGN KEY (`journal_id`) REFERENCES `journals` (`id`) ON DELETE SET NULL,
  CONSTRAINT `journal_entries_posted_by_foreign` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `journal_entries_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `journal_entries`
--

LOCK TABLES `journal_entries` WRITE;
/*!40000 ALTER TABLE `journal_entries` DISABLE KEYS */;
/*!40000 ALTER TABLE `journal_entries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `journal_entry_lines`
--

DROP TABLE IF EXISTS `journal_entry_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `journal_entry_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `journal_entry_id` bigint unsigned NOT NULL,
  `account_id` bigint unsigned NOT NULL,
  `debit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `credit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `journal_entry_lines_journal_entry_id_foreign` (`journal_entry_id`),
  KEY `journal_entry_lines_account_id_foreign` (`account_id`),
  KEY `journal_entry_lines_tenant_id_journal_entry_id_index` (`tenant_id`,`journal_entry_id`),
  KEY `journal_entry_lines_tenant_id_account_id_index` (`tenant_id`,`account_id`),
  CONSTRAINT `journal_entry_lines_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `journal_entry_lines_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `journal_entry_lines_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `journal_entry_lines`
--

LOCK TABLES `journal_entry_lines` WRITE;
/*!40000 ALTER TABLE `journal_entry_lines` DISABLE KEYS */;
/*!40000 ALTER TABLE `journal_entry_lines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `journals`
--

DROP TABLE IF EXISTS `journals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `journals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('sale','purchase','cash','bank','general') COLLATE utf8mb4_unicode_ci NOT NULL,
  `default_account_id` bigint unsigned DEFAULT NULL,
  `currency_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `journals_code_unique` (`code`),
  KEY `journals_tenant_id_foreign` (`tenant_id`),
  KEY `journals_default_account_id_foreign` (`default_account_id`),
  KEY `journals_currency_id_foreign` (`currency_id`),
  CONSTRAINT `journals_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `journals_default_account_id_foreign` FOREIGN KEY (`default_account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `journals_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `journals`
--

LOCK TABLES `journals` WRITE;
/*!40000 ALTER TABLE `journals` DISABLE KEYS */;
/*!40000 ALTER TABLE `journals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `leaves`
--

DROP TABLE IF EXISTS `leaves`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leaves` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `employee_id` bigint unsigned NOT NULL,
  `leave_type` enum('annual','sick','maternity','personal','unpaid','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `total_days` decimal(5,1) NOT NULL,
  `status` enum('draft','pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `leaves_tenant_id_foreign` (`tenant_id`),
  KEY `leaves_employee_id_foreign` (`employee_id`),
  KEY `leaves_approved_by_foreign` (`approved_by`),
  CONSTRAINT `leaves_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `leaves_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `leaves_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `leaves`
--

LOCK TABLES `leaves` WRITE;
/*!40000 ALTER TABLE `leaves` DISABLE KEYS */;
/*!40000 ALTER TABLE `leaves` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `loans`
--

DROP TABLE IF EXISTS `loans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `loans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `employee_id` bigint unsigned NOT NULL,
  `loan_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `monthly_deduction` decimal(15,2) NOT NULL,
  `total_paid` decimal(15,2) NOT NULL DEFAULT '0.00',
  `remaining` decimal(15,2) NOT NULL,
  `status` enum('pending','active','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `start_date` date NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `loans_loan_number_unique` (`loan_number`),
  KEY `loans_tenant_id_foreign` (`tenant_id`),
  KEY `loans_employee_id_foreign` (`employee_id`),
  CONSTRAINT `loans_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `loans_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `loans`
--

LOCK TABLES `loans` WRITE;
/*!40000 ALTER TABLE `loans` DISABLE KEYS */;
/*!40000 ALTER TABLE `loans` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2026_06_17_000001_create_tenants_table',1),(2,'2026_06_17_000002_create_companies_table',1),(3,'2026_06_17_000003_create_users_table',1),(4,'2026_06_17_000004_create_user_roles_table',1),(5,'2026_06_17_000005_create_user_permissions_table',1),(6,'2026_06_17_000006_create_currencies_table',1),(7,'2026_06_17_000007_create_fiscal_years_table',1),(8,'2026_06_17_000008_create_chart_of_accounts_table',1),(9,'2026_06_17_000009_create_journal_entries_table',1),(10,'2026_06_17_000010_create_journal_entry_lines_table',1),(11,'2026_06_17_000011_create_customers_table',1),(12,'2026_06_17_000012_create_suppliers_table',1),(13,'2026_06_17_000013_create_warehouses_table',1),(14,'2026_06_17_000014_create_item_categories_table',1),(15,'2026_06_17_000015_create_item_units_table',1),(16,'2026_06_17_000016_create_items_table',1),(17,'2026_06_17_000017_create_item_warehouses_table',1),(18,'2026_06_17_000018_create_stock_movements_table',1),(19,'2026_06_17_000019_create_sales_invoices_table',1),(20,'2026_06_17_000020_create_sales_invoice_lines_table',1),(21,'2026_06_17_000021_create_purchase_invoices_table',1),(22,'2026_06_17_000022_create_purchase_invoice_lines_table',1),(23,'2026_06_17_000023_create_sales_returns_table',1),(24,'2026_06_17_000024_create_sales_return_lines_table',1),(25,'2026_06_17_000025_create_purchase_returns_table',1),(26,'2026_06_17_000026_create_purchase_return_lines_table',1),(27,'2026_06_17_000027_create_quotations_table',1),(28,'2026_06_17_000028_create_quotation_lines_table',1),(29,'2026_06_17_000029_create_purchase_orders_table',1),(30,'2026_06_17_000030_create_purchase_order_lines_table',1),(31,'2026_06_17_000031_create_cash_treasuries_table',1),(32,'2026_06_17_000032_create_bank_accounts_table',1),(33,'2026_06_17_000033_create_treasury_transactions_table',1),(34,'2026_06_17_000034_create_bank_transactions_table',1),(35,'2026_06_17_000035_create_payments_table',1),(36,'2026_06_17_000036_create_tax_settings_table',1),(37,'2026_06_17_000037_create_invoice_templates_table',1),(38,'2026_06_17_000038_create_backup_logs_table',1),(39,'2026_06_17_000039_create_audit_logs_table',1),(40,'2026_06_17_000040_create_settings_table',1),(41,'2026_06_17_080936_create_sessions_table',1),(42,'2026_06_17_080953_create_cache_table',1),(43,'2026_06_17_080953_create_failed_jobs_table',1),(44,'2026_06_17_080953_create_jobs_table',1),(45,'2026_06_17_090004_add_deleted_at_to_audit_logs_and_backup_logs_table',1),(46,'2026_06_17_090752_add_is_header_to_chart_of_accounts_add_is_posted_to_journal_entries_add_quotation_number_to_quotations',1),(47,'2026_06_17_092404_add_deleted_at_to_stock_movements',1),(48,'2026_06_17_093256_create_journals_table',1),(49,'2026_06_17_093300_create_payment_terms_table',1),(50,'2026_06_17_093300_create_taxes_table',1),(51,'2026_06_17_093301_create_bank_statements_table',1),(52,'2026_06_17_093302_create_bank_statement_lines_table',1),(53,'2026_06_17_093303_create_analytical_accounts_table',1),(54,'2026_06_17_093303_create_budgets_table',1),(55,'2026_06_17_093304_create_budget_lines_table',1),(56,'2026_06_17_094714_create_sales_orders_table',1),(57,'2026_06_17_094717_create_sales_order_lines_table',1),(58,'2026_06_17_095853_add_converted_order_id_to_quotations',1),(59,'2026_06_17_101754_create_product_variants_table',1),(60,'2026_06_17_101755_create_inventory_adjustments_table',1),(61,'2026_06_17_101756_create_inventory_adjustment_lines_table',1),(62,'2026_06_17_101756_create_stock_transfers_table',1),(63,'2026_06_17_101757_create_reordering_rules_table',1),(64,'2026_06_17_101757_create_stock_transfer_lines_table',1),(65,'2026_06_17_101758_create_product_lots_table',1),(66,'2026_06_17_101812_create_departments_table',1),(67,'2026_06_17_101813_create_job_positions_table',1),(68,'2026_06_17_101814_create_employees_table',1),(69,'2026_06_17_101815_create_contracts_table',1),(70,'2026_06_17_101816_create_attendance_table',1),(71,'2026_06_17_101817_create_leaves_table',1),(72,'2026_06_17_101818_create_expenses_table',1),(73,'2026_06_17_101819_create_payroll_table',1),(74,'2026_06_17_101820_create_payslip_table',1),(75,'2026_06_17_101821_create_loans_table',1),(76,'2026_06_17_111014_add_secondary_currency_to_companies_table',1),(77,'2026_06_17_111015_add_display_currency_to_settings_table',1),(78,'2026_06_18_000001_create_sales_delivery_notes_table',1),(79,'2026_06_18_000002_create_sales_delivery_note_lines_table',1),(80,'2026_06_18_000003_create_purchase_receipt_notes_table',1),(81,'2026_06_18_000004_create_purchase_receipt_note_lines_table',1),(82,'2026_06_18_000005_fix_missing_columns',1),(83,'2026_06_18_000006_fix_more_columns',1),(84,'2026_06_18_000007_fix_all_missing_columns',1),(85,'2026_06_18_000008_fix_items_columns',1),(86,'2026_06_18_000009_add_item_currencies',1),(87,'2026_06_18_000010_add_opening_stock_to_items',1),(88,'2026_06_18_000011_fix_purchase_order_lines_columns',1),(89,'2026_06_18_000012_add_sales_order_id_to_sales_invoices',1),(90,'2026_06_18_000013_fix_quotation_lines_columns',1),(91,'2026_06_18_000014_add_account_id_to_payments',1),(92,'2026_06_18_000015_add_whatsapp_number_to_cash_treasuries',1),(93,'2026_06_18_154836_add_whatsapp_message_to_cash_treasuries',1),(94,'2026_06_18_161420_create_custodies_table',1),(95,'2026_06_20_082051_drop_department_id_from_job_positions',2),(96,'2026_06_20_082703_drop_department_id_from_employees',3),(97,'2026_06_20_083042_add_settlement_date_to_custodies',4),(98,'2026_06_20_085003_add_account_id_to_custodies',5),(99,'2026_06_20_093614_create_tenant_user_table',6),(101,'2026_06_21_095438_create_permission_role_table',7);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_terms`
--

DROP TABLE IF EXISTS `payment_terms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_terms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_type` enum('fixed','percentage') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `days_net` int NOT NULL DEFAULT '0',
  `days_discount` int DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `company_id` bigint unsigned DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_terms_tenant_id_foreign` (`tenant_id`),
  KEY `payment_terms_company_id_foreign` (`company_id`),
  CONSTRAINT `payment_terms_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payment_terms_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_terms`
--

LOCK TABLES `payment_terms` WRITE;
/*!40000 ALTER TABLE `payment_terms` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_terms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `payment_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `type` enum('receipt','payment') COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  `supplier_id` bigint unsigned DEFAULT NULL,
  `account_id` bigint unsigned DEFAULT NULL,
  `treasury_id` bigint unsigned DEFAULT NULL,
  `bank_account_id` bigint unsigned DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_method` enum('cash','bank_transfer','check','card','online') COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency_id` bigint unsigned DEFAULT NULL,
  `exchange_rate` decimal(15,6) NOT NULL DEFAULT '1.000000',
  `amount_in_currency` decimal(15,2) DEFAULT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `check_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('draft','completed','voided') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_tenant_id_payment_number_unique` (`tenant_id`,`payment_number`),
  KEY `payments_customer_id_foreign` (`customer_id`),
  KEY `payments_supplier_id_foreign` (`supplier_id`),
  KEY `payments_treasury_id_foreign` (`treasury_id`),
  KEY `payments_bank_account_id_foreign` (`bank_account_id`),
  KEY `payments_currency_id_foreign` (`currency_id`),
  KEY `payments_user_id_foreign` (`user_id`),
  KEY `payments_tenant_id_date_index` (`tenant_id`,`date`),
  KEY `payments_tenant_id_type_index` (`tenant_id`,`type`),
  KEY `payments_tenant_id_customer_id_index` (`tenant_id`,`customer_id`),
  KEY `payments_tenant_id_supplier_id_index` (`tenant_id`,`supplier_id`),
  KEY `payments_tenant_id_status_index` (`tenant_id`,`status`),
  KEY `payments_account_id_foreign` (`account_id`),
  CONSTRAINT `payments_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `payments_bank_account_id_foreign` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_accounts` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `payments_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `payments_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `payments_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_treasury_id_foreign` FOREIGN KEY (`treasury_id`) REFERENCES `cash_treasuries` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll`
--

DROP TABLE IF EXISTS `payroll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payroll` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `payroll_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `month` int NOT NULL,
  `year` int NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `state` enum('draft','computed','confirmed','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `total_basic` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_allowances` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_deductions` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_net` decimal(15,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payroll_payroll_number_unique` (`payroll_number`),
  KEY `payroll_tenant_id_foreign` (`tenant_id`),
  CONSTRAINT `payroll_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll`
--

LOCK TABLES `payroll` WRITE;
/*!40000 ALTER TABLE `payroll` DISABLE KEYS */;
/*!40000 ALTER TABLE `payroll` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payslip`
--

DROP TABLE IF EXISTS `payslip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payslip` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `payroll_id` bigint unsigned NOT NULL,
  `employee_id` bigint unsigned NOT NULL,
  `basic_salary` decimal(15,2) NOT NULL,
  `allowances` json DEFAULT NULL,
  `total_allowances` decimal(15,2) NOT NULL DEFAULT '0.00',
  `deductions` json DEFAULT NULL,
  `total_deductions` decimal(15,2) NOT NULL DEFAULT '0.00',
  `overtime_pay` decimal(15,2) NOT NULL DEFAULT '0.00',
  `net_salary` decimal(15,2) NOT NULL,
  `status` enum('draft','confirmed','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `paid_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payslip_tenant_id_foreign` (`tenant_id`),
  KEY `payslip_payroll_id_foreign` (`payroll_id`),
  KEY `payslip_employee_id_foreign` (`employee_id`),
  CONSTRAINT `payslip_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payslip_payroll_id_foreign` FOREIGN KEY (`payroll_id`) REFERENCES `payroll` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payslip_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payslip`
--

LOCK TABLES `payslip` WRITE;
/*!40000 ALTER TABLE `payslip` DISABLE KEYS */;
/*!40000 ALTER TABLE `payslip` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permission_role`
--

DROP TABLE IF EXISTS `permission_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permission_role` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint unsigned NOT NULL,
  `permission_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permission_role_role_id_permission_id_unique` (`role_id`,`permission_id`),
  KEY `permission_role_permission_id_foreign` (`permission_id`),
  CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `user_permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `user_roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=330 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permission_role`
--

LOCK TABLES `permission_role` WRITE;
/*!40000 ALTER TABLE `permission_role` DISABLE KEYS */;
INSERT INTO `permission_role` VALUES (1,1,1,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(2,1,2,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(3,1,3,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(4,1,4,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(5,1,5,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(6,1,6,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(7,1,7,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(8,1,8,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(9,1,9,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(10,1,10,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(11,1,11,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(12,1,12,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(13,1,13,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(14,1,14,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(15,1,15,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(16,1,16,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(17,1,17,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(18,1,18,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(19,1,19,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(20,1,20,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(21,1,21,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(22,1,22,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(23,1,23,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(24,1,24,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(25,1,25,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(26,1,26,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(27,1,27,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(28,1,28,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(29,1,29,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(30,1,30,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(31,1,31,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(32,1,32,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(33,1,33,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(34,1,34,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(35,1,35,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(36,1,36,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(37,1,37,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(38,1,38,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(39,1,39,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(40,1,40,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(41,1,41,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(42,1,42,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(43,1,43,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(44,1,44,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(45,1,45,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(46,1,46,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(47,1,47,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(48,1,48,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(49,1,49,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(50,1,50,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(51,1,51,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(52,1,52,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(53,1,53,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(54,1,54,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(55,1,55,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(56,1,56,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(57,1,57,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(58,1,58,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(59,1,59,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(60,1,60,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(61,1,61,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(62,1,62,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(63,1,63,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(64,1,64,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(65,1,65,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(66,1,66,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(67,1,67,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(68,1,68,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(69,1,69,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(70,1,70,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(71,1,71,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(72,1,72,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(73,1,73,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(74,1,74,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(75,1,75,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(76,1,76,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(77,1,77,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(78,1,78,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(79,1,79,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(80,1,80,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(81,1,81,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(82,1,82,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(83,1,83,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(84,1,84,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(85,1,85,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(86,1,86,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(87,1,87,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(88,1,88,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(89,1,89,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(90,1,90,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(91,1,91,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(92,1,92,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(93,2,1,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(94,2,2,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(95,2,3,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(96,2,4,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(97,2,5,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(98,2,6,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(99,2,7,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(100,2,8,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(101,2,9,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(102,2,10,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(103,2,11,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(104,2,12,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(105,2,13,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(106,2,14,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(107,2,15,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(108,2,16,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(109,2,17,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(110,2,18,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(111,2,19,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(112,2,20,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(113,2,21,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(114,2,22,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(115,2,23,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(116,2,24,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(117,2,25,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(118,2,26,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(119,2,27,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(120,2,28,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(121,2,29,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(122,2,30,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(123,2,31,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(124,2,32,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(125,2,33,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(126,2,34,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(127,2,35,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(128,2,36,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(129,2,37,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(130,2,38,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(131,2,39,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(132,2,40,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(133,2,41,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(134,2,42,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(135,2,43,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(136,2,44,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(137,2,45,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(138,2,46,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(139,2,47,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(140,2,48,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(141,2,49,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(142,2,50,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(143,2,51,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(144,2,52,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(145,2,53,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(146,2,54,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(147,2,55,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(148,2,56,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(149,2,57,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(150,2,58,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(151,2,59,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(152,2,60,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(153,2,61,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(154,2,62,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(155,2,63,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(156,2,64,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(157,2,65,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(158,2,66,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(159,2,67,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(160,2,68,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(161,2,69,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(162,2,70,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(163,2,71,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(164,2,72,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(165,2,73,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(166,2,74,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(167,2,75,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(168,2,76,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(169,2,77,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(170,2,78,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(171,2,79,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(172,2,80,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(173,2,81,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(174,2,82,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(175,2,83,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(176,2,84,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(177,2,85,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(178,2,86,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(179,2,87,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(180,2,88,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(181,2,89,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(182,2,90,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(183,2,91,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(184,2,92,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(185,3,1,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(186,3,2,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(187,3,3,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(188,3,5,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(189,3,9,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(190,3,17,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(191,3,18,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(192,3,19,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(193,3,21,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(194,3,22,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(195,3,23,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(196,3,24,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(197,3,26,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(198,3,52,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(199,3,53,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(200,3,56,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(201,3,57,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(202,3,58,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(203,3,72,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(204,3,73,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(205,3,74,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(206,3,75,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(207,3,76,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(208,3,65,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(209,3,66,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(210,3,68,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(211,3,69,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(212,3,70,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(213,3,87,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(214,3,88,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(215,3,89,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(216,4,13,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(217,4,14,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(218,4,15,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(219,4,60,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(220,4,61,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(221,4,62,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(222,4,63,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(223,4,64,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(224,4,46,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(225,4,47,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(226,4,49,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(227,4,50,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(228,4,27,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(229,4,31,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(230,5,5,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(231,5,6,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(233,5,13,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(234,5,17,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(235,5,18,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(236,5,27,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(237,5,28,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(238,5,29,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(239,5,35,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(241,5,41,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(242,5,42,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(243,5,43,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(244,5,45,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(245,5,46,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(246,5,47,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(247,5,87,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(248,6,9,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(249,6,10,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(250,6,11,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(251,6,13,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(252,6,22,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(253,6,23,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(254,6,31,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(255,6,32,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(256,6,33,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(257,6,38,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(258,6,39,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(259,6,49,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(260,6,50,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(261,6,87,'2026-06-21 06:56:08','2026-06-21 06:56:08'),(263,1,93,'2026-06-21 07:30:01','2026-06-21 07:30:01'),(264,2,93,'2026-06-21 07:30:01','2026-06-21 07:30:01'),(265,3,93,'2026-06-21 07:30:01','2026-06-21 07:30:01'),(266,4,93,'2026-06-21 07:30:01','2026-06-21 07:30:01'),(267,5,7,'2026-06-21 07:30:01','2026-06-21 07:30:01'),(268,5,36,'2026-06-21 07:30:01','2026-06-21 07:30:01'),(269,3,4,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(270,3,6,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(271,3,7,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(272,3,8,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(273,3,10,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(274,3,11,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(275,3,12,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(276,3,13,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(277,3,14,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(278,3,15,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(279,3,16,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(280,3,20,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(281,3,25,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(282,3,27,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(283,3,28,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(284,3,29,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(285,3,30,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(286,3,31,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(287,3,32,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(288,3,33,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(289,3,34,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(290,3,35,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(291,3,36,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(292,3,37,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(293,3,38,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(294,3,39,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(295,3,40,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(296,3,41,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(297,3,42,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(298,3,43,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(299,3,44,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(300,3,45,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(301,3,46,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(302,3,47,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(303,3,48,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(304,3,49,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(305,3,50,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(306,3,51,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(307,3,54,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(308,3,55,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(309,3,59,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(310,3,60,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(311,3,61,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(312,3,62,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(313,3,63,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(314,3,64,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(315,3,67,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(316,3,71,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(317,3,77,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(318,3,78,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(319,3,79,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(320,3,80,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(321,3,81,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(322,3,82,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(323,3,83,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(324,3,84,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(325,3,85,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(326,3,86,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(327,3,90,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(328,3,91,'2026-06-21 07:53:06','2026-06-21 07:53:06'),(329,3,92,'2026-06-21 07:53:06','2026-06-21 07:53:06');
/*!40000 ALTER TABLE `permission_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_lots`
--

DROP TABLE IF EXISTS `product_lots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_lots` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `lot_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(15,2) NOT NULL DEFAULT '0.00',
  `remaining_qty` decimal(15,2) NOT NULL DEFAULT '0.00',
  `expiration_date` date DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_lots_item_id_foreign` (`item_id`),
  KEY `pl_tenant_item_index` (`tenant_id`,`item_id`),
  CONSTRAINT `product_lots_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_lots_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_lots`
--

LOCK TABLES `product_lots` WRITE;
/*!40000 ALTER TABLE `product_lots` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_lots` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_variants`
--

DROP TABLE IF EXISTS `product_variants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_variants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attribute_values` json DEFAULT NULL,
  `cost_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `selling_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_variants_item_id_foreign` (`item_id`),
  KEY `pv_tenant_item_index` (`tenant_id`,`item_id`),
  CONSTRAINT `product_variants_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_variants_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_variants`
--

LOCK TABLES `product_variants` WRITE;
/*!40000 ALTER TABLE `product_variants` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_variants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_invoice_lines`
--

DROP TABLE IF EXISTS `purchase_invoice_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_invoice_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `purchase_invoice_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `quantity` decimal(15,2) NOT NULL DEFAULT '0.00',
  `unit_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `unit_cost` decimal(15,2) NOT NULL,
  `discount_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(15,2) NOT NULL,
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `warehouse_id` bigint unsigned DEFAULT NULL,
  `serial_numbers` json DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_invoice_lines_purchase_invoice_id_foreign` (`purchase_invoice_id`),
  KEY `purchase_invoice_lines_item_id_foreign` (`item_id`),
  KEY `purchase_invoice_lines_tenant_id_purchase_invoice_id_index` (`tenant_id`,`purchase_invoice_id`),
  KEY `purchase_invoice_lines_tenant_id_item_id_index` (`tenant_id`,`item_id`),
  KEY `purchase_invoice_lines_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `purchase_invoice_lines_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `purchase_invoice_lines_purchase_invoice_id_foreign` FOREIGN KEY (`purchase_invoice_id`) REFERENCES `purchase_invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_invoice_lines_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_invoice_lines_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_invoice_lines`
--

LOCK TABLES `purchase_invoice_lines` WRITE;
/*!40000 ALTER TABLE `purchase_invoice_lines` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_invoice_lines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_invoices`
--

DROP TABLE IF EXISTS `purchase_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `invoice_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `received_by` bigint unsigned DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `discount_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_percent` decimal(5,2) NOT NULL DEFAULT '15.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `shipping_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `due_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('draft','posted','paid','partial','voided') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `currency_id` bigint unsigned DEFAULT NULL,
  `exchange_rate` decimal(15,6) NOT NULL DEFAULT '1.000000',
  `supplier_invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `terms` text COLLATE utf8mb4_unicode_ci,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_invoices_tenant_id_invoice_number_unique` (`tenant_id`,`invoice_number`),
  KEY `purchase_invoices_supplier_id_foreign` (`supplier_id`),
  KEY `purchase_invoices_warehouse_id_foreign` (`warehouse_id`),
  KEY `purchase_invoices_received_by_foreign` (`received_by`),
  KEY `purchase_invoices_currency_id_foreign` (`currency_id`),
  KEY `purchase_invoices_tenant_id_date_index` (`tenant_id`,`date`),
  KEY `purchase_invoices_tenant_id_supplier_id_index` (`tenant_id`,`supplier_id`),
  KEY `purchase_invoices_tenant_id_status_index` (`tenant_id`,`status`),
  KEY `purchase_invoices_tenant_id_payment_status_index` (`tenant_id`,`payment_status`),
  CONSTRAINT `purchase_invoices_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_invoices_received_by_foreign` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_invoices_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `purchase_invoices_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_invoices_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_invoices`
--

LOCK TABLES `purchase_invoices` WRITE;
/*!40000 ALTER TABLE `purchase_invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_order_lines`
--

DROP TABLE IF EXISTS `purchase_order_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_order_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `purchase_order_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `quantity` decimal(15,2) NOT NULL DEFAULT '0.00',
  `received_quantity` decimal(15,2) NOT NULL DEFAULT '0.00',
  `unit_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `discount_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_order_lines_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `purchase_order_lines_item_id_foreign` (`item_id`),
  KEY `purchase_order_lines_tenant_id_purchase_order_id_index` (`tenant_id`,`purchase_order_id`),
  KEY `purchase_order_lines_tenant_id_item_id_index` (`tenant_id`,`item_id`),
  CONSTRAINT `purchase_order_lines_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `purchase_order_lines_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_order_lines_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_order_lines`
--

LOCK TABLES `purchase_order_lines` WRITE;
/*!40000 ALTER TABLE `purchase_order_lines` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_order_lines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_orders`
--

DROP TABLE IF EXISTS `purchase_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `order_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `expected_date` date DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `discount_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_percent` decimal(5,2) NOT NULL DEFAULT '15.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `shipping_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `currency_id` bigint unsigned DEFAULT NULL,
  `exchange_rate` decimal(15,6) NOT NULL DEFAULT '1.000000',
  `payment_term_id` bigint unsigned DEFAULT NULL,
  `status` enum('draft','sent','confirmed','received','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `receipt_status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `supplier_invoice_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancelled_reason` text COLLATE utf8mb4_unicode_ci,
  `warehouse_id` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `terms` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_orders_tenant_id_order_number_unique` (`tenant_id`,`order_number`),
  KEY `purchase_orders_supplier_id_foreign` (`supplier_id`),
  KEY `purchase_orders_warehouse_id_foreign` (`warehouse_id`),
  KEY `purchase_orders_user_id_foreign` (`user_id`),
  KEY `purchase_orders_tenant_id_date_index` (`tenant_id`,`date`),
  KEY `purchase_orders_tenant_id_supplier_id_index` (`tenant_id`,`supplier_id`),
  KEY `purchase_orders_tenant_id_status_index` (`tenant_id`,`status`),
  KEY `purchase_orders_currency_id_foreign` (`currency_id`),
  KEY `purchase_orders_payment_term_id_foreign` (`payment_term_id`),
  CONSTRAINT `purchase_orders_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_orders_payment_term_id_foreign` FOREIGN KEY (`payment_term_id`) REFERENCES `payment_terms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_orders_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `purchase_orders_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_orders_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_orders`
--

LOCK TABLES `purchase_orders` WRITE;
/*!40000 ALTER TABLE `purchase_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_receipt_note_lines`
--

DROP TABLE IF EXISTS `purchase_receipt_note_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_receipt_note_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `purchase_receipt_note_id` bigint unsigned NOT NULL,
  `purchase_order_line_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `quantity` decimal(15,2) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_receipt_note_lines_tenant_id_foreign` (`tenant_id`),
  KEY `purchase_receipt_note_lines_purchase_receipt_note_id_foreign` (`purchase_receipt_note_id`),
  KEY `purchase_receipt_note_lines_purchase_order_line_id_foreign` (`purchase_order_line_id`),
  KEY `purchase_receipt_note_lines_item_id_foreign` (`item_id`),
  CONSTRAINT `purchase_receipt_note_lines_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_receipt_note_lines_purchase_order_line_id_foreign` FOREIGN KEY (`purchase_order_line_id`) REFERENCES `purchase_order_lines` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_receipt_note_lines_purchase_receipt_note_id_foreign` FOREIGN KEY (`purchase_receipt_note_id`) REFERENCES `purchase_receipt_notes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_receipt_note_lines_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_receipt_note_lines`
--

LOCK TABLES `purchase_receipt_note_lines` WRITE;
/*!40000 ALTER TABLE `purchase_receipt_note_lines` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_receipt_note_lines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_receipt_notes`
--

DROP TABLE IF EXISTS `purchase_receipt_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_receipt_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `receipt_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `purchase_order_id` bigint unsigned NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `status` enum('draft','confirmed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_receipt_notes_tenant_id_receipt_number_unique` (`tenant_id`,`receipt_number`),
  KEY `purchase_receipt_notes_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `purchase_receipt_notes_supplier_id_foreign` (`supplier_id`),
  KEY `purchase_receipt_notes_warehouse_id_foreign` (`warehouse_id`),
  KEY `purchase_receipt_notes_user_id_foreign` (`user_id`),
  CONSTRAINT `purchase_receipt_notes_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_receipt_notes_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_receipt_notes_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_receipt_notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_receipt_notes_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_receipt_notes`
--

LOCK TABLES `purchase_receipt_notes` WRITE;
/*!40000 ALTER TABLE `purchase_receipt_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_receipt_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_return_lines`
--

DROP TABLE IF EXISTS `purchase_return_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_return_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `purchase_return_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `quantity` decimal(15,2) NOT NULL DEFAULT '0.00',
  `unit_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_return_lines_purchase_return_id_foreign` (`purchase_return_id`),
  KEY `purchase_return_lines_item_id_foreign` (`item_id`),
  KEY `purchase_return_lines_tenant_id_purchase_return_id_index` (`tenant_id`,`purchase_return_id`),
  KEY `purchase_return_lines_tenant_id_item_id_index` (`tenant_id`,`item_id`),
  CONSTRAINT `purchase_return_lines_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `purchase_return_lines_purchase_return_id_foreign` FOREIGN KEY (`purchase_return_id`) REFERENCES `purchase_returns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_return_lines_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_return_lines`
--

LOCK TABLES `purchase_return_lines` WRITE;
/*!40000 ALTER TABLE `purchase_return_lines` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_return_lines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_returns`
--

DROP TABLE IF EXISTS `purchase_returns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_returns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `return_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `original_invoice_id` bigint unsigned DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('draft','posted','voided') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `reason` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_returns_tenant_id_return_number_unique` (`tenant_id`,`return_number`),
  KEY `purchase_returns_supplier_id_foreign` (`supplier_id`),
  KEY `purchase_returns_warehouse_id_foreign` (`warehouse_id`),
  KEY `purchase_returns_original_invoice_id_foreign` (`original_invoice_id`),
  KEY `purchase_returns_user_id_foreign` (`user_id`),
  KEY `purchase_returns_tenant_id_date_index` (`tenant_id`,`date`),
  KEY `purchase_returns_tenant_id_supplier_id_index` (`tenant_id`,`supplier_id`),
  KEY `purchase_returns_tenant_id_status_index` (`tenant_id`,`status`),
  CONSTRAINT `purchase_returns_original_invoice_id_foreign` FOREIGN KEY (`original_invoice_id`) REFERENCES `purchase_invoices` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_returns_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `purchase_returns_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_returns_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_returns_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_returns`
--

LOCK TABLES `purchase_returns` WRITE;
/*!40000 ALTER TABLE `purchase_returns` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_returns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quotation_lines`
--

DROP TABLE IF EXISTS `quotation_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quotation_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `quotation_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `quantity` decimal(15,2) NOT NULL DEFAULT '0.00',
  `unit_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `discount_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quotation_lines_quotation_id_foreign` (`quotation_id`),
  KEY `quotation_lines_item_id_foreign` (`item_id`),
  KEY `quotation_lines_tenant_id_quotation_id_index` (`tenant_id`,`quotation_id`),
  KEY `quotation_lines_tenant_id_item_id_index` (`tenant_id`,`item_id`),
  CONSTRAINT `quotation_lines_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `quotation_lines_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quotation_lines_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quotation_lines`
--

LOCK TABLES `quotation_lines` WRITE;
/*!40000 ALTER TABLE `quotation_lines` DISABLE KEYS */;
/*!40000 ALTER TABLE `quotation_lines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quotations`
--

DROP TABLE IF EXISTS `quotations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quotations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `quote_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `valid_until` date DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `discount_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_percent` decimal(5,2) NOT NULL DEFAULT '15.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('draft','sent','accepted','rejected','converted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `converted_invoice_id` bigint unsigned DEFAULT NULL,
  `converted_order_id` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `terms` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `quotations_tenant_id_quote_number_unique` (`tenant_id`,`quote_number`),
  KEY `quotations_customer_id_foreign` (`customer_id`),
  KEY `quotations_converted_invoice_id_foreign` (`converted_invoice_id`),
  KEY `quotations_user_id_foreign` (`user_id`),
  KEY `quotations_tenant_id_date_index` (`tenant_id`,`date`),
  KEY `quotations_tenant_id_customer_id_index` (`tenant_id`,`customer_id`),
  KEY `quotations_tenant_id_status_index` (`tenant_id`,`status`),
  KEY `quotations_converted_order_id_foreign` (`converted_order_id`),
  CONSTRAINT `quotations_converted_invoice_id_foreign` FOREIGN KEY (`converted_invoice_id`) REFERENCES `sales_invoices` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quotations_converted_order_id_foreign` FOREIGN KEY (`converted_order_id`) REFERENCES `sales_orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quotations_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `quotations_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quotations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quotations`
--

LOCK TABLES `quotations` WRITE;
/*!40000 ALTER TABLE `quotations` DISABLE KEYS */;
/*!40000 ALTER TABLE `quotations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reordering_rules`
--

DROP TABLE IF EXISTS `reordering_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reordering_rules` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `product_min_qty` decimal(15,2) NOT NULL DEFAULT '0.00',
  `product_max_qty` decimal(15,2) NOT NULL DEFAULT '0.00',
  `reorder_qty` decimal(15,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reordering_rules_item_id_foreign` (`item_id`),
  KEY `reordering_rules_warehouse_id_foreign` (`warehouse_id`),
  KEY `rr_tenant_item_wh_index` (`tenant_id`,`item_id`,`warehouse_id`),
  CONSTRAINT `reordering_rules_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reordering_rules_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reordering_rules_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reordering_rules`
--

LOCK TABLES `reordering_rules` WRITE;
/*!40000 ALTER TABLE `reordering_rules` DISABLE KEYS */;
/*!40000 ALTER TABLE `reordering_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_delivery_note_lines`
--

DROP TABLE IF EXISTS `sales_delivery_note_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_delivery_note_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `sales_delivery_note_id` bigint unsigned NOT NULL,
  `sales_order_line_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `quantity` decimal(15,2) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_delivery_note_lines_tenant_id_foreign` (`tenant_id`),
  KEY `sales_delivery_note_lines_sales_delivery_note_id_foreign` (`sales_delivery_note_id`),
  KEY `sales_delivery_note_lines_sales_order_line_id_foreign` (`sales_order_line_id`),
  KEY `sales_delivery_note_lines_item_id_foreign` (`item_id`),
  CONSTRAINT `sales_delivery_note_lines_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sales_delivery_note_lines_sales_delivery_note_id_foreign` FOREIGN KEY (`sales_delivery_note_id`) REFERENCES `sales_delivery_notes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sales_delivery_note_lines_sales_order_line_id_foreign` FOREIGN KEY (`sales_order_line_id`) REFERENCES `sales_order_lines` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sales_delivery_note_lines_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_delivery_note_lines`
--

LOCK TABLES `sales_delivery_note_lines` WRITE;
/*!40000 ALTER TABLE `sales_delivery_note_lines` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_delivery_note_lines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_delivery_notes`
--

DROP TABLE IF EXISTS `sales_delivery_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_delivery_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `delivery_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `sales_order_id` bigint unsigned NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `status` enum('draft','confirmed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sales_delivery_notes_tenant_id_delivery_number_unique` (`tenant_id`,`delivery_number`),
  KEY `sales_delivery_notes_sales_order_id_foreign` (`sales_order_id`),
  KEY `sales_delivery_notes_customer_id_foreign` (`customer_id`),
  KEY `sales_delivery_notes_warehouse_id_foreign` (`warehouse_id`),
  KEY `sales_delivery_notes_user_id_foreign` (`user_id`),
  CONSTRAINT `sales_delivery_notes_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sales_delivery_notes_sales_order_id_foreign` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sales_delivery_notes_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sales_delivery_notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sales_delivery_notes_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_delivery_notes`
--

LOCK TABLES `sales_delivery_notes` WRITE;
/*!40000 ALTER TABLE `sales_delivery_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_delivery_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_invoice_lines`
--

DROP TABLE IF EXISTS `sales_invoice_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_invoice_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `sales_invoice_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `quantity` decimal(15,2) NOT NULL DEFAULT '0.00',
  `unit_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `discount_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(15,2) NOT NULL,
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `warehouse_id` bigint unsigned DEFAULT NULL,
  `serial_numbers` json DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_invoice_lines_sales_invoice_id_foreign` (`sales_invoice_id`),
  KEY `sales_invoice_lines_item_id_foreign` (`item_id`),
  KEY `sales_invoice_lines_tenant_id_sales_invoice_id_index` (`tenant_id`,`sales_invoice_id`),
  KEY `sales_invoice_lines_tenant_id_item_id_index` (`tenant_id`,`item_id`),
  KEY `sales_invoice_lines_warehouse_id_foreign` (`warehouse_id`),
  CONSTRAINT `sales_invoice_lines_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `sales_invoice_lines_sales_invoice_id_foreign` FOREIGN KEY (`sales_invoice_id`) REFERENCES `sales_invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sales_invoice_lines_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sales_invoice_lines_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_invoice_lines`
--

LOCK TABLES `sales_invoice_lines` WRITE;
/*!40000 ALTER TABLE `sales_invoice_lines` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_invoice_lines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_invoices`
--

DROP TABLE IF EXISTS `sales_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `invoice_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `sales_order_id` bigint unsigned DEFAULT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `cashier_id` bigint unsigned DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `discount_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_percent` decimal(5,2) NOT NULL DEFAULT '15.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `due_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('draft','posted','paid','partial','voided') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `currency_id` bigint unsigned DEFAULT NULL,
  `exchange_rate` decimal(15,6) NOT NULL DEFAULT '1.000000',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `terms` text COLLATE utf8mb4_unicode_ci,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sales_invoices_tenant_id_invoice_number_unique` (`tenant_id`,`invoice_number`),
  KEY `sales_invoices_customer_id_foreign` (`customer_id`),
  KEY `sales_invoices_warehouse_id_foreign` (`warehouse_id`),
  KEY `sales_invoices_cashier_id_foreign` (`cashier_id`),
  KEY `sales_invoices_currency_id_foreign` (`currency_id`),
  KEY `sales_invoices_tenant_id_date_index` (`tenant_id`,`date`),
  KEY `sales_invoices_tenant_id_customer_id_index` (`tenant_id`,`customer_id`),
  KEY `sales_invoices_tenant_id_status_index` (`tenant_id`,`status`),
  KEY `sales_invoices_tenant_id_payment_status_index` (`tenant_id`,`payment_status`),
  KEY `sales_invoices_sales_order_id_foreign` (`sales_order_id`),
  CONSTRAINT `sales_invoices_cashier_id_foreign` FOREIGN KEY (`cashier_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_invoices_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_invoices_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `sales_invoices_sales_order_id_foreign` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_invoices_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sales_invoices_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_invoices`
--

LOCK TABLES `sales_invoices` WRITE;
/*!40000 ALTER TABLE `sales_invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_order_lines`
--

DROP TABLE IF EXISTS `sales_order_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_order_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `sales_order_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `quantity` decimal(15,2) NOT NULL DEFAULT '0.00',
  `delivered_qty` decimal(15,2) NOT NULL DEFAULT '0.00',
  `unit_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `discount_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_order_lines_sales_order_id_foreign` (`sales_order_id`),
  KEY `sales_order_lines_item_id_foreign` (`item_id`),
  KEY `sales_order_lines_tenant_id_sales_order_id_index` (`tenant_id`,`sales_order_id`),
  KEY `sales_order_lines_tenant_id_item_id_index` (`tenant_id`,`item_id`),
  CONSTRAINT `sales_order_lines_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `sales_order_lines_sales_order_id_foreign` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sales_order_lines_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_order_lines`
--

LOCK TABLES `sales_order_lines` WRITE;
/*!40000 ALTER TABLE `sales_order_lines` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_order_lines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_orders`
--

DROP TABLE IF EXISTS `sales_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `order_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `required_date` date DEFAULT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `discount_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_percent` decimal(5,2) NOT NULL DEFAULT '15.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `currency_id` bigint unsigned DEFAULT NULL,
  `exchange_rate` decimal(15,6) NOT NULL DEFAULT '1.000000',
  `payment_term_id` bigint unsigned DEFAULT NULL,
  `status` enum('draft','confirmed','delivered','invoiced','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `delivery_status` enum('pending','partial','done') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `invoice_status` enum('not_invoiced','partial','invoiced') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'not_invoiced',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `terms` text COLLATE utf8mb4_unicode_ci,
  `reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancelled_reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sales_orders_tenant_id_order_number_unique` (`tenant_id`,`order_number`),
  KEY `sales_orders_customer_id_foreign` (`customer_id`),
  KEY `sales_orders_warehouse_id_foreign` (`warehouse_id`),
  KEY `sales_orders_user_id_foreign` (`user_id`),
  KEY `sales_orders_currency_id_foreign` (`currency_id`),
  KEY `sales_orders_payment_term_id_foreign` (`payment_term_id`),
  KEY `sales_orders_tenant_id_date_index` (`tenant_id`,`date`),
  KEY `sales_orders_tenant_id_customer_id_index` (`tenant_id`,`customer_id`),
  KEY `sales_orders_tenant_id_status_index` (`tenant_id`,`status`),
  CONSTRAINT `sales_orders_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `sales_orders_payment_term_id_foreign` FOREIGN KEY (`payment_term_id`) REFERENCES `payment_terms` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_orders_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sales_orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_orders_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_orders`
--

LOCK TABLES `sales_orders` WRITE;
/*!40000 ALTER TABLE `sales_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_return_lines`
--

DROP TABLE IF EXISTS `sales_return_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_return_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `sales_return_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `quantity` decimal(15,2) NOT NULL DEFAULT '0.00',
  `unit_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `reason` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sales_return_lines_sales_return_id_foreign` (`sales_return_id`),
  KEY `sales_return_lines_item_id_foreign` (`item_id`),
  KEY `sales_return_lines_tenant_id_sales_return_id_index` (`tenant_id`,`sales_return_id`),
  KEY `sales_return_lines_tenant_id_item_id_index` (`tenant_id`,`item_id`),
  CONSTRAINT `sales_return_lines_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `sales_return_lines_sales_return_id_foreign` FOREIGN KEY (`sales_return_id`) REFERENCES `sales_returns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sales_return_lines_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_return_lines`
--

LOCK TABLES `sales_return_lines` WRITE;
/*!40000 ALTER TABLE `sales_return_lines` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_return_lines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales_returns`
--

DROP TABLE IF EXISTS `sales_returns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales_returns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `return_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `customer_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `original_invoice_id` bigint unsigned DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('draft','posted','voided') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `reason` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sales_returns_tenant_id_return_number_unique` (`tenant_id`,`return_number`),
  KEY `sales_returns_customer_id_foreign` (`customer_id`),
  KEY `sales_returns_warehouse_id_foreign` (`warehouse_id`),
  KEY `sales_returns_original_invoice_id_foreign` (`original_invoice_id`),
  KEY `sales_returns_user_id_foreign` (`user_id`),
  KEY `sales_returns_tenant_id_date_index` (`tenant_id`,`date`),
  KEY `sales_returns_tenant_id_customer_id_index` (`tenant_id`,`customer_id`),
  KEY `sales_returns_tenant_id_status_index` (`tenant_id`,`status`),
  CONSTRAINT `sales_returns_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `sales_returns_original_invoice_id_foreign` FOREIGN KEY (`original_invoice_id`) REFERENCES `sales_invoices` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_returns_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sales_returns_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_returns_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales_returns`
--

LOCK TABLES `sales_returns` WRITE;
/*!40000 ALTER TABLE `sales_returns` DISABLE KEYS */;
/*!40000 ALTER TABLE `sales_returns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('B18WWiTiCjhBSOisFW0bBtxFE3PHw8qMOOHDfxiM',NULL,'127.0.0.1','curl/8.19.0','eyJfdG9rZW4iOiI5U3F3R1ZuVGs4TzdjYnk1N1pNVHlic1FFdVBMdDFxdWY0Q3g1NFEzIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1782038098),('EA7H34aeNuwBAUnYIHDwoCxb8vQAR0VmFYQSRSen',2,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJBaDlNN0NQSFI4cmVMMHBMT2pqc3p3c0ozVm53dUQ2am5ZeGYzWFZwIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAiLCJyb3V0ZSI6ImRhc2hib2FyZCJ9LCJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI6MiwiY3VycmVudF90ZW5hbnRfaWQiOjF9',1782039303),('Vbtc2oRhC6yLbaAgOeYw87zyfTJSDIwMpVQBQQZY',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT; Windows NT 10.0; en-GB) WindowsPowerShell/5.1.26100.8655','eyJfdG9rZW4iOiJlWTFKdFZEVTRkRk85OE1kcXZHWDhMdGxHUW9Pc2ZVQ3FBM25wSTFGIiwidXJsIjp7ImludGVuZGVkIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDAifSwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwIiwicm91dGUiOiJkYXNoYm9hcmQifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1782038107);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` longtext COLLATE utf8mb4_unicode_ci,
  `display_currency` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT 'EGP',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_tenant_id_group_key_unique` (`tenant_id`,`group`,`key`),
  KEY `settings_tenant_id_group_index` (`tenant_id`,`group`),
  CONSTRAINT `settings_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_movements`
--

DROP TABLE IF EXISTS `stock_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_movements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `warehouse_id` bigint unsigned NOT NULL,
  `type` enum('purchase','sale','return_in','return_out','transfer_in','transfer_out','adjustment_in','adjustment_out','opening') COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(15,2) NOT NULL,
  `unit_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `reference_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint unsigned DEFAULT NULL,
  `batch_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serial_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_movements_item_id_foreign` (`item_id`),
  KEY `stock_movements_warehouse_id_foreign` (`warehouse_id`),
  KEY `stock_movements_user_id_foreign` (`user_id`),
  KEY `stock_movements_tenant_id_item_id_index` (`tenant_id`,`item_id`),
  KEY `stock_movements_tenant_id_warehouse_id_index` (`tenant_id`,`warehouse_id`),
  KEY `stock_movements_tenant_id_type_index` (`tenant_id`,`type`),
  KEY `stock_mov_ref_index` (`tenant_id`,`reference_type`,`reference_id`),
  CONSTRAINT `stock_movements_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `stock_movements_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_movements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_movements_warehouse_id_foreign` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_movements`
--

LOCK TABLES `stock_movements` WRITE;
/*!40000 ALTER TABLE `stock_movements` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_movements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_transfer_lines`
--

DROP TABLE IF EXISTS `stock_transfer_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_transfer_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `stock_transfer_id` bigint unsigned NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `quantity` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_transfer_lines_stock_transfer_id_foreign` (`stock_transfer_id`),
  KEY `stock_transfer_lines_item_id_foreign` (`item_id`),
  KEY `stl_tenant_transfer_index` (`tenant_id`,`stock_transfer_id`),
  CONSTRAINT `stock_transfer_lines_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_transfer_lines_stock_transfer_id_foreign` FOREIGN KEY (`stock_transfer_id`) REFERENCES `stock_transfers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_transfer_lines_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_transfer_lines`
--

LOCK TABLES `stock_transfer_lines` WRITE;
/*!40000 ALTER TABLE `stock_transfer_lines` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_transfer_lines` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_transfers`
--

DROP TABLE IF EXISTS `stock_transfers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_transfers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `source_warehouse_id` bigint unsigned NOT NULL,
  `destination_warehouse_id` bigint unsigned NOT NULL,
  `state` enum('draft','confirmed','done','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `user_id` bigint unsigned NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_transfers_reference_unique` (`reference`),
  KEY `stock_transfers_source_warehouse_id_foreign` (`source_warehouse_id`),
  KEY `stock_transfers_destination_warehouse_id_foreign` (`destination_warehouse_id`),
  KEY `stock_transfers_user_id_foreign` (`user_id`),
  KEY `st_tenant_state_index` (`tenant_id`,`state`),
  CONSTRAINT `stock_transfers_destination_warehouse_id_foreign` FOREIGN KEY (`destination_warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_transfers_source_warehouse_id_foreign` FOREIGN KEY (`source_warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_transfers_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_transfers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_transfers`
--

LOCK TABLES `stock_transfers` WRITE;
/*!40000 ALTER TABLE `stock_transfers` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_transfers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `account_id` bigint unsigned DEFAULT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_ar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `address_en` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `commercial_registration` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credit_limit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `classification` enum('a','b','c','d') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'a',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `suppliers_tenant_id_code_unique` (`tenant_id`,`code`),
  KEY `suppliers_account_id_foreign` (`account_id`),
  KEY `suppliers_tenant_id_name_index` (`tenant_id`,`name`),
  KEY `suppliers_tenant_id_is_active_index` (`tenant_id`,`is_active`),
  CONSTRAINT `suppliers_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `suppliers_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tax_settings`
--

DROP TABLE IF EXISTS `tax_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tax_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `type` enum('fixed','percentage') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percentage',
  `account_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tax_settings_account_id_foreign` (`account_id`),
  KEY `tax_settings_tenant_id_is_active_index` (`tenant_id`,`is_active`),
  CONSTRAINT `tax_settings_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tax_settings_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tax_settings`
--

LOCK TABLES `tax_settings` WRITE;
/*!40000 ALTER TABLE `tax_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `tax_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `taxes`
--

DROP TABLE IF EXISTS `taxes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `taxes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('fixed','percentage','group') COLLATE utf8mb4_unicode_ci NOT NULL,
  `rate` decimal(8,4) NOT NULL DEFAULT '0.0000',
  `amount_type` enum('fixed','percent','group','division') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percent',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `is_included_in_price` tinyint(1) NOT NULL DEFAULT '0',
  `tax_group_id` bigint unsigned DEFAULT NULL,
  `account_id` bigint unsigned DEFAULT NULL,
  `purchase_account_id` bigint unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `taxes_code_unique` (`code`),
  KEY `taxes_tenant_id_foreign` (`tenant_id`),
  KEY `taxes_tax_group_id_foreign` (`tax_group_id`),
  KEY `taxes_account_id_foreign` (`account_id`),
  KEY `taxes_purchase_account_id_foreign` (`purchase_account_id`),
  CONSTRAINT `taxes_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `taxes_purchase_account_id_foreign` FOREIGN KEY (`purchase_account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `taxes_tax_group_id_foreign` FOREIGN KEY (`tax_group_id`) REFERENCES `taxes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `taxes_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `taxes`
--

LOCK TABLES `taxes` WRITE;
/*!40000 ALTER TABLE `taxes` DISABLE KEYS */;
/*!40000 ALTER TABLE `taxes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tenant_user`
--

DROP TABLE IF EXISTS `tenant_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenant_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenant_user_tenant_id_user_id_unique` (`tenant_id`,`user_id`),
  KEY `tenant_user_user_id_foreign` (`user_id`),
  CONSTRAINT `tenant_user_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tenant_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tenant_user`
--

LOCK TABLES `tenant_user` WRITE;
/*!40000 ALTER TABLE `tenant_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `tenant_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tenants`
--

DROP TABLE IF EXISTS `tenants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tenants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `domain` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `database` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `settings` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenants_slug_unique` (`slug`),
  UNIQUE KEY `tenants_domain_unique` (`domain`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tenants`
--

LOCK TABLES `tenants` WRITE;
/*!40000 ALTER TABLE `tenants` DISABLE KEYS */;
INSERT INTO `tenants` VALUES (1,'الشركة الافتراضية','default',NULL,NULL,1,NULL,'2026-06-20 05:11:26','2026-06-20 05:11:26',NULL);
/*!40000 ALTER TABLE `tenants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `treasury_transactions`
--

DROP TABLE IF EXISTS `treasury_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `treasury_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `treasury_id` bigint unsigned NOT NULL,
  `type` enum('in','out','transfer','opening') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `reference_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint unsigned DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `reference_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `target_treasury_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `treasury_transactions_treasury_id_foreign` (`treasury_id`),
  KEY `treasury_transactions_user_id_foreign` (`user_id`),
  KEY `treasury_transactions_target_treasury_id_foreign` (`target_treasury_id`),
  KEY `treasury_transactions_tenant_id_treasury_id_index` (`tenant_id`,`treasury_id`),
  KEY `treasury_transactions_tenant_id_type_index` (`tenant_id`,`type`),
  KEY `treasury_trans_ref_index` (`tenant_id`,`reference_type`,`reference_id`),
  CONSTRAINT `treasury_transactions_target_treasury_id_foreign` FOREIGN KEY (`target_treasury_id`) REFERENCES `cash_treasuries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `treasury_transactions_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `treasury_transactions_treasury_id_foreign` FOREIGN KEY (`treasury_id`) REFERENCES `cash_treasuries` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `treasury_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `treasury_transactions`
--

LOCK TABLES `treasury_transactions` WRITE;
/*!40000 ALTER TABLE `treasury_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `treasury_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_permissions`
--

DROP TABLE IF EXISTS `user_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_permissions_slug_unique` (`slug`),
  KEY `user_permissions_tenant_id_foreign` (`tenant_id`),
  CONSTRAINT `user_permissions_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_permissions`
--

LOCK TABLES `user_permissions` WRITE;
/*!40000 ALTER TABLE `user_permissions` DISABLE KEYS */;
INSERT INTO `user_permissions` VALUES (1,1,'عرض دليل الحسابات',NULL,'view_accounts','accounts','2026-06-21 06:55:55','2026-06-21 06:55:55'),(2,1,'إضافة حساب',NULL,'create_accounts','accounts','2026-06-21 06:55:55','2026-06-21 06:55:55'),(3,1,'تعديل حساب',NULL,'edit_accounts','accounts','2026-06-21 06:55:55','2026-06-21 06:55:55'),(4,1,'حذف حساب',NULL,'delete_accounts','accounts','2026-06-21 06:55:55','2026-06-21 06:55:55'),(5,1,'عرض العملاء',NULL,'view_customers','customers','2026-06-21 06:55:55','2026-06-21 06:55:55'),(6,1,'إضافة عميل',NULL,'create_customers','customers','2026-06-21 06:55:55','2026-06-21 06:55:55'),(7,1,'تعديل عميل',NULL,'edit_customers','customers','2026-06-21 06:55:55','2026-06-21 06:55:55'),(8,1,'حذف عميل',NULL,'delete_customers','customers','2026-06-21 06:55:55','2026-06-21 06:55:55'),(9,1,'عرض الموردين',NULL,'view_suppliers','suppliers','2026-06-21 06:55:55','2026-06-21 06:55:55'),(10,1,'إضافة مورد',NULL,'create_suppliers','suppliers','2026-06-21 06:55:55','2026-06-21 06:55:55'),(11,1,'تعديل مورد',NULL,'edit_suppliers','suppliers','2026-06-21 06:55:55','2026-06-21 06:55:55'),(12,1,'حذف مورد',NULL,'delete_suppliers','suppliers','2026-06-21 06:55:55','2026-06-21 06:55:55'),(13,1,'عرض الأصناف',NULL,'view_items','items','2026-06-21 06:55:55','2026-06-21 06:55:55'),(14,1,'إضافة صنف',NULL,'create_items','items','2026-06-21 06:55:55','2026-06-21 06:55:55'),(15,1,'تعديل صنف',NULL,'edit_items','items','2026-06-21 06:55:55','2026-06-21 06:55:55'),(16,1,'حذف صنف',NULL,'delete_items','items','2026-06-21 06:55:55','2026-06-21 06:55:55'),(17,1,'عرض فواتير البيع',NULL,'view_sales_invoices','sales_invoices','2026-06-21 06:55:55','2026-06-21 06:55:55'),(18,1,'إضافة فاتورة بيع',NULL,'create_sales_invoices','sales_invoices','2026-06-21 06:55:55','2026-06-21 06:55:55'),(19,1,'تعديل فاتورة بيع',NULL,'edit_sales_invoices','sales_invoices','2026-06-21 06:55:55','2026-06-21 06:55:55'),(20,1,'حذف فاتورة بيع',NULL,'delete_sales_invoices','sales_invoices','2026-06-21 06:55:55','2026-06-21 06:55:55'),(21,1,'اعتماد فاتورة بيع',NULL,'approve_sales_invoices','sales_invoices','2026-06-21 06:55:55','2026-06-21 06:55:55'),(22,1,'عرض فواتير الشراء',NULL,'view_purchase_invoices','purchase_invoices','2026-06-21 06:55:55','2026-06-21 06:55:55'),(23,1,'إضافة فاتورة شراء',NULL,'create_purchase_invoices','purchase_invoices','2026-06-21 06:55:55','2026-06-21 06:55:55'),(24,1,'تعديل فاتورة شراء',NULL,'edit_purchase_invoices','purchase_invoices','2026-06-21 06:55:55','2026-06-21 06:55:55'),(25,1,'حذف فاتورة شراء',NULL,'delete_purchase_invoices','purchase_invoices','2026-06-21 06:55:55','2026-06-21 06:55:55'),(26,1,'اعتماد فاتورة شراء',NULL,'approve_purchase_invoices','purchase_invoices','2026-06-21 06:55:55','2026-06-21 06:55:55'),(27,1,'عرض أوامر البيع',NULL,'view_sales_orders','sales_orders','2026-06-21 06:55:55','2026-06-21 06:55:55'),(28,1,'إضافة أمر بيع',NULL,'create_sales_orders','sales_orders','2026-06-21 06:55:55','2026-06-21 06:55:55'),(29,1,'تعديل أمر بيع',NULL,'edit_sales_orders','sales_orders','2026-06-21 06:55:55','2026-06-21 06:55:55'),(30,1,'حذف أمر بيع',NULL,'delete_sales_orders','sales_orders','2026-06-21 06:55:55','2026-06-21 06:55:55'),(31,1,'عرض أوامر الشراء',NULL,'view_purchase_orders','purchase_orders','2026-06-21 06:55:55','2026-06-21 06:55:55'),(32,1,'إضافة أمر شراء',NULL,'create_purchase_orders','purchase_orders','2026-06-21 06:55:55','2026-06-21 06:55:55'),(33,1,'تعديل أمر شراء',NULL,'edit_purchase_orders','purchase_orders','2026-06-21 06:55:55','2026-06-21 06:55:55'),(34,1,'حذف أمر شراء',NULL,'delete_purchase_orders','purchase_orders','2026-06-21 06:55:55','2026-06-21 06:55:55'),(35,1,'عرض مرتجعات البيع',NULL,'view_sales_returns','sales_returns','2026-06-21 06:55:55','2026-06-21 06:55:55'),(36,1,'إضافة مرتجع بيع',NULL,'create_sales_returns','sales_returns','2026-06-21 06:55:55','2026-06-21 06:55:55'),(37,1,'حذف مرتجع بيع',NULL,'delete_sales_returns','sales_returns','2026-06-21 06:55:55','2026-06-21 06:55:55'),(38,1,'عرض مرتجعات الشراء',NULL,'view_purchase_returns','purchase_returns','2026-06-21 06:55:55','2026-06-21 06:55:55'),(39,1,'إضافة مرتجع شراء',NULL,'create_purchase_returns','purchase_returns','2026-06-21 06:55:55','2026-06-21 06:55:55'),(40,1,'حذف مرتجع شراء',NULL,'delete_purchase_returns','purchase_returns','2026-06-21 06:55:55','2026-06-21 06:55:55'),(41,1,'عرض عروض الأسعار',NULL,'view_quotations','quotations','2026-06-21 06:55:55','2026-06-21 06:55:55'),(42,1,'إضافة عرض سعر',NULL,'create_quotations','quotations','2026-06-21 06:55:55','2026-06-21 06:55:55'),(43,1,'تعديل عرض سعر',NULL,'edit_quotations','quotations','2026-06-21 06:55:55','2026-06-21 06:55:55'),(44,1,'حذف عرض سعر',NULL,'delete_quotations','quotations','2026-06-21 06:55:55','2026-06-21 06:55:55'),(45,1,'تحويل عرض سعر',NULL,'convert_quotations','quotations','2026-06-21 06:55:55','2026-06-21 06:55:55'),(46,1,'عرض إذنات التسليم',NULL,'view_delivery_notes','delivery_notes','2026-06-21 06:55:55','2026-06-21 06:55:55'),(47,1,'إضافة إذن تسليم',NULL,'create_delivery_notes','delivery_notes','2026-06-21 06:55:55','2026-06-21 06:55:55'),(48,1,'حذف إذن تسليم',NULL,'delete_delivery_notes','delivery_notes','2026-06-21 06:55:55','2026-06-21 06:55:55'),(49,1,'عرض إذنات الاستلام',NULL,'view_receipt_notes','receipt_notes','2026-06-21 06:55:55','2026-06-21 06:55:55'),(50,1,'إضافة إذن استلام',NULL,'create_receipt_notes','receipt_notes','2026-06-21 06:55:55','2026-06-21 06:55:55'),(51,1,'حذف إذن استلام',NULL,'delete_receipt_notes','receipt_notes','2026-06-21 06:55:55','2026-06-21 06:55:55'),(52,1,'عرض قيود اليومية',NULL,'view_journal_entries','journal_entries','2026-06-21 06:55:55','2026-06-21 06:55:55'),(53,1,'إضافة قيد يومية',NULL,'create_journal_entries','journal_entries','2026-06-21 06:55:55','2026-06-21 06:55:55'),(54,1,'تعديل قيد يومية',NULL,'edit_journal_entries','journal_entries','2026-06-21 06:55:55','2026-06-21 06:55:55'),(55,1,'حذف قيد يومية',NULL,'delete_journal_entries','journal_entries','2026-06-21 06:55:55','2026-06-21 06:55:55'),(56,1,'اعتماد قيد يومية',NULL,'approve_journal_entries','journal_entries','2026-06-21 06:55:55','2026-06-21 06:55:55'),(57,1,'عرض المدفوعات',NULL,'view_payments','payments','2026-06-21 06:55:55','2026-06-21 06:55:55'),(58,1,'إضافة مدفوعات',NULL,'create_payments','payments','2026-06-21 06:55:55','2026-06-21 06:55:55'),(59,1,'حذف مدفوعات',NULL,'delete_payments','payments','2026-06-21 06:55:55','2026-06-21 06:55:55'),(60,1,'عرض حركات المخزون',NULL,'view_stock_movements','stock','2026-06-21 06:55:55','2026-06-21 06:55:55'),(61,1,'عرض تحويلات المخزون',NULL,'view_stock_transfers','stock','2026-06-21 06:55:55','2026-06-21 06:55:55'),(62,1,'إضافة تحويل مخزون',NULL,'create_stock_transfers','stock','2026-06-21 06:55:55','2026-06-21 06:55:55'),(63,1,'عرض تسويات المخزون',NULL,'view_inventory_adjustments','stock','2026-06-21 06:55:55','2026-06-21 06:55:55'),(64,1,'إضافة تسوية مخزون',NULL,'create_inventory_adjustments','stock','2026-06-21 06:55:55','2026-06-21 06:55:55'),(65,1,'عرض المصروفات',NULL,'view_expenses','expenses','2026-06-21 06:55:55','2026-06-21 06:55:55'),(66,1,'إضافة مصروف',NULL,'create_expenses','expenses','2026-06-21 06:55:55','2026-06-21 06:55:55'),(67,1,'حذف مصروف',NULL,'delete_expenses','expenses','2026-06-21 06:55:55','2026-06-21 06:55:55'),(68,1,'عرض الميزانيات',NULL,'view_budgets','budgets','2026-06-21 06:55:55','2026-06-21 06:55:55'),(69,1,'إضافة ميزانية',NULL,'create_budgets','budgets','2026-06-21 06:55:55','2026-06-21 06:55:55'),(70,1,'تعديل ميزانية',NULL,'edit_budgets','budgets','2026-06-21 06:55:55','2026-06-21 06:55:55'),(71,1,'حذف ميزانية',NULL,'delete_budgets','budgets','2026-06-21 06:55:55','2026-06-21 06:55:55'),(72,1,'عرض الحسابات البنكية',NULL,'view_bank_accounts','bank','2026-06-21 06:55:55','2026-06-21 06:55:55'),(73,1,'عرض كشوفات البنك',NULL,'view_bank_statements','bank','2026-06-21 06:55:55','2026-06-21 06:55:55'),(74,1,'إضافة معاملة بنكية',NULL,'create_bank_transactions','bank','2026-06-21 06:55:55','2026-06-21 06:55:55'),(75,1,'عرض الخزينة',NULL,'view_treasury','treasury','2026-06-21 06:55:55','2026-06-21 06:55:55'),(76,1,'إضافة معاملة خزينة',NULL,'create_treasury_transactions','treasury','2026-06-21 06:55:55','2026-06-21 06:55:55'),(77,1,'عرض الموظفين',NULL,'view_employees','employees','2026-06-21 06:55:55','2026-06-21 06:55:55'),(78,1,'إضافة موظف',NULL,'create_employees','employees','2026-06-21 06:55:55','2026-06-21 06:55:55'),(79,1,'تعديل موظف',NULL,'edit_employees','employees','2026-06-21 06:55:55','2026-06-21 06:55:55'),(80,1,'حذف موظف',NULL,'delete_employees','employees','2026-06-21 06:55:55','2026-06-21 06:55:55'),(81,1,'عرض الرواتب',NULL,'view_payroll','payroll','2026-06-21 06:55:55','2026-06-21 06:55:55'),(82,1,'إضافة راتب',NULL,'create_payroll','payroll','2026-06-21 06:55:55','2026-06-21 06:55:55'),(83,1,'اعتماد راتب',NULL,'approve_payroll','payroll','2026-06-21 06:55:55','2026-06-21 06:55:55'),(84,1,'عرض العهد',NULL,'view_custodies','custodies','2026-06-21 06:55:55','2026-06-21 06:55:55'),(85,1,'إضافة عهدة',NULL,'create_custodies','custodies','2026-06-21 06:55:55','2026-06-21 06:55:55'),(86,1,'حذف عهدة',NULL,'delete_custodies','custodies','2026-06-21 06:55:55','2026-06-21 06:55:55'),(87,1,'عرض التقارير',NULL,'view_reports','reports','2026-06-21 06:55:55','2026-06-21 06:55:55'),(88,1,'تصدير التقارير',NULL,'export_reports','reports','2026-06-21 06:55:55','2026-06-21 06:55:55'),(89,1,'عرض الإعدادات',NULL,'view_settings','settings','2026-06-21 06:55:55','2026-06-21 06:55:55'),(90,1,'تعديل الإعدادات',NULL,'edit_settings','settings','2026-06-21 06:55:55','2026-06-21 06:55:55'),(91,1,'إدارة الصلاحيات',NULL,'manage_roles','settings','2026-06-21 06:55:55','2026-06-21 06:55:55'),(92,1,'إدارة المستخدمين',NULL,'manage_users','settings','2026-06-21 06:55:55','2026-06-21 06:55:55'),(93,1,'الاطلاع على سعر التكلفة',NULL,'view_cost_price','items','2026-06-21 07:30:01','2026-06-21 07:30:01');
/*!40000 ALTER TABLE `user_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_system` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_roles_slug_unique` (`slug`),
  KEY `user_roles_tenant_id_slug_index` (`tenant_id`,`slug`),
  CONSTRAINT `user_roles_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_roles`
--

LOCK TABLES `user_roles` WRITE;
/*!40000 ALTER TABLE `user_roles` DISABLE KEYS */;
INSERT INTO `user_roles` VALUES (1,1,'مدير النظام','Super Admin','super_admin','صلاحية كاملة على جميع أجزاء النظام',1,1,'2026-06-21 06:55:55','2026-06-21 06:55:55',NULL),(2,1,'مدير','Admin','admin','مدير الشركة - صلاحية كاملة',1,1,'2026-06-21 06:56:08','2026-06-21 06:56:08',NULL),(3,1,'محاسب','Accountant','accountant','إدارة الحسابات والفواتير والقيود',1,1,'2026-06-21 06:56:08','2026-06-21 06:56:08',NULL),(4,1,'أمين مستودع','Warehouse Keeper','warehouse_keeper','إدارة المخزون والمستودعات',1,1,'2026-06-21 06:56:08','2026-06-21 06:56:08',NULL),(5,1,'مندوب مبيعات','Sales Representative','sales','إدارة المبيعات والعملاء',1,1,'2026-06-21 06:56:08','2026-06-21 06:56:08',NULL),(6,1,'مشتريات','Purchases','purchases','إدارة المشتريات والموردين',1,1,'2026-06-21 06:56:08','2026-06-21 06:56:08',NULL);
/*!40000 ALTER TABLE `user_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'viewer',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_system` tinyint(1) NOT NULL DEFAULT '0',
  `two_factor_secret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_recovery_codes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `locale` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ar',
  `timezone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Asia/Riyadh',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_tenant_id_email_unique` (`tenant_id`,`email`),
  KEY `users_tenant_id_role_index` (`tenant_id`,`role`),
  KEY `users_tenant_id_is_active_index` (`tenant_id`,`is_active`),
  CONSTRAINT `users_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,'bassam','admin@smart-erp.com',NULL,'$2y$12$1C7E3WU5a/aZDKVUaFV3jeiPyR25eDvILEUzEC1aWfWyaoCaLCCiW','accountant',1,0,NULL,NULL,NULL,NULL,'ar','Asia/Riyadh',NULL,'2026-06-20 05:11:27','2026-06-21 07:52:52',NULL),(2,1,'mazen','mazen@mazen.com',NULL,'$2y$12$kdSasY9DNjLyFzWSRNIVj.7SruGsX0l.Ij8TUotV7er2GFu3VaMvO','accountant',1,0,NULL,NULL,NULL,NULL,'ar','Asia/Riyadh',NULL,'2026-06-21 07:47:48','2026-06-21 07:53:43',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `warehouses`
--

DROP TABLE IF EXISTS `warehouses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `warehouses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manager_id` bigint unsigned DEFAULT NULL,
  `manager_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `manager_phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `warehouses_tenant_id_code_unique` (`tenant_id`,`code`),
  KEY `warehouses_manager_id_foreign` (`manager_id`),
  KEY `warehouses_tenant_id_is_active_index` (`tenant_id`,`is_active`),
  CONSTRAINT `warehouses_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `warehouses_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `warehouses`
--

LOCK TABLES `warehouses` WRITE;
/*!40000 ALTER TABLE `warehouses` DISABLE KEYS */;
/*!40000 ALTER TABLE `warehouses` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-21 14:15:25
