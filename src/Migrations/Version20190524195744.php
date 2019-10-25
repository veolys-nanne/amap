<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190524195744 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE amap_user ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE is_active active TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE amap_basket ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE amap_planning ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
        $this->addSql('ALTER TABLE amap_credit ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE is_active active TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE amap_product ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE is_active active TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE amap_document ADD deleted TINYINT(1) DEFAULT \'0\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE amap_basket DROP deleted');
        $this->addSql('ALTER TABLE amap_credit DROP deleted, CHANGE active is_active TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE amap_document DROP deleted');
        $this->addSql('ALTER TABLE amap_planning DROP deleted');
        $this->addSql('ALTER TABLE amap_product DROP deleted, CHANGE active is_active TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE amap_user DROP deleted, CHANGE active is_active TINYINT(1) NOT NULL');
    }
}
