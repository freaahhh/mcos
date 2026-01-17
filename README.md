# 🍔 MCOS - Melati Chillz Ordering System

MCOS is a full-stack PHP web application designed to streamline restaurant operations. asal lulus ict jadi lah ek

## Prerequisites

Before you begin, ensure you have met the following requirements to run the environment locally:

* **Local Server**: Install **XAMPP**, **WAMP**, or **MAMP**.
* **PHP Version**: Ensure your local server is running **PHP 8.0** or higher.
* **Database**: **MySQL** (managed via phpMyAdmin).
* **Git**: Installed for repository cloning.
* **Write Permissions**: Ensure the `images/` directory is writable for food and category image uploads.

---

## UI

### Customer Interface

* **Smart Menu**: Search for dishes and filter by category.
* **Order Tracking**: Real-time status updates for orders (Pending, On Delivery, Delivered).
* **User Profile**: Personal dashboard to manage contact details, dorm addresses, and bank information for payment verification.

### Admin & Staff Dashboard

* **Advanced Analytics**: Track Sales (Monthly/Yearly), view the top 2 best-selling items with images, and read live customer feedback.
* **Inventory Management**: Full CRUD support for food categories and menu items with image processing.
* **Work Management**:
* **Attendance Tracking**: Integration with `WORK_LOG` to monitor staff hours.
* **Employment Classification**: Differentiates between Full-Time (salary-based) and Part-Time (hourly-rated) staff.
* **Supervisory Chain**: Profiles display assigned supervisor details and employment status.


* **Order Fulfillment**: Master control to update delivery and payment statuses.

---

## MCOS Database

The system utilizes a relational schema including the following primary tables:

* `STAFF`, `CUSTOMER`, `MENU`, `CATEGORY`, `ORDER`, `WORK_LOG`, `FULL_TIME`, `PART_TIME`, `PAYMENT`, and `DELIVERY`.

---

## Installation (read prerequisites first)

1. **Change directory to htdcos: (paste on cmd)**
```bash
cd C:\xampp\htdocs

```

1. **Clone the Repo: (paste on cmd)**
```bash
git clone https://github.com/liyanawr/mcos.git

```

2. **Database Import**:
* Open **phpMyAdmin**.
* Create a database named `mcos`.
* Import the provided `.sql` file from the `/database` folder.

3. **Run**:
Visit `http://localhost/mcos/login.php` in your browser.

# Latest update/issue (current date: 23/12/2025)
- incomplete database (data and images/illustrations)
