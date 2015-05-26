ALTER TABLE `operation_t` ADD `OP_AC_ID` VARCHAR( 30 ) NOT NULL AFTER `OP_ID`;

UPDATE `operation_t` SET `OP_AC_ID` = '5253 4511';

ALTER TABLE `operation_t` ADD INDEX ( `OP_AC_ID` ) ;

ALTER TABLE `operation_t` ADD FOREIGN KEY ( `OP_AC_ID` ) REFERENCES `account_t` (
`AC_ID`
) ON DELETE RESTRICT ON UPDATE CASCADE ;

INSERT INTO `operation_t` (	OP_AC_ID, OP_NAME, OP_LOGIN ) SELECT '0983 4930', OP_NAME, 'chris' FROM `operation_t` WHERE OP_AC_ID = '5253 4511' AND OP_ID <> 0;
INSERT INTO `operation_t` (	OP_AC_ID, OP_NAME, OP_LOGIN ) SELECT '0917 9995', OP_NAME, 'bubu' FROM `operation_t` WHERE OP_AC_ID = '5253 4511' AND OP_ID <> 0;
INSERT INTO `operation_t` (	OP_AC_ID, OP_NAME, OP_LOGIN ) SELECT '0992 2004 E', OP_NAME, 'chris' FROM `operation_t` WHERE OP_AC_ID = '5253 4511' AND OP_ID <> 0;
INSERT INTO `operation_t` (	OP_AC_ID, OP_NAME, OP_LOGIN ) SELECT '0992 2004 H', OP_NAME, 'chris' FROM `operation_t` WHERE OP_AC_ID = '5253 4511' AND OP_ID <> 0;
INSERT INTO `operation_t` (	OP_AC_ID, OP_NAME, OP_LOGIN ) SELECT '0992 2004 V', OP_NAME, 'chris' FROM `operation_t` WHERE OP_AC_ID = '5253 4511' AND OP_ID <> 0;

UPDATE transaction_t tr SET TR_OP_ID = (
			SELECT op1.OP_ID 
			FROM operation_t op1 
			WHERE op1.OP_AC_ID = tr.TR_AC_ID 
			AND op1.OP_NAME = (
				SELECT op2.OP_NAME
				FROM operation_t op2
				WHERE op2.OP_ID = tr.TR_OP_ID
			)
)
WHERE TR_OP_ID <> 0;

DELETE FROM operation_t WHERE OP_ID NOT IN (SELECT DISTINCT TR_OP_ID FROM transaction_t);