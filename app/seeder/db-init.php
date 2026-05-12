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
    `picture` varchar(20) DEFAULT NULL,
    `created_at` datetime NULL
    )",

    "CREATE TABLE `post_categories` (
    `post_id` int NOT NULL,
    `category_id` int NOT NULL,
    FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`),
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
    )"
];
const DROP_TABLES = ['post_categories', 'categories', 'posts'];

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

function dropTables(\PDO $pdo): void {
    foreach (DROP_TABLES as $table) {
        $pdo->exec("DROP TABLE IF EXISTS $table");
    }
}

function createCategories(\PDO $pdo): void {
    $stmt = $pdo->prepare('
        INSERT INTO categories (name) VALUES (?)
    ');       
    for ($i = 1; $i < 11; $i++) {
        $stmt->execute(["Category #$i"]);
    }
}

function generatePosts(\PDO $pdo, int $postsCount): void {
    $titles = [
        'Новости', 'Обновление', 'Важное объявление', 'Полезный совет', 
        'Как начать', 'Лучшие практики', 'Руководство', 'Туториал',
        'Идеи для развития', 'Анализ рынка', 'Сравнение подходов',
        'Кейс: успешная реализация', 'Ошибки новичков', 'Секреты мастерства',
        'Тренды года', 'Инструменты профессионала', 'Будущее технологии'
    ];

    $adjectives = [
        'быстрый', 'удобный', 'мощный', 'простой', 'эффективный',
        'инновационный', 'надёжный', 'гибкий', 'безопасный', 'масштабируемый'
    ];

    $nouns = [
        'подход', 'метод', 'инструмент', 'решение', 'способ',
        'алгоритм', 'паттерн', 'фреймворк', 'библиотека', 'продукт'
    ];

    $paragraphs = [
        'В этой статье мы рассмотрим ключевые аспекты данной темы.',
        'На основе многолетнего опыта мы подготовили практические рекомендации.',
        'Этот подход уже используют ведущие компании в своей работе.',
        'Не забывайте учитывать контекст и специфику вашего проекта.',
        'Результаты нашего исследования подтверждают эффективность метода.',
        'Важно понимать, что каждая ситуация требует индивидуального решения.',
        'Мы рекомендуем начать с малого и постепенно масштабировать успех.',
        'Ошибки на начальном этапе неизбежны - главное делать выводы.',
        'Инвестиции в качество всегда окупаются в долгосрочной перспективе.',
        'Команда профессионалов поможет избежать типичных проблем.'
    ];
    // Функция генерации случайной даты (от текущей до 100 дней назад)
    function randomDate(int $daysBack = 100): string {
        $timestamp = time() - mt_rand(0, $daysBack * 86400);
        return date('Y-m-d H:i:s', $timestamp);
    }

    // Функция генерации заголовка
    function generateTitle(array $titles, array $adjectives, array $nouns): string {
        // 70% - готовые заголовки, 30% - комбинированные
        if (mt_rand(1, 100) <= 70) {
            return $titles[array_rand($titles)];
        } else {
            $adj = $adjectives[array_rand($adjectives)];
            $noun = $nouns[array_rand($nouns)];
            return ucfirst($adj) . ' ' . ucfirst($noun);
        }
    }
    // Функция генерации тела поста
    function generateBody(array $paragraphs): string {
        $numParagraphs = mt_rand(2, 6);
        $body = '';
        $usedParagraphs = [];
        
        for ($i = 0; $i < $numParagraphs; $i++) {
            // Выбираем случайный параграф, избегая дубликатов подряд
            do {
                $paragraph = $paragraphs[array_rand($paragraphs)];
            } while ($paragraph === end($usedParagraphs) && count($usedParagraphs) > 0);
            
            $usedParagraphs[] = $paragraph;
            $body .= $paragraph . ' ';
            
            // Иногда добавляем пояснения или примеры
            if (mt_rand(1, 100) <= 30) {
                $body .= 'Например, многие пользователи отмечают улучшение результатов на 30-50%. ';
            }
        }
        
        // Иногда добавляем заключение
        if (mt_rand(1, 100) <= 40) {
            $conclusions = [
                'В заключение стоит отметить, что последовательность действий критически важна.',
                'Попробуйте применить эти знания на практике уже сегодня.',
                'Делитесь своим опытом в комментариях - это поможет другим.',
                'Следите за обновлениями, мы готовим новые материалы.'
            ];
            $body .= $conclusions[array_rand($conclusions)];
        }
        
        return trim($body);
    }  
    
    // Генерация массива постов
    $stmt = $pdo->prepare('
        INSERT INTO posts (title, body, picture , created_at) VALUES (?, ?, ?, ?)
    ');
    for ($i = 1; $i <= $postsCount; $i++) {
        $stmt->execute([
            generateTitle($titles, $adjectives, $nouns),
            generateBody($paragraphs),
            'post-' . mt_rand(1, 3) . '.png',
            randomDate(100)
        ]);
    }
}

function assignRandomPostToRandomCategories(\PDO $pdo): void {
    $postsStmt = $pdo->query("SELECT id FROM posts");
    $posts = $postsStmt->fetchAll(PDO::FETCH_COLUMN);

    $categoriesStmt = $pdo->query("SELECT id FROM categories");
    $categories = $categoriesStmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($posts) || empty($categories)) {
        throw new RuntimeException("There are no posts or categories to create links to.");
    }

    // Подготавливаем запрос для вставки
    $insertStmt = $pdo->prepare(
        "INSERT INTO post_categories (post_id, category_id) VALUES (?, ?)"
    );    

    foreach ($posts as $postId) {
        // Определяем случайное количество категорий (от 1 до 4)
        $categoriesCount = min(
            random_int(1, 4),
            count($categories)
        );

        // Перемешиваем категории и берем нужное количество
        $shuffledCategories = $categories;
        shuffle($shuffledCategories);
        $selectedCategories = array_slice($shuffledCategories, 0, $categoriesCount); 
        
        // Создаем связи
        foreach ($selectedCategories as $categoryId) {
            $insertStmt->execute([$postId, $categoryId]);
        }        
    }
}

function main(array $argv): int {
    $rest = 0;
    $opts = getopt('h', ['help', 'version', 'fresh'], $rest);
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
        if (isset($opts['fresh'])) {
            fwrite(STDOUT, "⚠️  WARNING: This action will delete all tables in the database!\n");
            fwrite(STDOUT, "База данных: " . $env['DB_NAME'] . "\n\n");
            fwrite(STDOUT, "Вы уверены? (yes/no): ");

            $handle = fopen("php://stdin", "r");
            $confirmation = trim(fgets($handle));
            fclose($handle);

            if (strtolower($confirmation) === 'yes' || strtolower($confirmation) === 'y') {
                dropTables($pdo);
                fwrite(STDOUT, "✅ Tables have been deleted successfully.\n");
            }
        }
        if (!isEmptyDB($pdo)) {
            fwrite(STDERR, "Error: DB is not empty.\n");
            return EXIT_ERROR;
        }
        createTables($pdo);

        $pdo->beginTransaction();
        createCategories($pdo);
        generatePosts($pdo, 100);
        assignRandomPostToRandomCategories($pdo);
        $pdo->commit();
        fwrite(STDOUT, "✅ The tables have been successfully created and populated with data.\n");
        
    }  catch (\PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        fwrite(STDERR, "❌ Error connect DB: " . $e->getMessage() . "\n");
        return EXIT_ERROR;
    } catch (\Exception $e) {
        fwrite(STDERR, "❌ Error: " . $e->getMessage() . "\n");
        return EXIT_ERROR;
    }

    return EXIT_OK;
}

exit(main($argv));