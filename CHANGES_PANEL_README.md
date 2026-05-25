# Changes Panel

The Changes Panel is a specialized administrative interface within the TravelOmine system designed for managing and modifying bookings across all agents. It provides comprehensive access to view, search, filter, and edit booking data from the entire system.

## Features

### 🔐 Authentication

- Secure login system for authorized personnel
- Role-based access control (`changes` role required)
- Session management with automatic logout

### 📊 Dashboard

- Overview of key metrics and quick access links
- **Notification System**: Real-time notifications with bell icon and modal
- Clean, responsive Bootstrap-based interface
- Navigation sidebar for easy access to features

### 📋 Bookings Management

- **View All Bookings**: Comprehensive list of all bookings from all agents
- **Advanced Filtering**:
    - Search by customer email, phone, agent ID, or booking ID
    - Filter by booking status (pending, charged, ticketed, cancelled)
    - Filter by service type (flight, hotel, car)
    - Filter by specific agent
    - Date range filtering (from/to dates)
- **Pagination**: Efficient handling of large datasets
- **Detailed View**: Complete booking information display
- **Edit Functionality**: Modify booking details (framework ready)

### � Notification System

- **Real-time Notifications**: Bell icon in navbar with unread count badge
- **Modal Display**: Click bell to view notifications in a modal popup
- **Admin Announcements**: Receive system-wide announcements and updates
- **Dismissible Notifications**: Mark notifications as read when closed
- **Automatic Updates**: Notifications appear without page refresh

## Installation & Setup

### Prerequisites

- Laravel 10+
- PHP 8.1+
- MySQL/PostgreSQL database
- Composer
- Node.js & NPM (for assets)

### Database Setup

1. Ensure you have a user with `role = 'changes'` in the `users` table:

```sql
INSERT INTO users (name, email, password, role, created_at, updated_at)
VALUES ('Changes Admin', 'changes@example.com', '$2y$10$hashedpassword', 'changes', NOW(), NOW());
```

### Configuration

1. The panel uses standard Laravel authentication
2. Routes are automatically registered in `routes/web.php`
3. Views are located in `resources/views/changes/`

### Access URLs

- **Login**: `/changes/login`
- **Dashboard**: `/changes/dashboard`
- **Bookings List**: `/changes/bookings`
- **View Booking**: `/changes/bookings/{id}`
- **Edit Booking**: `/changes/bookings/{id}/edit`

## Usage Guide

### Logging In

1. Navigate to `/changes/login`
2. Enter credentials for a user with `role = 'changes'`
3. You'll be redirected to the dashboard

### Using Notifications

1. Look for the bell icon in the top navigation bar
2. A red badge shows the count of unread notifications
3. Click the bell to open the notifications modal
4. View system announcements and updates
5. Close notifications to mark them as read

### Managing Bookings

#### Viewing All Bookings

1. From the dashboard, click "All Bookings" or navigate to `/changes/bookings`
2. Use the filter form to narrow down results:
    - Enter search terms in the search box
    - Select status, service, or agent from dropdowns
    - Choose date ranges
3. Click "Search" to apply filters
4. Click "Clear Filters" to reset

#### Viewing Individual Bookings

1. Click the "View" button next to any booking
2. See complete booking details including:
    - Customer information
    - Agent details
    - Booking segments
    - Passenger information
    - Payment details

#### Editing Bookings

1. Click the "Edit" button next to any booking
2. Modify the required fields
3. Save changes (functionality ready for implementation)

## File Structure

```
app/
├── Http/Controllers/Changes/
│   ├── ChangesBookingsController.php    # Main bookings controller
│   └── ChangesLoginController.php       # Authentication controller

resources/views/changes/
├── auth/
│   └── login.blade.php                  # Login form
├── bookings/
│   ├── index.blade.php                  # Bookings list with filters
│   ├── show.blade.php                   # Individual booking view
│   └── edit.blade.php                   # Booking edit form
├── dashboard.blade.php                  # Main dashboard
└── layouts/
    └── changes.blade.php                # Panel layout template

resources/views/components/
├── user-notifications.blade.php        # Notification display component

resources/views/layouts/
└── notification-bell.blade.php         # Notification bell and modal

routes/web.php                            # Contains changes routes
```

## Security Features

- **Role-based Access**: Only users with `role = 'changes'` can access
- **CSRF Protection**: All forms include CSRF tokens
- **Session Security**: Automatic session regeneration on login
- **Input Validation**: Server-side validation on all inputs
- **SQL Injection Prevention**: Uses Eloquent ORM with parameterized queries

## API Endpoints

### Authentication

- `GET /changes/login` - Show login form
- `POST /changes/login` - Process login
- `POST /changes/logout` - Process logout

### Bookings

- `GET /changes/dashboard` - Dashboard view
- `GET /changes/bookings` - List all bookings with filters
- `GET /changes/bookings/{id}` - View specific booking
- `GET /changes/bookings/{id}/edit` - Edit booking form
- `PUT /changes/bookings/{id}` - Update booking

## Customization

### Adding New Filters

Edit `ChangesBookingsController@index()` to add new query conditions.

### Modifying the UI

Update the Blade templates in `resources/views/changes/` for custom styling.

### Extending Functionality

Add new methods to `ChangesBookingsController` and corresponding routes.

## Troubleshooting

### Cannot Access Panel

- Ensure user has `role = 'changes'` in database
- Check that routes are properly registered
- Verify middleware is applied correctly

### Filters Not Working

- Check database column names match query conditions
- Ensure date formats are correct
- Verify search terms are properly sanitized

### Performance Issues

- Add database indexes on frequently filtered columns
- Implement caching for large datasets
- Use eager loading for related models

## Contributing

When extending the Changes Panel:

1. Follow Laravel conventions
2. Add proper validation
3. Include error handling
4. Update this README for new features

## Support

For technical support or feature requests, please contact the development team.

---

**Note**: This panel provides powerful administrative capabilities. Ensure proper access controls and audit logging when deploying to production environments.

## Changes panel changes

1. When agent request a change in booking and click on submit for changes then it must be assigned to the changes team and show to them as a notification in booking requests where they can accept or reject that request.
2. If change team accept or reject the change in booking request then it must show in the booking requests in changing panel and the status will be updated in the agent panel that the request is accepted or rejected.
3. Also the Change Request Details message will be shown in bookings requests and also add an option to add a remark in changes panel booking requests.
4. Change panel me just booking show krni h change kuch nhi krna h and panel me to complete detail show hogi jaise charging team me hoti h but payment details show nhi krni h.
5. All bookings ko pnr se search krke dekh skte h changes team like agent panel

---
