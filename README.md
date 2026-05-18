<div align="center">
  <img src="public/images/favicon.png" width="100" alt="ParkEase Logo">
  <h1>ParkEase: Smart Mobility Operating System</h1>
  <p><strong>A Premium, Real-time Parking Management Ecosystem</strong></p>

  <p>🚀 <strong>Live Production Link:</strong> <a href="https://parkease-3as5.onrender.com/" target="_blank"><strong>https://parkease-3as5.onrender.com/</strong></a></p>

  <a href="https://parkease-3as5.onrender.com/" target="_blank">
    <img src="https://img.shields.io/badge/Live_Demo-Visit_ParkEase-0E5E6F?style=for-the-badge&logo=render&logoColor=white" alt="Live Demo">
  </a>
  <a href="https://github.com/ShivangChaurasia/ParkEase-Cost-Effective-Parking_System">
    <img src="https://img.shields.io/badge/Project_Status-Completed-success?style=for-the-badge&logo=checkmarx&logoColor=white" alt="Project Status">
  </a>

  <br><br>

  [![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
  [![MongoDB](https://img.shields.io/badge/MongoDB-5.0+-47A248?style=for-the-badge&logo=mongodb&logoColor=white)](https://www.mongodb.com)
  [![Clerk](https://img.shields.io/badge/Clerk-Auth-6C47FF?style=for-the-badge&logo=clerk&logoColor=white)](https://clerk.com)
  [![Razorpay](https://img.shields.io/badge/Razorpay-Payment-02042B?style=for-the-badge&logo=razorpay&logoColor=white)](https://razorpay.com)
</div>

---

## 🌟 Overview
ParkEase is a sophisticated, SaaS-grade parking management platform designed to streamline urban mobility. It transitions from traditional parking solutions into a **Real-time Operating System** for both users looking for space and hosts managing their inventory.

Featuring a **Premium Glassmorphism UI**, the platform emphasizes operational clarity, visual excellence, and a seamless end-to-end booking lifecycle.

> [!NOTE]
> **Project Status: Completed & Deployed**
> The active development phase of ParkEase is officially complete. The fully functional, production-ready application has been successfully deployed to Render. The code is archived in its current finalized state, and no further updates are planned.

---

## ✨ Key Features

### 💎 Premium Experience
- **Interactive UI**: Built with a "Stripe/Linear" inspired aesthetic using Deep Teal (`#0E5E6F`) and Aqua Cyan.
- **Dynamic Animations**: Integrated local Lottie animations for a high-end, responsive feel.
- **Intelligent Dashboard**: Categorized session management (Active, Upcoming, Past, Cancelled) with live countdown timers.

### 🛡️ Secure Infrastructure
- **Enterprise Auth**: Powered by Clerk.js for robust, passwordless, and multi-role (User/Host) identity management.
- **KYC Gating**: Automated onboarding flow ensuring all parking hosts are verified before going live.
- **Data Scalability**: Leveraging MongoDB for high-performance geospatial searches and flexible schema management.

### 💳 Transactional Excellence
- **Razorpay Integration**: Production-ready UPI-First payment flow with cryptographic signature verification.
- **Automated Invoicing**: Real-time PDF ticket and receipt generation with QR validation tokens.
- **Smart Refunds**: Time-based cancellation logic with automated refund reconciliation (100% / 50% / 0% windows).

### 🔍 Discovery Engine
- **Geospatial Search**: Advanced Haversine distance filtering based on GPS coordinates or Pincode.
- **Interactive Map**: Visual parking lot discovery with real-time slot availability indicators.

---

## 🛠️ Tech Stack
| Layer | Technology |
| :--- | :--- |
| **Backend** | Laravel 11 (PHP 8.4+) |
| **Database** | MongoDB (NoSQL) |
| **Auth** | Clerk.js (Identity-as-a-Service) |
| **Payments** | Razorpay SDK |
| **UI** | Blade, Bootstrap 5, Vanilla JS, CSS3 (Glassmorphism) |
| **PDF** | Barryvdh DomPDF |

---

## 🚀 Getting Started

### 1. Prerequisites
- PHP 8.4+
- Composer
- MongoDB Instance
- Node.js & NPM

### 2. Installation
```bash
# Clone the repository
git clone https://github.com/ShivangChaurasia/ParkEase-Cost-Effective-Parking_System.git
cd ParkEase

# Install dependencies
composer install --ignore-platform-reqs
npm install

# Setup Environment
cp .env.example .env
php artisan key:generate
```

### 3. Configuration
Add your API keys to the `.env` file:
```env
# Clerk
VITE_CLERK_PUBLISHABLE_KEY=pk_test_...
CLERK_JS_URL=...

# Razorpay
RAZORPAY_KEY=rzp_test_...
RAZORPAY_SECRET=...
```

### 4. Launch
```bash
php artisan serve
```

### 🌐 Live Production Demo
The application is pre-configured for production deployment (using the optimized Docker environment) and is live at:
👉 **[https://parkease-3as5.onrender.com/](https://parkease-3as5.onrender.com/)**

---

## 📸 Interface Showcase

<div align="center">
  <h3>✨ Premium Glassmorphism Interface ✨</h3>
  
  <p align="center">
    <img src="public/Project_SS/image1.png" width="90%" alt="ParkEase Dashboard Preview">
  </p>
  
  <br>
  
  <p align="center">
    <img src="public/Project_SS/image2.png" width="48%" alt="ParkEase Feature Grid">
    <img src="public/Project_SS/image3.png" width="48%" alt="ParkEase Screen Flow">
  </p>

  <br>

  <p align="center">
    <img src="public/Project_SS/image4.png" width="48%" alt="ParkEase Booking Interface">
    <img src="public/Project_SS/image5.png" width="48%" alt="ParkEase Payment Interface">
  </p>

  <br>

  <p align="center">
    <img src="public/Project_SS/image6.png" width="31%" alt="ParkEase Map Discovery">
    <img src="public/Project_SS/image7.png" width="31%" alt="ParkEase Profile Dashboard">
    <img src="public/Project_SS/image8.png" width="31%" alt="ParkEase Receipt and Tickets">
  </p>

  <br>

  <h4>👥 User & Host Perspectives</h4>
  <p align="center">
    <img src="public/images/user_app_screenshot.png" width="48%" alt="User Dashboard">
    <img src="public/images/host_app_screenshot.png" width="48%" alt="Host Dashboard">
  </p>
</div>

---

## 👥 Project Contributors

We are extremely proud of the hard work and dedication that went into building **ParkEase**. Meet the team:

| 👩‍💻 Riya | 👨‍💻 Himanshu Shekhar | 👨‍💻 Shivang Chaurasia |
| :--- | :--- | :--- |
| **GitHub:** [briya1597](https://github.com/briya1597) <br> **Email:** [briya1597@gmail.com](mailto:briya1597@gmail.com) <br> **Portfolio:** [riyacse.vercel.app](https://riyacse.vercel.app/) | **GitHub:** [himanshushekharon](https://github.com/himanshushekharon) <br> **Email:** [himanshushekharon@gmail.com](mailto:himanshushekharon@gmail.com) <br> **Portfolio:** [portfolio-pes-black.vercel.app](https://portfolio-pes-black.vercel.app/) | **GitHub:** [ShivangChaurasia](https://github.com/ShivangChaurasia) <br> **Email:** [shiva17ng@gmail.com](mailto:shiva17ng@gmail.com) <br> **Portfolio:** [shivangchaurasia.vercel.app](https://shivangchaurasia.vercel.app/) |

---

## 📄 License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

<div align="center">
  <p>Built with ❤️ for a Seamless Urban Future.</p>
</div>
