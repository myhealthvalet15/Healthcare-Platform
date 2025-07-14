# Corporate Healthcare Platform

A comprehensive healthcare management system designed for large corporations to manage their internal healthcare centers, employee health records, diagnostics, prescriptions, and health tracking.

## 🏥 Project Overview

This platform serves major corporations including **L&T, Hyundai, Apollo, Nissan, KIA** and other industry leaders by providing a complete digital healthcare solution for their internal medical facilities. The system enables efficient management of employee health data, medical consultations, diagnostics, prescriptions, and comprehensive health tracking.

### What it does:
- **Employee Health Tracking**: Comprehensive health record management for all company employees
- **Medical Consultations**: Digital platform for doctor-patient interactions within corporate healthcare centers
- **Prescription Management**: Streamlined prescription creation, tracking, and fulfillment
- **Diagnostic Tracking**: Complete diagnostic history and report management
- **Multi-tier Access Control**: Role-based access for different user types (Corporate Admin, Employees, Doctors, Lab Users)
- **Healthcare Analytics**: Insights and reporting for corporate healthcare decision-making


## 🔄 Migration & Technology Stack

**Legacy System**: Originally built on Core PHP (deprecated version)
**Current Implementation**: Complete redevelopment using modern technologies


## 🛠️ Technology Stack

| Technology | Purpose | Version |
|------------|---------|---------|
| **Laravel** | Backend Framework | 11.x |
| **PHP** | Server-side Language | 8.2+ |
| **MySQL** | Primary Database | 8.0+ |
| **RESTful APIs** | API Architecture | - |
| **Apache** | Web Server | Latest |
| **MySQL** | Database | 8.0+ |

## 🏗️ System Architecture

### Dual API Server Structure

#### 1. Admin API Server
- **Purpose**: Developer and administrative access
- **Access Level**: Internal development team
- **Functionality**: System configuration, user management, analytics

#### 2. Client API Server
- **Purpose**: End-user access across multiple user types
- **Architecture**: Frontend servers communicate with backend API via REST requests
- **Response Format**: JSON


### Architecture Diagram
```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │   Frontend      │    │   Frontend      │
│   (Corporate)   │    │   (Employee)    │    │   (Doctor/Lab)  │
└─────────┬───────┘    └─────────┬───────┘    └─────────┬───────┘
          │                      │                      │
          │              REST API Calls                 │
          │                      │                      │
          └──────────────────────┼──────────────────────┘
                                 │
                    ┌────────────┴──────────────┐
                    │     API Gateway           │
                    │   (Load Balancer)         │
                    └─────────────┬─────────────┘
                                  │
                    ┌─────────────┴─────────────┐
                    │     Backend Services      │
                    │                           │
                    │  ┌─────────────────────┐  │
                    │  │   Admin API Server  │  │
                    │  │   (Laravel)         │  │
                    │  └─────────────────────┘  │
                    │                           │
                    │  ┌─────────────────────┐  │
                    │  │   Client API Server │  │
                    │  │   (Laravel)         │  │
                    │  └─────────────────────┘  │
                    └─────────────┬─────────────┘
                                  │
                         ┌────────┴─────────┐
                         │   MySQL Database │
                         │   (Primary)      │
                         └──────────────────┘
```

### User Access Levels

| User Type | Access Level | Functionality |
|-----------|--------------|---------------|
| **Corporate Admin** | Organization-wide | Company health center management, reporting |
| **Employee** | Personal health data | View medical records, book appointments |
| **Doctor** | Medical consultations | Patient records, prescriptions, diagnostics |
| **Lab User** | Laboratory operations | Test results, lab reports, sample tracking |

## 🚀 Features

### Core Functionality
- **Employee Health Tracking**: Comprehensive health record management
- **Medical Consultations**: Digital consultation platform
- **Prescription Management**: Electronic prescription system
- **Diagnostic Services**: Lab test integration and results tracking
- **Health Analytics**: Corporate health insights and reporting
- **Appointment Scheduling**: Integrated booking system
- **Medical History**: Complete employee medical timeline

### Enterprise Features
- Multi-company support
- Role-based access control
- Secure data encryption
- Audit trails and compliance
- Scalable architecture for large organizations

## 👥 Development Team

| Role | Team Member | Responsibilities |
|------|-------------|------------------|
| **Project Manager** | Palanivel | Project coordination, stakeholder management |
| **Technical Lead** | M S Praveen Kumar | Code review, system architecture, Git administration |
| **Developer** | Bhavani | Feature development, implementation |

## 🛠️ Technical Requirements

### System Prerequisites
- PHP >= 8.1
- Laravel >= 10
- Composer
- MySQL >= 7.0
- Node.js & npm (for frontend assets)
- Laravel Passport for API authentication

## 📱 API Documentation

- Yet To Add with Laravel Swagger (TODO: Priority HIGH)

## 🔒 Security Features

- Laravel Passport OAuth2 Authentication
- Role-based permissions
- Data encryption at rest
- HIPAA compliance considerations
- Secure API endpoints
- Input validation and sanitization

## 🏢 Client Companies

This platform is designed to serve major corporations including:
- **L&T** (Larsen & Toubro)
- **Hyundai Motors**
- **Apollo Group**
- **Nissan**
- **KIA**
## 📊 Database Schema

The system uses MySQL with optimized schemas for:
- Employee management
- Medical records
- Appointment scheduling
- Prescription tracking
- Laboratory results
- User authentication and roles

## 🔄 Development Workflow

### Git Workflow
- **Main Branch**: Production-ready code
- **Development Branch**: Integration branch for features
- **Code Review**: All code reviewed by Technical Lead before merge

### Code Standards
- PSR-12 coding standards
- Laravel best practices
- Comprehensive unit testing
- API documentation with Swagger/OpenAPI

## 🛠️ Development Environment

### VS Code Extensions
Our development team uses the following VS Code extensions for enhanced productivity:

#### Theme & UI
- **Monokai Pro** - Professional color theme for better code readability

#### Laravel & PHP Development
- **Laravel Blade Formatter** - Auto-formatting for Blade templates
- **Laravel Extra Intellisense** - Enhanced Laravel project intelligence
- **IntelliPHP** - AI-powered PHP autocompletion and analysis
- **PHP Debug** - Debugging support with Xdebug integration
- **PHP Intelephense** - Advanced PHP language server
- **PHP Server** - Built-in PHP development server

#### Code Quality & Formatting
- **Prettier** - Code formatter for consistent styling
- **HTML CSS Support** - Enhanced HTML/CSS intellisense
- **HTML Format** - Auto-formatting for HTML documents

#### Git & Version Control
- **GitLens** - Supercharged Git capabilities
- **GitHub Copilot** - AI pair programming assistant
- **GitHub Copilot Chat** - AI-powered coding conversations

#### API Development & Testing
- **Postman** - API development and testing
- **IntelliCode** - AI-assisted development suggestions
- **IntelliCode API Usage Examples** - Real-world code examples

#### Productivity Tools
- **Live Server** - Local development server with live reload
- **Todo Tree** - Highlight and organize TODO comments
- **Bookmarks** - Navigate and mark important code sections
- **WakaTime** - Automatic time tracking and coding metrics
- **Stats Bar** - System statistics in status bar

#### Database & Composer
- **Composer** - PHP dependency management integration

## 📞 Contact

### Project Team

**Project Manager**  
📧 **Palanivel**  
- Role: Project Management & Client Relations
- Email: palanivel.c@myhealthvalet.in

**Technical Lead**  
👨‍💻 **M S Praveen Kumar**  
- Role: System Architecture, Code Review, Git Administration
- Email: mspraveenkumar77@gmail.com, praveen@inaiyathalam.in
- GitHub: [Praveen](https://github.com/Praveenms13)

**Developer**  
👩‍💻 **Bhavani**  
- Role: Feature Development & Implementation
- Email: bhavawebcoder@gmail.com
- GitHub: [Bhavani](https://github.com/bhavawebcoder)


## 📄 License

**Frontend License**  
This project includes a commercially licensed UI template obtained from Envato (ThemeForest).  
- PHP and integrated HTML are licensed under the GNU General Public License (GPL).  
- All other assets (CSS, JS, images, design) are governed by the Envato commercial license.  
See the full [Frontend LICENSE](./ui-templates/Licensing/License.txt) for more details.

**Backend License**  
The backend code is proprietary software developed by **Hygeiaes**.  
It is intended solely for internal use in corporate healthcare systems.  

See the full [Backend LICENSE](./Project/Api-MHV/License.txt) for more details.


**Note**: This is an enterprise-grade healthcare platform handling sensitive medical data. Ensure compliance with local healthcare regulations (HIPAA, GDPR, etc.) before deployment.

## 🏗️ Project Status

🔄 **Active Development** - Migrating from legacy Core PHP to modern Laravel architecture

**Current Phase**: Core API development and testing
**Next Phase**: Frontend integration and user acceptance testing

## 🔮 Future Development Ideas

### Deployment Options
- **Cloud Platforms**: AWS, Azure, Google Cloud Platform integration
- **Containerization**: Docker and Kubernetes deployment
- **On-Premise**: Enterprise server deployment options
- **Hybrid Solutions**: Cloud-on-premise hybrid architectures

### Feature Enhancements
- Mobile application development
- Telemedicine integration
- AI-powered health analytics
- Wearable device integration
- Advanced reporting dashboards
**Enterprise Healthcare Platform** - Transforming Corporate Healthcare Management

[![Made with ❤️ by Hygeiaes Team](https://img.shields.io/badge/Made%20with%20❤️%20by-Hygeiaes%20Team-red?style=for-the-badge)](https://www.myhealthvalet.in/health-productivity/)