DROP TABLE IF EXISTS providers;
CREATE TABLE providers
(
    id   INT UNSIGNED AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    CONSTRAINT providers_pk
        PRIMARY KEY (id)
);

INSERT INTO providers (id, name) VALUES (1, 'plurimedia');
INSERT INTO providers (id, name) VALUES (2, 'tucano-plurimedia');
INSERT INTO providers (id, name) VALUES (3, 'vodafone-es');
