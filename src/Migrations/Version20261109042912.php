<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261109042912 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE solicitud (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(100) NOT NULL, apellido VARCHAR(100) NOT NULL, dni VARCHAR(20) NOT NULL, telefono VARCHAR(30) NOT NULL, email VARCHAR(150) NOT NULL, fecha_inicio DATE NOT NULL, fecha_fin DATE NOT NULL, estado VARCHAR(20) NOT NULL, propiedad_id INT NOT NULL, INDEX IDX_96D27CC025C78794 (propiedad_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE solicitud ADD CONSTRAINT FK_96D27CC025C78794 FOREIGN KEY (propiedad_id) REFERENCES propiedad (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE solicitud DROP FOREIGN KEY FK_96D27CC025C78794');
        $this->addSql('DROP TABLE solicitud');
    }
}
