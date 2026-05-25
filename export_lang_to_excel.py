#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
Export lang_la.php translation file to Excel/CSV format
"""

import re
import csv

# Read the PHP file
input_file = r'c:\xampp\htdocs\php-bin\lang_la.php'
output_csv = r'c:\xampp\htdocs\lang_la_translations.csv'

translations = []

print("Reading PHP file...")
with open(input_file, 'r', encoding='utf-8') as f:
    content = f.read()
    
    # Find all translation pairs using regex
    # Pattern matches: 'key' => 'value',
    pattern = r"'([^']+)'\s*=>\s*'([^']*(?:''[^']*)*)'"
    
    matches = re.findall(pattern, content)
    
    for key, value in matches:
        translations.append([key, value])

print(f"Found {len(translations)} translations")

# Write to CSV file
print(f"Writing to CSV: {output_csv}")
with open(output_csv, 'w', encoding='utf-8-sig', newline='') as f:
    writer = csv.writer(f)
    # Write header
    writer.writerow(['English Key', 'Lao Translation'])
    # Write data
    writer.writerows(translations)

print(f"✓ Successfully exported {len(translations)} translations to {output_csv}")
print(f"You can open this CSV file in Excel!")
