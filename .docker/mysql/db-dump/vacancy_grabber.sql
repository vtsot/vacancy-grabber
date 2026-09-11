-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql:3306
-- Generation Time: Sep 11, 2026 at 09:07 PM
-- Server version: 8.0.46
-- PHP Version: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vacancy_grabber`
--

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` binary(16) NOT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `created_at`, `updated_at`, `name`, `url`) VALUES
(0x01a077e29a4c7373a404a49a020bd858, '2026-09-06 18:02:14.476210', '2026-09-06 18:02:14.476182', 'BetterMe', 'https://jobs.dou.ua/companies/betterme/vacancies/'),
(0x01a077e29a4c783fa404a49a0233e110, '2026-09-06 18:02:14.476521', '2026-09-06 18:02:14.476505', 'StairwaySoft', 'https://jobs.dou.ua/companies/stairwaysoft/vacancies/'),
(0x01a077e29a4c7b5ba404a49a0281d900, '2026-09-06 18:02:14.476721', '2026-09-06 18:02:14.476707', 'iPOST', 'https://jobs.dou.ua/companies/ipost/vacancies/'),
(0x01a077e29a4c7ce7a404a49a032c5cdb, '2026-09-06 18:02:14.476821', '2026-09-06 18:02:14.476815', 'CRMOZ', 'https://jobs.dou.ua/companies/crmoz/vacancies/'),
(0x01a077e29a4c7deba404a49a0405e7b1, '2026-09-06 18:02:14.476887', '2026-09-06 18:02:14.476883', 'Weltrade', 'https://jobs.dou.ua/companies/weltrade/vacancies/'),
(0x01a077e29a4d7018b25b32da4e1e8a21, '2026-09-06 18:02:14.477003', '2026-09-06 18:02:14.476999', 'Bitmotive', 'https://jobs.dou.ua/companies/bitmotive-llc/vacancies/'),
(0x01a077e29a4d7104b25b32da4f01a9eb, '2026-09-06 18:02:14.477062', '2026-09-06 18:02:14.477058', 'Starlight Media', 'https://jobs.dou.ua/companies/starlightmedia/vacancies/'),
(0x01a077e29a4d720cb25b32da4f528c0e, '2026-09-06 18:02:14.477128', '2026-09-06 18:02:14.477122', 'ConveyThis', 'https://jobs.dou.ua/companies/conveythis/vacancies/'),
(0x01a077e29a4d72f4b25b32da4ffb0ed9, '2026-09-06 18:02:14.477186', '2026-09-06 18:02:14.477181', 'meetFrankie', 'https://jobs.dou.ua/companies/meetfrankie/vacancies/'),
(0x01a077e29a4d73d8b25b32da5050f092, '2026-09-06 18:02:14.477243', '2026-09-06 18:02:14.477238', 'Kraken Leads', 'https://jobs.dou.ua/companies/kraken-leads/vacancies/'),
(0x01a077e29a4d74bcb25b32da514bbdd4, '2026-09-06 18:02:14.477300', '2026-09-06 18:02:14.477294', 'Altum Software', 'https://jobs.dou.ua/companies/altumsoftware/vacancies/'),
(0x01a077e29a4d7760b25b32da53441311, '2026-09-06 18:02:14.477469', '2026-09-06 18:02:14.477464', 'Hurma System', 'https://jobs.dou.ua/companies/hurma-system/vacancies/'),
(0x01a077e29a4d7838b25b32da53a9b9fc, '2026-09-06 18:02:14.477523', '2026-09-06 18:02:14.477518', 'SendPulse', 'https://jobs.dou.ua/companies/sendpulse/vacancies/'),
(0x01a077e29a4d7ac8b25b32da55076d3d, '2026-09-06 18:02:14.477685', '2026-09-06 18:02:14.477676', 'Jamworks', 'https://jobs.dou.ua/companies/jamworks/vacancies/'),
(0x01a077e29a4d7ef8b25b32da560b1ea0, '2026-09-06 18:02:14.477954', '2026-09-06 18:02:14.477945', 'Kultprosvet', 'https://jobs.dou.ua/companies/kultprosvet/vacancies/'),
(0x01a077e29a4e7069931cb571edc49fbc, '2026-09-06 18:02:14.478023', '2026-09-06 18:02:14.478018', 'Firetics', 'https://jobs.dou.ua/companies/firetics/vacancies/'),
(0x01a077e29a4e7165931cb571edd8b93b, '2026-09-06 18:02:14.478086', '2026-09-06 18:02:14.478081', 'Blue-Bird.Tech', 'https://jobs.dou.ua/companies/blue-bird-tech/vacancies/'),
(0x01a077e29a4e727d931cb571ee61d37a, '2026-09-06 18:02:14.478156', '2026-09-06 18:02:14.478149', 'Dedicatted', 'https://jobs.dou.ua/companies/dedicatted/vacancies/'),
(0x01a077e29a4e7489931cb571eee232b9, '2026-09-06 18:02:14.478287', '2026-09-06 18:02:14.478281', 'Artkai', 'https://jobs.dou.ua/companies/artkai/vacancies/'),
(0x01a077e29a4e7651931cb571f033e72a, '2026-09-06 18:02:14.478402', '2026-09-06 18:02:14.478397', 'Zade Space', 'https://jobs.dou.ua/companies/zade-agency/vacancies/'),
(0x01a077e29a4e7721931cb571f06499a8, '2026-09-06 18:02:14.478453', '2026-09-06 18:02:14.478449', 'King Group', 'https://jobs.dou.ua/companies/king-group/vacancies/'),
(0x01a077e29a4e78e1931cb571f092b6eb, '2026-09-06 18:02:14.478565', '2026-09-06 18:02:14.478560', 'IT Delight', 'https://jobs.dou.ua/companies/it-delight/vacancies/'),
(0x01a077e29a4e79c9931cb571f111ba03, '2026-09-06 18:02:14.478624', '2026-09-06 18:02:14.478619', 'Boosta', 'https://jobs.dou.ua/companies/boosta/vacancies/'),
(0x01a077e29a4e7aa5931cb571f1fe450c, '2026-09-06 18:02:14.478678', '2026-09-06 18:02:14.478673', 'Covent IT', 'https://jobs.dou.ua/companies/covent-it/vacancies/'),
(0x01a077e29a4e7b89931cb571f20fe68e, '2026-09-06 18:02:14.478735', '2026-09-06 18:02:14.478731', 'IT Service Ukraine', 'https://jobs.dou.ua/companies/speroteck/vacancies/'),
(0x01a077e29a4e7c6d931cb571f2be8ff8, '2026-09-06 18:02:14.478792', '2026-09-06 18:02:14.478787', 'BRAZY', 'https://jobs.dou.ua/companies/brazy/vacancies/'),
(0x01a077e29a4e7e25931cb571f3e392cd, '2026-09-06 18:02:14.478902', '2026-09-06 18:02:14.478897', 'RemoDevs', 'https://jobs.dou.ua/companies/remodevs/vacancies/'),
(0x01a077e29a4e7efd931cb571f4b11f75, '2026-09-06 18:02:14.478957', '2026-09-06 18:02:14.478952', 'Glorium Technologies', 'https://jobs.dou.ua/companies/glorium-technologies-corp/vacancies/'),
(0x01a077e29a4f7031b7d94ba2699a9130, '2026-09-06 18:02:14.479009', '2026-09-06 18:02:14.479004', 'Stream-market', 'https://jobs.dou.ua/companies/stream-market/vacancies/'),
(0x01a077e29a4f7105b7d94ba26a7bb443, '2026-09-06 18:02:14.479062', '2026-09-06 18:02:14.479057', 'iPay', 'https://jobs.dou.ua/companies/ipay/vacancies/'),
(0x01a077e29a4f71e1b7d94ba26a7c11f2, '2026-09-06 18:02:14.479117', '2026-09-06 18:02:14.479112', 'Cikumas GmbH', 'https://jobs.dou.ua/companies/cikumas-gmbh/vacancies/'),
(0x01a077e29a4f72d5b7d94ba26aa5b46d, '2026-09-06 18:02:14.479177', '2026-09-06 18:02:14.479171', 'Brainstack', 'https://jobs.dou.ua/companies/brainstack/vacancies/'),
(0x01a077e29a4f7759b7d94ba26b0ddbd7, '2026-09-06 18:02:14.479467', '2026-09-06 18:02:14.479460', 'ПриватБанк', 'https://jobs.dou.ua/companies/privatbank/vacancies/'),
(0x01a077e29a4f7a19b7d94ba26be9bc28, '2026-09-06 18:02:14.479643', '2026-09-06 18:02:14.479637', 'SFox', 'https://jobs.dou.ua/companies/sfox/vacancies/'),
(0x01a077e29a4f7cddb7d94ba26c6c95b2, '2026-09-06 18:02:14.479817', '2026-09-06 18:02:14.479811', 'FAVBET', 'https://jobs.dou.ua/companies/favbet/vacancies/'),
(0x01a077e29a4f7f71b7d94ba26d447e7d, '2026-09-06 18:02:14.479985', '2026-09-06 18:02:14.479980', 'eduki', 'https://jobs.dou.ua/companies/lehrermarktplatz/vacancies/'),
(0x01a077e29a50769e83b70c8555b0d621, '2026-09-06 18:02:14.480420', '2026-09-06 18:02:14.480414', 'Хорошоп', 'https://jobs.dou.ua/companies/horoshop/vacancies/'),
(0x01a077e29a50778a83b70c8555f70061, '2026-09-06 18:02:14.480479', '2026-09-06 18:02:14.480474', 'Aimprosoft', 'https://jobs.dou.ua/companies/aimprosoft/vacancies/'),
(0x01a077e29a507a6283b70c8557b3064d, '2026-09-06 18:02:14.480661', '2026-09-06 18:02:14.480655', 'onPoint Studio', 'https://jobs.dou.ua/companies/onpoint-studio/vacancies/'),
(0x01a077e29a507b4a83b70c8557f20164, '2026-09-06 18:02:14.480719', '2026-09-06 18:02:14.480714', 'bip.net.ua', 'https://jobs.dou.ua/companies/bip-buhgalter-idealnogo-pidpriyemtsya/vacancies/'),
(0x01a077e29a507c2683b70c8558a71e0e, '2026-09-06 18:02:14.480774', '2026-09-06 18:02:14.480768', 'Qubit Labs', 'https://jobs.dou.ua/companies/qubit-labs/vacancies/'),
(0x01a077e29a507d1a83b70c85596c0779, '2026-09-06 18:02:14.480835', '2026-09-06 18:02:14.480830', 'V-TER Solutions', 'https://jobs.dou.ua/companies/v-ter-solutions/vacancies/'),
(0x01a077e29a507ed283b70c855afcba1e, '2026-09-06 18:02:14.480945', '2026-09-06 18:02:14.480941', 'Checkbox Group', 'https://jobs.dou.ua/companies/checkbox/vacancies/'),
(0x01a077e29a51700eaf36aaaa60b53bfe, '2026-09-06 18:02:14.481000', '2026-09-06 18:02:14.480996', 'PioGroup Software', 'https://jobs.dou.ua/companies/piogroup-software/vacancies/'),
(0x01a077e29a51720eaf36aaaa6192991c, '2026-09-06 18:02:14.481128', '2026-09-06 18:02:14.481123', 'Candy Grill', 'https://jobs.dou.ua/companies/candygrill/vacancies/'),
(0x01a077e29a5172faaf36aaaa61c9f0c8, '2026-09-06 18:02:14.481187', '2026-09-06 18:02:14.481182', 'Digital Hiring', 'https://jobs.dou.ua/companies/digital-hiring/vacancies/'),
(0x01a077e29a5173deaf36aaaa62121c4d, '2026-09-06 18:02:14.481244', '2026-09-06 18:02:14.481238', 'Flexi IT', 'https://jobs.dou.ua/companies/flexi/vacancies/'),
(0x01a077e29a5174c6af36aaaa62f2ae10, '2026-09-06 18:02:14.481302', '2026-09-06 18:02:14.481297', '8allocate', 'https://jobs.dou.ua/companies/8allocate/vacancies/'),
(0x01a077e29a5175aaaf36aaaa638b738f, '2026-09-06 18:02:14.481359', '2026-09-06 18:02:14.481353', 'INFOBUS', 'https://jobs.dou.ua/companies/infobus/vacancies/'),
(0x01a077e29a517696af36aaaa63ea76ec, '2026-09-06 18:02:14.481418', '2026-09-06 18:02:14.481413', 'Softprom', 'https://jobs.dou.ua/companies/softprom/vacancies/'),
(0x01a077e29a51777aaf36aaaa63fc4a5a, '2026-09-06 18:02:14.481475', '2026-09-06 18:02:14.481470', 'Honeytech', 'https://jobs.dou.ua/companies/honeytech/vacancies/'),
(0x01a077e29a51785eaf36aaaa646416d6, '2026-09-06 18:02:14.481532', '2026-09-06 18:02:14.481527', 'Сільпо', 'https://jobs.dou.ua/companies/silpo/vacancies/'),
(0x01a077e29a517a1eaf36aaaa65242519, '2026-09-06 18:02:14.481644', '2026-09-06 18:02:14.481639', 'Alcor', 'https://jobs.dou.ua/companies/alcor/vacancies/'),
(0x01a077e29a517b0eaf36aaaa6548f82c, '2026-09-06 18:02:14.481704', '2026-09-06 18:02:14.481699', 'airSlate', 'https://jobs.dou.ua/companies/airslate/vacancies/'),
(0x01a077e6717e760683f7851aba00106b, '2026-09-06 18:06:26.174360', '2026-09-06 18:06:26.174259', 'MOJAM', NULL),
(0x01a077f79c127074ab26a317a794f87e, '2026-09-06 18:25:11.186008', '2026-09-07 13:16:20.460431', 'WhiteTech', 'https://jobs.dou.ua/companies/whitetech/vacancies/'),
(0x01a07808bfda712cbb4e6f6d236ed7d6, '2026-09-06 18:43:54.458053', '2026-09-06 18:43:54.457969', 'Zone3000', 'https://jobs.dou.ua/companies/zone3000/'),
(0x01a0780f814471ffafa3e13d108feb61, '2026-09-06 18:51:17.188103', '2026-09-06 18:51:17.188013', 'leguidenoir.com Aleksey Глухарев', 'https://leguidenoir.com/'),
(0x01a0781f794378c6bd22194e3c112c3c, '2026-09-06 19:08:43.715539', '2026-09-06 20:55:00.332406', 'Mobilunity', 'https://jobs.dou.ua/companies/mobilunity/vacancies/'),
(0x01a07824039671708959bb82761a1656, '2026-09-06 19:13:41.270073', '2026-09-06 19:13:41.269998', 'TrueLabel', 'https://jobs.dou.ua/companies/truelabel/'),
(0x01a07827022579c1928928071aaa4ee3, '2026-09-06 19:16:57.509601', '2026-09-06 19:16:57.509513', 'EspoCRM', 'https://jobs.dou.ua/companies/letrium-ltd-espocrm/'),
(0x01a0782ca88979d1a0a73089e07255df, '2026-09-06 19:23:07.785603', '2026-09-06 19:23:07.785512', 'Applyft', 'https://jobs.dou.ua/companies/applyft/'),
(0x01a0782ede6374aebb86de3782637bdd, '2026-09-06 19:25:32.643276', '2026-09-06 19:25:32.643185', 'UPSTARS', 'https://www.linkedin.com/jobs/view/4426639833/?trk=eml-email_jobs_viewed_job_reminder_01-job_card-0-jobcard_body_4426639833&refId=U8xSE51mRqOFmyMToDXE6A%3D%3D&trackingId=rWdoPkrWQzOtzZeZzizudw%3D%3D'),
(0x01a0783b4ba57ac9926e4ccb788b144c, '2026-09-06 19:39:07.045668', '2026-09-06 19:39:07.045581', 'SharpMinds', 'https://jobs.dou.ua/companies/sharpminds/'),
(0x01a0783cac3c78c69368bf5b457a2ff5, '2026-09-06 19:40:37.308537', '2026-09-06 19:40:37.308442', 'Avenga', 'https://jobs.dou.ua/companies/avenga/'),
(0x01a07841ef8174eda1e12b599d8704f0, '2026-09-06 19:46:22.209292', '2026-09-06 19:46:22.209206', 'Opinion Corp', 'https://jobs.dou.ua/companies/opinion-corp/'),
(0x01a078437e787b589f8ef781982e286b, '2026-09-06 19:48:04.344701', '2026-09-06 19:48:04.344608', 'Talmatic', 'https://jobs.dou.ua/companies/talmatic/'),
(0x01a07844dd99720eaa38ea7657a508f2, '2026-09-06 19:49:34.233106', '2026-09-06 19:49:34.233008', 'ImproveIT Solutions', 'https://jobs.dou.ua/companies/improveit-solutions-ukraine/'),
(0x01a078467a397041a16d87628ad8390a, '2026-09-06 19:51:19.864992', '2026-09-06 19:51:19.864901', 'PulsePaw', 'https://jobs.dou.ua/companies/pulsepaw/'),
(0x01a0784b372b75f5b2e5cadb164760c8, '2026-09-06 19:56:30.379358', '2026-09-06 19:56:30.379260', 'Agiliway', 'https://jobs.dou.ua/companies/agiliway/vacancies/365761/'),
(0x01a0785562577879ab3325440e82afff, '2026-09-06 20:07:36.791515', '2026-09-06 20:07:36.791389', 'Corefy', 'https://jobs.dou.ua/companies/corefy/'),
(0x01a0789598f27d51b6d040e3d0adcef6, '2026-09-06 21:17:45.074841', '2026-09-06 21:17:45.074812', 'Stape', 'https://jobs.dou.ua/companies/stape-inc/vacancies/'),
(0x01a0789598f772a686d2b62628a3c690, '2026-09-06 21:17:45.079163', '2026-09-06 21:17:45.079150', 'Dinarys', 'https://jobs.dou.ua/companies/dinarys/vacancies/'),
(0x01a0789598fa7adcaf99040b719cbe99, '2026-09-06 21:17:45.082690', '2026-09-06 21:17:45.082680', 'Nova Digital', 'https://jobs.dou.ua/companies/nova-digital/vacancies/'),
(0x01a0789598fb79d2abe632feb877e652, '2026-09-06 21:17:45.083624', '2026-09-06 21:17:45.083616', 'Frozeneon', 'https://jobs.dou.ua/companies/frozeneon/vacancies/'),
(0x01a0789598fe7f1ba7bd9fd77bef8aa8, '2026-09-06 21:17:45.086961', '2026-09-06 21:17:45.086951', 'DNIPRO-M', 'https://jobs.dou.ua/companies/dnipro-m/vacancies/'),
(0x01a07895991677f49580dd51dfd198e3, '2026-09-06 21:17:45.110505', '2026-09-06 21:17:45.110494', 'N-iX', 'https://jobs.dou.ua/companies/n-ix/vacancies/'),
(0x01a07b67a8f57b7a8ca0d335d78190de, '2026-09-07 10:26:26.165711', '2026-09-07 10:26:26.165623', 'Togizu Technologies LTD', 'https://www.togizu.com/'),
(0x01a07b73fef870d9a08ca3b94261e11b, '2026-09-07 10:39:54.616031', '2026-09-07 10:39:54.615943', 'Software Service and Innovation', 'https://soft-innovation.ch/offer/3-php-developer'),
(0x01a07c03366a739a9f18a8169909cdc5, '2026-09-07 13:16:20.458220', '2026-09-07 13:16:20.458195', 'Rebbix', 'https://jobs.dou.ua/companies/rebbix/vacancies/'),
(0x01a07c03366b7680994cb218696acddc, '2026-09-07 13:16:20.459411', '2026-09-07 13:16:20.459398', 'Zero Task Labs', 'https://jobs.dou.ua/companies/zerotasklabs/vacancies/'),
(0x01a07c03366d7617969584c21a071d30, '2026-09-07 13:16:20.461385', '2026-09-07 13:16:20.461373', 'Togizu Technologies', 'https://jobs.dou.ua/companies/togizu-technologies-ltd/vacancies/'),
(0x01a07c03367070bd81be1d9bf638a26c, '2026-09-07 13:16:20.464043', '2026-09-07 13:16:20.464028', 'Stfalcon', 'https://jobs.dou.ua/companies/stfalcon-studio/vacancies/'),
(0x01a07cad950e71d7be93a4e255c2f443, '2026-09-07 16:22:25.806098', '2026-09-07 16:22:25.806050', 'AnyforSoft', 'https://jobs.dou.ua/companies/anyforsoft/vacancies/'),
(0x01a07d5e9e097f69b0db76c1112c10da, '2026-09-07 19:35:47.977965', '2026-09-07 19:35:47.977891', 'EVOPLAY', 'https://jobs.dou.ua/companies/evoplay/vacancies/'),
(0x01a08169f7517db4bc04147b9fb373e9, '2026-09-08 14:26:40.593872', '2026-09-08 14:26:40.593858', 'SoloWay Technologies', 'https://jobs.dou.ua/companies/soloway-tech/vacancies/'),
(0x01a08198359a707e80c82a67665269b9, '2026-09-08 15:17:11.193995', '2026-09-08 15:17:11.193899', 'Latimeria', NULL),
(0x01a0819835a07c1c83b47c235efe355a, '2026-09-08 15:17:11.200762', '2026-09-08 15:17:11.200719', 'KAWORKS', NULL),
(0x01a0819835a87740988b0182d7a6a579, '2026-09-08 15:17:11.208440', '2026-09-08 15:17:11.208391', 'TradeFactor', NULL),
(0x01a08c681cef7e5f9669ddb09d4a582f, '2026-09-10 17:40:28.527881', '2026-09-10 17:40:28.527791', 'Gamingtec', 'https://jobs.dou.ua/companies/gamingtec/vacancies/'),
(0x01a08c681cfb76e785525a89f55e3f15, '2026-09-10 17:40:28.539394', '2026-09-10 17:40:28.539287', 'Kozak Group', 'https://jobs.dou.ua/companies/kozak-group/vacancies/'),
(0x01a08c681d027c9a9ed1e90683cb9145, '2026-09-10 17:40:28.546787', '2026-09-10 17:40:28.546734', 'Altamira', 'https://jobs.dou.ua/companies/altamira-ai/vacancies/'),
(0x01a08c681d087870a4840fed3b98a0b4, '2026-09-10 17:40:28.552522', '2026-09-10 17:40:28.552472', 'Embrox Solutions', 'https://jobs.dou.ua/companies/embrox-solutions/vacancies/'),
(0x01a08c681d367ed2af4f7d6c2b579bf1, '2026-09-10 17:40:28.598919', '2026-09-10 17:40:28.598831', 'Erbis', 'https://jobs.dou.ua/companies/erbis/vacancies/'),
(0x01a091ade6af7d34ba35982bb4a25fbb, '2026-09-11 18:14:48.239832', '2026-09-11 20:34:28.115140', 'Lavriv, Sole Proprietor (Ukraine)', 'https://persiahr.com/company/2024');

-- --------------------------------------------------------

--
-- Table structure for table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime(6) DEFAULT NULL,
  `execution_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20260906173232', '2026-09-06 18:02:07.275529', 805),
('DoctrineMigrations\\Version20260906175135', '2026-09-06 18:02:08.087575', 10),
('DoctrineMigrations\\Version20260906193236', '2026-09-06 19:36:41.756094', 54),
('DoctrineMigrations\\Version20260907131240', '2026-09-07 13:15:12.215619', 194),
('DoctrineMigrations\\Version20260907135929', '2026-09-07 14:04:31.704900', 28),
('DoctrineMigrations\\Version20260907162801', '2026-09-07 16:45:36.827976', 123),
('DoctrineMigrations\\Version20260908110933', '2026-09-08 14:26:17.635696', 1080),
('DoctrineMigrations\\Version20260908141511', '2026-09-08 14:26:18.721889', 79),
('DoctrineMigrations\\Version20260908142218', '2026-09-08 14:26:18.807220', 11),
('DoctrineMigrations\\Version20260910175155', '2026-09-10 17:53:14.506092', 111),
('DoctrineMigrations\\Version20260911205603', '2026-09-11 20:59:10.698950', 46);

-- --------------------------------------------------------

--
-- Table structure for table `grab_logs`
--

CREATE TABLE `grab_logs` (
  `id` binary(16) NOT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `counter_company_created` int NOT NULL DEFAULT '0',
  `counter_vacancy_created` int NOT NULL DEFAULT '0',
  `description` longtext,
  `source_id` binary(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `grab_logs`
--

INSERT INTO `grab_logs` (`id`, `created_at`, `updated_at`, `counter_company_created`, `counter_vacancy_created`, `description`, `source_id`) VALUES
(0x01a07c0336b17060b1f6b36ea9345e67, '2026-09-07 13:16:20.529016', '2026-09-07 13:16:20.528998', 4, 5, NULL, 0x01a077e2815c7509b25e6fcea82c5d29),
(0x01a07cad952871babf91553b46794f83, '2026-09-07 16:22:25.832105', '2026-09-07 16:22:25.832092', 1, 1, NULL, 0x01a077e2815c7509b25e6fcea82c5d29),
(0x01a07d5e9e34786fb82aa231f93b3893, '2026-09-07 19:35:48.020529', '2026-09-07 19:35:48.020504', 1, 1, NULL, 0x01a077e2815c7509b25e6fcea82c5d29),
(0x01a08169f76176da9164f2e08a13ee34, '2026-09-08 14:26:40.609433', '2026-09-08 14:26:40.609422', 1, 3, NULL, 0x01a077e2815c7509b25e6fcea82c5d29),
(0x01a0819835ab7d04a9ebd97613d367b4, '2026-09-08 15:17:11.211820', '2026-09-08 15:17:11.211786', 3, 4, NULL, 0x01a08169a23b78d7b5288e259d626675),
(0x01a08c681dbf7b199d51a739c9d96970, '2026-09-10 17:40:28.735689', '2026-09-10 17:40:28.735636', 5, 6, NULL, 0x01a077e2815c7509b25e6fcea82c5d29),
(0x01a091ade6cc7beaac8a86cf3c899164, '2026-09-11 18:14:48.268754', '2026-09-11 18:14:48.268733', 1, 1, NULL, 0x01a077e2815c7509b25e6fcea82c5d29);

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `id` binary(16) NOT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `noted_at` datetime(6) DEFAULT NULL,
  `description` longtext,
  `company_id` binary(16) DEFAULT NULL,
  `vacancy_id` binary(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`id`, `created_at`, `updated_at`, `noted_at`, `description`, `company_id`, `vacancy_id`) VALUES
(0x01a077f5b4d673148b19bf4c1c77aa71, '2026-09-06 18:23:06.454186', '2026-09-06 18:24:26.472838', '2025-10-20 00:00:00.000000', 'google meet', NULL, 0x01a077efdce37999a49f4b75ab4f84e0),
(0x01a077f673807d088128f35a34dae18e, '2026-09-06 18:23:55.264824', '2026-09-06 18:24:26.472868', '2025-10-20 00:00:00.000000', 'feedback не был получен. при пряммом обращении - HR просто отморозился', NULL, 0x01a077efdce37999a49f4b75ab4f84e0),
(0x01a077fae44677b38185dd175c0827eb, '2026-09-06 18:28:46.278483', '2026-09-06 18:32:29.168264', '2026-05-25 00:00:00.000000', 'sallary: 4500~5000~5500 EUR,\ngross per month - брутто в месяц,\nточно не помню,\nвроде ~4000~5000 EUR tax included (включая налоги)', NULL, 0x01a077f90ab0708b855324c7f2d12ad0),
(0x01a077fae44678438185dd175cf07cc3, '2026-09-06 18:28:46.278525', '2026-09-06 18:34:03.424134', '2026-05-25 00:00:00.000000', 'May 27, 2026 2:00pm-3:00pm (GMT+03:00) Kyiv\nInterviewers: Liudmyla Burdega', NULL, 0x01a077f90ab0708b855324c7f2d12ad0),
(0x01a077ffbb207074b300fcf8e14182d5, '2026-09-06 18:34:03.424019', '2026-09-06 18:34:03.423997', '2026-05-25 00:00:00.000000', 'google meet:\nhttps://meet.google.com/rrm-akxi-akz', NULL, 0x01a077f90ab0708b855324c7f2d12ad0),
(0x01a078018abb78e9898ed6b8f4ae36f5, '2026-09-06 18:36:02.107561', '2026-09-06 18:36:02.107538', '2026-05-29 00:00:00.000000', 'запросил обещанный feedback\nfeedback не получен. HR морозитися', NULL, 0x01a077f90ab0708b855324c7f2d12ad0),
(0x01a078018abb797d898ed6b8f541f305, '2026-09-06 18:36:02.107603', '2026-09-06 18:36:02.107598', '2026-06-09 00:00:00.000000', 'запросил повторный feedback,\nemail: liudmyla.bu@whitetech.com\nи PM в linkedin.com\n\nhttps://bitbucket.org/vitalii-sotnykov/resume-2025/src/39aa9673d15d17cb9f4e1c16309d2169b8d3f853/linkedin.com', NULL, 0x01a077f90ab0708b855324c7f2d12ad0),
(0x01a0780252af73bc9129856eb7be5193, '2026-09-06 18:36:53.295229', '2026-09-06 18:36:53.295207', '2026-06-09 00:00:00.000000', 'feedback: не взяли\nhttps://www.linkedin.com/messaging/thread/2-YjgxOGJhMjctNzg0Zi00NTdmLWI0NWEtYmZiZGExNWE3MWYwXzEwMA==/', NULL, 0x01a077f90ab0708b855324c7f2d12ad0),
(0x01a0780bf7de7d34800af98014ce6f3b, '2026-09-06 18:47:25.406831', '2026-09-06 18:47:25.406804', '2026-06-12 00:00:00.000000', 'Linkedin Chat: \nhttps://www.linkedin.com/messaging/thread/2-MjkwNDczN2MtYThkYS00MTJmLWEzYTktZWJjMjdiNTFjN2E4XzEwMA==/\nQuestions:\nhttps://bitbucket.org/vitalii-sotnykov/resume-2025/src/39aa9673d15d17cb9f4e1c16309d2169b8d3f853/questions-zone3000.md', NULL, 0x01a07809bfef727dba772ac2a92f6cb4),
(0x01a0780c84b87406a11e382cfef6ecf9, '2026-09-06 18:48:01.464247', '2026-09-06 18:48:01.464223', '2026-06-15 00:00:00.000000', 'feedback: не взяли\nReact required', NULL, 0x01a07809bfef727dba772ac2a92f6cb4),
(0x01a078125a67730f988f513aef8ac81c, '2026-09-06 18:54:23.847186', '2026-09-06 18:54:23.847166', '2026-06-25 00:00:00.000000', 'feedback:\nнаш СЕО сказал,\nчто ему достаточно нашего темпа и мы пока не будем брать еще одного разработчика.', NULL, 0x01a0781075b8715e98c75dd1bc7cd336),
(0x01a078125a67739b988f513aeffd3546, '2026-09-06 18:54:23.847227', '2026-09-06 18:54:23.847221', '2026-06-25 00:00:00.000000', 'и я особо не хочу\nstack: symfony 3.4. В целом есть в планах ее поднять до 5-6 хотя бы\nstack старее чем у Игоря.\nмутная перспектива роста.', NULL, 0x01a0781075b8715e98c75dd1bc7cd336),
(0x01a07821dde57ef2abe4a484114721b8, '2026-09-06 19:11:20.549946', '2026-09-06 19:11:20.549923', '2026-07-13 00:00:00.000000', 'хз, думаю что не подхожу.\n\nno feedback', NULL, 0x01a07820672f7cf69cebd2173769d68f),
(0x01a0782381737ba2865e091a8f49aaaf, '2026-09-06 19:13:07.955735', '2026-09-06 19:13:07.955714', '2026-07-21 00:00:00.000000', 'отправил повторно на сайте', NULL, 0x01a07822ea057970b60f7ad44129c883),
(0x01a078256ff3793fb17907d153c2c4b4, '2026-09-06 19:15:14.547581', '2026-09-06 19:15:14.547559', '2026-07-13 00:00:00.000000', 'no feedback', NULL, 0x01a07824ab0e750cab32f7609961c4be),
(0x01a07828faf07696b78612da383047da, '2026-09-06 19:19:06.736410', '2026-09-06 19:19:06.736387', '2026-07-13 00:00:00.000000', 'no feedback', NULL, 0x01a078285fc7724fa6bb27d7dcb6ac10),
(0x01a0782b98cd70989e7103b52c169630, '2026-09-06 19:21:58.221029', '2026-09-06 19:21:58.220993', '2026-07-13 00:00:00.000000', 'Вкансия только на linkedin. Отправил повторно.\nlinkedin  Vacancy: https://www.linkedin.com/jobs/view/4436498004/?alternateChannel=search&eBP=NON_CHARGEABLE_CHANNEL&refId=01TZF7ckGZWsC0FX337FAw%3D%3D&trackingId=Gqqqwd8fygJ41tP43miTpQ%3D%3D', NULL, 0x01a0782a8d6679dd8fa1a95721b1be4f),
(0x01a0782c02d67b6682662e115b27b291, '2026-09-06 19:22:25.366719', '2026-09-06 19:22:25.366696', '2026-07-21 00:00:00.000000', 'На жаль, з технічних причин наша система не відобразила її в роботі так, як мала б.\nСаме тому на цьому етапі, на жаль, не зможемо долучити вас до поточного процесу.', NULL, 0x01a0782a8d6679dd8fa1a95721b1be4f),
(0x01a0782dfddf701985c81eb6ea35e6ef, '2026-09-06 19:24:35.166997', '2026-09-06 19:24:35.166976', '2026-07-22 00:00:00.000000', 'no feedback', NULL, 0x01a0782d7a1d7c5688f73e44d40d69c4),
(0x01a0782feec374b3914091460cc3b581, '2026-09-06 19:26:42.371297', '2026-09-06 19:26:42.371291', '2026-07-22 00:00:00.000000', 'no feedback', NULL, 0x01a0782feec373ff914091460c96402c),
(0x01a07832682a778e94e15f54ef7ff55a, '2026-09-06 19:29:24.522471', '2026-09-06 19:29:24.522447', '2026-07-23 00:00:00.000000', 'feedback: двох тижнів (14 дней) = 6 августа 2026 года', NULL, 0x01a0783190037adeb3bd97463735ab08),
(0x01a07832682a782e94e15f54ef94565e, '2026-09-06 19:29:24.522519', '2026-09-06 19:29:24.522513', '2026-07-23 00:00:00.000000', 'no feedback', NULL, 0x01a0783190037adeb3bd97463735ab08),
(0x01a0784177ab77698656843aeb9465d8, '2026-09-06 19:45:51.531463', '2026-09-06 19:45:51.531440', '2026-07-30 00:00:00.000000', 'no feedback', NULL, 0x01a07840bf5b7a56a3ecd3ff52b2c0ef),
(0x01a078430f2d7e3296c58bafba15c7c5, '2026-09-06 19:47:35.853897', '2026-09-06 19:47:35.853873', '2026-07-30 00:00:00.000000', 'no feedback', NULL, 0x01a078429cb57de8803125097df76672),
(0x01a0784482cc75acb736e4e29b5b4fe8, '2026-09-06 19:49:10.988360', '2026-09-06 19:49:10.988354', '2026-07-30 00:00:00.000000', 'no feedback', NULL, 0x01a0784482cc74f4b736e4e29b12d5dd),
(0x01a07845e87a7811a6c65944c2ed9b8b, '2026-09-06 19:50:42.554513', '2026-09-06 19:50:42.554506', '2026-07-30 00:00:00.000000', 'no feedback', NULL, 0x01a07845e87a7759a6c65944c2003ad9),
(0x01a07847719d795896390918e5419090, '2026-09-06 19:52:23.197594', '2026-09-06 19:52:23.197588', '2026-07-30 00:00:00.000000', 'no feedback', NULL, 0x01a07847719d789096390918e50e7a43),
(0x01a07852911d7ea4a6726c9a1227b563, '2026-09-06 20:04:32.157927', '2026-09-06 20:04:32.157903', '2026-08-20 00:00:00.000000', 'no feedback', NULL, 0x01a077e29a6670a7a497b7f68ef3d33f),
(0x01a078543c067535b2ecca8751200380, '2026-09-06 20:06:21.446311', '2026-09-06 20:06:21.446285', '2026-08-21 00:00:00.000000', 'Переписал Резюме\nnew: https://bitbucket.org/vitalii-sotnykov/resume-2025/src/39aa9673d15d17cb9f4e1c16309d2169b8d3f853/cv.vitalii.sotnykov_ENG.pdf\nold: https://bitbucket.org/vitalii-sotnykov/resume-2025/src/39aa9673d15d17cb9f4e1c16309d2169b8d3f853/cv.vitalii.sotnykov_ENG_2025.pdf', NULL, NULL),
(0x01a078566c29716d809271fda5571d8c, '2026-09-06 20:08:44.841081', '2026-09-06 20:08:44.841040', '2026-08-21 00:00:00.000000', 'no feedback', NULL, 0x01a078561b0a77839261a09356475460),
(0x01a07b5324077683a5e0480fbf140a7f, '2026-09-07 10:04:01.415386', '2026-09-07 10:12:42.761874', '2026-09-07 00:00:00.000000', 'отправил через сайт\nhttps://jamworksuk.bamboohr.com/careers/32?source=aWQ9OA%3D%3D\n\nуказал ЗП 3000', NULL, 0x01a077e29a5e7ada837c2cf973c7fbac),
(0x01a07b65aa197ad3b10f22915f678d8b, '2026-09-07 10:24:15.385682', '2026-09-07 10:24:15.385659', '2026-09-07 00:00:00.000000', 'отправил заявку на сайте DOU', NULL, 0x01a077e29a6d7b8d8e2049e9f54b451b),
(0x01a07b6ac7d17a81b511dd756b2fa6bf, '2026-09-07 10:29:50.673669', '2026-09-07 10:29:50.673663', '2026-09-07 00:00:00.000000', 'отправил заяку https://www.togizu.com/send-cv', NULL, 0x01a07b6ac7d179cdb511dd756a9fe5ba),
(0x01a07b769d7d78efb0abd4cfb73127b7, '2026-09-07 10:42:46.269562', '2026-09-07 10:42:46.269538', '2026-09-07 00:00:00.000000', 'отправил резюме на email: info@soft-innovation.ch\nhttps://soft-innovation.ch/offer/3-php-developer', NULL, 0x01a07b753a907486b31a9fa1530cddb9),
(0x01a07b7ea7187ef3b690807c32a8b4d7, '2026-09-07 10:51:33.016947', '2026-09-07 10:51:33.016923', '2026-09-07 00:00:00.000000', 'отправил заявку на сайте DOU', NULL, 0x01a077e29a697e9092f7c93fef07ece4),
(0x01a07b83c9897c99abd8a7da18c4094c, '2026-09-07 10:57:09.513796', '2026-09-07 10:57:09.513770', '2026-09-07 00:00:00.000000', 'отправил заявку на сайте DOU', NULL, 0x01a077e29a627c67ba7ce435c08a1bf5),
(0x01a07c056fd971258ff75fe37b744005, '2026-09-07 13:18:46.233064', '2026-09-07 13:18:46.233040', '2026-06-09 00:00:00.000000', 'feedback: не взяли', 0x01a077f79c127074ab26a317a794f87e, NULL),
(0x01a07c0b3a4b7758bc9ae7e7322b6c78, '2026-09-07 13:25:05.739460', '2026-09-07 13:25:05.739436', '2026-09-07 00:00:00.000000', 'указал зп 2500-3000\nзаявка https://docs.google.com/forms/u/0/d/e/1FAIpQLSeA4eKpNW-eHCxlkIBK1lkES0tON-QbNlzNcFpgyKYtXGpyIg/formResponse', NULL, 0x01a07c0336707cd581be1d9bf71a1d5a),
(0x01a07c1018117269b9dc81473cef1a40, '2026-09-07 13:30:24.657145', '2026-09-07 13:30:24.657124', '2026-09-07 00:00:00.000000', 'отправил заяку на сайте DOU', NULL, 0x01a07b6ac7d179cdb511dd756a9fe5ba),
(0x01a07c13a62e75d5a2ec7130b5ca1f08, '2026-09-07 13:34:17.646358', '2026-09-07 13:34:17.646327', '2026-09-07 00:00:00.000000', 'must be available to work New York time (10:00 AM — 7:00 PM EST)', NULL, 0x01a077e29a5a79e096c2eba3cea37f2d),
(0x01a07d61fe6a7a23a6e8b6514daaf71b, '2026-09-07 19:39:29.258638', '2026-09-07 19:39:29.258613', '2026-09-07 00:00:00.000000', 'заполнил заявку на сайте DOU', NULL, 0x01a07d5e9e0c773b8bb7304dceb120e5),
(0x01a0816d9d1b70adb8bae36190c9cd33, '2026-09-08 14:30:39.643033', '2026-09-08 14:30:39.642988', '2026-09-08 00:00:00.000000', 'Team Lead is not interested', NULL, 0x01a08169f7537be68a5e4da7e8486ec8),
(0x01a0816ecbf07f4bb0e0c8a7400fc525, '2026-09-08 14:31:57.168968', '2026-09-08 14:31:57.168939', '2026-09-08 00:00:00.000000', 'Resume Sent At: August 14, 2026 \nSenior PHP Engineer в airSlate Київ, віддалено', 0x01a077e29a517b0eaf36aaaa6548f82c, NULL),
(0x01a0817174977eaf9459207dcc444360, '2026-09-08 14:34:51.415929', '2026-09-08 14:34:51.415902', '2026-09-08 00:00:00.000000', 'Digital Hiring is an international recruitment partner', 0x01a077e29a5172faaf36aaaa61c9f0c8, NULL),
(0x01a081745b487260acb2dbedb6229817, '2026-09-08 14:38:01.544143', '2026-09-08 14:38:01.544120', '2026-09-08 00:00:00.000000', 'Vanilla PHP refers to standard, pure PHP code written without any external frameworks, libraries, or extra tools', NULL, 0x01a08169f7527939b90b5bf4352f01f7),
(0x01a0817922577dcaa3869054fab34a15, '2026-09-08 14:43:14.647874', '2026-09-08 14:43:14.647849', '2026-09-08 00:00:00.000000', 'отправил на сайте DOU', NULL, 0x01a08169f75173a8bc04147b9f98b6e7),
(0x01a0819c0c9c755ea3534c33ba482d4c, '2026-09-08 15:21:22.844333', '2026-09-08 15:21:47.915648', '2026-09-08 00:00:00.000000', 'Resume Sent At: August 21, 2026 (DOU)', 0x01a077e29a507ed283b70c855afcba1e, NULL),
(0x01a081a165d474ea875cea175b491543, '2026-09-08 15:27:13.364299', '2026-09-08 15:35:28.751165', '2026-09-08 00:00:00.000000', 'Отрпавил заявку на сайте Djinni\nУказал ЗП: $2700', NULL, 0x01a08198359e769d9b5841daf6e014d0),
(0x01a081a50062718d8ed11a0611de8b01, '2026-09-08 15:31:09.538090', '2026-09-08 15:31:09.538065', '2026-09-08 00:00:00.000000', 'Профіль не відповідає\n* Україна Відгуки з цієї країни не приймаються', NULL, 0x01a0819835aa79de8fec3e3dae51fe9b),
(0x01a081a5c98d77e68b154efcab7bb092, '2026-09-08 15:32:01.037496', '2026-09-08 15:32:01.037469', '2026-09-08 00:00:00.000000', 'Англійська C1 – Просунутий\nВаш рівень: B1 – Середній \nне могу подать заявку', NULL, 0x01a0819835a370dd9e418dcc07800c93),
(0x01a08c6387437ddcbf4f72fbf5e3fe3a, '2026-09-10 17:35:28.067827', '2026-09-10 17:35:28.067696', '2026-09-10 00:00:00.000000', '@T_Svit_A\nДоброго дня! Після розгляду всіх кандидатів ми вирішили продовжити процес з іншою кандидатурою. Дякую вам за приділений час.\nБажаю успіху у пошуку роботи!', NULL, 0x01a08198359e769d9b5841daf6e014d0),
(0x01a08c63ecb3782b9de0e252adaef4a6, '2026-09-10 17:35:54.035467', '2026-09-10 17:35:54.035346', '2026-09-10 00:00:00.000000', 'Доброго дня.\nСумно чути.\nПриемного дня', NULL, 0x01a08198359e769d9b5841daf6e014d0),
(0x01a08c650f50794db2b68307e23bdf11, '2026-09-10 17:37:08.432576', '2026-09-10 17:38:02.110547', '2026-09-08 00:00:00.000000', 'Це сайт нашого клієнта:\nhttps://deka.ua/ua/naruchnyy-godynnyk/?gad_source=1&gad_campaignid=1356266060&gbraid=0AAAAADBHLlwngZfd3jFAwWwgB0NmcVCN8&gclid=CjwKCAjwwJzPBhBREiwAJfHRnSIBGAFTCf6nYIgEV5nzzs8_EiHlZGGV_Qon8qxisf9BcJT3lLBstxoCfiAQAvD_BwE\nПерегляньте собі, будь ласка.😊', NULL, 0x01a08169f75173a8bc04147b9f98b6e7),
(0x01a08c65e0fe719bbb4a7e1b052f90f6, '2026-09-10 17:38:02.110072', '2026-09-10 17:38:02.109996', '2026-09-08 00:00:00.000000', 'З вашого дозволу поставлю декілька уточнювальних запитань:\n\n✅Скільки у вас комерційного досвіду роботи з PHP?\n✅Скільки у вас комерційного досвіду роботи з Laravel?\n✅Скільки у вас комерційного досвіду роботи з Vue, Nuxt.js?\n✅Які у вас фінансові очікування?\nМожете коротенько відповісти.', NULL, 0x01a08169f75173a8bc04147b9f98b6e7),
(0x01a08c65e0fe735bbb4a7e1b05ca9c5a, '2026-09-10 17:38:02.110202', '2026-09-10 17:38:02.110184', '2026-09-08 00:00:00.000000', '> Скільки у вас комерційного досвіду роботи з PHP?\nз 2011 року \n\n> Скільки у вас комерційного досвіду роботи з Laravel?\nостання версiя з якою рабатал 5.3 \nосновний framework это symfony \n\n> Скільки у вас комерційного досвіду роботи з Vue, Nuxt.js?\nНе знаю, як відповісти на це запитання \nякщо з’являється новий проєкт, то працюємо на останніх версіях \n \n> Які у вас фінансові очікування?\nprobation period: 2700', NULL, 0x01a08169f75173a8bc04147b9f98b6e7),
(0x01a08c66430a76f98b83362730fd0251, '2026-09-10 17:38:27.210429', '2026-09-10 17:38:40.535703', '2026-09-10 00:00:00.000000', 'Доброго дня! 🌿\nДуже дякуємо вам за відгук на позицію!\nХочу повідомити, що вчора у клієнта відбулася технічна співбесіда, після якої кандидат отримав оффер і його прийняв.\nТому наразі вакансію вже закрито. Щиро дякуємо за ваш інтерес до позиції та приділений час! \nБудемо раді звернутися до вас, якщо з’явиться відповідна можливість у майбутньому.', NULL, 0x01a08169f75173a8bc04147b9f98b6e7),
(0x01a08c6918b8767d929902fe94405d72, '2026-09-10 17:41:32.984390', '2026-09-10 17:41:32.984325', '2026-09-10 00:00:00.000000', 'заполнил заявку на сайте DOU', NULL, 0x01a08c681cf4780d8a6e7b201e87bbf8),
(0x01a08c69858870bb8754b5071a351ba9, '2026-09-10 17:42:00.839990', '2026-09-10 17:42:00.839855', '2026-09-10 00:00:00.000000', 'Magento - не интресно', NULL, 0x01a08c681d007027ad043662b295922f),
(0x01a08c6b06ff738fba4798fe58e43013, '2026-09-10 17:43:39.519170', '2026-09-10 17:43:39.519005', '2026-09-10 00:00:00.000000', 'заполднил заявку на сайте \nhttps://job-boards.eu.greenhouse.io/altamirafreelance/jobs/4969341101/confirmation?gh_src=xullt0h2teu', NULL, 0x01a08c681d057cb993756ed2c1745f29),
(0x01a08c6b8eb87726bb8427ce7cce25d0, '2026-09-10 17:44:14.264423', '2026-09-10 17:44:14.264332', '2026-09-10 00:00:00.000000', 'WordPress - не интресно', NULL, 0x01a08c681d0a7e12bf3919dec22b56d0),
(0x01a08c6c57357ad395bbf5c1e648c92f, '2026-09-10 17:45:05.589674', '2026-09-10 17:45:05.589621', '2026-09-10 00:00:00.000000', 'WordPress - не интересно', NULL, 0x01a08c681d3c7bde9e0c6cbc6b91fcc8),
(0x01a08c6d64477153809bae9e1972dd52, '2026-09-10 17:46:14.471011', '2026-09-10 17:46:14.470880', '2026-09-09 00:00:00.000000', 'заполнил заявку на сайте https://job-boards.eu.greenhouse.io/eduki/jobs/4954685101', NULL, 0x01a08c681d0f756e9412f65bf38ed555),
(0x01a091b3e4ba75729bc8c9c66657f8cf, '2026-09-11 18:21:20.954337', '2026-09-11 18:21:20.954294', '2026-09-11 00:00:00.000000', 'зп низкое', NULL, 0x01a091ade6b272e68fca24618cd53aa3),
(0x01a0922dc4937061a0e9c2aa621bd853, '2026-09-11 20:34:28.115011', '2026-09-11 20:34:28.114947', '2026-09-11 00:00:00.000000', 'Агентство\nhttps://persiahr.com/company/2024', 0x01a091ade6af7d34ba35982bb4a25fbb, NULL),
(0x01a0923af3037b4fac89eacf6945c9e5, '2026-09-11 20:48:51.971713', '2026-09-11 20:50:36.934542', '2026-09-10 00:00:00.000000', 'Dear Vitalii,\n\nThank you for applying for the Senior PHP Developer role at Altamira. \nWe appreciate the time and effort you put into your application. \nOur team has received your application and will carefully review it.\n\nRest assured that we will contact you within the next five working days \nto discuss the next steps in our recruitment process. \nIf you do not receive feedback within this timeframe, \nit may indicate that we have decided to proceed \nwith other candidates who better match our current requirements.\n\nWe encourage you to learn more about Altamira by exploring our website \nand social media channels to gain a better understanding of our company culture and values.\n\n Our hiring process typically consists of several stages, including:\n\n    1. Recruiting Interview\n    2. Technical Interview\n    3. Client Interview (optional)\n    4. Pre-Offer Call\n    5. Reference Check\n    6. Offer\n\nOnce again, thank you for considering Altamira as your potential employer. \nWe look forward to the possibility of working together\n and wish you the best of luck with your application.\n\nWarm regards,', NULL, 0x01a08c681d057cb993756ed2c1745f29),
(0x01a09246a3617da4b13cf0847f9c62f5, '2026-09-11 21:01:38.017864', '2026-09-11 21:01:38.017838', '2026-09-12 00:00:00.000000', 'Awating feedback at: September 15, 2026', NULL, 0x01a08c681d057cb993756ed2c1745f29);

-- --------------------------------------------------------

--
-- Table structure for table `sources`
--

CREATE TABLE `sources` (
  `id` binary(16) NOT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `grabber` varchar(255) NOT NULL DEFAULT 'dou',
  `name` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `active` tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sources`
--

INSERT INTO `sources` (`id`, `created_at`, `updated_at`, `grabber`, `name`, `url`, `active`) VALUES
(0x01a077e2815c7509b25e6fcea82c5d29, '2026-09-06 18:02:08.000000', '2026-09-06 18:02:08.000000', 'dou', 'DOU', 'https://jobs.dou.ua/vacancies/?remote&category=PHP', 1),
(0x01a08169a23b78d7b5288e259d626675, '2026-09-08 14:26:18.000000', '2026-09-08 15:17:03.674354', 'djinni', 'Djinni', 'https://djinni.co/jobs/?employment=remote&primary_keyword=Symfony', 1);

-- --------------------------------------------------------

--
-- Table structure for table `vacancies`
--

CREATE TABLE `vacancies` (
  `id` binary(16) NOT NULL,
  `created_at` datetime(6) DEFAULT NULL,
  `updated_at` datetime(6) DEFAULT NULL,
  `resume_sent_at` datetime(6) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` longtext,
  `external_id` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `salary` varchar(255) DEFAULT NULL,
  `source_id` binary(16) DEFAULT NULL,
  `company_id` binary(16) DEFAULT NULL,
  `viewed` tinyint NOT NULL DEFAULT '0',
  `status` varchar(255) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `vacancies`
--

INSERT INTO `vacancies` (`id`, `created_at`, `updated_at`, `resume_sent_at`, `title`, `description`, `external_id`, `url`, `salary`, `source_id`, `company_id`, `viewed`, `status`) VALUES
(0x01a077e29a587bbfaed7edcbe06bc4b1, '2026-09-06 18:02:14.488743', '2026-09-10 17:55:21.959181', NULL, 'Backend Engineer Core Team (Go + PHP) в BetterMe Київ, за кордоном, віддалено', 'About us: BetterMe — an all-in-one well-being ecosystem empowering millions to become better — physically, mentally, and emotionally. We build what makes people better and keep challenging ourselves to inspire others.', '332846', 'https://jobs.dou.ua/companies/betterme/vacancies/332846/?from=list_hot', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4c7373a404a49a020bd858, 1, 'Done'),
(0x01a077e29a59712299925ba582470418, '2026-09-06 18:02:14.489067', '2026-09-10 17:55:21.944137', NULL, 'Backend Team Lead (PHP) в StairwaySoft віддалено', 'We are looking for an experienced Backend Team Lead with a strong PHP background to take ownership of a high-load SMS AdTech project.', '371871', 'https://jobs.dou.ua/companies/stairwaysoft/vacancies/371871/?from=list_hot', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4c783fa404a49a0233e110, 1, 'Done'),
(0x01a077e29a5976ca99925ba5827e0550, '2026-09-06 18:02:14.489429', '2026-09-10 17:55:21.929940', NULL, 'Senior PHP Yii2 Developer в iPOST Київ, віддалено', 'Привіт! Ми — iPOST, українська продуктова IT-компанія та технологічна платформа доставки, яку розвиваємо з 2017 року.', '186031', 'https://jobs.dou.ua/companies/ipost/vacancies/186031/?from=list_hot', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4c7b5ba404a49a0281d900, 1, 'Done'),
(0x01a077e29a597a2699925ba5834c5203, '2026-09-06 18:02:14.489645', '2026-09-10 17:55:21.913451', NULL, 'Junior Full Stack Developer в CRMOZ віддалено', 'Ми шукаємо Junior Full Stack Developer для віддаленої роботи. Наша компанія спеціалізується на інтеграції Zoho CRM та інших продуктів Zoho Corp.', '372154', 'https://jobs.dou.ua/companies/crmoz/vacancies/372154/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4c7ce7a404a49a032c5cdb, 1, 'Done'),
(0x01a077e29a597d0a99925ba583a5bfe3, '2026-09-06 18:02:14.489830', '2026-09-10 17:55:21.898818', NULL, 'Backend Engineer в Weltrade за кордоном, віддалено', 'About Weltrade Weltrade is a leading international Fintech company with over two decades of stability and trust in the global FX and online trading industry.', '372120', 'https://jobs.dou.ua/companies/weltrade/vacancies/372120/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4c7deba404a49a0405e7b1, 1, 'Done'),
(0x01a077e29a5a72d896c2eba3cd46cf54, '2026-09-06 18:02:14.490178', '2026-09-10 17:55:21.883792', NULL, 'Fullstack Developer (Laravel + React) в Bitmotive віддалено', 'The stack, and why we are not strict about it Our largest engagement is a Laravel API with a Filament admin and a React Native client. Others run on Python and on TypeScript with React.', '371897', 'https://jobs.dou.ua/companies/bitmotive-llc/vacancies/371897/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4d7018b25b32da4e1e8a21, 1, 'Done'),
(0x01a077e29a5a76f896c2eba3cdb483e7, '2026-09-06 18:02:14.490442', '2026-09-10 17:55:21.867230', NULL, 'Senior Full-Stack Developer (PHP / Go / Vue.js) в Starlight Media $2000–2500 віддалено', 'Starlight Media — одна з найбільших медіагруп у Східній Європі, виробник та дистриб’ютор відеоконтенту, улюблених програм і серіалів мільйонів українців, якість та актуальність яких забезпечує нам лідерство.', '371884', 'https://jobs.dou.ua/companies/starlightmedia/vacancies/371884/', '$2000–2500', 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4d7104b25b32da4f01a9eb, 1, 'Done'),
(0x01a077e29a5a79e096c2eba3cea37f2d, '2026-09-06 18:02:14.490628', '2026-09-10 17:55:21.853829', NULL, 'Middle PHP Developer в ConveyThis $2000–3000 віддалено', 'Technologies: PHP, Laravel, JavaScript, SQL/NoSQL, GIT, and Bootstrap Minimum Requirements & Job Information: Experience: 3+ years Work Mode: 100% Remote (Work from anywhere) Location: Open to candidates worldwide Countries Considered: No restrictions on location...', '371816', 'https://jobs.dou.ua/companies/conveythis/vacancies/371816/', '$2000–3000', 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4d720cb25b32da4f528c0e, 1, 'Done'),
(0x01a077e29a5a7c9096c2eba3cf5d7275, '2026-09-06 18:02:14.490801', '2026-09-10 17:55:21.834847', NULL, 'fullstack engineer (php + angular) в meetFrankie Лісабон (Португалія), віддалено', 'about the company attekmi is a white-label adtech infrastructure provider for companies building or looking to expand and gain greater control over their programmatic monetization operations, including ad networks, ssp, and media trading businesses.', '367585', 'https://jobs.dou.ua/companies/meetfrankie/vacancies/367585/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4d72f4b25b32da4ffb0ed9, 1, 'Done'),
(0x01a077e29a5a7f3c96c2eba3cfac0e44, '2026-09-06 18:02:14.490971', '2026-09-10 17:55:21.819160', NULL, 'PHP Developer в Kraken Leads віддалено', 'Kraken Leads — динамічна компанія з українським корінням, що вже більш ніж 6 років займається просуванням iGaming продуктів, зосереджуючись на наданні комплексних SEO-рішень. Шукаємо PHP Developer до внутрішньої IT-команди.', '371680', 'https://jobs.dou.ua/companies/kraken-leads/vacancies/371680/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4d73d8b25b32da5050f092, 1, 'Done'),
(0x01a077e29a5b7234bf8368f71d86e1af, '2026-09-06 18:02:14.491138', '2026-09-10 17:55:21.801185', NULL, 'Junior WordPress Developer в Altum Software $800–1300 віддалено', 'About Us Altum is a Ukrainian IT software development house providing technical expertise to digital agencies and startups across the US and Western Europe. We focus on innovation, scalability, and quality to help businesses grow.', '371533', 'https://jobs.dou.ua/companies/altumsoftware/vacancies/371533/', '$800–1300', 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4d74bcb25b32da514bbdd4, 1, 'Done'),
(0x01a077e29a5b7680bf8368f71e24be7f, '2026-09-06 18:02:14.491410', '2026-09-10 17:55:21.786989', NULL, 'Full Stack Engineer (PHP + JavaScript + AI) в BetterMe Київ, за кордоном, віддалено', 'About us: BetterMe — an all-in-one well-being ecosystem empowering millions to become better — physically, mentally, and emotionally. We build what makes people better and keep challenging ourselves to inspire others.', '359478', 'https://jobs.dou.ua/companies/betterme/vacancies/359478/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4c7373a404a49a020bd858, 1, 'Done'),
(0x01a077e29a5c712184abb0d19975d41e, '2026-09-06 18:02:14.492057', '2026-09-10 17:55:21.771035', NULL, 'Full Stack Developer (PHP, Laravel, Vue.js) в Hurma System віддалено', 'Hurma System впевнено рухається вперед і продовжує розширювати команду! Зараз ми шукаємо ініціативного Full Stack Developer (PHP, Laravel, Vue.js), який готовий долучитися до нашої команди.', '311366', 'https://jobs.dou.ua/companies/hurma-system/vacancies/311366/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4d7760b25b32da53441311, 1, 'Done'),
(0x01a077e29a5c7e8184abb0d19a3b37b1, '2026-09-06 18:02:14.492914', '2026-09-10 17:55:21.755700', NULL, 'Middle+ PHP Developer (with DevOps) в SendPulse Чернігів, віддалено', 'SendPulse — українська платформа email-маркетингу та чат-ботів для бізнесу. Наразі ми шукаємо Middle+ PHP Developer з DevOps-експертизою у мультикоманду, яка розробляє CRM-систему та освітню платформу.', '371315', 'https://jobs.dou.ua/companies/sendpulse/vacancies/371315/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4d7838b25b32da53a9b9fc, 1, 'Done'),
(0x01a077e29a5e7ada837c2cf973c7fbac, '2026-09-06 18:02:14.494677', '2026-09-10 17:55:21.740830', '2026-09-07 00:00:00.000000', 'Senior PHP Backend Engineer в Jamworks віддалено', 'About the Role We are looking for an experienced Senior PHP Backend Engineer to join our backend team and help us build and evolve reliable, scalable, and maintainable systems.', '371158', 'https://jobs.dou.ua/companies/jamworks/vacancies/371158/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4d7ac8b25b32da55076d3d, 1, 'Done'),
(0x01a077e29a6274a7ba7ce435bfbc4f76, '2026-09-06 18:02:14.498287', '2026-09-10 17:55:21.723873', NULL, 'Senior Backend Engineer (PHP/Laravel) в Kultprosvet Дніпро, віддалено', 'Hey! We are Kultprosvet, a team of web and mobile software developers who care. Now we’re expanding and welcome talented Senior Backend Engineer (PHP/Laravel) to join our team.', '370921', 'https://jobs.dou.ua/companies/kultprosvet/vacancies/370921/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4d7ef8b25b32da560b1ea0, 1, 'Done'),
(0x01a077e29a627c67ba7ce435c08a1bf5, '2026-09-06 18:02:14.498785', '2026-09-10 17:55:21.711525', '2026-09-07 00:00:00.000000', 'Backend Developer (Payments) в Firetics віддалено', 'Шукаємо Backend Developer, який буде працювати над інтеграцією та розвитком платіжних рішень у iGaming-продукті. Обов’язки: Інтегрувати та підтримувати платіжні провайдери.', '361314', 'https://jobs.dou.ua/companies/firetics/vacancies/361314/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4e7069931cb571edc49fbc, 1, 'Done'),
(0x01a077e29a637451aa4c7c2e7c1af748, '2026-09-06 18:02:14.499268', '2026-09-10 17:55:21.695946', NULL, 'OpenCart/PHP Developer в Blue-Bird.Tech $680–800 віддалено', 'BlueBird Tech — українська інженерна компанія, що створює інноваційні військові рішення. Ми працюємо на перетині оборонних розробок, програмного забезпечення, штучного інтелекту та систем безпеки.', '370822', 'https://jobs.dou.ua/companies/blue-bird-tech/vacancies/370822/', '$680–800', 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4e7165931cb571edd8b93b, 1, 'Done'),
(0x01a077e29a637ba9aa4c7c2e7ced79f8, '2026-09-06 18:02:14.499738', '2026-09-10 17:55:21.679557', NULL, 'Strong Technical Lead (PHP), UA/EU, remote в Dedicatted за кордоном, віддалено', 'We are looking for a Technical Lead for our core development team, to own the technical direction of the platform and its application layer: making the architectural decisions, setting engineering standards, and holding the coherence of the system as it evolves.', '370729', 'https://jobs.dou.ua/companies/dedicatted/vacancies/370729/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4e727d931cb571ee61d37a, 1, 'Done'),
(0x01a077e29a647a9d9dfce86551ab2b9f, '2026-09-06 18:02:14.500671', '2026-09-10 17:55:21.649733', NULL, 'Head of Backend Engineering (PHP / FinTech / Payments) в Artkai за кордоном, віддалено', 'At Artkai, we help mid-market and enterprise businesses modernize software, automate business processes, and build AI-driven solutions grounded in robust security and corporate governance.', '370704', 'https://jobs.dou.ua/companies/artkai/vacancies/370704/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4e7489931cb571eee232b9, 1, 'Done'),
(0x01a077e29a65795c904c66c14eb0c00d, '2026-09-06 18:02:14.501591', '2026-09-10 17:55:21.641660', NULL, 'Full-Stack Software Engineer в Zade Space до $2500 за кордоном, віддалено', 'About Tone Singleton Tone Singleton is a technology company building software products across multiple domains, from CRM systems for healthcare clients to mobile learning applications for Mandarin Chinese learners.', '370361', 'https://jobs.dou.ua/companies/zade-agency/vacancies/370361/', 'до $2500', 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4e7651931cb571f033e72a, 1, 'Done'),
(0x01a077e29a6670a7a497b7f68ef3d33f, '2026-09-06 18:02:14.502034', '2026-09-10 17:55:21.632117', '2026-08-20 00:00:00.000000', 'Middle PHP developer в King Group Київ, віддалено', 'Ми ростемо! King Group за останній рік запустила 5 нових брендів у сфері iGaming і вже готується до запуску ще кількох. Саме тому ми шукаємо PHP Developer, який допоможе нам розвивати продукти та підсилить нашу core або payment команди.', '370356', 'https://jobs.dou.ua/companies/king-group/vacancies/370356/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4e7721931cb571f06499a8, 1, 'Done'),
(0x01a077e29a667e4fa497b7f6902242bd, '2026-09-06 18:02:14.502907', '2026-09-10 17:55:21.622993', NULL, 'Senior Laravel + Vue Engineer в IT Delight віддалено', 'We are looking for a Senior Laravel + Vue Engineer to join our team for a 6-month full-time project.', '370295', 'https://jobs.dou.ua/companies/it-delight/vacancies/370295/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4e78e1931cb571f092b6eb, 1, 'Done'),
(0x01a077e29a677634a3f0718277141772, '2026-09-06 18:02:14.503389', '2026-09-10 17:55:21.614190', NULL, 'Senior/Lead PHP Developer | Edlight в Boosta віддалено', 'Привіт! Ми, Boosta — холдингова ІТ-компанія, яка створює, розвиває та інвестує в digital-бізнеси з глобальним потенціалом. Сьогодні з нами співпрацюють понад 700 фахівців з різних куточків світу, а нашими продуктами та сервісами користуються мільйони юзерів.', '364684', 'https://jobs.dou.ua/companies/boosta/vacancies/364684/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4e79c9931cb571f111ba03, 1, 'Done'),
(0x01a077e29a677d20a3f0718277f46955, '2026-09-06 18:02:14.503832', '2026-09-10 17:55:21.605257', NULL, 'Magento Developer в Covent IT віддалено', 'About the project: Type: E-commerce International platform, which provides tools for setting up an online store: A fully customized browsing experience; optimization of international checkout processes; handling all logistical aspects of cross-border selling; marketing...', '370187', 'https://jobs.dou.ua/companies/covent-it/vacancies/370187/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4e7aa5931cb571f1fe450c, 1, 'Done'),
(0x01a077e29a6874cb89a3e857db5a07dd, '2026-09-06 18:02:14.504298', '2026-09-10 17:55:21.593649', NULL, 'Middle+/Senior Shopware Developer (Part-Time Contract, US Project) в IT Service Ukraine віддалено', 'We are looking for a Middle+/Senior Full-stack Shopware developer to join our team on a part-time contract basis. Required Skills: 3+ years of commercial experience with Shopware 6. Strong knowledge of PHP 8, Symfony, and MySQL/MariaDB.', '347146', 'https://jobs.dou.ua/companies/speroteck/vacancies/347146/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4e7b89931cb571f20fe68e, 1, 'Done'),
(0x01a077e29a687bc789a3e857dbc72a1f, '2026-09-06 18:02:14.504746', '2026-09-10 17:55:21.582916', NULL, 'Middle Laravel/ JS Developer (Contract, 2–3 months) в BRAZY $2500–2700 віддалено', 'Brazy is looking for a Middle PHP/Laravel Developer to join our Affiliate project on a short-term contract basis (2–3 months). We are a team of daring, innovative professionals. We are Cyberpunks, Cyber Jedi, and Pattern Breakers.', '370104', 'https://jobs.dou.ua/companies/brazy/vacancies/370104/', '$2500–2700', 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4e7c6d931cb571f2be8ff8, 1, 'Done'),
(0x01a077e29a69785092f7c93fedfadec2, '2026-09-06 18:02:14.505527', '2026-09-10 17:55:21.573360', NULL, 'Senior PHP Fullstack Developer в RemoDevs $5500–5850 віддалено', 'We are now a payment software company. We changed our focus from subscription billing in October 2019. Our product has many features that help our clients move customers from the order page to the final payment page, and also help them pay renewals on time.', '369996', 'https://jobs.dou.ua/companies/remodevs/vacancies/369996/', '$5500–5850', 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4e7e25931cb571f3e392cd, 1, 'Done'),
(0x01a077e29a697bb492f7c93feeacb952, '2026-09-06 18:02:14.505745', '2026-09-10 17:55:21.561166', NULL, 'Magento Backend Developer в Glorium Technologies віддалено', 'We are modernizing the core e-commerce architecture for an established international aerospace parts distributor.', '369974', 'https://jobs.dou.ua/companies/glorium-technologies-corp/vacancies/369974/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4e7efd931cb571f4b11f75, 1, 'Done'),
(0x01a077e29a697e9092f7c93fef07ece4, '2026-09-06 18:02:14.505928', '2026-09-10 17:55:21.549912', '2026-09-07 00:00:00.000000', 'PHP Fullstack розробник в Stream-market $2000–2200 віддалено', 'Віддалена, постійна робота full-time! Продуктова компанія e-commerce (не аутсорс) Шукаємо сильного PHP Fullstack розробника рівня Middle+/Senior або потенційного Tech Lead, який готовий брати участь у прийнятті технічних рішень, впливати на архітектуру продукту.', '358896', 'https://jobs.dou.ua/companies/stream-market/vacancies/358896/', '$2000–2200', 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4f7031b7d94ba2699a9130, 1, 'Done'),
(0x01a077e29a6a719c894644c61e867839, '2026-09-06 18:02:14.506099', '2026-09-10 17:55:21.540578', NULL, 'Senior PHP Developer (Backend-focused, React.js) в iPay Київ, віддалено', 'iPay.ua — a nationwide online payment and transfer service in Ukraine that allows users to pay for goods and services at over 2,000 merchants using payment cards.We are one of the leaders in the online payments industry.', '351948', 'https://jobs.dou.ua/companies/ipay/vacancies/351948/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4f7105b7d94ba26a7bb443, 1, 'Done'),
(0x01a077e29a6a7440894644c61f105761, '2026-09-06 18:02:14.506269', '2026-09-10 17:55:21.530787', NULL, 'PHP Full-Stack Developer в Cikumas GmbH віддалено', 'About the role ClassOwl is a school-management system used by educational institutions. We are hiring a Middle+/Senior PHP Full-Stack Developer to join the team and work on our existing product. The application is built on a custom PHP framework.', '366568', 'https://jobs.dou.ua/companies/cikumas-gmbh/vacancies/366568/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4f71e1b7d94ba26a7c11f2, 1, 'Done'),
(0x01a077e29a6a76cc894644c61fffe056, '2026-09-06 18:02:14.506432', '2026-09-10 17:55:21.518656', NULL, 'PHP Developer (Billing) в Brainstack Київ, віддалено', 'Привіт, ми — Brainstack, українська мультипродуктова IT-компанія. Якщо ти шукаєш простір можливостей для постійного масштабування і любиш задачі із зірочкою — тоді тобі до нас.', '369880', 'https://jobs.dou.ua/companies/brainstack/vacancies/369880/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4f72d5b7d94ba26aa5b46d, 1, 'Done'),
(0x01a077e29a6a792c894644c620fa86da, '2026-09-06 18:02:14.506584', '2026-09-10 17:55:21.509273', NULL, 'Middle Strong/Senior PHP Developer (Laravel) в ПриватБанк Київ, віддалено', 'ПриватБанк— є найбільшим банком України та одним з найбільш інноваційних банків світу. Займає лідуючі позиції за всіма фінансовими показниками в галузі та складає близько чверті всієї банківської системи країни.', '369853', 'https://jobs.dou.ua/companies/privatbank/vacancies/369853/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4f7759b7d94ba26b0ddbd7, 1, 'Done'),
(0x01a077e29a6a7bb0894644c621a5480d, '2026-09-06 18:02:14.506745', '2026-09-10 17:55:21.500087', NULL, 'Middle + to Senior PHP / Craft CMS Developer в SFox $2300–4000 віддалено', 'About the Client Our client is a Norwegian digital agency working with high-end customers, including clients in the Norwegian public sector. The company positions itself as a high-quality technology partner, and its customers expect a high standard of technical delivery.', '369748', 'https://jobs.dou.ua/companies/sfox/vacancies/369748/', '$2300–4000', 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4f7a19b7d94ba26be9bc28, 1, 'Done'),
(0x01a077e29a6a7e38894644c6229857fc, '2026-09-06 18:02:14.506906', '2026-09-10 17:55:21.489284', NULL, 'Senior PHP Developer в FAVBET Київ, за кордоном, віддалено', 'FAVBET is an Entertainment Tech company that has been creating innovative solutions in betting and gaming for over 25 years. We don’t just develop products — we shape the culture of responsible gaming and support sports development.', '369737', 'https://jobs.dou.ua/companies/favbet/vacancies/369737/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4f7cddb7d94ba26c6c95b2, 1, 'Done'),
(0x01a077e29a6b7114b48cbe786c4ccc9f, '2026-09-06 18:02:14.507066', '2026-09-10 17:55:21.479886', NULL, 'Intermediate Back-End Developer в eduki віддалено', 'About Us At eduki, we believe that supporting teachers leads to the best possible lessons, the best possible schools, and the best possible education. That’s why we built a platform where teachers can find and share high quality, ready-to-use teaching materials.', '369724', 'https://jobs.dou.ua/companies/lehrermarktplatz/vacancies/369724/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4f7f71b7d94ba26d447e7d, 1, 'Done'),
(0x01a077e29a6c796aa73589b087d85031, '2026-09-06 18:02:14.508598', '2026-09-10 17:55:21.471711', NULL, 'Strong Junior PHP developer в Хорошоп $1000–1600 віддалено', 'Хорошоп — продуктова IT-компанія, найбільша SaaS-платформа для створення інтернет-магазинів в Україні. Нам довіряють понад 13 000 інтернет-магазинів і щодня ця цифра зростає. Ми активно розвиваємо продукт, виходимо на нові ринки та вдосконалюємо наші технології.', '311905', 'https://jobs.dou.ua/companies/horoshop/vacancies/311905/', '$1000–1600', 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a50769e83b70c8555b0d621, 1, 'Done'),
(0x01a077e29a6c7c1ea73589b0887e97d7, '2026-09-06 18:02:14.508771', '2026-09-10 17:55:21.463431', NULL, 'Architect/Senior BigCommerce Developer в Aimprosoft за кордоном, віддалено', 'AIMPROSOFT — Architect/Senior BigCommerce Developer opportunity! Aimprosoft, a fast-growing IT company, is looking for a Senior BigCommerce Developer to join a long-term international project in the B2B E-commerce domain.', '369547', 'https://jobs.dou.ua/companies/aimprosoft/vacancies/369547/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a50778a83b70c8555f70061, 1, 'Done'),
(0x01a077e29a6d74318e2049e9f31db6e0, '2026-09-06 18:02:14.509265', '2026-09-10 17:55:21.454509', NULL, 'Full Stack WordPress Developer (project-based) в onPoint Studio віддалено', 'We’re expanding our team And we’re excited to announce a new opening — Full Stack WordPress Developer to collaborate with on a project basis at onPoint Studio!', '327277', 'https://jobs.dou.ua/companies/onpoint-studio/vacancies/327277/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a507a6283b70c8557b3064d, 1, 'Done'),
(0x01a077e29a6d76898e2049e9f41031d2, '2026-09-06 18:02:14.509415', '2026-09-10 17:55:21.444387', NULL, 'PHP Fullstack Developer (Vue.js) в bip.net.ua $1500–1700 віддалено', 'Ми — продуктова компанія, яка розробляє систему Електронний Кабінет Бухгалтера (ЕКБ) — SaaS-рішення для автоматизації роботи бухгалтерів, яке виходить на зовнішній ринок у 2026 році.', '369394', 'https://jobs.dou.ua/companies/bip-buhgalter-idealnogo-pidpriyemtsya/vacancies/369394/', '$1500–1700', 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a507b4a83b70c8557f20164, 1, 'Done'),
(0x01a077e29a6d78fd8e2049e9f5042050, '2026-09-06 18:02:14.509572', '2026-09-10 17:55:21.436500', NULL, 'Lead PHP Developer (EU) в Qubit Labs віддалено', 'The company is a leading Digital-out-of-Home provider in Germany. It operates a growing network of digital screens at high-frequency retail locations and develops its own technology platform for campaign management, screen operations, monitoring and advertising delivery.', '369345', 'https://jobs.dou.ua/companies/qubit-labs/vacancies/369345/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a507c2683b70c8558a71e0e, 1, 'Done'),
(0x01a077e29a6d7b8d8e2049e9f54b451b, '2026-09-06 18:02:14.509735', '2026-09-10 17:55:21.426589', '2026-09-07 00:00:00.000000', 'Middle/Senior PHP Developer в V-TER Solutions віддалено', 'У зв’язку з розширенням команди шукаємо Middle/Senior PHP Developer на проєкт, пов’язаний із міжнародною логістикою та доставкою. Про проєкт: Основне завдання продукту — забезпечення доставки товарів для бізнесу. Проєкт працює вже не перший рік і продовжує активно розвиватися.', '369334', 'https://jobs.dou.ua/companies/v-ter-solutions/vacancies/369334/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a507d1a83b70c85596c0779, 1, 'Done'),
(0x01a077e29a6e70bc9899c821c6c9ce26, '2026-09-06 18:02:14.510044', '2026-09-10 17:55:21.416998', '2026-08-21 00:00:00.000000', 'PHP Developer (Symfony) в Checkbox Group $2000–3000 віддалено', 'Привіт! Checkbox — українська продуктова фінтех-компанія. Щомісяця ми фіскалізуємо понад 100 мільйонів чеків для 100+ тисяч підприємців і швидко зростаємо.', '369320', 'https://jobs.dou.ua/companies/checkbox/vacancies/369320/', '$2000–3000', 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a507ed283b70c855afcba1e, 1, 'Done'),
(0x01a077e29a6e73609899c821c6f719a8, '2026-09-06 18:02:14.510212', '2026-09-10 17:55:21.408947', NULL, 'Senior PHP / Laravel Developer в PioGroup Software віддалено', 'PioGroup Software is a team of professionals passionate about building reliable digital products. We are looking for an experienced PHP / Laravel Developer to join a long-term client project for a fixed 6-month full-time contract.', '369310', 'https://jobs.dou.ua/companies/piogroup-software/vacancies/369310/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a51700eaf36aaaa60b53bfe, 1, 'Done'),
(0x01a077e29a6e78649899c821c81dc6c4, '2026-09-06 18:02:14.510534', '2026-09-10 17:55:21.399788', NULL, 'Middle-to-Senior PHP-розробник онлайн та мобільних ігор (back-end, hi-load) в Candy Grill Дніпро, віддалено', 'Запрошуємо до нашої команди php backend розробника з досвідом.', '223159', 'https://jobs.dou.ua/companies/candygrill/vacancies/223159/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a51720eaf36aaaa6192991c, 1, 'Done'),
(0x01a077e29a6e7bb09899c821c887db86, '2026-09-06 18:02:14.510744', '2026-09-10 17:55:21.389981', NULL, 'Senior Full-Stack Developer PHP (Laravel + Vue.js) в Digital Hiring $3200–3500 віддалено', 'Remote | Senior | PHP + Laravel + Vue.js Шукаємо Senior Full-Stack Developer для нашого клієнта — великого українського eCommerce-ритейлера з 20+ роками на ринку та десятками представлених світових брендів.', '369263', 'https://jobs.dou.ua/companies/digital-hiring/vacancies/369263/', '$3200–3500', 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a5172faaf36aaaa61c9f0c8, 1, 'Done'),
(0x01a077e29a6e7e809899c821c91498ee, '2026-09-06 18:02:14.510925', '2026-09-10 17:55:21.380091', NULL, 'WordPress Developer 2+ years в Flexi IT $1000–1200 віддалено', 'Flexi IT — бутікова IT-компанія із Запоріжжя. З 2014 року ми розробляємо вебсайти, вебдодатки та digital-рішення для бізнесу.', '315929', 'https://jobs.dou.ua/companies/flexi/vacancies/315929/', '$1000–1200', 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a5173deaf36aaaa62121c4d, 1, 'Done'),
(0x01a077e29a6f7185bdb98069f35ee2f0, '2026-09-06 18:02:14.511092', '2026-09-10 17:55:21.370217', NULL, 'Senior Golang/PHP Developer в 8allocate віддалено', 'Client/Project: The client provides software for fitness and wellness businesses, such as gyms, yoga and Pilates studios, martial arts centers, and personal trainers. Their SaaS platform helps businesses manage operations and enables customers to book services.', '369182', 'https://jobs.dou.ua/companies/8allocate/vacancies/369182/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a5174c6af36aaaa62f2ae10, 1, 'Done'),
(0x01a077e29a6f7425bdb98069f44a9a5f, '2026-09-06 18:02:14.511262', '2026-09-10 17:55:21.361473', NULL, 'PHP Developer (Support) в INFOBUS віддалено', 'INFOBUS Holding is one of the leading European platforms for searching and purchasing bus, train, and flight tickets. We connect transport companies and travelers, creating convenient solutions for planning and booking trips.', '364976', 'https://jobs.dou.ua/companies/infobus/vacancies/364976/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a5175aaaf36aaaa638b738f, 1, 'Done'),
(0x01a077e29a6f76b1bdb98069f4cc5a66, '2026-09-06 18:02:14.511424', '2026-09-10 17:55:21.352820', NULL, 'FullStack Developer (PHP Laravel + React) for Automation and AI в Softprom $2000–2300 віддалено', 'We are looking for An experienced FullStack Developer who can handle both backend and frontend development for complex business automation and AI projects.', '318300', 'https://jobs.dou.ua/companies/softprom/vacancies/318300/', '$2000–2300', 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a517696af36aaaa63ea76ec, 1, 'Done'),
(0x01a077e29a6f7e81bdb98069f53bb834, '2026-09-06 18:02:14.511919', '2026-09-10 17:55:21.342846', NULL, 'Senior Software Engineer (Back End / Full Stack) в Honeytech Київ, за кордоном, віддалено', 'Привіт! Ми — Honeytech, українська продуктова компанія, яка створює digital entertainment продукти та розвиває власні платформи у сфері вебкоміксів для глобальної аудиторії. Наші результати: № 1 у своїй ніші на ринках Tier-1. 20M+ юзерів користуються нашим продуктом.', '355101', 'https://jobs.dou.ua/companies/honeytech/vacancies/355101/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a51777aaf36aaaa63fc4a5a, 1, 'Done'),
(0x01a077e29a707778aee338e54dd5d45f, '2026-09-06 18:02:14.512464', '2026-09-10 17:55:21.332297', NULL, 'Fullstack Engineer (PHP+Angular) в Сільпо віддалено', 'Привіт! Ми — E-Com — команда закоханих у FoodTech та український продукт. І ми шукаємо Spec-driven Fullstack Engineer — Symfony + Angular, який або яка разом з нами створюватиме прогресивні продукти, якими користуються тисячі українців. Не виключаємо, що і твоя сім’я.', '368959', 'https://jobs.dou.ua/companies/silpo/vacancies/368959/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a51785eaf36aaaa646416d6, 1, 'Done'),
(0x01a077e29a717738ab0ddb2ce8469222, '2026-09-06 18:02:14.513454', '2026-09-10 17:55:21.322875', NULL, 'PHP Tech Lead в Alcor віддалено', 'Alcor is global engineering infrastructure for technology companies building international engineering organizations as true in-house teams — not outsourcing.', '368864', 'https://jobs.dou.ua/companies/alcor/vacancies/368864/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a517a1eaf36aaaa65242519, 1, 'Done'),
(0x01a077e29a717ed4ab0ddb2ce874dcb9, '2026-09-06 18:02:14.513941', '2026-09-10 17:55:21.312112', '2026-08-14 00:00:00.000000', 'Senior PHP Engineer в airSlate Київ, віддалено', 'About airSlate airSlate is a global SaaS technology company that develops no-code workflow automation, electronic signature, and document management solutions.\n\nVacancy external: \nhttps://jobs.lever.co/airslate/0e0e2b0d-1f7c-4d78-aca5-594b60e731a8/thanks', '368837', 'https://jobs.dou.ua/companies/airslate/vacancies/368837/', '$3000-$3.500', 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a517b0eaf36aaaa6548f82c, 1, 'Done'),
(0x01a077efdce37999a49f4b75ab4f84e0, '2026-09-06 18:16:43.491606', '2026-09-10 17:55:21.301479', '2025-10-20 00:00:00.000000', 'Senior PHP developer', '(вакансія неактивна)', '323581', 'https://jobs.dou.ua/companies/mojam/vacancies/323581/', '$4500–5500', 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e6717e760683f7851aba00106b, 1, 'Done'),
(0x01a077f90ab0708b855324c7f2d12ad0, '2026-09-06 18:26:45.040024', '2026-09-10 17:55:21.290687', '2026-05-18 00:00:00.000000', 'Senior PHP Developer with DDD', NULL, NULL, 'https://job-boards.eu.greenhouse.io/whitetech/jobs/4523693101', '4500~5000~5500 EUR', NULL, 0x01a077f79c127074ab26a317a794f87e, 1, 'Done'),
(0x01a07809bfef727dba772ac2a92f6cb4, '2026-09-06 18:45:00.015150', '2026-09-10 17:55:21.281550', '2026-06-12 00:00:00.000000', 'Senior PHP / JS Developer in Hosting Development Team (#1150)', '(вакансія неактивна)\nsallary: 3000-3500\nprobation period: 3000\nthen: 3500', '354736', 'https://jobs.dou.ua/companies/zone3000/vacancies/354736/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a07808bfda712cbb4e6f6d236ed7d6, 1, 'Done'),
(0x01a0781075b8715e98c75dd1bc7cd336, '2026-09-06 18:52:19.768077', '2026-09-10 17:55:21.272169', '2026-06-23 00:00:00.000000', 'PHP Developer', 'sallary: 3000 USD\nMy Questions: https://bitbucket.org/vitalii-sotnykov/resume-2025/src/39aa9673d15d17cb9f4e1c16309d2169b8d3f853/questions-Aleksey-leguidenoir.com.md', NULL, NULL, NULL, NULL, 0x01a0780f814471ffafa3e13d108feb61, 1, 'Done'),
(0x01a07820672f7cf69cebd2173769d68f, '2026-09-06 19:09:44.623818', '2026-09-10 17:55:21.263951', '2026-06-25 00:00:00.000000', 'PHP Developer', '(вакансія неактивна)', NULL, 'https://jobs.dou.ua/companies/mobilunity/vacancies/353455/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a0781f794378c6bd22194e3c112c3c, 1, 'Done'),
(0x01a07822ea057970b60f7ad44129c883, '2026-09-06 19:12:29.189594', '2026-09-10 17:55:21.253523', '2026-07-21 00:00:00.000000', 'PHP Developer (Symfony)(closed)', NULL, NULL, 'https://mobilunity.com/vacancy/backend-developer-2/', NULL, NULL, 0x01a0781f794378c6bd22194e3c112c3c, 1, 'Done'),
(0x01a07824ab0e750cab32f7609961c4be, '2026-09-06 19:14:24.142314', '2026-09-10 17:55:21.243904', '2026-06-25 00:00:00.000000', 'Senior PHP Developer', '(вакансія неактивна)', '361062', 'https://jobs.dou.ua/companies/truelabel/vacancies/361062/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a07824039671708959bb82761a1656, 1, 'Done'),
(0x01a078285fc7724fa6bb27d7dcb6ac10, '2026-09-06 19:18:27.015137', '2026-09-10 17:55:21.235716', '2026-06-25 00:00:00.000000', 'Senior PHP Full-Stack Developer', '(вакансія неактивна)', '354200', 'https://jobs.dou.ua/companies/letrium-ltd-espocrm/vacancies/354200/', '$4000–4500', 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a07827022579c1928928071aaa4ee3, 1, 'Done'),
(0x01a0782a8d6679dd8fa1a95721b1be4f, '2026-09-06 19:20:49.766620', '2026-09-10 17:55:21.227920', '2026-07-13 00:00:00.000000', 'Senior PHP Developer', NULL, NULL, 'https://mojam.co/careers/php-developer/', NULL, NULL, 0x01a077e6717e760683f7851aba00106b, 1, 'Done'),
(0x01a0782d7a1d7c5688f73e44d40d69c4, '2026-09-06 19:24:01.437780', '2026-09-10 17:55:21.218842', '2026-07-22 00:00:00.000000', 'Middle PHP Developer', '(вакансія неактивна)', '357905', 'https://jobs.dou.ua/companies/applyft/vacancies/357905/', '3000 USD', 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a0782ca88979d1a0a73089e07255df, 1, 'Done'),
(0x01a0782feec373ff914091460c96402c, '2026-09-06 19:26:42.371244', '2026-09-10 17:55:21.208666', '2026-07-22 00:00:00.000000', 'PHP Developer', 'UPSTARS', NULL, 'https://www.linkedin.com/jobs/view/4426639833/?trk=eml-email_jobs_viewed_job_reminder_01-job_card-0-jobcard_body_4426639833&refId=U8xSE51mRqOFmyMToDXE6A%3D%3D&trackingId=rWdoPkrWQzOtzZeZzizudw%3D%3D', '3000 USD', NULL, 0x01a0782ede6374aebb86de3782637bdd, 1, 'Done'),
(0x01a0783190037adeb3bd97463735ab08, '2026-09-06 19:28:29.187686', '2026-09-10 17:55:21.200287', '2026-07-23 00:00:00.000000', 'Senior Software Engineer (Back End / Full Stack)', NULL, '355101', 'https://jobs.dou.ua/companies/honeytech/vacancies/355101/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a51777aaf36aaaa63fc4a5a, 1, 'Done'),
(0x01a0783c13087c2ca9b84091b7de0d13, '2026-09-06 19:39:58.088769', '2026-09-10 17:55:21.191730', '2026-07-23 00:00:00.000000', 'Senior Full-stack Developer', '(вакансія неактивна)', '363545', 'https://jobs.dou.ua/companies/sharpminds/vacancies/363545/', '$4000–5000', 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a0783b4ba57ac9926e4ccb788b144c, 1, 'Done'),
(0x01a0783d8b2579f190dfa913dcc315ed, '2026-09-06 19:41:34.373626', '2026-09-10 17:55:21.179337', '2026-07-30 00:00:00.000000', 'Senior PHP Engineer', '(вакансія неактивна)\nпо полученным письмам похоже на рекрутинговое агентсво\nhttps://career.avenga.com/jobs/8135331-senior-php-engineer\nhttps://jobs.dou.ua/companies/avenga/vacancies/362662/\nhttps://www.linkedin.com/jobs/view/4446313621/', '362662', 'https://jobs.dou.ua/companies/avenga/vacancies/362662/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a0783cac3c78c69368bf5b457a2ff5, 1, 'Done'),
(0x01a07840bf5b7a56a3ecd3ff52b2c0ef, '2026-09-06 19:45:04.347651', '2026-09-10 17:55:21.169765', '2026-07-30 00:00:00.000000', 'PHP Developer (only Europe or Georgia)', '(вакансія неактивна)', '364735', 'https://jobs.dou.ua/companies/qubit-labs/vacancies/364735/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a507c2683b70c8558a71e0e, 1, 'Done'),
(0x01a078429cb57de8803125097df76672, '2026-09-06 19:47:06.549881', '2026-09-10 17:55:21.160482', '2026-07-30 00:00:00.000000', 'Middle/Senior PHP Developer (вакансія неактивна)', NULL, '365937', 'https://jobs.dou.ua/companies/opinion-corp/vacancies/365937/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a07841ef8174eda1e12b599d8704f0, 1, 'Done'),
(0x01a0784482cc74f4b736e4e29b12d5dd, '2026-09-06 19:49:10.988307', '2026-09-10 17:55:21.149965', '2026-07-30 00:00:00.000000', 'Senior PHP Developer', '(вакансія неактивна)', '365956', 'https://jobs.dou.ua/companies/talmatic/vacancies/365956/', '$4000–5000', 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a078437e787b589f8ef781982e286b, 1, 'Done'),
(0x01a07845e87a7759a6c65944c2003ad9, '2026-09-06 19:50:42.554461', '2026-09-10 17:55:21.140544', '2026-07-30 00:00:00.000000', 'Senior Full-stack PHP (Yii2) / Vue 3 Developer', '(вакансія неактивна)', '345821', 'https://jobs.dou.ua/companies/improveit-solutions-ukraine/vacancies/345821/', '$3000–5000', 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a07844dd99720eaa38ea7657a508f2, 1, 'Done'),
(0x01a07847719d789096390918e50e7a43, '2026-09-06 19:52:23.197539', '2026-09-10 17:55:21.128903', '2026-07-30 00:00:00.000000', 'Junior Strong/Middle PHP developer', '(вакансія неактивна)\nhttps://jobs.dou.ua/companies/pulsepaw/vacancies/367930/\nhttps://pulsepaw.peopleforce.io/careers/v/228195-junior-strongmiddle-php-developer\nMy Feedback:2025-08-04: https://bitbucket.org/vitalii-sotnykov/resume-2025/src/39aa9673d15d17cb9f4e1c16309d2169b8d3f853/mails/PulsePaw-2025-08-04.md', '367930', 'https://jobs.dou.ua/companies/pulsepaw/vacancies/367930/', '$3,500 gross (до сплати податків)', 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a078467a397041a16d87628ad8390a, 1, 'Done'),
(0x01a0784ba9cd71908c16db37b311791e, '2026-09-06 19:56:59.725084', '2026-09-10 17:55:21.119289', '2026-08-14 00:00:00.000000', 'PHP Developer', NULL, '365761', 'https://jobs.dou.ua/companies/agiliway/vacancies/365761/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a0784b372b75f5b2e5cadb164760c8, 1, 'Done'),
(0x01a078561b0a77839261a09356475460, '2026-09-06 20:08:24.074471', '2026-09-10 17:55:21.109465', '2026-08-21 00:00:00.000000', 'Strong Junior/ Middle PHP Developer (Symfony)  Source: https://corefy.com/careers/junior-php-developer © Corefy.com', NULL, NULL, 'https://corefy.com/careers/junior-php-developer', NULL, NULL, 0x01a0785562577879ab3325440e82afff, 1, 'Done'),
(0x01a078600efd7f3b9fa7cb0a53037a21, '2026-09-06 20:19:16.349965', '2026-09-10 17:55:21.099366', '2026-08-14 00:00:00.000000', 'Middle PHP developer (core team) (вакансія неактивна)', '(вакансія неактивна)', '362960', 'https://jobs.dou.ua/companies/king-group/vacancies/362960/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4e7721931cb571f06499a8, 1, 'Done'),
(0x01a0789598f378c3b8d67d2e4a71066f, '2026-09-06 21:17:45.075554', '2026-09-10 17:55:21.087432', NULL, 'PHP Team Lead в Stape за кордоном, віддалено', 'Location: EU-based Cooperation type: Full-time 🧭 Work format: Remote We’re looking for a skilled PHP Team Lead who’s passionate about building reliable backend systems, enjoys working on high-load, scalable architectures, and is ready to lead a team of backend engineers...', '371904', 'https://jobs.dou.ua/companies/stape-inc/vacancies/371904/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a0789598f27d51b6d040e3d0adcef6, 1, 'Done'),
(0x01a0789598f77aa286d2b6262976d37e, '2026-09-06 21:17:45.079674', '2026-09-10 17:55:21.079455', NULL, 'Back-End Developer (Laravel+Shopify) в Dinarys віддалено', 'Dinarys is an ecommerce development company helping retailers and brands across DACH, the UK, and the US build, scale, and optimize their digital commerce solutions.', '364746', 'https://jobs.dou.ua/companies/dinarys/vacancies/364746/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a0789598f772a686d2b62628a3c690, 1, 'Done'),
(0x01a0789598f978a2b6d5991be0b1d711, '2026-09-06 21:17:45.081547', '2026-09-10 17:55:21.068804', NULL, 'Fullstack Laravel/TypeScript Engineer в Mobilunity Київ, за кордоном, віддалено', 'On behalf of our Client from France, Mobilunity is looking for a Fullstack Engineer. Our client is a table management software and CRM that enables restaurant owners to welcome their customers easily. The app is useful to manage booking requests and register new bookings.', '371170', 'https://jobs.dou.ua/companies/mobilunity/vacancies/371170/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a0781f794378c6bd22194e3c112c3c, 1, 'Done'),
(0x01a0789598fb731eabe632feb87090df, '2026-09-06 21:17:45.083194', '2026-09-10 17:55:21.057330', NULL, 'PHP Developer (Mobile App Nova Post) в Nova Digital віддалено', 'Nova Digital — це технологічне серце екосистеми NOVA, де твій код стає частиною щоденного життя цілої країни.', '371133', 'https://jobs.dou.ua/companies/nova-digital/vacancies/371133/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a0789598fa7adcaf99040b719cbe99, 1, 'Done'),
(0x01a0789598fc714b9128c7e07cdacce6, '2026-09-06 21:17:45.084078', '2026-09-10 17:55:21.047953', NULL, 'Middle PHP Backend Developer (AI Oriented) в Frozeneon $1500–2000 віддалено', 'Обов’язки Розробляти й підтримувати фічі у високонавантаженому моноліті, мікросервісах та внутрішніх пакетах (shared-бібліотека, SDK) Інтегрувати API та платіжні системи, працювати з вебхуками та постбеками Оптимізувати MySQL-запити, кешування через Redis та фонові задачі...', '371043', 'https://jobs.dou.ua/companies/frozeneon/vacancies/371043/', '$1500–2000', 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a0789598fb79d2abe632feb877e652, 1, 'Done'),
(0x01a0789598ff776f9493eb8361fd44ba, '2026-09-06 21:17:45.087471', '2026-09-10 17:55:21.038162', NULL, 'Middle/Senior PHP Developer (Laravel) в DNIPRO-M Київ, віддалено', 'Привіт, наш майбутній колего Ми віримо, що сильні результати створюють люди, які добре знають свою справу і ставляться до неї відповідально. У Dnipro-M ми будуємо команду майстрів своєї справи — людей, які хочуть не просто працювати, а впливати й бачити результат.', '370711', 'https://jobs.dou.ua/companies/dnipro-m/vacancies/370711/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a0789598fe7f1ba7bd9fd77bef8aa8, 1, 'Done'),
(0x01a078959901706fa7e76aed9d6083df, '2026-09-06 21:17:45.089023', '2026-09-10 17:55:21.028788', NULL, 'Middle/Senior PHP Developer (Symfony) в DNIPRO-M Київ, віддалено', 'Привіт, наш майбутній колего Ми віримо, що сильні результати створюють люди, які добре знають свою справу і ставляться до неї відповідально. У Dnipro-M ми будуємо команду майстрів своєї справи — людей, які хочуть не просто працювати, а впливати й бачити результат.', '370601', 'https://jobs.dou.ua/companies/dnipro-m/vacancies/370601/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a0789598fe7f1ba7bd9fd77bef8aa8, 1, 'Done'),
(0x01a0789599037613a50c282b07deccbf, '2026-09-06 21:17:45.091384', '2026-09-10 17:55:21.020010', NULL, 'Laravel Engineer в Mobilunity віддалено', 'On behalf of our Client from France, Mobilunity is looking for a Laravel Engineer. Our client is a table management software and CRM that enables restaurant owners to welcome their customers easily. The app is useful to manage booking requests and register new bookings.', '365018', 'https://jobs.dou.ua/companies/mobilunity/vacancies/365018/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a0781f794378c6bd22194e3c112c3c, 1, 'Done'),
(0x01a0789599087357b1d97f07b8c68c7e, '2026-09-06 21:17:45.096207', '2026-09-10 17:55:21.008423', NULL, 'Lead Backend PHP Developer (R&D) в Nova Digital віддалено', 'Nova Digital — це технологічне серце екосистеми NOVA, де твій код стає частиною щоденного життя цілої країни. Про масштаб наших сервісів: 50+ млн запитів щодня проходять через наші системи. 10+ млн активних користувачів покладаються на наші рішення.', '366227', 'https://jobs.dou.ua/companies/nova-digital/vacancies/366227/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a0789598fa7adcaf99040b719cbe99, 1, 'Done'),
(0x01a07895991279cfb071ae54b52c8ee0, '2026-09-06 21:17:45.106622', '2026-09-10 17:55:20.999319', NULL, 'Middle Full-stack Developer в Nova Digital віддалено', 'Не просто код, а твій вплив на мільйони українців Nova Digital — це технологічне серце екосистеми NOVA, де твій код стає частиною щоденного життя цілої країни.', '369679', 'https://jobs.dou.ua/companies/nova-digital/vacancies/369679/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a0789598fa7adcaf99040b719cbe99, 1, 'Done'),
(0x01a0789599137dd7a0458803edbe46f3, '2026-09-06 21:17:45.107881', '2026-09-10 17:55:20.989135', NULL, 'Senior Full-stack Developer в Nova Digital віддалено', 'Не просто код, а твій вплив на мільйони українців Nova Digital — це технологічне серце екосистеми NOVA, де твій код стає частиною щоденного життя цілої країни.', '369678', 'https://jobs.dou.ua/companies/nova-digital/vacancies/369678/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a0789598fa7adcaf99040b719cbe99, 1, 'Done'),
(0x01a07895991773729657e07e0fa00179, '2026-09-06 21:17:45.111216', '2026-09-10 17:55:20.977754', NULL, 'Senior/ Lead PHP Engineer (with AI Skills) (#5504) в N-iX Київ, Львів, Дніпро, Вінниця, Івано-Франківськ, Тернопіль, віддалено', 'Toronto time zone. Project duration — 3 months. We’re looking for a mature, Senior/Lead PHP Engineer with deep Laravel expertise and hands-on experience building or integrating agentic AI systems.', '258416', 'https://jobs.dou.ua/companies/n-ix/vacancies/258416/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a07895991677f49580dd51dfd198e3, 1, 'Done'),
(0x01a07895991876da99d6872acad35d77, '2026-09-06 21:17:45.112435', '2026-09-10 17:55:20.967646', NULL, 'Middle PHP Engineer (#5298) в N-iX Київ, Львів, Дніпро, Вінниця, Івано-Франківськ, Тернопіль, віддалено', 'N-iX is a global software development service company that helps businesses across the globe create next-generation software products.', '298240', 'https://jobs.dou.ua/companies/n-ix/vacancies/298240/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a07895991677f49580dd51dfd198e3, 1, 'Done'),
(0x01a07895991e738fa55d9502ab0ff7e4, '2026-09-06 21:17:45.118222', '2026-09-10 17:55:20.957025', NULL, 'Full Stack Developer в Stape за кордоном, віддалено', 'Location: EU-based Cooperation type: Full-time 🧭 Work format: Remote We’re looking for a skilled Full Stack Developer to join our product team.', '364868', 'https://jobs.dou.ua/companies/stape-inc/vacancies/364868/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a0789598f27d51b6d040e3d0adcef6, 1, 'Done'),
(0x01a078959922723ab7ced0d21b06b544, '2026-09-06 21:17:45.122138', '2026-09-10 17:55:20.946628', NULL, 'Full-Stack Developer (AI Oriented) в Frozeneon $2000–2500 віддалено', 'Обов’язки: Розробка та підтримка високонавантажених проєктів і мікросервісів Оптимізація продуктивності, рефакторинг коду та баз даних Проєктування масштабованої архітектури Участь у прийнятті технічних рішень та обмін досвідом усередині команди Вимоги: Практичний...', '369309', 'https://jobs.dou.ua/companies/frozeneon/vacancies/369309/', '$2000–2500', 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a0789598fb79d2abe632feb877e652, 1, 'Done'),
(0x01a07895992d7530b2b40f0eb1cfc228, '2026-09-06 21:17:45.133326', '2026-09-10 17:55:20.935341', NULL, 'Fractional AI Technical Lead (PHP + Java) в Dinarys віддалено', 'Dinarys is an Ecommerce Development Company helping retailers, brands, and manufacturers across DACH, the UK, and the US build, scale, and optimize their digital commerce solutions through custom development, architecture consulting, integrations, and AI-driven innovation.', '368865', 'https://jobs.dou.ua/companies/dinarys/vacancies/368865/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a0789598f772a686d2b62628a3c690, 1, 'Done'),
(0x01a07b6ac7d179cdb511dd756a9fe5ba, '2026-09-07 10:29:50.673618', '2026-09-10 17:55:20.925021', '2026-09-07 00:00:00.000000', 'Middle/Senior backend engineer в Togizu Technologies Ukraine, віддалено', 'Togizu Technologies is looking for an experienced Backend engineer to join our team. You will work under the leadership of the Solution Architect and report to him.', '222403', 'https://jobs.dou.ua/companies/togizu-technologies-ltd/vacancies/222403/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a07c03366d7617969584c21a071d30, 1, 'Done'),
(0x01a07b753a907486b31a9fa1530cddb9, '2026-09-07 10:41:15.408279', '2026-09-10 17:55:20.914650', '2026-09-07 00:00:00.000000', 'PHP Developer', '(вакансія неактивна)', '232686', 'https://jobs.dou.ua/companies/software-service-and-innovation/vacancies/232686/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a07b73fef870d9a08ca3b94261e11b, 1, 'Done'),
(0x01a07c03366a7f0e9f18a81699a6c6a9, '2026-09-07 13:16:20.458958', '2026-09-10 17:55:20.904982', NULL, 'Lead PHP/Laravel Engineer — Product-Focused в Rebbix Львів, віддалено', 'About the Role Our client is a leading European company providing solutions for product, material, and logistics cycles, with a strong focus on recycling and sustainability.', '372353', 'https://jobs.dou.ua/companies/rebbix/vacancies/372353/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a07c03366a739a9f18a8169909cdc5, 1, 'Done'),
(0x01a07c03366b7e70994cb2186a1e77e0, '2026-09-07 13:16:20.459915', '2026-09-10 17:55:20.896254', NULL, 'Full-Stack Developer (part-time) в Zero Task Labs віддалено', 'Full-Stack Developer (part-time) Компанія: Zero Task Labs Zero Task Labs — українська продуктова студія, яка будує digital-продукти та AI-автоматизацію для mid-market компаній і funded стартапів по всьому світу.', '372349', 'https://jobs.dou.ua/companies/zerotasklabs/vacancies/372349/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a07c03366b7680994cb218696acddc, 1, 'Done'),
(0x01a07c03366c7f189764d48473debcf1, '2026-09-07 13:16:20.460961', '2026-09-10 17:55:20.884026', NULL, 'Senior /Lead Backend Developer в WhiteTech віддалено', 'At WhiteTech, we empower businesses in the financial ecosystem with cutting-edge white-label and SAAS solutions. Our comprehensive suite of tools is tailored for EMI, PSP, e-commerce, and open banking platforms.', '372348', 'https://jobs.dou.ua/companies/whitetech/vacancies/372348/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077f79c127074ab26a317a794f87e, 1, 'Done');
INSERT INTO `vacancies` (`id`, `created_at`, `updated_at`, `resume_sent_at`, `title`, `description`, `external_id`, `url`, `salary`, `source_id`, `company_id`, `viewed`, `status`) VALUES
(0x01a07c03366f70e5adf38de7011fb7f4, '2026-09-07 13:16:20.463052', '2026-09-10 17:55:20.873009', NULL, 'PHP Laravel Developer в Frozeneon $1600–1800 віддалено', 'Обов’язки Розробляти та підтримувати фічі у високонавантаженому моноліті, мікросервісах та внутрішніх пакетах (shared-бібліотеки, SDK). Інтегрувати API та платіжні системи, працювати з вебхуками та постбеками.', '372304', 'https://jobs.dou.ua/companies/frozeneon/vacancies/372304/', '$1600–1800', 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a0789598fb79d2abe632feb877e652, 1, 'Done'),
(0x01a07c0336707cd581be1d9bf71a1d5a, '2026-09-07 13:16:20.464817', '2026-09-10 17:55:20.863104', '2026-09-07 00:00:00.000000', 'Middle PHP (Symfony) Developer в Stfalcon Хмельницький, віддалено', 'Stfalcon — українська IT-компанія з понад 16-річною історією розробки веб- та мобільних продуктів для бізнесу. Ми працюємо з проєктами у сферах логістики, транспорту, охорони здоров’я та фінтеху, де важливі стабільність, масштабованість і продумана архітектура.', '372239', 'https://jobs.dou.ua/companies/stfalcon-studio/vacancies/372239/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a07c03367070bd81be1d9bf638a26c, 1, 'Done'),
(0x01a07cad950f7f82b7aee3ea6a4eace9, '2026-09-07 16:22:25.807982', '2026-09-10 17:55:20.854433', NULL, 'Drupal Developer в AnyforSoft віддалено', 'Hi there! We are Anyforsoft, an outsourcing IT company that has been helping clients from Europe and US build scalable systems in domains such as EdTech, eCommerce, media and more for over 10 years. We are looking for a skilled Drupal Developer to join our team.', '372392', 'https://jobs.dou.ua/companies/anyforsoft/vacancies/372392/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a07cad950e71d7be93a4e255c2f443, 1, 'Done'),
(0x01a07d5e9e0c773b8bb7304dceb120e5, '2026-09-07 19:35:47.980451', '2026-09-10 17:55:20.843986', '2026-09-07 00:00:00.000000', 'Backend Developer(PHP) в EVOPLAY Київ, віддалено', 'Evoplay — одна з провідних продуктових IT-компаній України, яка працює на міжнародному ринку та створює комплексні B2B-рішення для індустрії онлайн-ігор.', '372414', 'https://jobs.dou.ua/companies/evoplay/vacancies/372414/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a07d5e9e097f69b0db76c1112c10da, 1, 'Done'),
(0x01a08169f75173a8bc04147b9f98b6e7, '2026-09-08 14:26:40.593224', '2026-09-10 17:55:20.833373', '2026-09-08 00:00:00.000000', 'Senior Full-Stack Developer PHP (Laravel + Vue.js) в Digital Hiring $3200–3500 віддалено', 'Remote | Senior | PHP + Laravel + Vue.js Шукаємо Senior Full-Stack Developer для нашого клієнта — великого українського eCommerce-ритейлера з 20+ роками на ринку та десятками представлених світових брендів.', '372540', 'https://jobs.dou.ua/companies/digital-hiring/vacancies/372540/', '$3200–3500', 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a5172faaf36aaaa61c9f0c8, 1, 'Done'),
(0x01a08169f7527939b90b5bf4352f01f7, '2026-09-08 14:26:40.594584', '2026-09-10 17:55:20.822437', NULL, 'Senior PHP Engineer (Vanilla PHP) в SoloWay Technologies Івано-Франківськ, Чернівці, віддалено', 'Remote · Full-time · Payments Platform About the role: We’re staffing a PHP Engineer for a team at a payments company.', '372538', 'https://jobs.dou.ua/companies/soloway-tech/vacancies/372538/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a08169f7517db4bc04147b9fb373e9, 1, 'Done'),
(0x01a08169f7537be68a5e4da7e8486ec8, '2026-09-08 14:26:40.595757', '2026-09-10 17:55:20.812770', NULL, 'Team Lead PHP Engineer в airSlate Вроцлав (Польща), віддалено', 'About airSlate airSlate is a global SaaS technology company that develops no-code workflow automation, electronic signature, and document management solutions.', '367780', 'https://jobs.dou.ua/companies/airslate/vacancies/367780/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a517b0eaf36aaaa6548f82c, 1, 'Done'),
(0x01a08198359e769d9b5841daf6e014d0, '2026-09-08 15:17:11.198408', '2026-09-10 17:55:20.800689', '2026-09-08 00:00:00.000000', 'PHP (symfony) strong middle backend developer', 'Запрошуємо на роботу strong middle developer (PHP Symfony) для розробки backend сервісів електроної комерцїї в продуктову компанію Кого ми шукаємо: - У вас є досвід комерційної розробки на PHP з використанням Symfony фреймворку від 2 років; - PostgreSQL/MySQL - Ви застосовуєте на практиці принципи SOLID, KISS, DRY, OOП та design patterns - REST Api, JSON - Ваш рівень компетенції дозволяє приймати та обґрунтовувати технічні рішення; - У вас є досвід роботи з інструментами: Git, Docker; - Буде плюсом досвід...', '839177', 'https://djinni.co/jobs/839177-php-symfony-strong-middle-backend-developer/', '$$$', 0x01a08169a23b78d7b5288e259d626675, 0x01a08198359a707e80c82a67665269b9, 1, 'Done'),
(0x01a0819835a370dd9e418dcc07800c93, '2026-09-08 15:17:11.203040', '2026-09-10 17:55:20.791516', NULL, 'Senior Full Stack PHP Developer (Symfony)', 'We are currently looking for a Senior Full Stack PHP Developer to join a professional German-based product company team that designs innovative eHealth solutions for hospitals. You will work on a stable healthcare product, contributing to backend architecture and supporting frontend functionality where needed. Qualifications: 5+ years of professional PHP, with real depth in Symfony (DI, console, event handling, Doctrine). We run PHP 8.4 / Symfony 7.4 and stay current. Proven external systems integration...', '803894', 'https://djinni.co/jobs/803894-senior-full-stack-php-developer-symfony/', '$$$$', 0x01a08169a23b78d7b5288e259d626675, 0x01a0819835a07c1c83b47c235efe355a, 1, 'Done'),
(0x01a0819835a67319ba5af2f1b88b3a85, '2026-09-08 15:17:11.206184', '2026-09-10 17:55:20.781709', NULL, 'PHP Developer', 'Привіт! Checkbox — українська продуктова фінтех-компанія. Щомісяця ми фіскалізуємо понад 100 мільйонів чеків для 100+ тисяч підприємців і швидко зростаємо. Ми шукаємо сильного PHP-розробника, який упевнено працює з високонавантаженими системами та готовий вести розвиток продукту: апгрейд стеку, міграцію черг і побудову автоматизованих перевірок у CI. Технічний стек: PHP (7.4 → 8.x), Symfony (4.4 → 6), API Platform 2.5, Doctrine ORM, PostgreSQL, Symfony Messenger, RabbitMQ, Kafka, Redis, Docker. Наші...', '842295', 'https://djinni.co/jobs/842295-php-developer/', '$2000-3000', 0x01a08169a23b78d7b5288e259d626675, 0x01a077e29a507ed283b70c855afcba1e, 1, 'Done'),
(0x01a0819835aa79de8fec3e3dae51fe9b, '2026-09-08 15:17:11.210617', '2026-09-10 17:55:20.770228', NULL, 'PHP Developer', 'Responsibilities Developing functionality for personal accounts for webmasters and administrators. Creation of a system for receiving and processing postback notifications from partners. Integration of third-party services (SMS gateways, anti-fraud, Geo-IP). Code coverage with tests (PHPUnit). Requirements At least 3 years of experience in commercial development on Symfony. Ability to write clean, maintainable code (SOLID, DRY). Experience with PostgreSQL (complex joins, indexes). Experience with...', '797689', 'https://djinni.co/jobs/797689-php-developer/', '$$$', 0x01a08169a23b78d7b5288e259d626675, 0x01a0819835a87740988b0182d7a6a579, 1, 'Done'),
(0x01a08c681cf4780d8a6e7b201e87bbf8, '2026-09-10 17:40:28.532475', '2026-09-10 17:58:07.475105', '2026-09-10 00:00:00.000000', 'Senior Backend Developer (PHP) в Gamingtec Лімасол (Кіпр), Лондон (Велика Британія), Тбілісі (Грузія), віддалено', 'Are you ready to take your PHP skills to the next level? We are dedicated to providing ongoing support for an existing project built on Laravel/Symfony/React technologies.', '372878', 'https://jobs.dou.ua/companies/gamingtec/vacancies/372878/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a08c681cef7e5f9669ddb09d4a582f, 1, 'pending'),
(0x01a08c681d007027ad043662b295922f, '2026-09-10 17:40:28.543986', '2026-09-10 17:55:20.748412', NULL, 'Magento 2 Full-Stack Developer (part-time) в Kozak Group віддалено', 'Kozak Group — українська сервісна ІТ-компанія, заснована в 2015 році. Ми будуємо та підтримуємо складні рішення електронної комерції, маркетплейси та високозавантажені платформи для клієнтів з Європи, США та України.', '372790', 'https://jobs.dou.ua/companies/kozak-group/vacancies/372790/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a08c681cfb76e785525a89f55e3f15, 1, 'Done'),
(0x01a08c681d057cb993756ed2c1745f29, '2026-09-10 17:40:28.549794', '2026-09-10 17:57:59.671270', '2026-09-10 00:00:00.000000', 'Senior PHP Developer в Altamira віддалено', 'Altamira is a software development outsourcing company. We provide solutions that make a tangible impact on our clients’ growth and productivity.', '372787', 'https://jobs.dou.ua/companies/altamira-ai/vacancies/372787/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a08c681d027c9a9ed1e90683cb9145, 1, 'pending'),
(0x01a08c681d0a7e12bf3919dec22b56d0, '2026-09-10 17:40:28.554885', '2026-09-10 17:55:20.727242', NULL, 'Middle WordPress Developer (Freelance) в Embrox Solutions віддалено', 'At Embrox Solutions, we bring ideas to life. We’re not just about writing code — we create complete hardware and software products that make a difference.', '325762', 'https://jobs.dou.ua/companies/embrox-solutions/vacancies/325762/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a08c681d087870a4840fed3b98a0b4, 1, 'Done'),
(0x01a08c681d0f756e9412f65bf38ed555, '2026-09-10 17:40:28.559323', '2026-09-10 17:57:00.045427', '2026-09-09 00:00:00.000000', 'Senior Back-End Developer (all genders) в eduki $4300–5900 віддалено', 'Back-End Developer (all genders) | Microservice Developer (all genders) | PHP Developer (all genders) | Back-End Engineer (all genders) | Microservice Engineer (all genders) Level: Senior | Ukraine (Working from Home) | Full Time (40h/week) | Salary Range: $4.300 — $5.900...', '372701', 'https://jobs.dou.ua/companies/lehrermarktplatz/vacancies/372701/', '$4300–5900', 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a077e29a4f7f71b7d94ba26d447e7d, 1, 'pending'),
(0x01a08c681d3c7bde9e0c6cbc6b91fcc8, '2026-09-10 17:40:28.604736', '2026-09-10 17:55:20.706633', NULL, 'WordPress Developer (freelance) в Erbis за кордоном, віддалено', 'About us: Erbis is a thriving tech powerhouse where innovation meets opportunity. We specialize in building cutting-edge software solutions that drive success for businesses worldwide. At Erbis, we don’t just develop software. We create impact.', '371931', 'https://jobs.dou.ua/companies/erbis/vacancies/371931/', NULL, 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a08c681d367ed2af4f7d6c2b579bf1, 1, 'Done'),
(0x01a091ade6b272e68fca24618cd53aa3, '2026-09-11 18:14:48.242179', '2026-09-11 20:34:55.140688', NULL, 'PHP розробник в Lavriv, Sole Proprietor (Ukraine) $1500–2000 Київ, Львів, Івано-Франківськ, віддалено', 'Посадові обов’язки: Розробка та підтримка backend-сервісів на PHP. Міграція даних між сервісами та системами. Створення та зміна структури таблиць у БД. Розробка нових звітів та вибірок у БД. Розробка нових backend-сервісів та API.', '372960', 'https://jobs.dou.ua/companies/fop-lavriv/vacancies/372960/', '$1500–2000', 0x01a077e2815c7509b25e6fcea82c5d29, 0x01a091ade6af7d34ba35982bb4a25fbb, 1, 'Done');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_name` (`name`),
  ADD KEY `created_at_idx` (`created_at`),
  ADD KEY `updated_at_idx` (`updated_at`);

--
-- Indexes for table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `grab_logs`
--
ALTER TABLE `grab_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_source_id` (`source_id`),
  ADD KEY `created_at_idx` (`created_at`),
  ADD KEY `updated_at_idx` (`updated_at`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_company_id` (`company_id`),
  ADD KEY `fk_vacancy_id` (`vacancy_id`),
  ADD KEY `idx_noted_at` (`noted_at`),
  ADD KEY `created_at_idx` (`created_at`),
  ADD KEY `updated_at_idx` (`updated_at`);

--
-- Indexes for table `sources`
--
ALTER TABLE `sources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_at_idx` (`created_at`),
  ADD KEY `updated_at_idx` (`updated_at`),
  ADD KEY `idx_active` (`active`);

--
-- Indexes for table `vacancies`
--
ALTER TABLE `vacancies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_source_id` (`source_id`),
  ADD KEY `fk_company_id` (`company_id`),
  ADD KEY `idx_resume_sent_at` (`resume_sent_at`),
  ADD KEY `created_at_idx` (`created_at`),
  ADD KEY `updated_at_idx` (`updated_at`),
  ADD KEY `idx_status` (`status`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `grab_logs`
--
ALTER TABLE `grab_logs`
  ADD CONSTRAINT `FK_330BC674953C1C61` FOREIGN KEY (`source_id`) REFERENCES `sources` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `FK_11BA68C433B78C4` FOREIGN KEY (`vacancy_id`) REFERENCES `vacancies` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `FK_11BA68C979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `vacancies`
--
ALTER TABLE `vacancies`
  ADD CONSTRAINT `FK_99165A59953C1C61` FOREIGN KEY (`source_id`) REFERENCES `sources` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `FK_99165A59979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
