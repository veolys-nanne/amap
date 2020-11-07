<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201106131258 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE amap_user (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, portfolio_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, broadcast_list LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', user_order INT DEFAULT NULL, color VARCHAR(255) DEFAULT NULL, denomination VARCHAR(255) DEFAULT NULL, payto VARCHAR(255) DEFAULT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, zip_code INT DEFAULT NULL, phone_numbers LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', password VARCHAR(64) NOT NULL, reset_password VARCHAR(255) DEFAULT NULL, active TINYINT(1) NOT NULL, new TINYINT(1) DEFAULT \'1\' NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', deleveries LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', deleted TINYINT(1) DEFAULT \'0\' NOT NULL, UNIQUE INDEX UNIQ_D6882FFCE7927C74 (email), INDEX IDX_D6882FFC727ACA70 (parent_id), UNIQUE INDEX UNIQ_D6882FFCB96B5643 (portfolio_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE amap_product_quantity (product_id INT NOT NULL, basket_id INT NOT NULL, price NUMERIC(10, 2) DEFAULT NULL, quantity INT NOT NULL, INDEX IDX_65C9AE084584665A (product_id), INDEX IDX_65C9AE081BE1FB52 (basket_id), PRIMARY KEY(product_id, basket_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE amap_portfolio (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE amap_document (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, role VARCHAR(255) DEFAULT NULL, text LONGTEXT NOT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE amap_product (id INT AUTO_INCREMENT NOT NULL, producer_id INT DEFAULT NULL, portfolio_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, product_order INT DEFAULT NULL, product_stock INT DEFAULT NULL, price NUMERIC(10, 2) NOT NULL, active TINYINT(1) NOT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_5FC4AB1589B658FE (producer_id), UNIQUE INDEX UNIQ_5FC4AB15B96B5643 (portfolio_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE amap_basket (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, date DATE NOT NULL, frozen TINYINT(1) NOT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_7F951B25A76ED395 (user_id), INDEX IDX_7F951B25727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE amap_credit_basket_amount (credit_id INT NOT NULL, basket_id INT NOT NULL, amount NUMERIC(10, 2) DEFAULT NULL, INDEX IDX_F0084B05CE062FF9 (credit_id), INDEX IDX_F0084B051BE1FB52 (basket_id), PRIMARY KEY(credit_id, basket_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE amap_thumbnail (id INT AUTO_INCREMENT NOT NULL, portfolio_id INT DEFAULT NULL, media VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_A92230A1B96B5643 (portfolio_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE amap_planning (id INT AUTO_INCREMENT NOT NULL, state INT NOT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE amap_planning_element (id INT AUTO_INCREMENT NOT NULL, planning_id INT NOT NULL, date DATE NOT NULL, INDEX IDX_ADA7D17B3D865311 (planning_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE amap_planning_element_user (planning_element_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_7C2203186BE2B44 (planning_element_id), INDEX IDX_7C22031A76ED395 (user_id), PRIMARY KEY(planning_element_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE amap_mail_log (id INT AUTO_INCREMENT NOT NULL, sent_at DATE NOT NULL, subject VARCHAR(255) DEFAULT NULL, content LONGTEXT NOT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE amap_user_mail_log (mail_log_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_92EE191C375EE2ED (mail_log_id), INDEX IDX_92EE191CA76ED395 (user_id), PRIMARY KEY(mail_log_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE amap_credit (id INT AUTO_INCREMENT NOT NULL, producer_id INT DEFAULT NULL, member_id INT DEFAULT NULL, date DATE NOT NULL, total_amount NUMERIC(10, 2) NOT NULL, current_amount NUMERIC(10, 2) NOT NULL, object VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_411225A089B658FE (producer_id), INDEX IDX_411225A07597D3FE (member_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE amap_unavailability (date DATE NOT NULL COMMENT \'(DC2Type:DateKey)\', member_id INT NOT NULL, INDEX IDX_5D89B35D7597D3FE (member_id), PRIMARY KEY(member_id, date)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE amap_user ADD CONSTRAINT FK_D6882FFC727ACA70 FOREIGN KEY (parent_id) REFERENCES amap_user (id)');
        $this->addSql('ALTER TABLE amap_user ADD CONSTRAINT FK_D6882FFCB96B5643 FOREIGN KEY (portfolio_id) REFERENCES amap_portfolio (id)');
        $this->addSql('ALTER TABLE amap_product_quantity ADD CONSTRAINT FK_65C9AE084584665A FOREIGN KEY (product_id) REFERENCES amap_product (id)');
        $this->addSql('ALTER TABLE amap_product_quantity ADD CONSTRAINT FK_65C9AE081BE1FB52 FOREIGN KEY (basket_id) REFERENCES amap_basket (id)');
        $this->addSql('ALTER TABLE amap_product ADD CONSTRAINT FK_5FC4AB1589B658FE FOREIGN KEY (producer_id) REFERENCES amap_user (id)');
        $this->addSql('ALTER TABLE amap_product ADD CONSTRAINT FK_5FC4AB15B96B5643 FOREIGN KEY (portfolio_id) REFERENCES amap_portfolio (id)');
        $this->addSql('ALTER TABLE amap_basket ADD CONSTRAINT FK_7F951B25A76ED395 FOREIGN KEY (user_id) REFERENCES amap_user (id)');
        $this->addSql('ALTER TABLE amap_basket ADD CONSTRAINT FK_7F951B25727ACA70 FOREIGN KEY (parent_id) REFERENCES amap_basket (id)');
        $this->addSql('ALTER TABLE amap_credit_basket_amount ADD CONSTRAINT FK_F0084B05CE062FF9 FOREIGN KEY (credit_id) REFERENCES amap_credit (id)');
        $this->addSql('ALTER TABLE amap_credit_basket_amount ADD CONSTRAINT FK_F0084B051BE1FB52 FOREIGN KEY (basket_id) REFERENCES amap_basket (id)');
        $this->addSql('ALTER TABLE amap_thumbnail ADD CONSTRAINT FK_A92230A1B96B5643 FOREIGN KEY (portfolio_id) REFERENCES amap_portfolio (id)');
        $this->addSql('ALTER TABLE amap_planning_element ADD CONSTRAINT FK_ADA7D17B3D865311 FOREIGN KEY (planning_id) REFERENCES amap_planning (id)');
        $this->addSql('ALTER TABLE amap_planning_element_user ADD CONSTRAINT FK_7C2203186BE2B44 FOREIGN KEY (planning_element_id) REFERENCES amap_planning_element (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE amap_planning_element_user ADD CONSTRAINT FK_7C22031A76ED395 FOREIGN KEY (user_id) REFERENCES amap_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE amap_user_mail_log ADD CONSTRAINT FK_92EE191C375EE2ED FOREIGN KEY (mail_log_id) REFERENCES amap_mail_log (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE amap_user_mail_log ADD CONSTRAINT FK_92EE191CA76ED395 FOREIGN KEY (user_id) REFERENCES amap_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE amap_credit ADD CONSTRAINT FK_411225A089B658FE FOREIGN KEY (producer_id) REFERENCES amap_user (id)');
        $this->addSql('ALTER TABLE amap_credit ADD CONSTRAINT FK_411225A07597D3FE FOREIGN KEY (member_id) REFERENCES amap_user (id)');
        $this->addSql('ALTER TABLE amap_unavailability ADD CONSTRAINT FK_5D89B35D7597D3FE FOREIGN KEY (member_id) REFERENCES amap_user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE amap_user DROP FOREIGN KEY FK_D6882FFC727ACA70');
        $this->addSql('ALTER TABLE amap_product DROP FOREIGN KEY FK_5FC4AB1589B658FE');
        $this->addSql('ALTER TABLE amap_basket DROP FOREIGN KEY FK_7F951B25A76ED395');
        $this->addSql('ALTER TABLE amap_planning_element_user DROP FOREIGN KEY FK_7C22031A76ED395');
        $this->addSql('ALTER TABLE amap_user_mail_log DROP FOREIGN KEY FK_92EE191CA76ED395');
        $this->addSql('ALTER TABLE amap_credit DROP FOREIGN KEY FK_411225A089B658FE');
        $this->addSql('ALTER TABLE amap_credit DROP FOREIGN KEY FK_411225A07597D3FE');
        $this->addSql('ALTER TABLE amap_unavailability DROP FOREIGN KEY FK_5D89B35D7597D3FE');
        $this->addSql('ALTER TABLE amap_user DROP FOREIGN KEY FK_D6882FFCB96B5643');
        $this->addSql('ALTER TABLE amap_product DROP FOREIGN KEY FK_5FC4AB15B96B5643');
        $this->addSql('ALTER TABLE amap_thumbnail DROP FOREIGN KEY FK_A92230A1B96B5643');
        $this->addSql('ALTER TABLE amap_product_quantity DROP FOREIGN KEY FK_65C9AE084584665A');
        $this->addSql('ALTER TABLE amap_product_quantity DROP FOREIGN KEY FK_65C9AE081BE1FB52');
        $this->addSql('ALTER TABLE amap_basket DROP FOREIGN KEY FK_7F951B25727ACA70');
        $this->addSql('ALTER TABLE amap_credit_basket_amount DROP FOREIGN KEY FK_F0084B051BE1FB52');
        $this->addSql('ALTER TABLE amap_planning_element DROP FOREIGN KEY FK_ADA7D17B3D865311');
        $this->addSql('ALTER TABLE amap_planning_element_user DROP FOREIGN KEY FK_7C2203186BE2B44');
        $this->addSql('ALTER TABLE amap_user_mail_log DROP FOREIGN KEY FK_92EE191C375EE2ED');
        $this->addSql('ALTER TABLE amap_credit_basket_amount DROP FOREIGN KEY FK_F0084B05CE062FF9');
        $this->addSql('DROP TABLE amap_user');
        $this->addSql('DROP TABLE amap_product_quantity');
        $this->addSql('DROP TABLE amap_portfolio');
        $this->addSql('DROP TABLE amap_document');
        $this->addSql('DROP TABLE amap_product');
        $this->addSql('DROP TABLE amap_basket');
        $this->addSql('DROP TABLE amap_credit_basket_amount');
        $this->addSql('DROP TABLE amap_thumbnail');
        $this->addSql('DROP TABLE amap_planning');
        $this->addSql('DROP TABLE amap_planning_element');
        $this->addSql('DROP TABLE amap_planning_element_user');
        $this->addSql('DROP TABLE amap_mail_log');
        $this->addSql('DROP TABLE amap_user_mail_log');
        $this->addSql('DROP TABLE amap_credit');
        $this->addSql('DROP TABLE amap_unavailability');
    }
}
