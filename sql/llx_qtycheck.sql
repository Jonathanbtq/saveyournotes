CREATE TABLE llx_qtycheck
(
    rowid INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    datec DATETIME,
    expression varchar(255) NOT NULL,
    fk_product INT NOT NULL,
    fk_object INT NOT NULL,
    type_object varchar(255),
    import_key TEXT,
    tms TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_product_qtycheck_product
        FOREIGN KEY (fk_product) REFERENCES llx_product (rowid) ON UPDATE CASCADE,
);