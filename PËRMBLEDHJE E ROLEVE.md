# 📋 MATRICA E QASJEVE SIPAS ROLEVE - SISTEMI I MARANGOZËRISË

## 👥 PËRMBLEDHJE E ROLEVE:
- **🔴 Administrator** (rol_id=1): 2 përdorues aktiv
- **🔵 Menaxher** (rol_id=2): 1 përdorues aktiv  
- **🟡 Mjeshtër** (rol_id=3): 2 përdorues aktiv
- **🟠 Montues** (rol_id=4): 1 përdorues aktiv
- **🟢 Disajnere** (rol_id=5): 0 përdorues (i sapo krijuar)

---

## 🔴 **ADMINISTRATOR** (rol_id = 1)
**Qasja më e lartë në sistem - Menaxhon gjithçka**

### ✅ QASJE TË PLOTA:
- **STAFI** - Menaxhon të gjithë stafin (create/edit/delete)
- **PROJEKTET** - Shikon/krijon/modifikon të gjitha projektet
- **KLIENTËT** - Regjistron/modifikon klientë
- **MATERIALET** - Shtон/fshin/modifikon materiale
- **STATUSET** - Menaxhon statuset e projekteve
- **FAZAT** - Qasje e plotë në të gjitha fazat
- **RAPORTET** - Eksporton të gjitha raportet
- **DOKUMENTET** - Ngarkon/fshin dokumentet
- **DIMENSIONET** - Menaxhon dimensionet
- **SISTEMI** - Qasje administrative e plotë

### 🎯 PËRGJEGJËSITË:
- Menaxhimi i stafit dhe roleve
- Konfigurimi i sistemit
- Mbikëqyrja e të gjitha operacioneve
- Menaxhimi i sigurisë së sistemit

---

## 🔵 **MENAXHER** (rol_id = 2)
**Menaxhon operacionet e përditshme të biznesit**

### ✅ QASJE TË PLOTA:
- **PROJEKTET** - Shikon/krijon/modifikon të gjitha projektet
- **KLIENTËT** - Regjistron/modifikon klientë
- **MATERIALET** - Shtон/fshin/modifikon materiale
- **RAPORTET** - Eksporton të gjitha raportet
- **DOKUMENTET** - Ngarkon/fshin dokumentet
- **DIMENSIONET** - Menaxhon dimensionet

### 👀 QASJE TË KUFIZUARA:
- **STATUSET** - Shikon statuset (nuk i ndryshon)
- **FAZAT** - Shikon fazat (nuk i modifikon)

### ❌ PA QASJE:
- **STAFI** - Nuk menaxhon stafin

### 🎯 PËRGJEGJËSITË:
- Menaxhimi i projekteve
- Koordinimi me klientët
- Menaxhimi i materialeve dhe stokut
- Raportimi dhe analiza

---

## 🟢 **DISAJNERE** (rol_id = 5)
**Specializohet në dizajn dhe planifikim**

### ✅ QASJE TË PLOTA:
- **PROJEKTET** - Krijon/modifikon projekte
- **KLIENTËT** - Regjistron/modifikon klientë
- **DOKUMENTET/SKICAT** - Ngarkon skicat dhe planet
- **DIMENSIONET** - Shtон dimensionet e projekteve
- **RAPORTET** - Eksporton raportet e projekteve

### 👀 QASJE TË KUFIZUARA:
- **MATERIALET** - Shikon listën (nuk shtон/fshin)
- **STATUSET** - Shikon statuset (nuk i ndryshon)
- **FAZAT** - Shikon fazat e projekteve

### ❌ PA QASJE:
- **STAFI** - Nuk menaxhon stafin
- **SISTEMI** - Pa qasje administrative

### 🎯 PËRGJEGJËSITË:
- Dizajnimi i projekteve
- Ngarkimi i skicave dhe planeve
- Përcaktimi i dimensioneve
- Komunikimi me klientët për kërkesat

---

## 🟡 **MJESHTËR** (rol_id = 3)
**Punon në projektet e caktuara**

### 👀 QASJE TË KUFIZUARA (VETËM SHIKON):
- **PROJEKTET** - Shikon vetëm projektet ku është caktuar si mjeshtër
- **KLIENTËT** - Shikon listën e klientëve (nuk mund t'i modifikojë)
- **MATERIALET** - Shikon listën e materialeve (nuk mund t'i modifikojë)
- **STATUSET** - Shikon statuset (nuk mund t'i ndryshojë)
- **FAZAT** - Shikon fazat e projekteve të tij
- **DOKUMENTET** - Shikon dokumentet e projekteve të tij
- **DIMENSIONET** - Shikon dimensionet e projekteve të tij (nuk mund t'i modifikojë)

### ❌ PA QASJE:
- **STAFI** - Nuk menaxhon stafin
- **RAPORTET** - Nuk eksporton raporte
- Krijimi i projekteve të reja
- Regjistrimi i klientëve të rinj
- Shtimi i materialeve

### 🎯 PËRGJEGJËSITË:
- Punimi në projektet e caktuara
- Implementimi i dizajnit
- Raportimi i progresit
- Komunikimi me menaxherin

---

## 🟠 **MONTUES** (rol_id = 4)
**Specializohet në montazh dhe instalim**

### 👀 QASJE TË KUFIZUARA (VETËM SHIKON):
- **PROJEKTET** - Shikon vetëm projektet ku është caktuar si montues
- **KLIENTËT** - Shikon listën e klientëve (nuk mund t'i modifikojë)
- **MATERIALET** - Shikon listën e materialeve (nuk mund t'i modifikojë)
- **STATUSET** - Shikon statuset (nuk mund t'i ndryshojë)
- **FAZAT** - Shikon fazat e projekteve të tij
- **DOKUMENTET** - Shikon dokumentet e projekteve të tij
- **DIMENSIONET** - Shikon dimensionet e projekteve të tij (nuk mund t'i modifikojë)

### ❌ PA QASJE:
- **STAFI** - Nuk menaxhon stafin
- **RAPORTET** - Nuk eksporton raporte
- Krijimi i projekteve të reja
- Regjistrimi i klientëve të rinj
- Shtimi i materialeve

### 🎯 PËRGJEGJËSITË:
- Montazhi i produkteve
- Instalimi në objektin e klientit
- Kontrolli i cilësisë
- Raportimi i problemeve

---

## 📊 **HIERARKIA E QASJEVE** (nga më e larta te më e ulëta):

1. **🔴 Administrator** - Qasje e plotë në të gjithçka
   - Menaxhon sistemin dhe stafin
   - Kontrollon të gjitha aspektet

2. **🔵 Menaxher** - Qasje operative e plotë
   - Menaxhon operacionet e përditshme
   - Pa qasje në menaxhimin e stafit

3. **🟢 Disajnere** - Qasje kreative dhe projektimi
   - Fokus në dizajn dhe planifikim
   - Komunikim me klientët

4. **🟡 Mjeshtër** - Qasje e kufizuar në projektet e tij
   - Punon në projektet e caktuara
   - Implementon dizajnet

5. **🟠 Montues** - Qasje e kufizuar në projektet e tij
   - Specializohet në montazh
   - Punon në terren

---

## 🔒 **SIGURIA E SISTEMIT**

### Kontrollet e Implementuara:
- ✅ **Autentifikimi** - Të gjithë përdoruesit duhet të kyçen
- ✅ **Autorizimi** - Çdo veprim kontrollohet bazuar në rol
- ✅ **Auditimi** - Të gjitha veprimet regjistrohen
- ✅ **Kufizimi i të dhënave** - Përdoruesit shohin vetëm të dhënat e nevojshme

### Parimet e Sigurisë:
- **Principle of Least Privilege** - Çdo përdorues ka minimumin e nevojshëm
- **Role-Based Access Control** - Qasjet bazuar në rolin e punës
- **Data Segregation** - Ndarje e të dhënave sipas përgjegjësive

---

## 📝 **SHËNIME TË RËNDËSISHME**

1. **Roli Disajnere** është i sapo krijuar dhe nuk ka përdorues aktualë
2. **Të gjitha ndryshimet** janë implementuar dhe aktivizuar
3. **Sistemi është i sigurt** dhe i testuar për të gjitha rolet
4. **Qasjet mund të përditësohen** nga Administratori sipas nevojave

---

---

## 🔧 **NDRYSHIMET E FUNDIT**

**Data: 27 Janar 2025 - Ora: 00:30**

### ✅ PROBLEMET E RREGULLUARA:
1. **MJESHTRI** - Hequr qasja për modifikimin e:
   - Klientëve (vetëm shikon listën)
   - Dimensioneve (vetëm shikon të projektet e tij)
   - Materialeve (vetëm shikon listën)
   - Statuseve (vetëm shikon)

2. **MONTUES** - Hequr qasja për modifikimin e:
   - Klientëve (vetëm shikon listën)
   - Dimensioneve (vetëm shikon të projektet e tij)
   - Materialeve (vetëm shikon listën)
   - Statuseve (vetëm shikon)

3. **KONTROLLET E SIGURISË** - Shtuar në:
   - `ProjektetDimensionsController` - Vetëm Admin/Menaxher/Disajnere modifikojnë
   - `KlientetController` - Shtuar kontroll për edit/update
   - `MaterialetController` - Shtuar kontroll për edit/update/destroy
   - `StatusetProjektitController` - Shtuar kontroll për edit/update/destroy

### 🛡️ SIGURIA TANI:
- **Mjeshtri dhe Montues** kanë vetëm qasje për të **PARË** të dhënat
- **Nuk mund të modifikojnë** asgjë përveç projekteve të tyre (nëse lejohet)
- **Të gjitha kontrollet** janë implementuar dhe testuar

---

*Dokumenti i përditësuar më: 27 Janar 2025 - Ora: 00:30*
*Versioni: 2.1*
*Statusi: Aktiv dhe i Sigurt*