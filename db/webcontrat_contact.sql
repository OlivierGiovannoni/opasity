-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Lun 29 Octobre 2018 à 17:30
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
-- Structure de la table `webcontrat_contact`
--

CREATE TABLE IF NOT EXISTS `webcontrat_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Nom` varchar(64) NOT NULL,
  `Prenom` varchar(64) NOT NULL,
  `Societe` varchar(64) NOT NULL,
  `Fonction` varchar(64) NOT NULL,
  `Type_id` int(11) NOT NULL,
  `Addr` varchar(128) NOT NULL,
  `Mail` varchar(128) NOT NULL,
  `CP` varchar(16) NOT NULL,
  `Ville` varchar(64) NOT NULL,
  `Tel` varchar(16) NOT NULL,
  `Fax` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Type_id_referencing_webcontrat_typecontact_id` (`Type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=72 ;

--
-- Contenu de la table `webcontrat_contact`
--

INSERT INTO `webcontrat_contact` (`id`, `Nom`, `Prenom`, `Societe`, `Fonction`, `Type_id`, `Addr`, `Mail`, `CP`, `Ville`, `Tel`, `Fax`) VALUES
(24, 'PICHON', 'OLIVIER', 'PRESENCE GRAPHIQUE', 'IMPRIMERIE', 1, '2 RUE DE LA PINSONNIERE', 'olivier.pichon@presence-graphique.com', '37260', 'MONTS', '02 47 34 25 40', '02 47 34 25 41'),
(23, 'BESNARD', 'DAVID', 'BARNEOUD', 'IMPRIMERIE', 1, '2 RUE PIERRE LEMONNIER BP 44', 'd.besnard@barneoud.fr', '53960', 'BONCHAMPS LES LAVAL', '06 61 09 12 92', '01 58 30 10 11'),
(8, 'STAUB', 'FRANCOISE', 'CFPV', 'SECRETAIRE', 2, '18 rue de l''université', 'cfpv-jmv@wanadoo.fr', '75007', 'PARIS', '0155048213', '0155048217'),
(9, 'HAGEGE', 'HERVE', '', 'MEDECIN', 2, 'CHIC', 'herve.hagege@chicreteil.fr', '', 'CRETEIL', '0145175479', '0145175476'),
(26, 'POULET', 'THIERRY', 'NOAO PRODUCT', 'IMPRIMERIE', 1, '13 RUE YVES TOUDIC', 't.poulet@noao.com', '75010', 'PARIS', '01 53 19 87 89', '01 53 19 87 88'),
(28, 'RAMOS', 'JOSE ', 'RICCOBONO', 'IMPRIMERIE', 1, '115 CHEMIN DES VALETTES', 'compo@riccobono.fr', '83490', 'LE MUY', '04 94 19 54 57', '04 94 81 82 19'),
(29, '', 'CYRIL', 'IAPCA', 'IMPRIMERIE', 1, 'RUE DU LIEGE ZA DES FERRIERES', 'iapca@riccobono.fr', '83490', 'LE MUY', '04 98 11 09 54 ', '04 94 45 95 98'),
(30, 'CHIFFOLEAU', 'NICOLAS ', 'CHAMPAGNE', 'IMPRIMERIE', 1, 'ZI LES FRANCHISES    RUE DE L'' ETOILE DE LANGRES 52200 LANGRES', 'prepresse@imprimerie-champagne.com', '52200', 'LANGRES', '03 25 87 08 34', '03 25 87 73 10'),
(32, 'ABBOU', 'DANIEL ', 'ONE COMMUNICATION', 'IMPRIMERIE', 1, '65 RUE DE TURENNE', 'onecommunication@wanadoo.fr', '75003', 'PARIS', '06 15 51 41 41', '01 48 87 49 07'),
(33, '', '', 'COURSE CHAMPAGNE', 'COURSIER', 3, '', '', '', '', '01 45 40 69 69', ''),
(34, 'LEVY', 'SANDRINE', 'IMPROFI', 'ROUTEUR', 3, '851 RUE DE BERNAU', '', '94500', 'CHAMPIGNY', '01 48 81 08 08', '01 48 81 11 34'),
(35, '', '', 'ARTIS', '', 1, '', '', '', '', '', ''),
(36, 'LE TRIONNAIRE', 'HERVé', '', '', 2, '', '', '', '', '', ''),
(37, 'LAURENCE', 'BRULARD', '', '', 2, '', '', '', '', '0144436348', ''),
(38, 'KALFON', 'PASCALE', '', '', 2, '', '', '', '', '', ''),
(39, 'UNEN', 'EROL', 'LA POSTE', '', 3, '', '', '', '', '0140453914', '0148566987'),
(40, 'GRAFICAS DIAZ', '', '', '', 1, '', '', '', '', '', ''),
(41, '', '', 'GRAFICAS DIAZ', '', 1, '', '', '', '', '', ''),
(49, '', '', 'INTERCOM', '', 3, '', '', '', '', '', ''),
(43, 'MME GUICHARD', '', '', '', 2, '16, rue Châteauneuf', '', '06000', 'NICE', '04 92 29 22 22', '04 92 29 22 21'),
(44, '', '', 'IAPCA', '', 3, '', '', '', '', '', ''),
(45, '', '', 'LéONCE DEPREZ', '', 1, '', '', '', '', '', ''),
(46, '', '', 'LEONCE DEPREZ', '', 1, '', '', '', '', '', ''),
(47, '', '', 'EFPP- LECLERC', '', 1, '', '', '', '', '', ''),
(50, 'MR VINCENT', '', '', '', 2, 'Maison des collectivitées  85 rue Claude BERNARD', 'asso-maires-aude@orange.fr', '11022', 'CARCASSONE CEDEX', '03 79 10 40 91', ''),
(51, '', '', 'EFPP', '', 1, '', '', '', '', '', ''),
(52, 'CORVEE', 'NATHALIE', 'ADREXO 184', '', 3, 'ZA des Dahinières', 'adx184@spir.fr', '53810', 'CHANGE', '0243686688', ''),
(53, 'THILLAY', 'ANNE', '', '', 2, '', 'thillay.a@orange.fr', '', '', '0675492440', ''),
(54, 'PICARD', 'NATHALIE', 'APVF', 'ASSISTANTE DU DéLéGUé GéNéRAL', 2, '42 Bld Raspail', 'npicard@apvf.asso.fr', '75007', 'PARIS', '0145446398', '0145480256'),
(55, '', '', 'PRINTCO', '', 1, '', '', '', '', '', ''),
(56, 'VASTI-DUMAS', '', '', '', 1, '', '', '', '', '', ''),
(57, '', '', 'VASTI-DUMAS', '', 1, '', '', '', '', '', ''),
(58, '', '', 'RICCOBONO', '', 3, '', '', '', '', '', ''),
(59, '', '', 'ROCHELAISE', '', 1, '', '', '', '', '', ''),
(60, '', '', 'LA TOSCANE', '', 1, '', '', '', '', '', ''),
(61, 'ATHANASSOPOULOS', 'NICOLAS ', '', '', 1, '', '', '', '', '', ''),
(62, 'ATHANASSOPOULOS', '', '', '', 1, '', '', '', '', '', ''),
(63, '', 'NICOLAS', '', '', 1, '', '', '', '', '', ''),
(64, '', 'NICOLAS', '', '', 1, '', '', '', '', '', ''),
(65, '', '', 'PRINTSTORE', '', 1, '', '', '', '', '', ''),
(66, '', '', 'NICOLAS', '', 1, '', '', '', '', '', ''),
(67, '', '', 'NICOLAS', '', 1, '', '', '', '', '', ''),
(68, '', '', 'PRINTCORP', '', 1, '', '', '', '', '', ''),
(69, '', '', 'LES EDITEURS', '', 1, '', '', '', '', '', ''),
(70, '', '', 'TOUTES LES EDITIONS', '', 1, '', '', '', '', '', ''),
(71, '', '', 'PUBADRESSEROUTAGE', '', 3, '', '', '', '', '', '');
