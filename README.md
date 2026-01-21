# ♻️ Vangari-Link  
**A Web-Based Scrap Collection & Pricing Management System**

---

## 🔍 Project Overview
Vangari-Link is a web application that connects **household scrap sellers** with **scrap dealers (buyers)** on a single platform.

It enables sellers to request scrap pickup and view current market prices, while dealers can manage daily scrap prices and handle pickup requests efficiently.

The system is designed to be simple, role-based, and transparent, ensuring smooth interaction between sellers and buyers through a centralized web interface.

---

## 🎯 Objectives
- Digitize the scrap collection process  
- Enable transparent daily scrap price updates  
- Provide role-based dashboards for sellers and dealers  
- Simplify pickup request handling and order tracking  
- Ensure secure user authentication and data handling  

---

## 👥 User Roles & Features

### 🏠 Seller (Household)

#### Account Management
- Register as a seller  
- Login / Logout  
- Update personal information (name, area, phone, email)  
- Change password  
- Upload profile picture  
- Delete account  

#### Core Features
- View current scrap prices  
- Create scrap pickup requests  
- Track pickup request status  

---

### 🏪 Buyer (Dealer)

#### Account Management
- Register as a dealer  
- Login / Logout  
- Update profile information (owner name, shop name, area, phone, email)  
- Change password  
- Upload profile picture  
- Delete account  

#### Core Features
- Set daily scrap prices (one submission per day)  
- Manage pickup requests in their area  
- Accept pickup requests  
- Update order status (Pending → Dispatched → Collected)  
- View order history with pagination  

---

## 🛠️ Technology Stack

### Server & Environment
- **XAMPP** (Local development environment)  
- **Apache** (Web server)  
- **MySQL** (Relational database)  
- **Localhost** for development and testing  

### Backend
- **PHP** (Core backend language)  
- **Custom MVC Architecture**  
- **Simple Routing System** (Single entry point)  

### Frontend
- **HTML** (Structure)  
- **CSS** (Styling)  
- **JavaScript** (Client-side interaction)  

### Communication (for dynamic data exchange)
- **AJAX**  
- **JSON**  

---

## 🧭 Routing System
The application uses a **single entry point routing system**.

All requests are handled through:
public/index.php

Routes follow this format:
index.php?url=module/action

### Examples
- `auth/login`  
- `auth/register_seller`  
- `dealer/dashboard`  
- `household/dashboard`  

### Router Responsibilities
- Reads the URL parameter  
- Determines the module and action  
- Loads the corresponding controller  
- Executes the requested method  

---

## 🔐 Authentication & Security
- Password hashing using `password_hash()`  
- Session-based authentication  
- Optional **“Remember Me”** using cookies  
- Role-based access control (seller vs buyer)  
- Prepared SQL statements to prevent SQL injection  
- Input validation on both client-side and server-side  
