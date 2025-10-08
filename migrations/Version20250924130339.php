<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250924130339 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bookings DROP FOREIGN KEY FK_7A853C354502E565');
        $this->addSql('ALTER TABLE bookings DROP FOREIGN KEY FK_7A853C35D474C7E3');
        $this->addSql('ALTER TABLE bookings CHANGE status status VARCHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE bookings ADD CONSTRAINT FK_7A853C354502E565 FOREIGN KEY (passenger_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bookings ADD CONSTRAINT FK_7A853C35D474C7E3 FOREIGN KEY (car_sharing_id_id) REFERENCES car_sharings (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bookings DROP FOREIGN KEY FK_7A853C35D474C7E3');
        $this->addSql('ALTER TABLE bookings DROP FOREIGN KEY FK_7A853C354502E565');
        $this->addSql('ALTER TABLE bookings CHANGE status status VARCHAR(20) NOT NULL');
        $this->addSql('ALTER TABLE bookings ADD CONSTRAINT FK_7A853C35D474C7E3 FOREIGN KEY (car_sharing_id_id) REFERENCES car_sharings (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE bookings ADD CONSTRAINT FK_7A853C354502E565 FOREIGN KEY (passenger_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
