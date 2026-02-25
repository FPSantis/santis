#!/bin/bash
# Get the directory of this script
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# Navigate to code directory
cd "$DIR/../code"

echo "============================================="
echo "🔄 Updating Migration Data (JSON)..."
echo "============================================="

# Execute the PHP script inside DDEV
ddev exec php database/update_migrations.php

echo "============================================="
echo "✅ Data Updated!"
echo "============================================="
