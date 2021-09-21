INFORMATIONS IMPORTANTES A LIRE AVANT INSTALLATION EN LOCAL DU PROJET

Variables d'environnement
*
*
Il vous faut définir les 2 variables d'environnement suivante dans votre
serveur local :

SetEnv HTTP_DATABASE_USER "root"
SetEnv HTTP_DATABASE_PWD ""

Ainsi, si votre configuration locale est basée sur les Virtual Host, votre
fichier de configuration devra contenir plus ou moins :

<VirtualHost *:80>
	ServerName extranetgroupebancaire
	DocumentRoot "c:............./p3-extranet groupe bancaire"
	SetEnv HTTP_DATABASE_USER "root"
	SetEnv HTTP_DATABASE_PWD ""
	<Directory  "c:............./p3-extranet groupe bancaire/">
		Options +Indexes +Includes +FollowSymLinks +MultiViews
		AllowOverride All
		Require local
	</Directory>
</VirtualHost>
