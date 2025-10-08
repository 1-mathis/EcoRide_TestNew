<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250917131146 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rendre suspended_at et suspended_reason NULLABLE';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            ALTER TABLE user
              CHANGE suspended_at suspended_at DATETIME DEFAULT NULL,
              CHANGE suspended_reason suspended_reason LONGTEXT DEFAULT NULL
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("
            ALTER TABLE user
              CHANGE suspended_at suspended_at DATETIME NOT NULL,
              CHANGE suspended_reason suspended_reason LONGTEXT NOT NULL
        ");
    }
}
