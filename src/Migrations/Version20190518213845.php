<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190518213845 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('
     CREATE TABLE amap_credit_basket_amount (credit_id INT NOT NULL, basket_id INT NOT NULL, amount NUMERIC(10, 2) DEFAULT NULL, INDEX IDX_F0084B05CE062FF9 (credit_id), INDEX IDX_F0084B051BE1FB52 (basket_id), PRIMARY KEY(credit_id, basket_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
     CREATE TABLE amap_user (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, portfolio_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, broadcast_list LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', user_order INT DEFAULT NULL, color VARCHAR(255) DEFAULT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, zip_code INT DEFAULT NULL, phone_number VARCHAR(255) DEFAULT NULL, password VARCHAR(64) NOT NULL, is_active TINYINT(1) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', deleveries LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_D6882FFCE7927C74 (email), INDEX IDX_D6882FFC727ACA70 (parent_id), UNIQUE INDEX UNIQ_D6882FFCB96B5643 (portfolio_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
     CREATE TABLE amap_availability_schedule (id INT AUTO_INCREMENT NOT NULL, planning_id INT DEFAULT NULL, member_id INT DEFAULT NULL, INDEX IDX_C0266A613D865311 (planning_id), INDEX IDX_C0266A617597D3FE (member_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
     CREATE TABLE amap_product_quantity (product_id INT NOT NULL, basket_id INT NOT NULL, price NUMERIC(10, 2) DEFAULT NULL, quantity INT NOT NULL, INDEX IDX_65C9AE084584665A (product_id), INDEX IDX_65C9AE081BE1FB52 (basket_id), PRIMARY KEY(product_id, basket_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
     CREATE TABLE amap_planning (id INT AUTO_INCREMENT NOT NULL, state INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
     CREATE TABLE amap_document (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, role VARCHAR(255) DEFAULT NULL, text LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
     CREATE TABLE amap_availability_schedule_element (id INT AUTO_INCREMENT NOT NULL, availability_schedule_id INT DEFAULT NULL, is_available TINYINT(1) NOT NULL, date DATE NOT NULL, INDEX IDX_3E6F023AC87F2A74 (availability_schedule_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
     CREATE TABLE amap_portfolio (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
     CREATE TABLE amap_credit (id INT AUTO_INCREMENT NOT NULL, producer_id INT DEFAULT NULL, member_id INT DEFAULT NULL, date DATE NOT NULL, total_amount NUMERIC(10, 2) NOT NULL, current_amount NUMERIC(10, 2) NOT NULL, object VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_411225A089B658FE (producer_id), INDEX IDX_411225A07597D3FE (member_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
     CREATE TABLE amap_product (id INT AUTO_INCREMENT NOT NULL, producer_id INT DEFAULT NULL, portfolio_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, product_order INT DEFAULT NULL, price NUMERIC(10, 2) NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_5FC4AB1589B658FE (producer_id), UNIQUE INDEX UNIQ_5FC4AB15B96B5643 (portfolio_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
     CREATE TABLE amap_basket (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, date DATE NOT NULL, is_frozen TINYINT(1) NOT NULL, INDEX IDX_7F951B25A76ED395 (user_id), INDEX IDX_7F951B25727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
     CREATE TABLE amap_thumbnail (id INT AUTO_INCREMENT NOT NULL, portfolio_id INT DEFAULT NULL, media VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_A92230A1B96B5643 (portfolio_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
     CREATE TABLE amap_planning_element (id INT AUTO_INCREMENT NOT NULL, planning_id INT NOT NULL, date DATE NOT NULL, INDEX IDX_ADA7D17B3D865311 (planning_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
     CREATE TABLE amap_planning_element_user (planning_element_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_7C2203186BE2B44 (planning_element_id), INDEX IDX_7C22031A76ED395 (user_id), PRIMARY KEY(planning_element_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;
     ALTER TABLE amap_credit_basket_amount ADD CONSTRAINT FK_F0084B05CE062FF9 FOREIGN KEY (credit_id) REFERENCES amap_credit (id);
     ALTER TABLE amap_credit_basket_amount ADD CONSTRAINT FK_F0084B051BE1FB52 FOREIGN KEY (basket_id) REFERENCES amap_basket (id);
     ALTER TABLE amap_user ADD CONSTRAINT FK_D6882FFC727ACA70 FOREIGN KEY (parent_id) REFERENCES amap_user (id);
     ALTER TABLE amap_user ADD CONSTRAINT FK_D6882FFCB96B5643 FOREIGN KEY (portfolio_id) REFERENCES amap_portfolio (id);
     ALTER TABLE amap_availability_schedule ADD CONSTRAINT FK_C0266A613D865311 FOREIGN KEY (planning_id) REFERENCES amap_planning (id);
     ALTER TABLE amap_availability_schedule ADD CONSTRAINT FK_C0266A617597D3FE FOREIGN KEY (member_id) REFERENCES amap_user (id);
     ALTER TABLE amap_product_quantity ADD CONSTRAINT FK_65C9AE084584665A FOREIGN KEY (product_id) REFERENCES amap_product (id);
     ALTER TABLE amap_product_quantity ADD CONSTRAINT FK_65C9AE081BE1FB52 FOREIGN KEY (basket_id) REFERENCES amap_basket (id);
     ALTER TABLE amap_availability_schedule_element ADD CONSTRAINT FK_3E6F023AC87F2A74 FOREIGN KEY (availability_schedule_id) REFERENCES amap_availability_schedule (id);
     ALTER TABLE amap_credit ADD CONSTRAINT FK_411225A089B658FE FOREIGN KEY (producer_id) REFERENCES amap_user (id);
     ALTER TABLE amap_credit ADD CONSTRAINT FK_411225A07597D3FE FOREIGN KEY (member_id) REFERENCES amap_user (id);
     ALTER TABLE amap_product ADD CONSTRAINT FK_5FC4AB1589B658FE FOREIGN KEY (producer_id) REFERENCES amap_user (id);
     ALTER TABLE amap_product ADD CONSTRAINT FK_5FC4AB15B96B5643 FOREIGN KEY (portfolio_id) REFERENCES amap_portfolio (id);
     ALTER TABLE amap_basket ADD CONSTRAINT FK_7F951B25A76ED395 FOREIGN KEY (user_id) REFERENCES amap_user (id);
     ALTER TABLE amap_basket ADD CONSTRAINT FK_7F951B25727ACA70 FOREIGN KEY (parent_id) REFERENCES amap_basket (id);
     ALTER TABLE amap_thumbnail ADD CONSTRAINT FK_A92230A1B96B5643 FOREIGN KEY (portfolio_id) REFERENCES amap_portfolio (id);
     ALTER TABLE amap_planning_element ADD CONSTRAINT FK_ADA7D17B3D865311 FOREIGN KEY (planning_id) REFERENCES amap_planning (id);
     ALTER TABLE amap_planning_element_user ADD CONSTRAINT FK_7C2203186BE2B44 FOREIGN KEY (planning_element_id) REFERENCES amap_planning_element (id) ON DELETE CASCADE;
     ALTER TABLE amap_planning_element_user ADD CONSTRAINT FK_7C22031A76ED395 FOREIGN KEY (user_id) REFERENCES amap_user (id) ON DELETE CASCADE;
    INSERT INTO `amap_document` (`id`, `name`, `role`, `text`) VALUES
    (1, \'homepage\', \'ROLE_ADMIN\', \'<ul style=\"box-sizing: border-box; margin-top: 0px; margin-bottom: 1rem; color: #212529; font-family: -apple-system, BlinkMacSystemFont, \\\'Segoe UI\\\', Roboto, \\\'Helvetica Neue\\\', Arial, sans-serif, \\\'Apple Color Emoji\\\', \\\'Segoe UI Emoji\\\', \\\'Segoe UI Symbol\\\'; font-size: 16px;\">\r\n<li style=\"box-sizing: border-box;\">Tous les &eacute;l&eacute;ments cr&eacute;&eacute;s (producteur, r&eacute;f&eacute;rent, consom\\\'acteur, produit, avoir) sont initialement dans l\\\'&eacute;tat inactif.</li>\r\n<li style=\"box-sizing: border-box;\">D&eacute;sactiver un producteur d&eacute;sactive tous ses produits.</li>\r\n<li style=\"box-sizing: border-box;\">R&eacute;activer un producteur r&eacute;active ses produits.</li>\r\n<li style=\"box-sizing: border-box;\">D&eacute;sactiver un produit retire le produit de tous les mod&egrave;les ouverts et de tous les paniers ouverts.</li>\r\n<li style=\"box-sizing: border-box;\">R&eacute;activer un produit l\\\'ajoute dans les mod & egrave;les ouverts sans l\\\'activer dans les paniers.</li>\r\n<li style=\"box-sizing: border-box;\">R&eacute;activer un produit n\\\'est possible que si son producteur est activ & eacute;.</li > \r\n<li style = \"box-sizing: border-box;\">Cloturer un mod&egrave;le retire l\\\'entr&eacute;e correspondante des paniers.</li>\r\n<li style=\"box-sizing: border-box;\">R&eacute;ouvrir un mod&egrave;le r&eacute;active l\\\'entr&eacute;e correspondante dans tous les paniers.</li>\r\n<li style=\"box-sizing: border-box;\">Cloturer un mod&egrave;le fixe tous les &eacute;l&eacute;ments correspondants dans les paniers, liste de produits, prix des produits...</li>\r\n<li style=\"box-sizing: border-box;\">Les mod&egrave;les clotur&eacute;s ne peuvent plus &ecirc;tre &eacute;dit&eacute;s, seulement consult&eacute;s.</li>\r\n<li style=\"box-sizing: border-box;\">Un bouton de relance par mail est disponible si au moins un utilisateur n\\\'a pas valid&eacute; ses paniers.</li>\r\n<li style=\"box-sizing: border-box;\">Seul l\\\'administrateur peut activer ou d&eacute;sactiver le droit d\\\'&ecirc;tre consom\\\'acteur.</li>\r\n<li style=\"box-sizing: border-box;\">Un consom\\\'acteur d&eacute;sactiv&eacute; n\\\'a plus acc&egrave;s &agrave; l\\\'interface.</li>\r\n<li style=\"box-sizing: border-box;\">Un r&eacute;f&eacute;rent d&eacute;sactiv&eacute; n\\\'a plus acc&egrave;s &agrave; ses producteurs mais toujours &agrave; son panier.</li>\r\n<li style=\"box-sizing: border-box;\">Seuls l\\\'administrateur et les r&eacute;f&eacute;rents peuvent changer le prix d\\\'un produit actif.</li>\r\n<li style=\"box-sizing: border-box;\">Pour retirer l\\\'acc&egrave;s au panier &agrave; un producteur ou &agrave; un r&eacute;f&eacute;rent, il faut modifier son profil.</li>\r\n<li style=\"box-sizing: border-box;\">Un avoir doit &ecirc;tre activ&eacute; pour &ecirc;tre automatiquement pris en compte.</li>\r\n<li style=\"box-sizing: border-box;\">Un avoir activ&eacute; ne peut plus &ecirc;tre ni modifi&eacute;, ni d&eacute;sactiv&eacute;.</li>\r\n</ul>\'),
    (2, \'homepage\', \'ROLE_REFERENT\', \'<ul style=\"box-sizing: border-box; margin-top: 0px; margin-bottom: 1rem; color: #212529; font-family: -apple-system, BlinkMacSystemFont, \\\'Segoe UI\\\', Roboto, \\\'Helvetica Neue\\\', Arial, sans-serif, \\\'Apple Color Emoji\\\', \\\'Segoe UI Emoji\\\', \\\'Segoe UI Symbol\\\'; font-size: 16px;\">\r\n<li style=\"box-sizing: border-box;\">Tous les &eacute;l&eacute;ments cr&eacute;&eacute;s (producteur, produit) sont initialement dans l\\\'&eacute;tat inactif.</li>\r\n<li style=\"box-sizing: border-box;\">D&eacute;sactiver un producteur d&eacute;sactive tous ses produits.</li>\r\n<li style=\"box-sizing: border-box;\">R&eacute;activer un producteur r&eacute;active ses produits.</li>\r\n<li style=\"box-sizing: border-box;\">D&eacute;sactiver un produit retire le produit de tous les mod&egrave;les ouverts et de tous les paniers ouverts.</li>\r\n<li style=\"box-sizing: border-box;\">R&eacute;activer un produit l\\\'ajoute dans les mod&egrave;les ouverts sans l\\\'activer dans les paniers.</li>\r\n<li style=\"box-sizing: border-box;\">R&eacute;activer un produit n\\\'est possible que si son producteur est activ&eacute;.</li>\r\n<li style=\"box-sizing: border-box;\">Seuls l\\\'administrateur et les r&eacute;f&eacute;rents peuvent changer le prix d\\\'un produit actif.</li>\r\n</ul>\'),
    (3, \'homepage\', \'ROLE_PRODUCER\', \'<ul style=\"box-sizing: border-box; margin-top: 0px; margin-bottom: 1rem; color: #212529; font-family: -apple-system, BlinkMacSystemFont, \\\'Segoe UI\\\', Roboto, \\\'Helvetica Neue\\\', Arial, sans-serif, \\\'Apple Color Emoji\\\', \\\'Segoe UI Emoji\\\', \\\'Segoe UI Symbol\\\'; font-size: 16px;\">\r\n<li style=\"box-sizing: border-box;\">Seul les producteurs peuvent ajouter des images aux produits et remplir leur descriptif.</li>\r\n<li style=\"box-sizing: border-box;\"><span>Les producteurs ne peuvent changer les prix que des produits inactifs.</span></li>\r\n<li style=\"box-sizing: border-box;\"><span>Pour changer le prix d\\\'un produit actif, le producteur doit s\\\'adresser &agrave; l\\\'administrateur ou &agrave; son r&eacute;f&eacute;rent.</span></li>\r\n<li style=\"box-sizing: border-box;\"><span>Les producteurs peuvent rentrer de nouveaux produits et leur prix.</span></li>\r\n<li style=\"box-sizing: border-box;\"><span>Les nouveaux produits saisis par les producteurs sont dans l\\\'&eacute;tat inactif et doivent &ecirc;tre activ&eacute;s par l\\\'administrateur ou le r&eacute;f&eacute;rent.</span></li>\r\n</ul>\'),
    (4, \'homepage\', \'ROLE_MEMBER\', \'<h1 style=\"text-align: center;\">Bienvenue sur votre service de saisie des commandes</h1>\r\n<h1 style=\"text-align: center;\">AMAP Hommes de terre</h1>\'),
    (5, \'rules\', NULL, \'<h1>AMAP Hommes de Terre</h1>\r\n<h2>Commande au mois</h2>\r\n<p>Libre pas d&rsquo;obligation mais \"engagement moral\" &agrave; commander le plus r&eacute;guli&egrave;rement possible avec une souplesse bien s&ucirc;r pour les vacances de chaque famille!</p>\r\n<p>Les produits sont livr&eacute;s tous les jeudis (4 ou 5 par mois), les commandes peuvent &ecirc;tre diff&eacute;rentes d\\\'une semaine &agrave; l\\\'autre.</p>\r\n<p>Vous recevez un mail en 2&egrave;me quinzaine de mois, pour vous pr&eacute;venir que la commande du mois suivant est disponible sur l\\\'interface et que vous pouvez saisir la v&ocirc;tre.</p>\r\n<p>Commande &agrave; <strong>valider l&rsquo;avant derni&egrave;re semaine du mois pour le mois suivant. </strong>Si pas de r&eacute;ponse avant date butoir : Pas de commande enregistr&eacute;e.</p>\r\n<p>Merci d&rsquo;&ecirc;tre rigoureux dans la validation de la commande avant la date limite.</p>\r\n<p>Les commandes de produits canard/oie chez les Storez, le miel d&rsquo;Augustin, le jus de pommes, les produits La Patte JeanJean, les Pont l\\\'Ev&ecirc;que: uniquement la deuxi&egrave;me semaine de chaque mois.</p>\r\n<p>Les commandes de Spiruline chez Hyes, la bi&egrave;re de chez Gris\\\'mouss: uniquement la 4&egrave;me semaine de chaque mois.</p>\r\n<p>Les poulets d\\\' Olivier Brifaut et les produits de Gaylord (beurre, cr&egrave;me, fromage blanc): uniquement les 2&egrave;mes et 4&egrave;mes jeudis de chaque mois.</p>\r\n<p>Les commandes de pizzas : uniquement les semaines paires du calendrier.</p>\r\n<h2>R&egrave;glement et Paiement</h2>\r\n<p><strong>Adh&eacute;sion annuelle 10 euros</strong>&nbsp;&agrave; r&eacute;gler au premier trimestre ou &agrave; l&rsquo;arriv&eacute;e en liquide.</p>\r\n<p><strong>Paiement des producteurs: &agrave; d&eacute;poser le dernier jeudi du mois pr&eacute;c&eacute;dent</strong> <strong>la commande, au local dans chaque case des producteurs</strong> (ex : fin mai pour juin)</p>\r\n<p>Vous recevez par mail un r&eacute;capitulatif des commandes, et c&rsquo;est sur ce r&eacute;capitulatif qu&rsquo;il faut vous baser pour faire vos r&egrave;glements aux producteurs, car y sont &eacute;galement int&eacute;gr&eacute;s des avoirs quand il manque des produits sur votre commande et que vous l&rsquo;avez signal&eacute;.</p>\r\n<p>Ce r&eacute;capitulatif est &eacute;galement imprim&eacute; et d&eacute;pos&eacute; au local AMAP pour ceux qui font leur r&egrave;glement directement l&agrave;-bas.</p>\r\n<p>Un paiement par producteur en monnaie ou par ch&egrave;que (conseill&eacute;) &agrave; d&eacute;poser dans les cases des producteurs au local.</p>\r\n<p style=\"padding-left: 40px;\">Monnaie : l&rsquo;appoint sous enveloppe<strong>&nbsp;avec le mois not&eacute;, le nom de la famille, le nom du producteur et la somme</strong>.</p>\r\n<p style=\"padding-left: 40px;\">Ch&egrave;que: Bien respecter les d&eacute;nominations des producteurs qui figurent sur votre BDC pour remplir l\\\'ordre des ch&egrave;ques.</p>\r\n<p><strong>Les paiements pour les poulets d\\\'Olivier Brifaut sont diff&eacute;r&eacute;s</strong>. Il y a une &eacute;tiquette avec le prix sur chaque paquet. Chacun fait son total et d&eacute;pose son ch&egrave;que dans la case d\\\'Olvier Brifaut au local en fin de mois, en pr&eacute;cisant au dos du ch&egrave;que \"poulets\" pour les distinguer des ch&egrave;ques des oeufs qui eux doivent &ecirc;tre d&eacute;pos&eacute;s le dernier jeudi du M-1.</p>\r\n<p>Si litige de paiement merci de contacter le r&eacute;f&eacute;rent concern&eacute;.</p>\r\n<p>Si il vous manque un produit &agrave; la livraison, vous pouvez demander &agrave; Sylvain de vous enregistrer un avoir sur le mois suivant.</p>\r\n<h2>Absence</h2>\r\n<p>Si absence, envoyer<strong>&nbsp;un message le mois d&rsquo;avant</strong>&nbsp;pour pr&eacute;ciser absence et dur&eacute;e.</p>\r\n<h2>Et&eacute; - No&euml;l</h2>\r\n<p>Commande Juillet et Ao&ucirc;t&nbsp;<strong>disponibles fin juin</strong>&nbsp;en pr&eacute;vision des absences, de m&ecirc;me les commandes D&eacute;cembre et Janvier seront&nbsp;<strong>envoy&eacute;es ensemble fin novembre</strong>&nbsp;pour les m&ecirc;mes raisons.</p>\r\n<h2>Permanences et pr&eacute;paration des paniers</h2>\r\n<p>&nbsp;<strong>Local Chez J.A et V. Motte La Mimarnel, Cambremer</strong></p>\r\n<p><strong>Tous les jeudis sauf f&eacute;ri&eacute;s</strong>&nbsp;(report le mercredi)</p>\r\n<p style=\"padding-left: 40px;\"><strong>Pr&eacute;paration des paniers de 17H30 &agrave; 18H15</strong></p>\r\n<p>Attention pour la distribution aux petits paniers de l&eacute;gumes (10 euros) et grands paniers (15 euros) et aux sortes de pains.</p>\r\n<p><strong>Vous devez d\\\'abord pointer les commandes de chaque producteur (v&eacute;rifier la conformit&eacute; des quantit&eacute;s livr&eacute;es par rapport aux quantit&eacute;s command&eacute;es). Cette &eacute;tape est tr&egrave;s importante, d\\\'abord en cas d\\\'erreur pendant la distribution et ensuite pour les &eacute;ventuelles demandes d\\\'avoir.</strong></p>\r\n<p>Le planning des permanences est &eacute;tabli pour 4 mois. 3 familles par semaine pr&eacute;parent les paniers des autres familles.</p>\r\n<p>Vous pouvez pr&eacute;ciser vos disponibilit&eacute;s sur l\\\'interface pr&eacute;vue &agrave; cet effet.</p>\r\n<p>Les coordonn&eacute;es des consomm&rsquo;acteurs sont affich&eacute;es dans le local pour d&rsquo;&eacute;ventuels &eacute;changes.</p>\r\n<p style=\"padding-left: 40px;\"><strong>Retrait des produits de 18H20 &agrave; 19H30</strong></p>\r\n<p>Pr&eacute;voir son panier car les caisses en bois restent sur place.</p>\r\n<p>Souplesse si impossibilit&eacute; le jeudi soir, le panier reste dans le local (attention au chat, bien fermer la porte).</p>\r\n<p>Les cartons pizza, les bouteilles de lait avec bouchons propres, les bocaux canard en verre, les boites d&rsquo;&oelig;ufs sont &agrave; ramener au local pour recyclage.</p>\r\n<p>Signalez les produits manquants de vos commandes &agrave; Sylvain si vous souhaitez qu\\\'il vous fasse un avoir sur le mois suivant.</p>\r\n<h2>R&eacute;f&eacute;rents par producteur pour paiements</h2>\r\n<p><strong>Andr&eacute; et Gigi Arruego - andre.arruego@gmail.com - 06 31 61 80 05</strong></p>\r\n<p>Thierry et V&eacute;ronique Martin (Pont l\\\'Ev&ecirc;que)</p>\r\n<p><strong>Marc Besnard - cram.dranseb@free.fr - 07 83 74 14 41</strong></p>\r\n<p>Augustin Renault (miel)</p>\r\n<p>Les Co&rsquo;Pains (pains et pizzas)</p>\r\n<p>Olivier Brifaut (&oelig;ufs et viande).</p>\r\n<p><strong>Christelle Bercot - christellepouclee@yahoo.fr - 06 82 22 60 81</strong></p>\r\n<p>Hyes, J&eacute;r&ocirc;me Lemonnier (spiruline)</p>\r\n<p><strong>S&eacute;bastien BERCOT - bercotseb@voila.fr - 06 82 22 60 81</strong></p>\r\n<p>Manoir de Grandouet, St&eacute;phane et Lucille Grandval (jus de pomme)</p>\r\n<p>Erwin Gaudin&nbsp; (l&eacute;gumes) - Il peut arriver que le GAEC du Champs des Cigognes livre &agrave; la place d\\\'Erwin en cas de \"rupture de stock\"! Cela sera indiqu&eacute; dans le bon de commande pour inscrire l\\\'ordre sur les ch&egrave;ques.</p>\r\n<p><strong>Val&eacute;rie Guillemette - guillemettevalerie@gmail.com - 06 19 76 03 04</strong></p>\r\n<p>Gaylord Roney (beurre, cr&egrave;me, fromage blanc)</p>\r\n<p><strong>C&eacute;cile Larralde - cecebatlulu@hotmail.fr - 06 59 26 20 30</strong></p>\r\n<p>Clara Motte Rhizome (fromages de ch&egrave;vre)</p>\r\n<p>Gris\\\'mouss, David Leriche (bi&egrave;res)</p>\r\n<p><strong>No&eacute;mie LECLECH noemielc@hotmail.com - 06 26 36 61 61</strong></p>\r\n<p>GAEC du bois de Canon, Lo&iuml;c Gueguen (lait, fromage)</p>\r\n<p>GAEC de la Ferme de Livet, Olivier et Fabienne Storez (produits canard et oie)</p>\r\n<p><strong>C&eacute;cile LIDEC - lidec.cecile@orange.fr - 07 85 88 66 08</strong></p>\r\n<p>La Patte Jeanjean (p&acirc;tes et l&eacute;gumes secs)</p>\r\n<h2>Contacts</h2>\r\n<p>J&eacute;r&ocirc;me (SAV) 02 31 31 32 08 / 06 28 35 24 01</p>\r\n<p>Laurence (commandes) 02 31 63 45 70 / 06 28 54 40 34</p>\r\n<p>Nicolas (SAV) 06 14 26 46 81</p>\r\n<p>Sylvain (avoirs) 02 31 61 95 76 / 06 82 49 00 31</p>\r\n<p>Vanessa ( planning) 02 31 31 32 08 / 06 74 04 32 43</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\r\n<p>&nbsp;</p>\');
    INSERT INTO `amap_user` (`id`, `parent_id`, `email`, `firstname`, `lastname`, `address`, `city`, `zip_code`, `phone_number`, `password`, `is_active`, `roles`, `color`, `user_order`, `broadcast_list`, `portfolio_id`, `deleveries`) VALUES
    (1, NULL, \'amaphommesdeterre@yahoo.fr\', \'admin\', \'amaphommesdeterre\', NULL, \'Cambremer\', 14340, NULL, \'$2y$13$9BPPJ9aEyN.8ErcawzfnpOdeWcu7JschbdYmAflRU96RdnGvDjaqm\', 1, \'a:2:{i:0;s:10:\"ROLE_ADMIN\";i:1;s:13:\"ROLE_REFERENT\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (2, 1, \'nicolas.anne@laposte.net\', \'Nicolas\', \'ANNE\', \'2180, route des bois de Bayeux\', \'Montreuil-en-auge\', 14340, \'0614264681\', \'$2y$13$YWcbZXThL.hDdlbuLJhDBOKms33XYiWQ95FvbegIyeW4IuxYl6EjC\', 1, \'a:1:{i:0;s:10:\"ROLE_ADMIN\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (16, 1, \'sandrine.regnault@wanadoo.fr\', \'Sylvain et Sandrine\', \'CANARD-REGNAULT\', \'12, le lieu Droulin\', \'Cambremer\', 14340, \'0682490031\', \'$2y$13$wgkqbwUFfDAp1..PQ.02tu/oI8v5vpUA.GXZr.AQb78qsosquPIuG\', 1, \'a:1:{i:0;s:11:\"ROLE_MEMBER\";}\', NULL, NULL, \'a:1:{i:0;s:18:\"scanard@wanadoo.fr\";}\', NULL, NULL),
    (17, 1, \'andre.arruego@gmail.com\', \'André et Gigi\', \'ARRUEGO\', \'14 venelle Béneauville\', \'Chicheboville\', 14370, \'0631618005\', \'$2y$13$PpX6YsAN4nZPcjoW0m.C4.fxkPoONy9nATMBxw47YC18tPsau6qpm\', 1, \'a:2:{i:0;s:11:\"ROLE_MEMBER\";i:1;s:13:\"ROLE_REFERENT\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (18, 1, \'artman-bob@orange.fr\', \'Karine et Bob\', \'ARTMAN\', \'11 rue de Verdun\', \'Cambremer\', 14340, \'0687127083\', \'$2y$13$mp448IU/6ovbtNdTnSszAOpd8paxK82mtQzA0TGPg.P.W6o3dVz9i\', 1, \'a:1:{i:0;s:11:\"ROLE_MEMBER\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (19, 1, \'therese.besnard@free.fr\', \'Marc et Thérese\', \'BESNARD\', NULL, \'St Aubin sur Algot\', 14340, \'0231631883 / Thérèse 0652922362 / Marc 0783741441\', \'$2y$13$LzSkPDgnnW51LMTu6NR06.7Lewk64neYW8x9iWdzBTTiGoraHORx6\', 1, \'a:1:{i:0;s:11:\"ROLE_MEMBER\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (20, 1, \'sebastien.bercot@yahoo.fr\', \'Sébastien et Christelle\', \'BERCOT\', NULL, \'Cambremer\', 14340, \'0682226081\', \'$2y$13$yFh1/EkpsIsNGmIwue936exKRTwxgueHT6fCx0xyO2F.W0EXaudpO\', 1, \'a:2:{i:0;s:11:\"ROLE_MEMBER\";i:1;s:13:\"ROLE_REFERENT\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (21, 1, \'dorothee.bostoen@orange.fr\', \'Dorothée\', \'BOSTOEN\', \'3980, route d\\\'Englesqueville\', \'Cambremer\', 14430, \'0780344863\', \'$2y$13$wjEOzcEgNNemHzjOvv0LxuiB8sJv9w5G4QZqU8piGDL/9AJd0xoCu\', 1, \'a:1:{i:0;s:11:\"ROLE_MEMBER\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (22, 1, \'hbunichon@hotmail.fr\', \'Héléna et Sebastian\', \'BUNICHON-SUAREZ\', \'Rue de Verdun\', \'Cambremer\', 14340, \'0615054178\', \'$2y$13$qZgFeNsuHPT9fJGgOFrNmuyHMwwhOVt9OG0dp4XdKR8zg2wh0mBiW\', 1, \'a:1:{i:0;s:11:\"ROLE_MEMBER\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (23, 1, \'chollet.carine@outlook.fr\', \'Carine\', \'CHOLLET\', \'4, rue Pasteur\', \'Cambremer\', 14340, \'0622320928\', \'$2y$13$Et4/6Fsky7KsstRP8bWVV.PmTPoWOMv4vBlKMokuTJ2wOBc1xBGLe\', 1, \'a:1:{i:0;s:11:\"ROLE_MEMBER\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (24, 1, \'vanessadha@hotmail.fr\', \'Jérôme et Vanessa\', \'DE HAAN\', NULL, \'Notre Dame d\\\'Estrées\', 14340, \'0674043243\', \'$2y$13$fPKGBIsG.jULGgAaX5n.IeEHXtBNrrjqFI5gZodRxcHt7sUpky71a\', 1, \'a:2:{i:0;s:11:\"ROLE_MEMBER\";i:1;s:10:\"ROLE_ADMIN\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (25, 1, \'rinodibianca@infonie.fr\', \'Réjane et Cirino\', \'DI BIANCA\', NULL, \'Bonnebosq\', 14340, \'0231641726\', \'$2y$13$b4fi1eXAbqAT9J5tnwkZXOw02u.4bpGvmnCrizZVzGjK6nZct1A/.\', 1, \'a:1:{i:0;s:11:\"ROLE_MEMBER\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (26, 1, \'emiliejoigneaux@hotmail.com\', \'Sylvain et Emilie\', \'ELIE\', NULL, \'Cambremer\', 14340, NULL, \'$2y$13$B0yxDlAZsJP7l4.BeDiDo.1jYznZIpSLhH7i8TL4euLi63XXxOPXW\', 1, \'a:1:{i:0;s:11:\"ROLE_MEMBER\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (27, 1, \'etiennecamille.perso@gmail.com\', \'Camille\', \'ETIENNE\', \'700 boulevard Jamot-Biéville-Quétiéville\', \'Belle Vie en Auge\', 14270, \'0619362080\', \'$2y$13$Us1AdYZBim5ihW8b1mfiX.Add9PBiUVvsaA8wKRQeEHaGm27CNAqu\', 1, \'a:1:{i:0;s:11:\"ROLE_MEMBER\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (28, 1, \'jacqueline.fremont@yahoo.fr\', \'Jacqueline\', \'FREMONT\', NULL, \'Vimont\', 14370, \'0231239677 / 0781573474\', \'$2y$13$2x/o1IoVHazOBxqaWL2iAu/4a9qH2bqkS3kEeWv4udmVqpXpcX3pW\', 1, \'a:1:{i:0;s:11:\"ROLE_MEMBER\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (29, 1, \'francoisophie@hotmail.com\', \'Sophie\', \'GERARD\', \'La Ruette\', \'Léaupartie\', 14340, \'0633952267\', \'$2y$13$uBhtKuyIhpOAbfrGe6JRc.IpVawQt2g/FdMdJ3HZ1h.sV.0YxHJxi\', 1, \'a:1:{i:0;s:11:\"ROLE_MEMBER\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (30, 1, \'guillemettevalerie@gmail.com\', \'Valérie\', \'GUILLEMETTE\', \'12, impasse de la Salle des Fêtes\', \'Crèvecoeur-en-Auge\', 14340, \'0619760304\', \'$2y$13$Bl9LZiMbvQu0KtY2VOmZhO1Cg6jVWoNVBwiyFVldXK3H0v.v3qx3e\', 1, \'a:2:{i:0;s:11:\"ROLE_MEMBER\";i:1;s:13:\"ROLE_REFERENT\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (31, 1, \'gael.jouan@yahoo.fr\', \'Caroline et Gaël\', \'JOUAN\', NULL, \'Victot Pontfol\', 14430, \'0668171020\', \'$2y$13$gHMJZy.v1VvT0HSGauovwOiQGyFNQUflB.X3jIfmXuZhYUI1IT0jm\', 1, \'a:1:{i:0;s:11:\"ROLE_MEMBER\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (32, 1, \'cecebatlulu@hotmail.fr\', \'Cécile et Baptiste\', \'LARRALDE\', \'La Petite Ragoterie\', \'La Houblonnière\', 14340, \'0231620318 / 0659262030\', \'$2y$13$9UWt/qSpsDV9z98VmDI.JefCq.DsZnVBEUpfua.gitWfr/RUBcqb.\', 1, \'a:2:{i:0;s:11:\"ROLE_MEMBER\";i:1;s:13:\"ROLE_REFERENT\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (33, 1, \'lebihan_audrey@yahoo.fr\', \'Audrey et Gaylord\', \'LE BIHAN-ROGER\', \'7 avenue des Tilleuls\', \'Cambremer\', 14340, \'0642129153\', \'$2y$13$FQpx0fk1S94lt.KlYKzNHeQyloOmZv.eRyyFHtjumBLe5yNvvI1Di\', 1, \'a:1:{i:0;s:11:\"ROLE_MEMBER\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (34, 1, \'privebarbara@yahoo.fr\', \'Barbara et Anthony\', \'LEBOUCHER\', \'11, chemin du Champ Marin\', \'La Houblonnière\', 14340, \'0622618811 / 0675221851\', \'$2y$13$FOcDwCq5/OgKICJFP6IfHeHbu9ueHpXfNOP576XaQ42nCBe4xvp4y\', 1, \'a:1:{i:0;s:11:\"ROLE_MEMBER\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (35, 1, \'noemielc@hotmail.fr\', \'Noémie\', \'LE CLECH\', \'3, la Vergerie\', \'Cambremer\', 14340, \'0626366161\', \'$2y$13$UrNUR5SPuSvhDXTZCPR2oO4t2CtHCO2Dpcl48wpwpwCsk4YKVV36i\', 1, \'a:2:{i:0;s:11:\"ROLE_MEMBER\";i:1;s:13:\"ROLE_REFERENT\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (36, 1, \'stephaneleclache@yahoo.fr\', \'Fanny et Stéphane\', \'LE CLANCHE\', \'6 rue du Cadran\', \'Cambremer\', 14340, \'0231620568\', \'$2y$13$ikXsoSsPCw.gRgHizD4VFORqbDlbarVWbBvNIe7BmE.qJ0qxp03UO\', 1, \'a:1:{i:0;s:11:\"ROLE_MEMBER\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (37, 1, \'lidec.cecile@orange.fr\', \'Cécile et Olivier\', \'LIDEC\', \'10, rue de la Rosière\', \'Cambremer\', 14340, \'0231317624 / 0785886608\', \'$2y$13$yHhiJOLuZ8r1F45xnG.YoO.yhm0oMerYAPKetyrK8du5r82pQvBze\', 1, \'a:2:{i:0;s:11:\"ROLE_MEMBER\";i:1;s:13:\"ROLE_REFERENT\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (38, 1, \'anaelle.1988@gmail.com\', \'Anaëlle et Sofian\', \'MARTIN\', NULL, \'Cambremer\', 14340, NULL, \'$2y$13$v3rb8pykpBmHz9fr8t5HG.q2IsSUcsT1JG4M/Ux3FFfmaC7awMWbi\', 1, \'a:1:{i:0;s:11:\"ROLE_MEMBER\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (39, 1, \'juliette.megret@gmail.com\', \'Juliette\', \'MEGRET\', NULL, \'Corbon\', 14340, \'0643237916\', \'$2y$13$GTuqHzXqJq5rwKUU4Mn2MOsXy84NF42XDw12VdMfpcIkO/dFC.bMq\', 1, \'a:1:{i:0;s:11:\"ROLE_MEMBER\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (40, 1, \'jlchristophe@aol.com\', \'Laurence et Christophe\', \'MENARD\', \'Chemin de Pontfol\', \'Victot Pontfol\', 14430, \'0628544034\', \'$2y$13$2ZlkRMy.BzZwPSUC25sT9.uWR9Whg5zYq5jQZBZz1RYNYkLHt7SeO\', 1, \'a:2:{i:0;s:11:\"ROLE_MEMBER\";i:1;s:10:\"ROLE_ADMIN\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (41, 1, \'julie.piton@volvo.com\', \'Julie et Yohann\', \'MICHEL\', \'Route d\\\'Englesqueville\', \'Saint Laurent du Mont\', 14340, \'0665859000\', \'$2y$13$X/IDkbr2af8FuZDd5G0e/u5GvlWJQb7OpaZDitpzKhgAlqLM.P11i\', 1, \'a:1:{i:0;s:11:\"ROLE_MEMBER\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (42, 1, \'marionmouette@gmail.com\', \'Marion et Benjamin\', \'MOTTE-ANDRE\', NULL, \'Grandouet\', 14340, \'0688576036\', \'$2y$13$E8VMKMdrk.xCav.uQWjIduovyNcPP1n1FWoDEpQ.A0zcRhVuRrrT6\', 1, \'a:1:{i:0;s:11:\"ROLE_MEMBER\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (43, 32, \'clara.mott__@laposte.net\', \'Clara\', \'MOTTE RHIZOME_INACTIF\', NULL, \'Cambremer\', 14340, \'0601322246\', \'$2y$13$1cUENSXmiN6PUmm09nUGVuJFLVyWOz7PlxkIaZrWj0AhhPFDFNQoC\', 0, \'a:2:{i:0;s:11:\"ROLE_MEMBER\";i:1;s:13:\"ROLE_PRODUCER\";}\', \'#8ee21d\', NULL, \'a:0:{}\', NULL, NULL),
    (44, 1, \'elm1308@gmail.com\', \'Aurélien et Elisabeth\', \'MUIDEBLE\', NULL, \'Cambremer\', 14340, \'0622545489\', \'$2y$13$Yh5gU6osmOBuddq8nOZXVu8BTBZqyNTDf3bhXSCLHmekAHNW4iaV2\', 1, \'a:1:{i:0;s:11:\"ROLE_MEMBER\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (45, 1, \'bnidelet@ac-caen.fr\', \'Bruno\', \'NIDELET\', NULL, \'Notre Dame d\\\'Estrées\', 14340, \'0678150498\', \'$2y$13$xw8pS0Xc25zmUSAR2yrNE.O5hVN9Ix7prOCw7lNmnZ3H6JD2xqROu\', 1, \'a:1:{i:0;s:11:\"ROLE_MEMBER\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (46, 1, \'gaelleletourneur@hotmail.com\', \'Gaëlle\', \'PARQUET\', NULL, \'Cambremer\', 14340, \'0699752003\', \'$2y$13$C7Eq7T3iKaeggBAaPi2csOod0olgN.ZdelzRn5tLyvwgIt//CxaZ2\', 1, \'a:1:{i:0;s:11:\"ROLE_MEMBER\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (47, 1, \'patoux.soenen@orange.fr\', \'Jérôme et Valérie\', \'PATOUX\', NULL, \'Notre Dame d\\\'Estrées\', 14340, \'0619404063\', \'$2y$13$AJEuFtBcX7v145ecZzsXMupbaoNVpvVeGO0wIyPcoQ3d.UtQUmKGy\', 1, \'a:1:{i:0;s:11:\"ROLE_MEMBER\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (48, 1, \'fannythiry@outlook.fr\', \'Fanny\', \'THIRY\', NULL, \'Formentin\', 14340, NULL, \'$2y$13$TBD1x5sE11GkguhrLl1HeOuJcEffy.aSinyIoZ7Jj7Eu818mu5Q/m\', 1, \'a:1:{i:0;s:11:\"ROLE_MEMBER\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (49, 1, \'melanie.uleyn@gmail.com\', \'Mélanie et Arnaud\', \'ULEYN-AUBERT\', \'Cour Portebosq\', \'Cambremer\', 14340, \'0695712082\', \'$2y$13$cZTfMKv9wDqqwGouKtxjOehRbLpUQ8Pa3ykq/A2uA1AgNDG2Rv6MK\', 1, \'a:1:{i:0;s:11:\"ROLE_MEMBER\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (50, 20, \'erwingaudin@yahoo.fr\', \'Erwin\', \'GAUDIN\', \'La Mimarnel\', \'CAMBREMER\', 14340, \'0630359695\', \'$2y$13$K8tz/cyk.hpBneouiQ7bp.MPlerf47uEGbxZEBR/Fh0eRunSyxk0i\', 1, \'a:1:{i:0;s:13:\"ROLE_PRODUCER\";}\', \'#808040\', 13, \'a:0:{}\', NULL, NULL),
    (51, 32, \'david.leriche@club.fr\', \'David Leriche\', \'GRIS\\\'MOUSS\', \'1, bis rue du village\', \'GRISY\', 14170, \'0631544242\', \'$2y$13$VmhMZke5/meZAqP2KosY/OKkoBPsWnHVwowIqWKvFQoI0.CZrIFmK\', 1, \'a:1:{i:0;s:13:\"ROLE_PRODUCER\";}\', \'#c0c0c0\', 14, \'a:0:{}\', NULL, \'a:1:{i:0;i:4;}\'),
    (52, 1, \'christellepouclee@yahoo.fr\', \'Christelle\', \'BERCOT\', NULL, \'Cambremer\', 14430, \'0682226081\', \'$2y$13$O0uRY3FUA.q6w/B0fxoDzeuv2BNWP7e0fDUjmr3tilU/13B8NtU7K\', 1, \'a:1:{i:0;s:13:\"ROLE_REFERENT\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (53, 1, \'cram.dranseb@free.fr\', \'Marc\', \'BESNARD\', NULL, \'Saint Aubin sur Algot\', 14430, \'0231631883 / 0783741441\', \'$2y$13$Bu3c3cTlF96XijTabKJxNuUzH81O7RXRQoByd9xpqzQlpg.wqmrIe\', 1, \'a:1:{i:0;s:13:\"ROLE_REFERENT\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (54, 1, \'anne_fleur@hotmail.fr\', \'Fleur et Nicolas\', \'ANNE\', \'2180, route des Bois de Bayeux\', \'Montreuil-en-Auge\', 14340, \'0676000418\', \'$2y$13$dQ.LZOLdui5QcruSnsFfJOLSYItn7aoXT7SflSRvlcSAB6kOKvn/G\', 1, \'a:1:{i:0;s:11:\"ROLE_MEMBER\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (55, 1, \'sycanard@wanadoo.fr\', \'Sylvain\', \'CANARD\', NULL, \'Saint-Laurent-du-Mont\', 14340, \'0682490031\', \'$2y$13$koVB7mYJ.zgMc8n4KG6n9OsVWOkit0dzCbOHTIOUZvlTVMEdwCxgC\', 0, \'a:1:{i:0;s:10:\"ROLE_ADMIN\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (56, 1, \'philippeparret85@gmail.com\', \'Camille et Philippe\', \'PARRET\', NULL, NULL, NULL, \'0618080751\', \'$2y$13$Uxz6xbQ8TG2rGu18OcZSDe8OAVQjpyOpf31yyPwGnq.f7Cqdlss96\', 1, \'a:1:{i:0;s:11:\"ROLE_MEMBER\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL),
    (3, 20, \'marielle.duquenoy@yahoo.fr\', \'Marielle\', \'GAEC LE CHAMPS DES CIGOGNES\', \'Ferme du Bois de Canon\', \'Mézidon-Canon\', 14270, \'0670100277\', \'$2y$13$d6H0.1R/wFc1sYxQ.Qt6teL1qFH5.jnD9eRBs4YScxagvezLBEcPe\', 0, \'a:1:{i:0;s:13:\"ROLE_PRODUCER\";}\', \'#eca750\', 1, \'a:0:{}\', NULL, NULL),
    (4, 53, \'boulangerielescopains@gmail.com\', NULL, \'LES CO\\\'PAINS\', \'la Côte au Seigneur\', \'Saint-Aubin-Sur-Algot\', 14340, \'0231322224 / Thierry 0662003282 / Valérie 0660148322\', \'$2y$13$7V2rWcnXj59UT9ulvC.ryu2ajyRKC/DGwwx9XPUH/ZQlP2QV2AanS\', 1, \'a:1:{i:0;s:13:\"ROLE_PRODUCER\";}\', \'#f6fedb\', 2, \'a:0:{}\', NULL, NULL),
    (5, 35, \'g.loic@neuf.fr\', \'Loïc Gueguen\', \'GAEC DU BOIS DE CANON\', \'Ferme du Bois de Canon\', \'Mézidon-Canon\', 14270, \'0622642627\', \'$2y$13$VL6Z4w.VEgVdJ/1W9gJeW.RpoTAu67EaHE1Cjr7kLfRRCwxpvH/HC\', 1, \'a:1:{i:0;s:13:\"ROLE_PRODUCER\";}\', \'#e6d3a3\', 3, \'a:0:{}\', NULL, NULL),
    (6, 32, \'clara.mott@laposte.net\', \'Clara\', \'MOTTE RHIZOME\', \'La Mimarnel\', \'Cambremer\', 14340, \'0601322246\', \'$2y$13$ezGxo5T0v7bo0yG3uT49v.6o7.m8E3ZRcJGxKqOgvCfVv9BdSjmqG\', 1, \'a:2:{i:0;s:13:\"ROLE_PRODUCER\";i:1;s:11:\"ROLE_MEMBER\";}\', \'#b6c454\', 4, \'a:0:{}\', NULL, NULL),
    (7, 35, \'gaecdelivet@gmail.com\', \'Olivier et Fabienne Storez\', \'GAEC LA FERME DE LIVET\', NULL, \'Notre Dame De Fresnay\', 14170, \'0628302332\', \'$2y$13$qY70MH1cJS7xsDGbbGwXo.6rW4UjW8c.ToYnkNIZmFr7VvpznEXGu\', 1, \'a:1:{i:0;s:13:\"ROLE_PRODUCER\";}\', \'#b1f8f2\', 5, \'a:0:{}\', NULL, \'a:1:{i:0;i:2;}\'),
    (8, 20, \'cavesdumanoir@orange.fr\', \'Stéphane et Lucille Grandval\', \'GAEC DU MANOIR DE GRANDOUET\', \'Le Manoir - Grandouet\', \'Cambremer\', 14340, \'0231630873 / 0677760221\', \'$2y$13$h/8DL.lS2QK8zeChobGDs.uWnHA5yJoBk532LtTci34iDEsjzcoOy\', 1, \'a:1:{i:0;s:13:\"ROLE_PRODUCER\";}\', \'#9e788f\', 6, \'a:0:{}\', NULL, NULL),
    (9, 53, \'renault-aug@orange.fr\', \'Augustin\', \'RENAULT\', NULL, \'Saint-Aubin-sur-Algot\', 14340, \'0610874763\', \'$2y$13$NAF0KZ1lTZA1DLGLWtHzCOkHUHAH2kDekOetDQVFhRJvShZIXUkzK\', 1, \'a:1:{i:0;s:13:\"ROLE_PRODUCER\";}\', \'#817f75\', 7, \'a:0:{}\', NULL, NULL),
    (10, 53, \'olivier.brifaut@sfr.fr\', \'Olivier et Anne\', \'BRIFAUT\', \'Noiremare\', \'Saint-Ouen-le-Houx\', 14140, \'0231329135\', \'$2y$13$Bqy2dQC5URFEp06IhQfSFOpCqwymfQBYlU78JhMXE8XXWephJ4936\', 1, \'a:1:{i:0;s:13:\"ROLE_PRODUCER\";}\', \'#a9b3ce\', 8, \'a:0:{}\', NULL, \'a:2:{i:0;i:2;i:1;i:4;}\'),
    (11, 30, \'r.gaylord@hotmail.fr\', \'Gaylord\', \'RONEY\', \'CD 45C\', \'Douville-en-Auge\', 14430, \'0630368323\', \'$2y$13$R.OdjuQRt06WgAZLaM8PWuv1BNAcrO52Wb9I6hOOe8QnocBx84hF6\', 1, \'a:1:{i:0;s:13:\"ROLE_PRODUCER\";}\', \'#f4f7be\', 9, \'a:0:{}\', NULL, \'a:2:{i:0;i:2;i:1;i:4;}\'),
    (12, 37, \'misamoma@outlook.fr\', \'Sandrine Drouet\', \'LA PATTE JEANJEAN\', \'CCI Intech, Pôle universitaire d’Alençon\', \'Damigny\', 61250, NULL, \'$2y$13$.C36Xtd37qCjdsekjNy35eNXGeDw0Cejm5z5w0cOZfVoQD.Iz2jtm\', 1, \'a:1:{i:0;s:13:\"ROLE_PRODUCER\";}\', \'#eca750\', 10, \'a:0:{}\', NULL, \'a:1:{i:0;i:2;}\'),
    (13, 52, \'commande@akalfood.com\', \'Jérôme Lemonnier\', \'HYES\', \'Ecodomaine de Bouquetot, Chemin des Broches\', \'Saint-Pierre-Azif\', 14950, \'0683521824\', \'$2y$13$ZD0AP6icl.Z7PVxpr2TQMutV/HcX8T9txFAa08EJ4O86ot1gFtIha\', 1, \'a:1:{i:0;s:13:\"ROLE_PRODUCER\";}\', \'#99d5c9\', 11, \'a:0:{}\', NULL, NULL),
    (14, 17, \'bruyere.vero@wanadoo.fr\', \'Thierry et Véronique\', \'MARTIN\', \'Le Petit Malheur\', \'Bourgeauville\', 14430, \'0231648385 / 0608146779\', \'$2y$13$0ugTwhi0cfqvJO36xlI.De3LPf4/8NK0LFE0nnJAT0ImapwiT7/o.\', 1, \'a:1:{i:0;s:13:\"ROLE_PRODUCER\";}\', \'#4d9de0\', 12, \'a:0:{}\', NULL, \'a:1:{i:0;i:2;}\'),
    (15, NULL, \'nicolas.anne@businessdecision.com\', NULL, \'\', NULL, NULL, NULL, NULL, \'$2y$13$ezGxo5T0v7bo0yG3uT49v.6o7.m8E3ZRcJGxKqOgvCfVv9BdSjmqG\', 1, \'a:1:{i:0;s:10:\"ROLE_ADMIN\";}\', NULL, NULL, \'a:0:{}\', NULL, NULL);
    INSERT INTO `amap_product` (`id`, `producer_id`, `name`, `price`, `is_active`, `product_order`, `portfolio_id`) VALUES
    (1, 3, \'Légumes panier\', \'15.00\', 0, 1, NULL),
    (2, 3, \'Légumes petit panier\', \'10.00\', 0, 2, NULL),
    (3, 4, \'Pizza pour 2 personnes\', \'8.00\', 1, 1, NULL),
    (4, 4, \'Baguette 350 g\', \'1.45\', 1, 2, NULL),
    (5, 4, \'Pain T65 blanc 700 g\', \'3.25\', 1, 3, NULL),
    (6, 4, \'Pain T65 blanc 1 kg\', \'4.20\', 1, 4, NULL),
    (7, 4, \'Pain T80 demi-complet nature 700 g\', \'3.25\', 1, 5, NULL),
    (8, 4, \'Pain T80 demi-complet nature 1 kg\', \'4.20\', 1, 6, NULL),
    (9, 4, \'Pain T150 intégral nature 700 g\', \'3.25\', 1, 7, NULL),
    (10, 4, \'Pain T150 intégral nature 1 kg\', \'4.20\', 1, 8, NULL),
    (11, 4, \'Pain T150 avec graines sésame 700 g\', \'3.60\', 1, 9, NULL),
    (12, 4, \'Pain T150 avec graines sésame 1kg\', \'4.95\', 1, 10, NULL),
    (13, 4, \'Pain T150 avec graines tournesol 700 g\', \'3.60\', 1, 11, NULL),
    (14, 4, \'Pain T150 avec graines tournesol 1kg\', \'4.95\', 1, 12, NULL),
    (15, 4, \'Pain T150 avec graines pavot 700 g\', \'3.60\', 1, 13, NULL),
    (16, 4, \'Pain T150 avec graines pavot 1kg\', \'4.95\', 1, 14, NULL),
    (17, 4, \'Pain T80 avec graines noix 700 g\', \'4.30\', 1, 15, NULL),
    (18, 4, \'Pain T80 avec graines noix 1kg\', \'5.90\', 1, 16, NULL),
    (19, 4, \'T80 raisins secs 700g\', \'4.30\', 1, 17, NULL),
    (20, 4, \'T80 raisins secs 1kg\', \'5.90\', 1, 18, NULL),
    (21, 4, \'T150 graines de lin 700g\', \'3.60\', 1, 19, NULL),
    (22, 4, \'T150 graines de lin 1kg\', \'4.95\', 1, 20, NULL),
    (23, 4, \'Pain brioché 600g simple\', \'3.05\', 1, 21, NULL),
    (24, 4, \'Pain brioché 600g aux raisins\', \'3.35\', 1, 22, NULL),
    (25, 4, \'Pain brioché 600g au chocolat\', \'3.35\', 1, 23, NULL),
    (26, 4, \'Pain épeautre 700g\', \'3.90\', 1, 24, NULL),
    (27, 4, \'Sablé Nature\', \'0.95\', 1, 25, NULL),
    (28, 4, \'Sablé Raisin\', \'0.95\', 1, 26, NULL),
    (29, 4, \'Sablé Chocolat\', \'0.95\', 1, 27, NULL),
    (30, 4, \'Sablé Sésame\', \'0.95\', 1, 28, NULL),
    (31, 5, \'Lait cru entier 1 litre\', \'1.00\', 1, 1, NULL),
    (32, 5, \'Tome Part 285 g\', \'4.00\', 1, 2, NULL),
    (33, 5, \'Tome Part 500 g\', \'7.00\', 1, 3, NULL),
    (34, 6, \'Le Crottin - frais\', \'2.15\', 0, 1, NULL),
    (35, 6, \'Le Crottin - demi-sec\', \'2.15\', 1, 2, NULL),
    (36, 6, \'Le Crottin - sec\', \'2.15\', 0, 3, NULL),
    (37, 6, \'Le petit Motte - frais\', \'2.80\', 1, 4, NULL),
    (38, 6, \'Le petit Motte - demi-sec\', \'2.80\', 1, 5, NULL),
    (39, 6, \'Le petit Motte - sec\', \'2.80\', 1, 6, NULL),
    (40, 6, \'Le petit Motte  - poivre\', \'2.90\', 1, 7, NULL),
    (41, 6, \'Le petit Motte  - estragon\', \'2.90\', 1, 8, NULL),
    (42, 6, \'Le petit Motte  - ciboulette\', \'2.90\', 1, 9, NULL),
    (43, 6, \'Le petit Motte - échalote\', \'2.90\', 1, 10, NULL),
    (44, 6, \'La Brique cendrée\', \'3.50\', 1, 15, NULL),
    (45, 6, \'La Brique nature\', \'3.50\', 1, 16, NULL),
    (46, 6, \'La Faisselle (grande)\', \'3.00\', 1, 17, NULL),
    (47, 7, \'Canard - Foie gras entier (bocal 180g)\', \'26.00\', 1, 1, NULL),
    (48, 7, \'Canard - Foie gras entier mi-cuit (sous-vide) 100g\', \'14.00\', 1, 2, NULL),
    (49, 7, \'Canard - Foie gras entier mi-cuit (sous-vide) 200g\', \'28.00\', 1, 3, NULL),
    (50, 7, \'Canard - Mousse de foie 190g\', \'16.00\', 1, 4, NULL),
    (51, 7, \'Canard - Mousse de foie 95g\', \'8.50\', 1, 5, NULL),
    (52, 7, \'Canard - Mousse de foie 65g\', \'6.50\', 1, 6, NULL),
    (53, 7, \'Canard - Pâté de Livet 125g\', \'7.00\', 1, 7, NULL),
    (54, 7, \'Canard - Cou de canard farci 400g\', \'18.00\', 1, 8, NULL),
    (55, 7, \'Canard - Rillettes pur canard 200g\', \'6.80\', 1, 9, NULL),
    (56, 7, \'Canard - Rillettes pur 90g\', \'4.00\', 1, 10, NULL),
    (57, 7, \'Canard - Rillettes au foie gras 200g\', \'10.00\', 1, 11, NULL),
    (58, 7, \'Canard - Gésiers 320g\', \'7.00\', 1, 12, NULL),
    (59, 7, \'Canard - Confits 2 cuisses 600g\', \'15.00\', 1, 13, NULL),
    (60, 7, \'Canard - Confits 2 magrets 750g\', \'16.00\', 1, 14, NULL),
    (61, 7, \'Magret séché la pièce (sous-vide) 250g\', \'12.50\', 1, 15, NULL),
    (62, 7, \'Magret séché tranché (sous-vide) 150g\', \'10.50\', 1, 16, NULL),
    (63, 7, \'Canard au cidre (bocal 700g)\', \'12.50\', 1, 17, NULL),
    (64, 7, \'Oie - Foie gras entier (bocal 180g)\', \'29.00\', 1, 18, NULL),
    (65, 7, \'Oie - Foie gras entier mi-cuit (sous-vide) 100g\', \'16.00\', 1, 19, NULL),
    (66, 7, \'Oie - Foie gras entier mi-cuit (sous-vide) 200g\', \'32.00\', 1, 20, NULL),
    (67, 7, \'Oie - Foie gras entier mi-cuit (sous-vide) 300g\', \'48.00\', 1, 21, NULL),
    (68, 7, \'Oie - Mousse de foie 190g\', \'17.00\', 1, 22, NULL),
    (69, 7, \'Oie - Mousse de foie 95g\', \'9.50\', 1, 23, NULL),
    (70, 7, \'Oie - Mousse de foie 65g\', \'7.00\', 1, 24, NULL),
    (71, 7, \'Oie - Pâté de Livet 125g\', \'7.00\', 1, 25, NULL),
    (72, 7, \'Oie - Rillettes pure 200g\', \'6.80\', 1, 26, NULL),
    (73, 7, \'Oie - Rillettes pure 90g\', \'4.00\', 1, 27, NULL),
    (74, 7, \'Oie - Gésiers 320g\', \'7.00\', 1, 28, NULL),
    (75, 7, \'Oie - Confits aile et cuisse 850g\', \'16.00\', 1, 29, NULL),
    (76, 7, \'Oie - Confits 2 magrets 750g\', \'16.00\', 1, 30, NULL),
    (77, 7, \'Oie - Cassoulet au confit d\\\'oie 750g\', \'13.50\', 1, 31, NULL),
    (78, 7, \'Oie - Graisse d\\\'oie 300g\', \'3.50\', 1, 32, NULL),
    (79, 8, \'Jus de pomme 75cl\', \'2.10\', 1, 1, NULL),
    (80, 9, \'Miel toutes fleurs 500 g\', \'5.00\', 1, 1, NULL),
    (81, 10, \'6 Œufs\', \'2.00\', 1, 1, NULL),
    (82, 10, \'Poulet entier bio - 2 à 2,4 kg @ 9,90 € / kg\', \'0.00\', 1, 2, NULL),
    (83, 10, \'Poulet bio - 2 cuisses sous vide @ 9,95 € / kg\', \'0.00\', 1, 3, NULL),
    (84, 10, \'Poulet bio - 2 blancs sous vide @ 20 € / kg\', \'0.00\', 1, 4, NULL),
    (85, 11, \'Beurre doux (plaquette de 250g.)\', \'2.80\', 1, 1, NULL),
    (86, 11, \'Beurre demi-sel (plaquette de 250g.)\', \'2.80\', 1, 2, NULL),
    (87, 11, \'Crème 25 cl\', \'2.50\', 1, 3, NULL),
    (88, 11, \'Crème 50 cl\', \'4.50\', 1, 4, NULL),
    (89, 11, \'Fromage blanc battu 50cl\', \'2.50\', 1, 5, NULL),
    (90, 12, \'Gigli (250 g) - Epeautre et blé des Pharaons\', \'2.90\', 1, 1, NULL),
    (91, 12, \'Crête de Coq (250g) - Epeautre/blé des Pharaons\', \'2.90\', 1, 2, NULL),
    (92, 12, \'Radiatori (250 g) - Epeautre et blé des Pharaons\', \'2.90\', 1, 3, NULL),
    (93, 12, \'P\\\'tite Tini (1 kg) - Epeautre et blé des Pharaons\', \'7.50\', 1, 4, NULL),
    (94, 12, \'P\\\'tite Tini (250g) - Engrain (pauvre en gluten)\', \'2.90\', 1, 5, NULL),
    (95, 12, \'La P\\\'tite Tini (250 g) - Sarrasin (sans gluten)\', \'2.90\', 1, 6, NULL),
    (96, 12, \'Gigli (250 g) - Tomate Basilic\', \'2.90\', 1, 7, NULL),
    (97, 12, \'Crête de Coq (250g) - Citron & gingembre\', \'2.90\', 1, 8, NULL),
    (98, 12, \'Radiatori (250g) - Curry Indien\', \'2.90\', 1, 9, NULL),
    (99, 12, \'Lumaconi (250g - Persil Ail\', \'2.90\', 1, 10, NULL),
    (100, 12, \'Amore Mio (250g) - Orange Romarin\', \'2.90\', 1, 11, NULL),
    (101, 12, \'Fusilli Trio de Légumes (250g)\', \'2.90\', 1, 12, NULL),
    (102, 12, \'Pois cassés du Perche (500g)\', \'3.00\', 1, 13, NULL),
    (103, 12, \'Lentillons du Perche (500 g)\', \'3.00\', 1, 14, NULL),
    (104, 12, \'Lentilles Beluga du Perche (500g)\', \'3.50\', 1, 15, NULL),
    (105, 12, \'Boulgour de blé des Pharaons (400g)\', \'3.50\', 1, 16, NULL),
    (106, 12, \'Semoule de blé des Pharaons (400g)\', \'3.50\', 1, 17, NULL),
    (107, 13, \'Spiruline fraîche (150 g.)\', \'7.50\', 1, 1, NULL),
    (108, 13, \'Spiruline sèche en poudre (100g.)\', \'18.00\', 1, 2, NULL),
    (109, 13, \'Spiruline sèche Brindilles (100g.)\', \'18.00\', 1, 3, NULL),
    (110, 13, \'Spirtonic (50g.)\', \'9.50\', 1, 4, NULL),
    (111, 13, \'Spirumix (100g.)\', \'4.00\', 1, 5, NULL),
    (112, 14, \'Pont l\\\'Evêque affiné\', \'4.50\', 1, 1, NULL),
    (113, 14, \'Pont l\\\'Evêque à affiner\', \'4.50\', 1, 2, NULL),
    (114, 6, \'Le petit Motte - herbes de provence\', \'2.90\', 1, 11, NULL),
    (115, 6, \'Le petit Motte - 5 baies\', \'2.90\', 1, 13, NULL),
    (116, 6, \'Le petit Motte - ail et fines herbes\', \'2.90\', 1, 14, NULL),
    (117, 6, \'Le petit Motte - épices mexicaines\', \'2.90\', 1, 12, NULL),
    (118, 51, \'Bière Gris\\\'Mouss Blanche 75 cl\', \'5.00\', 1, 1, NULL),
    (119, 51, \'Bière Gris\\\'Mouss Blonde 75 cl\', \'5.00\', 1, 1, NULL),
    (120, 51, \'Bière Gris\\\'Mouss Ambrée 75 cl\', \'5.00\', 1, 1, NULL),
    (121, 51, \'Bière Gris\\\'Mouss Brune 75 cl\', \'5.00\', 1, 1, NULL),
    (122, 51, \'Bière Gris\\\'Mouss Blanche 33 cl\', \'2.50\', 1, 1, NULL),
    (123, 51, \'Bière Gris\\\'Mouss Blonde 33cl\', \'2.50\', 1, 1, NULL),
    (124, 51, \'Bière Gris\\\'Mouss Ambrée 33 cl\', \'2.50\', 1, 1, NULL),
    (125, 51, \'Bière Gris\\\'Mouss Brune 33 cl\', \'2.50\', 1, 1, NULL);
        ');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE availability_schedule');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE availability_schedule_element');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE basket');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE credit');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE credit_basket_amount');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE document');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE planning');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE planning_element');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE planning_element_user');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE portfolio');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE product');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE product_quantity');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE thumbnail');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE user');
    }
}
