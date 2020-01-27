<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200126214310 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE amap_mail_log (id INT AUTO_INCREMENT NOT NULL, sent_at DATE NOT NULL, subject VARCHAR(255) DEFAULT NULL, content LONGTEXT NOT NULL, deleted TINYINT(1) DEFAULT \'0\' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE amap_user_mail_log (mail_log_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_73A9A3AF375EE2ED (mail_log_id), INDEX IDX_73A9A3AFA76ED395 (user_id), PRIMARY KEY(mail_log_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE amap_user_mail_log ADD CONSTRAINT FK_73A9A3AF375EE2ED FOREIGN KEY (mail_log_id) REFERENCES amap_mail_log (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE amap_user_mail_log ADD CONSTRAINT FK_73A9A3AFA76ED395 FOREIGN KEY (user_id) REFERENCES amap_user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE amap_user_mail_log DROP FOREIGN KEY FK_73A9A3AF375EE2ED');
        $this->addSql('DROP TABLE amap_mail_log');
        $this->addSql('DROP TABLE amap_user_mail_log');
        $this->addSql('ALTER TABLE amap_user CHANGE email email VARCHAR(190) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
