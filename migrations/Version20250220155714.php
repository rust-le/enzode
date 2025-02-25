<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250220155714 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'entity creation';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE attributes (id UUID NOT NULL, code VARCHAR(3) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_319B9E7077153098 ON attributes (code)');
        $this->addSql('COMMENT ON COLUMN attributes.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE categories (id UUID NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3AF346685E237E06 ON categories (name)');
        $this->addSql('COMMENT ON COLUMN categories.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE currencies (id UUID NOT NULL, code VARCHAR(3) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_37C4469377153098 ON currencies (code)');
        $this->addSql('COMMENT ON COLUMN currencies.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE product_attributes (id UUID NOT NULL, product_id UUID NOT NULL, attribute_id UUID NOT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A2FCC15B4584665A ON product_attributes (product_id)');
        $this->addSql('CREATE INDEX IDX_A2FCC15BB6E62EFA ON product_attributes (attribute_id)');
        $this->addSql('COMMENT ON COLUMN product_attributes.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN product_attributes.product_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN product_attributes.attribute_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE products (id UUID NOT NULL, currency_id UUID NOT NULL, category_id UUID NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, price NUMERIC(10, 2) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B3BA5A5A38248176 ON products (currency_id)');
        $this->addSql('CREATE INDEX IDX_B3BA5A5A12469DE2 ON products (category_id)');
        $this->addSql('COMMENT ON COLUMN products.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN products.currency_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN products.category_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN products.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE product_attributes ADD CONSTRAINT FK_A2FCC15B4584665A FOREIGN KEY (product_id) REFERENCES products (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_attributes ADD CONSTRAINT FK_A2FCC15BB6E62EFA FOREIGN KEY (attribute_id) REFERENCES attributes (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A38248176 FOREIGN KEY (currency_id) REFERENCES currencies (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5A12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE product_attributes DROP CONSTRAINT FK_A2FCC15B4584665A');
        $this->addSql('ALTER TABLE product_attributes DROP CONSTRAINT FK_A2FCC15BB6E62EFA');
        $this->addSql('ALTER TABLE products DROP CONSTRAINT FK_B3BA5A5A38248176');
        $this->addSql('ALTER TABLE products DROP CONSTRAINT FK_B3BA5A5A12469DE2');
        $this->addSql('DROP TABLE attributes');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE currencies');
        $this->addSql('DROP TABLE product_attributes');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
