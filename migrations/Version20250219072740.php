<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250219072740 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE input_url (id SERIAL NOT NULL, initial_route_step_id INT DEFAULT NULL, url_pattern VARCHAR(2048) NOT NULL, priority INT NOT NULL, is_active BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F02418C6AE4D81B0 ON input_url (initial_route_step_id)');
        $this->addSql('CREATE TABLE route_step (id SERIAL NOT NULL, input_url_id INT DEFAULT NULL, on_pass_step_id INT DEFAULT NULL, on_decline_step_id INT DEFAULT NULL, scheme_type VARCHAR(255) NOT NULL, scheme_props JSON NOT NULL, discr VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EEFCFFB94D7B53BA ON route_step (input_url_id)');
        $this->addSql('CREATE INDEX IDX_EEFCFFB9AB41947B ON route_step (on_pass_step_id)');
        $this->addSql('CREATE INDEX IDX_EEFCFFB9CC1B8D73 ON route_step (on_decline_step_id)');
        $this->addSql('ALTER TABLE input_url ADD CONSTRAINT FK_F02418C6AE4D81B0 FOREIGN KEY (initial_route_step_id) REFERENCES route_step (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE route_step ADD CONSTRAINT FK_EEFCFFB94D7B53BA FOREIGN KEY (input_url_id) REFERENCES input_url (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE route_step ADD CONSTRAINT FK_EEFCFFB9AB41947B FOREIGN KEY (on_pass_step_id) REFERENCES route_step (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE route_step ADD CONSTRAINT FK_EEFCFFB9CC1B8D73 FOREIGN KEY (on_decline_step_id) REFERENCES route_step (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE input_url DROP CONSTRAINT FK_F02418C6AE4D81B0');
        $this->addSql('ALTER TABLE route_step DROP CONSTRAINT FK_EEFCFFB94D7B53BA');
        $this->addSql('ALTER TABLE route_step DROP CONSTRAINT FK_EEFCFFB9AB41947B');
        $this->addSql('ALTER TABLE route_step DROP CONSTRAINT FK_EEFCFFB9CC1B8D73');
        $this->addSql('DROP TABLE input_url');
        $this->addSql('DROP TABLE route_step');
    }
}
