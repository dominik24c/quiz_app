<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210322211629 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE answer (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL, answer VARCHAR(100) NOT NULL, is_correct TINYINT(1) NOT NULL, INDEX IDX_DADD4A251E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (name VARCHAR(40) NOT NULL, PRIMARY KEY(name)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE question (id INT AUTO_INCREMENT NOT NULL, quiz_id INT DEFAULT NULL, question VARCHAR(255) NOT NULL, points INT NOT NULL, INDEX IDX_B6F7494E853CD175 (quiz_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quiz (id INT AUTO_INCREMENT NOT NULL, category VARCHAR(40) DEFAULT NULL, user_id INT DEFAULT NULL, title VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, created_at DATETIME NOT NULL, expired_at DATETIME NOT NULL, INDEX IDX_A412FA9264C19C1 (category), INDEX IDX_A412FA92A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE solution (id INT AUTO_INCREMENT NOT NULL, quiz_id INT DEFAULT NULL, user_id INT DEFAULT NULL, points INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_9F3329DB853CD175 (quiz_id), INDEX IDX_9F3329DBA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE solution_answer (solution_id INT NOT NULL, answer_id INT NOT NULL, INDEX IDX_A92B9D431C0BE183 (solution_id), INDEX IDX_A92B9D43AA334807 (answer_id), PRIMARY KEY(solution_id, answer_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(40) NOT NULL, last_name VARCHAR(40) NOT NULL, nick VARCHAR(50) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649290B2F37 (nick), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A251E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494E853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA9264C19C1 FOREIGN KEY (category) REFERENCES category (name)');
        $this->addSql('ALTER TABLE quiz ADD CONSTRAINT FK_A412FA92A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE solution ADD CONSTRAINT FK_9F3329DB853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id)');
        $this->addSql('ALTER TABLE solution ADD CONSTRAINT FK_9F3329DBA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE solution_answer ADD CONSTRAINT FK_A92B9D431C0BE183 FOREIGN KEY (solution_id) REFERENCES solution (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE solution_answer ADD CONSTRAINT FK_A92B9D43AA334807 FOREIGN KEY (answer_id) REFERENCES answer (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE solution_answer DROP FOREIGN KEY FK_A92B9D43AA334807');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA9264C19C1');
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A251E27F6BF');
        $this->addSql('ALTER TABLE question DROP FOREIGN KEY FK_B6F7494E853CD175');
        $this->addSql('ALTER TABLE solution DROP FOREIGN KEY FK_9F3329DB853CD175');
        $this->addSql('ALTER TABLE solution_answer DROP FOREIGN KEY FK_A92B9D431C0BE183');
        $this->addSql('ALTER TABLE quiz DROP FOREIGN KEY FK_A412FA92A76ED395');
        $this->addSql('ALTER TABLE solution DROP FOREIGN KEY FK_9F3329DBA76ED395');
        $this->addSql('DROP TABLE answer');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE question');
        $this->addSql('DROP TABLE quiz');
        $this->addSql('DROP TABLE solution');
        $this->addSql('DROP TABLE solution_answer');
        $this->addSql('DROP TABLE `user`');
    }
}
