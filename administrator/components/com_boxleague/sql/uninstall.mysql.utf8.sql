DROP TABLE IF EXISTS `#__boxleague_boxleague`;
DROP TABLE IF EXISTS `#__boxleague_box`;
DROP TABLE IF EXISTS `#__boxleague_player`;
DROP TABLE IF EXISTS `#__boxleague_match`;

DELETE FROM `#__content_types` WHERE (type_alias LIKE 'com_boxleague.%');