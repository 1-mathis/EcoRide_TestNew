<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250923132704 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute passenger_id et rend car_sharing_id_id NOT NULL en recréant proprement les FKs.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bookings DROP FOREIGN KEY FK_7A853C35D474C7E3');

        $this->addSql('DROP INDEX IDX_7A853C35D474C7E3 ON bookings');

        $this->addSql('ALTER TABLE bookings CHANGE car_sharing_id_id car_sharing_id_id INT NOT NULL');

        $this->addSql('ALTER TABLE bookings ADD passenger_id INT NOT NULL AFTER car_sharing_id_id');
        $this->addSql('CREATE INDEX IDX_7A853C354502E565 ON bookings (passenger_id)');
        $this->addSql('ALTER TABLE bookings ADD CONSTRAINT FK_7A853C354502E565 FOREIGN KEY (passenger_id) REFERENCES user (id)');

        $this->addSql('CREATE INDEX IDX_7A853C35D474C7E3 ON bookings (car_sharing_id_id)');
        $this->addSql('ALTER TABLE bookings ADD CONSTRAINT FK_7A853C35D474C7E3 FOREIGN KEY (car_sharing_id_id) REFERENCES car_sharings (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bookings DROP FOREIGN KEY FK_7A853C354502E565');
        $this->addSql('ALTER TABLE bookings DROP FOREIGN KEY FK_7A853C35D474C7E3');

        $this->addSql('DROP INDEX IDX_7A853C354502E565 ON bookings');
        $this->addSql('DROP INDEX IDX_7A853C35D474C7E3 ON bookings');

        $this->addSql('ALTER TABLE bookings DROP passenger_id, CHANGE car_sharing_id_id car_sharing_id_id INT DEFAULT NULL');

        $this->addSql('CREATE INDEX IDX_7A853C35D474C7E3 ON bookings (car_sharing_id_id)');
        $this->addSql('ALTER TABLE bookings ADD CONSTRAINT FK_7A853C35D474C7E3 FOREIGN KEY (car_sharing_id_id) REFERENCES car_sharings (id)');
    }
}
