#!/bin/bash
# Move to the 'code' directory (where .ddev is)
cd "$(dirname "$0")/../code" || exit 1

echo "Resetting environment to 'Fresh Install' state..."

# 1. Empty Database
echo "Emptying database..."
ddev mysql -e "DROP DATABASE IF EXISTS db; CREATE DATABASE db;"

# 2. Remove Lock File
echo "Removing lock file..."
rm -f config/installed.lock

# 3. Clean Public Uploads
echo "Cleaning public uploads..."
# Note: Cleaning recursive while inside 'code' directory relative path
rm -rf public_html/uploads/files/*
rm -rf public_html/uploads/albums/*

echo "Done. Access the site to run the Installer."
