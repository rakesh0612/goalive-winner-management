# CLOUDWAYS .HTACCESS TROUBLESHOOTING

## Issue: "Server unable to read htaccess file, denying access to be safe"

This error occurs when:
1. .htaccess file has wrong permissions
2. Cloudways doesn't support certain directives
3. Apache modules are disabled

## SOLUTIONS (try in order):

### Solution 1: Fix File Permissions
In Cloudways File Manager:
1. Find .htaccess file
2. Right-click → Permissions
3. Set to 644 (rw-r--r--)

### Solution 2: Use Minimal .htaccess
Replace .htaccess content with just:
```
DirectoryIndex login.html index.html
```

### Solution 3: Rename .htaccess Temporarily
1. Rename .htaccess to .htaccess_backup
2. Use .htaccess_minimal instead:
   - Copy .htaccess_minimal to .htaccess
   
### Solution 4: Remove .htaccess Completely
1. Delete or rename .htaccess file
2. Access site directly:
   - https://your-domain.com/login.html
   - https://your-domain.com/test_basic.php

### Solution 5: Cloudways Settings
In Cloudways Control Panel:
1. Go to Server Management
2. Settings & Packages
3. Check if mod_rewrite is enabled

## TEST AFTER EACH SOLUTION:
- https://your-domain.com/test_basic.php
- https://your-domain.com/login.html

## If still not working:
Contact Cloudways support about .htaccess compatibility.