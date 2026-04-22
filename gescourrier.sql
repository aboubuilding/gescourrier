-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 22 avr. 2026 à 16:59
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
-- Base de données : `gescourrier`
--

-- --------------------------------------------------------

--
-- Structure de la table `agents`
--

CREATE TABLE `agents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `fonction` varchar(255) DEFAULT NULL COMMENT 'Poste ou rôle attribué',
  `service_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `etat` int(11) NOT NULL DEFAULT 1 COMMENT '1=actif, 2=inactif/supprimé',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `agents`
--

INSERT INTO `agents` (`id`, `nom`, `prenom`, `email`, `telephone`, `fonction`, `service_id`, `user_id`, `etat`, `created_at`, `updated_at`) VALUES
(1, 'Sammie', 'Boehm', 'kareem21@example.net', '325-443-5809', 'Chef de service', 1, 2, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(2, 'Ezequiel', 'King', 'daphne.reichert@example.com', '1-980-265-1594', 'Chef de service', 2, 3, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(3, 'Emma', 'Trantow', 'caterina62@example.org', '+18705259836', 'Chef de service', 3, 4, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(4, 'Mr.', 'Donavon', 'obartell@example.org', '+1-424-946-6841', 'Chef de service', 4, 5, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(5, 'Enola', 'Predovic', 'sharon90@example.net', NULL, 'Chef de service', 5, 6, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(6, 'Batz', 'Francis', 'abagail.homenick@example.com', NULL, 'Assistant', 2, 7, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(7, 'Bode', 'Xavier', 'igleason@example.org', '+18502585141', 'Agent de saisie', 4, 15, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(8, 'Terry', 'Juliana', 'kailyn.stroman@example.org', NULL, 'Assistant', 2, 21, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(9, 'Krajcik', 'Kurtis', 'luna.heathcote@example.org', NULL, 'Agent de saisie', 2, 16, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(10, 'O\'Reilly', 'Johanna', 'morissette.erin@example.com', '262.442.6634', 'Gestionnaire', 4, 14, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(11, 'Graham', 'Lyda', 'cade29@example.org', NULL, 'Chargé de mission', 5, 18, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(12, 'Feest', 'Braulio', 'ayla58@example.org', '+1-361-540-4454', 'Assistant', 3, 17, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(13, 'Price', 'Kelley', 'tate.blick@example.org', '218.683.6598', 'Gestionnaire', 3, 13, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(14, 'Bergstrom', 'Rene', 'hmcglynn@example.net', '(726) 627-2802', 'Gestionnaire', 4, 21, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(15, 'Heaney', 'Jarrett', 'cassidy12@example.org', '1-312-621-6748', 'Gestionnaire', 1, 16, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(16, 'Schultz', 'Elsie', 'rsatterfield@example.net', NULL, 'Gestionnaire', 3, 20, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(17, 'Medhurst', 'Cali', 'yasmin96@example.net', '1-813-904-5254', 'Gestionnaire', 4, 18, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(18, 'Cummerata', 'Jabari', 'growe@example.net', '1-949-704-6010', 'Chargé de mission', 4, 7, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(19, 'Kshlerin', 'Harold', 'elsa06@example.org', '+1-283-327-5924', 'Gestionnaire', 2, 14, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(20, 'Wilderman', 'Aric', 'dariana13@example.net', NULL, 'Assistant', 4, 15, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(21, 'Rippin', 'Armand', 'klein.jeramy@example.com', '682-943-2528', 'Assistant', 2, 9, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(22, 'Harber', 'Kaya', 'porter37@example.com', '1-320-916-9445', 'Chargé de mission', 1, 20, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(23, 'Conn', 'Dalton', 'letitia.johnson@example.net', NULL, 'Assistant', 1, 10, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(24, 'Ondricka', 'Asia', 'graciela.hoppe@example.net', '+1 (430) 953-0145', 'Gestionnaire', 2, 21, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(25, 'Fahey', 'Darius', 'cordie78@example.net', NULL, 'Chargé de mission', 4, 17, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30');

-- --------------------------------------------------------

--
-- Structure de la table `courriers`
--

CREATE TABLE `courriers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reference` varchar(255) DEFAULT NULL COMMENT 'Référence unique (externe ou interne)',
  `numero` varchar(255) DEFAULT NULL COMMENT 'Numéro de chronologie/enregistrement',
  `type` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=entrant, 1=sortant, 2=interne',
  `priorite` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=normale, 1=urgente, 2=très_urgente',
  `statut` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=non_affecte, 1=affecte, 2=traite',
  `objet` varchar(255) DEFAULT NULL COMMENT 'Objet du courrier',
  `description` text DEFAULT NULL COMMENT 'Contenu détaillé ou notes',
  `date_reception` date DEFAULT NULL,
  `date_envoi` date DEFAULT NULL,
  `date_affectation` timestamp NULL DEFAULT NULL,
  `date_traitement` timestamp NULL DEFAULT NULL,
  `url_fichier` varchar(255) DEFAULT NULL COMMENT 'Chemin relatif dans storage/app/public',
  `fichier_nom_original` varchar(255) DEFAULT NULL COMMENT 'Nom du fichier lors de l''upload',
  `fichier_mime_type` varchar(255) DEFAULT NULL COMMENT 'Ex: application/pdf, image/jpeg',
  `fichier_taille` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Taille en octets',
  `agent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `service_id` bigint(20) UNSIGNED DEFAULT NULL,
  `utilisateur_id` bigint(20) UNSIGNED DEFAULT NULL,
  `organisation_id` bigint(20) UNSIGNED DEFAULT NULL,
  `etat` int(11) NOT NULL DEFAULT 1 COMMENT '1=actif, 2=supprimé',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `courriers`
--

INSERT INTO `courriers` (`id`, `reference`, `numero`, `type`, `priorite`, `statut`, `objet`, `description`, `date_reception`, `date_envoi`, `date_affectation`, `date_traitement`, `url_fichier`, `fichier_nom_original`, `fichier_mime_type`, `fichier_taille`, `agent_id`, `service_id`, `utilisateur_id`, `organisation_id`, `etat`, `created_at`, `updated_at`) VALUES
(1, 'REF-8352-03', 'N°0525348', 0, 1, 1, 'Accusantium qui hic repudiandae.', NULL, '2026-02-12', NULL, '2026-04-20 17:49:02', NULL, NULL, NULL, NULL, NULL, 1, 2, NULL, 2, 1, '2026-04-17 22:52:30', '2026-04-20 17:49:02'),
(2, 'REF-4417-77', 'N°3907661', 0, 1, 0, 'Consequatur et nesciunt non.', NULL, '2026-02-18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 2, 0, '2026-04-17 22:52:30', '2026-04-21 15:44:13'),
(3, 'REF-2172-23', 'N°8332460', 0, 1, 1, 'Vel quidem sunt nesciunt praesentium.', 'Earum accusantium et repudiandae. Et maiores aperiam nemo qui qui libero ut. Consequatur a qui enim.', '2025-12-19', NULL, '2026-04-21 16:30:03', NULL, NULL, NULL, NULL, NULL, 4, 4, NULL, 2, 1, '2026-04-17 22:52:30', '2026-04-21 16:30:03'),
(4, 'REF-6215-46', 'N°1627216', 0, 1, 0, 'Sit debitis cupiditate error.', 'Vitae nihil qui debitis eos molestiae voluptas rerum tempore. Ut quo rerum laboriosam labore. Perspiciatis eaque repellat quibusdam. Iste consequatur nostrum voluptas molestias sit.', '2026-03-18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3, NULL, 6, 0, '2026-04-17 22:52:30', '2026-04-21 15:43:39'),
(5, 'REF-6124-36', 'N°9314704', 0, 1, 0, 'Rerum tempora doloremque.', NULL, '2026-04-09', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3, NULL, NULL, 0, '2026-04-17 22:52:30', '2026-04-21 15:52:27'),
(6, 'REF-8789-67', 'N°9444541', 0, 1, 1, 'Qui reiciendis quia quo deleniti.', 'Odit earum ut ex incidunt laudantium fugiat asperiores. Fugiat maiores placeat eius. Libero et beatae aut est.', '2026-04-06', NULL, '2026-04-20 16:57:35', NULL, NULL, NULL, NULL, NULL, 3, 12, NULL, 4, 1, '2026-04-17 22:52:30', '2026-04-20 16:57:35'),
(7, 'REF-0741-84', 'N°5187730', 0, 1, 0, 'Ad natus quae est minima eveniet.', 'Est itaque qui assumenda culpa ex velit voluptatem. Veritatis voluptatem qui sit aliquid. Odit qui et blanditiis ex id alias excepturi.', '2026-04-13', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4, NULL, 6, 0, '2026-04-17 22:52:30', '2026-04-21 15:45:08'),
(8, 'REF-5936-08', 'N°6895347', 0, 1, 1, 'Aut aut ullam quisquam.', 'Quis laboriosam magni sint dolore. Maiores aut iste laboriosam quod quis. Voluptas cumque voluptate et incidunt adipisci omnis. Et vero eum saepe debitis vitae atque molestiae rerum.', '2025-12-13', NULL, '2026-04-21 16:31:00', NULL, NULL, NULL, NULL, NULL, 2, 2, NULL, 4, 1, '2026-04-17 22:52:30', '2026-04-21 16:31:00'),
(9, 'REF-8007-95', 'N°8308056', 0, 1, 0, 'Maiores commodi harum similique ut.', 'Itaque voluptas mollitia iure distinctio non omnis quia. Aut distinctio quos dolor recusandae reprehenderit magnam. Fugit et in adipisci consequuntur voluptatem.', '2025-10-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3, NULL, 2, 0, '2026-04-17 22:52:30', '2026-04-21 15:45:46'),
(10, 'REF-7344-91', 'N°0389113', 0, 1, 0, 'A veritatis molestias cum ut omnis.', NULL, '2025-11-02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, 6, 0, '2026-04-17 22:52:30', '2026-04-21 15:42:39'),
(11, 'REF-4553-22', 'N°4209175', 0, 1, 1, 'Maiores et explicabo deserunt vitae ullam.', NULL, '2026-01-25', NULL, '2026-04-17 22:52:30', NULL, 'courriers/cf5586bc-bb56-368b-b112-ceff35d0680e.', 'quisquam-provident-aperiam-aperiam-enim_20260409_2465.pdf', 'application/pdf', 1455090, 10, 8, NULL, NULL, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(12, 'REF-5277-20', 'N°9229185', 0, 1, 1, 'Mollitia hic est.', 'Et perferendis corrupti nostrum. Commodi quaerat facilis ducimus quod officiis dolorum. Sunt qui officia dolorum quaerat amet sed debitis quas.', '2026-03-29', NULL, '2026-04-17 22:52:30', NULL, 'courriers/c225068b-f9e8-3197-80d8-9489b023c7fa.', 'ipsam-aut-omnis-ut-ut-fugit-temporibus-tempore_20260214_8507.pdf', 'application/pdf', 472865, 15, 9, NULL, NULL, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(13, 'REF-7088-61', 'N°0127003', 0, 0, 1, 'Dolorem voluptatibus reprehenderit illum.', 'Tempore voluptatem vero odio voluptas. Praesentium omnis ut praesentium error illum id esse. Neque iusto ex dolor sunt laborum quo qui. Libero est voluptatem dolore repellendus dignissimos ut.', '2025-11-21', NULL, '2026-04-17 22:52:30', NULL, 'courriers/1092e844-88d9-3616-93f5-8bfbc8fc34bd.', 'vel-incidunt-provident-qui-omnis-officiis-minus-facilis_20260412_0882.pdf', 'application/pdf', 2203192, 20, 3, NULL, NULL, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(14, 'REF-1082-27', 'N°4028164', 0, 0, 1, 'Et pariatur eos sint.', 'Ullam ullam corporis aut expedita dolores libero sit. Officiis fuga qui sit ullam sunt. Quidem aliquid sunt in placeat ut consectetur est.', '2026-04-14', NULL, '2026-04-17 22:52:30', NULL, 'courriers/19cfe0fe-3843-342b-a52e-12a5d5f684a5.', 'est-magni-voluptatem-dicta-facere-sapiente-harum_20260314_6851.pdf', 'application/pdf', 2469824, 15, 6, NULL, NULL, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(15, 'REF-1581-74', 'N°2925390', 0, 1, 1, 'Ipsam unde architecto.', NULL, '2025-10-27', NULL, '2026-04-17 22:52:30', NULL, 'courriers/d55cbd8d-d2dd-346e-b7e2-5e577e9f3298.', 'aut-odio-neque-id-ex-et_20260114_7721.pdf', 'application/pdf', 4328501, 21, 15, NULL, NULL, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(16, 'REF-4795-68', 'N°1891895', 0, 0, 1, 'Sed quia in aut dicta.', 'Omnis culpa eligendi numquam voluptatum neque. Eligendi corporis beatae repellat similique aperiam excepturi. Qui quia reiciendis quia sint et consequatur vitae rerum. Voluptas quia quia maxime pariatur.', '2025-11-11', NULL, '2026-04-17 22:52:30', NULL, 'courriers/99fba850-9a8a-3b3d-88a4-7a3519ca454d.', 'voluptatem-eligendi-ex-iusto-voluptas-aut-voluptate_20260124_5345.pdf', 'application/pdf', 4851880, 16, 5, NULL, NULL, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(17, 'REF-4803-23', 'N°4320383', 0, 0, 1, 'Libero nemo vel.', 'Maxime voluptates minima hic veniam. Culpa error quasi laborum velit ipsum quia quisquam. Earum consequatur et quae voluptas non repudiandae cumque fugiat. Consequatur nesciunt in in eveniet rerum animi sit.', '2026-02-26', NULL, '2026-04-17 22:52:30', NULL, 'courriers/9729ab27-4fec-34c9-a712-1b20f8c513c2.', 'nesciunt-magni-nihil-enim-tenetur_20260406_8887.pdf', 'application/pdf', 2043208, 21, 3, NULL, NULL, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(18, 'REF-8902-77', 'N°8138883', 0, 0, 1, 'Consectetur nostrum perspiciatis reiciendis omnis quod.', 'Repellendus excepturi sit magni eaque neque hic quasi. Labore quidem repellat ut dignissimos excepturi at et qui.', '2025-11-29', NULL, '2026-04-20 16:56:52', NULL, 'courriers/8efb8004-7401-357f-b488-f5809e1f64d1.', 'dolores-quae-laudantium-unde-sed_20260219_7734.pdf', 'application/pdf', 149479, 2, 12, NULL, NULL, 1, '2026-04-17 22:52:30', '2026-04-20 16:56:52'),
(19, 'REF-6639-24', 'N°7401687', 0, 0, 1, 'Quo repudiandae dolorem dolorum et.', 'Eius incidunt et pariatur voluptatum distinctio necessitatibus fugiat in. Cum odit quae fuga culpa. Eveniet ut eos qui qui aut necessitatibus. Omnis eos aperiam similique neque maiores.', '2025-12-11', NULL, '2026-04-17 22:52:30', NULL, 'courriers/0150eefb-e403-373c-8038-f393e422ba89.', 'velit-perspiciatis-cupiditate-tempora-enim-voluptas-modi_20260105_4738.pdf', 'application/pdf', 4586915, 2, 9, NULL, NULL, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(20, 'REF-5316-81', 'N°8537036', 0, 0, 1, 'Amet neque deserunt reiciendis qui.', 'Laudantium distinctio in debitis cupiditate dolorum. Itaque cum blanditiis asperiores veniam minima ea. Non consequatur et aliquam fugit. Vero architecto ea laboriosam ex.', '2026-01-13', NULL, '2026-04-17 22:52:30', NULL, 'courriers/64e72a56-0f63-39c7-be31-96b844c299c3.', 'sequi-eius-inventore-rerum-autem-quod_20260305_7879.pdf', 'application/pdf', 225507, 20, 14, NULL, NULL, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(21, 'REF-5443-15', 'N°2622449', 0, 1, 1, 'Aliquam rerum omnis laboriosam.', 'Odit ab molestias ut. Deleniti magni sunt blanditiis assumenda officiis. Sed enim impedit commodi.', '2026-01-14', NULL, '2026-04-17 22:52:30', NULL, 'courriers/5ffddaad-c133-31f0-a641-4ab7e702e9c9.', 'corrupti-corrupti-sequi-ut-hic_20260103_7726.pdf', 'application/pdf', 3785087, 17, 5, NULL, NULL, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(22, 'REF-4877-40', 'N°6782142', 0, 0, 1, 'Sit asperiores ab eos nihil.', NULL, '2025-12-28', NULL, '2026-04-17 22:52:30', NULL, 'courriers/ec0776d8-5408-313f-ae6f-f6751baad5dd.', 'illo-optio-magnam-sapiente-saepe-eum-corrupti-praesentium-excepturi_20260102_6269.pdf', 'application/pdf', 233455, 17, 1, NULL, NULL, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(23, 'REF-6237-31', 'N°1681599', 0, 1, 1, 'Dolorem aperiam tempora vitae.', 'Mollitia blanditiis at accusamus quos. Pariatur dolorum autem possimus et dolorum quos. Quas numquam dolorum nulla sit. Ea nam velit excepturi autem et.', '2025-11-15', NULL, '2026-04-17 22:52:30', NULL, 'courriers/6b4047ec-2d8c-3a9f-8ab5-570fdd50f8f9.', 'delectus-porro-est-amet-quia-ratione-et-laudantium-distinctio_20260401_4964.pdf', 'application/pdf', 4076064, 15, 2, NULL, NULL, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(24, 'REF-7363-82', 'N°9088391', 0, 1, 1, 'Dignissimos quis dolor delectus.', 'Rem qui impedit rerum molestias nulla dicta est. Voluptatem repudiandae et dolorem et quae repellendus. Harum consequuntur aut qui itaque. Dolor suscipit ipsa similique non assumenda.', '2025-11-05', NULL, '2026-04-17 22:52:30', NULL, 'courriers/9c98189f-d493-3221-a53a-4765a5010fdb.', 'voluptatum-voluptate-dolorum-velit-possimus-sed-praesentium_20260410_5032.pdf', 'application/pdf', 3653217, 17, 14, NULL, NULL, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(25, 'REF-4675-44', 'N°3174470', 0, 0, 1, 'Voluptatum quo architecto et.', 'Totam commodi labore sint impedit similique ducimus. Quisquam ut repellat qui ut placeat est ab. Molestias eum ratione dolorum qui.', '2025-11-25', NULL, '2026-04-17 22:52:30', NULL, 'courriers/3fb1a6d0-9e4e-3498-baba-68f455cfda1d.', 'iure-beatae-quos-quas-ipsum-cumque_20260319_1419.pdf', 'application/pdf', 4364985, 21, 6, NULL, NULL, 1, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(26, 'REF-3595-05', 'N°1584438', 1, 0, 2, 'Incidunt reprehenderit consequuntur aut possimus.', NULL, '2026-01-30', NULL, NULL, '2026-02-18 02:10:30', 'courriers/418f74ad-de5d-359b-998c-3d0a48dac0cb.', 'expedita-facilis-est-id-et-repudiandae_20260217_6004.pdf', 'application/pdf', 2047414, NULL, NULL, 22, 8, 1, '2026-04-17 22:52:35', '2026-04-17 22:52:35'),
(27, 'REF-5393-75', 'N°0760223', 0, 0, 2, 'Aperiam recusandae distinctio.', 'Tenetur quisquam qui eos deserunt est soluta. Magnam voluptas est totam nemo neque eius. Eos nostrum voluptate velit explicabo.', '2026-02-09', NULL, NULL, '2026-03-16 19:45:43', 'courriers/97de7918-e4b5-3c73-93bb-82ede01fa280.', 'ut-quis-sint-sit-asperiores-enim-id-nesciunt_20260323_8146.pdf', 'application/pdf', 1234052, NULL, NULL, 23, 9, 1, '2026-04-17 22:52:35', '2026-04-17 22:52:35'),
(28, 'REF-5872-75', 'N°2224362', 0, 1, 2, 'Nulla eligendi sequi dolore consequatur dolor.', 'Vel corrupti reprehenderit iste ut eligendi. Est ducimus adipisci id error aspernatur. Ut est facilis et occaecati similique.', '2026-03-14', NULL, NULL, '2026-02-12 20:13:36', 'courriers/ed64c5fd-20c5-31fa-b29b-d2eff482404a.', 'vel-velit-maiores-voluptas-eos-ut_20260416_2435.pdf', 'application/pdf', 2318730, NULL, NULL, 24, 9, 1, '2026-04-17 22:52:35', '2026-04-17 22:52:35'),
(29, 'REF-8014-45', 'N°4220403', 2, 0, 2, 'Suscipit repudiandae quo.', 'Tempora similique minus velit ab. In est minus totam repudiandae. Possimus dolore optio id quibusdam vel qui est. Sed dolorem at ipsa enim consectetur natus veniam tempora.', '2026-03-30', NULL, NULL, '2026-01-29 16:33:56', 'courriers/48897ea0-0a3c-3046-be1c-1aa4014abf80.', 'quos-aliquam-voluptate-reiciendis-rerum-sit-facilis-accusantium-nisi_20260310_6710.pdf', 'application/pdf', 4573036, NULL, NULL, 25, 9, 1, '2026-04-17 22:52:35', '2026-04-17 22:52:35'),
(30, 'REF-0578-92', 'N°8185239', 2, 0, 2, 'Quia mollitia perspiciatis facere.', NULL, '2026-02-27', NULL, NULL, '2026-02-24 10:27:10', 'courriers/463c066e-1a20-3874-8a80-c56168279d1f.', 'perspiciatis-id-nemo-et-consequuntur-itaque_20260318_8457.pdf', 'application/pdf', 2187888, NULL, NULL, 26, 9, 1, '2026-04-17 22:52:35', '2026-04-17 22:52:35'),
(31, 'REF-6114-67', 'N°3641459', 1, 1, 2, 'Accusamus mollitia ipsum nulla omnis.', 'Maiores et labore consectetur necessitatibus. Neque veniam error perferendis quibusdam id. Itaque molestiae beatae necessitatibus enim.', '2026-03-14', NULL, NULL, '2026-03-02 22:09:53', 'courriers/f8e54f8c-1582-33d5-9df8-f95d81d600b9.', 'corrupti-tempore-omnis-architecto-aperiam_20260205_9928.pdf', 'application/pdf', 2722926, NULL, NULL, 27, 7, 1, '2026-04-17 22:52:35', '2026-04-17 22:52:35'),
(32, 'REF-8198-28', 'N°7025430', 2, 1, 2, 'Laboriosam quia molestiae.', NULL, '2025-11-25', NULL, NULL, '2026-03-06 16:35:47', 'courriers/1fb5bd5b-c989-3ac5-b71d-6106b5e25072.', 'at-iusto-ratione-dolorem-alias_20260409_9968.pdf', 'application/pdf', 2241948, NULL, NULL, 28, 8, 1, '2026-04-17 22:52:35', '2026-04-17 22:52:35'),
(33, 'REF-6338-00', 'N°4682398', 2, 1, 2, 'Et facilis ipsa ratione sint similique.', NULL, '2025-12-12', NULL, NULL, '2026-03-06 09:25:10', 'courriers/d9809700-44a1-3726-9cfe-801dd89c0190.', 'inventore-in-magnam-libero-est-velit-cum-necessitatibus_20260412_5771.pdf', 'application/pdf', 4364824, NULL, NULL, 29, 8, 1, '2026-04-17 22:52:35', '2026-04-17 22:52:35'),
(34, 'REF-4538-71', 'N°3755734', 0, 1, 2, 'Minima molestiae explicabo.', NULL, '2026-02-05', NULL, NULL, '2026-03-15 02:46:45', 'courriers/c48fe06a-b37c-3a87-889d-48757d0f97c3.', 'ipsa-est-odit-repellat-ut_20260120_2976.pdf', 'application/pdf', 3799404, NULL, NULL, 30, 8, 1, '2026-04-17 22:52:35', '2026-04-17 22:52:35'),
(35, 'REF-5167-10', 'N°2832445', 0, 0, 2, 'Rerum reprehenderit dolores placeat quae.', NULL, '2026-03-17', NULL, NULL, '2026-03-16 19:58:18', 'courriers/3051f754-8fad-3fc6-a5bf-8a155b3e16ef.', 'sed-omnis-unde-ut-accusantium-mollitia_20260411_5786.pdf', 'application/pdf', 2800845, NULL, NULL, 31, 7, 1, '2026-04-17 22:52:35', '2026-04-17 22:52:35'),
(36, 'REF-0078-71', 'N°8333099', 1, 1, 2, 'Omnis officia laudantium neque.', NULL, '2026-03-29', NULL, NULL, '2026-01-18 08:43:40', 'courriers/f8aad01b-b7e3-332b-b273-de1ee49a08c1.', 'officia-aut-consequatur-praesentium-aut-iusto-voluptatibus_20260223_8553.pdf', 'application/pdf', 2752874, NULL, NULL, 32, 8, 1, '2026-04-17 22:52:35', '2026-04-17 22:52:35'),
(37, 'REF-7945-59', 'N°9048630', 2, 1, 2, 'Commodi cum nihil est sit quia.', NULL, '2025-12-05', NULL, NULL, '2026-02-24 21:33:57', 'courriers/af0faf1b-c31f-3e7a-a50c-54e9c82e8559.', 'aut-dolores-totam-sapiente-ut_20260116_7803.pdf', 'application/pdf', 2482445, NULL, NULL, 33, 9, 1, '2026-04-17 22:52:35', '2026-04-17 22:52:35'),
(38, 'REF-7036-71', 'N°5919636', 1, 1, 2, 'Ipsam ut voluptatem id.', NULL, '2026-03-11', NULL, NULL, '2026-02-12 20:37:36', 'courriers/23af3e10-30ed-3fd8-96aa-747ea449d1b8.', 'aspernatur-consequuntur-asperiores-quae-non-et_20260205_5062.pdf', 'application/pdf', 4623879, NULL, NULL, 34, 7, 1, '2026-04-17 22:52:35', '2026-04-17 22:52:35'),
(39, 'REF-6460-05', 'N°8946325', 2, 0, 2, 'Cum voluptate in earum hic.', NULL, '2026-04-03', NULL, NULL, '2026-01-30 08:13:35', 'courriers/e537a9d0-1f08-3292-90f9-306707506dcf.', 'qui-qui-dolor-et-ut-dignissimos-cum-voluptas_20260321_8592.pdf', 'application/pdf', 2453465, NULL, NULL, 35, 7, 1, '2026-04-17 22:52:35', '2026-04-17 22:52:35'),
(40, 'REF-2227-38', 'N°4187207', 2, 0, 2, 'Quidem cumque inventore.', NULL, '2026-04-06', NULL, NULL, '2026-02-08 20:22:17', 'courriers/24660526-cdf9-3a2a-be05-4f73ea547017.', 'quidem-ut-exercitationem-ut_20260123_6205.pdf', 'application/pdf', 1244528, NULL, NULL, 36, 8, 1, '2026-04-17 22:52:35', '2026-04-17 22:52:35'),
(41, 'REF-6787-57', 'N°1863022', 0, 1, 2, 'Iure minus omnis autem recusandae.', 'Voluptas illo dicta reiciendis molestias officia. Hic et molestiae aut vero rerum.', '2026-04-07', NULL, NULL, '2026-02-19 22:00:55', 'courriers/c825ac49-c4a1-3b54-8684-7d506ad610ab.', 'excepturi-commodi-culpa-error-voluptatibus-quis-quis_20260123_0894.pdf', 'application/pdf', 3642058, NULL, NULL, 37, 8, 1, '2026-04-17 22:52:35', '2026-04-17 22:52:35'),
(42, 'REF-2012-89', 'N°8912817', 0, 1, 2, 'Incidunt deleniti rem.', 'Voluptates veniam facilis distinctio exercitationem incidunt culpa omnis. Et nemo deserunt odio fuga et dicta. Laudantium ex quae consequatur reprehenderit doloribus labore tempore labore.', '2025-10-30', NULL, NULL, '2026-02-24 03:29:16', 'courriers/f52adbff-7a02-3c08-ac26-029bf10dd077.', 'itaque-earum-laboriosam-dolores-at-accusantium-et_20260412_0589.pdf', 'application/pdf', 232614, NULL, NULL, 38, 7, 1, '2026-04-17 22:52:35', '2026-04-17 22:52:35'),
(43, 'REF-1916-43', 'N°3773797', 1, 0, 2, 'Aspernatur illo rerum illo minima.', 'Libero quod voluptatem quos non perferendis blanditiis. Non vero voluptates vitae sed debitis voluptate qui. Quia nostrum dolores quia expedita cum beatae excepturi.', '2026-03-05', NULL, NULL, '2026-03-02 17:18:42', 'courriers/f12f3e9e-3b09-3cd5-91dc-9e00a02ca236.', 'est-distinctio-esse-nihil-possimus-unde-nulla-cumque_20260122_6190.pdf', 'application/pdf', 1375340, NULL, NULL, 39, 7, 1, '2026-04-17 22:52:35', '2026-04-17 22:52:35'),
(44, 'REF-0719-80', 'N°8694262', 2, 1, 2, 'Deserunt velit eum aut.', 'Sequi dicta voluptatum corporis non. Labore dolorum ut amet alias officia quae.', '2026-03-01', NULL, NULL, '2026-02-23 11:33:09', 'courriers/82e3db63-065a-36fd-b34f-49ed1e53cdcb.', 'perspiciatis-quia-eos-qui-perferendis-et-ratione_20260322_1203.pdf', 'application/pdf', 2302973, NULL, NULL, 40, 8, 1, '2026-04-17 22:52:35', '2026-04-17 22:52:35'),
(45, 'REF-4867-30', 'N°6997802', 1, 0, 2, 'Quisquam magnam optio id qui.', NULL, '2025-12-07', NULL, NULL, '2026-02-28 06:25:45', 'courriers/c1f37a86-01c5-3c09-a4ff-d2cb6650a364.', 'ea-distinctio-amet-nihil-quaerat-porro_20260327_2678.pdf', 'application/pdf', 4980733, NULL, NULL, 41, 7, 1, '2026-04-17 22:52:35', '2026-04-17 22:52:35'),
(46, 'REF-3086-92', 'N°1793223', 1, 1, 0, 'Autem dolor sunt qui.', 'Iusto id placeat tenetur suscipit expedita exercitationem corrupti. Sit voluptatem iusto maiores. Ut aut tempore voluptatum eos voluptatem qui qui. Omnis reiciendis quae dolor sunt incidunt.', '2026-01-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-04-17 22:52:35', '2026-04-17 22:52:35'),
(47, 'REF-0053-11', 'N°5327041', 0, 1, 0, 'Doloribus aut id hic.', 'Excepturi architecto quia facilis non molestiae veritatis quae. Quo assumenda laborum sed amet totam voluptate nihil quasi. Non et debitis corporis vel ut. Consequuntur reprehenderit nostrum labore ex.', '2026-02-28', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-04-17 22:52:35', '2026-04-17 22:52:35'),
(48, 'REF-8346-26', 'N°8745522', 2, 0, 0, 'Quo laudantium voluptatibus quia.', 'Molestias nobis eos reiciendis officiis non quia quia. Ex sapiente exercitationem autem officiis ipsum ipsum aut. Omnis dolores repellat ratione id facilis et ab.', '2025-10-26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-04-17 22:52:35', '2026-04-17 22:52:35'),
(49, 'REF-7635-95', 'N°8497634', 0, 1, 0, 'Consequatur accusantium natus quae illum.', 'Qui sit exercitationem aspernatur. Eveniet laboriosam quia aliquam. Quis et ea eaque est temporibus a officia. Fugiat tempore delectus enim dolor occaecati ea dolores. Nulla totam ipsam aliquam eveniet unde.', '2025-10-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-04-17 22:52:35', '2026-04-17 22:52:35'),
(50, 'REF-9615-57', 'N°7826389', 0, 0, 0, 'Harum neque voluptates distinctio vitae repellat.', 'Quis et sit et et ut expedita. Voluptas sed et minus officia. Enim beatae ex ratione et aliquid veritatis consequatur porro. A corporis eos earum eos placeat inventore.', '2025-10-30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, '2026-04-17 22:52:35', '2026-04-17 22:52:35'),
(51, 'MAR-PAI-00011661', 'tryyty', 0, 1, 0, 'test 2', 'testty', '2026-04-19', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 9, 0, '2026-04-19 01:59:18', '2026-04-21 15:42:17'),
(52, 'MAR-PAI-00011661', 'tryyty', 0, 1, 0, 'test 2', 'testty', '2026-04-19', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 9, 0, '2026-04-19 01:59:23', '2026-04-21 15:39:42'),
(53, 'MAR-PAI-00011661', 'tryyty', 0, 0, 0, 'test 2', NULL, '2026-04-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 9, 0, '2026-04-20 17:44:18', '2026-04-21 15:39:36'),
(54, 'MAR-PAI-00011661', 'tryyty', 0, 0, 0, 'test 2', NULL, '2026-04-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 9, 0, '2026-04-20 17:44:23', '2026-04-21 15:39:25'),
(55, 'MAR-PAI-00011661', 'tryyty', 0, 0, 0, 'test 2', NULL, '2026-04-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 9, 0, '2026-04-20 17:44:25', '2026-04-21 15:33:45'),
(56, 'MAR-PAI-00011661', 'tryyty', 0, 0, 0, 'test 2', NULL, '2026-04-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 9, 0, '2026-04-20 17:44:42', '2026-04-21 15:31:08'),
(57, 'MAR-PAI-000116665', '0003459', 0, 0, 0, 'OBJET TEST 1', NULL, '2026-04-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 9, 0, '2026-04-20 18:07:32', '2026-04-21 15:30:44'),
(58, 'MAR-PAI-000116665', '0003459', 0, 0, 0, 'OBJET TEST 1', NULL, '2026-04-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 9, 0, '2026-04-20 18:07:37', '2026-04-21 15:30:34'),
(59, 'MAR-PAI-000116665', '0003459', 0, 0, 0, 'OBJET TEST 1', NULL, '2026-04-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 9, 0, '2026-04-20 18:07:43', '2026-04-21 15:30:23'),
(60, 'MAR-PAI-000116689', '0000056788', 0, 0, 0, 'OBJET TEST JKLL', NULL, '2026-04-20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 9, 0, '2026-04-20 18:20:10', '2026-04-21 15:29:58'),
(61, 'MAR-PAI-000116698', '000345', 0, 2, 0, 'TEST DE CE MATIN', NULL, '2026-04-21', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 0, '2026-04-21 11:47:31', '2026-04-21 15:24:29'),
(62, 'MAR-PAI-00011668990000', '000005679890000', 0, 2, 1, 'testtt du produits', NULL, '2026-04-21', NULL, '2026-04-21 16:53:18', NULL, NULL, NULL, NULL, NULL, 5, 5, NULL, 9, 1, '2026-04-21 16:37:30', '2026-04-21 16:53:18'),
(63, 'MAR-PAI-0001166986788', NULL, 0, 1, 1, 'OBJET TEST JKLL', NULL, '2026-04-21', NULL, '2026-04-21 17:22:29', NULL, NULL, NULL, NULL, NULL, 4, 4, NULL, 9, 1, '2026-04-21 17:21:38', '2026-04-21 17:22:29'),
(64, 'MAR-PAI-00011668990009', '0003467', 0, 2, 1, 'TESTTTT bliblooooo', NULL, '2026-04-21', NULL, '2026-04-21 17:54:04', NULL, NULL, NULL, NULL, NULL, 3, 3, NULL, 4, 1, '2026-04-21 17:25:47', '2026-04-21 17:54:04');

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2026_04_17_000001_create_organisations_table', 1),
(6, '2026_04_17_000002_create_services_table', 1),
(7, '2026_04_17_000003_create_agents_table', 1),
(8, '2026_04_17_000004_create_courriers_table', 1);

-- --------------------------------------------------------

--
-- Structure de la table `organisations`
--

CREATE TABLE `organisations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL COMMENT 'Nom officiel de l''organisation',
  `sigle` varchar(255) DEFAULT NULL COMMENT 'Sigle ou acronyme (ex: MINFI, DGSN, UNESCO)',
  `type` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=externe, 1=interne, 2=gouvernementale, 3=privée, 4=ONG',
  `adresse` text DEFAULT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `contact_principal` varchar(255) DEFAULT NULL COMMENT 'Personne de contact principale',
  `etat` int(11) NOT NULL DEFAULT 1 COMMENT '1=actif, 2=inactif/supprimé',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `organisations`
--

INSERT INTO `organisations` (`id`, `nom`, `sigle`, `type`, `adresse`, `telephone`, `email`, `contact_principal`, `etat`, `created_at`, `updated_at`) VALUES
(1, 'Direction Générale de l\'Administration', 'DGA', 1, '88341 Christelle Trail Apt. 154\nDeshaunhaven, UT 71163', '+17634580833', 'znikolaus@example.com', NULL, 1, '2026-04-17 22:52:20', '2026-04-17 22:52:20'),
(2, 'Runolfsson LLC', 'MQS', 0, '74379 Kohler Lane Suite 028\nNorth Raphaelle, OR 20403', '1-859-962-3542', 'hkreiger@example.org', NULL, 1, '2026-04-17 22:52:20', '2026-04-17 22:52:20'),
(3, 'Johns, Corkery and Blick', 'TKD', 0, NULL, '1-484-239-2766', NULL, NULL, 1, '2026-04-17 22:52:20', '2026-04-17 22:52:20'),
(4, 'Howe LLC', 'FIH', 0, '53186 Daniel Park\nNorth Norma, ME 10134-4561', NULL, 'rosalind.daugherty@example.net', NULL, 1, '2026-04-17 22:52:20', '2026-04-17 22:52:20'),
(6, 'Gulgowski Inc', 'WQC', 0, '65029 Devin Ports\nNew Alvahfort, ID 59259', NULL, NULL, NULL, 1, '2026-04-17 22:52:20', '2026-04-17 22:52:20'),
(7, 'Skiles Inc', 'UMZ', 2, '2085 Parker Plain Apt. 907\nHodkiewiczport, HI 73206-2575', '+1-859-815-9187', 'august46@example.net', NULL, 1, '2026-04-17 22:52:20', '2026-04-17 22:52:20'),
(8, 'Kerluke, Stroman and Stoltenberg', 'DGD', 2, '281 Gianni Fall Suite 952\nSkylamouth, MT 63967', '1-916-532-3124', 'jfay@example.net', NULL, 1, '2026-04-17 22:52:20', '2026-04-17 22:52:20'),
(9, 'Bruen Group', 'YRJ', 2, '105 Elva Trail\nPourosfurt, OH 68874-9306', '(213) 214-4048', 'destany76@example.com', NULL, 1, '2026-04-17 22:52:20', '2026-04-17 22:52:20'),
(11, 'TOULASSI', 'PZV', 4, 'BP 20475', '+22890594677', 'aboukadani@gmail.com', NULL, 1, '2026-04-22 10:00:02', '2026-04-22 10:00:02');

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `services`
--

CREATE TABLE `services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL COMMENT 'Nom complet du service',
  `description` text DEFAULT NULL,
  `localisation` varchar(255) DEFAULT NULL COMMENT 'Bâtiment, étage, bureau',
  `telephone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `organisation_id` bigint(20) UNSIGNED DEFAULT NULL,
  `etat` int(11) NOT NULL DEFAULT 1 COMMENT '1=actif, 2=inactif/supprimé',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `services`
--

INSERT INTO `services` (`id`, `nom`, `description`, `localisation`, `telephone`, `email`, `organisation_id`, `etat`, `created_at`, `updated_at`) VALUES
(1, 'Ressources Humaines', NULL, NULL, NULL, NULL, 1, 1, '2026-04-17 22:52:20', '2026-04-17 22:52:20'),
(2, 'Courrier & Archives', NULL, NULL, NULL, NULL, 1, 1, '2026-04-17 22:52:20', '2026-04-17 22:52:20'),
(3, 'Informatique', NULL, NULL, NULL, NULL, 1, 1, '2026-04-17 22:52:20', '2026-04-17 22:52:20'),
(4, 'Juridique', NULL, NULL, NULL, NULL, 1, 1, '2026-04-17 22:52:20', '2026-04-17 22:52:20'),
(5, 'Finances', NULL, NULL, NULL, NULL, 1, 1, '2026-04-17 22:52:20', '2026-04-17 22:52:20'),
(6, 'Velit Eius', NULL, NULL, NULL, NULL, 7, 1, '2026-04-17 22:52:20', '2026-04-17 22:52:20'),
(7, 'Sit Saepe', NULL, NULL, NULL, NULL, 4, 1, '2026-04-17 22:52:20', '2026-04-17 22:52:20'),
(8, 'Sunt Cumque', NULL, NULL, NULL, NULL, NULL, 1, '2026-04-17 22:52:20', '2026-04-17 22:52:20'),
(9, 'Distinctio Est', NULL, NULL, NULL, NULL, NULL, 1, '2026-04-17 22:52:20', '2026-04-17 22:52:20'),
(10, 'Eaque Accusamus', NULL, NULL, NULL, NULL, 6, 1, '2026-04-17 22:52:20', '2026-04-17 22:52:20'),
(11, 'Facilis Animi', NULL, NULL, NULL, NULL, NULL, 1, '2026-04-17 22:52:20', '2026-04-17 22:52:20'),
(12, 'Ad Ex', NULL, NULL, NULL, NULL, 4, 1, '2026-04-17 22:52:20', '2026-04-17 22:52:20'),
(13, 'Ullam Enim', NULL, NULL, NULL, NULL, 7, 1, '2026-04-17 22:52:20', '2026-04-17 22:52:20'),
(14, 'Pariatur Et', NULL, NULL, NULL, NULL, 2, 1, '2026-04-17 22:52:20', '2026-04-17 22:52:20'),
(15, 'Non Officia', NULL, NULL, NULL, NULL, 7, 1, '2026-04-17 22:52:20', '2026-04-17 22:52:20');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Nom complet ou identifiant d''affichage',
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'agent' COMMENT 'admin, agent, secretaire, chef_service, super_admin',
  `etat` int(11) NOT NULL DEFAULT 1 COMMENT '1=actif, 2=suspendu/supprimé',
  `telephone` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL COMMENT 'Chemin relatif vers la photo de profil',
  `derniere_connexion` timestamp NULL DEFAULT NULL COMMENT 'Dernière authentification réussie',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `role`, `etat`, `telephone`, `avatar`, `derniere_connexion`, `created_at`, `updated_at`) VALUES
(1, 'Administrateur Système', 'admin@gestion.local', '2026-04-17 22:52:20', '$2y$12$DGAdX3brQUo5edu5Ia.uM.pGGUoaUzoUgrhdrPHmejdesKQF3ggxy', 'hsaNhyxaOQHhW9Y3bf4QvNAjw4koSw8y2Syt6P2v5aq3EqUQOUa0ZAORTJi0', 'admin', 1, NULL, NULL, '2026-04-22 12:07:41', '2026-04-17 22:52:20', '2026-04-22 12:07:41'),
(2, 'Sammie Boehm MD', 'ressources.humaines@dga.local', '2026-04-17 22:52:20', '$2y$12$pzXm3OV7x6RJaI54tAC14uv6fn4YF5srl0nj4RgQlpxt.bz3zQqiG', 'EnPPWkGGgF', 'chef_service', 1, NULL, NULL, NULL, '2026-04-17 22:52:21', '2026-04-17 22:52:21'),
(3, 'Ezequiel King', 'courrier.&.archives@dga.local', '2026-04-17 22:52:21', '$2y$12$VzhJnLR7CManJRvz4CxGIuQMGXnua9piLlvqs.HDzXuI48OpFzKFa', '3lOVk6aHMc', 'chef_service', 1, NULL, NULL, NULL, '2026-04-17 22:52:21', '2026-04-17 22:52:21'),
(4, 'Emma Trantow', 'informatique@dga.local', '2026-04-17 22:52:21', '$2y$12$a7C8qAIt4v56WkQmn6NzoOhwqChKXE2xH5BuG1sz8H5FTvVrdBLvK', 'lW6cuIfB2B', 'chef_service', 1, NULL, NULL, NULL, '2026-04-17 22:52:22', '2026-04-17 22:52:22'),
(5, 'Mr. Donavon Gerlach IV', 'juridique@dga.local', '2026-04-17 22:52:22', '$2y$12$b29bTbTvMsvuGf1jJ0oM0OUOUtbVlBrRH9DEobU1uglxN6Ku9EAii', 'AYHFVTgNiH', 'chef_service', 1, NULL, NULL, NULL, '2026-04-17 22:52:22', '2026-04-17 22:52:22'),
(6, 'Enola Predovic Sr.', 'finances@dga.local', '2026-04-17 22:52:22', '$2y$12$NeqLloQeEACCm5VP5DaDu.mrT1Gx4hde9h2Q85RnSVGg8diXilJNK', '4US6xVP36p', 'chef_service', 1, NULL, NULL, NULL, '2026-04-17 22:52:23', '2026-04-17 22:52:23'),
(7, 'Weldon Fahey', 'clinton19@example.com', '2026-04-17 22:52:23', '$2y$12$boonP7nB1JPdrymcNH2eTuBcSDNxHBsneiliR7LyEazCfMl.9m6Cu', 'pQNSy0U3Xk', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(8, 'Lois Lehner DVM', 'dlubowitz@example.net', '2026-04-17 22:52:23', '$2y$12$d/A.Wz37YNZW86YtBvXw6u/DUlNoIG3Y3wJSyulSynANuVkZWowzy', 'f1YqyF2Xho', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(9, 'Miracle Kirlin', 'gusikowski.leonard@example.com', '2026-04-17 22:52:24', '$2y$12$VawUUqa5XbUCO1dj8TBN2OciO2A59c0fUTi0vXVhNOVU7LphTHwaW', 'qT1RyR9c64', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(10, 'Dorthy Moen', 'breitenberg.idell@example.net', '2026-04-17 22:52:24', '$2y$12$9WjJ87Fo1Zr7ox3zH8FIMOTpwu.QI8xldBQT8RlXvYcqZUL0cBYAi', 'sfbeHhzGjR', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(11, 'Jarod Cormier', 'liza35@example.net', '2026-04-17 22:52:25', '$2y$12$b.5AbsHpsBKpevOQoL3O6ubMcWIFUkdFzyoo.vvIMDRoZloN2.KZO', 'CdJuvD64YQ', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(12, 'Vergie Stark', 'morar.alfreda@example.net', '2026-04-17 22:52:25', '$2y$12$P8TmnJuxkyRftQq83ZcN0e.44o.NnF/B/vXQW4Pb65nIB6wENNZDi', 'nEOeAUBA5t', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(13, 'Prof. Darien Schultz', 'lolita.christiansen@example.net', '2026-04-17 22:52:26', '$2y$12$kO3BTiwp9rnBDfA13xXmbuEEBvhBTP.Ei1H1PwW1OWacw3bRCo1EO', 'zYuVUQQBHE', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(14, 'Dr. Art Mueller', 'pietro08@example.com', '2026-04-17 22:52:26', '$2y$12$BkNpLNxMxrB7yNbLTOWmT.oAKL028JQb3Q/ZWNyj4wDk4v7yYiUc6', 'zlSJYDNSDT', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(15, 'Dr. Prince Nienow II', 'reynolds.preston@example.com', '2026-04-17 22:52:27', '$2y$12$d6ViYgqXHdx7rnlzYWO1CeNHRBJywF7VXsl2/MdvLIP7/19hvX3g2', '2RxeQZmy0h', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(16, 'Blaze Kozey', 'nigel84@example.net', '2026-04-17 22:52:27', '$2y$12$EmeoYu/ASuF44K2bpzA2lutw564dLgnGGQAFlmPPGZbpnzWbVylxW', 'j6mfp35GZc', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(17, 'Stefan Waelchi', 'herbert.ruecker@example.net', '2026-04-17 22:52:27', '$2y$12$I9jXLJQ4fF.a8jDkHqp0ruzt2gMAfmJioIIczqoaCRJU2/2rRsyUS', 'PiBrkm4jh8', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(18, 'Cruz Williamson', 'plarson@example.net', '2026-04-17 22:52:28', '$2y$12$d56T6vNM3qY63p9D1f4zPOsoDNHr72jV4jBh.n5rbSBNa1wSrQ0/y', '2HbOWWinlL', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(19, 'Dr. Emerson Hauck Jr.', 'bernier.sasha@example.org', '2026-04-17 22:52:28', '$2y$12$NHZ2KLivpzGC/68o1./RseSdxi91YPiMiye1a/HPk4GSGSmoiB0h6', 'fzWnmmH65Y', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(20, 'Camden Dooley', 'alanna.ratke@example.com', '2026-04-17 22:52:29', '$2y$12$xA/R4v6O0ZrUTKTtXv3KVuDycz7QCBqz9IY2DCW7G5RCwiefjtPEy', '56NgLoW5Kw', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(21, 'Dylan Hill', 'kari96@example.com', '2026-04-17 22:52:29', '$2y$12$je8Ifn2rJsd.Xhi5Gk8oCeC.smgSjJpsryi5imWw.tqYO6VgNL.wK', 'cvfjfDVyCt', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:30', '2026-04-17 22:52:30'),
(22, 'Baylee Schneider', 'dwolf@example.net', '2026-04-17 22:52:30', '$2y$12$JbBH8mUOK86HgMch75f/IOApcVjrAsRKItzFa/ZtJxXyA19Zw6k1W', 'qvroraeTmv', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:31', '2026-04-17 22:52:31'),
(23, 'Dustin Kassulke III', 'nat.stokes@example.org', '2026-04-17 22:52:31', '$2y$12$BM13.AjyIuid4VElGp6SQuOec8p9CVDtmm9wGUtKdO2cjr80MBe2G', 'QLAIEmN9xo', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:31', '2026-04-17 22:52:31'),
(24, 'Otilia Oberbrunner', 'mayer.destin@example.com', '2026-04-17 22:52:31', '$2y$12$BstL3VWg/l9eDgNNfXdquedS/lC.qUOHjfvg1mUejDiQRJwhnfGMO', 'yFlQJl9pk4', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:31', '2026-04-17 22:52:31'),
(25, 'Cathy Runolfsson', 'ahmad.muller@example.com', '2026-04-17 22:52:31', '$2y$12$Lb3apEL8alSNJyONenmepelYR2su5eXyi9uHCEOU5UswgMjhE08tG', 'fak2zxRoXu', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:31', '2026-04-17 22:52:31'),
(26, 'Mr. Curtis Kerluke', 'tavares49@example.com', '2026-04-17 22:52:31', '$2y$12$E/EFy7XkYwP7V4laeZBe5ecQJMZ7yYwGei2i8lZ6JikAXx/gGiz2O', 'HOMBCrShc9', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:32', '2026-04-17 22:52:32'),
(27, 'Krystel Collins', 'king.mollie@example.com', '2026-04-17 22:52:32', '$2y$12$wgkFMpfxA69EMIlIu0reu.4X9gTVVTArfYrrJt6v.Ib3jPFqOJSaS', 'nAz51aXIs6', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:32', '2026-04-17 22:52:32'),
(28, 'Dr. Janessa Connelly', 'nader.stella@example.net', '2026-04-17 22:52:32', '$2y$12$QtCedTG1H8V.Kmt0/0qJm.xNGZPGZ6rWyJ28TE0V1FkI9Sh9gNYDC', 'ZhpwNWJB7L', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:32', '2026-04-17 22:52:32'),
(29, 'Dr. Josh Zieme V', 'elliott84@example.org', '2026-04-17 22:52:32', '$2y$12$un61tXnW8QQCni4ST16y1OXCrrIaD64yy7Tbp7reAiAsAMAmac0Aq', '18Qn9bHt1o', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:32', '2026-04-17 22:52:32'),
(30, 'Melody McCullough DDS', 'qschmitt@example.com', '2026-04-17 22:52:32', '$2y$12$1Qc2lcMkSSmgaJCxVSDkLuAxw6zbQ23qiW3C.aiIrdRyFsczEwIee', 'Pv9hhigexq', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:33', '2026-04-17 22:52:33'),
(31, 'Mr. Jon Veum IV', 'mschimmel@example.org', '2026-04-17 22:52:33', '$2y$12$657mUWhMKbGYz.TKiPu0nOF35TBSZuJw2bVynK9vdppF8NDRC7022', 'Trf5iYFZt7', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:33', '2026-04-17 22:52:33'),
(32, 'Leone Cummerata', 'helga44@example.net', '2026-04-17 22:52:33', '$2y$12$/JedQVd68JIQ6dfHDvZ9kuUtCVmdEGE0hzZsozLaILVZG.UZEqXlu', 'CSYyYt0L8P', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:33', '2026-04-17 22:52:33'),
(33, 'Augustine Little', 'thill@example.org', '2026-04-17 22:52:33', '$2y$12$tMqAk.SjtSKuusa1NBsHIe0LarVLGSQINwMTvDpN8IOFd2LFbG7hG', 'Ovgf9LAYYm', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:33', '2026-04-17 22:52:33'),
(34, 'Margarita Farrell', 'czulauf@example.net', '2026-04-17 22:52:33', '$2y$12$Do2CgXlvmPOIcv5nuCO5EeasFXdi/c7HM5Rg1ByVMxg.BUrbq4oF2', 'UYNTDO0TZ0', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:34', '2026-04-17 22:52:34'),
(35, 'Dr. Janessa Frami V', 'ayla.hills@example.net', '2026-04-17 22:52:34', '$2y$12$CkqhCjN8APBqaxCi//TuAOXXs21iXnYvStvIdxGV1Ow5oWRI4rAZW', 'jJeMg6oOlB', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:34', '2026-04-17 22:52:34'),
(36, 'Jody Simonis', 'giuseppe92@example.net', '2026-04-17 22:52:34', '$2y$12$UYdxpTUmiJaHm7WTEmThI.ibrSb.Raf9kfPyQfGya7RT1rn2XLFPG', 'KYtUMlPvck', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:34', '2026-04-17 22:52:34'),
(37, 'Trudie Weber', 'wuckert.alexandrea@example.com', '2026-04-17 22:52:34', '$2y$12$k2ofxTErC.Qm6FKX5BGqwOiwCD7/2LHvu.ypaiYg2FXB5lWfNdaIG', 'odSbbsPabK', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:34', '2026-04-17 22:52:34'),
(38, 'Jacky Morar', 'nfriesen@example.org', '2026-04-17 22:52:34', '$2y$12$ltrll6uh/8ic0pGfJB978ubx0ZzXkYzTRr/XgaCHc0ql6dfTXNGey', 'y4svCgjf1d', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:35', '2026-04-17 22:52:35'),
(39, 'Elena Muller', 'sophie.gaylord@example.org', '2026-04-17 22:52:35', '$2y$12$DgS0mNsC1FVZ8Y/k541vD.0Jst8w.DzgpztWkgh1iTJKm/fe1C8QW', 'AjIbld77IP', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:35', '2026-04-17 22:52:35'),
(40, 'Joy Wolf', 'rogahn.fernando@example.com', '2026-04-17 22:52:35', '$2y$12$fG773HDiFnC2lTDlSAEfhevAcnlf/xn/iFKhkTCTxkOCWtoTDu3UK', 'wwa22M9wzx', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:35', '2026-04-17 22:52:35'),
(41, 'Leonora Reilly', 'mcclure.diana@example.net', '2026-04-17 22:52:35', '$2y$12$KiepZMirx09vacyDeiRFW.CcE3Ub1wRhC/hOXyX9Xnz77qhOj8Uky', 'azJglTXQQz', 'agent', 1, NULL, NULL, NULL, '2026-04-17 22:52:35', '2026-04-17 22:52:35');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `agents`
--
ALTER TABLE `agents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `agents_email_unique` (`email`),
  ADD KEY `agents_user_id_foreign` (`user_id`),
  ADD KEY `agents_service_id_etat_index` (`service_id`,`etat`),
  ADD KEY `agents_nom_prenom_index` (`nom`,`prenom`),
  ADD KEY `agents_etat_index` (`etat`);

--
-- Index pour la table `courriers`
--
ALTER TABLE `courriers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `courriers_agent_id_foreign` (`agent_id`),
  ADD KEY `courriers_utilisateur_id_foreign` (`utilisateur_id`),
  ADD KEY `courriers_organisation_id_foreign` (`organisation_id`),
  ADD KEY `courriers_service_id_statut_etat_index` (`service_id`,`statut`,`etat`),
  ADD KEY `courriers_date_reception_type_etat_index` (`date_reception`,`type`,`etat`),
  ADD KEY `courriers_reference_index` (`reference`),
  ADD KEY `courriers_numero_index` (`numero`),
  ADD KEY `courriers_date_reception_index` (`date_reception`),
  ADD KEY `courriers_etat_index` (`etat`);

--
-- Index pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `organisations`
--
ALTER TABLE `organisations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `organisations_sigle_unique` (`sigle`),
  ADD KEY `organisations_type_etat_index` (`type`,`etat`),
  ADD KEY `organisations_nom_index` (`nom`),
  ADD KEY `organisations_etat_index` (`etat`);

--
-- Index pour la table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Index pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Index pour la table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `services_organisation_id_etat_index` (`organisation_id`,`etat`),
  ADD KEY `services_nom_index` (`nom`),
  ADD KEY `services_etat_index` (`etat`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_index` (`role`),
  ADD KEY `users_etat_index` (`etat`),
  ADD KEY `users_derniere_connexion_index` (`derniere_connexion`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `agents`
--
ALTER TABLE `agents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT pour la table `courriers`
--
ALTER TABLE `courriers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `organisations`
--
ALTER TABLE `organisations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `agents`
--
ALTER TABLE `agents`
  ADD CONSTRAINT `agents_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `agents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `courriers`
--
ALTER TABLE `courriers`
  ADD CONSTRAINT `courriers_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `courriers_organisation_id_foreign` FOREIGN KEY (`organisation_id`) REFERENCES `organisations` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `courriers_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `courriers_utilisateur_id_foreign` FOREIGN KEY (`utilisateur_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_organisation_id_foreign` FOREIGN KEY (`organisation_id`) REFERENCES `organisations` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
