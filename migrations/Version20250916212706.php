<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250916212706 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bookings (id INT AUTO_INCREMENT NOT NULL, car_sharing_id_id INT DEFAULT NULL, seats_booked INT NOT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_7A853C35D474C7E3 (car_sharing_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bookings ADD CONSTRAINT FK_7A853C35D474C7E3 FOREIGN KEY (car_sharing_id_id) REFERENCES car_sharings (id)');
        $this->addSql('ALTER TABLE user ADD bookings_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649AAC0EC61 FOREIGN KEY (bookings_id) REFERENCES bookings (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649AAC0EC61 ON user (bookings_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649AAC0EC61');
        $this->addSql('ALTER TABLE bookings DROP FOREIGN KEY FK_7A853C35D474C7E3');
        $this->addSql('DROP TABLE bookings');
        $this->addSql('DROP INDEX IDX_8D93D649AAC0EC61 ON user');
        $this->addSql('ALTER TABLE user DROP bookings_id');
    }
}
