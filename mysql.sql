SELECT
    count(*),
    `Column1`,
    `Testing`,
    `Testing Three`
FROM `Table1`
WHERE Column1 = 'testing' AND ((`Column2` = `Column3` OR Column4 >= now()))
GROUP BY Column1
ORDER BY Column3 DESC LIMIT 5, 10
