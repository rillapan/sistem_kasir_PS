# Testing Guide - Sistem Kasir PS

## Running Tests

### Run All Tests
```bash
php artisan test
```

### Run Specific Test File
```bash
# FnB Management Tests
php artisan test tests/Feature/TransactionFnbManagementTest.php

# Midnight Timer Tests
php artisan test tests/Feature/MidnightTimerCrossingTest.php
```

### Run Specific Test Method
```bash
php artisan test --filter=can_add_new_fnb_to_transaction
```

### Run with Coverage
```bash
php artisan test --coverage
```

---

## Test Files Overview

### 1. TransactionFnbManagementTest.php
Tests for FnB management features on transaction edit page.

**Test Cases:**
- ✅ View transaction edit page with FnB management UI
- ✅ Add new FnB to transaction
- ✅ Adding FnB updates transaction total
- ✅ Cannot add FnB with insufficient stock
- ✅ Update FnB quantity in transaction
- ✅ Update FnB price in transaction
- ✅ Decreasing quantity restores stock
- ✅ Delete FnB from transaction
- ✅ Deleting FnB updates transaction total
- ✅ Cannot modify FnB on paid transaction
- ✅ Unlimited stock FnB works correctly

### 2. MidnightTimerCrossingTest.php
Tests for timer functionality across midnight (23:59 → 00:00).

**Test Cases:**
- ✅ Prepaid transaction crossing midnight calculates end time correctly
- ✅ Custom package crossing midnight calculates correctly
- ✅ Device controller detects midnight crossing for timer data
- ✅ Transaction before midnight does not cross
- ✅ Lost time across midnight calculates duration correctly
- ✅ Editing transaction near midnight handles correctly
- ✅ Device status updates correctly after midnight crossing

---

## Manual Testing Scenarios

### FnB Management Testing

#### Scenario 1: Add FnB to Transaction
1. Login as admin/kasir
2. Create an unpaid transaction
3. Go to transaction list, click "Edit" on the transaction
4. In "Kelola FnB Pesanan" section, select an FnB item
5. Enter quantity, click "Tambah FnB"
6. **Verify:**
   - FnB appears in table
   - Total updates correctly
   - Success notification shows
   - Stock in database decreased

#### Scenario 2: Edit FnB Quantity
1. On edit page with existing FnB
2. Change qty value in the input field
3. Click outside or press Enter
4. **Verify:**
   - Subtotal updates
   - Total updates
   - Success notification appears
   - Stock adjusts correctly

#### Scenario 3: Delete FnB
1. Click delete button on an FnB row
2. Confirm deletion
3. **Verify:**
   - Row disappears
   - Total updates
   - Stock restored
   - Stock mutation created

### Midnight Timer Testing

#### Scenario 1: Create Transaction at 23:50
```bash
# Use browser developer tools to set system time
# Or wait until actual midnight
```
1. Create prepaid transaction at 23:50 with 1 hour paket
2. **Verify:**
   - End time shows 00:50
   - Timer counts down correctly
   - After midnight, timer continues (doesn't reset)
   - At 00:50, device status changes to "Tersedia"

#### Scenario 2: Lost Time Across Midnight
1. Start lost time at 23:00
2. Stop at 01:30 next day
3. **Verify:**
   - Duration shows "2 jam 30 menit" (not negative)
   - Total calculated correctly
   - Payment page shows correct amount

---

## Database Seeding for Tests

```bash
# Refresh database and run seeders
php artisan migrate:fresh --seed

# Run specific seeder
php artisan db:seed --class=TransactionSeeder
```

---

## Common Test Failures and Solutions

### Issue: "Class 'Database\Factories\UserFactory' not found"
**Solution:**
```bash
php artisan make:factory UserFactory
```

### Issue: "SQLSTATE[HY000] [1049] Unknown database"
**Solution:**
1. Create test database in `.env.testing`:
```env
DB_CONNECTION=mysql
DB_DATABASE=kasir_ps_test
```
2. Create the database:
```bash
mysql -u root -p -e "CREATE DATABASE kasir_ps_test"
```

### Issue: "Target class [App\Models\TransactionFnb] does not exist"
**Solution:**
Check namespace and file location match.

---

## CI/CD Integration

### GitHub Actions Example
```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v2
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
          
      - name: Install Dependencies
        run: composer install
        
      - name: Run Tests
        run: php artisan test
```

---

## Test Coverage Goals

**Target:** 80% code coverage for new features

**Current Coverage:**
- FnB Management: ~90%
- Midnight Timer: ~85%

To check coverage:
```bash
php artisan test --coverage --min=80
```

---

## Notes

- All tests use `RefreshDatabase` trait to reset database between tests
- Carbon::setTestNow() is used to simulate different times
- Factory pattern should be used for creating test data
- Always clean up after tests (automatic with RefreshDatabase)
