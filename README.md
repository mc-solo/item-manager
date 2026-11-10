# Item Management System

Hey Miki and Mesi

This is your personal item management system, built to help you keep track of all your items in one organized place. Here's what we've got so far and what's coming up next!

## What's Working Now

**Dashboard** - A clean overview of your items and key metrics // this will not be part of the first deployment
**Item Management** - Add, view, and manage all your items
**Responsive Design** - Works great on both desktop and mobile
**Modern UI** - Built with a clean, user-friendly interface // elegant and minimal
**Secure Authentication** - Keep your data safe with secure login //email + password
**Roles (admin / staff)** - Admin can do everything, including deleting devices and managing users at `/admin/users`. Staff can create, view, edit and complete devices, but cannot delete them or change roles. New accounts start as staff; the seeded shop account (`boss@shop.com`) is admin.
**Advanced Search and Filtering** - Search by client, order number, model, serial number, IMEI and issue. Filter by status, device type, amount range and date (today, yesterday, this week, last week, this month, last month, or a custom range), and sort by date, amount, client or order number.
**Bulk Actions** - Tick devices in the list, then mark them pending, in progress, completed, cancelled or delivered in one go. Bulk delete is admin only.
**Export** - Export the currently filtered list as CSV from the Devices page.

## What's Coming Next

I'm excited to share some of the exciting features I'm working on:

### Client-Side API
- Faster, smoother user experience
- Expose API for external use
- Better error handling and feedback

### Other Essential Updates
- Customizable views and preferences
- Import functionality
- And much more!

## Getting Started

1. Log in with your credentials
2. Check out your dashboard for an overview //for now just go to the devices module
3. Start adding and managing your items

To make someone an admin, an existing admin changes their role at `/admin/users`. After pulling this change, run `php artisan migrate` to add the `role` column to users.

## Need Help?

If you run into any issues or have feature requests, just let me know! I'm here to make this system work perfectly for you. You know my number, just hit me :)

Wondwosen A.
