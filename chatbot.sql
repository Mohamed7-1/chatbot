/* cree une base donner nomme "bot" */
/* cree un tableau nomme "chatbot"  */

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `bot`
--

-- --------------------------------------------------------

--
-- Structure de la table `chatbot`
--

CREATE TABLE `chatbot` (
  `id` int(11) NOT NULL,
  `queries` varchar(300) NOT NULL,
  `replies` varchar(300) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `chatbot`
--


ALTER TABLE `chatbot` ADD `pricing` DECIMAL(10, 2) NOT NULL DEFAULT 0.00;
-- Supprime toutes les anciennes données
DELETE FROM `chatbot`;

-- Insère les nouvelles données avec les prix
INSERT INTO `chatbot` (`id`, `queries`, `replies`, `pricing`) VALUES
(1, 'What custom software development services do you offer?', 'We provide custom software solutions tailored to meet specific business needs.', 200.00),
(2, 'How can you assist with digitalization?', 'We help businesses transition to digital platforms efficiently and effectively.', 150.00),
(3, 'What digital marketing services are available?', 'Our digital marketing services include SEO, SEM, social media marketing, and more.', 250.00),
(4, 'Can you implement a CRM system for our business?', 'Yes, we specialize in CRM implementations to enhance customer relationship management.', 300.00),
(5, 'Do you offer GLPI services?', 'We provide GLPI solutions for IT asset management and helpdesk support.', 180.00),
(6, 'How do you create websites?', 'We design and develop websites tailored to business needs, ensuring a strong online presence.', 400.00),
(7, 'How can you help with social media management?', 'We manage and grow your social media presence on platforms like Facebook, LinkedIn, and Instagram.', 120.00),
(8, 'Do you develop Android and iOS applications?', 'Yes, we create custom Android and iOS applications to meet your business requirements.', 500.00),
(9, 'What digital consulting services do you offer?', 'Our digital consulting includes strategy development and e-branding advice.', 220.00),
(10, 'Can you assist with e-branding strategies?', 'We provide comprehensive e-branding strategies to enhance your online identity.', 130.00);

--
-- ndex pour les tables déchargéesI
--

--
-- Index pour la table `chatbot`
--
ALTER TABLE `chatbot`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `chatbot`
--
ALTER TABLE `chatbot`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;




/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
