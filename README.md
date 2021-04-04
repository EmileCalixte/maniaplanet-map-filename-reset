# Maniaplanet map filename reset

A utility tool to rename all map files in a directory based on the real name of the map.  

The tool will recursively browse the provided directory and rename all map files to the original name of the map.

Works well with Maniaplanet and Trackmania2020 map files.

## Prerequisites

### Requirements

- [PHP](https://www.php.net/downloads) 7.4 or newest
- [Composer](https://getcomposer.org/)

### Installation

- Clone the project
- Install composer dependencies : 
```sh
composer install
```

## Usage

```sh
php ./src/main.php <PATH>
```

## Contributing

All contributions are welcome! Feel free to create issues or pull requests to enhance this tool.

## Ideas for improvement

- Build a PHAR archive
- Add an option to request a confirmation from the user for each file to rename
- Add an option to not browse the directory recursively (process only files that are direct children of the specified directory)
- Ability to specify a file path (currently only directory paths are allowed)
