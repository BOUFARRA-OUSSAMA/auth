<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# API Documentation

## Authentication

All protected endpoints require a valid JWT token in the Authorization header:
```
Authorization: Bearer your_token_here
```

### Public Endpoints
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET    | /api/test | Health check endpoint to verify if the API is working |
| POST   | /api/auth/register | Register a new user with the system |
| POST   | /api/auth/login | Authenticate a user and generate a JWT token |

### Protected Authentication Endpoints
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET    | /api/auth/me | Get the currently authenticated user's information |
| POST   | /api/auth/refresh | Refresh the user's JWT token |
| POST   | /api/auth/logout | Invalidate the current JWT token and log out the user |

## User Management
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET    | /api/users | List all users (pagination supported) |
| POST   | /api/users | Create a new user |
| GET    | /api/users/{user} | Get detailed information about a specific user |
| PUT/PATCH | /api/users/{user} | Update an existing user |
| DELETE | /api/users/{user} | Delete a user |
| GET    | /api/users/{user}/roles | Get all roles assigned to a specific user |
| POST   | /api/users/{user}/roles | Assign roles to a specific user |

## Role Management
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET    | /api/roles | List all roles (pagination supported) |
| POST   | /api/roles | Create a new role |
| GET    | /api/roles/{role} | Get detailed information about a specific role |
| PUT/PATCH | /api/roles/{role} | Update an existing role |
| DELETE | /api/roles/{role} | Delete a role |
| GET    | /api/roles/{role}/users | Get all users assigned to a specific role |
| GET    | /api/roles/{role}/permissions | Get all permissions assigned to a specific role |
| POST   | /api/roles/{role}/permissions | Assign permissions to a specific role |

## Permission Management
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET    | /api/permissions | List all permissions (pagination supported) |
| POST   | /api/permissions | Create a new permission |
| GET    | /api/permissions/{permission} | Get detailed information about a specific permission |
| PUT/PATCH | /api/permissions/{permission} | Update an existing permission |
| DELETE | /api/permissions/{permission} | Delete a permission |
| GET    | /api/permissions/groups | Get all permission groups |
| GET    | /api/permissions/group/{group} | Get all permissions in a specific group |
| GET    | /api/permissions/role/{role} | Get all permissions for a specific role |

## Entity-Attribute-Value (EAV) System

### Entity Types
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET    | /api/entity-types | List all entity types |
| POST   | /api/entity-types | Create a new entity type |
| GET    | /api/entity-types/{entityType} | Get detailed information about a specific entity type |
| PUT/PATCH | /api/entity-types/{entityType} | Update an existing entity type |
| DELETE | /api/entity-types/{entityType} | Delete an entity type |
| GET    | /api/entity-types/{entityType}/attributes | Get all attributes for a specific entity type |

### Attributes
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET    | /api/attributes | List all attributes |
| POST   | /api/attributes | Create a new attribute |
| GET    | /api/attributes/{attribute} | Get detailed information about a specific attribute |
| PUT/PATCH | /api/attributes/{attribute} | Update an existing attribute |
| DELETE | /api/attributes/{attribute} | Delete an attribute |
| GET    | /api/attributes/{attribute}/values | Get all values for a specific attribute |

### Attribute Values
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET    | /api/attribute-values | List all attribute values |
| POST   | /api/attribute-values | Create a new attribute value |
| GET    | /api/attribute-values/{attributeValue} | Get detailed information about a specific attribute value |
| PUT/PATCH | /api/attribute-values/{attributeValue} | Update an existing attribute value |
| DELETE | /api/attribute-values/{attributeValue} | Delete an attribute value |
| POST   | /api/attribute-values/batch | Batch update multiple attribute values at once |

## Debug
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET    | /api/token/debug | Debug JWT token information, validates a token and shows payload and expiration |

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
