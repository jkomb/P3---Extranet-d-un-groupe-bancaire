# Readme - Extranet d'un groupe bancaire

Vous trouverez ici toutes les informations utiles à l'installation du projet en local sur votre machine.

## INFORMATIONS IMPORTANTES A LIRE AVANT INSTALLATION EN LOCAL DU PROJET

**Variables d'environnement**

Il vous faut définir les 2 variables d'environnement suivante dans votre
serveur local :

HTTP_DATABASE_USER "root"
HTTP_DATABASE_PWD ""
HTTP_DATABASE_HOST "localhost"
HTTP_DATABASE_NAME "extranet"

Ainsi, si votre configuration locale est basée sur les Virtual Host, votre
fichier de configuration devra contenir plus ou moins :

<**VirtualHost *:80>
	ServerName extranetgroupebancaire
	DocumentRoot "c:............./p3-extranet groupe bancaire"
	SetEnv HTTP_DATABASE_USER "root"
	SetEnv HTTP_DATABASE_PWD ""
	SetEnv HTTP_DATABASE_LOCATION "localhost"
	SetEnv HTTP_DATABASE_HOST "host"
	SetEnv HTTP_DATABASE_NAME "extranet"
	<Directory  "c:../p3-extranet groupe bancaire/">
		Options +Indexes +Includes +FollowSymLinks +MultiViews
		AllowOverride All
		Require local
	</Director*>
</VirtualHost**>



## Création des tables

Vous trouverez ci-dessous l'ensemble des requêtes pour créer les tables (et leur contenu le cas échéant) nécessaires au fonctionnement du site :

```SQL
**SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


-- Structure de la table `accounts`

DROP TABLE IF EXISTS `accounts`;
CREATE TABLE IF NOT EXISTS `accounts` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `question` text NOT NULL,
  `reponse` text NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;


-- Structure de la table `admin`

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `id_user` int(11) NOT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- Structure de la table `acteurs`

DROP TABLE IF EXISTS `acteurs`;
CREATE TABLE IF NOT EXISTS `acteurs` (
  `id_acteur` int(11) NOT NULL AUTO_INCREMENT,
  `nom_acteur` varchar(20) NOT NULL,
  `description_courte` text NOT NULL,
  `description` text NOT NULL,
  `total_posts` int(11) NOT NULL DEFAULT '0',
  `total_likes` int(11) NOT NULL DEFAULT '0',
  `total_dislikes` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_acteur`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;


INSERT INTO `acteurs` (`id_acteur`, `nom_acteur`, `description_courte`, `description`, `logo`, `total_posts`, `total_likes`, `total_dislikes`) VALUES
(1, 'Formation&amp;co\r\n', 'Formation&co est une association française présente sur tout le territoire.', 'Formation&co est une association française présente sur tout le territoire.\r\n\r\nNous proposons à des personnes issues de tout milieu de devenir entrepreneur grâce à un crédit et un accompagnement professionnel et personnalisé.\r\n\r\nNotre proposition :\r\n\r\n- un financement jusqu’à 30 000€ ;\r\n\r\n- un suivi personnalisé et gratuit ;\r\n\r\n- une lutte acharnée contre les freins sociétaux et les stéréotypes.\r\n\r\nLe financement est possible, peu importe le métier : coiffeur, banquier, éleveur de chèvres…\r\n\r\nNous collaborons avec des personnes talentueuses et motivées.\r\n\r\nVous n’avez pas de diplômes ?\r\n\r\nCe n’est pas un problème pour nous ! Nos financements s’adressent à tous.\r\n', 'png', 0, 0, 0),
(2, 'Protectpeople\r\n', 'Protectpeople finance la solidarité nationale.', 'Protectpeople finance la solidarité nationale.\r\n\r\nNous appliquons le principe édifié par la Sécurité sociale française en 1945 : permettre à chacun de bénéficier d’une protection sociale.\r\n\r\nChez Protectpeople, chacun cotise selon ses moyens et reçoit selon ses besoins.\r\nProectecpeople est ouvert à tous, sans considération d’âge ou d’état de santé.\r\nNous garantissons un accès aux soins et une retraite.\r\n\r\nChaque année, nous collectons et répartissons 300 milliards d’euros.\r\n\r\nNotre mission est double :\r\n\r\n- sociale : nous garantissons la fiabilité des données sociales ;\r\n- économique : nous apportons une contribution aux activités économiques.\r\n', 'png', 0, 0, 0),
(3, 'Dsa France\r\n', 'Dsa France accélère la croissance du territoire et s’engage avec les collectivités territoriales.', 'Dsa France accélère la croissance du territoire et s’engage avec les collectivités territoriales.\r\n\r\nNous accompagnons les entreprises dans les étapes clés de leur évolution.\r\n\r\nNotre philosophie : s’adapter à chaque entreprise.\r\n\r\nNous les accompagnons pour voir plus grand et plus loin et proposons des solutions de financement adaptées à chaque étape de la vie des entreprises.\r\n', 'png', 0, 0, 0),
(4, 'CDE\r\n', 'La CDE (Chambre Des Entrepreneurs) accompagne les entreprises dans leurs démarches de formation. ', 'La CDE (Chambre Des Entrepreneurs) accompagne les entreprises dans leurs démarches de formation. \r\n\r\nSon président est élu pour 3 ans par ses pairs, chefs d’entreprises et présidents des CDE.\r\n', 'png', 0, 0, 0);


-- Structure de la table `posts`

DROP TABLE IF EXISTS `posts`;
CREATE TABLE IF NOT EXISTS `posts` (
  `id_post` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `id_acteur` int(11) NOT NULL,
  `date_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `post` text NOT NULL,
  PRIMARY KEY (`id_post`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

-- Structure de la table `vote`

DROP TABLE IF EXISTS `vote`;
CREATE TABLE IF NOT EXISTS `vote` (
  `indexation` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_user` int(11) NOT NULL,
  `id_acteur` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  PRIMARY KEY (`indexation`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
```

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
