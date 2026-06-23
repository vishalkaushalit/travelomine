# PDF Template Editors - Setup & Usage Guide

## Overview

You now have **two powerful PDF editors** for designing your booking ticket templates:

1. **Visual PDF Editor** - Like Figma, with canvas-based design
2. **WYSIWYG Builder** - Drag-and-drop component builder

## File Locations

- **Visual PDF Editor View**: `resources/views/admin/bookings/visual-pdf-editor.blade.php`
- **WYSIWYG Builder View**: `resources/views/admin/bookings/wysiwyg-pdf-builder.blade.php`
- **PDF Controller**: `app/Http/Controllers/Admin/PDFEditorController.php`

## Access URLs

After running the application:

- **Visual PDF Editor**: `http://yourapp/admin/pdf-editor/visual`
- **WYSIWYG Builder**: `http://yourapp/pdf-editor/wysiwyg`

## Features

### Visual PDF Editor

**Purpose**: Pixel-perfect design similar to Figma

**Components**:
- Text boxes with formatting
- Images (upload your own)
- Rectangles and shapes
- Lines/dividers
- Barcodes
- QR Codes

**Features**:
- Drag and drop elements on canvas
- Edit properties in sidebar (position, size, color, font)
- Layer management
- Multiple page sizes (A4, Letter, A5)
- Save/load templates as JSON
- Quick variable insertion
- Export to PDF

**How to Use**:
1. Go to Visual PDF Editor
2. Click components from left sidebar to add them
3. Click on canvas to place elements
4. Adjust position/properties in right sidebar
5. Use "Quick Insert Variables" to add booking data
6. Click "Generate PDF" to download
7. Click "Save Template" to store for later

### WYSIWYG PDF Builder

**Purpose**: Fast, semantic component-based design

**Components**:
- Headings (with font size, color, alignment)
- Paragraphs (with full text formatting)
- Tables (configurable rows/columns)
- Images (upload or link)
- Dividers
- Spacers
- Barcodes
- QR Codes

**Features**:
- Drag-and-drop components
- Live preview
- Inspector panel for property editing
- Layer management
- Presets (Airline Ticket, Hotel Voucher, Blank)
- Variable insertion for dynamic content
- Save/load templates as JSON
- Export to PDF

**How to Use**:
1. Go to WYSIWYG Builder
2. Drag components from left sidebar to canvas
3. Click on component to select and edit properties
4. Use "Quick Insert Variables" to add dynamic data
5. Preview changes in real-time
6. Click "Generate PDF" to download
7. Click "Save Template" to store configuration

## Available Template Variables

Both editors support these variables that will be replaced with booking data:

```
{{booking_reference}}    - Booking ID
{{customer_name}}        - Customer name
{{customer_email}}       - Customer email
{{customer_phone}}       - Customer phone number
{{booking_date}}         - Date booking was made
{{airline_pnr}}          - Airline PNR code
{{gk_pnr}}               - Your system PNR code
{{status}}               - Booking status
```

## Integration with Booking PDF Generation

To use your designed template when generating booking PDFs:

1. Design your template using either editor
2. Save the template (JSON file)
3. Load the template when generating a booking PDF
4. Variables will be automatically replaced with actual booking data

## API Endpoints

### Generate PDF from Visual Editor
```
POST /admin/pdf/generate-visual
Body: { elements: [...] }
Returns: PDF file
```

### Generate PDF from WYSIWYG Builder
```
POST /admin/pdf/generate-wysiwyg
Body: { components: [...] }
Returns: PDF file
```

### Generate Booking-Specific PDF
```
POST /admin/bookings/{id}/pdf-from-template
Body: { components: [...] }
Returns: PDF file for booking
```

### Save Template
```
POST /admin/pdf/save-template
Body: { name, type, data }
Returns: { success, template }
```

### Load Template
```
GET /admin/pdf/load-template?template_id=xxx
Returns: Template data
```

## Customization Tips

### Visual Editor
- Use "Layers" panel to organize and manage elements
- Properties panel shows all editable options for selected element
- Variables can be inserted as text and will be replaced when generating PDFs
- Barcodes and QR codes are automatically generated from values

### WYSIWYG Builder
- Components maintain semantic structure (h3 for headings, p for paragraphs)
- Table component creates standard HTML tables
- Inspector panel provides all editing options
- Drag components to reorder or move to different positions
- Delete button removes selected component

## Barcode & QR Code

Both editors support barcodes and QR codes:

**Barcodes**:
- Format: Code128 (numeric)
- Height adjustable
- Can use variables (e.g., {{booking_reference}})

**QR Codes**:
- Size adjustable
- Can encode URLs or text
- Perfect for linking to booking details

## Template Storage

Templates are saved as JSON files containing:
- Component structure
- Properties (colors, fonts, sizes)
- Position information
- Text content

**Note**: Currently templates are client-side stored. To enable server-side storage:

1. Create a database migration for storing templates
2. Modify `PDFEditorController::saveTemplate()` to save to database
3. Update `PDFEditorController::loadTemplate()` to retrieve from database

## Browser Compatibility

- Chrome/Chromium: ✅ Full support
- Firefox: ✅ Full support
- Safari: ✅ Full support
- Edge: ✅ Full support

## Performance Notes

- Large templates (50+ elements) may take longer to render
- QR codes and barcodes are generated externally for best compatibility
- PDF generation is processed server-side for reliability

## Troubleshooting

**PDF not downloading**: Check browser console for errors, ensure JavaScript is enabled

**Variables not replacing**: Ensure you're using exact variable names with {{ }} syntax

**Images not showing**: Upload images in supported formats (PNG, JPG, GIF, WebP)

**Template not saving**: Check browser console, ensure localStorage is available

## Advanced Usage

### Custom Styles in Templates
Both editors generate HTML, so you can modify generated HTML/CSS through the controller:

**File**: `app/Http/Controllers/Admin/PDFEditorController.php`

Modify the `buildHTMLFromElements()` or `buildHTMLFromComponents()` methods to add custom styling.

### Database Integration
To make templates persistent, update the controller methods:

```php
// In PDFEditorController
public function saveTemplate(Request $request)
{
    PDFTemplate::create([
        'name' => $request->name,
        'type' => $request->type,
        'data' => json_encode($request->data),
        'user_id' => auth()->id(),
    ]);
}
```

## Security Considerations

- Validate all user input on the server
- Sanitize template data before rendering
- Ensure proper authorization checks are in place
- Don't allow users to upload malicious files as images

## Next Steps

1. Access the editors at the provided URLs
2. Experiment with creating templates
3. Test PDF generation
4. Integrate with your booking workflow
5. Customize styling as needed

For additional help, check the controller documentation or modify the views as needed for your specific requirements.
