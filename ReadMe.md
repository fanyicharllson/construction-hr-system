# 🏗️ BuildMaster Construction HR Management System

![PHP Version](https://img.shields.io/badge/PHP-8.1+-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=flat&logo=docker&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green.svg)
![Status](https://img.shields.io/badge/Status-Production%20Ready-brightgreen)

A comprehensive Human Resource Management System for construction companies, featuring employee management, department organization, and full CRUD operations with role-based access control.

## 📋 Table of Contents
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Quick Start](#quick-start)
- [Installation](#installation)
- [Database Schema](#database-schema)
- [Project Structure](#project-structure)
- [Usage Guide](#usage-guide)
- [API Endpoints](#api-endpoints)
- [Screenshots](#screenshots)
- [Testing](#testing)
- [Deployment](#deployment)
- [Contributing](#contributing)
- [License](#license)

## ✨ Features

### 👥 User Management
- **Employee Registration** - Complete profile creation with personal details
- **Secure Authentication** - Password hashing with bcrypt
- **Role-Based Access** - Admin and Employee roles with different permissions
- **Password Recovery** - Email-based password reset system

### 🏢 Department Management
- **10 Construction Departments**:
  - Management
  - Construction
  - Engineering
  - Safety
  - Procurement
  - HR
  - Finance
  - Logistics
  - Maintenance
  - Quality Control

### 📊 Employee Management (CRUD Operations)
- **Create** - Add new employees with all details
- **Read** - View employee information in organized tables
- **Update** - Edit employee details with validation
- **Delete** - Remove employee records with confirmation

### 🔒 Security Features
- Password hashing (bcrypt)
- SQL injection prevention (prepared statements)
- Session-based authentication
- XSS protection
- Role-based access control

### 🎨 UI/UX Highlights
- Modern gradient design
- Fully responsive layout
- Smooth animations and transitions
- Status badges with color coding
- Interactive dropdown menus
- Real-time form validation

## 🛠️ Tech Stack

| Technology | Version | Purpose |
|------------|---------|---------|
| PHP | 8.1+ | Backend logic & API |
| MySQL | 8.0 | Database management |
| HTML5 | - | Structure |
| CSS3 | - | Styling & animations |
| JavaScript | ES6 | Interactivity |
| Docker | 20.10+ | Containerization |
| Apache | 2.4 | Web server |

## 🚀 Quick Start

### Prerequisites
- Docker Desktop (Recommended) OR
- XAMPP/WAMP/LAMP (Alternative)
- Git

### One-Click Setup with Docker

```bash
# Clone the repository
git clone https://github.com/fanyicharllson/construction-hr-system.git
cd construction-hr-system

# Start containers
docker-compose up -d

# Wait 30 seconds for database initialization
# Access the application
open http://localhost:8080