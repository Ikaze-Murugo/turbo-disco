# Contributing to Murugo

Thank you for your interest in contributing to Murugo! This document provides guidelines and information for contributors.

## 🚀 Getting Started

### Prerequisites
- PHP 8.4.10 or higher
- Composer 2.x
- Node.js 16+ and npm
- Git

### Development Setup
1. Fork the repository
2. Clone your fork: `git clone https://github.com/YOUR_USERNAME/miniature-den.git`
3. Install dependencies: `composer install && npm install`
4. Copy environment file: `cp .env.example .env`
5. Generate application key: `php artisan key:generate`
6. Set up database: `touch database/database.sqlite && php artisan migrate`
7. Seed the database: `php artisan db:seed`
8. Build assets: `npm run build`

## 📝 Code Standards

### PHP/Laravel
- Follow PSR-12 coding standards
- Use meaningful variable and function names
- Write comprehensive docblocks for classes and methods
- Keep methods small and focused
- Use type hints where possible

### JavaScript/CSS
- Use ES6+ features
- Follow consistent indentation (2 spaces)
- Use meaningful variable names
- Comment complex logic

### Database
- Use descriptive table and column names
- Add proper indexes for performance
- Include foreign key constraints
- Write migration rollbacks

## 🧪 Testing

### Writing Tests
- Write tests for all new features
- Aim for high test coverage
- Use descriptive test names
- Test both success and failure scenarios

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=PropertyTest

# Run with coverage
php artisan test --coverage
```

## 📋 Pull Request Process

1. **Create a feature branch** from `main`
2. **Make your changes** following the code standards
3. **Write tests** for your changes
4. **Update documentation** if needed
5. **Commit your changes** with descriptive messages
6. **Push to your fork** and create a pull request

### Commit Message Format
```
type(scope): description

- Use imperative mood ("add feature" not "added feature")
- Limit first line to 72 characters
- Reference issues and pull requests liberally

Examples:
feat(auth): add email verification
fix(maps): resolve marker positioning issue
docs(readme): update installation instructions
```

## 🐛 Bug Reports

When reporting bugs, please include:
- Clear description of the issue
- Steps to reproduce
- Expected vs actual behavior
- Screenshots if applicable
- Environment details (OS, PHP version, etc.)

## ✨ Feature Requests

For feature requests, please:
- Describe the feature clearly
- Explain the use case
- Consider implementation complexity
- Check for existing similar requests

## 📚 Documentation

- Update README.md for significant changes
- Add inline code comments for complex logic
- Update API documentation for new endpoints
- Include examples in code comments

## 🔒 Security

- Never commit sensitive information (API keys, passwords)
- Report security vulnerabilities privately
- Follow secure coding practices
- Validate all user inputs

## 📞 Getting Help

- Check existing issues and discussions
- Join our community discussions
- Contact maintainers for urgent issues

## 🎯 Areas for Contribution

- **Frontend**: UI/UX improvements, responsive design
- **Backend**: API enhancements, performance optimization
- **Maps**: Mapbox integration, location services
- **Testing**: Test coverage, automated testing
- **Documentation**: Code comments, user guides
- **Security**: Vulnerability fixes, security audits

## 📄 License

By contributing, you agree that your contributions will be licensed under the MIT License.

Thank you for contributing to Murugo! 🏠
