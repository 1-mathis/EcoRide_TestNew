<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250916213846 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reviews (id INT AUTO_INCREMENT NOT NULL, driver_id_id INT DEFAULT NULL, author_id_id INT DEFAULT NULL, car_sharings_id_id INT DEFAULT NULL, reviewed_by_id INT DEFAULT NULL, rating INT NOT NULL, comment LONGTEXT NOT NULL, status VARCHAR(20) NOT NULL, reviewed_at DATETIME NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_6970EB0FFFC6537A (driver_id_id), INDEX IDX_6970EB0F69CCBE9A (author_id_id), INDEX IDX_6970EB0F31C666EF (car_sharings_id_id), INDEX IDX_6970EB0FFC6B21F1 (reviewed_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0FFFC6537A FOREIGN KEY (driver_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0F69CCBE9A FOREIGN KEY (author_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0F31C666EF FOREIGN KEY (car_sharings_id_id) REFERENCES car_sharings (id)');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0FFC6B21F1 FOREIGN KEY (reviewed_by_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reviews DROP FOREIGN KEY FK_6970EB0FFFC6537A');
        $this->addSql('ALTER TABLE reviews DROP FOREIGN KEY FK_6970EB0F69CCBE9A');
        $this->addSql('ALTER TABLE reviews DROP FOREIGN KEY FK_6970EB0F31C666EF');
        $this->addSql('ALTER TABLE reviews DROP FOREIGN KEY FK_6970EB0FFC6B21F1');
        $this->addSql('DROP TABLE reviews');
    }
}
