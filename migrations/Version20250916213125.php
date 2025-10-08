<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250916213125 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE credits_ledger (id INT AUTO_INCREMENT NOT NULL, user_id_id INT DEFAULT NULL, direction VARCHAR(10) NOT NULL, amount INT NOT NULL, reason VARCHAR(80) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_AD1553099D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE credits_ledger ADD CONSTRAINT FK_AD1553099D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE car_sharings ADD credits_ledger_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE car_sharings ADD CONSTRAINT FK_D7FF928995DB146E FOREIGN KEY (credits_ledger_id) REFERENCES credits_ledger (id)');
        $this->addSql('CREATE INDEX IDX_D7FF928995DB146E ON car_sharings (credits_ledger_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE car_sharings DROP FOREIGN KEY FK_D7FF928995DB146E');
        $this->addSql('ALTER TABLE credits_ledger DROP FOREIGN KEY FK_AD1553099D86650F');
        $this->addSql('DROP TABLE credits_ledger');
        $this->addSql('DROP INDEX IDX_D7FF928995DB146E ON car_sharings');
        $this->addSql('ALTER TABLE car_sharings DROP credits_ledger_id');
    }
}
