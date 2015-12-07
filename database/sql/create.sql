CREATE TABLE organization_industry_info (
    catcode VARCHAR(20) NOT NULL PRIMARY KEY,
    catname VARCHAR(120) NOT NULL,
    catorder VARCHAR(20) NOT NULL,
    industry VARCHAR(120) NOT NULL,
    sector VARCHAR(120) NOT NULL,
    sector_long VARCHAR(120) NOT NULL
);

CREATE TABLE catcode_positions (
    catcode VARCHAR(20) NOT NULL,
    billID VARCHAR(20) NOT NULL,
    interest_position VARCHAR(20) NOT NULL,
    passed VARCHAR(20) NOT NULL,
    UNIQUE(catcode, billID)
);

CREATE TABLE organizations_in_positions (
    org_id INTEGER NOT NULL PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    catcode VARCHAR(20)
);

CREATE TABLE positions (
    bill_id VARCHAR(20) NOT NULL REFERENCES bills(id),
    org_id INTEGER NOT NULL REFERENCES organizations_in_positions(org_id),
    disposition VARCHAR(20) NOT NULL
);

CREATE TABLE senate_contributions (
    catcode VARCHAR(20) NOT NULL,
    senator_first VARCHAR(20) NOT NULL,
    senator_last VARCHAR(20) NOT NULL,
    contribution_amount INTEGER NOT NULL,
    UNIQUE(catcode, senator_first, senator_last)
);

CREATE TABLE sponsors (
    sponsor_id INTEGER NOT NULL REFERENCES persons(id_govtrack),
    bill_id CHAR(20) NOT NULL REFERENCES bills(id)
);
CREATE TABLE cosponsors (
    cosponsor_id INTEGER NOT NULL REFERENCES persons(id_govtrack),
    bill_id CHAR(20) NOT NULL REFERENCES bills(id)
);