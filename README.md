# Attendance-list
A simple application to manage attendance lists

## Install
1. Set up MySQL DB whit file db.sql
1. fill config.php.temp file
1. rename config.php.temp to config.php

## Functions
1. Registration of the start and end of work
1. Printing the attendance list
1. Acceptance of days by superiors
1. Add whole timeframes in one click
1. Counting days of remote work

## db.sql
Holidays that occur in Poland are added to the db.sql file. If you want to replace it with your country, edit it as follows: (lines form **77**)
```sql
INSERT INTO
    `dniwolne` (`id`, `data`, `nazwaSwieta`, `aktywne`)
VALUES
    (
        'YYYY-MM-DD',
        'Event 1',
        1
    ),
    (
        'YYYY-MM-DD',
        'Event 2',
        1
    ),
    ...
    (
        'YYYY-MM-DD',
        'Event N',
        1
    );
```

## TODO

## Licence
Attendance-list © 2023 by kry008 is licensed under Attribution-NonCommercial-NoDerivatives 4.0 International 