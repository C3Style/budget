CREATE OR REPLACE VIEW _year_report_v AS (
	SELECT 	RE_YEAR,
			RE_MONTH,
			OP_ID,
			AC_ID,
			TY_ID,
			ROUND(SUM(TR_AMOUNT), 2) As TR_AMOUNT
	FROM 		transaction_t, 
				operation_t, 
				account_t, 
				type_t,
				recurrence_t
	WHERE 		TR_OP_ID = OP_ID
	AND			TR_AC_ID = AC_ID
	AND			TR_TY_ID = TY_ID
	AND			TR_ID = RE_TR_ID
	GROUP BY 	RE_YEAR,
				RE_MONTH,
				TR_OP_ID,
				TR_AC_ID,
				TR_TY_ID
	ORDER BY 	RE_YEAR,
				AC_ID,
				OP_ID,
				TY_ID DESC,
				RE_MONTH				
)