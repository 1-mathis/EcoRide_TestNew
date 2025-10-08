<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250916212343 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE car_sharings (id INT AUTO_INCREMENT NOT NULL, driver_id_id INT DEFAULT NULL, vehicle_id_id INT DEFAULT NULL, slug VARCHAR(160) NOT NULL, from_city VARCHAR(120) NOT NULL, to_city VARCHAR(120) NOT NULL, departure_at DATETIME NOT NULL, arrival_at DATETIME NOT NULL, duration_minutes INT NOT NULL, price NUMERIC(7, 2) NOT NULL, seats_total INT NOT NULL, seats_remaining INT NOT NULL, is_eco TINYINT(1) NOT NULL, status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_D7FF9289FFC6537A (driver_id_id), INDEX IDX_D7FF92891DEB1EBB (vehicle_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE car_sharings ADD CONSTRAINT FK_D7FF9289FFC6537A FOREIGN KEY (driver_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE car_sharings ADD CONSTRAINT FK_D7FF92891DEB1EBB FOREIGN KEY (vehicle_id_id) REFERENCES vehicle (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE car_sharings DROP FOREIGN KEY FK_D7FF9289FFC6537A');
        $this->addSql('ALTER TABLE car_sharings DROP FOREIGN KEY FK_D7FF92891DEB1EBB');
        $this->addSql('DROP TABLE car_sharings');
    }
}
