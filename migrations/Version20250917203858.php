<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250917203858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix: driver_id_id NOT NULL + dates en DATETIME + recréation de la contrainte FK.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE car_sharings DROP FOREIGN KEY FK_D7FF9289FFC6537A');

        $this->addSql('ALTER TABLE car_sharings 
            MODIFY departure_at DATETIME NOT NULL,
            MODIFY arrival_at DATETIME NOT NULL,
            MODIFY created_at DATETIME NOT NULL,
            MODIFY updated_at DATETIME NOT NULL
        ');

        $this->addSql('ALTER TABLE car_sharings 
            MODIFY driver_id_id INT NOT NULL
        ');

        $this->addSql('ALTER TABLE car_sharings 
            ADD CONSTRAINT FK_D7FF9289FFC6537A 
            FOREIGN KEY (driver_id_id) REFERENCES `user` (id)
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE car_sharings DROP FOREIGN KEY FK_D7FF9289FFC6537A');

        $this->addSql('ALTER TABLE car_sharings 
            MODIFY driver_id_id INT DEFAULT NULL
        ');

        $this->addSql('ALTER TABLE car_sharings 
            ADD CONSTRAINT FK_D7FF9289FFC6537A 
            FOREIGN KEY (driver_id_id) REFERENCES `user` (id)
        ');
    }
}
