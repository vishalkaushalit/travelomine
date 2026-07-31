# Travelomine Panel Functionality Document

This document lists the functionality managed by the different role-based panels in the Travelomine application. It is based on the routes, controllers, and panel navigation currently present in the project.

## Panel Overview

| Panel | Role | Main Responsibility |
|---|---|---|
| Admin | `admin` | Complete system administration, master data, users, bookings, reports, and monitoring |
| Agent | `agent` | Create and manage bookings, customer communication, charging requests, and call records |
| Charging | `charge` | Process booking charges, payment authorization, and payment links |
| Customer Support | `support` | Review and update bookings and manage chargeback cases |
| MIS | `mis` | Booking data management, quality control, and exports |
| MIS Manager | `mis-manager` | Supervisory review and correction of booking information |
| Changes | `changes` | Process post-booking change requests and update bookings |

All panels have role-protected login/logout, dashboard access, and a user profile page unless otherwise noted.

## 1. Admin Panel

The Admin panel manages the entire application and provides the broadest access.

### Dashboard and Monitoring

- View system dashboard statistics and operational summaries.
- View recent system activity.
- View activity logs with filtering and pagination.
- View currently online users.
- View the latest activity-log data through the dashboard refresh endpoint.

### User and Agent Management

- View the list of agents.
- View individual agent information.
- Enable or disable an agent account.
- Create users for the supported system roles.
- Edit user details and roles.
- Activate or deactivate users.
- Block or unblock users.
- Delete users.

### Booking Management

- View all bookings.
- Search and filter booking records.
- View complete booking details, including passengers, flights, payment details, services, remarks, and status information.
- Edit and update booking information.
- Delete bookings.
- Upload/import old booking records.
- Export one booking to CSV.
- Export all filtered bookings.
- Export selected bookings.
- Edit ticket information and generate ticket PDFs.
- Access the ticket/PDF template editing functionality implemented in the Admin controllers.
- View and update booking payment status, available status transitions, and payment-status history where integrated into the booking screens.

### Agent MCO Reporting

- View MCO data for all agents.
- Filter MCO records by agent and date.
- View a specific agent's MCO details.
- Export overall or agent-specific MCO information.

### Call Log Management

- View call logs created by agents.
- Filter call logs.
- View individual call-log details.
- Export call logs to CSV.

### Master Data and Configuration

- Create, view, edit, and delete airlines.
- Create, view, edit, and delete merchants.
- Manage booking dropdown/settings options used by booking forms.
- Add and delete configurable booking options.

### Notification Management

- View system notifications.
- Create and edit notifications.
- Duplicate an existing notification.
- Activate or deactivate notifications.
- Delete notifications.
- View notification statistics.

### Reports

- View operational reports.
- Apply report filters.
- Export report results.

### Charging Access

- Admin users are also permitted by the role middleware to access the Charging panel and its charging workflows.

## 2. Agent Panel

The Agent panel manages the sales/booking workflow and sends work to other operational teams.

### Dashboard

- View the agent's booking and work summary.
- Access assigned work and common booking actions.

### Booking Creation and Management

- Create a new booking.
- Paste/decode an itinerary and use the parsed result to create a booking.
- Add passenger and flight-segment information.
- View bookings belonging to or available to the agent.
- Search bookings using the booking-search form.
- Open full booking details.
- Edit permitted booking information.
- Update a booking's PNR.
- Search for a booking by PNR and record booking updates.
- Add remarks to a booking and view its remarks timeline.

### Customer Authorization

- Edit the customer authorization message.
- Preview the authorization email/page.
- Send or resend authorization to the customer.
- Mark the authorization process as completed.

### Charging Workflow

- Open the charging form for a booking.
- Submit/assign a booking to the Charging team.
- Provide the information required by the Charging team to process the payment.

### Assignment and Change Requests

- Assign a booking to an operational team.
- View assignments created by the logged-in agent.
- View assignment details and their current statuses.
- Access booking-request information.

### Call Logs

- Create a customer call log.
- View the agent's call logs.
- Filter call records.
- View an individual call-log entry.

### MCO

- View the logged-in agent's MCO records.
- Filter MCO data.
- Export the agent's MCO report.

### Notifications and Profile

- View panel notifications.
- Mark individual or all notifications as read through shared notification services.
- View the logged-in user's profile.

## 3. Charging Panel

The Charging panel manages payment processing work received from agents.

### Dashboard and Assignments

- View charging assignments with status and date filters.
- View assignment and booking details.
- Mark a booking/assignment as viewed.
- Accept or reject a charging assignment.
- Record assignment remarks.
- Complete an accepted assignment.
- View accepted, pending, rejected, and completed work as supported by the dashboard.

### Secure Card and Charge Handling

- View the booking information needed for charging.
- Access/decrypt protected card details through the controlled charging action.
- Accept an assigned booking for processing.
- Update the workflow after the charge is processed.

### Customer Authorization

- Edit and preview the customer authorization content.
- Send or resend authorization emails.
- Mark authorization as done.
- Use the available authorization templates for new booking, change, exchange, cancellation, refund, baggage, seat, pet, name correction, future credit, and other cases.

### Payment Links

- Create a secure payment link for a booking.
- Store the payment-link amount and expiry information.
- Email the payment link to the customer.
- Allow the customer to complete payment through the public payment page.

### Notifications and Profile

- View charging notifications.
- Use shared notification read/unread actions.
- View the logged-in user's profile.

## 4. Customer Support Panel

The Customer Support panel manages customer-service booking reviews and chargeback records.

### Dashboard

- View support-related booking statistics and summaries.

### Booking Support

- View all bookings.
- Search and filter booking records.
- View full booking details.
- Edit and update booking information permitted to Support users.
- Review booking status and related customer/payment information shown on the booking pages.

### Chargeback Management

- Create a chargeback record against a booking.
- Record chargeback details such as type, amount, reason, evidence, dates, and notes according to the support form.
- Trigger the application's chargeback notification flow when a chargeback is created or updated by the implemented service.

### Profile

- View the logged-in support user's profile.

## 5. MIS Panel

The MIS panel manages booking data verification, corrections, and exports.

### Dashboard

- View MIS booking totals and operational summaries.

### Booking Data Management

- View all bookings.
- Search and filter bookings.
- View complete booking details.
- Edit and update booking data.
- Delete a booking.
- Access booking statuses and related booking information displayed by the MIS screens.

### Data Export

- Export all bookings based on the current filters.
- Export selected booking records.

### Notifications and Profile

- View MIS notifications.
- Use shared notification read/unread actions.
- View the logged-in user's profile.

## 6. MIS Manager Panel

The MIS Manager panel provides supervisory access to booking data maintained by the MIS operation.

### Dashboard

- View MIS Manager booking statistics and summary information.

### Booking Review and Correction

- View all bookings.
- Search and filter booking records.
- View full booking details.
- Edit and update booking, passenger, flight, service, payment, and related information handled by the manager update workflow.
- Delete a booking.
- Generate the booking-change notifications/emails implemented for MIS Manager updates.

### Notifications and Profile

- View MIS Manager notifications, including booking-change notifications.
- Use shared notification read/unread actions.
- View the logged-in user's profile.

## 7. Changes Panel

The Changes panel handles post-booking service and itinerary change requests.

### Dashboard

- View Changes-team workload and booking summaries.

### Booking Management

- View all bookings.
- Search and filter booking records.
- View full booking details.
- Edit and update booking information needed to complete a requested change.
- Delete a booking where the role is permitted by the current route/controller implementation.

### Booking Requests

- View bookings assigned to the Changes team.
- Review the booking and assignment details.
- Add a Changes-team remark to the booking timeline.
- Accept/complete a booking request.
- Reject a booking request.
- Track request state using the assignment status.

### Notifications and Profile

- View Changes-team notifications.
- Receive assignment notifications for newly assigned work.
- Use shared notification read/unread actions.
- View the logged-in user's profile.

## 8. Shared Functionality Across Panels

The following functionality is used by more than one panel:

- Role-based authentication and route access control.
- User login, logout, and session management.
- User profile display.
- Notification count, unread-notification list, mark-as-read, and mark-all-as-read actions.
- Booking status creation, synchronization, viewing, and updating for authenticated users where the UI exposes those actions.
- Booking remarks and operational history.
- Email notifications for new bookings, booking assignment, booking status changes, payment links, charging assignments, customer authorization, chargebacks, and MIS Manager booking changes.
- Activity logging for important user and booking actions.

## 9. Customer-Facing Functions Connected to the Panels

These are not staff panels, but they support workflows initiated from the Agent and Charging panels.

### Public Payment

- Open a payment page using a secure token.
- Validate link status and expiry.
- Submit a payment.
- Display payment success, expired-link, or already-paid states.
- Store and reconcile payment/transaction status through the payment services.

### Customer Consent/Authorization

- Open a signed customer-consent link.
- Review booking and authorization information.
- Prevent simple URL tampering through signed-link validation.

## 10. Current Navigation/Implementation Notes

- Some panel sidebars contain placeholder links such as **Reports** or **Settings** that currently point to `#`; these should not be treated as completed panel features until routes and controllers are connected.
- A `manager` layout exists, but there is no active Manager role route group in the current main route file. It is therefore not listed as a functioning standalone panel.
- Several routes are duplicated in the route file (especially Admin and MIS booking routes). The functionality is documented once under the relevant panel.
- Some controllers exist for payment status, PDF editing, and MIS exports even when every action is not linked directly in a sidebar. They are listed above only where the surrounding booking workflow or routes indicate intended/current use.

---

**Document source:** `routes/web.php`, role-specific controllers, layouts, and views in the current Travelomine codebase.  
**Last reviewed:** July 31, 2026.
