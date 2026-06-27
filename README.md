# Purbachal Apparel Limited - Official Website V2

This is the official demo website for Purbachal Apparel Limited, a woven garment manufacturer and 100% exporter based in Gazipur, Bangladesh.

## About the Project

This is a complete rebuild of the company website. The goal was to create something clean, professional, and appropriate for B2B buyers and international retail partners browsing the site.

The site covers everything a potential buyer needs: who we are, what we make, our certifications, our clients, and how to get in touch.

## Pages

- **Home** - Company overview with key stats and capabilities
- **About Us** - History, board of directors, mission, and factory overview
- **Our Clients** - Global retail partners with country details
- **Certifications** - Full list of compliance and quality certifications
- **Our Products** - Kid's Wear, Men's Wear, and Women's Wear with image carousel
- **Sustainability** - Environmental and CSR initiatives
- **Our Team** - Board of directors profiles
- **Contact Us** - Get in touch and request a quote

## Tech Stack

Plain HTML, CSS, and vanilla JavaScript. No frameworks, no build tools. Just open any `.html` file in a browser and it works.

- Google Fonts: Outfit + DM Sans
- One shared stylesheet: `styles.css`
- One shared script: `nav.js`

## Key Features

- Fully responsive across desktop, tablet, and mobile
- Product image carousel with category filtering (Kids / Men / Women)
- Lightbox image viewer on the products page
- Scroll reveal animations and counter animations
- WhatsApp and back-to-top floating buttons
- BGMEA No. 6356, ERC No. BA 26032610788220

## Company

Purbachal Apparel Limited  
South Panjora, Ward No. 05, Nagori, Kaliganj, Gazipur-1720, Bangladesh  
info@purbachalapparel.com  
+880 1713 001008

## Contact Form

The contact form on `contact.html` uses [EmailJS](https://www.emailjs.com/), which sends submissions directly to `info@purbachalapparel.com` using a connected Gmail account. No server or backend is needed. Free tier allows 200 emails per month.

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

These are referenced in the inline `<script>` block inside the contact form in `contact.html`.

## Built By

Designed and developed by [SKMMT](http://skmmt.rootexception.com/).
