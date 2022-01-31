USE demo;
CREATE TABLE employee (
    eid INT AUTO_INCREMENT,
    name VARCHAR(255),
    CONSTRAINT pk_eid PRIMARY KEY (eid)
);
INSERT INTO employee (name) VALUES ('Karl');
INSERT INTO employee (name) VALUES ('Matti');
INSERT INTO employee (name) VALUES ('Wolski');