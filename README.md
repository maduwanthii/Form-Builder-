## 📝 Form Builder

## 🚀 Project Overview

A full-stack Form Builder web app built with **Vue.js** (frontend) and **Laravel (PHP)** (backend).  
Users can create, view, update, and delete forms with a MySQL database connected.

## 📋 Prerequisites

- Node.js v16 or newer  
- npm, yarn, or pnpm package manager  
- PHP 8.x or newer  
- Composer  
- MySQL database  

## 🎥 Project Demo / Video

Screen recording link:  
[Form Builder Demo Video]([https://your-video-link.com](https://drive.google.com/file/d/1g1pCUWQXkeJGQHZj7rcDy7Cw2cQtq63E/view)) 

## ⚙️ Setup & Run

## Clone the repository

```bash
git clone https://github.com/maduwanthii/Form-Builder-.git
cd Form-Builder-

npm install  
npm run dev


composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve


## 🔗 API Endpoints

Method	Endpoint	Description
POST	/api/forms	Create a new form
GET	/api/forms	Get all forms
GET	/api/forms/{id}	Get a specific form
PUT	/api/forms/{id}	Update a form
DELETE	/api/forms/{id}	Delete a form
