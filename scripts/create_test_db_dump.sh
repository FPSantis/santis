#!/bin/bash
# Move to the 'code' directory (where .ddev is)
cd "$(dirname "$0")/../code" || exit 1

echo "Syncing uploads from Public to Master (config)..."
ddev exec rsync -a --delete public_html/uploads/ config/uploads/

echo "Creating database dump..."
ddev export-db --file=database/default_state.sql.gz
echo "Dump saved to database/default_state.sql.gz"
