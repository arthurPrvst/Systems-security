-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Lun 04 Juin 2018 à 10:10
-- Version du serveur :  10.1.16-MariaDB
-- Version de PHP :  5.6.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `authentificationModule`
--

-- --------------------------------------------------------

--
-- Structure de la table `bruteforce_policy`
--

CREATE TABLE `bruteforce_policy` (
  `delay` int(11) NOT NULL,
  `lockingAccount` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `bruteforce_policy`
--

INSERT INTO `bruteforce_policy` (`delay`, `lockingAccount`) VALUES
(0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `clientsaffaires`
--

CREATE TABLE `clientsaffaires` (
  `id` int(11) NOT NULL,
  `name` varchar(400) NOT NULL,
  `picture` varchar(400) NOT NULL,
  `bio` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `clientsaffaires`
--

INSERT INTO `clientsaffaires` (`id`, `name`, `picture`, `bio`) VALUES
(1, 'JMA', '../images/1.jpg', 'MA BIO EST VIDE TABARNAK'),
(2, 'Jean Eustache', '../images/2.jpg', 'LORUM IPSUS TABARNAK'),
(3, 'Patrick', '../images/3.jpg', 'La sécurité c''est bien, dormir c''est mieux ! ');

-- --------------------------------------------------------

--
-- Structure de la table `clientsresidentiels`
--

CREATE TABLE `clientsresidentiels` (
  `id` int(11) NOT NULL,
  `name` varchar(400) NOT NULL,
  `picture` varchar(400) NOT NULL,
  `bio` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `clientsresidentiels`
--

INSERT INTO `clientsresidentiels` (`id`, `name`, `picture`, `bio`) VALUES
(1, 'Madeleine', '../images/madeleine.jpg', 'Je suis une Madeleine. '),
(2, 'Pistache', '../images/pistache.jpg', 'Et moi je suis une pistache. '),
(3, 'Cacahuètes', '../images/cacahuete.jpg', 'Coucou la cacahuète ! ');

-- --------------------------------------------------------

--
-- Structure de la table `password_management`
--

CREATE TABLE `password_management` (
  `forgotten` int(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `password_management`
--

INSERT INTO `password_management` (`forgotten`) VALUES
(1);

-- --------------------------------------------------------

--
-- Structure de la table `password_policy`
--

CREATE TABLE `password_policy` (
  `minLength` int(11) NOT NULL DEFAULT '7',
  `specialCharacter` tinyint(1) NOT NULL,
  `number` tinyint(1) NOT NULL,
  `lowerAndUpper` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `password_policy`
--

INSERT INTO `password_policy` (`minLength`, `specialCharacter`, `number`, `lowerAndUpper`) VALUES
(10, 1, 1, 0);

-- --------------------------------------------------------

--
-- Structure de la table `permissions`
--

CREATE TABLE `permissions` (
  `perm_id` int(10) UNSIGNED NOT NULL,
  `perm_desc` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `permissions`
--

INSERT INTO `permissions` (`perm_id`, `perm_desc`) VALUES
(2, 'displayBusinessContent'),
(1, 'displayResidentialContent'),
(0, 'displayAdminContent');

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(0, 'admin'),
(1, 'resclient'),
(2, 'busclient');

-- --------------------------------------------------------

--
-- Structure de la table `role_perm`
--

CREATE TABLE `role_perm` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `perm_id` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `role_perm`
--

INSERT INTO `role_perm` (`role_id`, `perm_id`) VALUES
(0, 0),
(0, 1),
(0, 2),
(1, 1),
(2, 2);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `login` varchar(30) NOT NULL,
  `pwd` longtext NOT NULL,
  `name` varchar(60) NOT NULL,
  `surname` varchar(60) NOT NULL,
  `mail` varchar(100) NOT NULL,
  `codepermanent` int(11) NOT NULL,
  `secret` varchar(20) DEFAULT NULL,
  `lastConnectionOrAttempt` int(11) NOT NULL,
  `failedAttempt` int(11) NOT NULL,
  `banned` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`login`, `pwd`, `name`, `surname`, `mail`, `codepermanent`, `secret`, `lastConnectionOrAttempt`, `failedAttempt`, `banned`) VALUES
('admin', '$2y$12$vxXfhHScZzfu2QlnMBWnQ.z5u2yFvV0RrESn9flLcf/UKXO579LzW', 'Admin', 'Admin', 'admin@admin.a', 15, 'systemAdmin', 1523313342, 2, 0),
('Business', '$2y$12$RqFZy.5Wf69c/9vwnQZH0.Ar6PJHOxOZI5sOTAfMG3.AXC5.DUyKq', 'Business', 'Business', 'Business@Business.Business', 2, 'professeur', 1523307494, 0, 0),
('Resident', '$2y$12$kmaEimOLjLYwqWJkHnkbI.9TAQKC7gQ1Am3ar2JAg.wQU2ihw7Zp2', 'Resident', 'Resident', 'Resident@Resident.Resident', 1, 'secretaire', 1523313027, 0, 0),
('test', '$2y$12$qPpIbFjNtyxEPrcnIY3B8ufrC2t8yM88wTkHm9YQpbP57Cw8HAa6G', 'test', 'test', 'test@test.test', 4, 'bl', 1523313232, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `user_role`
--

CREATE TABLE `user_role` (
  `user_id` varchar(30) NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `user_role`
--

INSERT INTO `user_role` (`user_id`, `role_id`) VALUES
('Business', 2),
('Resident', 1),
('admin', 0),
('test', 1);

--
-- Index pour les tables exportées
--

--
-- Index pour la table `bruteforce_policy`
--
ALTER TABLE `bruteforce_policy`
  ADD PRIMARY KEY (`delay`);

--
-- Index pour la table `clientsaffaires`
--
ALTER TABLE `clientsaffaires`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `clientsresidentiels`
--
ALTER TABLE `clientsresidentiels`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `password_management`
--
ALTER TABLE `password_management`
  ADD PRIMARY KEY (`forgotten`);

--
-- Index pour la table `password_policy`
--
ALTER TABLE `password_policy`
  ADD PRIMARY KEY (`minLength`);

--
-- Index pour la table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`perm_id`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Index pour la table `role_perm`
--
ALTER TABLE `role_perm`
  ADD KEY `role_id` (`role_id`),
  ADD KEY `perm_id` (`perm_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`login`);

--
-- Index pour la table `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `clientsaffaires`
--
ALTER TABLE `clientsaffaires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `clientsresidentiels`
--
ALTER TABLE `clientsresidentiels`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `perm_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
