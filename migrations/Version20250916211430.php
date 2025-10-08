<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250916211430 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE driver_preferences (id INT AUTO_INCREMENT NOT NULL, driver_id_id INT DEFAULT NULL, key_name VARCHAR(60) NOT NULL, value_text VARCHAR(120) NOT NULL, INDEX IDX_E5E3F947FFC6537A (driver_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE driver_preferences ADD CONSTRAINT FK_E5E3F947FFC6537A FOREIGN KEY (driver_id_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE driver_preferences DROP FOREIGN KEY FK_E5E3F947FFC6537A');
        $this->addSql('DROP TABLE driver_preferences');
    }
}
