# Dolibarr PEPPOL Export

Export Dolibarr customer invoices as **UBL 2.1 / PEPPOL BIS Billing 3.0** XML and send them over the PEPPOL network through the [Peppyrus](https://www.peppyrus.be) access point.

> Maintained fork of [marcrant1/dolibarr-peppol-export](https://github.com/marcrant1/dolibarr-peppol-export) with a working send path, corrected UBL generation, a customer PEPPOL‑ID field, and an English/French language switch.

- **Module version:** 1.1.0
- **Tested with:** Dolibarr 23.0.2, PHP 8.x
- **License:** GPL‑3.0‑or‑later

---

## Features

- **Generate UBL** — produce a PEPPOL BIS Billing 3.0 invoice XML from any validated invoice (downloadable). Output validates with **0 errors / 0 warnings** on official PEPPOL validators.
- **Send to PEPPOL** — transmit the invoice through the Peppyrus access point (production or test environment).
- **PEPPOL lookup** *(experimental)* — query the PEPPOL directory for a participant.
- **Customer PEPPOL ID field** — a dedicated "Peppol ID" field is added to third parties so you can set each customer's participant identifier.
- **Credit notes** — UBL generation emits the correct `CreditNoteTypeCode`.
- **Bilingual UI** — switch the module's labels between **English** and **French** from the setup page, independent of the global Dolibarr language.

---

## Requirements

- A working Dolibarr installation (with the **Third parties**, **Products**, **Bank/Cash** and **Invoices** modules enabled).
- A [Peppyrus](https://www.peppyrus.be) account and an API key (a separate **test** account is available for end‑to‑end testing).
- Your own PEPPOL participant ID registered on the network (e.g. `0208:0123456789` for a Belgian company).

---

## Installation

The module **must** live in a folder named `peppolnew` inside Dolibarr's custom modules directory — it references its own files via that path:

```
<dolibarr>/htdocs/custom/peppolnew/
```

**Option A — git clone** (the second argument forces the correct folder name):

```bash
cd <dolibarr>/htdocs/custom
git clone https://github.com/doomzlight/dolibarr-peppol-export.git peppolnew
```

**Option B — ZIP upload:** download the repository ZIP, **rename the extracted folder to `peppolnew`**, then either drop it into `htdocs/custom/` or upload it via **Home → Setup → Modules → Deploy/install external app/module**.

Then:

1. Go to **Home → Setup → Modules/Applications**, find **PEPPOL Export NEW**, and click to **enable** it.

   On activation the module automatically:
   - creates the `llx_peppolnew_log` table (send history), and
   - adds a **Peppol ID** custom field (`peppyrus_id`) to third parties.

2. *(Optional, non‑admin users)* Grant the **Export invoices to Peppol** permission under **Home → Users & Groups**. Administrators have it by default.

---

## Configuration

### 1. Module settings

Open **Setup → Modules → PEPPOL Export NEW → configure** (or `custom/peppolnew/admin/setup.php`):

| Setting | Description |
|---|---|
| **Module language** | `English` or `French` — display language for this module's labels. |
| **Peppol API URL** | `https://api.peppyrus.be/v1` (production) or `https://api.test.peppyrus.be/v1` (test). |
| **API key** | Your Peppyrus API key (test key for the test URL). |
| **Your Peppol ID** | Your participant ID, e.g. `0208:0123456789` (Belgium: use scheme **0208**, the enterprise number). |

### 2. Company & bank details

These feed the XML and are taken from Dolibarr core:

- **Home → Setup → Company/Organization** — name, address, VAT number, enterprise number.
- **Bank/Cash → New financial account** — IBAN and BIC (used for the `PayeeFinancialAccount` block).

### 3. Customer PEPPOL ID

Each recipient needs a PEPPOL participant ID. On the customer's third‑party record, fill the **Peppol ID** field (e.g. `0208:0123456789`).

If left empty, the module derives it from the customer's VAT number:
- **Belgium** → `0208:<enterprise number>`
- **NL / FR / DE / LU / ES / IT / AT** → VAT‑based EAS scheme
- other countries → set the **Peppol ID** field manually.

---

## Usage

1. Create and **validate** a customer invoice.
2. Open the invoice — three buttons appear:
   - **📄 Generate UBL** — download the PEPPOL XML.
   - **🔍 Peppol lookup** — search the directory for the customer (experimental).
   - **📤 Send to Peppol** — transmit the invoice via Peppyrus.
3. A confirmation pop‑up reports success (with the transaction/message ID) or the error returned by the access point.

> The buttons are shown on validated standard invoices. To validate the XML manually, use a PEPPOL validator such as [peppolvalidator.com](https://peppolvalidator.com/) or [e‑invoice.be](https://e-invoice.be/peppol-validator).

---

## Testing end‑to‑end (recommended)

Use the Peppyrus **test** environment to verify the whole chain without touching production:

1. Set **Peppol API URL** to `https://api.test.peppyrus.be/v1` and enter your **test** API key.
2. Set **Your Peppol ID** to your registered test participant (e.g. `0208:0123456789`).
3. Create a customer whose **Peppol ID** is your *own* participant (loopback), validate an invoice for it, and click **Send to Peppol**.
4. The document is sent (`direction: OUT`) and, because it is a loopback, arrives back in your **test.peppyrus inbox** — proof the full round‑trip works.

When going live, switch the API URL back to `https://api.peppyrus.be/v1` and use your production key.

---

## Configuration constants

| Constant | Default | Purpose |
|---|---|---|
| `PEPPOLNEW_API_URL` | `https://api.peppyrus.be/v1` | Peppyrus API base URL |
| `PEPPOLNEW_API_KEY` | *(empty)* | Peppyrus API key (stored encrypted) |
| `PEPPOLNEW_PEPPOL_ID` | *(empty)* | Your PEPPOL participant ID (sender) |
| `PEPPOLNEW_LANG` | `fr_FR` | Module display language (`fr_FR` / `en_US`) |

---

## Project structure

```
peppolnew/
├── admin/
│   ├── setup.php             # Module configuration page
│   └── diagnostic.php        # Standalone diagnostic page
├── class/
│   ├── ublgenerator.class.php      # Builds the UBL / BIS 3.0 XML
│   ├── peppolapi.class.php         # Peppyrus API client (send / lookup)
│   └── actions_peppolnew.class.php # Invoice-card hook (buttons)
├── core/modules/
│   └── modPeppolNew.class.php      # Module descriptor (install/enable)
├── lib/peppolnew.lib.php           # Helpers (language, recipient ID)
├── js/peppolnew.js                 # Front-end (send/lookup actions)
├── langs/{en_US,fr_FR}/peppolnew.lang
├── sql/llx_peppolnew_log.sql       # Send-history table
├── peppol_send.php                 # Endpoint: generate / send / lookup
└── tools/                          # Developer/test helpers (not required at runtime)
```

---

## Troubleshooting

| Symptom | Cause / fix |
|---|---|
| `Cannot create dir /var/www/documents/facture/...` | The web server user can't write the documents folder. Fix ownership of Dolibarr's `documents/` directory. |
| Pop‑up: `Unexpected token '<' ... is not valid JSON` | Old cached JavaScript. Hard‑refresh the page (Ctrl+F5). |
| `API Error (HTTP 401)` | Wrong API key, or a **test** key used against the **production** URL (or vice versa). |
| `API Error (HTTP 422): Incorrect sender` | `Your Peppol ID` is not a registered participant. For Belgium use the `0208:` scheme. |
| `Customer Peppol ID not configured` | Set the **Peppol ID** field on the customer, or give the customer a VAT number the module can derive from. |

---

## PEPPOL identifiers (Belgium)

The recommended scheme for Belgian companies is **`0208`** (enterprise / KBO number), e.g. `0208:0123456789`. The legacy VAT scheme `9925` is being phased out and is often not registered in the PEPPOL directory — invoices using it may be rejected.

---

## Credits & license

- Original module by [marcrant1](https://github.com/marcrant1/dolibarr-peppol-export).
- PEPPOL transport via [Peppyrus](https://www.peppyrus.be); API verified against the official [tigron/peppyrus-api-php](https://github.com/tigron/peppyrus-api-php) client.
- Licensed under **GPL‑3.0‑or‑later** — see [LICENSE](LICENSE).

See [CHANGELOG.md](CHANGELOG.md) for the list of changes.
