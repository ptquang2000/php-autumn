USE demo;
CREATE TABLE employee (
    eid INT AUTO_INCREMENT,
    name VARCHAR(255),
    CONSTRAINT pk_eid PRIMARY KEY (eid)
);
CREATE TABLE customer (
    cid INT AUTO_INCREMENT,
    name VARCHAR(255),
    eid INT,
    CONSTRAINT pk_cid PRIMARY KEY (cid),
    CONSTRAINT fk_eid FOREIGN KEY (eid) REFERENCES employee (eid)
);