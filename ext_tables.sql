CREATE TABLE tt_content (
    tx_hhextaz_records text,
    tx_hhextaz_categories text,
    tx_hhextaz_category_conjunction varchar(8) DEFAULT 'or' NOT NULL,
    tx_hhextaz_sort_field varchar(32) DEFAULT 'sorting' NOT NULL,
    tx_hhextaz_sort_order varchar(4) DEFAULT 'asc' NOT NULL,
);

CREATE TABLE tx_hhextaz_domain_model_entry (
    record_type varchar(32) DEFAULT 'default' NOT NULL,
    title varchar(255) DEFAULT '' NOT NULL,
    slug varchar(2048) DEFAULT '' NOT NULL,
    teaser text,
    description text,
    image int(11) unsigned DEFAULT '0' NOT NULL,
    link varchar(1024) DEFAULT '' NOT NULL,
    categories int(11) unsigned DEFAULT '0' NOT NULL
);
