<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261101062436 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cliente (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(100) NOT NULL, apellido VARCHAR(100) NOT NULL, dni VARCHAR(20) NOT NULL, telefono VARCHAR(30) NOT NULL, email VARCHAR(150) NOT NULL, UNIQUE INDEX UNIQ_F41C9B257F8F253B (dni), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE contrato (id INT AUTO_INCREMENT NOT NULL, fecha_inicio DATE NOT NULL, fecha_fin DATE NOT NULL, precio_mensual INT NOT NULL, estado VARCHAR(20) NOT NULL, propiedad_id INT NOT NULL, inquilino_id INT NOT NULL, INDEX IDX_6669652325C78794 (propiedad_id), INDEX IDX_666965231F3DB43C (inquilino_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE propiedad (id INT AUTO_INCREMENT NOT NULL, direccion VARCHAR(150) NOT NULL, tipo VARCHAR(30) NOT NULL, ambientes INT NOT NULL, precio INT NOT NULL, estado VARCHAR(20) NOT NULL, imagen VARCHAR(255) DEFAULT NULL, propietario_id INT NOT NULL, INDEX IDX_6F3EFE0D53C8D32C (propietario_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE usuario (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nombre VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id))');
        $this->addSql('ALTER TABLE contrato ADD CONSTRAINT FK_6669652325C78794 FOREIGN KEY (propiedad_id) REFERENCES propiedad (id)');
        $this->addSql('ALTER TABLE contrato ADD CONSTRAINT FK_666965231F3DB43C FOREIGN KEY (inquilino_id) REFERENCES cliente (id)');
        $this->addSql('ALTER TABLE propiedad ADD CONSTRAINT FK_6F3EFE0D53C8D32C FOREIGN KEY (propietario_id) REFERENCES cliente (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contrato DROP FOREIGN KEY FK_6669652325C78794');
        $this->addSql('ALTER TABLE contrato DROP FOREIGN KEY FK_666965231F3DB43C');
        $this->addSql('ALTER TABLE propiedad DROP FOREIGN KEY FK_6F3EFE0D53C8D32C');
        $this->addSql('DROP TABLE cliente');
        $this->addSql('DROP TABLE contrato');
        $this->addSql('DROP TABLE propiedad');
        $this->addSql('DROP TABLE usuario');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
