# Quick Start - PDF Editors

## 🚀 Access Your PDF Editors

### Option 1: Direct URLs
After starting your application, navigate to:

- **Visual PDF Editor** (Figma-like):  
  `http://localhost:8000/admin/pdf-editor/visual`

- **WYSIWYG PDF Builder** (Drag & Drop):  
  `http://localhost:8000/admin/pdf-editor/wysiwyg`

### Option 2: Add Links to Admin Dashboard
Edit your dashboard/navigation to include:

```html
<!-- In your admin dashboard blade template -->
<div class="card-body">
    <h5>PDF Template Builders</h5>
    <a href="{{ route('admin.pdf-editor.visual') }}" class="btn btn-primary btn-sm">
        Visual PDF Editor
    </a>
    <a href="{{ route('admin.pdf-editor.wysiwyg') }}" class="btn btn-success btn-sm">
        WYSIWYG Builder
    </a>
</div>
```

## 📁 File Structure

```
travelomine/
├── app/
│   └── Http/Controllers/Admin/
│       └── PDFEditorController.php          # PDF generation logic
├── resources/
│   └── views/admin/bookings/
│       ├── visual-pdf-editor.blade.php      # Visual editor UI
│       └── wysiwyg-pdf-builder.blade.php    # WYSIWYG builder UI
├── routes/
│   └── web.php                              # Routes (already added)
└── PDF_EDITORS_GUIDE.md                     # Full documentation
```

## ⚡ Quick Example

### Create an Airline Ticket Template

**Using Visual PDF Editor:**
1. Click "Add Text" → add "AIRLINE TICKET" heading
2. Click "Add Text" → add passenger info text with {{customer_name}} variable
3. Click "Add Barcode" → set value to {{airline_pnr}}
4. Click "Generate PDF" → download your design

**Using WYSIWYG Builder:**
1. Drag "Heading" component
2. Set text to "AIRLINE TICKET"
3. Drag "Paragraph" component
4. Set text to "Passenger: {{customer_name}}"
5. Drag "Barcode" component
6. Set value to {{airline_pnr}}
7. Click "Generate PDF" → download

## 🎨 Which Editor to Use?

| Feature | Visual | WYSIWYG |
|---------|--------|---------|
| Pixel-perfect control | ✅ | ❌ |
| Easy layout | ❌ | ✅ |
| Freeform design | ✅ | ❌ |
| Semantic elements | ❌ | ✅ |
| Quick templates | ❌ | ✅ |
| Complex designs | ✅ | ❌ |

## 🔄 Export Your Template

Both editors allow you to:

1. **Export as PDF** - Download the final PDF immediately
2. **Save Template** - Download JSON file with all design data
3. **Load Template** - Upload previously saved JSON template

## 📊 Supported Elements

### Both Editors Support:
- ✅ Text (with fonts, colors, sizes)
- ✅ Images (upload PNG, JPG, GIF, WebP)
- ✅ Barcodes (Code128)
- ✅ QR Codes
- ✅ Dynamic variables ({{booking_reference}}, etc.)

### Visual Editor Only:
- ✅ Precise coordinates
- ✅ Line drawing
- ✅ Rectangles/shapes

### WYSIWYG Builder Only:
- ✅ Tables (customizable rows/columns)
- ✅ Spacers
- ✅ Component presets
- ✅ Semantic HTML output

## 🔗 Integration with Bookings

Use your template with a booking:

```php
// In your booking controller
Route::post('/bookings/{id}/pdf-from-template', [
    PDFEditorController::class, 'generateBookingPDF'
])->name('bookings.pdf-from-template');
```

Send template JSON and booking ID:
```javascript
fetch('/admin/bookings/123/pdf-from-template', {
    method: 'POST',
    body: JSON.stringify({ components: templateData }),
    headers: { 'Content-Type': 'application/json' }
})
.then(r => r.blob())
.then(blob => /* download PDF */);
```

## 🧪 Test Your Setup

Run these commands to verify installation:

```bash
# Check routes
php artisan route:list | grep pdf-editor

# Check views exist
ls resources/views/admin/bookings/visual-pdf-editor.blade.php
ls resources/views/admin/bookings/wysiwyg-pdf-builder.blade.php

# Check controller exists
ls app/Http/Controllers/Admin/PDFEditorController.php
```

## 🐛 Troubleshooting

| Issue | Solution |
|-------|----------|
| Routes not found | Run `php artisan route:clear` |
| Views not loading | Check file paths in `routes/web.php` |
| PDF generation fails | Ensure DomPDF is installed via Composer |
| Variables not replacing | Use exact format: `{{variable_name}}` |
| Images not uploading | Check `/storage` permissions |

## 📝 Available Template Variables

```
{{booking_reference}}    Customer's booking ID
{{customer_name}}        Full customer name
{{customer_email}}       Customer email address
{{customer_phone}}       Customer phone number
{{booking_date}}         Date booking was created
{{airline_pnr}}          Airline confirmation number
{{gk_pnr}}               Your system PNR
{{status}}               Current booking status
```

## 💾 Save Your Progress

Both editors save your work locally:
- **Browser storage** for current session
- **JSON download** for permanent backup
- **JSON upload** to restore templates

## 🎯 Next Steps

1. ✅ Navigate to the editor URL
2. ✅ Create a test template
3. ✅ Download PDF to verify
4. ✅ Save template as JSON
5. ✅ Integrate with booking generation
6. ✅ Customize styling to match your brand

## 📞 Support

For issues or questions:
- Check `PDF_EDITORS_GUIDE.md` for detailed documentation
- Review controller comments for technical details
- Check browser console for JavaScript errors

---

**Ready to design?** Start with [Visual PDF Editor](http://localhost:8000/admin/pdf-editor/visual) or [WYSIWYG Builder](http://localhost:8000/admin/pdf-editor/wysiwyg)
