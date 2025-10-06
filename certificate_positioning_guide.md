## Certificate Positioning Guide

### 📍 **How to Position Certificate Fields**

You now have precise control over where each field appears on your certificate. Here's how to use it:

### **1. Basic Positioning**
Each field can be positioned using CSS classes. The coordinate system works like this:
- **Top**: Distance from the top of the page (in millimeters)
- **Left**: Distance from the left edge (in millimeters)  
- **Right**: Distance from the right edge (in millimeters)
- **Width**: How wide the field should be (in millimeters)

### **2. Current Field Positions**
```css
Certificate No:     top: 50mm,  right: 25mm
TO field:          top: 80mm,  left: 25mm,  width: 160mm
Place of Issue:    top: 100mm, left: 25mm,  width: 80mm
Date of Issue:     top: 100mm, right: 25mm, width: 80mm
```

### **3. Quick Position Classes**
You can easily adjust positions by adding classes to your HTML:

**Vertical positioning (top):**
```html
<div class="certificate-field field-to pos-top-90">  <!-- Moves to 90mm from top -->
```

**Horizontal positioning:**
```html
<div class="certificate-field field-to pos-left-30">  <!-- Moves to 30mm from left -->
<div class="certificate-field field-date-issue pos-right-20"> <!-- 20mm from right -->
```

**Width adjustment:**
```html
<div class="certificate-field field-to width-140">  <!-- Makes field 140mm wide -->
```

### **4. Example Adjustments**

**To move Certificate Number lower:**
```css
.certificate-no {
    top: 60mm; /* Change from 50mm to 60mm */
}
```

**To move TO field to the right:**
```css
.field-to {
    left: 35mm; /* Change from 25mm to 35mm */
}
```

**To make Place of Issue field wider:**
```css
.field-place-issue {
    width: 100mm; /* Change from 80mm to 100mm */
}
```

### **5. Fine-tuning Tips**

1. **View your certificate background image** to see exactly where fields should go
2. **Start with rough positions** using the utility classes
3. **Fine-tune by editing the CSS** for exact positioning
4. **Test print preview** to ensure fields align correctly
5. **Use browser developer tools** to see exact measurements

### **6. Common Adjustments**

**Move everything down by 10mm:**
Add `10mm` to all `top` values

**Center a field horizontally:**
```css
left: 50%;
transform: translateX(-50%);
```

**Make text smaller:**
```css
font-size: 10pt; /* or 8pt, 12pt, etc. */
```

### **7. Background Image Alignment**

Make sure your `certificate_draft.jpg` image is properly sized. If fields don't align:
1. Check image dimensions
2. Adjust `background-size` property
3. Use `background-position` to fine-tune

### **8. Debugging Positions**

Add this temporary CSS to see field boundaries:
```css
.certificate-field {
    border: 1px red dashed !important;
    background: rgba(255,0,0,0.1) !important;
}
```

Remember: A4 page is **210mm wide × 297mm tall**. Keep your positions within these bounds!