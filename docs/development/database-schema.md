# Database Schema Documentation

## Overview

This document provides detailed information about the database structure of the Carpentry Project Management System. The database is designed to efficiently manage projects, clients, materials, and user roles within a carpentry business.

## Tables

### 1. Rolet (Roles)
- `rol_id` (SERIAL, PRIMARY KEY)
- `emri_rolit` (VARCHAR(50), UNIQUE, NOT NULL) - Values: 'administrator', 'menaxher', 'mjeshtër', 'montues'
- `pershkrimi` (TEXT)
- `data_krijimit` (TIMESTAMP WITH TIME ZONE)

### 2. Perdoruesit (Users)
- `perdorues_id` (SERIAL, PRIMARY KEY)
- `rol_id` (INTEGER, REFERENCES Rolet ON DELETE RESTRICT)
- `emri` (VARCHAR(100), NOT NULL)
- `mbiemri` (VARCHAR(100), NOT NULL)
- `email` (VARCHAR(255), UNIQUE, NOT NULL)
- `fjalekalimi_hash` (VARCHAR(255), NOT NULL)
- `telefon` (VARCHAR(20))
- `adresa` (TEXT)
- `aktiv` (BOOLEAN, DEFAULT TRUE)
- `data_krijimit` (TIMESTAMP)
- `data_perditesimit` (TIMESTAMP)

### 3. Klientet (Clients)
- `klient_id` (SERIAL, PRIMARY KEY)
- `person_kontakti` (VARCHAR(255), NOT NULL)
- `telefon_kontakt` (VARCHAR(20))
- `email_kontakt` (VARCHAR(255))
- `adresa_faktura` (TEXT)
- `qyteti` (VARCHAR(100))
- `kodi_postal` (VARCHAR(20))
- `shteti` (VARCHAR(100))
- `shenime` (TEXT)
- `data_krijimit` (TIMESTAMP)
- `data_perditesimit` (TIMESTAMP)

### 4. Statuset_Projektit (Project Statuses)
- `status_id` (SERIAL, PRIMARY KEY)
- `emri_statusit` (VARCHAR(50), UNIQUE, NOT NULL)
- `pershkrimi` (TEXT)
- `renditja` (INTEGER, UNIQUE, NOT NULL)

### 5. Projektet (Projects)
- `projekt_id` (SERIAL, PRIMARY KEY)
- `klient_id` (INTEGER, REFERENCES Klientet ON DELETE RESTRICT)
- `emri_projektit` (VARCHAR(255), NOT NULL)
- `pershkrimi` (TEXT)
- `data_fillimit_parashikuar` (DATE)
- `data_perfundimit_parashikuar` (DATE)
- `data_perfundimit_real` (DATE)
- `status_id` (INTEGER, REFERENCES Statuset_Projektit ON DELETE RESTRICT)
- `mjeshtri_caktuar_id` (INTEGER, REFERENCES Perdoruesit ON DELETE SET NULL)
- `montuesi_caktuar_id` (INTEGER, REFERENCES Perdoruesit ON DELETE SET NULL)
- `shenime_projekt` (TEXT)
- `data_krijimit` (TIMESTAMP)
- `data_perditesimit` (TIMESTAMP)

## Relationships

1. **Perdoruesit** belongs to **Rolet**
   - One-to-Many: One role can have many users
   - Foreign Key: `Perdoruesit.rol_id` references `Rolet.rol_id`

2. **Projektet** belongs to **Klientet**
   - One-to-Many: One client can have many projects
   - Foreign Key: `Projektet.klient_id` references `Klientet.klient_id`

3. **Projektet** has many **Dokumentet_Projekti**
   - One-to-Many: One project can have many documents
   - Foreign Key: `Dokumentet_Projekti.projekt_id` references `Projektet.projekt_id` (CASCADE on delete)

## Important Constraints

1. **Role-based Access Control**
   - Only predefined roles are allowed: 'administrator', 'menaxher', 'mjeshtër', 'montues'
   - Users must have a valid role to access the system

2. **Project Management**
   - A project must belong to a client
   - A project must have a status
   - Projects can have assigned craftsmen (mjeshtër) and assemblers (montues)

3. **Data Integrity**
   - Client deletion is restricted if they have associated projects
   - Project materials must have a quantity greater than 0
   - All timestamps are automatically managed by the database

## Indexes

Primary keys are automatically indexed. Additional indexes exist on:
- `Perdoruesit.email` (UNIQUE)
- `Klientet.email_kontakt`
- `Projektet.status_id`
- `Projektet.klient_id`
- `Dokumentet_Projekti.projekt_id`

## Sample Queries

### Get All Active Projects with Client and Status
```sql
SELECT p.emri_projektit, 
       k.person_kontakt AS klienti,
       s.emri_statusit AS statusi,
       p.data_fillimit_parashikuar,
       p.data_perfundimit_parashikuar
FROM projektet p
JOIN klientet k ON p.klient_id = k.klient_id
JOIN statuset_projektit s ON p.status_id = s.status_id
WHERE p.data_perfundimit_real IS NULL
ORDER BY p.data_krijimit DESC;
```

### Get Project Materials with Quantities
```sql
SELECT m.emri_materialit,
       m.njesia_matese,
       pm.sasia_perdorur,
       (pm.sasia_perdorur * m.cmimi_njesi) AS kosto_totale
FROM materialet m
JOIN projekt_materiale pm ON m.material_id = pm.material_id
WHERE pm.projekt_id = :projekt_id;
```

### Get User's Assigned Projects
```sql
SELECT p.emri_projektit, 
       s.emri_statusit AS statusi,
       p.data_fillimit_parashikuar,
       p.data_perfundimit_parashikuar
FROM projektet p
JOIN statuset_projektit s ON p.status_id = s.status_id
WHERE p.mjeshtri_caktuar_id = :perdorues_id 
   OR p.montuesi_caktuar_id = :perdorues_id
ORDER BY p.data_perfundimit_parashikuar ASC;
```

## Data Retention and Archiving

1. **Active Data**
   - All active projects and their related data are stored in the main tables
   - Soft deletes are implemented where appropriate

2. **Audit Trail**
   - All significant actions are logged in the `Ditar_Veprimet` table
   - Includes user ID, action type, timestamp, and IP address

3. **Backup Strategy**
   - Daily database backups
   - Weekly full system backups including uploaded files
   - 30-day retention for daily backups
   - 1-year retention for monthly backups
