<?php

return [

    // Navigation
    'employees' => 'Employees',

    // Employee information
    'employee_information' => 'Employee Information',
    'employee_name' => 'Employee Name',
    'employee_number' => 'Employee Number',
    'automatically_generated' => 'Automatically generated',
    'active' => 'Active',

    // Login account
    'login_account' => 'Login Account',
    'login_account_description' => 'The employee will use this account to access the mobile application.',
    'account' => 'Account',
    'no_login_account_configured' => 'No login account configured.',
    'edit_login_account' => 'Edit Login Account',
    'create_login_account' => 'Create Login Account',
    'edit_employee_login_account' => 'Edit Employee Login Account',
    'create_employee_login_account' => 'Create Employee Login Account',
    'update_login_account_description' => 'Update the email or password used by this employee to access the mobile application.',
    'create_login_account_description' => 'Enter the email and password that the employee will use to log in to the mobile application.',
    'save_login_account' => 'Save Login Account',

    // Login fields
    'email' => 'Email',
    'new_password' => 'New Password',
    'confirm_new_password' => 'Confirm New Password',

    // Table
    'name' => 'Name',
    'devices' => 'Devices',
    'created' => 'Created',
    'updated' => 'Updated',

    // Filters
    'status' => 'Status',
    'all' => 'All',
    'inactive' => 'Inactive',

    // Errors
    'no_login_account_error' => 'This employee does not have a login account.',


    // Work Locations
    'work_locations' => 'Work Locations',
    'work_location' => 'Work Location',
    'location_information' => 'Location Information',
    'location_on_map' => 'Location on Map',
    'radius_meters' => 'Radius (meters)',
    'timezone' => 'Timezone',
    'work_start_time' => 'Work Start Time',
    'work_end_time' => 'Work End Time',

    // Work Location Table
    'latitude' => 'Latitude',
    'longitude' => 'Longitude',
    'radius' => 'Radius',
    'start' => 'Start',
    'end' => 'End',

    // Employee Devices
    'employee_devices' => 'Employee Devices',
    'employee_device' => 'Employee Device',
    'device_identifier' => 'Device Identifier',
    'device_identifier_copied' => 'Device identifier copied',
    'employee' => 'Employee',
    'device' => 'Device',
    'device_name' => 'Device Name',
    'platform' => 'Platform',
    'android' => 'Android',
    'ios' => 'iOS',
    'registered' => 'Registered',
    'registered_at' => 'Registered At',
    'last_used' => 'Last Used',
    'last_used_at' => 'Last Used At',
    'never' => 'Never',
    'view' => 'View',
    'device_details' => 'Device Details',
    'close' => 'Close',

    'attendance_records' => 'Attendance Records',
    'attendance_record' => 'Attendance Record',
    'check_in' => 'Check-in',
    'check_out' => 'Check-out',
    'not_checked_out' => 'Not checked out',
    'completed' => 'Completed',
    'open' => 'Open',
    'check_in_device' => 'Check-in Device',
    'check_out_device' => 'Check-out Device',
    'check_in_date' => 'Check-in Date',
    'from' => 'From',
    'until' => 'Until',
    'check_in_at' => 'Check-in At',
    'check_out_at' => 'Check-out At',
    'check_in_location' => 'Check-in Location',
    'check_out_location' => 'Check-out Location',
    'check_in_work_location' => 'Check-in Work Location',
    'check_out_work_location' => 'Check-out Work Location',
    'check_in_accuracy' => 'Check-in Accuracy',
    'check_out_accuracy' => 'Check-out Accuracy',
    'duration' => 'Duration',

    'attendance_attempts' => 'Attendance Attempts',
    'attendance_attempt' => 'Attendance Attempt',
    'action' => 'Action',
    'result' => 'Result',
    'failure_reason' => 'Failure Reason',
    'accuracy' => 'Accuracy',
    'attempted_at' => 'Attempted At',
    'success' => 'Success',
    'failed' => 'Failed',
    'none' => 'None',
    'location' => 'Location',
    'gps_accuracy' => 'GPS Accuracy',

    'attendance_report' => 'Attendance Report',
    'all_employees' => 'All Employees',
    'reset_filters' => 'Reset Filters',

    'attendance_report_employees' => 'Employees',
    'attendance_report_records' => 'Records',
    'attendance_report_completed' => 'Completed',
    'attendance_report_open' => 'Open',
    'report_filters' => 'Report Filters',
    'export_excel' => 'Export Excel',
    'export_pdf' => 'Export PDF',

    'attendance_export_employee_number' => 'Employee Number',
    'attendance_export_employee' => 'Employee',
    'attendance_export_date' => 'Date',
    'attendance_export_work_location' => 'Work Location',
    'attendance_export_check_in' => 'Check-in',
    'attendance_export_check_out' => 'Check-out',
    'attendance_export_duration' => 'Duration',
    'attendance_export_status' => 'Status',
    'attendance_export_check_in_latitude' => 'Check-in Latitude',
    'attendance_export_check_in_longitude' => 'Check-in Longitude',
    'attendance_export_check_in_accuracy' => 'Check-in Accuracy',
    'attendance_export_check_out_latitude' => 'Check-out Latitude',
    'attendance_export_check_out_longitude' => 'Check-out Longitude',
    'attendance_export_check_out_accuracy' => 'Check-out Accuracy',


    'attendance_records_summary' => 'Attendance Records',
    'date' => 'Date',
    'no_attendance_records' => 'No attendance records found.',
    'generated_on' => 'Generated on',
    'employee_no' => 'Employee No.',

    //API MESSAGES
    'User is not associated with an employee.' => 'User is not associated with an employee.',
    'Employee account is inactive.' => 'Employee account is inactive.',
    'This device is already registered to another employee.' => 'This device is already registered to another employee.',
    'Device registered successfully.' => 'Device registered successfully.',
    'No active device is registered.' => 'No active device is registered.',
    'Check-in successful.' => 'Check-in successful.',
    'Check-out successful.' => 'Check-out successful.',
    'Invalid credentials.' => 'Invalid credentials.',
    'Login successful.' => 'Login successful.',
    'Logout successful.' => 'Logout successful.',
    'Employee does not have a work location assigned.' => 'Employee does not have a work location assigned.',
    'Work location is inactive.' => 'Work location is inactive.',
    'Device is not registered or is inactive.' => 'Device is not registered or is inactive.',
    'Invalid attendance action.' => 'Invalid attendance action.',
    'Employee is already checked in.' => 'Employee is already checked in.',
    'There is no active check-in.' => 'There is no active check-in.',
    'GPS accuracy is not acceptable.' => 'GPS accuracy is not acceptable.',
    'Employee is outside the allowed work location radius.' => 'Employee is outside the allowed work location radius.',
    'Attendance is outside working hours.' => 'Attendance is outside working hours.',

    'source' => 'Source',
    'online' => 'Online',
    'offline' => 'Offline',

    'client_event_id' => 'Client Event ID',
    'client_event_id_copied' => 'Client event ID copied',
    'captured_at' => 'Captured At',
    'sync_status' => 'Sync Status',

    'accepted' => 'Accepted',
    'rejected' => 'Rejected',

     'delete_account_succes' => 'Your account has been successfully deleted.',
     'delete_account_failed' => 'An error occurred while deleting your account. Please try again.',

    // Employee Registration Requests
    'employee_registration_requests' => 'Employee Registration Requests',
    'employee_registration_request' => 'Employee Registration Request',
    'request_id' => 'Request ID',
    'requested_email' => 'Requested Email',
    'submitted_at' => 'Submitted At',
    'reviewed_by' => 'Reviewed By',
    'reviewed_at' => 'Reviewed At',
    'pending' => 'Pending',
    'approved' => 'Approved',
    'approve' => 'Approve',
    'reject' => 'Reject',
    'approve_employee_registration' => 'Approve Employee Registration',
    'approve_employee_registration_description' => 'This will create the user account and link it to the employee.',
    'reject_employee_registration' => 'Reject Employee Registration',
    'rejection_reason' => 'Rejection Reason',

    // Map
    'location_map_help' => 'Click on the map or drag the marker to set the work location.',
    'map_standard' => 'Standard',
    'map_terrain' => 'Terrain',
    'map_satellite' => 'Satellite',
    'map_place_names' => 'Place names',

    // Notifications
    'Registration approved' => 'Registration approved',
    'Registration rejected' => 'Registration rejected',
    'Your employee account registration has been approved.' => 'Your employee account registration has been approved.',
    'Your employee account registration has been rejected.' => 'Your employee account registration has been rejected.',
    'Account registration update' => 'Account registration update',
    'Your registration status has changed.' => 'Your registration status has changed.',

    //API MESSAGES
    'Employee not found.' => 'Employee not found.',
    'Employee already has a user account.' => 'Employee already has a user account.',
    'Employee already has a pending registration request.' => 'Employee already has a pending registration request.',
    'This registration request has already been reviewed.' => 'This registration request has already been reviewed.',
    'The email reservation is invalid.' => 'The email reservation is invalid.',
    'Attendance events synchronized successfully.' => 'Attendance events synchronized successfully.',
    'FCM token updated successfully.' => 'FCM token updated successfully.',
    'Account deleted successfully.' => 'Account deleted successfully.',
];
