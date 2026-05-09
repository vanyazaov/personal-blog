#!/usr/bin/env php
<?php
declare(strict_types=1);

const APP_NAME = 'blog_database_init';
const APP_VERSION = '1.0.0';
const EXIT_OK = 0;
const EXIT_ERROR = 1;
const CONFIG_INI_FILE = __DIR__ . "/../.env.ini";
const SQL_CREATE_TABLES = [
    "CREATE TABLE `categories` (
        `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `name` varchar(120) NOT NULL
    )",
    "CREATE TABLE `posts` (
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `title` varchar(200) NOT NULL,
    `body` text NOT NULL,
    `created_at` datetime NULL
    )",

    "CREATE TABLE `post_categories` (
    `post_id` int NOT NULL,
    `category_id` int NOT NULL,
    FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`),
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
    )"
];

function showHelp(): void {
    echo <<<HELP
Использование: {APP_NAME} [options] <input>

Опции:
  -h, --help            Показать справку
      --version         Показать версию
      --fresh           Удаляет таблицы и заново заполняет данными\n
HELP;
}

function connectDB(array $env): \PDO {
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=utf8mb4',
        $env['DB_HOST'] ?? 'localhost',
        $env['DB_NAME'] ?? 'app'
    );

    $pdo = new \PDO($dsn, $env['DB_USER'], $env['DB_PASS'], [
        \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

    return $pdo;
}

function isEmptyDB(\PDO $pdo): bool {
    $result = $pdo->query('SHOW TABLES');
    return empty($result->fetch());
}

function createTables(\PDO $pdo): void {
    foreach (SQL_CREATE_TABLES as $table) {
        $pdo->exec($table);
    }
}

function main(array $argv): int {
    $rest = 0;
    $opts = getopt('h', ['help', 'version'], $rest);
    $positional = array_slice($argv, $rest);

    if (isset($opts['h']) || isset($opts['help'])) {
        showHelp();
        return EXIT_OK;
    }
    if (isset($opts['version'])) {
        echo APP_NAME . ' ' . APP_VERSION . "\n";
        return EXIT_OK;
    }

    try {
        if (!file_exists(CONFIG_INI_FILE)) {
            throw new InvalidArgumentException("Not found config file: " . CONFIG_INI_FILE);
        }
        $env = parse_ini_file(CONFIG_INI_FILE);

        $pdo = connectDB($env);
        if (!isEmptyDB($pdo)) {
            fwrite(STDERR, "Error: DB is not empty.\n");
            return EXIT_ERROR;
        }
        createTables($pdo);

        $pdo->beginTransaction();
        $pdo->commit();
        var_dump($pdo);
        
    } catch (\InvalidArgumentException $e) {
        fwrite(STDERR, "Error: " . $e->getMessage() . "\n");
        return EXIT_ERROR;
    } catch (\PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        fwrite(STDERR, "Error connect DB: " . $e->getMessage() . "\n");
        return EXIT_ERROR;
    }

    return EXIT_OK;
}

exit(main($argv));