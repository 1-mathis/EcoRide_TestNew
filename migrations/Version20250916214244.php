<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250916214244 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE trip_reports (id INT AUTO_INCREMENT NOT NULL, car_sharings_id_id INT DEFAULT NULL, reporter_id_id INT DEFAULT NULL, driver_id_id INT DEFAULT NULL, handled_by_id INT DEFAULT NULL, reason LONGTEXT NOT NULL, status VARCHAR(20) NOT NULL, handle_at DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_C7F3E2E831C666EF (car_sharings_id_id), INDEX IDX_C7F3E2E8D6B1FFA1 (reporter_id_id), INDEX IDX_C7F3E2E8FFC6537A (driver_id_id), INDEX IDX_C7F3E2E8FE65AF40 (handled_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE trip_reports ADD CONSTRAINT FK_C7F3E2E831C666EF FOREIGN KEY (car_sharings_id_id) REFERENCES car_sharings (id)');
        $this->addSql('ALTER TABLE trip_reports ADD CONSTRAINT FK_C7F3E2E8D6B1FFA1 FOREIGN KEY (reporter_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE trip_reports ADD CONSTRAINT FK_C7F3E2E8FFC6537A FOREIGN KEY (driver_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE trip_reports ADD CONSTRAINT FK_C7F3E2E8FE65AF40 FOREIGN KEY (handled_by_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE trip_reports DROP FOREIGN KEY FK_C7F3E2E831C666EF');
        $this->addSql('ALTER TABLE trip_reports DROP FOREIGN KEY FK_C7F3E2E8D6B1FFA1');
        $this->addSql('ALTER TABLE trip_reports DROP FOREIGN KEY FK_C7F3E2E8FFC6537A');
        $this->addSql('ALTER TABLE trip_reports DROP FOREIGN KEY FK_C7F3E2E8FE65AF40');
        $this->addSql('DROP TABLE trip_reports');
    }
}
