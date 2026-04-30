# MIS Manager Panel - Implementation Guide

## Overview
A comprehensive panel has been implemented for MIS Managers to view and edit bookings with proper access controls and change tracking. The system automatically notifies administrators when changes are made.

## Features Implemented

### 1. ✅ MIS Manager Dashboard
- **Location**: `/mis-manager/dashboard`
- **Route Name**: `mis-manager.dashboard`
- **Features**:
  - Total bookings count
  - Today's bookings count
  - Editable vs. restricted bookings breakdown
  - Recent booking changes with details
  - Booking status distribution
  - Latest bookings list

### 2. ✅ View All Bookings
- **Location**: `/mis-manager/bookings/all`
- **Route Name**: `mis-manager.bookings.all`
- **Features**:
  - View all bookings in the system
  - Search functionality (customer name, agent, booking ID, phone, email)
  - Filter by status
  - Filter by agent
  - Date range filtering
  - Visual indicator for editable vs. locked bookings
  - Pagination (25 per page)

### 3. ✅ View Booking Details
- **Location**: `/mis-manager/bookings/{id}`
- **Route Name**: `mis-manager.bookings.show`
- **Features**:
  - Complete booking information
  - Customer details
  - Flight information
  - Financial details
  - Passenger list
  - Flight segments
  - Edit status indicator (locked/editable)
  - Clear indication of why a booking is locked

### 4. ✅ Edit Bookings (with Restrictions)
- **Location**: `/mis-manager/bookings/{id}/edit`
- **Route Name**: `mis-manager.bookings.edit`
- **Restriction Rules**: 
  - ❌ Cannot edit if status is: `confirmed`, `ticketed`, `charged`
  - ❌ Cannot edit if `payment_confirmed_at` is set
  - ❌ Cannot edit if `ticketed_at` is set
  - ✅ Can edit all other statuses
- **Features**:
  - Editable fields:
    - Customer name
    - Customer email
    - Customer phone
    - Agent custom ID
    - Departure city
    - Arrival city
    - Airline PNR
    - Amount charged
    - Amount paid to airline
    - Total MCO
    - Booking status
    - MIS remarks
    - **Manager remark** (required when making changes)
  - Change tracking with old and new values
  - Automatic admin notification & email

### 5. ✅ Change Tracking & Notifications

#### Tracked Information
When a booking is updated, the system records:
- **booking_id**: Which booking was changed
- **booking_status**: Current status of the booking
- **agent_id**: ID of the original booking agent
- **agent_name**: Name of the booking agent
- **customer_name**: Name of the customer
- **mis_manager_id**: ID of the MIS Manager who made the change
- **mis_manager_name**: Name of the MIS Manager
- **changed_fields**: Array of field names that were modified
- **old_values**: Previous values of changed fields
- **new_values**: New values of changed fields
- **manager_remark**: Why the change was made (required field)
- **created_at**: Timestamp of the change

#### Admin Notifications
Admins receive **both**:
1. **In-App Notification** (stored in database)
   - Appears in notification center
   - Contains all change details
   - Includes fields changed, old/new values

2. **Email Notification** (sent immediately)
   - Subject: "Booking #{id} Changed by MIS Manager"
   - Contains formatted change log
   - Includes manager remarks
   - Has link to view booking

---

## Database Schema

### `booking_changes` Table
```sql
CREATE TABLE booking_changes (
    id BIGINT PRIMARY KEY,
    booking_id BIGINT FOREIGN KEY,
    booking_status VARCHAR(255) NULL,
    agent_id BIGINT FOREIGN KEY NULL,
    agent_name VARCHAR(255) NULL,
    customer_name VARCHAR(255) NULL,
    mis_manager_id BIGINT FOREIGN KEY,
    mis_manager_name VARCHAR(255),
    changed_fields JSON NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    manager_remark TEXT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    INDEXES:
    - booking_id
    - mis_manager_id
    - agent_id
    - created_at
)
```

---

## File Structure

### Controllers
- `app/Http/Controllers/MisManager/MisManagerDashboardController.php` - Dashboard logic
- `app/Http/Controllers/MisManager/MisManagerBookingsController.php` - Bookings CRUD + change tracking
- `app/Http/Controllers/MisManager/MisManagerLoginController.php` - Authentication

### Models
- `app/Models/BookingChange.php` - Change tracking model (NEW)
- `app/Models/Booking.php` - Updated with `changes()` relationship

### Mail & Notifications
- `app/Mail/MisManagerBookingChangeMail.php` - Email to admins (NEW)
- `app/Notifications/MisManagerBookingChangeNotification.php` - In-app notification (NEW)

### Views
- `resources/views/mis-manager/dashboard.blade.php` - Dashboard
- `resources/views/mis-manager/bookings/all.blade.php` - All bookings list
- `resources/views/mis-manager/bookings/show.blade.php` - Booking details
- `resources/views/mis-manager/bookings/edit.blade.php` - Edit booking form
- `resources/views/emails/mis-manager-booking-change.blade.php` - Email template

### Routes
- Login: `GET /mis-manager/login` (name: `mis-manager.login`)
- Logout: `POST /mis-manager/logout` (name: `mis-manager.logout`)
- Dashboard: `GET /mis-manager/dashboard` (name: `mis-manager.dashboard`)
- List all: `GET /mis-manager/bookings/all` (name: `mis-manager.bookings.all`)
- List by agent: `GET /mis-manager/bookings` (name: `mis-manager.bookings.index`)
- Show: `GET /mis-manager/bookings/{id}` (name: `mis-manager.bookings.show`)
- Edit form: `GET /mis-manager/bookings/{id}/edit` (name: `mis-manager.bookings.edit`)
- Update: `PUT /mis-manager/bookings/{id}` (name: `mis-manager.bookings.update`)
- Delete: `DELETE /mis-manager/bookings/{id}` (name: `mis-manager.bookings.destroy`)

---

## Installation & Setup

### 1. Run Migration
```bash
php artisan migrate
```

This creates the `booking_changes` table.

### 2. Ensure User Has Role
Make sure MIS Manager users have the `mis-manager` role assigned via Spatie Laravel Permission:

```php
$user->assignRole('mis-manager');
```

### 3. Test the Feature
1. Log in as a MIS Manager
2. Navigate to `/mis-manager/dashboard`
3. Go to "All Bookings"
4. Find a booking with status other than `confirmed`, `ticketed`, or `charged`
5. Click Edit and make changes
6. Admin users should receive notification + email

---

## User Roles & Permissions

### Required Roles
- **mis-manager**: Can view and edit non-restricted bookings
- **admin** or **manager**: Receive notifications when changes are made

### Middleware
All MIS Manager routes are protected by:
```php
middleware(['auth', 'role:mis-manager'])
```

---

## API Endpoints Summary

| Method | Endpoint | Route Name | Description |
|--------|----------|-----------|-------------|
| GET | `/mis-manager/dashboard` | `mis-manager.dashboard` | MIS Manager dashboard |
| GET | `/mis-manager/bookings/all` | `mis-manager.bookings.all` | List all bookings |
| GET | `/mis-manager/bookings` | `mis-manager.bookings.index` | List by agent |
| GET | `/mis-manager/bookings/{id}` | `mis-manager.bookings.show` | View booking |
| GET | `/mis-manager/bookings/{id}/edit` | `mis-manager.bookings.edit` | Edit form |
| PUT | `/mis-manager/bookings/{id}` | `mis-manager.bookings.update` | Save changes |
| DELETE | `/mis-manager/bookings/{id}` | `mis-manager.bookings.destroy` | Delete booking |

---

## Key Business Logic

### Edit Restrictions
A booking is **RESTRICTED** (cannot be edited) if ANY of these are true:
```php
in_array($booking->status, ['confirmed', 'ticketed', 'charged']) 
|| !is_null($booking->payment_confirmed_at)
|| !is_null($booking->ticketed_at)
```

### Change Detection
Only fields that differ from their original values are:
- Included in `changed_fields` array
- Stored in `old_values` and `new_values` JSON
- Reported to admins

### Admin Notification Flow
1. MIS Manager updates booking
2. System compares old vs. new values
3. Creates `BookingChange` record
4. Queries all admin/manager users
5. For each admin:
   - Creates database notification
   - Sends email notification
6. Both notifications include complete change details

---

## Testing Checklist

- [ ] MIS Manager can log in
- [ ] Dashboard displays correct statistics
- [ ] Can view all bookings with search/filter
- [ ] Can view booking details
- [ ] Can edit editable bookings
- [ ] Cannot edit confirmed bookings (shows error)
- [ ] Cannot edit paid bookings (shows error)
- [ ] Cannot edit ticketed bookings (shows error)
- [ ] Changes are tracked in database
- [ ] Admins receive in-app notifications
- [ ] Admins receive emails with change details
- [ ] Email includes all fields changed
- [ ] Email includes old and new values
- [ ] Email includes manager remark
- [ ] Manager remark is required field
- [ ] Booking deletion is restricted same as edit

---

## Troubleshooting

### Users don't have access
- Check user has `mis-manager` role: `$user->hasRole('mis-manager')`
- Ensure middleware is applied: `middleware(['auth', 'role:mis-manager'])`

### Emails not sending
- Check mail configuration in `.env`
- Verify admin users have valid email addresses
- Check queue is running if using async queues

### Changes not tracked
- Ensure migration ran successfully: `php artisan migrate`
- Check `booking_changes` table exists in database
- Verify no validation errors on booking update

### Bookings appearing locked
- Check booking status value
- Check if `payment_confirmed_at` or `ticketed_at` is set
- View may not be showing correct status

---

## Future Enhancements

Potential improvements:
- Bulk edit functionality
- CSV export of changes
- Change history viewer per booking
- Admin approval workflow for certain changes
- Audit trail with IP tracking
- Change revert capability
- SMS notifications for critical changes
- Slack integration for admin alerts

---

## Support

For issues or questions about the MIS Manager panel implementation, check the files listed in the "File Structure" section above.
