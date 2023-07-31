ALTER TABLE `bins` ADD `card_total` MEDIUMINT NOT NULL AFTER `OtherCondition` ,
ADD INDEX ( `card_total` ) ;
ALTER TABLE `bins` ADD `card_refund` MEDIUMINT NOT NULL AFTER `card_total` ,
ADD INDEX ( `card_refund` ) ;
ALTER TABLE `bins` ADD `card_valid` MEDIUMINT NOT NULL AFTER `card_refund` ,
ADD INDEX ( `card_valid` ) ;
-- Convert total card
CREATE TABLE cards_total SELECT card_bin, count( * ) AS total
FROM `cards`
WHERE 1 GROUP BY card_bin;
-- Update total cards to card_total of bins table
UPDATE bins b, `cards_total` ct SET b.card_total=ct.total,b.card_valid=ct.total WHERE b.card_bin=ct.card_bin;
-- Delete cards_total
DROP TABLE cards_total;

-- Convert total invalid
CREATE TABLE cards_total SELECT card_bin, count( * ) AS total
FROM `cards`
WHERE card_check IN(2,4) GROUP BY card_bin;
-- Update total cards to card_total of bins table
UPDATE bins b, `cards_total` ct SET b.card_valid=b.card_valid-ct.total WHERE b.card_bin=ct.card_bin;
-- Delete cards_total
DROP TABLE cards_total;

-- Convert update refund
CREATE TABLE cards_total SELECT card_bin, count( * ) AS total
FROM `cards`
WHERE card_check IN(3,5) GROUP BY card_bin;
-- Update total cards to card_total of bins table
UPDATE bins b, `cards_total` ct SET b.card_refund=ct.total WHERE b.card_bin=ct.card_bin;
-- Delete cards_total
DROP TABLE cards_total;