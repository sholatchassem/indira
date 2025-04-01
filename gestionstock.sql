-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 01 avr. 2025 à 01:47
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestionstock`
--

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `nom`, `description`) VALUES
(1, 'Electronique', 'Nous offrons des produits de qualité'),
(2, 'Electrique', 'Produit de haute qualité');

-- --------------------------------------------------------

--
-- Structure de la table `demandes_acces`
--

CREATE TABLE `demandes_acces` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `demande` text NOT NULL,
  `statut` enum('en attente','approuvé','refusé') DEFAULT 'en attente',
  `date_demande` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `entrees`
--

CREATE TABLE `entrees` (
  `id` int(11) NOT NULL,
  `produit_id` int(11) DEFAULT NULL,
  `quantite` int(11) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `date_entree` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `entrees`
--

INSERT INTO `entrees` (`id`, `produit_id`, `quantite`, `prix`, `date_entree`) VALUES
(1, 4, 44, 2000.00, '2025-03-13 00:00:00'),
(2, 6, 2, 0.38, '2025-03-21 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `fournisseurs`
--

CREATE TABLE `fournisseurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `contact` varchar(100) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `fournisseurs`
--

INSERT INTO `fournisseurs` (`id`, `nom`, `contact`, `adresse`) VALUES
(1, 'ULRICH', '671012924', 'PK15'),
(2, 'Tchassem', '659220705', 'Institut Jean Paul II\r\nLOGBESSOU'),
(3, 'Tchassem', '659220705', 'Institut Jean Paul II\r\nLOGBESSOU'),
(4, 'Noubissi', '657999041', 'La rue après Bao');

-- --------------------------------------------------------

--
-- Structure de la table `inventaires`
--

CREATE TABLE `inventaires` (
  `id` int(11) NOT NULL,
  `produit_id` int(11) DEFAULT NULL,
  `quantite` int(11) NOT NULL,
  `quantite_theorique` int(11) DEFAULT NULL,
  `ecart` int(11) DEFAULT NULL,
  `date_inventaire` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `inventaires`
--

INSERT INTO `inventaires` (`id`, `produit_id`, `quantite`, `quantite_theorique`, `ecart`, `date_inventaire`) VALUES
(1, 3, 4, NULL, NULL, '2025-03-12 00:00:00'),
(2, 4, 30, NULL, NULL, '2025-03-13 00:00:00'),
(3, 4, 30, NULL, NULL, '2025-03-13 00:00:00'),
(4, 4, 64, NULL, NULL, '2025-03-13 00:00:00'),
(5, 4, 64, NULL, NULL, '2025-03-13 00:00:00'),
(6, 4, 20, NULL, NULL, '2025-03-13 00:00:00'),
(7, 4, 12, NULL, NULL, '2025-03-15 00:00:00'),
(8, 4, 10, 12, -2, '2025-03-16 00:00:00'),
(9, 4, 8, 10, -2, '2025-03-17 00:00:00'),
(10, 5, 25, 25, 0, '2025-03-19 00:00:00'),
(11, 5, 25, 25, 0, '2025-03-19 00:00:00'),
(12, 5, 25, 25, 0, '2025-03-19 00:00:00'),
(13, 5, 25, 25, 0, '2025-03-19 00:00:00'),
(14, 5, 25, 25, 0, '2025-03-19 00:00:00'),
(15, 5, 25, 25, 0, '2025-03-19 00:00:00'),
(16, 5, 25, 25, 0, '2025-03-19 00:00:00'),
(17, 5, 25, 25, 0, '2025-03-19 00:00:00'),
(18, 5, 25, 25, 0, '2025-03-19 00:00:00'),
(19, 5, 25, 25, 0, '2025-03-19 00:00:00'),
(20, 5, 25, 25, 0, '2025-03-19 00:00:00'),
(21, 5, 25, 25, 0, '2025-03-19 00:00:00'),
(22, 5, 25, 25, 0, '2025-03-19 00:00:00'),
(23, 5, 25, 25, 0, '2025-03-19 00:00:00'),
(24, 5, 25, 25, 0, '2025-03-19 00:00:00'),
(25, 5, 25, 25, 0, '2025-03-19 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

CREATE TABLE `produits` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `quantite` int(11) DEFAULT 0,
  `prix` decimal(10,2) NOT NULL,
  `marque` varchar(100) DEFAULT NULL,
  `fournisseur_id` int(11) DEFAULT NULL,
  `categorie_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id`, `nom`, `description`, `quantite`, `prix`, `marque`, `fournisseur_id`, `categorie_id`) VALUES
(3, 'cahier', 'qualite', 0, 2000.00, 'SAFCA', 1, NULL),
(4, 'Camera de surveillance', 'QUALITE', 8, 20000.00, 'CIAM', 1, 2),
(5, 'Groupe electrogene', 'Tres puissante et une garantie de plusieurs jours', 25, 50000.00, 'SDMO', 3, 1),
(6, 'Groupe electrogene', 'Tres puissante et une garantie de plusieurs jours', 27, 50000.00, 'SDMO', 3, 1),
(7, 'Onduleur', 'Nous offrons des produits de qualité', 25, 50000.00, 'CISCO', 4, 2),
(8, 'Regulateur de tension', 'produit', 20, 10000.00, 'SDMO', 2, 2),
(11, 'climaisaseur', 'avec une garantie assuré et une consommation reduite', 20, 54000.00, 'cisco', 2, 1);

-- --------------------------------------------------------

--
-- Structure de la table `sorties`
--

CREATE TABLE `sorties` (
  `id` int(11) NOT NULL,
  `produit_id` int(11) DEFAULT NULL,
  `quantite` int(11) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `date_sortie` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `sorties`
--

INSERT INTO `sorties` (`id`, `produit_id`, `quantite`, `prix`, `date_sortie`) VALUES
(1, 3, 4, 2000.00, '2025-03-12 00:00:00'),
(2, 4, 40, 2000.00, '2025-03-13 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` varchar(50) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `mot_de_passe` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `email`, `role`, `active`, `mot_de_passe`) VALUES
(1, 'Tchassem Indira', 'Tchassem.indira@email.com', 'administrateur', 1, 'shola 1234'),
(2, 'claude Waffo', 'claude.waffo@email.com', 'magasinier', 0, 'claude-waffo');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `demandes_acces`
--
ALTER TABLE `demandes_acces`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `entrees`
--
ALTER TABLE `entrees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produit_id` (`produit_id`);

--
-- Index pour la table `fournisseurs`
--
ALTER TABLE `fournisseurs`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `inventaires`
--
ALTER TABLE `inventaires`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produit_id` (`produit_id`);

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fournisseur_id` (`fournisseur_id`),
  ADD KEY `categorie_id` (`categorie_id`);

--
-- Index pour la table `sorties`
--
ALTER TABLE `sorties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produit_id` (`produit_id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `demandes_acces`
--
ALTER TABLE `demandes_acces`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `entrees`
--
ALTER TABLE `entrees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `fournisseurs`
--
ALTER TABLE `fournisseurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `inventaires`
--
ALTER TABLE `inventaires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `sorties`
--
ALTER TABLE `sorties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `demandes_acces`
--
ALTER TABLE `demandes_acces`
  ADD CONSTRAINT `demandes_acces_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`);

--
-- Contraintes pour la table `entrees`
--
ALTER TABLE `entrees`
  ADD CONSTRAINT `entrees_ibfk_1` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`);

--
-- Contraintes pour la table `inventaires`
--
ALTER TABLE `inventaires`
  ADD CONSTRAINT `inventaires_ibfk_1` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`);

--
-- Contraintes pour la table `produits`
--
ALTER TABLE `produits`
  ADD CONSTRAINT `produits_ibfk_1` FOREIGN KEY (`fournisseur_id`) REFERENCES `fournisseurs` (`id`),
  ADD CONSTRAINT `produits_ibfk_2` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`);

--
-- Contraintes pour la table `sorties`
--
ALTER TABLE `sorties`
  ADD CONSTRAINT `sorties_ibfk_1` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
