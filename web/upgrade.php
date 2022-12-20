<?php
exit;
require_once('uppa_core/functions.php');

$mysqli = createDatabaseConnection();

$sql = "CREATE TABLE `department_info` (
    `rid` int(11) NOT NULL AUTO_INCREMENT,
    `dptid` varchar(30) NOT NULL,
    `dpt_full_name` varchar(300) NOT NULL,
    `valid_from` datetime NOT NULL,
    `valid_until` datetime DEFAULT NULL,
    PRIMARY KEY (`rid`),
    KEY `fk_dptid` (`dptid`),
    CONSTRAINT `fk_dptid` FOREIGN KEY (`dptid`) REFERENCES `department` (`dpt_id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

if ($result = $mysqli -> query($sql)) {

    $sql = "SELECT * FROM department";
    if ($result = $mysqli -> query($sql)) {
        while ($row = $result -> fetch_assoc()) {
            $mysqli->query("INSERT INTO department_info (dptid, dpt_full_name, valid_from) 
                VALUES ('".$row['dpt_id']."', '".$row['dpt_full_name']."', '2022-01-01 00:00:00')");
        }
    }

    $mysqli->query("ALTER TABLE `department` DROP COLUMN `dpt_full_name`");

    echo "Success";

} else {
    echo "Error creating department_info table"; 
}

$sql = "UPDATE `rank` SET rank_order_id = 6 WHERE rank_id = 'no_rank'";
$mysqli->query($sql);

$sql = "INSERT INTO `rank` (`rank_id`, `rank_full_title`, `rank_short_title`, `rank_order_id`) VALUES ('edip', 'ΕΔΙΠ', 'ΕΔΙΠ', 5)";
$mysqli->query($sql);
