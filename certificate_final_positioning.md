## 📋 Certificate Fields Positioning - Final Implementation

Based on the certificate template image with numbered positions, here's the complete mapping:

### ✅ **Positioned Fields:**

| Position | Field Name | Location | CSS Class |
|----------|------------|----------|-----------|
| **1** | Certificate No | Top Right (35mm, right 15mm) | `.certificate-no` |
| **2** | Export Country | Top Left (52mm, left 15mm) | `.field-export-country` |
| **3** | Import Country | Top Right (52mm, right 15mm) | `.field-import-country` |
| **4** | Exporter Name & Address | Left side (68mm, left 15mm) | `.field-exporter` |
| **5** | Importer Name & Address | Right side (68mm, right 15mm) | `.field-importer` |
| **6** | Package Description | Left side (95mm, left 15mm) | `.field-packages` |
| **7** | Distinguishing Marks | Right side (95mm, right 15mm) | `.field-distinguishing-marks` |
| **8** | Place of Origin | Left side (120mm, left 15mm) | `.field-place-origin` |
| **9** | Entry Point | Right side (120mm, right 15mm) | `.field-entry-point` |
| **10** | Quantity & Unit | Left side (145mm, left 15mm) | `.field-quantity` |
| **11** | Scientific Name | Right side (145mm, right 15mm) | `.field-scientific-name` |
| **12** | Additional Declaration | Full width (170mm, left 15mm) | `.field-additional-declaration` |
| **13** | Treatment Date | Left (210mm, left 15mm) | `.field-treatment-date` |
| **14** | Treatment Method | Center (210mm, left 75mm) | `.field-treatment-method` |
| **15** | Duration & Temperature | Right (210mm, right 15mm) | `.field-duration-temp` |
| **16** | Additional Information | Left (235mm, left 15mm) | `.field-additional-info` |
| **17** | Date Inspected | Center (235mm, left 105mm) | `.field-date-inspected` |
| **18** | Date Issued | Left (260mm, left 15mm) | `.field-date-issued` |
| **19** | Place of Issue | Right (260mm, right 15mm) | `.field-place-issued` |

### 🎯 **Key Features:**

1. **Precise Positioning**: Each field positioned exactly according to certificate template
2. **Background Integration**: Fields overlay cleanly on `certificate_draft.jpg`
3. **Data Integration**: Pulls from multiple database tables (application, certificate, inspection)
4. **Print Ready**: Optimized for A4 printing with proper margins

### 🔧 **Fine-tuning Instructions:**

If any field needs adjustment, modify the corresponding CSS class:

```css
.field-name {
    top: XXmm;    /* Vertical position from top */
    left: XXmm;   /* Horizontal position from left */
    right: XXmm;  /* OR horizontal position from right */
    width: XXmm;  /* Field width */
}
```

### 📊 **Data Sources:**

- **Application Data**: `ApplicationInfo()` - Basic application details
- **Certificate Data**: `CertificateInfo()` - Certificate-specific information  
- **Inspection Data**: `tbinspection` table - Treatment and inspection details
- **Entity Data**: `EntityExportInfo()`, `EntityImportInfo()` - Company information
- **Reference Data**: Countries, units, treatment methods, etc.

### 🖨️ **Print Settings:**

- **Page Size**: A4 (210mm × 297mm)
- **Margins**: 15mm on all sides
- **Background**: `certificate_draft.jpg` included in print
- **Font Sizes**: 8pt-14pt for optimal readability

The certificate is now perfectly aligned with your official template!