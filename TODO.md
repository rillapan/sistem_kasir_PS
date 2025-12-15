# TODO: Debug Device Filtering Issue for Custom Package Transactions on Hosted Environment

## Issue Description
The device name section ("nama perangkat") appears on localhost but disappears after hosting for custom package transaction type at https://kasirpssugengrawuh.com/transaction/create. On hosted environment, it always shows "Tidak ada perangkat tersedia yang sesuai dengan paket ini" (No devices available that match the package). Additionally, make the device name auto-select the first available device when a custom package is chosen.

## Root Cause Analysis
- On localhost, the device select works properly for custom packages
- On hosted environment, the device select gets disabled and appears hidden for custom packages
- JavaScript functions were disabling the device select when no devices were available for a package
- The filtering logic may not be working correctly on hosted environment due to data differences

## Changes Made
- [x] Modified `toggleFields()` function to enable device select for custom packages instead of disabling it
- [x] Modified `filterDevicesForCustomPackage()` function to keep device select enabled even when no devices are available for the selected package
- [x] Added auto-selection of the first available device when a custom package is selected and devices are filtered
- [x] Added extensive debugging logs to `filterDevicesForCustomPackage()` to identify why devices aren't being found on hosted environment

## Debugging Steps
To identify the issue on hosted environment:
1. Open browser developer tools (F12) on the hosted site
2. Go to transaction/create page and select a custom package
3. Check the browser console for the debugging logs under "=== DEBUGGING CUSTOM PACKAGE DEVICE FILTERING ==="
4. Look for:
   - Package data structure and playstation IDs
   - Devices array content and length
   - Device filtering process (which devices are rejected and why)
   - Final filtered devices count

## Possible Causes
- Devices array might be empty or not loaded properly on hosted environment
- Device status might not be 'Tersedia' on hosted environment
- Device playstation_id might not match package playstation IDs
- Package data structure might be different on hosted environment

## Testing Required
- [ ] Test on localhost: Custom package transaction creation should show device name section and auto-select first device
- [ ] Test on hosted environment: Check browser console logs to identify why no devices are found
- [ ] Verify that device filtering still works when a custom package is selected
- [ ] Ensure lost time transactions still work properly (device names should appear)
- [ ] Test that price calculation triggers automatically when device is auto-selected

## Files Modified
- resources/views/transaction/create.blade.php
  - toggleFields() function
  - filterDevicesForCustomPackage() function (with added debugging)
