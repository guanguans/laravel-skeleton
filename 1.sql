# noinspection SqlResolveForFile

SELECT
    COUNT(*),
    `Column1`,
    `Testing`,
    `Testing Three`
FROM
    `Table1`
WHERE
    Column1 = 'testing'
    AND(
        (
            `Column2` = `Column3`
            OR Column4 >= NOW()
        )
    )
GROUP BY
    Column1
ORDER BY
    Column3 DESC
LIMIT
    5, 10;

SELECT
    COUNT(*),
    `Column1`,
    `Testing`,
    `Testing Three`
FROM
    `Table1`
WHERE
    Column1 = 'testing'
    AND(
        (
            `Column2` = `Column3`
            OR Column4 >= NOW()
        )
    )
GROUP BY
    Column1
ORDER BY
    Column3 DESC
LIMIT
    11111115, 10;
