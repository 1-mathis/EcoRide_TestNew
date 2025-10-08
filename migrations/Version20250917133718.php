<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250917133718 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix FKs order: vehicle.owner_id_id & user_roles.user_id_id; add vehicle.energy_id; drop old vehicle_energies.vehicle_id';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_roles DROP FOREIGN KEY FK_54FCD59F9D86650F');
        $this->addSql('ALTER TABLE user_roles CHANGE user_id_id user_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_roles ADD CONSTRAINT FK_54FCD59F9D86650F FOREIGN KEY (user_id_id) REFERENCES `user` (id)');

        $this->addSql('ALTER TABLE vehicle DROP FOREIGN KEY FK_1B80E4868FDDAB70');
        $this->addSql('ALTER TABLE vehicle CHANGE owner_id_id owner_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE vehicle ADD CONSTRAINT FK_1B80E4868FDDAB70 FOREIGN KEY (owner_id_id) REFERENCES `user` (id)');

        $this->addSql('ALTER TABLE vehicle ADD energy_id INT NOT NULL');
        $this->addSql('CREATE INDEX IDX_1B80E486EDDF52D ON vehicle (energy_id)');
        $this->addSql('ALTER TABLE vehicle ADD CONSTRAINT FK_1B80E486EDDF52D FOREIGN KEY (energy_id) REFERENCES vehicle_energies (id)');

        $this->addSql('ALTER TABLE vehicle_energies DROP FOREIGN KEY FK_10E8E4AD545317D1');
        $this->addSql('DROP INDEX IDX_10E8E4AD545317D1 ON vehicle_energies');
        $this->addSql('ALTER TABLE vehicle_energies DROP vehicle_id');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE vehicle_energies ADD vehicle_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_10E8E4AD545317D1 ON vehicle_energies (vehicle_id)');
        $this->addSql('ALTER TABLE vehicle_energies ADD CONSTRAINT FK_10E8E4AD545317D1 FOREIGN KEY (vehicle_id) REFERENCES vehicle (id)');

        $this->addSql('ALTER TABLE vehicle DROP FOREIGN KEY FK_1B80E486EDDF52D');
        $this->addSql('DROP INDEX IDX_1B80E486EDDF52D ON vehicle');
        $this->addSql('ALTER TABLE vehicle DROP energy_id');

        $this->addSql('ALTER TABLE vehicle DROP FOREIGN KEY FK_1B80E4868FDDAB70');
        $this->addSql('ALTER TABLE vehicle CHANGE owner_id_id owner_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vehicle ADD CONSTRAINT FK_1B80E4868FDDAB70 FOREIGN KEY (owner_id_id) REFERENCES `user` (id)');

        $this->addSql('ALTER TABLE user_roles DROP FOREIGN KEY FK_54FCD59F9D86650F');
        $this->addSql('ALTER TABLE user_roles CHANGE user_id_id user_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_roles ADD CONSTRAINT FK_54FCD59F9D86650F FOREIGN KEY (user_id_id) REFERENCES `user` (id)');
    }
}
