# Exam Management System - Testing Setup

This project now includes comprehensive PHP unit tests for the `view_exams.php` functionality and a proper `.gitignore` file for version control.

## Project Structure

```
jannat/
├── view_exams.php              # Main exam viewing page
├── view_exams_functions.php    # Extracted functions for testing
├── composer.json               # Composer configuration
├── phpunit.xml                 # PHPUnit configuration
├── run-tests.php              # Test runner script
├── .gitignore                 # Git ignore rules
├── tests/
│   ├── bootstrap.php          # Test bootstrap file
│   ├── ViewExamsTest.php      # Unit tests for functions
│   └── ViewExamsIntegrationTest.php # Integration tests
└── README.md                  # This file
```

## Testing Setup

### 1. Install Dependencies

First, install Composer if you haven't already:
```bash
# Download and install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

Then install the project dependencies:
```bash
composer install
```

### 2. Run Tests

You can run tests in several ways:

#### Option 1: Using the test runner script
```bash
php run-tests.php
```

#### Option 2: Using Composer script
```bash
composer test
```

#### Option 3: Using PHPUnit directly
```bash
./vendor/bin/phpunit
```

#### Option 4: Run with coverage (if Xdebug is installed)
```bash
./vendor/bin/phpunit --coverage-html coverage/
```

## Test Coverage

The test suite includes:

### Unit Tests (`ViewExamsTest.php`)
- ✅ `deleteExam()` function with successful deletion
- ✅ `deleteExam()` function with failed deletion
- ✅ `deleteExam()` function with execute failure
- ✅ `changeExamStatus()` function with successful status change
- ✅ `changeExamStatus()` function with failed status change
- ✅ `changeExamStatus()` function with execute failure
- ✅ `changeExamStatus()` with different status values
- ✅ Function parameter type validation

### Integration Tests (`ViewExamsIntegrationTest.php`)
- ✅ SQL query structure for fetching exams
- ✅ Exam deletion workflow
- ✅ Exam status change workflow
- ✅ HTML output generation with exam data
- ✅ Empty exam list scenario

## Functions Tested

### `deleteExam(PDO $conn, int $examId): bool`
- Deletes an exam by its ID
- Returns `true` on success, `false` on failure
- Uses prepared statements for security

### `changeExamStatus(PDO $conn, int $examId, string $status): bool`
- Changes the status of an exam
- Accepts 'active', 'inactive', or other status values
- Returns `true` on success, `false` on failure
- Uses prepared statements for security

## Git Ignore Configuration

The `.gitignore` file excludes:

### Dependencies & Build Files
- `/vendor/` - Composer dependencies
- `composer.lock` - Lock file (optional exclusion)
- `node_modules/` - Node.js dependencies
- `build/`, `dist/` - Build directories

### Development & IDE Files
- `.vscode/`, `.idea/` - IDE configurations
- `*.swp`, `*.swo` - Vim swap files
- `.DS_Store` - macOS system files

### Sensitive Configuration
- `config.php`, `conn.php` - Database configurations
- `.env*` - Environment files
- `*.pem`, `*.key` - Security certificates

### Temporary & Cache Files
- `cache/`, `tmp/`, `temp/` - Cache directories
- `*.log` - Log files
- `sessions/` - Session files
- `uploads/` - User uploaded files

### Testing & Coverage
- `.phpunit.result.cache` - PHPUnit cache
- `coverage/` - Coverage reports

## Best Practices Implemented

1. **Separation of Concerns**: Functions extracted to separate file for testability
2. **Type Safety**: Strict typing enabled with `declare(strict_types=1);`
3. **Security**: Prepared statements used for all database operations
4. **Testing**: Comprehensive unit and integration tests
5. **Documentation**: Proper PHPDoc comments for all functions
6. **Version Control**: Comprehensive .gitignore for clean repository

## Running in Development

To run the application:

1. Ensure your web server is running (Apache/Nginx)
2. Make sure the database connection (`conn.php`) is properly configured
3. Access `view_exams.php` through your web browser
4. Run tests regularly during development: `php run-tests.php`

## Continuous Integration

The test suite is designed to work with CI/CD pipelines. You can integrate it with:
- GitHub Actions
- GitLab CI
- Jenkins
- Travis CI

Example GitHub Actions workflow:
```yaml
name: PHP Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v2
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.0'
    - name: Install dependencies
      run: composer install
    - name: Run tests
      run: composer test
```