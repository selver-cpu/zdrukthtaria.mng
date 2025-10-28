# OSI EB108 XML Format - Ndryshimet e Bëra

## Problemi
XML-i i gjeneruar nuk po pranohej nga Selco EB108 (OSI version OI 01.09.01.00, PLC 01.09.00.00).

## Ndryshimet e Implementuara

### 1. **Job ID - Fillimi nga 100**
- **Para**: `$jobId = 101`
- **Tani**: `$jobId = 100`
- **Arsyeja**: Standardi OSI preferon job ID të fillojë nga 100

### 2. **Piece ID - Fillimi nga 1**
- **Para**: `$globalPieceId = 0` dhe të gjitha pjesët kishin `id='0'`
- **Tani**: `$globalPieceId = 1` dhe çdo pjesë ka ID unik (1, 2, 3...)
- **Arsyeja**: Makina Selco kërkon ID unik për çdo pjesë për të gjurmuar prerjet

### 3. **Program Tag - Pa Hapësirë**
- **Para**: `<Program >` (me hapësirë)
- **Tani**: `<Program>` (pa hapësirë)
- **Arsyeja**: Parseri XML i OSI mund të ketë probleme me hapësirën

### 4. **Edgebanding (Kantim) - Shtuar Atributet E1, E2, E3, E4**
- **E1**: Kantim përpara (front)
- **E2**: Kantim majtas (left)
- **E3**: Kantim djathtas (right)  
- **E4**: Kantim pas (back)

Tani nëse një pjesë ka `kantim_needed=true`, XML-i përfshin atributet:
```xml
<Piece N='1' id='1' L='600.00' W='400.00' Q='2' QPatt='2' E1='1' E3='1'/>
```

## Si të Testosh

1. **Eksporto XML të ri**:
   - Shko te `Dimensionet e Projekteve`
   - Filtro projektin që do
   - Kliko "OSI 2007 XML"

2. **Importo në Selco EB108**:
   - Hap software OSI në makinë
   - Import → Select XML file
   - Kontrollo që të gjitha pjesët shfaqen me kantim të saktë

3. **Verifikime**:
   - ✅ Job ID fillon nga 100
   - ✅ Piece ID janë unikë (1, 2, 3...)
   - ✅ Edgebanding shfaqet korrekt (E1, E2, E3, E4)
   - ✅ Program tag pa hapësirë
   - ✅ Checksum në cut-in e fundit

## Nëse Problemi Vazhdon

Kontrollo këto në software OSI:
1. **Version Compatibility**: OSI version OI 01.09.01.00 duhet të pranojë këtë format
2. **XML Import Settings**: Kontrollo që "XML Link" ose "XML Import" është i aktivizuar
3. **Material Codes**: Sigurohu që kodet e materialeve në XML përputhen me ato në databazën e OSI
4. **Error Log**: Kontrollo error log në OSI për mesazhe specifike gabimi

## Të Dhëna Teknike

**Selco EB108 Specs**:
- Model: EB108
- Matricule: 93108
- OSI Version: OI 01.09.01.00
- PLC Version: 01.09.00.00
- Max Cutting Length: 4300mm
- Max Cutting Width: 4400mm
- Max Blade Projection: 108mm

**Format XML**: OSI 2007 Standard (Biesse Selco)
