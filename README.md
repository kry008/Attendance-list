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

## SCREENSHOTS

### Login
![Login](https://git.kry008.xyz/kry008/Attendance-list/raw/branch/main/README_IMG/login.png)

### Main page
| Admin | User |
| --- | --- |
| ![Admin](https://git.kry008.xyz/kry008/Attendance-list/raw/branch/main/README_IMG/admin.png) | ![User](https://git.kry008.xyz/kry008/Attendance-list/raw/branch/main/README_IMG/user.png) |

### Reports
![Reports](https://git.kry008.xyz/kry008/Attendance-list/raw/branch/main/README_IMG/reports1.png)  
![Reports](https://git.kry008.xyz/kry008/Attendance-list/raw/branch/main/README_IMG/reports2.png)  
![Reports](https://git.kry008.xyz/kry008/Attendance-list/raw/branch/main/README_IMG/reports3.png)  


## TODO
1. Multi-language support

## Licence
Attendance-list © 2023 by kry008 is licensed under Attribution-NonCommercial-NoDerivatives 4.0 International 