<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221119045120 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE usuario_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE usuario (id INT NOT NULL, userr VARCHAR(100) DEFAULT NULL, password VARCHAR(150) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE metodo_pago ADD usuario_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE metodo_pago ADD CONSTRAINT FK_8A0E8868DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8A0E8868DB38439E ON metodo_pago (usuario_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE metodo_pago DROP CONSTRAINT FK_8A0E8868DB38439E');
        $this->addSql('DROP SEQUENCE usuario_id_seq CASCADE');
        $this->addSql('DROP TABLE usuario');
        $this->addSql('DROP INDEX IDX_8A0E8868DB38439E');
        $this->addSql('ALTER TABLE metodo_pago DROP usuario_id');
    }
}
