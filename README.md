# Tapomix / Starter-Kit # PHP

## Install

```bash
curl -L -o ~/.local/bin/start-project-php https://raw.githubusercontent.com/tapomix/php-starter-kit/main/bin/start-project-php.sh \
&& chmod +x ~/.local/bin/start-project-php
```

## Update

Just repeat the installation process (*curl + chmod*).

## Usage

```bash
start-project-php
```

or to customize the remote source

```bash
STARTER_GIT="REPO_URL" start-project-php
```

- choose a project name + a branch ( = framework )

and then we can build the containers and install a framework with

```bash
castor build
castor laravel:install # or symfony:install
```

---

## Branches

> **Only branches named with dev-XXX are clonable with the script ./bin/start-project-php.sh**

### branch:main

Main branch with info files + script

### branch:dev

Common base branch for all frameworks

### branch:dev-symfony (*clonable*)

Specific branch for Symfony framework

### branch:dev-laravel (*clonable*)

Specific branch for Laravel framework
