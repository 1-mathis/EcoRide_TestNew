<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250916211246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE vehicle (id INT AUTO_INCREMENT NOT NULL, owner_id_id INT DEFAULT NULL, slug VARCHAR(120) NOT NULL, brand VARCHAR(60) NOT NULL, model VARCHAR(60) NOT NULL, color VARCHAR(20) NOT NULL, seats INT NOT NULL, plate VARCHAR(20) NOT NULL, first_registration DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_1B80E4868FDDAB70 (owner_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE vehicle ADD CONSTRAINT FK_1B80E4868FDDAB70 FOREIGN KEY (owner_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE vehicle_energies ADD vehicle_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vehicle_energies ADD CONSTRAINT FK_10E8E4AD545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id)');
        $this->addSql('CREATE INDEX IDX_10E8E4AD545317D1 ON vehicle_energies (vehicle_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vehicle_energies DROP FOREIGN KEY FK_10E8E4AD545317D1');
        $this->addSql('ALTER TABLE vehicle DROP FOREIGN KEY FK_1B80E4868FDDAB70');
        $this->addSql('DROP TABLE vehicle');
        $this->addSql('DROP INDEX IDX_10E8E4AD545317D1 ON vehicle_energies');
        $this->addSql('ALTER TABLE vehicle_energies DROP vehicle_id');
    }
}
