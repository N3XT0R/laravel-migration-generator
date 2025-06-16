#!/bin/bash

# SQL Server starten
/opt/mssql/bin/sqlservr &

# Warten, bis SQL Server erreichbar ist (max. 30 Sekunden)
for i in {30..0}; do
  /opt/mssql-tools/bin/sqlcmd -S tcp:localhost,1433 -U SA -P "$SA_PASSWORD" -Q "SELECT 1" > /dev/null 2>&1
  if [ $? -eq 0 ]; then
    echo "SQL Server is up"
    break
  fi
  echo "Waiting for SQL Server to start..."
  sleep 1
done

if [ $i -eq 0 ]; then
  echo "SQL Server did not start in time."
  exit 1
fi

# SQL-Befehle ausführen
/opt/mssql-tools/bin/sqlcmd -S tcp:localhost,1433 -U SA -P "$SA_PASSWORD" -Q "
CREATE DATABASE testing;
GO
USE testing;
GO
CREATE LOGIN testuser WITH PASSWORD = 'TestPassw0rd!';
GO
CREATE USER testuser FOR LOGIN testuser;
GO
ALTER ROLE db_owner ADD MEMBER testuser;
GO
"

# SQL Server Prozess im Vordergrund halten, damit Container nicht endet
wait
