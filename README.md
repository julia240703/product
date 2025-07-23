# Project Name Documentation

Welcome to the documentation for Project Name. This document provides a comprehensive guide to understanding and using the features of the project.

## Table of Contents
- [Project Name Documentation](#project-name-documentation)
  - [Table of Contents](#table-of-contents)
  - [Introduction](#introduction)
  - [What is Project Name?](#what-is-project-name)
  - [Who Should Read This Documentation?](#who-should-read-this-documentation)
  - [How to Use This Guide](#how-to-use-this-guide)
  - [Getting Started](#getting-started)
  - [User Manual](#user-manual)
    - [User Manual: Admin](#user-manual-admin)
    - [User Manual: Sub-admin](#user-manual-sub-admin)
    - [User Manual: User](#user-manual-user)
  - [Troubleshooting](#troubleshooting)
    - [Issue: HTTP ERROR 500](#issue-http-error-500)
  - [Version History](#version-history)
    - [Version 1.0.0 (dd-mm-yyy)](#version-100-dd-mm-yyy)

## Introduction

This documentation is designed to guide you through the installation process and help you get started with using the software. This guide will provide you with the necessary information to set up and use the application effectively.

## What is Project Name?

Project Name is a web-based application built using Laravel 9 with PHP 8.1 designed to assist HR teams and recruiters find potential employees. With this system, you can streamline and optimize the prospective employee selection process by looking at the prospective employee's psychological test results.


## Who Should Read This Documentation?

This documentation is intended for various users, including:

- **Recruiters**: If you're responsible for evaluating and selecting employee candidates, this guide will walk you through the process and provide insights into using the system effectively.
- **Developers and Technical Users**: If you're a developer or have technical responsibilities, you'll find instructions on how to set up and configure the system for optimal performance.
- **Administrators**: For those managing the deployment and configuration of the system, this guide will help you make informed decisions.

## How to Use This Guide

This guide is organized into sections that cover various aspects of using Project Name:

- **Installation**: Step-by-step instructions for installing the software on your system.
- **Getting Started**: An overview of the software's main features and how to access them.
- **Usage Examples**: Practical examples that demonstrate how to use different features.
- **Troubleshooting**: Solutions for common issues you might encounter.
- **Appendix**: Additional resources and reference information.
- **Version History**: A log of changes made to the software and documentation across different versions.

Feel free to navigate through the sections based on your needs. If you have any questions or need assistance, don't hesitate to reach out to our support team.

Let's get started with the installation process!


## Getting Started

To start using Project Name, follow these steps:

1. Install Composer dependencies:
   ```sh
   composer install
2. Create a copy of the .env.example file and name it .env:
   ```sh
   cp .env.example .env
3. Generate an application key: 
   ```sh
   php artisan key:generate
4. Run database migrations:
   ```sh
   php artisan migrate
5. Install Node.js dependencies:
   ```sh
   npm install
For more detailed instructions, refer to the [Installation Guide](/docs/installation/installation.md).

## User Manual

### [User Manual: Admin](/docs/manual/admin/README.md) 

### [User Manual: Sub-admin](/docs/manual/sub-admin/README.md)

### [User Manual: User](/docs/manual/user/README.md)

## Troubleshooting

### Issue: HTTP ERROR 500
If the application crashes on startup and you encounter an HTTP ERROR 500, you can try the following steps to resolve the issue:

1. **Check Server .env File**: Verify that the `.env` file on your server contains accurate configuration settings. Ensure that the necessary database credentials, cache configurations, and other environment-specific settings are correctly defined.

2. **Verify APP_KEY**: The `APP_KEY` is a critical value used for encryption and security purposes. If this key is not correctly generated or configured, it can lead to unexpected errors. To ensure the `APP_KEY` is correct, follow these steps:

   - Open a terminal window.
   - Navigate to the root directory of your project.
   - Run the following command to generate a new `APP_KEY`:
     ```sh
     php artisan key:generate
     ```

3. **Check Log Files**: Sometimes, the error details are recorded in the application's log files. Check the log files (usually located in the `storage/logs` directory) for more specific error messages that can help diagnose the issue.

4. **Server Environment**: Ensure that your server environment meets the software's requirements. Check for compatibility issues with PHP version, required extensions, and other dependencies.

5. **Clear Cache**: Stale or corrupted cache files can lead to unexpected errors. Try clearing the cache by running the following command:
   ```sh
   php artisan cache:clear
## Version History

### Version 1.0.0 (dd-mm-yyy)
- Initial release of Project Name.
