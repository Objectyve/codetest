
CREATE TABLE `address` (
  `id` int(11) NOT NULL,
  `street` varchar(255) CHARACTER SET latin1 NOT NULL,
  `city` varchar(32) CHARACTER SET latin1 NOT NULL,
  `state` varchar(32) CHARACTER SET latin1 NOT NULL,
  `postalcode` varchar(32) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci