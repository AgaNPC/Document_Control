# Enterprise Electronic Document Management System (EDMS)

An enterprise-grade Electronic Document Management System (EDMS) built with **Laravel 11/12**, **SQL Server Schema DDL**, **JavaScript (Alpine.js + PDF.js + Tailwind CSS)**, and **Windows Shared Drive (SMB) / NAS storage integration**.

Compliant with **ISO 14001**, **ISO 45001**, and **ISO 27001** (Clause 7.5 Documented Information & Information Security).

---

## 🚀 Key Features & Architectural Modules

### 1. Document Repository (Perpustakaan Dokumen)
- **Interactive Directory Tree Explorer:** Categorized by Company Entity > Department > ISO Standards Category (`K3`, `Lingkungan`, `IT/Keamanan`, `Mutu`).
- **Multi-Criteria Search & Filter:** Debounced search across Document Number, Title, Department, Category, and Revision level.
- **In-App Secure PDF Viewer:** Powered by **PDF.js** rendering without direct download/print buttons. Streams securely via `/documents/{id}/stream` with server-side **Dynamic Watermarking**:
  ```text
  [INTERNAL] - Diakses oleh: {Auth::user()->name} - {Timestamp}
  ```

### 2. Controlled Copy & Print Management (Penggandaan Dokumen)
- Form modal for controlled print requests (Document selection, Copy count, Placement location, Reason).
- **3-Layer / Dept Head Approval Gate** enforcing ISO 27001 print distribution tracking.
- **Controlled Copy Stamping:** Generates dynamic stamped PDF:
  ```text
  CONTROLLED COPY No. [{RequestID}] - Approver: {ApproverName} - Requester: {UserName} - {Timestamp}
  ```

### 3. Document Lifecycle Control (Pengendalian Dokumen)
3 Sequential Sub-Modules with **3-Layer Approval Workflow** (`PIC L1` &rarr; `Section Head L2` &rarr; `Department Head L3`):
1. **Pendaftaran (New Document Registration):** Draft upload to staging. Dept Head approval automatically moves file to master storage folder, marks status as `ACTIVE` (`Rev 00`), and publishes to Page 1.
2. **Revisi (Document Revision):** Select active document, upload draft replacement, input change log. Dept Head approval marks previous document as `SUPERSEDED`, publishes new version as `ACTIVE` with revision increment (`Rev 00` &rarr; `Rev 01`), replacing Page 1 view.
3. **Obsolete (Penarikan Dokumen):** Select document and input obsolescence reason. Dept Head approval sets status to `OBSOLETE` and removes it from Page 1 repository active list.

### 4. In-App Notification System & Quick Action Modal
- Navbar Bell icon with real-time red badge counter driven by `/api/notifications/unread-count`.
- Dropdown card list of pending tasks for logged-in user.
- **Quick Action Approval Modal:** Inspect metadata, preview document details, and execute `[ Setujui ]` or `[ Tolak ]` (rejection requires mandatory feedback notes).

---

## 🛠️ Tech Stack & Database Schema

- **Backend:** Laravel 11/12 (Eloquent ORM targeting SQL Server `sqlsrv`).
- **Frontend:** Blade Components + Alpine.js + Tailwind CSS + PDF.js.
- **PDF Stamping:** FPDI (`setasign/fpdi`) + FPDF (`fpdf/fpdf`).
- **Database Tables:**
  - `documents` (DocumentID, DocNumber, Title, Department, Category, CurrentRevision, Status, ConfidentialityLevel, FilePath)
  - `document_requests` (RequestID, RequestType, TargetDocumentID, DocNumber, Title, Department, Category, Reason, TempFilePath, CopyCount, CurrentStepOrder, CurrentStatus, RequestedBy)
  - `request_approval_logs` (LogID, RequestID, StepOrder, ApproverID, Action, Notes, ActionDate)
  - `user_notifications` (NotificationID, UserID, RequestID, Title, Message, IsRead, IsHandled)
  - `users` (id, name, email, role, department)

---

## 🚦 Quick Start & Installation

```bash
# Clone Repository
git clone https://github.com/AgaNPC/Document_Control.git
cd Document_Control

# Install Composer Dependencies
composer install

# Environment & Database Setup
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed

# Run Development Server
php artisan serve
```

Access the application at `http://localhost:8000`. Test roles available on the login page:
- `Karyawan` (Read-only viewer with watermark)
- `Ahmad PIC` (Layer 1 Approver)
- `Dedi Section Head` (Layer 2 Approver)
- `Eko Department Head` (Layer 3 Approver & Execution Trigger)
- `Rian IT Admin` (System Maintenance)
