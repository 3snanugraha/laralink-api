laralink-api/
├── config/
│   ├── database.php         # Database connection configuration
│   └── constants.php        # Application constants
├── controllers/
│   ├── UserController.php   # User registration and authentication
│   ├── ReportController.php # Report creation and management
│   ├── MaterialController.php # Educational materials
│   └── ContactController.php  # Contact information
├── models/
│   ├── User.php             # User model
│   ├── Report.php           # Report model
│   ├── ReportMedia.php      # Report media model
│   ├── ViolenceType.php     # Violence types model
│   ├── ViolenceMaterial.php # Educational materials model
│   └── ContactInfo.php      # Contact information model
├── utils/
│   ├── ApiResponse.php      # Standardized API response format
│   ├── Validator.php        # Input validation
│   ├── FileUploader.php     # Handle file uploads
│   └── Logger.php           # API request logging
├── middleware/
│   ├── ApiKeyMiddleware.php # API key validation
│   └── AuthMiddleware.php   # User authentication check
├── uploads/                 # Directory for uploaded files
│   ├── reports/             # Report media files
│   └── materials/           # Material images
├── .htaccess                # URL rewriting and security
├── index.php                # Entry point for all API requests
└── README.md                # API documentation
