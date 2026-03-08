#!/bin/bash
set -euo pipefail

if [ -x /opt/mssql-tools18/bin/sqlcmd ]; then
  SQLCMD_BIN=/opt/mssql-tools18/bin/sqlcmd
elif [ -x /opt/mssql-tools/bin/sqlcmd ]; then
  SQLCMD_BIN=/opt/mssql-tools/bin/sqlcmd
else
  echo "sqlcmd binary not found in mssql-tools image"
  exit 1
fi

run_sql_file() {
  local db="$1"
  local file="$2"

  echo "[mssql-init] applying ${file} (db=${db})"
  "$SQLCMD_BIN" -S mssql -U sa -P "$MSSQL_SA_PASSWORD" -d "$db" -i "$file"
}

for i in {1..60}; do
  if "$SQLCMD_BIN" -S mssql -U sa -P "$MSSQL_SA_PASSWORD" -Q "SELECT 1" > /dev/null 2>&1; then
    run_sql_file master /init/01-init.sql

    for file in /init/schema/*.sql; do
      [ -f "$file" ] || continue
      run_sql_file lifelink "$file"
    done

    for file in /init/seed/*.sql; do
      [ -f "$file" ] || continue
      run_sql_file lifelink "$file"
    done

    echo "[mssql-init] initialization completed"
    exit 0
  fi

  sleep 2
done

echo "SQL Server was not ready after 120 seconds"
exit 1
