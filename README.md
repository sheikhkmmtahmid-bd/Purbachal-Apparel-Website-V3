# Purbachal Apparel Limited - Official Website V3

This is the official website for Purbachal Apparel Limited, a woven garment manufacturer and 100% exporter based in Gazipur, Bangladesh.

## About the Project

This started as a complete rebuild of the company's website — clean, professional, and aimed squarely at B2B buyers and international retail partners. The first version of V2 was pure static HTML: no build tools, no server requirements, just files that open in a browser. That was the right call for getting the design right quickly without overhead getting in the way.

After the initial release, the site was converted to PHP and a custom admin dashboard was built on top of it. The client is non-technical, and the expectation was always that they'd need to update text, swap images, manage certifications, and eventually add new pages without calling a developer for every change. Rather than dropping in WordPress or a third-party CMS, a flat-file system was built from scratch. Content lives in JSON files, PHP reads and renders it, and an admin panel gives the client a way to edit everything directly in the browser. This kept the site fast, lightweight, and easy to host on Namecheap shared hosting where there's no reason to spin up a database for what is essentially a brochure site.

The site covers everything a potential buyer needs: who we are, what we make, our certifications, our clients, and how to get in touch.

## Pages

- **Home** - Company overview with key stats and capabilities
- **About Us** - History, board of directors, mission, and factory overview
- **Our Clients** - Global retail partners with country details
- **Certifications** - Full list of compliance and quality certifications
- **Our Products** - Kid's Wear, Men's Wear, and Women's Wear with image carousel
- **Sustainability** - Environmental and CSR initiatives
- **Our Team** - Board of directors profiles with a managed photo gallery
- **Contact Us** - Get in touch and request a quote

Custom pages can also be created and published from the admin dashboard without touching code.

## Tech Stack

**Front-end**

Still plain HTML, CSS, and vanilla JavaScript — the conversion to PHP didn't change any of that. No framework, no build step.

- Google Fonts: Outfit + DM Sans
- Shared stylesheet: `styles.css`
- Shared script: `nav.js`

**Back-end**

- PHP 8.2, no framework. Namecheap shared hosting runs Apache and PHP out of the box, so a framework would just add weight with no real benefit at this scale.
- Content stored as JSON files in `data/` — no database, no credentials to manage, and the entire site's data is a single folder that can be copied for backup or migration.
- Apache `.htaccess` handles URL routing and maps HTTP errors to custom error pages.

**Admin dashboard**

- Session-based authentication with login, forgot password (email link), and profile management.
- CSRF token on every admin form and API endpoint.
- All backend API endpoints return JSON; the dashboard UI communicates with them over `fetch()`.
- SortableJS for drag-to-reorder where needed (gallery, page sections, navigation).

## Admin Dashboard

The dashboard is at `/admin/` and has no link from the public site. It includes:

**Page Editor** — edits text, images, and icons on any existing page. Icon fields render a visual picker with all available icons instead of asking someone to type a name like `check-circle` from memory. That's not something a non-technical user should have to do.

**Page Builder** — lets you build new custom pages by stacking sections in any order: hero, card grid, split image, stats row, pillars, and so on. Sections can be dragged to reorder. Each section type has a form-based editor with dropdowns for things like icon color and card style, so the client isn't making decisions by guessing class names.

**Gallery Manager** — manages the team page photo gallery. Images can be reordered by dragging and each one has an alt text field.

**Products Manager** — manages product listings across Kids, Men's, and Women's Wear.

**Nav Manager** — edits navigation labels, page order, and dropdown structure. Changes go live immediately.

**Site Settings** — site name, tagline, address, phone, email, and similar global details that appear across multiple pages.

**Footer Editor** — footer content managed separately from page content.

## Data Storage

Each page's content lives in `data/pages/[slug].json`. Everything else — nav structure, site settings, team gallery, product listings — has its own JSON file under `data/`. Uploads go into `uploads/` with subdirectories by type: `gallery/`, `pages/`, `mens/`, `womens/`, `kids/`.

The flat-file approach was a deliberate choice. A database would mean configuring MySQL on the hosting account, writing schema and migration scripts, and keeping database backups in sync with file backups. With JSON, everything the site needs is in one folder. Moving to a new host is a file upload.

## Error Pages

Custom error pages are registered in `.htaccess` for 400, 401, 403, 404, 405, 500, and 503. The 404, 400, 401, 403, and 405 pages use the full site template — header, footer, nav — so they look like part of the site. The 500 and 503 pages are standalone HTML with no PHP dependencies at all. If the server is broken badly enough to produce a 500, you cannot trust PHP to render the error page, so those two are completely self-contained.

## Hosting

The site is deployed on Namecheap shared hosting (Apache + PHP 8.2). Locally it runs on XAMPP. The only environment-specific thing is the absolute paths for `DATA_DIR` and `UPLOAD_DIR` set in `includes/functions.php` — those need to match wherever the files actually live on the server. There is no build step. Upload the files and it works.

## Key Features

- Fully responsive across desktop, tablet, and mobile
- Product image carousel with category filtering (Kids / Men / Women)
- Lightbox image viewer on the products page
- Scroll reveal animations and counter animations
- WhatsApp and back-to-top floating buttons
- BGMEA No. 6356, ERC No. BA 26032610788220
- Animated color bar on hero sections (colors match the pinwheel logo mark)

## Company

Purbachal Apparel Limited  
South Panjora, Ward No. 05, Nagori, Kaliganj, Gazipur-1720, Bangladesh  
info@purbachalapparel.com  
+880 1713 001008

## Contact Form

The contact form on the Contact page uses [EmailJS](https://www.emailjs.com/), which sends submissions directly to `info@purbachalapparel.com` using a connected Gmail account. No server or backend is needed for the form itself. Free tier allows 200 emails per month.

**To change the destination email:**

1. Log in at https://dashboard.emailjs.com/ with `sheikh.k.m.m.tahmid@gmail.com`
2. Go to **Email Templates > Contact Us**
3. Change the **To Email** field to the new address and click Save

**To change the sending Gmail account:**

1. Go to **Email Services** in the dashboard
2. Click the Gmail service and reconnect with a different account

**Keys (all public, safe in client-side code):**

| Key | Value |
|---|---|
| Public Key | `mt429GxgF_853gm9a` |
| Service ID | `service_l20zm0b` |
| Template ID | `template_tjydd46` |

These are referenced in `contact.php`.

## Built By

Designed and developed by [SKMMT](http://skmmt.rootexception.com/).
