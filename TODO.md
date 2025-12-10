# Fix Custom Package Device Status Issue

## Problem

When saving a transaction with "custom paket" type, the selected device's status remains "Tersedia" instead of changing to "Digunakan" like other transaction types.

## Root Cause

The DeviceController's index method updates device statuses based on transactions but only handles 'prepaid' and 'postpaid' transactions. It doesn't handle 'custom_package' transactions, so they default to 'Tersedia'.

## Solution

Add handling for 'custom_package' transactions in DeviceController's index method to set device status to 'Digunakan' until the package duration ends (waktu_Selesai).

## Steps

-   [ ] Update DeviceController index method to handle custom_package transactions
-   [ ] Test the fix by creating a custom package transaction
