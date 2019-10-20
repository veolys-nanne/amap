-- MySQL dump 10.13  Distrib 8.0.15, for Linux (x86_64)
--
-- Host: localhost    Database: amaphommesdeterre
-- ------------------------------------------------------
-- Server version	8.0.15

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
 SET NAMES utf8mb4 ;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `basket`
--

LOCK TABLES `basket` WRITE;
/*!40000 ALTER TABLE `basket` DISABLE KEYS */;
/*!40000 ALTER TABLE `basket` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `migration_versions`
--

LOCK TABLES `migration_versions` WRITE;
/*!40000 ALTER TABLE `migration_versions` DISABLE KEYS */;
/*!40000 ALTER TABLE `migration_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `product`
--

LOCK TABLES `product` WRITE;
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
INSERT INTO `product` VALUES (1,3,'Légumes panier',15.00,1,1),(2,3,'Légumes petit panier',10.00,1,2),(3,4,'Pizza pour 2 personnes',8.00,1,1),(4,4,'Baguette 350 g',1.45,1,2),(5,4,'Pain T65 blanc 700 g',3.25,1,3),(6,4,'Pain T65 blanc 1 kg',4.20,1,4),(7,4,'Pain T80 demi-complet nature 700 g',3.25,1,5),(8,4,'Pain T80 demi-complet nature 1 kg',4.20,1,6),(9,4,'Pain T150 intégral nature 700 g',3.25,1,7),(10,4,'Pain T150 intégral nature 1 kg',4.20,1,8),(11,4,'Pain T150 avec graines Sésame 700 g',3.60,1,9),(12,4,'Pain T150 avec graines Sésame 1kg',4.95,1,10),(13,4,'Pain T150 avec graines tournesol 700 g',3.60,1,11),(14,4,'Pain T150 avec graines tournesol 1kg',4.95,1,12),(15,4,'Pain T150 avec graines pavot 700 g',3.60,1,13),(16,4,'Pain T150 avec graines pavot 1kg',4.95,1,14),(17,4,'Pain T80 avec graines noix 700 g',4.30,1,15),(18,4,'Pain T80 avec graines noix 1kg',5.90,1,16),(19,4,'T80 raisins secs 700g',4.30,1,17),(20,4,'T80 raisins secs 1kg',5.90,1,18),(21,4,'T150 graines de lin 700g',3.60,1,19),(22,4,'T150 graines de lin 1kg',4.95,1,20),(23,4,'Pain brioché 600g simple',3.05,1,21),(24,4,'Pain brioché 600g aux raisins',3.35,1,22),(25,4,'Pain brioché 600g au chocolat',3.35,1,23),(26,4,'Pain épeautre 700g',3.90,1,24),(27,4,'Sablé Nature',0.95,1,25),(28,4,'Sablé Raisin',0.95,1,26),(29,4,'Sablé Chocolat',0.95,1,27),(30,4,'Sablé Sésame',0.95,1,28),(31,5,'Lait cru entier 1 litre',1.00,1,1),(32,5,'Tome Part 285 g',4.00,1,2),(33,5,'Tome Part 500 g',7.00,1,3),(34,6,'Le Crottin - frais',2.15,1,1),(35,6,'Le Crottin - demi-sec',2.15,1,2),(36,6,'Le Crottin - sec',2.15,1,3),(37,6,'Le petit Motte - frais',2.75,1,4),(38,6,'Le petit Motte - demi-sec',2.75,1,5),(39,6,'Le petit Motte - sec',2.75,1,6),(40,6,'Le petit Motte  - poivre',2.85,1,7),(41,6,'Le petit Motte  - estragon',2.85,1,8),(42,6,'Le petit Motte  - ciboulette',2.85,1,9),(43,6,'Le petit Motte échalote',2.85,1,10),(44,6,'La Brique',3.25,1,11),(45,6,'La Faisselle',0.90,1,12),(46,6,'La grande Faisselle',3.00,1,13),(47,7,'Canard - Foie gras entier (bocal 180g)',26.00,1,1),(48,7,'Canard - Foie gras entier mi-cuit (sous-vide) 100g',14.00,1,2),(49,7,'Canard - Foie gras entier mi-cuit (sous-vide) 200g',28.00,1,3),(50,7,'Canard - Mousse de foie 190g',16.00,1,4),(51,7,'Canard - Mousse de foie 95g',8.50,1,5),(52,7,'Canard - Mousse de foie 65g',6.50,1,6),(53,7,'Canard - Pâté de Livet 125g',7.00,1,7),(54,7,'Canard - Cou de canard farci 400g',18.00,1,8),(55,7,'Canard - Rillettes pur canard 200g',6.80,1,9),(56,7,'Canard - Rillettes pur 90g',4.00,1,10),(57,7,'Canard - Rillettes au foie gras 200g',10.00,1,11),(58,7,'Canard - Gésiers 320g',7.00,1,12),(59,7,'Canard - Confits 2 cuisses 600g',15.00,1,13),(60,7,'Canard - Confits 2 magrets 750g',16.00,1,14),(61,7,'Magret séché la pièce (sous-vide) 250g',12.50,1,15),(62,7,'Magret séché tranché (sous-vide) 150g',10.50,1,16),(63,7,'Canard au cidre (bocal 700g)',12.50,1,17),(64,7,'Oie - Foie gras entier (bocal 180g)',29.00,1,18),(65,7,'Oie - Foie gras entier mi-cuit (sous-vide) 100g',16.00,1,19),(66,7,'Oie - Foie gras entier mi-cuit (sous-vide) 200g',32.00,1,20),(67,7,'Oie - Foie gras entier mi-cuit (sous-vide) 300g',48.00,1,21),(68,7,'Oie - Mousse de foie 190g',17.00,1,22),(69,7,'Oie - Mousse de foie 95g',9.50,1,23),(70,7,'Oie - Mousse de foie 65g',7.00,1,24),(71,7,'Oie - Pâté de Livet 125g',7.00,1,25),(72,7,'Oie - Rillettes pure 200g',6.80,1,26),(73,7,'Oie - Rillettes pure 90g',4.00,1,27),(74,7,'Oie - Gésiers 320g',7.00,1,28),(75,7,'Oie - Confits aile et cuisse 850g',16.00,1,29),(76,7,'Oie - Confits 2 magrets 750g',16.00,1,30),(77,7,'Oie - Cassoulet au confit d\'oie 750g',13.50,1,31),(78,7,'Oie - Graisse d\'oie 300g',3.50,1,32),(79,8,'Jus de pomme 75cl',2.10,1,1),(80,9,'Miel toutes fleurs 500 g',5.00,1,1),(81,10,'6 Œufs',2.00,1,1),(82,10,'Poulet entier bio - 2 à 2,4 kg @ 9,90 € / kg',0.00,1,2),(83,10,'Poulet bio - 2 cuisses sous vide @ 9,95 € / kg',0.00,1,3),(84,10,'Poulet bio - 2 blancs sous vide @ 20 € / kg',0.00,1,4),(85,11,'Beurre doux (plaquette de 250g.)',2.80,1,1),(86,11,'Beurre demi-sel (plaquette de 250g.)',2.80,1,2),(87,11,'Crème 25 cl',2.50,1,3),(88,11,'Crème 50 cl',4.50,1,4),(89,11,'Fromage blanc battu 50cl',2.50,1,5),(90,12,'Gigli (250 g) - Epeautre et blé des Pharaons',2.90,1,1),(91,12,'Crête de Coq(250g)- Epeautre/blé des Pharaons',2.90,1,2),(92,12,'Radiatori (250 g) - Epeautre et blé des Pharaons',2.90,1,3),(93,12,'P\'tite Tini (1 kg) - Epeautre et blé des Pharaons',7.50,1,4),(94,12,'P\'tite Tini (250g) - Engrain (pauvre en gluten)',2.90,1,5),(95,12,'La P\'tite Tini (250 g) - Sarrasin (sans gluten)',2.90,1,6),(96,12,'Gigli (250 g) - Tomate Basilic',2.90,1,7),(97,12,'Crête de Coq (250g) - Citron & gingembre',2.90,1,8),(98,12,'Radiatori (250g) - Curry Indien',2.90,1,9),(99,12,'Lumaconi (250g - Persil Ail',2.90,1,10),(100,12,'Amore Mio (250g) - Orange Romarin',2.90,1,11),(101,12,'Fusilli Trio de Légumes (250g)',2.90,1,12),(102,12,'Pois cassés du Perche (500g)',3.00,1,13),(103,12,'Lentillons du Perche (500 g)',3.00,1,14),(104,12,'Lentilles Beluga du Perche (500g)',3.50,1,15),(105,12,'Boulgour de blé des Pharaons (400g)',3.50,1,16),(106,12,'Semoule de blé des Pharaons (400g)',3.50,1,17),(107,13,'Spiruline fraîche (150 g.)',7.50,1,1),(108,13,'Spiruline sèche en poudre (100g.)',18.00,1,2),(109,13,'Spiruline sèche Brindilles (100g.)',18.00,1,3),(110,13,'Spirtonic (50g.)',9.50,1,4),(111,13,'Spirumix (100g.)',4.00,1,5),(112,14,'Pont l\'Evêque affiné',4.50,1,1),(113,14,'Pont l\'Evêque à affiner',4.50,1,2);
/*!40000 ALTER TABLE `product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `product_quantity`
--

LOCK TABLES `product_quantity` WRITE;
/*!40000 ALTER TABLE `product_quantity` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_quantity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,NULL,'amaphommesdeterre@yahoo.fr','admin','amaphommesdeterre',NULL,'Cambremer',14340,NULL,'$2y$13$9BPPJ9aEyN.8ErcawzfnpOdeWcu7JschbdYmAflRU96RdnGvDjaqm',1,'a:1:{i:0;s:10:\"ROLE_ADMIN\";}',NULL,NULL),(2,1,'nicolas.anne@laposte.net','Nicolas','ANNE','2180, route des bois de Bayeux','Montreuil-en-auge',14340,'0614264681','$2y$13$YWcbZXThL.hDdlbuLJhDBOKms33XYiWQ95FvbegIyeW4IuxYl6EjC',1,'a:2:{i:0;s:11:\"ROLE_MEMBER\";i:1;s:13:\"ROLE_REFERENT\";}',NULL,NULL),(3,2,'orange2000@hotmail.fr',NULL,'GAEC Le Champ des Cigognes','Ferme du Bois de Canon','Mézidon-Canon',14270,'0650806293','$2y$13$d6H0.1R/wFc1sYxQ.Qt6teL1qFH5.jnD9eRBs4YScxagvezLBEcPe',1,'a:1:{i:0;s:13:\"ROLE_PRODUCER\";}','#eca750',1),(4,2,'boulangerielescopains@gmail.com',NULL,'les Co\'Pains',NULL,'Saint-Aubin-Sur-Algot',14340,'0231322224','$2y$13$7V2rWcnXj59UT9ulvC.ryu2ajyRKC/DGwwx9XPUH/ZQlP2QV2AanS',1,'a:1:{i:0;s:13:\"ROLE_PRODUCER\";}','#f6fedb',2),(5,2,'sofie.martinet@yahoo.fr',NULL,'GAEC du Bois de Canon','Ferme du Bois de Canon','Mézidon-Canon',14270,'0231203755','$2y$13$VL6Z4w.VEgVdJ/1W9gJeW.RpoTAu67EaHE1Cjr7kLfRRCwxpvH/HC',1,'a:1:{i:0;s:13:\"ROLE_PRODUCER\";}','#e6d3a3',3),(6,2,'lamimarnel.motte@laposte.net',NULL,'Ferme de la Mimarnel','La Mimarnel','Cambremer',14340,'0231630050','$2y$13$ezGxo5T0v7bo0yG3uT49v.6o7.m8E3ZRcJGxKqOgvCfVv9BdSjmqG',1,'a:1:{i:0;s:13:\"ROLE_PRODUCER\";}','#b6c454',4),(7,2,'gaecdelivet@gmail.com',NULL,'GAEC de Livet',NULL,'Notre Dame De Fresnay',14170,'0628302332','$2y$13$qY70MH1cJS7xsDGbbGwXo.6rW4UjW8c.ToYnkNIZmFr7VvpznEXGu',1,'a:1:{i:0;s:13:\"ROLE_PRODUCER\";}','#b1f8f2',5),(8,2,'cavesdumanoir@orange.fr',NULL,'Manoir de Grandouet','Le Manoir','Cambremer',14340,'0231630873','$2y$13$h/8DL.lS2QK8zeChobGDs.uWnHA5yJoBk532LtTci34iDEsjzcoOy',1,'a:1:{i:0;s:13:\"ROLE_PRODUCER\";}','#9e788f',6),(9,2,'augustin.renault@laposte.net','Augustin','Renault',NULL,'Saint-Aubin-sur-Algot',14340,'0610874763','$2y$13$NAF0KZ1lTZA1DLGLWtHzCOkHUHAH2kDekOetDQVFhRJvShZIXUkzK',1,'a:1:{i:0;s:13:\"ROLE_PRODUCER\";}','#817f75',7),(10,2,'olivier.brifaut@laposte.net','Olivier & Anne','Brifaut','noiremare','Saint-Ouen-le-Houx',14140,NULL,'$2y$13$Bqy2dQC5URFEp06IhQfSFOpCqwymfQBYlU78JhMXE8XXWephJ4936',1,'a:1:{i:0;s:13:\"ROLE_PRODUCER\";}','#a9b3ce',8),(11,2,'r.gaylord@hotmail.fr','Gaylord','Roney','cd 45 c','Douville en Auge',14430,NULL,'$2y$13$R.OdjuQRt06WgAZLaM8PWuv1BNAcrO52Wb9I6hOOe8QnocBx84hF6',1,'a:1:{i:0;s:13:\"ROLE_PRODUCER\";}','#f4f7be',9),(12,2,'lapattejeanjean@gmail.com',NULL,'Pâtes Jeanjean','CCI Intech, Pôle universitaire d’Alençon','Damigny',61250,NULL,'$2y$13$.C36Xtd37qCjdsekjNy35eNXGeDw0Cejm5z5w0cOZfVoQD.Iz2jtm',1,'a:1:{i:0;s:13:\"ROLE_PRODUCER\";}','#eca750',10),(13,2,'hello@akalfood.com',NULL,'AKAL FOOD','Ecodomaine de Bouquetot, Chemin des Broches','Saint-Pierre-Azif',14950,'0683521824','$2y$13$ZD0AP6icl.Z7PVxpr2TQMutV/HcX8T9txFAa08EJ4O86ot1gFtIha',1,'a:1:{i:0;s:13:\"ROLE_PRODUCER\";}','#99d5c9',11),(14,2,'bruyere.vero@wanadoo.fr','Daniel et Véronique','Martin','Le Petit Malheur','Bourgeauville',14430,'0231648385','$2y$13$0ugTwhi0cfqvJO36xlI.De3LPf4/8NK0LFE0nnJAT0ImapwiT7/o.',1,'a:1:{i:0;s:13:\"ROLE_PRODUCER\";}','#4d9de0',12);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-03-16 21:29:54

$2y$13$ezGxo5T0v7bo0yG3uT49v.6o7.m8E3ZRcJGxKqOgvCfVv9BdSjmqG