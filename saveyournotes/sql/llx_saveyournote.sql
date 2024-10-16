create table llx_saveyournotes
(
    rowid INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    datec DATETIME,
    note TEXT NOT NULL,
    fk_object INT NOT NULL,
    type_object TEXT,
    tms TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
