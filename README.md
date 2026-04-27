# Events CRUD Application

This is a PHP MVC application for managing events and locations, integrated with the Feane restaurant template.

## Features

- **Events Management**: Create, Read, Update, Delete events
- **Locations Management**: Associate events with locations
- **MVC Architecture**: Model-View-Controller pattern
- **Bootstrap Integration**: Uses the existing template styling

## Requirements

- **Web Server**: Apache/Nginx with PHP support (XAMPP, WAMP, or similar)
- **PHP**: Version 7.0 or higher
- **MySQL**: Version 5.6 or higher
- **phpMyAdmin**: For database management

## Setup Instructions

### 1. Database Setup

1. Open phpMyAdmin
2. Create a new database called `feane_events`
3. Import the `create_tables.sql` file to create the necessary tables and sample data

### 2. Configuration

1. Open `config/config.php`
2. Update the database credentials if needed:
   - `DB_USER`: Your MySQL username (default: 'root')
   - `DB_PASS`: Your MySQL password (default: '')
3. Update `BASE_URL` to match your local server setup (e.g., 'http://localhost/feane-1.0.0/')

### 3. File Structure

```
feane-1.0.0/
├── config/
│   └── config.php          # Database configuration
├── controllers/
│   └── EventController.php # Handles event CRUD operations
├── lib/
│   ├── Database.php        # Database connection class
│   └── Model.php           # Base model class
├── models/
│   ├── Event.php           # Event model
│   └── Location.php        # Location model
├── views/
│   ├── layout.php          # Main layout template
│   └── events/
│       ├── index.php       # List all events
│       ├── create.php      # Create new event form
│       ├── edit.php        # Edit event form
│       └── show.php        # View single event
├── events.php              # Main routing file
├── create_tables.sql       # Database schema
└── [existing template files]
```

### 4. Access the Application

- Navigate to `events.php` in your browser to access the events management system
- The Events link has been added to the navigation menu in all template pages

## Database Schema

### Tables

- **locations**: Stores location information (id, name, address, city, country, capacity)
- **events**: Stores event information (id, title, description, date, location_id, price, max_attendees, status)

### Sample Data

The SQL file includes sample locations and events for testing.

## Usage

### Managing Events

1. **View Events**: Go to `events.php` to see all events in a table
2. **Add Event**: Click "Add New Event" and fill out the form
3. **Edit Event**: Click "Edit" next to any event to modify it
4. **Delete Event**: Click "Delete" and confirm to remove an event
5. **View Event**: Click "View" to see detailed information about an event

### Event Fields

- **Title**: Event name
- **Description**: Detailed description
- **Date & Time**: When the event occurs
- **Location**: Where the event takes place (from locations table)
- **Price**: Cost to attend (optional)
- **Max Attendees**: Maximum number of participants (optional)
- **Status**: Active, Cancelled, or Completed

## Technologies Used

- **PHP**: Server-side scripting
- **MySQL**: Database
- **PDO**: Database abstraction
- **Bootstrap**: CSS framework (from template)
- **MVC Pattern**: Application architecture

## Security Notes

- This is a basic implementation for demonstration purposes
- In production, add proper input validation, authentication, and authorization
- Consider using prepared statements (already implemented) and escaping output (partially done)

## Troubleshooting

1. **Database Connection Error**: Check your database credentials in `config/config.php`
2. **Blank Page**: Ensure PHP error reporting is enabled and check error logs
3. **Styling Issues**: Make sure the CSS files are accessible and the paths are correct