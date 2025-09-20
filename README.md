# Form Builder Application

A modern, full-stack form builder application that allows users to create, manage, and collect responses from custom forms. Built with Laravel (backend API) and Vue.js 3 (frontend).

## 🚀 Features

### Form Management
- **Create Custom Forms** - Build forms with multiple field types
- **Field Types Support** - Text, textarea, radio buttons, checkboxes
- **Form Validation** - Required field validation and custom rules
- **Form Preview** - Real-time preview of forms as you build them
- **Form Editing** - Modify existing forms with ease

### Response Management
- **Collect Submissions** - Receive form responses from users
- **View Submissions** - Browse and search through all submissions
- **Export Data** - Export submissions to CSV format
- **Search & Filter** - Find specific submissions with advanced filters

### User Interface
- **Responsive Design** - Works seamlessly on desktop and mobile
- **Modern UI** - Clean, professional interface with Tailwind CSS
- **Real-time Updates** - Dynamic form building with instant preview
- **Intuitive Navigation** - Easy-to-use sidebar navigation

## 🛠️ Technology Stack

### Backend (API)
- **Laravel 12** - PHP framework for robust backend API
- **MySQL** - Database for storing forms and submissions
- **PHP 8.2** - Modern PHP with latest features
- **RESTful API** - Clean API endpoints for frontend consumption

### Frontend
- **Vue.js 3** - Progressive JavaScript framework
- **Vue Router** - Client-side routing
- **Composition API** - Modern Vue.js development pattern
- **Tailwind CSS** - Utility-first CSS framework
- **Vite** - Fast build tool and development server
- **Lucide Vue** - Beautiful icon library

## 📁 Project Structure

```
vxd_practical_test/
├── form-builder-api/          # Laravel Backend API
│   ├── app/
│   │   ├── Http/Controllers/  # API Controllers
│   │   └── Models/           # Eloquent Models
│   ├── database/
│   │   └── migrations/       # Database migrations
│   ├── routes/
│   │   └── api.php          # API routes
│   └── config/              # Configuration files
└── form-builder-frontend/     # Vue.js Frontend
    ├── src/
    │   ├── components/       # Vue components
    │   ├── views/           # Page components
    │   ├── router/          # Vue Router config
    │   └── services/        # API service layer
    └── public/              # Static assets
```

## ⚡ Quick Start

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js 16+ and npm
- MySQL database

### Backend Setup

1. **Navigate to the API directory:**
   ```bash
   cd form-builder-api
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Set up environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure database in `.env`:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=vxd_form_builder
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

5. **Run migrations:**
   ```bash
   php artisan migrate
   ```

6. **Start the development server:**
   ```bash
   php artisan serve
   ```
   API will be available at `http://127.0.0.1:8000`

### Frontend Setup

1. **Navigate to the frontend directory:**
   ```bash
   cd form-builder-frontend
   ```

2. **Install dependencies:**
   ```bash
   npm install
   ```

3. **Start the development server:**
   ```bash
   npm run dev
   ```
   Frontend will be available at `http://localhost:5173`

## 📚 API Documentation

### Forms API

#### Get All Forms
```http
GET /api/forms
```

#### Create Form
```http
POST /api/forms
Content-Type: application/json

{
  "name": "Contact Form",
  "description": "A simple contact form",
  "fields": [
    {
      "label": "Name",
      "type": "text",
      "required": true
    },
    {
      "label": "Email",
      "type": "text",
      "required": true
    }
  ]
}
```

#### Get Form
```http
GET /api/forms/{id}
```

#### Update Form
```http
PUT /api/forms/{id}
```

#### Delete Form
```http
DELETE /api/forms/{id}
```

### Submissions API

#### Submit Form
```http
POST /api/forms/{id}/submit
Content-Type: application/json

{
  "field_1": "John Doe",
  "field_2": "john@example.com"
}
```

#### Get Form Submissions
```http
GET /api/forms/{id}/submissions
```

#### Get Single Submission
```http
GET /api/forms/{formId}/submissions/{submissionId}
```

## 🎯 Usage Guide

### Creating a Form

1. Navigate to the **Dashboard**
2. Click **"Create Form"** or use the sidebar navigation
3. Fill in form details (name, description)
4. Add fields using the **"Add Field"** button
5. Configure each field (label, type, required status)
6. Preview your form in real-time
7. Save the form

### Managing Submissions

1. Go to **Forms** list
2. Click on a form to view its submissions
3. Use the search and filter options to find specific submissions
4. Click on any submission to view detailed responses
5. Export data using the **"Export CSV"** button

### Form Field Types

- **Text Field** - Single-line text input
- **Textarea** - Multi-line text input
- **Radio Buttons** - Single choice selection
- **Checkboxes** - Multiple choice selection

## 🔧 Configuration

### CORS Configuration

The API includes CORS support for cross-origin requests. Configure allowed origins in `config/cors.php`:

```php
'allowed_origins' => [
    'http://localhost:5173',
    'http://127.0.0.1:5173',
],
```

### Environment Variables

Key environment variables for the Laravel API:

```env
APP_NAME="Form Builder API"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vxd_form_builder
DB_USERNAME=root
DB_PASSWORD=
```

## 🚀 Deployment

### Backend Deployment (Railway)

1. **Configure `railway.toml`:**
   ```toml
   [build]
   builder = "heroku/php"
   buildCommand = "composer install --no-dev --optimize-autoloader && php artisan config:cache"

   [deploy]
   startCommand = "vendor/bin/heroku-php-apache2 public/"
   ```

2. **Set environment variables in Railway:**
   - `APP_KEY` (generate with `php artisan key:generate --show`)
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - Database connection variables

3. **Deploy to Railway:**
   ```bash
   railway login
   railway link
   railway deploy
   ```

### Frontend Deployment

Build the frontend for production:

```bash
npm run build
```

Deploy the `dist` folder to your preferred hosting service (Netlify, Vercel, etc.).

## 📝 Database Schema

### Forms Table
- `id` - Primary key
- `name` - Form title
- `description` - Form description
- `is_active` - Form status
- `created_at`, `updated_at` - Timestamps

### Form Fields Table
- `id` - Primary key
- `form_id` - Foreign key to forms
- `label` - Field label
- `type` - Field type (text, textarea, radio, checkbox)
- `required` - Whether field is required
- `options` - JSON array for radio/checkbox options
- `order_index` - Field display order

### Form Submissions Table
- `id` - Primary key
- `form_id` - Foreign key to forms
- `submitted_at` - Submission timestamp
- `ip_address` - Submitter's IP address

### Submission Answers Table
- `id` - Primary key
- `submission_id` - Foreign key to submissions
- `form_field_id` - Foreign key to form fields
- `answer_value` - JSON array of answers

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

## 🐛 Troubleshooting

### Common Issues

**CORS Errors:**
- Ensure the frontend URL is added to `config/cors.php`
- Check that the API server is running

**Database Connection:**
- Verify database credentials in `.env`
- Ensure MySQL service is running

**Icon Import Errors:**
- Make sure `lucide-vue-next` is installed: `npm install lucide-vue-next`
- Use correct icon names from the Lucide library

**Build Errors:**
- Clear Vite cache: `rm -rf node_modules/.vite`
- Reinstall dependencies: `npm install`

## 📞 Support

For questions and support, please open an issue in the repository or contact the development team.

---

**Built with ❤️ for VXD Practical Test**
