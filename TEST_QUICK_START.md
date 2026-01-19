# Quick Test Commands

## Run All Tests
```bash
# Run all tests
php artisan test

# Run with detailed output
php artisan test --testsuite=Feature

# Parallel execution (faster)
php artisan test --parallel
```

## Run Specific Tests
```bash
# FnB Management
php artisan test tests/Feature/TransactionFnbManagementTest.php

# Midnight Timer
php artisan test tests/Feature/MidnightTimerCrossingTest.php

# Single test method
php artisan test --filter=can_add_new_fnb_to_transaction
```

## Test Coverage
```bash
# Basic coverage
php artisan test --coverage

# Minimum coverage enforcement
php artisan test --coverage --min=80

# Html coverage report
php artisan test --coverage-html coverage/
```

## Before Running Tests
```bash
# Create test database
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS kasir_ps_test"

# Copy .env to .env.testing if needed
cp .env .env.testing

# Run migrations for test database
php artisan migrate --env=testing
```

## Test Files Created
✅ tests/Feature/TransactionFnbManagementTest.php (11 tests)
✅ tests/Feature/MidnightTimerCrossingTest.php (7 tests)
✅ TESTING.md (Full documentation)
✅ TEST_QUICK_START.md (This file)

Total: **18 automated tests**
