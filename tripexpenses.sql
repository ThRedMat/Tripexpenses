-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 01 nov. 2025 à 14:03
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `tripexpenses`
--

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Hébergement'),
(2, 'Alimentation'),
(3, 'Transport'),
(4, 'Activités et Loisirs'),
(5, 'Souvenirs et Achats'),
(6, 'Santé et Bien-être'),
(7, 'Services'),
(8, 'Communication'),
(9, 'Transport Local'),
(10, 'Divertissement Nocturne'),
(11, 'Urgences et Imprévus');

-- --------------------------------------------------------

--
-- Structure de la table `currencies`
--

CREATE TABLE `currencies` (
  `id` int(11) NOT NULL,
  `code` varchar(3) NOT NULL,
  `name` varchar(100) NOT NULL,
  `symbol` varchar(10) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `is_main` tinyint(1) DEFAULT 0,
  `union_flag` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `currencies`
--

INSERT INTO `currencies` (`id`, `code`, `name`, `symbol`, `country`, `is_main`, `union_flag`, `created_at`) VALUES
(1, 'USD', 'Dollar américain', '$', 'États-Unis', 1, '/Tripexpenses/images/drapeaux/amériques/us.png', '2025-10-13 13:30:25'),
(2, 'CAD', 'Dollar canadien', 'C$', 'Canada', 1, '/Tripexpenses/images/drapeaux/amériques/cad.png', '2025-10-13 13:30:25'),
(3, 'MXN', 'Peso mexicain', '$', 'Mexique', 0, '/Tripexpenses/images/drapeaux/amériques/mx.png', '2025-10-13 13:30:25'),
(4, 'BRL', 'Real brésilien', 'R$', 'Brésil', 0, '/Tripexpenses/images/drapeaux/amériques/br.png', '2025-10-13 13:30:25'),
(5, 'ARS', 'Peso argentin', '$', 'Argentine', 0, '/Tripexpenses/images/drapeaux/amériques/ar.png', '2025-10-13 13:30:25'),
(6, 'CLP', 'Peso chilien', '$', 'Chili', 0, '/Tripexpenses/images/drapeaux/amériques/cl.png', '2025-10-13 13:30:25'),
(7, 'COP', 'Peso colombien', '$', 'Colombie', 0, '/Tripexpenses/images/drapeaux/amériques/co.png', '2025-10-13 13:30:25'),
(8, 'PEN', 'Sol péruvien', 'S/', 'Pérou', 0, '/Tripexpenses/images/drapeaux/amériques/pe.png', '2025-10-13 13:30:25'),
(9, 'UYU', 'Peso uruguayen', '$U', 'Uruguay', 0, '/Tripexpenses/images/drapeaux/amériques/uy.png', '2025-10-13 13:30:25'),
(10, 'BOB', 'Boliviano', 'Bs.', 'Bolivie', 0, '/Tripexpenses/images/drapeaux/amériques/bo.png', '2025-10-13 13:30:25'),
(11, 'PYG', 'Guaraní paraguayen', '₲', 'Paraguay', 0, '/Tripexpenses/images/drapeaux/amériques/py.png', '2025-10-13 13:30:25'),
(12, 'VES', 'Bolívar vénézuélien', 'Bs.', 'Venezuela', 0, '/Tripexpenses/images/drapeaux/amériques/ve.png', '2025-10-13 13:30:25'),
(13, 'DOP', 'Peso dominicain', 'RD$', 'République dominicaine', 0, '/Tripexpenses/images/drapeaux/amériques/do.png', '2025-10-13 13:30:25'),
(14, 'CRC', 'Colón costaricain', '₡', 'Costa Rica', 0, '/Tripexpenses/images/drapeaux/amériques/cr.png', '2025-10-13 13:30:25'),
(15, 'GTQ', 'Quetzal guatémaltèque', 'Q', 'Guatemala', 0, '/Tripexpenses/images/drapeaux/amériques/gt.png', '2025-10-13 13:30:25'),
(16, 'HNL', 'Lempira hondurien', 'L', 'Honduras', 0, '/Tripexpenses/images/drapeaux/amériques/hn.png', '2025-10-13 13:30:25'),
(17, 'NIO', 'Córdoba nicaraguayen', 'C$', 'Nicaragua', 0, '/Tripexpenses/images/drapeaux/amériques/ni.png', '2025-10-13 13:30:25'),
(18, 'PAB', 'Balboa panaméen', 'B/.', 'Panama', 0, '/Tripexpenses/images/drapeaux/amériques/pa.png', '2025-10-13 13:30:25'),
(19, 'CUP', 'Peso cubain', '$', 'Cuba', 0, '/Tripexpenses/images/drapeaux/amériques/cu.png', '2025-10-13 13:30:25'),
(20, 'JMD', 'Dollar jamaïcain', 'J$', 'Jamaïque', 0, '/Tripexpenses/images/drapeaux/amériques/jm.png', '2025-10-13 13:30:25'),
(21, 'TTD', 'Dollar de Trinité-et-Tobago', 'TT$', 'Trinité-et-Tobago', 0, '/Tripexpenses/images/drapeaux/amériques/tt.png', '2025-10-13 13:30:25'),
(22, 'BBD', 'Dollar barbadien', 'Bds$', 'Barbade', 0, '/Tripexpenses/images/drapeaux/amériques/bb.png', '2025-10-13 13:30:25'),
(23, 'BSD', 'Dollar bahaméen', 'B$', 'Bahamas', 0, '/Tripexpenses/images/drapeaux/amériques/bs.png', '2025-10-13 13:30:25'),
(24, 'HTG', 'Gourde haïtienne', 'G', 'Haïti', 0, '/Tripexpenses/images/drapeaux/amériques/ht.png', '2025-10-13 13:30:25'),
(25, 'EUR', 'Euro', '€', 'Zone Euro', 1, '/Tripexpenses/images/drapeaux/europe/eu.png', '2025-10-13 13:30:25'),
(26, 'GBP', 'Livre sterling', '£', 'Royaume-Uni', 1, '/Tripexpenses/images/drapeaux/europe/uk.png', '2025-10-13 13:30:25'),
(27, 'CHF', 'Franc suisse', 'CHF', 'Suisse', 1, '/Tripexpenses/images/drapeaux/europe/ch.png', '2025-10-13 13:30:25'),
(28, 'NOK', 'Couronne norvégienne', 'kr', 'Norvège', 0, '/Tripexpenses/images/drapeaux/europe/no.png', '2025-10-13 13:30:25'),
(29, 'SEK', 'Couronne suédoise', 'kr', 'Suède', 0, '/Tripexpenses/images/drapeaux/europe/se.png', '2025-10-13 13:30:25'),
(30, 'DKK', 'Couronne danoise', 'kr', 'Danemark', 0, '/Tripexpenses/images/drapeaux/europe/dk.png', '2025-10-13 13:30:25'),
(31, 'PLN', 'Zloty polonais', 'zł', 'Pologne', 0, '/Tripexpenses/images/drapeaux/europe/pl.png', '2025-10-13 13:30:25'),
(32, 'CZK', 'Couronne tchèque', 'Kč', 'République tchèque', 0, '/Tripexpenses/images/drapeaux/europe/cz.png', '2025-10-13 13:30:25'),
(33, 'HUF', 'Forint hongrois', 'Ft', 'Hongrie', 0, '/Tripexpenses/images/drapeaux/europe/hu.png', '2025-10-13 13:30:25'),
(34, 'RON', 'Leu roumain', 'lei', 'Roumanie', 0, '/Tripexpenses/images/drapeaux/europe/ro.png', '2025-10-13 13:30:25'),
(35, 'BGN', 'Lev bulgare', 'лв', 'Bulgarie', 0, '/Tripexpenses/images/drapeaux/europe/bg.png', '2025-10-13 13:30:25'),
(36, 'HRK', 'Kuna croate', 'kn', 'Croatie', 0, '/Tripexpenses/images/drapeaux/europe/hr.png', '2025-10-13 13:30:25'),
(37, 'RUB', 'Rouble russe', '₽', 'Russie', 0, '/Tripexpenses/images/drapeaux/europe/ru.png', '2025-10-13 13:30:25'),
(38, 'UAH', 'Hryvnia ukrainienne', '₴', 'Ukraine', 0, '/Tripexpenses/images/drapeaux/europe/ua.png', '2025-10-13 13:30:25'),
(39, 'TRY', 'Livre turque', '₺', 'Turquie', 0, '/Tripexpenses/images/drapeaux/europe/tr.png', '2025-10-13 13:30:25'),
(40, 'ISK', 'Couronne islandaise', 'kr', 'Islande', 0, '/Tripexpenses/images/drapeaux/europe/is.png', '2025-10-13 13:30:25'),
(41, 'RSD', 'Dinar serbe', 'дин.', 'Serbie', 0, '/Tripexpenses/images/drapeaux/europe/rs.png', '2025-10-13 13:30:25'),
(42, 'MKD', 'Denar macédonien', 'ден', 'Macédoine du Nord', 0, '/Tripexpenses/images/drapeaux/europe/mk.png', '2025-10-13 13:30:25'),
(43, 'ALL', 'Lek albanais', 'L', 'Albanie', 0, '/Tripexpenses/images/drapeaux/europe/al.png', '2025-10-13 13:30:25'),
(44, 'BAM', 'Mark convertible', 'KM', 'Bosnie-Herzégovine', 0, '/Tripexpenses/images/drapeaux/europe/ba.png', '2025-10-13 13:30:25'),
(45, 'MDL', 'Leu moldave', 'L', 'Moldavie', 0, '/Tripexpenses/images/drapeaux/europe/md.png', '2025-10-13 13:30:25'),
(46, 'GEL', 'Lari géorgien', '₾', 'Géorgie', 0, '/Tripexpenses/images/drapeaux/europe/ge.png', '2025-10-13 13:30:25'),
(47, 'BYN', 'Rouble biélorusse', 'Br', 'Biélorussie', 0, '/Tripexpenses/images/drapeaux/europe/by.png', '2025-10-13 13:30:25'),
(48, 'CNY', 'Yuan chinois', '¥', 'Chine', 1, '/Tripexpenses/images/drapeaux/asie/cn.png', '2025-10-13 13:30:25'),
(49, 'JPY', 'Yen japonais', '¥', 'Japon', 1, '/Tripexpenses/images/drapeaux/asie/jp.png', '2025-10-13 13:30:25'),
(50, 'THB', 'Baht thaïlandais', '฿', 'Thaïlande', 0, '/Tripexpenses/images/drapeaux/asie/th.png', '2025-10-13 13:30:25'),
(51, 'KRW', 'Won sud-coréen', '₩', 'Corée du Sud', 0, '/Tripexpenses/images/drapeaux/asie/kr.png', '2025-10-13 13:30:25'),
(52, 'INR', 'Roupie indienne', '₹', 'Inde', 0, '/Tripexpenses/images/drapeaux/asie/in.png', '2025-10-13 13:30:25'),
(53, 'IDR', 'Roupie indonésienne', 'Rp', 'Indonésie', 0, '/Tripexpenses/images/drapeaux/asie/id.png', '2025-10-13 13:30:25'),
(54, 'MYR', 'Ringgit malaisien', 'RM', 'Malaisie', 0, '/Tripexpenses/images/drapeaux/asie/my.png', '2025-10-13 13:30:25'),
(55, 'SGD', 'Dollar de Singapour', 'S$', 'Singapour', 1, '/Tripexpenses/images/drapeaux/asie/sg.png', '2025-10-13 13:30:25'),
(56, 'PHP', 'Peso philippin', '₱', 'Philippines', 0, '/Tripexpenses/images/drapeaux/asie/ph.png', '2025-10-13 13:30:25'),
(57, 'VND', 'Dong vietnamien', '₫', 'Vietnam', 0, '/Tripexpenses/images/drapeaux/asie/vn.png', '2025-10-13 13:30:25'),
(58, 'PKR', 'Roupie pakistanaise', '₨', 'Pakistan', 0, '/Tripexpenses/images/drapeaux/asie/pk.png', '2025-10-13 13:30:25'),
(59, 'BDT', 'Taka bangladais', '৳', 'Bangladesh', 0, '/Tripexpenses/images/drapeaux/asie/bd.png', '2025-10-13 13:30:25'),
(60, 'LKR', 'Roupie srilankaise', 'Rs', 'Sri Lanka', 0, '/Tripexpenses/images/drapeaux/asie/lk.png', '2025-10-13 13:30:25'),
(61, 'MMK', 'Kyat birman', 'K', 'Myanmar', 0, '/Tripexpenses/images/drapeaux/asie/mm.png', '2025-10-13 13:30:25'),
(62, 'KHR', 'Riel cambodgien', '៛', 'Cambodge', 0, '/Tripexpenses/images/drapeaux/asie/kh.png', '2025-10-13 13:30:25'),
(63, 'LAK', 'Kip laotien', '₭', 'Laos', 0, '/Tripexpenses/images/drapeaux/asie/la.png', '2025-10-13 13:30:25'),
(64, 'BND', 'Dollar de Brunei', 'B$', 'Brunei', 0, '/Tripexpenses/images/drapeaux/asie/bn.png', '2025-10-13 13:30:25'),
(65, 'TWD', 'Dollar taïwanais', 'NT$', 'Taïwan', 0, '/Tripexpenses/images/drapeaux/asie/tw.png', '2025-10-13 13:30:25'),
(66, 'HKD', 'Dollar de Hong Kong', 'HK$', 'Hong Kong', 1, '/Tripexpenses/images/drapeaux/asie/hk.png', '2025-10-13 13:30:25'),
(67, 'MOP', 'Pataca macanaise', 'MOP$', 'Macao', 0, '/Tripexpenses/images/drapeaux/asie/mo.png', '2025-10-13 13:30:25'),
(68, 'KPW', 'Won nord-coréen', '₩', 'Corée du Nord', 0, '/Tripexpenses/images/drapeaux/asie/kp.png', '2025-10-13 13:30:25'),
(69, 'MNT', 'Tugrik mongol', '₮', 'Mongolie', 0, '/Tripexpenses/images/drapeaux/asie/mn.png', '2025-10-13 13:30:25'),
(70, 'NPR', 'Roupie népalaise', 'Rs', 'Népal', 0, '/Tripexpenses/images/drapeaux/asie/np.png', '2025-10-13 13:30:25'),
(71, 'BTN', 'Ngultrum bhoutanais', 'Nu.', 'Bhoutan', 0, '/Tripexpenses/images/drapeaux/asie/bt.png', '2025-10-13 13:30:25'),
(72, 'MVR', 'Rufiyaa maldivienne', 'Rf', 'Maldives', 0, '/Tripexpenses/images/drapeaux/asie/mv.png', '2025-10-13 13:30:25'),
(73, 'AFN', 'Afghani afghan', '؋', 'Afghanistan', 0, '/Tripexpenses/images/drapeaux/asie/af.png', '2025-10-13 13:30:25'),
(74, 'AED', 'Dirham des Émirats', 'د.إ', 'Émirats arabes unis', 0, '/Tripexpenses/images/drapeaux/moyen-orient/uae.png', '2025-10-13 13:30:25'),
(75, 'SAR', 'Riyal saoudien', '﷼', 'Arabie saoudite', 0, '/Tripexpenses/images/drapeaux/moyen-orient/sa.png', '2025-10-13 13:30:25'),
(76, 'ILS', 'Shekel israélien', '₪', 'Israël', 0, '/Tripexpenses/images/drapeaux/moyen-orient/il.png', '2025-10-13 13:30:25'),
(77, 'IRR', 'Rial iranien', '﷼', 'Iran', 0, '/Tripexpenses/images/drapeaux/moyen-orient/ir.png', '2025-10-13 13:30:25'),
(78, 'IQD', 'Dinar irakien', 'ع.د', 'Irak', 0, '/Tripexpenses/images/drapeaux/moyen-orient/iq.png', '2025-10-13 13:30:25'),
(79, 'JOD', 'Dinar jordanien', 'د.ا', 'Jordanie', 0, '/Tripexpenses/images/drapeaux/moyen-orient/jo.png', '2025-10-13 13:30:25'),
(80, 'KWD', 'Dinar koweïtien', 'د.ك', 'Koweït', 0, '/Tripexpenses/images/drapeaux/moyen-orient/kw.png', '2025-10-13 13:30:25'),
(81, 'LBP', 'Livre libanaise', 'ل.ل', 'Liban', 0, '/Tripexpenses/images/drapeaux/moyen-orient/lb.png', '2025-10-13 13:30:25'),
(82, 'OMR', 'Rial omanais', 'ر.ع.', 'Oman', 0, '/Tripexpenses/images/drapeaux/moyen-orient/om.png', '2025-10-13 13:30:25'),
(83, 'QAR', 'Riyal qatari', 'ر.ق', 'Qatar', 0, '/Tripexpenses/images/drapeaux/moyen-orient/qa.png', '2025-10-13 13:30:25'),
(84, 'SYP', 'Livre syrienne', '£S', 'Syrie', 0, '/Tripexpenses/images/drapeaux/moyen-orient/sy.png', '2025-10-13 13:30:25'),
(85, 'YER', 'Rial yéménite', '﷼', 'Yémen', 0, '/Tripexpenses/images/drapeaux/moyen-orient/ye.png', '2025-10-13 13:30:25'),
(86, 'BHD', 'Dinar bahreïni', 'ب.د', 'Bahreïn', 0, '/Tripexpenses/images/drapeaux/moyen-orient/bh.png', '2025-10-13 13:30:25'),
(87, 'MAD', 'Dirham marocain', 'د.م.', 'Maroc', 0, '/Tripexpenses/images/drapeaux/afrique/maroc.png', '2025-10-13 13:30:25'),
(88, 'TND', 'Dinar tunisien', 'د.ت', 'Tunisie', 0, '/Tripexpenses/images/drapeaux/afrique/tunisie.png', '2025-10-13 13:30:25'),
(89, 'ZAR', 'Rand sud-africain', 'R', 'Afrique du Sud', 0, '/Tripexpenses/images/drapeaux/afrique/za.png', '2025-10-13 13:30:25'),
(90, 'EGP', 'Livre égyptienne', '£', 'Égypte', 0, '/Tripexpenses/images/drapeaux/afrique/eg.png', '2025-10-13 13:30:25'),
(91, 'NGN', 'Naira nigérian', '₦', 'Nigeria', 0, '/Tripexpenses/images/drapeaux/afrique/ng.png', '2025-10-13 13:30:25'),
(92, 'KES', 'Shilling kényan', 'KSh', 'Kenya', 0, '/Tripexpenses/images/drapeaux/afrique/ke.png', '2025-10-13 13:30:25'),
(93, 'GHS', 'Cedi ghanéen', '₵', 'Ghana', 0, '/Tripexpenses/images/drapeaux/afrique/gh.png', '2025-10-13 13:30:25'),
(94, 'TZS', 'Shilling tanzanien', 'TSh', 'Tanzanie', 0, '/Tripexpenses/images/drapeaux/afrique/tz.png', '2025-10-13 13:30:25'),
(95, 'UGX', 'Shilling ougandais', 'USh', 'Ouganda', 0, '/Tripexpenses/images/drapeaux/afrique/ug.png', '2025-10-13 13:30:25'),
(96, 'ETB', 'Birr éthiopien', 'Br', 'Éthiopie', 0, '/Tripexpenses/images/drapeaux/afrique/et.png', '2025-10-13 13:30:25'),
(97, 'DZD', 'Dinar algérien', 'د.ج', 'Algérie', 0, '/Tripexpenses/images/drapeaux/afrique/dz.png', '2025-10-13 13:30:25'),
(98, 'AOA', 'Kwanza angolais', 'Kz', 'Angola', 0, '/Tripexpenses/images/drapeaux/afrique/ao.png', '2025-10-13 13:30:25'),
(99, 'XOF', 'Franc CFA (BCEAO)', 'CFA', 'Afrique de l\'Ouest', 0, '/Tripexpenses/images/drapeaux/afrique/xof.png', '2025-10-13 13:30:25'),
(100, 'XAF', 'Franc CFA (BEAC)', 'FCFA', 'Afrique centrale', 0, '/Tripexpenses/images/drapeaux/afrique/xaf.png', '2025-10-13 13:30:25'),
(101, 'MUR', 'Roupie mauricienne', '₨', 'Maurice', 0, '/Tripexpenses/images/drapeaux/afrique/mu.png', '2025-10-13 13:30:25'),
(102, 'SCR', 'Roupie seychelloise', '₨', 'Seychelles', 0, '/Tripexpenses/images/drapeaux/afrique/sc.png', '2025-10-13 13:30:25'),
(103, 'MGA', 'Ariary malgache', 'Ar', 'Madagascar', 0, '/Tripexpenses/images/drapeaux/afrique/mg.png', '2025-10-13 13:30:25'),
(104, 'ZMW', 'Kwacha zambien', 'ZK', 'Zambie', 0, '/Tripexpenses/images/drapeaux/afrique/zm.png', '2025-10-13 13:30:25'),
(105, 'BWP', 'Pula botswanais', 'P', 'Botswana', 0, '/Tripexpenses/images/drapeaux/afrique/bw.png', '2025-10-13 13:30:25'),
(106, 'MZN', 'Metical mozambicain', 'MT', 'Mozambique', 0, '/Tripexpenses/images/drapeaux/afrique/mz.png', '2025-10-13 13:30:25'),
(107, 'NAD', 'Dollar namibien', 'N$', 'Namibie', 0, '/Tripexpenses/images/drapeaux/afrique/na.png', '2025-10-13 13:30:25'),
(108, 'SZL', 'Lilangeni swazi', 'L', 'Eswatini', 0, '/Tripexpenses/images/drapeaux/afrique/sz.png', '2025-10-13 13:30:25'),
(109, 'LSL', 'Loti lesothan', 'L', 'Lesotho', 0, '/Tripexpenses/images/drapeaux/afrique/ls.png', '2025-10-13 13:30:25'),
(110, 'RWF', 'Franc rwandais', 'FRw', 'Rwanda', 0, '/Tripexpenses/images/drapeaux/afrique/rw.png', '2025-10-13 13:30:25'),
(111, 'BIF', 'Franc burundais', 'FBu', 'Burundi', 0, '/Tripexpenses/images/drapeaux/afrique/bi.png', '2025-10-13 13:30:25'),
(112, 'SOS', 'Shilling somalien', 'Sh', 'Somalie', 0, '/Tripexpenses/images/drapeaux/afrique/so.png', '2025-10-13 13:30:25'),
(113, 'SDG', 'Livre soudanaise', '£', 'Soudan', 0, '/Tripexpenses/images/drapeaux/afrique/sd.png', '2025-10-13 13:30:25'),
(114, 'SSP', 'Livre sud-soudanaise', '£', 'Soudan du Sud', 0, '/Tripexpenses/images/drapeaux/afrique/ss.png', '2025-10-13 13:30:25'),
(115, 'LYD', 'Dinar libyen', 'ل.د', 'Libye', 0, '/Tripexpenses/images/drapeaux/afrique/ly.png', '2025-10-13 13:30:25'),
(116, 'MWK', 'Kwacha malawite', 'MK', 'Malawi', 0, '/Tripexpenses/images/drapeaux/afrique/mw.png', '2025-10-13 13:30:25'),
(117, 'GMD', 'Dalasi gambien', 'D', 'Gambie', 0, '/Tripexpenses/images/drapeaux/afrique/gm.png', '2025-10-13 13:30:25'),
(118, 'SLL', 'Leone sierra-léonais', 'Le', 'Sierra Leone', 0, '/Tripexpenses/images/drapeaux/afrique/sl.png', '2025-10-13 13:30:25'),
(119, 'LRD', 'Dollar libérien', 'L$', 'Liberia', 0, '/Tripexpenses/images/drapeaux/afrique/lr.png', '2025-10-13 13:30:25'),
(120, 'GNF', 'Franc guinéen', 'FG', 'Guinée', 0, '/Tripexpenses/images/drapeaux/afrique/gn.png', '2025-10-13 13:30:25'),
(121, 'CVE', 'Escudo cap-verdien', '$', 'Cap-Vert', 0, '/Tripexpenses/images/drapeaux/afrique/cv.png', '2025-10-13 13:30:25'),
(122, 'STN', 'Dobra santoméen', 'Db', 'Sao Tomé-et-Principe', 0, '/Tripexpenses/images/drapeaux/afrique/st.png', '2025-10-13 13:30:25'),
(123, 'DJF', 'Franc djiboutien', 'Fdj', 'Djibouti', 0, '/Tripexpenses/images/drapeaux/afrique/dj.png', '2025-10-13 13:30:25'),
(124, 'ERN', 'Nakfa érythréen', 'Nfk', 'Érythrée', 0, '/Tripexpenses/images/drapeaux/afrique/er.png', '2025-10-13 13:30:25'),
(125, 'MRU', 'Ouguiya mauritanien', 'UM', 'Mauritanie', 0, '/Tripexpenses/images/drapeaux/afrique/mr.png', '2025-10-13 13:30:25'),
(126, 'KMF', 'Franc comorien', 'CF', 'Comores', 0, '/Tripexpenses/images/drapeaux/afrique/km.png', '2025-10-13 13:30:25'),
(127, 'AUD', 'Dollar australien', 'A$', 'Australie', 1, '/Tripexpenses/images/drapeaux/oceanie/au.png', '2025-10-13 13:30:25'),
(128, 'NZD', 'Dollar néo-zélandais', 'NZ$', 'Nouvelle-Zélande', 0, '/Tripexpenses/images/drapeaux/oceanie/nz.png', '2025-10-13 13:30:25'),
(129, 'FJD', 'Dollar fidjien', 'FJ$', 'Fidji', 0, '/Tripexpenses/images/drapeaux/oceanie/fj.png', '2025-10-13 13:30:25'),
(130, 'PGK', 'Kina papou', 'K', 'Papouasie-Nouvelle-Guinée', 0, '/Tripexpenses/images/drapeaux/oceanie/pg.png', '2025-10-13 13:30:25'),
(131, 'WST', 'Tala samoan', 'WS$', 'Samoa', 0, '/Tripexpenses/images/drapeaux/oceanie/ws.png', '2025-10-13 13:30:25'),
(132, 'TOP', 'Paʻanga tongien', 'T$', 'Tonga', 0, '/Tripexpenses/images/drapeaux/oceanie/to.png', '2025-10-13 13:30:25'),
(133, 'VUV', 'Vatu vanuatuan', 'VT', 'Vanuatu', 0, '/Tripexpenses/images/drapeaux/oceanie/vu.png', '2025-10-13 13:30:25'),
(134, 'SBD', 'Dollar des Salomon', 'SI$', 'Îles Salomon', 0, '/Tripexpenses/images/drapeaux/oceanie/sb.png', '2025-10-13 13:30:25'),
(135, 'XPF', 'Franc Pacifique', '₣', 'Polynésie française', 0, '/Tripexpenses/images/drapeaux/oceanie/pf.png', '2025-10-13 13:30:25'),
(136, 'KZT', 'Tenge kazakh', '₸', 'Kazakhstan', 0, '/Tripexpenses/images/drapeaux/asie_centrale/kz.png', '2025-10-13 13:30:25'),
(137, 'UZS', 'Sum ouzbek', 'so\'m', 'Ouzbékistan', 0, '/Tripexpenses/images/drapeaux/asie_centrale/uz.png', '2025-10-13 13:30:25'),
(138, 'TJS', 'Somoni tadjik', 'ЅМ', 'Tadjikistan', 0, '/Tripexpenses/images/drapeaux/asie_centrale/tj.png', '2025-10-13 13:30:25'),
(139, 'TMT', 'Manat turkmène', 'T', 'Turkménistan', 0, '/Tripexpenses/images/drapeaux/asie_centrale/tm.png', '2025-10-13 13:30:25'),
(140, 'KGS', 'Som kirghize', 'с', 'Kirghizistan', 0, '/Tripexpenses/images/drapeaux/asie_centrale/kg.png', '2025-10-13 13:30:25'),
(141, 'AZN', 'Manat azerbaïdjanais', '₼', 'Azerbaïdjan', 0, '/Tripexpenses/images/drapeaux/asie_centrale/az.png', '2025-10-13 13:30:25'),
(142, 'AMD', 'Dram arménien', '֏', 'Arménie', 0, '/Tripexpenses/images/drapeaux/asie_centrale/am.png', '2025-10-13 13:30:25'),
(143, 'DEF', 'Sélectionnez une devise', NULL, NULL, 0, '/Tripexpenses/images/icons/terre.webp', '2025-10-13 15:03:08');

-- --------------------------------------------------------

--
-- Structure de la table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `trip_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `expense_date` date NOT NULL,
  `description` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `lieu` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `status`
--

CREATE TABLE `status` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `min_trips` int(11) NOT NULL,
  `max_trips` int(11) DEFAULT NULL,
  `icon` varchar(10) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `status`
--

INSERT INTO `status` (`id`, `name`, `min_trips`, `max_trips`, `icon`, `description`) VALUES
(1, 'Backpacker', 0, 2, '🎒', 'Sac au dos et plein de rêves, tu explores le monde en toute simplicité.'),
(2, 'Explorer', 3, 5, '🧭', 'Curieux et audacieux, chaque destination est pour toi une nouvelle énigme.'),
(3, 'Globetrotter', 6, 9, '🌍', 'Le monde est ton terrain de jeu, aucun continent ne t’est inconnu.'),
(4, 'Nomade', 10, 14, '🚐', 'Toujours en mouvement, tu es chez toi partout et nulle part à la fois.'),
(5, 'Aventurier', 15, 20, '💪', 'Rien ne t’arrête, chaque voyage est une mission pleine de défis.'),
(6, 'Capitaine', 21, 30, '⚓', 'À la barre de tes expéditions, tu traces la route vers de nouveaux horizons.'),
(7, 'Légende du Voyage', 31, NULL, '📖✨', 'Ton nom restera gravé dans les carnets de voyage, tu es une véritable inspiration.');

-- --------------------------------------------------------

--
-- Structure de la table `trip`
--

CREATE TABLE `trip` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `budget_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `devise` varchar(10) NOT NULL,
  `transport_type` varchar(50) NOT NULL,
  `transport_reserved` tinyint(1) DEFAULT 0,
  `transport_cost` decimal(10,2) DEFAULT NULL,
  `accommodation_type` varchar(50) NOT NULL,
  `accommodation_reserved` tinyint(1) DEFAULT 0,
  `accommodation_cost` decimal(10,2) DEFAULT NULL,
  `status` enum('À venir','En cours','Terminé') DEFAULT 'À venir',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `closed_date` date DEFAULT NULL,
  `archived_date` datetime DEFAULT NULL,
  `feedback_comment` text DEFAULT NULL,
  `feedback_rating` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `trip`
--

INSERT INTO `trip` (`id`, `user_id`, `destination`, `start_date`, `end_date`, `budget_total`, `devise`, `transport_type`, `transport_reserved`, `transport_cost`, `accommodation_type`, `accommodation_reserved`, `accommodation_cost`, `status`, `created_at`, `updated_at`, `closed_date`, `archived_date`, `feedback_comment`, `feedback_rating`) VALUES
(1, 1, 'Paris, France', '2025-10-14', '2025-10-14', 500.00, 'EUR', 'Train', 1, 150.00, 'Autre', 0, NULL, 'Terminé', '2025-10-14 08:14:15', '2025-10-14 14:25:01', '2025-10-14', NULL, 'Dommage que le train soit arrivé encore en retard', 4),
(2, 2, 'Lyon, France', '2025-10-14', '2025-10-15', 500.00, 'EUR', 'Avion', 1, 150.00, 'Autre', 0, NULL, 'En cours', '2025-10-14 08:52:23', '2025-10-14 08:52:23', NULL, NULL, NULL, NULL),
(3, 1, 'Paris, France', '2025-10-15', '2025-10-16', 500.00, 'EUR', 'Avion', 1, 250.00, 'Autre', 0, NULL, 'Terminé', '2025-10-15 13:26:39', '2025-10-24 08:06:22', NULL, NULL, NULL, NULL),
(5, 21, 'Paris,France', '2025-10-27', '2025-10-28', 1000.00, 'EUR', 'Train', 1, 150.00, 'Hôtel', 1, 200.00, 'En cours', '2025-10-27 08:59:27', '2025-10-27 08:59:27', NULL, NULL, NULL, NULL),
(6, 1, 'Paris, France', '2025-10-27', '2025-10-27', 500.00, 'EUR', 'Train', 1, 150.00, 'Autre', 0, NULL, 'Terminé', '2025-10-27 15:10:11', '2025-10-27 15:13:51', '2025-10-27', NULL, NULL, NULL),
(7, 1, 'Toulouse, France', '2025-10-27', '2025-10-27', 200.00, 'EUR', 'Voiture', 0, NULL, 'Autre', 0, NULL, 'Terminé', '2025-10-27 15:14:16', '2025-10-30 08:52:21', NULL, NULL, NULL, NULL),
(8, 1, 'Paris, France', '2025-10-30', '2025-10-31', 1500.00, 'EUR', 'Train', 1, 150.00, 'Hôtel', 1, 100.00, 'En cours', '2025-10-30 08:52:50', '2025-10-30 08:52:50', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `pseudo` varchar(50) NOT NULL,
  `mail` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status_id` int(11) DEFAULT NULL,
  `trips_count` int(11) DEFAULT 0,
  `avatar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `confirmation_token` varchar(100) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `confirmation_sent_at` timestamp NULL DEFAULT NULL,
  `confirmed` tinyint(1) DEFAULT 0,
  `preferred_currency` varchar(10) DEFAULT 'EUR',
  `confirmation_code` varchar(6) DEFAULT NULL,
  `otp_code` varchar(6) DEFAULT NULL,
  `otp_expires` datetime DEFAULT NULL,
  `confirm_token` varchar(64) DEFAULT NULL,
  `confirm_expires` datetime DEFAULT NULL,
  `confirmed_at` datetime DEFAULT NULL,
  `ville` varchar(255) NOT NULL,
  `pays` varchar(255) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `lastname`, `pseudo`, `mail`, `password`, `status_id`, `trips_count`, `avatar`, `created_at`, `updated_at`, `confirmation_token`, `remember_token`, `confirmation_sent_at`, `confirmed`, `preferred_currency`, `confirmation_code`, `otp_code`, `otp_expires`, `confirm_token`, `confirm_expires`, `confirmed_at`, `ville`, `pays`, `reset_token`, `reset_expires`) VALUES
(1, 'Red', 'Mat', 'R&dM@t', 'm@m.fr', '$2y$10$Jlay8ohPnTU8qWx.3jbNAOj9jQLdO8Q/5yoYnTnRRYofcgBDn4uT2', 5, 16, 'avatar_1.webp', '2025-10-02 09:36:42', '2025-10-27 15:14:36', NULL, NULL, '2025-10-02 09:36:42', 1, 'EUR', NULL, NULL, NULL, NULL, NULL, NULL, 'Bordeaux', 'France', NULL, NULL),
(2, 'Jean', 'Test', 'El testor', 't@t.fr', '$2y$10$BJF6S2OxjEnmbTFZkU/Y7e7vVczezWLM42cdi.f3QuozQ8z.VjYX6', NULL, 0, NULL, '2025-10-14 08:17:23', '2025-10-14 08:17:25', NULL, NULL, '2025-10-14 08:17:23', 1, 'EUR', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL),
(19, 'Jean', 'test', 'gen', 'matt.darribeau@gmail.com', '$2y$10$pk8L9eJcquFtLYq3PBJs.OplafA6USrXw6UGDbYASWf7D3MtIm2HC', NULL, 0, NULL, '2025-10-24 09:28:48', '2025-10-24 09:29:01', NULL, NULL, NULL, 1, 'EUR', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL),
(21, 'Mathieu', 'DAR', 'Math', 'mathdarribau33@gmail.com', '$2y$10$kwdgEMkPumx5IKNdaa/8jeNgCVyP62J6Z36RmLG6K9/0Bm.cufeyK', NULL, 0, NULL, '2025-10-24 10:54:52', '2025-10-27 13:52:01', NULL, NULL, NULL, 1, 'EUR', NULL, NULL, NULL, NULL, NULL, NULL, 'Bordeaux', 'France', NULL, NULL),
(22, 'Jean', 'Essau', 'Jessai', 'kotefoc802@lovleo.com', '$2y$10$wRGMsZDApDmYorXdUyh0IuyjL9GpMJSJkvg5sNwR6tcDUVt7zftcq', NULL, 0, NULL, '2025-10-24 13:54:33', '2025-10-27 15:08:33', NULL, NULL, NULL, 1, 'EUR', NULL, NULL, NULL, NULL, NULL, NULL, '', '', 'ba94efedca499754349da54d0dda3d49', '2025-10-27 17:08:33'),
(23, 'Jim', 'BOE', 'Jumbo', 'pibefo6093@lovleo.com', '$2y$10$x4xck1KK2emWFLXyNV9ja.VQ1bNF5IdzKW0zORSjM2KPlQ63VX0Pa', NULL, 0, NULL, '2025-10-27 10:43:06', '2025-10-27 10:43:49', NULL, NULL, NULL, 1, 'EUR', NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, NULL),
(27, 'test', 'test', 'test', 'vihadep909@hh7f.com', '$2y$10$mRxt2eGz.j/nHgncHh37aO934hDOgKXB2sG5Kkq6LBo5U6VM0CKr6', NULL, 0, NULL, '2025-10-27 13:48:15', '2025-10-27 14:13:42', NULL, NULL, NULL, 1, 'EUR', NULL, NULL, NULL, NULL, NULL, '2025-10-27 14:48:39', '', '', '08139ac7ed8dbb664048f388dd6ca442', '2025-10-27 16:13:42');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_country` (`country`);

--
-- Index pour la table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `trip`
--
ALTER TABLE `trip`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_trip_user` (`user_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pseudo` (`pseudo`),
  ADD UNIQUE KEY `mail` (`mail`),
  ADD UNIQUE KEY `mail_2` (`mail`),
  ADD UNIQUE KEY `pseudo_2` (`pseudo`),
  ADD KEY `status_id` (`status_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;

--
-- AUTO_INCREMENT pour la table `status`
--
ALTER TABLE `status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `trip`
--
ALTER TABLE `trip`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `trip`
--
ALTER TABLE `trip`
  ADD CONSTRAINT `fk_trip_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `status` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
