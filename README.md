# Red2Green
> Securing DVWA Through a DevSecOps Pipeline

A university DevSecOps project that demonstrates how to identify, exploit, fix, and automatically scan for common web application vulnerabilities using a CI/CD security pipeline.

---

## Project Overview

This project takes a deliberately vulnerable web application (DVWA — Damn Vulnerable Web Application) and transforms it from an insecure baseline to a secure state by:

1. **Exploiting** four real-world vulnerabilities on the unmodified application
2. **Fixing** each vulnerability with secure coding practices on individual fix branches
3. **Automating** security validation through a GitHub Actions DevSecOps pipeline

---

## System Architecture

The application runs as two Docker containers on a private bridge network:

| Container | Role | Exposed Port |
|---|---|---|
| `dvwa-web` | Apache 2.4 + PHP 8 (Application) | `4280` (host) -> `80` (internal) |
| `dvwa-db` | MariaDB 10 (Database) | Internal only (port `3306`) |

**Trust Zones:** Public (Browser) -> Application Zone (`dvwa-web`) -> Data Zone (`dvwa-db`)

---

## Running the App Locally

### Prerequisites
- Docker and Docker Compose installed

### Steps
```bash
# 1. Clone the repository
git clone https://github.com/thenuladew/Red2Green.git
cd Red2Green

# 2. Copy the environment file
cp .env.example .env

# 3. Start the containers
docker compose up -d

# 4. Visit the app
# Open http://localhost:4280/setup.php in your browser
```

After database initialization, log in to `http://localhost:4280` using `admin` / `password`. Set the Security Level to **Low** via the DVWA Security menu.

---

## Vulnerabilities Remediated

Each vulnerability was resolved on a dedicated branch and verified by the CI/CD pipeline via a Pull Request.

| # | Vulnerability | Branch | Fix Applied |
|---|---|---|---|
| 1 | Broken Access Control (BAC) | Replaced client-side `$_COOKIE` check with server-side `$_SESSION` validation |
| 2 | SQL Injection (SQLi) | Replaced string concatenation with parameterised queries (`mysqli_prepare`) |
| 3 | Stored XSS | Sanitized output using `htmlspecialchars()` to neutralize script execution |
| 4 | Brute Force | Implemented session-based login attempt tracking with lockout after 3 failures |

---

## CI/CD Security Pipeline

The GitHub Actions pipeline (`.github/workflows/devsecops.yml`) runs automatically on every Pull Request to `main` and enforces **four security gates**:

| Gate | Tool | Purpose | Enforcement |
|---|---|---|---|
| Gate 1 | **Gitleaks** | Detects committed credentials and secrets | Blocking |
| Gate 2 | **Semgrep** | Static Application Security Testing (SAST) for PHP | Blocking |
| Gate 3 | **Trivy (FS)** | Software Composition Analysis (SCA) for dependencies | Informational |
| Gate 4 | **Trivy (Image)** | Container image vulnerability scanning | Blocking |

---

## Team Members and Roles

| Student ID | Name | Role |
|---|---|---|
| IT24102533 | Dewanmith K.D.T | Security Engineer |
| IT24102879 | Rajapaksha R.K.T | AppSec Analyst |
| IT24102931 | Senanayake P.A | CI/CD Architect |
| IT24102685 | Weerasekara D.P.P | Threat Modeler |

---

## License

Based on [DVWA](https://github.com/digininja/DVWA) by digininja — licensed under [GPL-3.0](COPYING.txt).

