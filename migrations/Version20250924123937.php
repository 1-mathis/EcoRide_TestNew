<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250924123937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rend booking_id_id NOT NULL, dates NULLables, ajoute comment; drop/recreate FK proprement.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE passenger_confirmations DROP FOREIGN KEY FK_4D2BE671EE3863E2');

        $this->addSql('ALTER TABLE passenger_confirmations 
            ADD comment LONGTEXT DEFAULT NULL,
            CHANGE booking_id_id booking_id_id INT NOT NULL,
            CHANGE confirmed_at confirmed_at DATETIME DEFAULT NULL,
            CHANGE created_at created_at DATETIME DEFAULT NULL,
            CHANGE updated_at updated_at DATETIME DEFAULT NULL
        ');

        $this->addSql('ALTER TABLE passenger_confirmations 
            ADD CONSTRAINT FK_4D2BE671EE3863E2 FOREIGN KEY (booking_id_id) REFERENCES bookings (id) ON DELETE CASCADE
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE passenger_confirmations DROP FOREIGN KEY FK_4D2BE671EE3863E2');

        $this->addSql('ALTER TABLE passenger_confirmations 
            DROP comment,
            CHANGE booking_id_id booking_id_id INT DEFAULT NULL,
            CHANGE confirmed_at confirmed_at DATETIME NOT NULL,
            CHANGE created_at created_at DATETIME NOT NULL,
            CHANGE updated_at updated_at DATETIME NOT NULL
        ');

        $this->addSql('ALTER TABLE passenger_confirmations 
            ADD CONSTRAINT FK_4D2BE671EE3863E2 FOREIGN KEY (booking_id_id) REFERENCES bookings (id)
        ');
    }
}
