<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250916214531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE moderations_actions (id INT AUTO_INCREMENT NOT NULL, actor_id_id INT DEFAULT NULL, target_type VARCHAR(20) NOT NULL, target_id INT NOT NULL, action VARCHAR(40) NOT NULL, details VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_755EAC3B5BC075C3 (actor_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE moderations_actions ADD CONSTRAINT FK_755EAC3B5BC075C3 FOREIGN KEY (actor_id_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE moderations_actions DROP FOREIGN KEY FK_755EAC3B5BC075C3');
        $this->addSql('DROP TABLE moderations_actions');
    }
}
