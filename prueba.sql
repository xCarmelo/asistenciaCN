-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: asistenciaCNSR
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `asistencias`
--

DROP TABLE IF EXISTS `asistencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asistencias` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `id_seccion` bigint(20) unsigned NOT NULL,
  `asis` char(1) NOT NULL,
  `justificado` tinyint(1) NOT NULL DEFAULT 0,
  `injustificado` tinyint(1) NOT NULL DEFAULT 0,
  `id_estudiante` bigint(20) unsigned NOT NULL,
  `id_corte` bigint(20) unsigned NOT NULL,
  `id_tipo_asistencia` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asistencias_id_seccion_foreign` (`id_seccion`),
  KEY `asistencias_id_estudiante_foreign` (`id_estudiante`),
  KEY `asistencias_id_corte_foreign` (`id_corte`),
  KEY `id_tipo_asistencia` (`id_tipo_asistencia`),
  CONSTRAINT `asistencias_ibfk_1` FOREIGN KEY (`id_tipo_asistencia`) REFERENCES `tipos_asistencia` (`id`) ON DELETE SET NULL,
  CONSTRAINT `asistencias_id_corte_foreign` FOREIGN KEY (`id_corte`) REFERENCES `cortes` (`id`),
  CONSTRAINT `asistencias_id_estudiante_foreign` FOREIGN KEY (`id_estudiante`) REFERENCES `estudiantes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `asistencias_id_seccion_foreign` FOREIGN KEY (`id_seccion`) REFERENCES `secciones` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1582 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asistencias`
--

LOCK TABLES `asistencias` WRITE;
/*!40000 ALTER TABLE `asistencias` DISABLE KEYS */;
INSERT INTO `asistencias` VALUES (1546,'2026-05-24',22,'P',0,0,60,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1548,'2026-05-24',22,'P',0,0,62,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1549,'2026-05-24',22,'J',1,0,63,1,3,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1550,'2026-05-24',22,'A',0,1,64,1,2,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1551,'2026-05-24',22,'T',0,0,65,1,4,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1552,'2026-05-24',22,'P',0,0,66,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1553,'2026-05-24',22,'P',0,0,67,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1554,'2026-05-24',22,'P',0,0,68,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1555,'2026-05-24',22,'P',0,0,69,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1556,'2026-05-24',22,'P',0,0,70,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1557,'2026-05-24',22,'P',0,0,71,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1558,'2026-05-24',22,'P',0,0,72,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1559,'2026-05-24',22,'P',0,0,73,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1560,'2026-05-24',22,'P',0,0,74,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1561,'2026-05-24',22,'P',0,0,75,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1562,'2026-05-24',22,'P',0,0,76,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1563,'2026-05-24',22,'P',0,0,77,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1564,'2026-05-24',22,'P',0,0,78,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1565,'2026-05-24',22,'P',0,0,79,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1566,'2026-05-24',22,'P',0,0,80,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1567,'2026-05-24',22,'P',0,0,81,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1568,'2026-05-24',22,'P',0,0,82,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1569,'2026-05-24',22,'P',0,0,83,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1570,'2026-05-24',22,'P',0,0,84,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1571,'2026-05-24',22,'P',0,0,85,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1572,'2026-05-24',22,'P',0,0,86,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1573,'2026-05-24',22,'P',0,0,87,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1574,'2026-05-24',22,'P',0,0,88,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1575,'2026-05-24',22,'P',0,0,89,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1576,'2026-05-24',22,'P',0,0,90,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1577,'2026-05-24',22,'P',0,0,91,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1578,'2026-05-24',22,'P',0,0,92,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1579,'2026-05-24',22,'P',0,0,93,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1580,'2026-05-24',22,'P',0,0,94,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08'),(1581,'2026-05-24',22,'P',0,0,95,1,1,'2026-05-25 02:18:16','2026-05-25 02:19:08');
/*!40000 ALTER TABLE `asistencias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asistencias_maestros`
--

DROP TABLE IF EXISTS `asistencias_maestros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asistencias_maestros` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `id_maestro` bigint(20) unsigned NOT NULL,
  `asis` char(1) NOT NULL,
  `justificado` tinyint(1) NOT NULL DEFAULT 0,
  `injustificado` tinyint(1) NOT NULL DEFAULT 0,
  `id_corte` bigint(20) unsigned NOT NULL,
  `id_tipo_asistencia` bigint(20) unsigned DEFAULT NULL,
  `tutelado` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asistencias_maestros_id_maestro_foreign` (`id_maestro`),
  KEY `asistencias_maestros_id_corte_foreign` (`id_corte`),
  KEY `id_tipo_asistencia` (`id_tipo_asistencia`),
  CONSTRAINT `asistencias_maestros_ibfk_1` FOREIGN KEY (`id_tipo_asistencia`) REFERENCES `tipos_asistencia` (`id`) ON DELETE SET NULL,
  CONSTRAINT `asistencias_maestros_id_corte_foreign` FOREIGN KEY (`id_corte`) REFERENCES `cortes` (`id`),
  CONSTRAINT `asistencias_maestros_id_maestro_foreign` FOREIGN KEY (`id_maestro`) REFERENCES `maestros` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asistencias_maestros`
--

LOCK TABLES `asistencias_maestros` WRITE;
/*!40000 ALTER TABLE `asistencias_maestros` DISABLE KEYS */;
INSERT INTO `asistencias_maestros` VALUES (1,'2026-05-14',2,'A',0,1,1,NULL,NULL,'2026-05-14 17:25:13','2026-05-15 00:44:15'),(2,'2026-05-14',8,'P',0,0,1,NULL,NULL,'2026-05-14 17:25:13','2026-05-15 00:44:15'),(3,'2026-05-14',10,'A',0,1,1,NULL,NULL,'2026-05-14 17:25:13','2026-05-15 00:44:15'),(4,'2026-05-14',12,'A',0,1,1,NULL,NULL,'2026-05-14 17:25:13','2026-05-15 00:44:15'),(5,'2026-05-14',13,'P',0,0,1,NULL,NULL,'2026-05-14 17:25:13','2026-05-15 00:44:15'),(6,'2026-05-14',14,'P',0,0,1,NULL,NULL,'2026-05-14 17:25:13','2026-05-15 00:44:15'),(7,'2026-05-15',2,'A',0,1,1,NULL,NULL,'2026-05-15 00:43:28','2026-05-15 00:43:28'),(8,'2026-05-15',8,'P',0,0,1,NULL,NULL,'2026-05-15 00:43:28','2026-05-15 00:43:28'),(9,'2026-05-15',10,'A',0,1,1,NULL,NULL,'2026-05-15 00:43:28','2026-05-15 00:43:28'),(10,'2026-05-15',12,'P',0,0,1,NULL,NULL,'2026-05-15 00:43:28','2026-05-15 00:43:28'),(11,'2026-05-15',13,'P',0,0,1,NULL,NULL,'2026-05-15 00:43:28','2026-05-15 00:43:28'),(12,'2026-05-15',14,'P',0,0,1,NULL,NULL,'2026-05-15 00:43:28','2026-05-15 00:43:28'),(13,'2026-05-16',2,'P',0,0,1,NULL,NULL,'2026-05-17 03:08:02','2026-05-17 03:08:02'),(14,'2026-05-16',8,'P',0,0,1,NULL,NULL,'2026-05-17 03:08:02','2026-05-17 03:08:02'),(15,'2026-05-16',10,'P',0,0,1,NULL,NULL,'2026-05-17 03:08:02','2026-05-17 03:08:02'),(16,'2026-05-16',12,'P',0,0,1,NULL,NULL,'2026-05-17 03:08:02','2026-05-17 03:08:02'),(17,'2026-05-16',13,'P',0,0,1,NULL,NULL,'2026-05-17 03:08:02','2026-05-17 03:08:02'),(18,'2026-05-16',14,'P',0,0,1,NULL,NULL,'2026-05-17 03:08:02','2026-05-17 03:08:02'),(19,'2026-05-17',2,'P',0,0,1,NULL,NULL,'2026-05-17 04:28:32','2026-05-17 04:28:32'),(20,'2026-05-17',8,'P',0,0,1,NULL,NULL,'2026-05-17 04:28:32','2026-05-17 04:28:32'),(21,'2026-05-17',10,'P',0,0,1,NULL,NULL,'2026-05-17 04:28:32','2026-05-17 04:28:32'),(22,'2026-05-17',12,'P',0,0,1,NULL,NULL,'2026-05-17 04:28:32','2026-05-17 04:28:32'),(23,'2026-05-17',13,'P',0,0,1,NULL,NULL,'2026-05-17 04:28:32','2026-05-17 04:28:32'),(24,'2026-05-17',14,'P',0,0,1,NULL,NULL,'2026-05-17 04:28:32','2026-05-17 04:28:32'),(25,'2026-05-18',2,'P',0,0,1,NULL,NULL,'2026-05-17 04:29:02','2026-05-19 16:52:32'),(26,'2026-05-18',8,'P',0,0,1,NULL,NULL,'2026-05-17 04:29:02','2026-05-19 16:52:32'),(27,'2026-05-18',10,'P',0,0,1,NULL,NULL,'2026-05-17 04:29:02','2026-05-19 16:52:32'),(28,'2026-05-18',12,'P',0,0,1,NULL,NULL,'2026-05-17 04:29:02','2026-05-19 16:52:32'),(29,'2026-05-18',13,'P',0,0,1,NULL,NULL,'2026-05-17 04:29:02','2026-05-19 16:52:32'),(30,'2026-05-18',14,'P',0,0,1,NULL,NULL,'2026-05-17 04:29:02','2026-05-19 16:52:32'),(31,'2026-05-19',2,'P',0,0,1,NULL,NULL,'2026-05-19 16:32:29','2026-05-19 16:32:29'),(32,'2026-05-19',8,'P',0,0,1,NULL,NULL,'2026-05-19 16:32:29','2026-05-19 16:32:29'),(33,'2026-05-19',10,'P',0,0,1,NULL,NULL,'2026-05-19 16:32:29','2026-05-19 16:32:29'),(34,'2026-05-19',12,'P',0,0,1,NULL,NULL,'2026-05-19 16:32:29','2026-05-19 16:32:29'),(35,'2026-05-19',13,'P',0,0,1,NULL,NULL,'2026-05-19 16:32:29','2026-05-19 16:32:29'),(36,'2026-05-19',14,'P',0,0,1,NULL,NULL,'2026-05-19 16:32:29','2026-05-19 16:32:29'),(37,'2026-05-19',15,'P',0,0,1,NULL,NULL,'2026-05-19 16:32:29','2026-05-19 16:32:29'),(38,'2026-05-19',16,'P',0,0,1,NULL,NULL,'2026-05-19 16:32:29','2026-05-19 16:42:16'),(39,'2026-05-19',18,'P',0,0,1,NULL,NULL,'2026-05-19 16:32:29','2026-05-19 16:32:54'),(40,'2026-05-19',19,'P',0,0,1,NULL,NULL,'2026-05-19 16:32:30','2026-05-19 16:32:30'),(41,'2026-05-18',15,'P',0,0,1,NULL,NULL,'2026-05-19 16:52:32','2026-05-19 16:52:32'),(42,'2026-05-18',16,'P',0,0,1,NULL,NULL,'2026-05-19 16:52:32','2026-05-19 16:52:32'),(43,'2026-05-18',18,'A',1,0,1,NULL,NULL,'2026-05-19 16:52:32','2026-05-19 16:52:43'),(44,'2026-05-18',19,'P',0,0,1,NULL,NULL,'2026-05-19 16:52:32','2026-05-19 16:52:32'),(45,'2026-05-20',2,'P',0,0,1,NULL,NULL,'2026-05-20 16:44:49','2026-05-20 16:49:02'),(46,'2026-05-20',8,'P',0,0,1,NULL,NULL,'2026-05-20 16:44:49','2026-05-20 16:49:02'),(47,'2026-05-20',10,'P',0,0,1,NULL,NULL,'2026-05-20 16:44:49','2026-05-20 16:49:02'),(48,'2026-05-20',12,'P',0,0,1,NULL,NULL,'2026-05-20 16:44:49','2026-05-20 16:49:02'),(49,'2026-05-20',13,'P',0,0,1,NULL,NULL,'2026-05-20 16:44:49','2026-05-20 16:49:02'),(50,'2026-05-20',14,'P',0,0,1,NULL,NULL,'2026-05-20 16:44:49','2026-05-20 16:49:02'),(75,'2026-05-23',2,'P',0,0,1,1,NULL,'2026-05-24 04:05:14','2026-05-24 04:05:14'),(76,'2026-05-23',4,'P',0,0,1,1,NULL,'2026-05-24 04:05:14','2026-05-24 04:05:14'),(77,'2026-05-23',8,'P',0,0,1,1,NULL,'2026-05-24 04:05:14','2026-05-24 04:05:14'),(78,'2026-05-23',10,'P',0,0,1,1,NULL,'2026-05-24 04:05:14','2026-05-24 04:05:14'),(79,'2026-05-23',12,'P',0,0,1,1,NULL,'2026-05-24 04:05:14','2026-05-24 04:05:14'),(80,'2026-05-23',13,'P',0,0,1,1,NULL,'2026-05-24 04:05:14','2026-05-24 04:05:14'),(81,'2026-05-23',14,'P',0,0,1,1,NULL,'2026-05-24 04:05:14','2026-05-24 04:05:14'),(82,'2026-05-23',15,'P',0,0,1,1,NULL,'2026-05-24 04:05:14','2026-05-24 04:05:14'),(83,'2026-05-23',16,'P',0,0,1,1,NULL,'2026-05-24 04:05:14','2026-05-24 04:05:14'),(84,'2026-05-23',19,'P',0,0,1,1,NULL,'2026-05-24 04:05:14','2026-05-24 04:05:14');
/*!40000 ALTER TABLE `asistencias_maestros` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `backups`
--

DROP TABLE IF EXISTS `backups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `backups` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `backups`
--

LOCK TABLES `backups` WRITE;
/*!40000 ALTER TABLE `backups` DISABLE KEYS */;
/*!40000 ALTER TABLE `backups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
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
-- Table structure for table `cortes`
--

DROP TABLE IF EXISTS `cortes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cortes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cortes`
--

LOCK TABLES `cortes` WRITE;
/*!40000 ALTER TABLE `cortes` DISABLE KEYS */;
INSERT INTO `cortes` VALUES (1,'I Corte','2026-05-11 03:38:34','2026-05-11 03:38:34'),(2,'II Corte','2026-05-11 03:38:34','2026-05-11 03:38:34'),(3,'III Corte','2026-05-11 03:38:34','2026-05-11 03:38:34'),(4,'IV Corte','2026-05-11 03:38:34','2026-05-11 03:38:34');
/*!40000 ALTER TABLE `cortes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estudiantes`
--

DROP TABLE IF EXISTS `estudiantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `estudiantes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `numero_lista` int(11) DEFAULT NULL,
  `genero` char(1) DEFAULT NULL,
  `año` int(11) DEFAULT NULL,
  `id_seccion` bigint(20) unsigned DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'Activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `estudiantes_id_seccion_foreign` (`id_seccion`),
  CONSTRAINT `estudiantes_id_seccion_foreign` FOREIGN KEY (`id_seccion`) REFERENCES `secciones` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=749 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estudiantes`
--

LOCK TABLES `estudiantes` WRITE;
/*!40000 ALTER TABLE `estudiantes` DISABLE KEYS */;
INSERT INTO `estudiantes` VALUES (1,'Ana Pérez',1,'F',10,NULL,'Activo','2026-05-11 03:38:34','2026-05-12 16:22:54'),(2,'Luis Gómez',2,'M',10,NULL,'Activo','2026-05-11 03:38:34','2026-05-12 16:22:54'),(3,'Sofía Rodríguez',3,'F',10,NULL,'Activo','2026-05-11 03:38:34','2026-05-12 16:22:54'),(4,'Mateo Fernández',4,'M',10,NULL,'Activo','2026-05-11 03:38:34','2026-05-12 16:22:54'),(5,'Valentina López',1,'F',10,NULL,'Activo','2026-05-11 03:38:34','2026-05-11 03:38:34'),(6,'Santiago Díaz',2,'M',10,NULL,'Activo','2026-05-11 03:38:34','2026-05-11 04:13:32'),(7,'Isabella Torres',3,'F',10,NULL,'Activo','2026-05-11 03:38:34','2026-05-11 04:13:32'),(8,'Nicolás Sánchez',1,'M',11,NULL,'Activo','2026-05-11 03:38:34','2026-05-11 03:38:34'),(9,'Camila Ramírez',2,'F',11,NULL,'Activo','2026-05-11 03:38:34','2026-05-12 16:01:04'),(10,'Andrés Castro',3,'M',11,NULL,'Activo','2026-05-11 03:38:34','2026-05-12 16:01:04'),(11,'Lucía Morales',1,'F',11,NULL,'Activo','2026-05-11 03:38:34','2026-05-11 03:38:34'),(12,'Diego Ortega',2,'M',11,NULL,'Activo','2026-05-11 03:38:34','2026-05-11 03:38:34'),(13,'jose',6,NULL,NULL,NULL,'Activo','2026-05-11 03:39:27','2026-05-12 16:01:04'),(14,'Jose clemente romero valdivia',2,'M',2026,NULL,'Activo','2026-05-11 03:41:36','2026-05-11 04:13:32'),(15,'Kevin',10,'M',2026,NULL,'Activo','2026-05-11 04:18:14','2026-05-12 16:22:54'),(16,'jose',7,NULL,NULL,NULL,'Activo','2026-05-11 04:19:58','2026-05-12 16:01:04'),(17,'jose',2,NULL,NULL,NULL,'Activo','2026-05-11 04:21:16','2026-05-12 16:01:03'),(18,'Kevin dasd',2,'M',2026,NULL,'Inactivo','2026-05-11 05:00:06','2026-05-12 15:58:20'),(19,'Kevin 89',5,'M',2026,NULL,'Activo','2026-05-12 15:26:24','2026-05-12 16:22:54'),(20,'Kevin 99',6,'M',2026,NULL,'Activo','2026-05-12 16:00:05','2026-05-12 16:22:54'),(21,'jose',3,'M',NULL,NULL,'Activo','2026-05-12 16:01:03','2026-05-12 16:01:03'),(22,'Kevin 100',2,'M',2026,NULL,'Activo','2026-05-12 16:16:10','2026-05-12 16:22:16'),(23,'Kevin 101',1,'M',2026,NULL,'Activo','2026-05-12 16:22:16','2026-05-12 16:22:16'),(24,'Kevin 102',3,'M',2026,NULL,'Activo','2026-05-12 16:22:54','2026-05-12 16:22:54'),(25,'Kevin 102',1,'M',2026,NULL,'Activo','2026-05-14 17:26:51','2026-05-14 17:28:30'),(26,'Valeria Martínez',2,'F',7,NULL,'Activo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(27,'Emilio Rodríguez',3,'M',7,NULL,'Activo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(28,'Daniela Sánchez',4,'F',7,NULL,'Activo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(29,'Mateo López',5,'M',7,NULL,'Inactivo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(30,'Renata Pérez',1,'F',7,NULL,'Activo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(31,'Santiago Gómez',2,'M',7,NULL,'Activo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(32,'Julieta Díaz',3,'F',7,NULL,'Activo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(33,'Tomás Torres',4,'M',7,NULL,'Activo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(34,'Isabella Ramírez',5,'F',7,NULL,'Activo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(35,'Gabriel Flores',1,'M',9,NULL,'Activo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(36,'Valentina Castro',2,'F',9,NULL,'Activo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(37,'Lucas Mendoza',3,'M',9,NULL,'Activo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(38,'Camila Ortega',4,'F',9,NULL,'Inactivo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(39,'Joaquín Rojas',1,'M',9,NULL,'Activo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(40,'Mía Silva',2,'F',9,NULL,'Activo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(41,'Thiago Herrera',3,'M',9,NULL,'Activo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(42,'Emma Morales',4,'F',9,NULL,'Activo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(43,'Dylan Acosta',1,'M',10,NULL,'Activo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(44,'Salomé Paz',2,'F',10,NULL,'Activo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(45,'Ian Mejía',3,'M',10,NULL,'Activo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(46,'Aitana Córdoba',4,'F',10,NULL,'Inactivo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(47,'Nicolás Paredes',1,'M',11,NULL,'Activo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(48,'Luciana Bermúdez',2,'F',11,NULL,'Activo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(49,'Mateo Villa',3,'M',11,NULL,'Activo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(50,'Alma Rivas',4,'F',11,NULL,'Activo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(51,'Simón Quintero',1,'M',12,NULL,'Activo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(52,'Clara Orozco',2,'F',12,NULL,'Activo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(53,'Maximiliano Rangel',3,'M',12,NULL,'Activo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(54,'Julia Navarro',4,'F',12,NULL,'Activo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(55,'Facundo Luna',1,'M',12,NULL,'Activo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(56,'Rocío Zambrano',2,'F',12,NULL,'Activo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(57,'Julián Fuentes',3,'M',12,NULL,'Inactivo','2026-05-18 03:07:04','2026-05-18 03:07:04'),(58,'Altamirano Rodríguez Grace Sofía',4,'F',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:21:59'),(60,'Arauz Ortez Camilo Alberto',5,'M',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:21:59'),(62,'Barahona Herrera Ellie Camila',6,'F',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(63,'Benavides Flores Juan José',7,'M',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(64,'Blandón Balmaceda Lenin Alejandro',8,'M',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(65,'Borge Barreto Amari Charlotte',9,'F',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(66,'Castellón Ruiz Alice Valentina',10,'F',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(67,'Castillo Arauz Mario Francisco',11,'M',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(68,'Castillo Blandón Jaslin Alondra',12,'F',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(69,'Castillo Castillo Anthony Gael',13,'M',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(70,'Centeno Leiva Zoe Yalimar',14,'F',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(71,'Chávez Andino Yassiry Sofía',15,'F',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(72,'Córdoba Rivera José David',16,'M',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(73,'Espinoza Benavidez Rommel Ibrahim',17,'M',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(74,'Flores Lagos Liam Caleb',18,'M',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(75,'Fonseca Olivera Lía Antonella',19,'F',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(76,'García Toruño Nahomy Gissell',20,'F',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(77,'Gutiérrez Cáceres Marcela',21,'M',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(78,'Gutiérrez Cáceres Paola',22,'M',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(79,'Lanuza Mendoza Hansel Marcelo',23,'M',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(80,'López Sánchez Adriana de Fátima',24,'F',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(81,'Molina Vargas Noah Sebastian',25,'M',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(82,'Montoya Hernández Jared Tadeo',26,'M',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(83,'Morales Gómez Marcelo Matias',27,'M',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(84,'Moreno Sevilla Lucía Mercedes',28,'F',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(85,'Ríos Mairena Magdiel Zaid',29,'M',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(86,'Rivera Martínez Zoe Camila',30,'F',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(87,'Rivera Rodríguez Alvin Alejandro',31,'M',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(88,'Rodríguez Arcia Andrea Karina',32,'F',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(89,'Salgado Espinoza Eythan Aarón',33,'M',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(90,'Salgado Reyes Edwin Elias',34,'M',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(91,'Urbina Calderón Aislinn Marie',35,'M',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(92,'Valdivia Lewin Cataleya Teresa',36,'M',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(93,'Valle Rivera Valerys Fiorella',37,'M',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(94,'Valle Valdivia Maviet Samara',38,'M',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(95,'Vega López Arturo Alonso',39,'M',NULL,22,'Activo','2026-05-20 15:48:26','2026-05-25 16:22:09'),(96,'Casco Máxima Nathacha',1,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(97,'Díaz Ruiz Nadiehsda Nataly',2,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(98,'Flores Novoa Noelle Karolina',3,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(99,'Flores Talavera Mía Montserrat',4,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(100,'García Ortega Liam Jareth',5,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(101,'González Huete Nicole Valentina',6,'F',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(102,'Gutiérrez Galeano José Leonel',7,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(103,'Gutiérrez Guillén Eydan Abdiel',8,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(104,'Gutiérrez Lanuza Marianny Francelly',9,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(105,'Hernández  Olivas Samuel Alejandro',10,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(106,'Hernández Vindell Mathias Sebastian',11,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(107,'Lanuza Gutiérrez Danny Santiago',12,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(108,'Lanuza Valdivia Lucas Andrés',13,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(109,'Lazo Lanuza Alexia Yamileth',14,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(110,'Leiva González Amari Elizabeth',15,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(111,'López Molina Amelia Larissa',16,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(112,'López Polanco Ronald Ariel',17,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(113,'Luna Lanuza Denisse Sofía',18,'F',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(114,'Maradiaga Ortuño Noha Jossary',19,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(115,'Mendoza Molina Yeimy Lucía',20,'F',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(116,'Meza Morales Nacely Guadalupe',21,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(117,'Montoya Salinas José Elias',22,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(118,'Morán Escobar Miguel Ángel',23,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(119,'Moreno Montenegro Gerald de Jesús',24,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(120,'Parrilla Ramírez Anielka Paola',25,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(121,'Peralta Castilblanco María Guadalupe',26,'F',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(122,'Peralta Romero Adriana Michelle',27,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(123,'Picado Espinoza Heliel  Santiago',28,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(124,'Rizo Bellorín Ramón Isaac',29,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(125,'Romero Valle Osmany José',30,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(126,'Rugama López Antony Caleb',31,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(127,'Ruiz Ordóñez Liam David',32,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(128,'Salgado Fletes Leandro José',33,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(129,'Talavera Castro Jerald Joeth',34,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(130,'Talavera López Ariana Isabella',35,'F',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(131,'Téllez Hernández Andrea Solange',36,'F',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(132,'Valenzuela Salinas Jashuary Valentina',37,'F',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(133,'Zeledón Zeledón Ariadne Jessireth',38,'M',NULL,23,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(134,'Alfaro Blandón Ian Mateo',1,'M',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(135,'Benavides Mendoza Ailany Valeria',2,'M',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(136,'Calderón Oporta Karelis  Rosely',3,'M',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(137,'Camas Meneses Valery Lucía',4,'F',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(138,'Casco López Mary Luz',5,'F',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(139,'Castellanos Rayo Dylan Enoc',6,'M',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(140,'Gutiérrez Ibarra Isabella Sofía',7,'F',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(141,'Hidalgo Toruño Sarah Daniela',8,'F',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(142,'Laguna Rodas Angelie Camila',9,'F',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(143,'Lanuza Rivera Máximo',10,'M',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(144,'López Castellón Anthony Mateo',11,'M',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(145,'López Pérez Ariana Ruby',12,'M',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(146,'López Rayo Jocsan Ernesto',13,'M',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(147,'López Rivera Edward Samuel',14,'M',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(148,'Maradiaga Jarquín Marcelo Noe',15,'M',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(149,'Martínez Cárcamo Miguel Matias',16,'M',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(150,'Méndez Meza Mía Valentina',17,'F',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(151,'Morales Illescas Isabella de María',18,'F',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(152,'Moreno Acuña Hanna Sophía',19,'M',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(153,'Navarro Ortez Rodrigo Emiliano',20,'M',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(154,'Osegueda Gómez Caleb Josafat',21,'M',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(155,'Oviedo Rivera Brigitte Monserrat',22,'M',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(156,'Pérez Meza Isamar Isabella',23,'F',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(157,'Ponce Pérez Yerania Isabella',24,'F',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(158,'Preza Alfaro Génesis Lucía',25,'F',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(159,'Rayo Cruz Aishbell Alessia',26,'M',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(160,'Rivas Tórrez Elías Alonso',27,'M',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(161,'Rizo Herrera Irany Joseani',28,'M',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(162,'Rodríguez Lanuza Jeremy Orlando',29,'M',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(163,'Ruiz Gámez Ainhoa Esperanza',30,'M',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(164,'Saldaña González Abdel Sebastian',31,'M',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(165,'Salguera Olivas Joaquín Alexander',32,'M',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(166,'Talavera López Nahiara Sofía',33,'F',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(167,'Talavera Obando Valentina Lisareth',34,'F',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(168,'Toruño Blandón Briana Francella',35,'M',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(169,'Vado Ordóñez Lucas Matheo',36,'M',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(170,'Zeas Lanuza Liam Orlando',37,'M',NULL,24,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(171,'Arauz Hernández Hanna Michelle',1,'M',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(172,'Blandón Obando Matheo Darell',2,'M',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(173,'Caldera Romero Luan Alejandro',3,'M',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(174,'Canales Guevara Mateo Miguel',4,'M',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(175,'Castillo Bravo Lucas Ariel',5,'M',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(176,'Castillo Méndez Alisson Guadalupe',6,'M',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(177,'Castillo Moreno Matthews Johan',7,'M',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(178,'Centeno Toruño María Lucía',8,'F',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(179,'Chavarría González Paula Camila',9,'F',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(180,'Cornavaca Espinoza Elsayra  Nadiela',10,'M',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(181,'Cruz Cárdenas Ángel Adrian',11,'M',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(182,'Cruz Rodríguez Dorian',12,'M',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(183,'Dávila Talavera Valery Sugey',13,'M',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(184,'Domingues Castro Soriana Guadalupe',14,'M',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(185,'Espinoza Benavidez Derrick Josué',15,'M',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(186,'Florian Jiménez Eithan Enmanuel',16,'M',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(187,'Gámez Salgado Diego Alejandro',17,'M',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(188,'González Blandón Amy Giuliana',18,'M',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(189,'Gutiérrez Canales Danny Enmanuel',19,'M',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(190,'Harvey Aguilera Ana Michelle',20,'F',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(191,'Lanuza Palacios Dasha Valentina',21,'F',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(192,'Lazo Machado Thiago Mateo',22,'M',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(193,'Leduc Mayorga Jeremy Gabriel',23,'M',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(194,'López Gutiérrez Adriana Lucía',24,'F',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(195,'López Guzmán Liam Steeven',25,'M',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(196,'López Romero Weslly Josué',26,'M',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(197,'Martínez González Robert Matías',27,'M',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(198,'Ordóñez González Angie Alexandra',28,'M',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(199,'Palacios Castro Diego Alejandro',29,'M',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(200,'Pérez Olivas Lexa Aileen',30,'M',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(201,'Rios Martínez Yuneidy Nicol',31,'M',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(202,'Rodríguez Zelaya Marvin Elias',32,'M',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(203,'Sobalvarro Centeno Fredman Josias',33,'M',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(204,'Soto Moreno Eimy Sophía',34,'M',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(205,'Tinoco Mora Nelson Emmanuel',35,'M',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(206,'Valdez Manzanares Génesis Edith',36,'F',NULL,25,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(207,'Acevedo Albir Sophía Janeth',1,'M',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(208,'Álvarez Moreno José Miguel',2,'M',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(209,'Arauz Hernández Danna Paola',3,'M',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(210,'Cárdenas Hernández Nathan Moisés',4,'M',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(211,'Castellón Andino Hilary Valeria',5,'M',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(212,'Espinoza Blandón Austin Mariano',6,'M',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(213,'Flores Moreno Ian Josué',7,'M',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(214,'Gadea Arauz Noreyda Elena',8,'F',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(215,'Galeano Martínez Camilo Gabriel',9,'M',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(216,'García López Andrew Enmanuel',10,'M',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(217,'García Turniell Santiago Tadeo',11,'M',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(218,'González Chavarría Anderson Guillermo',12,'M',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(219,'González Hoyes Jefren Caleb',13,'M',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(220,'Jiménez Córdoba Daniela Jimena',14,'F',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(221,'Kontorovsky Castillo Moisés',15,'M',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(222,'Laguna Ramos Luis Santiago',16,'M',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(223,'Laínez Lanzas Eithan Gadiel',17,'M',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(224,'Manzanares Padilla Gabriella Elizabeth',18,'M',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(225,'Molina Escalante Andrea Belén',19,'F',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(226,'Obando Romero Angelee María',20,'F',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(227,'Palacios Chavarría Wilder Bladimir',21,'M',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(228,'Pérez Chavarría Harold Emmanuel',22,'M',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(229,'Pineda Escoto Oscar Aveth',23,'M',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(230,'Quintanilla Talavera María Victoria',24,'F',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(231,'Ramos Ráudez Julián David',25,'M',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(232,'Rayo Salas David Alejandro',26,'M',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(233,'Rizo Molina Cristhell Catalina',27,'M',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(234,'Rodríguez Lanuza Emily Anahí',28,'M',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(235,'Rodríguez Nájera Diego Javier',29,'M',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(236,'Saldivar Castillo Itzell Guadalupe',30,'M',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(237,'Toledo Zeledón William David',31,'M',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(238,'Toruño Blandón Kristie Sofía',32,'F',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(239,'Valdivia Abud Guadalupe Alejandra',33,'F',NULL,26,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(240,'Aguilar Lanuza Samuel Enrique',1,'M',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(241,'Altamirano Valle Joalis Ixchel',2,'M',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(242,'Arevalo Aguirre Liam Marcell',3,'M',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(243,'Arias Cuadra Cesia Abigail',4,'F',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(244,'Castillo Suárez Ian Ulises',5,'M',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(245,'Castillo Toruño Jahzara Nauzeth',6,'M',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(246,'Castillo Úbeda Lya Massiel',7,'M',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(247,'Castro Monzón Caroline Lucía',8,'F',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(248,'Centeno Montoya Alex Enmanuel',9,'M',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(249,'Centeno Rodríguez Mia Valentina',10,'F',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(250,'Cruz Cruz Jocsan Caleb',11,'M',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(251,'Duarte Morán María Fernanda',12,'F',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(252,'Flores Hudiel Hanny Yoelis',13,'M',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(253,'Fox González Derick Denvorn',14,'M',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(254,'Gallardo Valenzuela Gael Valentín',15,'M',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(255,'García Aguilar Oswin Alonso',16,'M',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(256,'Garmendia Ruiz Angelys Janaan',17,'M',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(257,'Jarquín Caballero Ian Matias',18,'M',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(258,'López Cárcamo Nahomy Sofía',19,'F',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(259,'López Melgara Anderson Eladio',20,'M',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(260,'Martínez Peralta Ángel Matheo',21,'M',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(261,'Molina López Sofía Isabel',22,'F',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(262,'Moreno Valle Victor Josué',23,'M',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(263,'Peralta Lanuza Ángel Gabriel',24,'M',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(264,'Pérez Moreno Cristhian Said',25,'M',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(265,'Pérez Thomas Leslie Evelia',26,'M',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(266,'Pineda Suárez Jeffrey Gabriel',27,'M',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(267,'Rocha Ruiz Ángel Danilo',28,'M',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(268,'Romero Espino Mariam Sofía',29,'F',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(269,'Romero Matute Emely Yasuri',30,'M',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(270,'Sevilla Vásquez Mateo Alexander',31,'M',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(271,'Soto López Matias Gabriel',32,'M',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(272,'Talavera Cristopher Mateo',33,'M',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(273,'Tórrez Pérez Gabriel',34,'M',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(274,'Valdivia Castillo María Gabriela',35,'F',NULL,27,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(275,'Alaniz Toruño German Sebastian',1,'M',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(276,'Aquino Zelaya Gabriela del Carmen',2,'M',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(277,'Arauz Toruño Norlan José',3,'M',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(278,'Arauz Vallejos Magda Sofía',4,'F',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(279,'Blandón Cárcamo Carlos Sebastan',5,'M',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(280,'Castillo Castillo Freylie Mariel',6,'M',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(281,'Castillo Rodríguez Elieth Alejandra',7,'F',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(282,'Dávila Espinoza Zoe Nazareth',8,'F',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(283,'Flores García Zareth Matias',9,'M',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(284,'Hernández Gutiérrez Mery Sophía',10,'M',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(285,'Herrera Peralta Nolvin Aarón',11,'M',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(286,'Illescas Úbeda María Isabel',12,'F',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(287,'Juárez Olivas Briana Milagro',13,'M',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(288,'Leiva Bermúdez Eskarled Jasmin',14,'M',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(289,'Leiva Gutiérrez Sophía Valentina',15,'F',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(290,'López Cerrato Elias Marcel',16,'M',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(291,'López Rocha Jairo Alberto',17,'M',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(292,'Mendoza Barreda Leandro Manuel',18,'M',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(293,'Mora Toruño David Eduardo',19,'M',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(294,'Morán Zeledón Keysi Xiomara',20,'M',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(295,'Moreno González Mía Valentina',21,'F',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(296,'Moreno Webster Hellfrank Alejandro',22,'M',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(297,'Moya Benavides Arian Bayardo',23,'M',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(298,'Palacios Cerrato Marielis Galilea',24,'M',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(299,'Pérez Lezama Tita Grace',25,'F',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(300,'Pérez Rodríguez Marcela Rachell',26,'M',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(301,'Reyes Córdoba Dunniel Jesús',27,'M',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(302,'Rodríguez Castillo Andrea Nohemi',28,'F',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(303,'Rugama Matute Marlon Nahum',29,'M',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(304,'Ruiz Jarquín Marisabel Lucía',30,'F',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(305,'Tórrez Avilez Eliam Mateo',31,'M',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(306,'Toruño Benavides Perla Karina',32,'M',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(307,'Toruño Cerrato Samara Raiza',33,'M',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(308,'Valenzuela Salgado Marian Nazaret',34,'M',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(309,'Valladares Fierro Maxwell Julian',35,'M',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(310,'Zapata Cerrato Laura Daniela',36,'F',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(311,'Zelaya Gutiérrez Ema Isabella',37,'F',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(312,'Zeledón Lanuza Jordan Jasareth',38,'M',NULL,28,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(313,'Acuña Gámez Dariana Elizabeth',1,'M',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(314,'Aguilera Salgado Yoseling Gabriela',2,'F',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(315,'Altamirano Amador Ayham David',3,'M',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(316,'Barreda Briones Ariel Francisco',4,'M',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(317,'Blandón Florian Celso José',5,'M',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(318,'Britton Canales Nicole Isabel',6,'F',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(319,'Canales Chavarría Adriana Valentina',7,'F',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(320,'Castellanos Jarquín Fernando',8,'M',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(321,'Castillo Caldera Mateo Javier',9,'M',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(322,'Escorcia Rayo Zoe Fiorella',10,'F',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(323,'Espinoza Ruiz Liam Mateo',11,'M',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(324,'Gámez Zeledón Victoria Eleonora',12,'M',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(325,'Gómez Lanuza Daniela Sofía',13,'F',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(326,'Hernández Blandón Mariangely Gabriela',14,'F',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(327,'Hernánez Pérez Joubam Joseth',15,'M',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(328,'Hidalgo Monzón Elmer Yair',16,'M',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(329,'Laguna Rodas Bianka Sofía',17,'F',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(330,'López Salgado Gustavo Rafael',18,'M',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(331,'López Sánchez Lauren Francella',19,'M',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(332,'Mata Cruz Mario Francisco',20,'M',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(333,'Moreno Pineda Enmanuel Alberto',21,'M',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(334,'Novoa Hidalgo Diana Sofía',22,'F',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(335,'Olivares Juárez Valentina Guisselle',23,'F',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(336,'Picado Ruiz Fernanda Nahomy',24,'F',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(337,'Pineda Ventura Alice Sophya',25,'F',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(338,'Ponce Sanabria Ian Jared',26,'M',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(339,'Rivas Rojas Luciano Roberto',27,'M',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(340,'Rivera Amaya Jaycob David',28,'M',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(341,'Rivera Meléndez Lia Alexandra',29,'F',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(342,'Rivera Zelaya María Alejandra',30,'F',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(343,'Rizo Cruz Diego Alonso',31,'M',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(344,'Rodríguez Romero Rodrigo José',32,'M',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(345,'Rodríguez Rugama Oscar Josué',33,'M',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(346,'Rugama Rizo Aarón Santiago',34,'M',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(347,'Sevilla Arauz Camila Aurora',35,'F',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(348,'Sobalvarro Martínez Roger Nicolás',36,'M',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(349,'Tijerino Arauz Erick Santiago',37,'M',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(350,'Urbina Rodríguez Roxanna Guadalupe',38,'M',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(351,'Vallejos Aguirre Victoria Valentina',39,'F',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(352,'Zúniga Tórrez Erick Isaias',40,'M',NULL,29,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(353,'Altamirano Amador Lucas David',1,'M',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(354,'Arauz Cruz Leandro Josué',2,'M',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(355,'Barahona Velásquez Eliam Josué',3,'M',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(356,'Barquero Pérez Claudia Isabel',4,'F',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(357,'Benavides Pérez Andrea Lucía',5,'F',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(358,'Blandón Téllez Génesis Abigail',6,'F',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(359,'Briones Carrero Briana Paola',7,'M',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(360,'Castellón Lanuza Brianys Alexa',8,'M',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(361,'Castillo Arróliga Jhassling Ariam',9,'M',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(362,'Castillo Irias Azuhey Lucía',10,'F',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(363,'Castillo Salinas Flora Lucía',11,'F',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(364,'Cruz Ruiz Maryham Isabella',12,'F',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(365,'Díaz Castillo Emily Francella',13,'M',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(366,'Espinoza Toruño José Emanuel',14,'M',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(367,'Flores Ramírez Marcela Sofía',15,'F',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(368,'Fuentes Rivera Walmor Joao',16,'M',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(369,'García Ramírez Izzy Amelia',17,'M',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(370,'Gómez Moreno Wilbren Matthews',18,'M',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(371,'Gutiérrez Lira April Samara',19,'M',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(372,'Legall Borge Jetmary Valeria',20,'M',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(373,'López Peralta Mariana Isabella',21,'F',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(374,'López Polanco Tadeo Ariel',22,'M',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(375,'Martínez Flores Adriel Said',23,'M',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(376,'Molina Romero Mía Fernanda',24,'M',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(377,'Molina Vílchez Nohelia Marie',25,'M',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(378,'Paladino Velásquez Leandro Marcelo',26,'M',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(379,'Pineda Canales Angie Lucía',27,'F',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(380,'Rayo Espinoza Cristopher Humberto',28,'M',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(381,'Rayo Soza Stephanie Yaiza',29,'M',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(382,'Rizo Andino Jean Carlos',30,'M',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(383,'Roa Palacios Mateo Julian',31,'M',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(384,'Rugama Salgado Tommy Ryan',32,'M',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(385,'Ruiz Ramírez Virginia Lizeth',33,'M',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(386,'Talavera García Mariel Alexandra',34,'M',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(387,'Uriarte Martínez Jeffrey Jacanel',35,'M',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(388,'Valle Benavidez Briana Valeria',36,'M',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(389,'Zelaya Gaitán Hilary Thais',37,'M',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(390,'Zelaya González Lauren Aínes',38,'M',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(391,'Zeledón Garay Lía Samaria',39,'F',NULL,30,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(392,'Altamirano Mejía Milagro Guadalupe',1,'M',NULL,31,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(393,'Andino Castillo Wilfredo Daniel',2,'M',NULL,31,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(394,'Armas Luna Sebastian Andrés',3,'M',NULL,31,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(395,'Benavides Gutiérrez Alexandra Gissell',4,'M',NULL,31,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(396,'Blandón Escorcia María José',5,'F',NULL,31,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(397,'Castro Blandón Jeremy Ibrahim',6,'M',NULL,31,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(398,'Cerrato Blandón Jasson Francisco',7,'M',NULL,31,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(399,'Cruz Ponce Elias Oniel',8,'M',NULL,31,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(400,'Gadea Gutiérrez Alejandra Michelle',9,'F',NULL,31,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(401,'Garay Rivera Ángel Gabriel',10,'M',NULL,31,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(402,'García Escoto Liam Johan',11,'M',NULL,31,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(403,'García Rivera Lian Alonso',12,'M',NULL,31,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(404,'González Talavera Alessandra Sophía',13,'M',NULL,31,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(405,'Gutiérrez Salcedo Carlos Said',14,'M',NULL,31,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(406,'Hernández Alvarado Matthew Joseph',15,'M',NULL,31,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(407,'Illescas Úbeda Ana Sofía',16,'F',NULL,31,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(408,'Laguna Pichardo Alvaro Dominick',17,'M',NULL,31,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(409,'Lanuza Vílchez Danny Matheo',18,'M',NULL,31,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(410,'López López Zoe Lucía',19,'F',NULL,31,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(411,'Machado Flores Jean Carlos',20,'M',NULL,31,'Activo','2026-05-20 15:48:26','2026-05-20 15:48:26'),(412,'Molina Aguilera Fabian Emanuel',21,'M',NULL,31,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(413,'Montalván Gómez Keely Yaiza',22,'M',NULL,31,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(414,'Montenegro Zeledón Natasha Isabella',23,'F',NULL,31,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(415,'Montoya Pineda Estefanía Rocío',24,'M',NULL,31,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(416,'Moreno Sevilla Edward Francisco',25,'M',NULL,31,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(417,'Pérez Pérez Hanna Joly',26,'M',NULL,31,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(418,'Pineda Bermúdez Dominick Matteo',27,'M',NULL,31,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(419,'Quintero Rodríguez José Mariano',28,'M',NULL,31,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(420,'Rodríguez Castillo Ian Mateo',29,'M',NULL,31,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(421,'Saavedra Rodríguez Alvaro Gael',30,'M',NULL,31,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(422,'Smith Gámez Brianda Arisli',31,'M',NULL,31,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(423,'Talavera Vásquez Nathaly Joanna',32,'M',NULL,31,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(424,'Valle Rojas Jimmy Noé',33,'M',NULL,31,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(425,'Vega Torres Mauricio José',34,'M',NULL,31,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(426,'Zeledón Pineda Zaira Fernanda',35,'M',NULL,31,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(427,'Acevedo Castillo Matias Alexander',1,'M',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(428,'Aguilera Meza Noah Samara',2,'M',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(429,'Alsawaleha Payán Bosyh Nuray  ',3,'M',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(430,'Arevalo Aguirre Eliam Alberto',4,'M',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(431,'Benavidez González Yared Emilio',5,'M',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(432,'Betanco Castellón Liss Amanda',6,'M',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(433,'Blandón Moreno Joao Matheo',7,'M',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(434,'Blandón Rocha Mía Nicole',8,'F',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(435,'Canales Díaz Alice del Carmen',9,'M',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(436,'Castillo Dávila Kathia Nicolle',10,'M',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(437,'Castillo González Madeline Lissandra',11,'M',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(438,'Cerrato López Tatiana Sarahí',12,'M',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(439,'Chévez Pérez Ayling Isabella',13,'F',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(440,'Cruz López Lian Matteo',14,'M',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(441,'Dávila Ortiz Diego José',15,'M',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(442,'Espinoza Castro Briana Belén',16,'M',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(443,'Guillén Vásquez Matías André',17,'M',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(444,'Gutiérrez Sirias María Cecilia',18,'F',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(445,'Lanuza Mendoza Celeste Thairis',19,'F',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(446,'López Gómez Edward Joshua',20,'M',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(447,'López González Joseph Leonardo',21,'M',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(448,'Meza Arróliga Wendy Nallely',22,'M',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(449,'Miranda  Gutiérrez Ailish Ariana',23,'M',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(450,'Molina Castellón Leandro Moisés',24,'M',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(451,'Molina Velazques Zoe Celine',25,'F',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(452,'Narvaez Arana Mateo Leonardo',26,'M',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(453,'Ordónez Rizo Jadden Christtoph',27,'M',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(454,'Pérez Benavides Hamlet Rafael',28,'M',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(455,'Ramírez Matute Carlos Jafeht',29,'M',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(456,'Rivera Ramírez Giancarlo',30,'M',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(457,'Rodríguez Benavides Nathaly Giselle',31,'M',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(458,'Soto Torres Liam Gael',32,'M',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(459,'Tinoco Mora Ximena Fernanda',33,'F',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(460,'Urroz Parrilla Bryan Alessandro',34,'M',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(461,'Wingchang Raudez Laura Francella',35,'F',NULL,32,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(462,'Acuña Peralta Osmara Kaory',1,'M',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(463,'Arauz Castillo Kathi Jadelin',2,'M',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(464,'Castillo Barreda Juan Octavio',3,'M',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(465,'Castillo Gutiérrez Daniel Alexander',4,'M',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(466,'Centeno Montoya Ainhoa Yarisbel',5,'M',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(467,'Cruz Cruz Dylan Jaziel',6,'M',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(468,'Escorcia Lagos Cristhian Darell',7,'M',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(469,'Fajardo Robles Yaiza Cristela',8,'M',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(470,'Flores Valdivia Marissa Antonella',9,'F',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(471,'García Hernández Deyra Nahomy',10,'F',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(472,'García López Zoe Ayelen',11,'F',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(473,'Hernández Andino Odalys Marlene',12,'M',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(474,'Laguna Mairena Emelyng Tatianna',13,'M',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(475,'Lanuza Rayo Astrid Camila',14,'F',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(476,'Leiva González Edward Fernando',15,'M',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(477,'López Mejía Valery Dorieth',16,'M',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(478,'López Miranda Angie Isabel',17,'M',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(479,'Molina Alvarado Kenia Thais',18,'M',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(480,'Montenegro Montoya Bismary Guadalupe',19,'M',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(481,'Ordoñez Moreno Carlos Steven',20,'M',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(482,'Palacios Castro Steyci Alejandra',21,'F',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(483,'Pao Hernández Ashley Nicole',22,'F',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(484,'Pérez Rivera Luis Eduardo',23,'M',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(485,'Pérez Sobalvarro María Fernanda',24,'F',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(486,'Rodríguez Morales Ana Mercedes',25,'F',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(487,'Rodríguez Zamora Elias',26,'M',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(488,'Rodríguez Zeledón Allison Noeimy',27,'M',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(489,'Romero Espino Maryangel Alondra',28,'F',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(490,'Rugama Rayo Mirian Yarelis',29,'M',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(491,'Siles Siles Alejandra Lisseth',30,'F',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(492,'Solares Vásquez Iam Marcelo',31,'M',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(493,'Soto Moreno Xadriel Mateo',32,'M',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(494,'Vado Ordóñez Amy Sofía',33,'F',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(495,'Valle Escoto Virgelid Cristal',34,'M',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(496,'Velásquez Ráudez Anthony Sebastian',35,'M',NULL,33,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(497,'Acevedo Castillo Abigail Alessandra',1,'F',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(498,'Amador Úbeda Andrea Belén',2,'F',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(499,'Arauz Zeledón Matheo Enmanuel',3,'M',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(500,'Bendaña Sobalvarro Grace Alessandra',4,'F',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(501,'Blandón Martínez José Miguel',5,'M',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(502,'Blandón Rayo Valery Isabella',6,'F',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(503,'Bonilla Alvarado Andrea Sofía',7,'F',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(504,'Canales Monzón Angeleth Guadalupe ',8,'M',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(505,'Castillo Jiménez Sebastian Andrés',9,'M',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(506,'Castillo Martínez Allyson Nicole',10,'F',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(507,'Castillo Talavera Donald Albeiro',11,'M',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(508,'Chinchilla Benavidez Carlos Enrique',12,'M',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(509,'Espinoza Cornavaca Roseli Nazareth',13,'M',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(510,'Galeano Tinoco Brandon Huzield',14,'M',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(511,'Gámez Meza Ángel Fabricio',15,'M',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(512,'Gómez Cárdenas Dylan Josué',16,'M',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(513,'González Alvarado Jeremias José',17,'M',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(514,'Guerra López Antonella',18,'F',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(515,'Guerrero Betanco Marianne Sophía',19,'M',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(516,'Gutiérrez Mairena Ariel Alejandro',20,'M',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(517,'Hernández Rugama Kirel Alejandra',21,'F',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(518,'Hidalgo Toruño Camila Abisag',22,'F',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(519,'Lagos González Alondra Sofía',23,'F',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(520,'Lovo Chavarría Josué Gabriel',24,'M',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(521,'Martínez Osorio Max Emilio',25,'M',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(522,'Martínez Rayo Josmary Guadalupe',26,'M',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(523,'Meléndez Blandón Mileidy Lismari',27,'M',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(524,'Molina Vargas Vida Isabella',28,'F',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(525,'Montano Pérez Julhian David',29,'M',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(526,'Montoya López Luis Mateo',30,'M',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(527,'Montoya Pineda Elias Emmanuel',31,'M',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(528,'Morales Illescas Mariana Lucía',32,'F',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(529,'Padilla Brenes Génesis Geovanela',33,'M',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(530,'Portobanco Aguilera Edelmary Yissell',34,'M',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(531,'Rivera González Andrew Camilo',35,'M',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(532,'Rugama López Lexy Lorena',36,'M',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(533,'Rugama Romero Amanda Sophía',37,'M',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(534,'Ruiz Jarquín Illiam Mariell',38,'M',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(535,'Tórrez Zeledón Alberto Javier',39,'M',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(536,'Valdivia Cruz Isabella Sofía',40,'F',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(537,'Vargas Blandón Melany Caricsa',41,'M',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(538,'Velásquez Raudez Johan Diroy',42,'M',NULL,34,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(539,'Acevedo Zelaya Javier Andrés',1,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(540,'Aquino Zelaya Víctor José',2,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(541,'Blandón Valdez Gustavo Enrique',3,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(542,'Calero Flores Ruby Isabella',4,'F',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(543,'Cardoza Mairena Yaried Antonio',5,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(544,'Castillo Montoya Angelly Rashell',6,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(545,'Castillo Rivera Sofía Nicolle',7,'F',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(546,'Espinoza Montoya José Gabriel',8,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(547,'Gallardo Valenzuela Anthony Gabriel',9,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(548,'Garmendia Godoy Silvia Marcela',10,'F',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(549,'Gómez Aguilar Andrea Marcela ',11,'F',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(550,'Gómez Flores Mateo Sebastian',12,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(551,'Gutiérrez Gutiérrez Jeyson Gabriel',13,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(552,'Gutiérrez Vásquez Joelis Nazareth',14,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(553,'Jiménez Téllez Elias José',15,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(554,'Lau Casco Luz Argentina ',16,'F',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(555,'López Zelaya Mateo Ernesto',17,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(556,'Maldonado Huete Isaac Darío',18,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(557,'Matute Zapata Christopher Iván',19,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(558,'Mendoza Zelaya Leandro Matias',20,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(559,'Peralta Talavera Sergio Leandro ',21,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(560,'Peralta Vargas Dariela José ',22,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(561,'Pérez Barreda Isabella Marieth ',23,'F',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(562,'Quintero Pérez Jefferson Amaru',24,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(563,'Rios Centeno Francisco José',25,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(564,'Rivera Martínez Lucero Alejandra',26,'F',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(565,'Rodríguez Marín Suri Dariana',27,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(566,'Rodríguez Urbina Arianna Celeste',28,'F',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(567,'Romero Peña Mathias Jesús',29,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(568,'Rugama Jirón Jorge Antonio',30,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(569,'Rugama Martínez Diego Nicolas',31,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(570,'Ruiz Arauz Luciana Sofía',32,'F',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(571,'Ruiz Cruz Liham Marcela ',33,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(572,'Ruiz Hidalgo Luis Alberto',34,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(573,'Ruiz Rivera Marians Rachell',35,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(574,'Salguera Olivas Rolando Josué',36,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(575,'Toruño Valles Asia Sophía',37,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(576,'Valdez Arauz Renata Loana ',38,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(577,'Valenzuela Valdivia Valeria',39,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(578,'Vega López Sofía Vanessa',40,'F',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(579,'Zapata Cerrato Fernando Antonio',41,'M',NULL,35,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(580,'Aguirre Ruiz Ana Valentina',1,'F',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(581,'Alsawaleha Payán Nael Naim ',2,'M',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(582,'Arauz Blandón Hashly Nicole',3,'F',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(583,'Argüello Caballero Lidice Ximena',4,'F',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(584,'Barrientos Torrez Noel Ivan',5,'M',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(585,'Benavides Martínez Frida Isabella',6,'F',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(586,'Bermúdez Lazo Edward Nadir',7,'M',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(587,'Chavarría Arosteguí Valentina Montserrat',8,'F',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(588,'Chavarría Ponce Santiago Andrés',9,'M',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(589,'Chávez Mairena Engels David',10,'M',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(590,'Córdoba Valle Alondra Sharlotte',11,'F',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(591,'Gómez Espino Liam Mateo',12,'M',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(592,'Gutiérrez Hernández Oscar Emmanuel',13,'M',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(593,'Gutiérrez Meza Ian Mateo',14,'M',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(594,'Laguna Escalante Matías Sebastian',15,'M',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(595,'Leiva García James Alejandro',16,'M',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(596,'López Tórrez Joshua Alexander',17,'M',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(597,'Manzanares Rodríguez Mia Elizabeth  ',18,'M',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(598,'Matute Cruz Anthony Jafeb',19,'M',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(599,'Molina Mairena Luciana Belén',20,'M',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(600,'Molina Valdivia Jhoana Vanessa',21,'F',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(601,'Montenegro González Cristal Angelina',22,'M',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(602,'Montenegro Rivera April Carolain',23,'M',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(603,'Moreno Alaníz  Mairim Nicole ',24,'F',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(604,'Olivas Ruiz Danna Sofía',25,'F',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(605,'Peralta Tórrez Jazleny Fernanda',26,'M',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(606,'Pineda Bermúdez Ian Marcelo',27,'M',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(607,'Rivas Figueroa Zoe Marian',28,'F',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(608,'Rivera Velásquez Aslie Daniela',29,'F',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(609,'Rizo Valdivia Ariany Sofía',30,'F',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(610,'Ruiz Gutiérrez Angie Sofía',31,'F',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(611,'Ruiz Vallejos Sharon Arianna',32,'M',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(612,'Sáenz Lorio Miguel Mathias',33,'M',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(613,'Salazar Aguirre Mateo Dimaria',34,'M',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(614,'Siles Siles Mathias Alejandro',35,'M',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(615,'Tinoco Mora Ana María',36,'F',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(616,'Toruño Martínez Diosy Lenara',37,'M',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(617,'Valdivia Montenegro Alejandra Belén',38,'F',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(618,'Vargas Gutiérrez María Guadalupe ',39,'F',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(619,'Virji Bustamante Irfan Zahid',40,'M',NULL,36,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(620,'Aguilar Benavides Farid Zahir',1,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(621,'Alaníz Altamirano Brandy Said',2,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(622,'Altamirano Valle Bliss Azury',3,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(623,'Álvarez Gutiérrez Matheo Antonio',4,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(624,'Barahona Velásquez Fernanda Isabella',5,'F',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(625,'Briones Lazo Génesis Nazareth',6,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(626,'Castillo Zeledón Vivian Marcela',7,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(627,'Cornavaca Arce Marvin Emilio',8,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(628,'Cruz Aguilar Itzel Zuneydi',9,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(629,'Flores Novoa Neymar José',10,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(630,'Flores Valdivia Silvio José',11,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(631,'Gadea Meneses  Anaid Francela ',12,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(632,'Guatemala Chavarría Kheany Vanessa',13,'F',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(633,'Gutiérrez Ibarra Norman Antonio',14,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(634,'Hernández Rugama Alexa Nohemy',15,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(635,'Laguna Ramos Roberto Gabriel',16,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(636,'Leiva Benavides Eliazar Noé',17,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(637,'López Rayo Joel Eduardo',18,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(638,'Martínez Rodríguez Allisson Dayana',19,'F',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(639,'Martínez Talavera Alonzo Tadeo',20,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(640,'Méndez Meza Hannah Alessandra',21,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(641,'Mendoza Mejía Noel Alberto',22,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(642,'Menjivar Blandón Amy Guadalupe',23,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(643,'Moli Gutiérrez Gabriel Alexander',24,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(644,'Navarro Ortez Diego Tomás',25,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(645,'Obando Lorente Ainara Lucía',26,'F',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(646,'Orozco Peralta Adriana Angelia',27,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(647,'Pineda Martínez Pedro Enmanuel',28,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(648,'Quintero García Ariana Elizabeth',29,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(649,'Rivas Tórrez José Gabriel',30,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(650,'Rizo Gutiérrez Christy Gisselle',31,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(651,'Rodríguez Torres Indira Nahomy',32,'F',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(652,'Ruiz Mendieta Diego Alejandro',33,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(653,'Sánchez Somarriba Iker Gerardo',34,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(654,'Sobalvarro Zamora Xochilth Antonella',35,'F',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(655,'Torres Hernández Mared Marcela ',36,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(656,'Toruño Talavera Cristian Alejandro',37,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(657,'Vargas Blandón Brytany Jamileth',38,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(658,'Zamora Altamirano Liam Mateo',39,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(659,'Zeledón Rodríguez Ariana',40,'M',NULL,37,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(660,'Benavidez González André Said',1,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(661,'Betanco Cerrato Krysta Isabella ',2,'F',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(662,'Casco Hernández Mariam Guadalupe',3,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(663,'Castro Rocha Francesco Ernesto',4,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(664,'Centeno Espinoza Sharon Nicole',5,'F',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(665,'Cruz Blandón Oscar Mateo',6,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(666,'Cruz Cruz Alexa Denisse',7,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(667,'Flores Rayo Silgian',8,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(668,'Gámez Zeledón Bruno Ernesto',9,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(669,'García Palacios Franchesca Elena',10,'F',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(670,'García Sevilla Dereck Said',11,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(671,'Gutiérrez Elsis Lisseth',12,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(672,'Gutiérrez Blandón Mayce Betzabé',13,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(673,'Gutiérrez Maradiaga Leandro Omar',14,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(674,'Herrera Montenegro Isaias Tadeo',15,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(675,'Hidalgo Pichardo  Mia Isabella  ',16,'F',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(676,'Larios Meza Evans Reynaldo',17,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(677,'Lira Salgado Arjen Isaac',18,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(678,'Luna Úbeda Lucas Mateo',19,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(679,'Martínez Osorio José Joaquín',20,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(680,'Molina Montenegro Cristiana Sophia',21,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(681,'Montalván Gómez Kassy Francella ',22,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(682,'Montoya Rivas Alex Enmanuel',23,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(683,'Morán Blandón José Antonio',24,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(684,'Moreno Blandón Sofía Isabella',25,'F',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(685,'Moreno Sevilla Jossiel Eduardo',26,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(686,'Obando Morán Rosita María',27,'F',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(687,'Rivera Zelaya Junaysi Alessandra',28,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(688,'Rocha Rayo Ángel Leonardo ',29,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(689,'Rodríguez Alfaro Oscar Francisco',30,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(690,'Rodríguez Flores Osmary Lisbeth',31,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(691,'Roñac Lagos Sophía Isabella',32,'F',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(692,'Salgado Arce Dylan Rodrigo ',33,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(693,'Salgado Gámez Mathias Isaí',34,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(694,'Tórrez Castillo Leandro Antonio ',35,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(695,'Toruño Benavides Cristhel Massiel',36,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(696,'Ubau Rivera Ivania Estela',37,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(697,'Vallejos Gutiérrez Francis Alexandra',38,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(698,'Vallejos Pineda Manuel Alejandro',39,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(699,'Zelaya Valenzuela Dylan Adael',40,'M',NULL,38,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(700,'Blandón Cerrato Michell Danisa',1,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(701,'Castillo Badilla Isabella',2,'F',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(702,'Centeno Toruño  Roger Alejandro',3,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(703,'Cruz Gutiérrez Nahiara Ahtziri',4,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(704,'Duarte Castro Anthony Josué',5,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(705,'Escorcia Montecinos José Raúl',6,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(706,'Escorcia Rodríguez Anghelina Nicolle',7,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(707,'Espinoza Ruiz Thais Nicole',8,'F',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(708,'García Altamirano Alicia Guadalupe',9,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(709,'García Valle Roberth Joab',10,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(710,'González Almendarez Mateo Emmanuel ',11,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(711,'González Espinoza Stephany Loana',12,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(712,'González Rodríguez Verónica Renee',13,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(713,'Hernández Calderón Josias José',14,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(714,'Herrera Padilla Gabriela Cecilia',15,'F',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(715,'Herrera Tinoco Marelyn Dayanara',16,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(716,'Hurtado Lira Ly Esmeralda ',17,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(717,'Lazo Machado Edwin Joao',18,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(718,'Martínez Gutiérrez Emiliano Enrique',19,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(719,'Mendoza Camas Laikel',20,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(720,'Mendoza Tinoco Aarón Emmanuel ',21,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(721,'Montenegro Colindre Sixela Dayana',22,'F',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(722,'Montenegro Merlo Andrea Camila',23,'F',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(723,'Moreno Peralta Anthony Jahasiel',24,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(724,'Moreno Torres Alondra Sofía',25,'F',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(725,'Olivas Larios Alisson Nicole',26,'F',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(726,'Palacios Molina Marcos Antonio',27,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(727,'Palma Ruiz  Joan Matteo',28,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(728,'Reyes Galeano Víctor Javier',29,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(729,'Rodríguez Paiz Diego Mateo',30,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(730,'Rodríguez Salgado Alexa Milagros',31,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(731,'Rugama Castillo Ethan Sebastian ',32,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(732,'Rugama Cruz Deyvi Gabriel ',33,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(733,'Salgado Rodríguez Arliz Julianna',34,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(734,'Sandino Lanuza Carlos Gabriel',35,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(735,'Talavera Palacios María José',36,'F',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(736,'Tórrez Cárdenas Kevin Daniel',37,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(737,'Urrutia Rodríguez María Celeste',38,'F',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(738,'Valdivia Castillo Sebastian',39,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(739,'Vargas Rodríguez Elton José',40,'M',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(740,'Velásquez Picado Isabella Sofía',41,'F',NULL,39,'Activo','2026-05-20 15:48:27','2026-05-20 15:48:27'),(741,'Fracela Arauz   Fracela Arauz',2,'M',2026,22,'Inactivo','2026-05-25 04:38:09','2026-05-25 04:39:30'),(742,'Fracela Arauz   Fracela Arauz',2,'F',2026,22,'Inactivo','2026-05-25 04:39:38','2026-05-25 04:42:29'),(743,'Fracela Arauz   Fracela Arauz',6,'F',2026,22,'Inactivo','2026-05-25 04:42:35','2026-05-25 16:22:09'),(744,'Fracela Arauz   Fracela Arauz',3,'F',2026,22,'Inactivo','2026-05-25 15:13:23','2026-05-25 16:21:59'),(745,'jose',3,'M',NULL,22,'Activo','2026-05-25 15:14:53','2026-05-25 16:21:59'),(746,'Fracela Arauz   Fracela Arauz',2,'F',2026,22,'Activo','2026-05-25 15:21:37','2026-05-25 16:19:08'),(747,'jose',1,'M',2026,22,'Activo','2026-05-25 16:19:08','2026-05-25 16:19:08'),(748,'Fracela Arauz   Fracela Arauz',1,'F',2026,NULL,'Activo','2026-05-25 16:24:27','2026-05-25 16:24:27');
/*!40000 ALTER TABLE `estudiantes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
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
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
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
-- Table structure for table `maestros`
--

DROP TABLE IF EXISTS `maestros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `maestros` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `estado` int(11) DEFAULT NULL,
  `genero` char(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `maestros_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maestros`
--

LOCK TABLES `maestros` WRITE;
/*!40000 ALTER TABLE `maestros` DISABLE KEYS */;
INSERT INTO `maestros` VALUES (1,'Carlos Mendoza',0,'M','2026-05-11 03:38:34','2026-05-13 17:20:00'),(2,'María González',1,'F','2026-05-11 03:38:34','2026-05-11 03:38:34'),(3,'José Ramírez',0,'M','2026-05-11 03:38:34','2026-05-14 16:10:43'),(4,'Ana Martínez',1,'F','2026-05-11 03:38:34','2026-05-24 04:01:37'),(8,'Kevin',1,'M','2026-05-12 16:47:20','2026-05-12 16:47:38'),(10,'Kevin12_10',1,'F','2026-05-12 17:08:00','2026-05-12 17:08:00'),(11,'Kevin12_11',0,'F','2026-05-12 17:08:00','2026-05-13 17:19:43'),(12,'Francisco',1,'M','2026-05-14 16:25:29','2026-05-14 16:25:29'),(13,'Francisco 5012',1,'M','2026-05-14 16:33:45','2026-05-14 16:33:45'),(14,'Jose505',1,'F','2026-05-14 16:34:15','2026-05-14 16:34:15'),(15,'Luis Fernando López',1,'M','2026-05-18 03:07:04','2026-05-18 03:07:04'),(16,'Martha Isabel Rivas',1,'F','2026-05-18 03:07:04','2026-05-18 03:07:04'),(17,'Roberto Carlos Méndez',0,'M','2026-05-18 03:07:04','2026-05-18 03:07:04'),(18,'Andrea Paola Zúñiga',0,'F','2026-05-18 03:07:04','2026-05-23 23:44:22'),(19,'Fernando José Calderón',1,'M','2026-05-18 03:07:04','2026-05-18 03:07:04'),(20,'Silvia Elena Morales',0,'F','2026-05-18 03:07:04','2026-05-18 03:07:04');
/*!40000 ALTER TABLE `maestros` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_05_10_213641_create_maestros_table',1),(5,'2026_05_10_213642_create_cortes_table',1),(6,'2026_05_10_213642_create_secciones_table',1),(7,'2026_05_10_213643_create_estudiantes_table',1),(8,'2026_05_10_213644_create_asistencias_maestros_table',1),(9,'2026_05_10_213644_create_asistencias_table',1),(10,'2026_05_10_213645_create_reportes_table',1),(11,'2026_05_10_220748_drop_deleted_at_from_estudiantes_table',2),(12,'2026_05_12_184100_add_estado_to_secciones_table',3),(13,'2026_05_12_184105_add_estado_to_secciones_table',3),(14,'2026_05_14_101824_add_unique_index_to_maestros_name',4),(15,'2026_05_14_101837_add_unique_index_to_maestros_name',5),(17,'2026_05_19_173804_make_id_seccion_nullable_in_reportes_table',6),(18,'2026_05_19_173810_make_id_seccion_nullable_in_reportes_table',6),(20,'2026_05_25_172658_create_backups_table',7);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reportes`
--

DROP TABLE IF EXISTS `reportes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reportes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_seccion` bigint(20) unsigned DEFAULT NULL,
  `cef` int(11) NOT NULL DEFAULT 0,
  `cem` int(11) NOT NULL DEFAULT 0,
  `crf` int(11) NOT NULL DEFAULT 0,
  `crm` int(11) NOT NULL DEFAULT 0,
  `fecha` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tipo` enum('estudiante','maestro') DEFAULT 'estudiante',
  `id_maestro` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reportes_id_seccion_foreign` (`id_seccion`),
  CONSTRAINT `reportes_id_seccion_foreign` FOREIGN KEY (`id_seccion`) REFERENCES `secciones` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reportes`
--

LOCK TABLES `reportes` WRITE;
/*!40000 ALTER TABLE `reportes` DISABLE KEYS */;
INSERT INTO `reportes` VALUES (37,NULL,5,5,4,4,'2026-05-20','2026-05-20 17:39:41','2026-05-20 17:39:41','maestro',NULL),(48,NULL,5,5,0,0,'2026-05-21','2026-05-23 22:14:30','2026-05-23 22:14:30','maestro',NULL),(49,NULL,5,5,5,1,'2026-05-04','2026-05-23 22:14:32','2026-05-23 22:14:32','maestro',NULL),(59,22,15,23,8,21,'2026-06-01','2026-05-23 23:28:11','2026-05-23 23:28:11','estudiante',NULL),(60,NULL,5,5,0,0,'2026-06-01','2026-05-23 23:28:11','2026-05-23 23:28:11','maestro',NULL),(61,22,15,23,12,22,'2026-04-08','2026-05-23 23:29:16','2026-05-23 23:29:16','estudiante',NULL),(62,NULL,5,5,0,0,'2026-04-08','2026-05-23 23:29:16','2026-05-23 23:29:16','maestro',NULL),(63,22,15,23,12,21,'2026-01-07','2026-05-23 23:29:32','2026-05-23 23:29:32','estudiante',NULL),(64,NULL,5,5,0,0,'2026-01-07','2026-05-23 23:29:32','2026-05-23 23:29:32','maestro',NULL),(69,NULL,5,5,5,5,'2026-05-23','2026-05-25 02:18:03','2026-05-25 02:18:03','maestro',NULL),(70,NULL,5,5,0,0,'2026-05-24','2026-05-25 02:18:06','2026-05-25 02:18:06','maestro',NULL),(71,22,14,23,13,21,'2026-05-24','2026-05-25 02:18:16','2026-05-25 02:19:08','estudiante',NULL);
/*!40000 ALTER TABLE `reportes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `secciones`
--

DROP TABLE IF EXISTS `secciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `secciones` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `id_maestro_guia` bigint(20) unsigned DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `secciones_id_maestro_guia_foreign` (`id_maestro_guia`),
  CONSTRAINT `secciones_id_maestro_guia_foreign` FOREIGN KEY (`id_maestro_guia`) REFERENCES `maestros` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `secciones`
--

LOCK TABLES `secciones` WRITE;
/*!40000 ALTER TABLE `secciones` DISABLE KEYS */;
INSERT INTO `secciones` VALUES (22,'1ro A',NULL,1,'2026-05-20 15:40:42','2026-05-20 15:40:42'),(23,'1ro B',NULL,1,'2026-05-20 15:40:42','2026-05-20 15:40:42'),(24,'1ro C',NULL,1,'2026-05-20 15:40:42','2026-05-20 15:40:42'),(25,'2do A',NULL,1,'2026-05-20 15:40:42','2026-05-20 15:40:42'),(26,'2do B',NULL,1,'2026-05-20 15:40:42','2026-05-20 15:40:42'),(27,'2do C',NULL,1,'2026-05-20 15:40:42','2026-05-20 15:40:42'),(28,'3ro A',NULL,1,'2026-05-20 15:40:42','2026-05-20 15:40:42'),(29,'3ro B',NULL,1,'2026-05-20 15:40:42','2026-05-20 15:40:42'),(30,'3ro C',NULL,1,'2026-05-20 15:40:42','2026-05-20 15:40:42'),(31,'4to A',NULL,1,'2026-05-20 15:40:42','2026-05-20 15:40:42'),(32,'4to B',NULL,1,'2026-05-20 15:40:42','2026-05-20 15:40:42'),(33,'4to C',NULL,1,'2026-05-20 15:40:42','2026-05-20 15:40:42'),(34,'5to A',NULL,1,'2026-05-20 15:40:42','2026-05-20 15:40:42'),(35,'5to B',NULL,1,'2026-05-20 15:40:42','2026-05-20 15:40:42'),(36,'5to C',NULL,1,'2026-05-20 15:40:42','2026-05-20 15:40:42'),(37,'6to A',NULL,1,'2026-05-20 15:40:42','2026-05-20 15:40:42'),(38,'6to B',NULL,1,'2026-05-20 15:40:42','2026-05-20 15:40:42'),(39,'6to C',NULL,1,'2026-05-20 15:40:42','2026-05-20 15:40:42');
/*!40000 ALTER TABLE `secciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
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
INSERT INTO `sessions` VALUES ('L6xT3Ez61FvNPoQ8wKoaybi36y8ENHKclf3hW8wu',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiTXRaWWl1eHF4M3VFaTBoM2xUdElMbHFMcmNPNnVIVmpwcWpFWG43diI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly9hc2lzdGVuY2lhLm5pL2JhY2t1cHMvY3JlYXRlIjtzOjU6InJvdXRlIjtzOjE0OiJiYWNrdXBzLmNyZWF0ZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1779808284),('LqVm5N5XZWTeW6EeXOTqF9ehoCd4EDwt5rRWAm89',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZVFBZzZUcjliNmE4U0g5dThDakNGNERRSG9XUllrSTVJRXpvanNoNSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly9hc2lzdGVuY2lhLm5pL2JhY2t1cHMvY3JlYXRlIjtzOjU6InJvdXRlIjtzOjE0OiJiYWNrdXBzLmNyZWF0ZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1779752416),('Q5BUGbHGJl56LTFoWB8x29HXKGIg99z764C9rcj8',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36 Edg/148.0.0.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiaW02d3pJbGhDb3R1WFNCR1ltMTBkbkc3Q3VaM2ZsRk02dGFUMWNieCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHA6Ly9hc2lzdGVuY2lhLm5pL2VzdHVkaWFudGVzIjtzOjU6InJvdXRlIjtzOjE3OiJlc3R1ZGlhbnRlcy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1779728325),('z7ZCnBaM4SU3XPeRqibhk8pucWXLTiBiQKIqmXRT',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoibFZZVHF4cXRzNkU5UE9aSUZOdWdLRjl4MmlBS3dxdUIzeXpEa1JRRyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly9hc2lzdGVuY2lhLm5pL3JlcG9ydGUtYXVzZW5jaWFzIjtzOjU6InJvdXRlIjtzOjE3OiJyZXBvcnRlLWF1c2VuY2lhcyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1779738258);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_asistencia`
--

DROP TABLE IF EXISTS `tipos_asistencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipos_asistencia` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo` char(1) NOT NULL,
  `nombre` varchar(20) NOT NULL,
  `es_presente` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tipos_asistencia_codigo_unique` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_asistencia`
--

LOCK TABLES `tipos_asistencia` WRITE;
/*!40000 ALTER TABLE `tipos_asistencia` DISABLE KEYS */;
INSERT INTO `tipos_asistencia` VALUES (1,'P','Presente',1,'2026-05-21 21:05:46','2026-05-21 21:05:46'),(2,'A','Ausente',0,'2026-05-21 21:05:46','2026-05-21 21:05:46'),(3,'J','Justificado',0,'2026-05-21 21:05:46','2026-05-21 21:05:46'),(4,'T','Llegada tarde',1,'2026-05-21 21:05:46','2026-05-21 21:05:46');
/*!40000 ALTER TABLE `tipos_asistencia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-26  9:22:53
