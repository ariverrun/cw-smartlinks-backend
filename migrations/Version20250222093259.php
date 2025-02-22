<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250222093259 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE route (id SERIAL NOT NULL, initial_step_id INT DEFAULT NULL, url_pattern VARCHAR(2048) NOT NULL, priority INT NOT NULL, is_active BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2C42079C7C07434 ON route (initial_step_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2C420791ECFAAD2 ON route (url_pattern)');
        $this->addSql('CREATE TABLE routing_step (id SERIAL NOT NULL, route_id INT NOT NULL, on_pass_step_id INT DEFAULT NULL, on_decline_step_id INT DEFAULT NULL, scheme_type VARCHAR(255) NOT NULL, scheme_props JSON NOT NULL, discr VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_14C5AD0F34ECB4E6 ON routing_step (route_id)');
        $this->addSql('CREATE INDEX IDX_14C5AD0FAB41947B ON routing_step (on_pass_step_id)');
        $this->addSql('CREATE INDEX IDX_14C5AD0FCC1B8D73 ON routing_step (on_decline_step_id)');
        $this->addSql('ALTER TABLE route ADD CONSTRAINT FK_2C42079C7C07434 FOREIGN KEY (initial_step_id) REFERENCES routing_step (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE routing_step ADD CONSTRAINT FK_14C5AD0F34ECB4E6 FOREIGN KEY (route_id) REFERENCES route (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE routing_step ADD CONSTRAINT FK_14C5AD0FAB41947B FOREIGN KEY (on_pass_step_id) REFERENCES routing_step (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE routing_step ADD CONSTRAINT FK_14C5AD0FCC1B8D73 FOREIGN KEY (on_decline_step_id) REFERENCES routing_step (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE route DROP CONSTRAINT FK_2C42079C7C07434');
        $this->addSql('ALTER TABLE routing_step DROP CONSTRAINT FK_14C5AD0F34ECB4E6');
        $this->addSql('ALTER TABLE routing_step DROP CONSTRAINT FK_14C5AD0FAB41947B');
        $this->addSql('ALTER TABLE routing_step DROP CONSTRAINT FK_14C5AD0FCC1B8D73');
        $this->addSql('DROP TABLE route');
        $this->addSql('DROP TABLE routing_step');
    }
}
