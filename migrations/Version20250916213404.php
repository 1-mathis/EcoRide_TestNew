<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250916213404 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE passenger_confirmations (id INT AUTO_INCREMENT NOT NULL, booking_id_id INT DEFAULT NULL, token VARCHAR(64) NOT NULL, status VARCHAR(20) NOT NULL, confirmed_at DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_4D2BE671EE3863E2 (booking_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE passenger_confirmations ADD CONSTRAINT FK_4D2BE671EE3863E2 FOREIGN KEY (booking_id_id) REFERENCES bookings (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE passenger_confirmations DROP FOREIGN KEY FK_4D2BE671EE3863E2');
        $this->addSql('DROP TABLE passenger_confirmations');
    }
}
