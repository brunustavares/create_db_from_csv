# create_db_from_csv
PHP script to create a Moodle database activity from a CSV file.

It was originally developed for [Universidade Aberta (UAb)](https://portal.uab.pt/).

## Overview
This CLI script automates the creation of a Moodle Database (`mod_data`) activity. It reads the header of a provided CSV file to automatically generate the corresponding database fields within a specified Moodle course.

## Prerequisites
- A working Moodle installation.
- The script should be executed in an environment where it can access Moodle's `config.php` (it currently expects to be located at `ROOT/scripts/create_db_from_csv/`).

## Usage
Run the script from the command line:

```bash
php create_db_from_csv.php --courseid=ID --csv=/path/to/file.csv [--name="Activity Name"]
```

### Arguments
- `--courseid`: The ID of the course where the activity will be created.
- `--csv`: Full path to the CSV file containing the headers.
- `--name`: (Optional) The name of the Database activity. Defaults to "New Database".

## CSV Header Format
The script determines the database fields based on the first line of your CSV. Each column header **must** use the `name:type` format.

**Example Header:**
`Full Name:text,Description:textarea,Year:number,Choice:radiobutton`

### Supported Field Types
- `text`: Standard text input (max 255 chars).
- `textarea`: Multi-line text area.
- `number`: Numeric field (5 decimal places).
- `menu`: Dropdown menu (pre-filled with default options).
- `radiobutton`: Radio selection (pre-filled with default options).

## License

**Author**: Bruno Tavares  
**Contact**: [brunustavares@gmail.com](mailto:brunustavares@gmail.com)  
**LinkedIn**: [https://www.linkedin.com/in/brunomastavares/](https://www.linkedin.com/in/brunomastavares/)  
**Copyright**: 2026-present Bruno Tavares  
**License**: GNU GPL v3 or later  

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program. If not, see <https://www.gnu.org/licenses/>.
