-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Lun 01 Octobre 2018 à 12:00
-- Version du serveur: 5.1.69
-- Version de PHP: 5.3.2-1ubuntu4.19

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `opas`
--

-- --------------------------------------------------------

--
-- Structure de la table `webcontrat_contrat`
--

CREATE TABLE IF NOT EXISTS `webcontrat_contrat` (
  `Commande` varchar(16) NOT NULL,
  `Client_id` int(11) NOT NULL,
  `ContratSociete` varchar(4) NOT NULL,
  `Support_id` int(11) NOT NULL,
  `NoContrat` int(11) NOT NULL,
  `Parution` int(11) NOT NULL,
  `DateEmission` date NOT NULL,
  `Departement` varchar(4) DEFAULT NULL,
  `NbParution` int(11) DEFAULT NULL,
  `NbJustif` int(11) DEFAULT NULL,
  `PrixHT` varchar(16) DEFAULT NULL,
  `PrixTTC` varchar(16) DEFAULT NULL,
  `Reglement` varchar(4) DEFAULT NULL,
  `Supplement` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`Commande`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `webcontrat_contrat`
--

INSERT INTO `webcontrat_contrat` (`Commande`, `Client_id`, `ContratSociete`, `Support_id`, `NoContrat`, `Parution`, `DateEmission`, `Departement`, `NbParution`, `NbJustif`, `PrixHT`, `PrixTTC`, `Reglement`, `Supplement`) VALUES
('CSAM00001A0011', 7256, 'CSS', 2, 11, 1, '2018-09-25', '49', 1, 1, '790.', '948.', '', ''),
('OPET00001A0010', 72531, 'OPAS', 137, 10, 1, '2018-09-25', '01', 1, 1, '5000.', '5000.', '', ''),
('OPET00001A0011', 72933, 'OPAS', 137, 11, 1, '2018-09-25', '01', 1, 1, '14500.', '14500.', '', ''),
('OPGI00004A4468', 86897, 'OPAS', 161, 4468, 4, '2018-09-25', '14', 1, 1, '1450.', '1740.', '', ''),
('OPKM00001A5168', 50626, 'OPAS', 184, 5168, 1, '2018-09-25', '92', 1, 1, '6000.', '7200.', '', 'DOSSIER ISSY LES MOULINEAUX'),
('OPKM00001A5169', 64014, 'OPAS', 184, 5169, 1, '2018-09-25', '92', 1, 1, '7500.', '9000.', '', 'DOSSIER ISSY LES MOULINEAUX'),
('OPKM00001A5170', 81783, 'OPAS', 184, 5170, 1, '2018-09-25', '33', 1, 1, '350.', '420.', 'R', 'DOSSIER VILLE DE BORDEAUX'),
('OPLW00008A1744', 84459, 'OPAS', 200, 1744, 8, '2018-09-25', '52', 1, 1, '115.', '138.', 'R', ''),
('OPPS00001A0608', 81978, 'OPAS', 5398, 608, 1, '2018-09-25', '77', 1, 1, '-7000.', '-8400.', '', 'ANNULATION PS0483'),
('OPRD00001A2061', 86074, 'OPAS', 5406, 2061, 1, '2018-09-25', '78', 1, 1, '15350.', '18420.', '', ''),
('OPRD00001A2062', 81978, 'OPAS', 5406, 2062, 1, '2018-09-25', '69', 1, 2, '7000.', '8400.', '', 'CDE 4500062268'),
('OPTM00001A1756', 21694, 'OPAS', 5458, 1756, 1, '2018-09-25', '97', 1, 1, '2500.', '3000.', '', 'DOSSIER REGION AUVERGNE RHONE ALPES'),
('OPVQ00001A0659', 86399, 'OPAS', 5481, 659, 1, '2018-09-25', '67', 1, 1, '2971.', '3565.2', '', ''),
('OPVQ00001A0660', 86422, 'OPAS', 5481, 660, 1, '2018-09-25', '75', 1, 1, '5590.', '6708.', '', ''),
('OPET00001A0009', 86903, 'OPAS', 137, 9, 1, '2018-09-25', '01', 1, 1, '14500.', '14500.', '', ''),
('OPET00001A0008', 86902, 'OPAS', 137, 8, 1, '2018-09-25', '01', 1, 1, '4000.', '4000.', '', ''),
('CSPL00012A0813', 63429, 'CSS', 248, 813, 12, '2018-09-25', '59', 1, 1, '1200.', '1440.', '', ''),
('CSPL00012A0814', 73714, 'CSS', 248, 814, 12, '2018-09-25', '75', 1, 2, '1000.', '1200.', '', ''),
('CSVO00001A0043', 82509, 'CSS', 5479, 43, 1, '2018-09-25', '75', 1, 1, '3900.', '4680.', '', ''),
('OFDR00001A0019', 60628, 'OFRE', 7, 19, 1, '2018-09-25', '21', 1, 1, '2083.34', '2500.', '', ''),
('OFGD00002A0282', 81730, 'OFRE', 71, 282, 2, '2018-09-25', '69', 1, 1, '500.', '600.', '', ''),
('OFIG00001A0058', 86894, 'OFRE', 5526, 58, 1, '2018-09-25', '01', 1, 1, '2700.', '3240.', '', ''),
('OFIG00001A0059', 86895, 'OFRE', 5526, 59, 1, '2018-09-25', '01', 1, 1, '1000.', '1200.', '', ''),
('OFIG00001A0060', 86896, 'OFRE', 5526, 60, 1, '2018-09-25', '01', 1, 1, '3200.', '3840.', '', ''),
('OPBL00001A0004', 70678, 'OPAS', 5527, 4, 1, '2018-09-25', '03', 1, 1, '4500.', '5400.', '', ''),
('OPET00001A0004', 86898, 'OPAS', 137, 4, 1, '2018-09-25', '01', 1, 1, '10000.', '10000.', '', ''),
('OPET00001A0005', 86899, 'OPAS', 137, 5, 1, '2018-09-25', '01', 1, 1, '10000.', '10000.', '', ''),
('OPET00001A0006', 86900, 'OPAS', 137, 6, 1, '2018-09-25', '01', 1, 1, '2000.', '2000.', '', ''),
('OPET00001A0007', 86901, 'OPAS', 137, 7, 1, '2018-09-25', '01', 1, 1, '14500.', '14500.', '', ''),
('OPVQ00001A0661', 71655, 'OPAS', 5481, 661, 1, '2018-09-25', '25', 1, 4, '5900.', '7080.', '', '');
